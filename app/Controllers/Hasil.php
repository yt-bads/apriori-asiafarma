<?php

namespace App\Controllers;

use App\Models\HasilAprioriModel;
use App\Models\FrequentItemsetModel;
use App\Models\AssociationRulesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class Hasil extends BaseController
{
    public function detail($id_hasil)
    {
        $hasilModel = new HasilAprioriModel();
        $frequentModel = new FrequentItemsetModel();
        $rulesModel = new AssociationRulesModel();

        $header = $hasilModel->find($id_hasil);
        
        if (!$header) {
            session()->setFlashdata('error', 'Data riwayat analisis tidak ditemukan.');
            return redirect()->to('/analisa');
        }

        // LIMITASI MEMORI: Hanya ambil maksimal 1000 itemset teratas agar HTML tidak crash
        $frequentMentah = $frequentModel->where('id_hasil', $id_hasil)->findAll(1000); 
        $frequentGrouped = [];
        foreach ($frequentMentah as $row) {
            $frequentGrouped[$row['iterasi']][] = $row;
        }

        // LIMITASI MEMORI: Hanya ambil maksimal 300 rules terbaik berdasarkan confidence
        $rulesData = $rulesModel->where('id_hasil', $id_hasil)
                                ->orderBy('confidence', 'DESC')
                                ->orderBy('support', 'DESC')
                                ->findAll(300);

        $chartLabels = [];
        $chartData = [];
        $count = 0;

        foreach ($rulesData as $rule) {
            if ($count >= 10) break;

            $antArr = json_decode($rule['antecedent'], true);
            $conArr = json_decode($rule['consequent'], true);
            
            $label = implode(' + ', $antArr) . ' => ' . implode(' + ', $conArr);
            
            $chartLabels[] = $label;
            $chartData[] = $rule['confidence'];
            
            $count++;
        }

        $data = [
            'title'             => 'Detail Hasil Analisis',
            'header'            => $header,
            'frequent_itemsets' => $frequentGrouped,
            'rules'             => $rulesData,
            'chartLabels'       => json_encode($chartLabels),
            'chartData'         => json_encode($chartData)
        ];

        return view('hasil/detail', $data);
    }

    public function export_excel($id_hasil)
    {
        $rulesModel = new AssociationRulesModel();
        $headerModel = new HasilAprioriModel();
        
        $header = $headerModel->find($id_hasil);
        $rules = $rulesModel->where('id_hasil', $id_hasil)->orderBy('confidence', 'DESC')->findAll();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'LAPORAN HASIL ASOSIASI APRIORI - ASIAFARMA');
        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A2', 'Min Support: ' . $header['min_support'] . '% | Min Confidence: ' . $header['min_confidence'] . '%');
        $sheet->setCellValue('A3', 'Tanggal Proses: ' . $header['tanggal_proses']);

        $sheet->setCellValue('A5', 'No');
        $sheet->setCellValue('B5', 'Jika Beli (Antecedent)');
        $sheet->setCellValue('C5', 'Maka Beli (Consequent)');
        $sheet->setCellValue('D5', 'Support (%)');
        $sheet->setCellValue('E5', 'Confidence (%)');
        $sheet->setCellValue('F5', 'Lift Ratio');

        $rowNum = 6;
        $no = 1;
        foreach ($rules as $rule) {
            $ant = implode(', ', json_decode($rule['antecedent']));
            $cons = implode(', ', json_decode($rule['consequent']));

            $sheet->setCellValue('A' . $rowNum, $no++);
            $sheet->setCellValue('B' . $rowNum, $ant);
            $sheet->setCellValue('C' . $rowNum, $cons);
            $sheet->setCellValue('D' . $rowNum, $rule['support']);
            $sheet->setCellValue('E' . $rowNum, $rule['confidence']);
            $sheet->setCellValue('F' . $rowNum, $rule['lift']);
            $rowNum++;
        }

        $sheet->getStyle('A5:F5')->getFont()->setBold(true);
        foreach(range('A','F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        $filename = 'Hasil_Apriori_Asiafarma_' . $id_hasil . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function export_pdf($id_hasil)
    {
        $rulesModel = new AssociationRulesModel();
        $headerModel = new HasilAprioriModel();
        
        $data['header'] = $headerModel->find($id_hasil);
        $data['rules'] = $rulesModel->where('id_hasil', $id_hasil)->orderBy('confidence', 'DESC')->findAll();

        $dompdf = new Dompdf();
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); 
        $dompdf->setOptions($options);

        $html = view('hasil/export_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Laporan_Apriori_Asiafarma_" . $id_hasil . ".pdf", ["Attachment" => 1]);
    }
}