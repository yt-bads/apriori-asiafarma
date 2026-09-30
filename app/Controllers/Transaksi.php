<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\DetailTransaksiModel;

class Transaksi extends BaseController
{
    protected $transaksiModel;
    protected $detailTransaksiModel;

    public function __construct()
    {
        $this->transaksiModel = new TransaksiModel();
        $this->detailTransaksiModel = new DetailTransaksiModel();
    }

    public function index()
    {
        // 1. Ambil semua data header transaksi
        $transaksiData = $this->transaksiModel->orderBy('tanggal', 'DESC')->findAll();
        
        // 2. Ambil semua data detail (item) produk
        $detailData = $this->detailTransaksiModel->findAll();

        // 3. Kelompokkan produk berdasarkan ID Transaksi agar mudah ditampilkan di View
        $groupedDetail = [];
        foreach ($detailData as $detail) {
            $groupedDetail[$detail['id_transaksi']][] = $detail['nama_produk'];
        }

        // 4. Masukkan array produk ke dalam data header transaksi
        foreach ($transaksiData as &$trx) {
            // Jika ada produk untuk TRX ini, masukkan. Jika tidak, set array kosong.
            $trx['produk'] = isset($groupedDetail[$trx['id_transaksi']]) ? $groupedDetail[$trx['id_transaksi']] : [];
        }

        $data = [
            'title'     => 'Manajemen Transaksi',
            'transaksi' => $transaksiData
        ];
        
        return view('transaksi/index', $data);
    }

    public function hapus($id_transaksi)
    {
        // Mencari data transaksi berdasarkan id_transaksi (contoh: TRX001)
        $transaksi = $this->transaksiModel->where('id_transaksi', $id_transaksi)->first();
        
        if ($transaksi) {
            // Hapus berdasarkan primary key (id). 
            // Detail transaksi akan otomatis terhapus karena ON DELETE CASCADE di database.
            $this->transaksiModel->delete($transaksi['id']);
            session()->setFlashdata('success', 'Data transaksi ' . $id_transaksi . ' berhasil dihapus beserta detailnya.');
        } else {
            session()->setFlashdata('error', 'Data transaksi tidak ditemukan.');
        }

        return redirect()->to('/transaksi');
    }

    public function upload_proses()
    {
        $file = $this->request->getFile('file_excel');
        
        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File tidak valid atau belum dipilih.');
            return redirect()->to('/transaksi');
        }

        $extension = $file->getClientExtension();
        $filepath = $file->getTempName();

        if ($extension == 'csv') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else if ($extension == 'xlsx' || $extension == 'xls') {
            $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        } else {
            session()->setFlashdata('error', 'Format file harus CSV, XLS, atau XLSX.');
            return redirect()->to('/transaksi');
        }

        $spreadsheet = $reader->load($filepath);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        $transaksiHeader = [];
        $transaksiDetail = [];

        for ($i = 1; $i < count($sheetData); $i++) {
            $tanggal = $sheetData[$i][0];
            $id_transaksi = $sheetData[$i][1];
            $produk_mentah = $sheetData[$i][2]; 

            if (empty($id_transaksi) || empty($produk_mentah)) {
                continue; 
            }

            $tanggal_format = date('Y-m-d', strtotime($tanggal));

            if (!isset($transaksiHeader[$id_transaksi])) {
                $transaksiHeader[$id_transaksi] = [
                    'id_transaksi' => $id_transaksi,
                    'tanggal'      => $tanggal_format
                ];
            }

            $produk_array = explode(',', $produk_mentah);
            foreach ($produk_array as $p) {
                $nama_produk = strtoupper(trim($p));
                
                if ($nama_produk === 'HAEMA ASTAXAN ASTAXANTHIN 30KPSL') {
                    $nama_produk = 'HAEMA ASTAXAN ASTAXANTHIN';
                }

                if (!empty($nama_produk)) {
                    $transaksiDetail[] = [
                        'id_transaksi' => $id_transaksi,
                        'nama_produk'  => $nama_produk
                    ];
                }
            }
        }

        $this->db = \Config\Database::connect();
        $this->db->transStart(); 

        if (!empty($transaksiHeader)) {
            $this->transaksiModel->builder()->ignore(true)->insertBatch(array_values($transaksiHeader));
        }

        if (!empty($transaksiDetail)) {
            $this->detailTransaksiModel->builder()->ignore(true)->insertBatch($transaksiDetail);
        }

        $this->db->transComplete(); 

        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan ke database.');
        } else {
            session()->setFlashdata('success', 'Data transaksi berhasil diimpor dan melalui tahap preprocessing!');
        }

        return redirect()->to('/transaksi');
    }

    public function simpan_manual()
    {
        $tanggal = $this->request->getPost('tanggal');
        $id_transaksi = $this->request->getPost('id_transaksi');
        $produk_mentah = $this->request->getPost('produk'); 

        // TAMBAHAN BARU: Cek apakah ID Transaksi sudah ada di database
        $cekId = $this->transaksiModel->where('id_transaksi', $id_transaksi)->first();
        
        if ($cekId) {
            // Jika ID sudah ada, gagalkan proses dan kembalikan pesan error
            session()->setFlashdata('error', 'Gagal! ID Transaksi "' . $id_transaksi . '" sudah terdaftar di sistem. Gunakan ID lain.');
            return redirect()->to('/transaksi');
        }

        // Jika ID belum ada, lanjutkan proses simpan
        $this->db = \Config\Database::connect();
        $this->db->transStart();

        // Kita hapus ignore(true) karena sudah divalidasi di atas
        $this->transaksiModel->insert([
            'id_transaksi' => $id_transaksi,
            'tanggal'      => $tanggal
        ]);

        $produk_array = explode(',', $produk_mentah);
        $transaksiDetail = [];

        foreach ($produk_array as $p) {
            $nama_produk = strtoupper(trim($p));
            
            if ($nama_produk === 'HAEMA ASTAXAN ASTAXANTHIN 30KPSL') {
                $nama_produk = 'HAEMA ASTAXAN ASTAXANTHIN';
            }

            if (!empty($nama_produk)) {
                $transaksiDetail[] = [
                    'id_transaksi' => $id_transaksi,
                    'nama_produk'  => $nama_produk
                ];
            }
        }

        if (!empty($transaksiDetail)) {
            $this->detailTransaksiModel->insertBatch($transaksiDetail);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Terjadi kesalahan sistem saat menyimpan transaksi manual.');
        } else {
            session()->setFlashdata('success', 'Transaksi manual berhasil ditambahkan dan diproses.');
        }

        return redirect()->to('/transaksi');
    }
}