<?php

namespace App\Controllers;

use App\Models\TransaksiModel;
use App\Models\HasilAprioriModel;

class Dashboard extends BaseController
{
    public function index()
    {
        // Memanggil model untuk mengambil data statistik
        $transaksiModel = new TransaksiModel();
        $hasilModel = new HasilAprioriModel();

        // Menghitung jumlah baris di tabel transaksi dan hasil_apriori
        $totalTransaksi = $transaksiModel->countAllResults();
        $totalProsesApriori = $hasilModel->countAllResults();

        // Menyiapkan data untuk dikirim ke antarmuka (View)
        $data = [
            'title'              => 'Dashboard Admin',
            'total_transaksi'    => $totalTransaksi,
            'total_proses'       => $totalProsesApriori,
            'nama_admin'         => session()->get('nama')
        ];

        return view('dashboard/index', $data);
    }
}