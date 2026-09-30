<?php

namespace App\Controllers;

use App\Models\DetailTransaksiModel;
use App\Models\HasilAprioriModel;
use App\Models\FrequentItemsetModel;
use App\Models\AssociationRulesModel;
use App\Libraries\apriori;

class Analisa extends BaseController
{
    public function index()
    {
        // Ambil data riwayat analisis dari yang terbaru
        $hasilModel = new HasilAprioriModel();
        $riwayat = $hasilModel->orderBy('tanggal_proses', 'DESC')->findAll();

        $data = [
            'title'   => 'Proses Apriori',
            'riwayat' => $riwayat
        ];

        return view('apriori/form', $data);
    }

    public function proses()
    {
        $minSupport = $this->request->getPost('min_support');
        $minConfidence = $this->request->getPost('min_confidence');

        if (!is_numeric($minSupport) || !is_numeric($minConfidence)) {
            session()->setFlashdata('error', 'Parameter Support dan Confidence harus berupa angka.');
            return redirect()->to('/analisa');
        }

        $detailModel = new DetailTransaksiModel();
        $dataTransaksiMentah = $detailModel->findAll();

        if (empty($dataTransaksiMentah)) {
            session()->setFlashdata('error', 'Data transaksi masih kosong. Silakan upload dataset terlebih dahulu.');
            return redirect()->to('/analisa');
        }

        $aprioriLib = new apriori();
        $hasilApriori = $aprioriLib->process($dataTransaksiMentah, $minSupport, $minConfidence);

        if (empty($hasilApriori['frequent_itemsets']) || empty($hasilApriori['rules'])) {
            session()->setFlashdata('warning', 'Tidak ada aturan asosiasi (rules) yang memenuhi Minimum Support ' . $minSupport . '% dan Confidence ' . $minConfidence . '%. Coba turunkan nilainya.');
            return redirect()->to('/analisa');
        }

        $hasilModel = new HasilAprioriModel();
        $frequentModel = new FrequentItemsetModel();
        $rulesModel = new AssociationRulesModel();

        $this->db = \Config\Database::connect();
        $this->db->transStart();

        $id_hasil = $hasilModel->insert([
            'min_support'    => $minSupport,
            'min_confidence' => $minConfidence,
            'tanggal_proses' => date('Y-m-d H:i:s')
        ]);

        $batchFrequent = [];
        foreach ($hasilApriori['frequent_itemsets'] as $iterasi => $itemsets) {
            foreach ($itemsets as $item) {
                $batchFrequent[] = [
                    'id_hasil'       => $id_hasil,
                    'iterasi'        => $iterasi,
                    'itemset'        => json_encode($item['itemset']),
                    'support_count'  => $item['count'],
                    'support_persen' => $item['support']
                ];
            }
        }
        
        if (!empty($batchFrequent)) {
            $frequentModel->insertBatch($batchFrequent);
        }

        $batchRules = [];
        foreach ($hasilApriori['rules'] as $rule) {
            $batchRules[] = [
                'id_hasil'   => $id_hasil,
                'antecedent' => json_encode($rule['antecedent']),
                'consequent' => json_encode($rule['consequent']),
                'support'    => $rule['support'],
                'confidence' => $rule['confidence'],
                'lift'       => $rule['lift']
            ];
        }

        if (!empty($batchRules)) {
            $rulesModel->insertBatch($batchRules);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === FALSE) {
            session()->setFlashdata('error', 'Terjadi kesalahan saat menyimpan hasil analisis ke database.');
            return redirect()->to('/analisa');
        }

        session()->setFlashdata('success', 'Analisis Apriori berhasil diselesaikan dan disimpan!');
        
        return redirect()->to('/hasil/detail/' . $id_hasil);
    }

    public function hapus($id)
    {
        $hasilModel = new HasilAprioriModel();
        $cek = $hasilModel->find($id);
        
        if ($cek) {
            // Menghapus header hasil, otomatis tabel anak (itemset & rules) akan terhapus karena ON DELETE CASCADE
            $hasilModel->delete($id);
            session()->setFlashdata('success', 'Riwayat analisis berhasil dihapus.');
        } else {
            session()->setFlashdata('error', 'Data riwayat tidak ditemukan.');
        }

        return redirect()->to('/analisa');
    }
}