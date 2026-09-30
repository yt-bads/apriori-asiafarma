<?php

namespace App\Libraries;

class apriori
{
    private $minSupport;
    private $minConfidence;
    private $totalTransactions;
    private $transactions = [];

    public function process($dataTransaksiMentah, $minSupport, $minConfidence)
    {
        $this->minSupport = $minSupport;
        $this->minConfidence = $minConfidence;
        $this->transactions = $this->formatTransactions($dataTransaksiMentah);
        $this->totalTransactions = count($this->transactions);

        if ($this->totalTransactions == 0) {
            return ['frequent_itemsets' => [], 'rules' => []];
        }

        $frequentItemsets = [];
        $allFrequentItems = []; // Menyimpan semua Lk untuk rule generation

        // STEP 1: Dapatkan semua item unik dari transaksi (C1)
        $items = [];
        foreach ($this->transactions as $trx) {
            foreach ($trx as $item) {
                if (!in_array($item, $items)) {
                    $items[] = $item;
                }
            }
        }
        sort($items);

        // STEP 2: Hitung L1 (Frequent 1-Itemset)
        $k = 1;
        $L1 = $this->calculateSupport(array_map(function($item) { return [$item]; }, $items));
        
        if (empty($L1)) {
            return ['frequent_itemsets' => [], 'rules' => []];
        }

        $frequentItemsets["L{$k}"] = $L1;
        $allFrequentItems = array_merge($allFrequentItems, $L1);
        $currentL = $L1;

        // STEP 3 & 4: Iterasi L2, L3, dst
        while (!empty($currentL)) {
            $k++;
            // Generate kombinasi Ck dari L(k-1)
            $candidates = $this->generateCandidates($currentL, $k);
            
            if (empty($candidates)) {
                break;
            }

            // Hitung support dan filter untuk mendapatkan Lk
            $nextL = $this->calculateSupport($candidates);
            
            if (empty($nextL)) {
                break;
            }

            $frequentItemsets["L{$k}"] = $nextL;
            $allFrequentItems = array_merge($allFrequentItems, $nextL);
            $currentL = $nextL;
        }

        // STEP 5: Generate Association Rules
        $rules = $this->generateRules($allFrequentItems);

        return [
            'frequent_itemsets' => $frequentItemsets,
            'rules'             => $rules
        ];
    }

    private function formatTransactions($dataMentah)
    {
        $formatted = [];
        foreach ($dataMentah as $row) {
            $formatted[$row['id_transaksi']][] = $row['nama_produk'];
        }
        return array_values($formatted);
    }

    private function calculateSupport($candidates)
    {
        $frequentSet = [];
        foreach ($candidates as $candidate) {
            $count = 0;
            // Cek apakah kandidat itemset ada di dalam setiap transaksi
            foreach ($this->transactions as $trx) {
                // array_diff akan kosong jika semua item kandidat ada di dalam transaksi
                if (empty(array_diff($candidate, $trx))) {
                    $count++;
                }
            }

            $supportPercent = ($count / $this->totalTransactions) * 100;

            if ($supportPercent >= $this->minSupport) {
                $frequentSet[] = [
                    'itemset' => $candidate,
                    'count'   => $count,
                    'support' => $supportPercent
                ];
            }
        }
        return $frequentSet;
    }

    private function generateCandidates($prevL, $k)
    {
        $candidates = [];
        $totalPrev = count($prevL);

        // Menggabungkan itemset dari L(k-1) untuk membentuk Ck
        for ($i = 0; $i < $totalPrev; $i++) {
            for ($j = $i + 1; $j < $totalPrev; $j++) {
                $itemset1 = $prevL[$i]['itemset'];
                $itemset2 = $prevL[$j]['itemset'];
                
                // Gabungkan kedua array dan hilangkan duplikat
                $merged = array_unique(array_merge($itemset1, $itemset2));
                sort($merged);

                // Pastikan kandidat baru tepat memiliki jumlah item sebanyak $k
                if (count($merged) == $k) {
                    if (!in_array($merged, $candidates)) {
                        $candidates[] = $merged;
                    }
                }
            }
        }
        return $candidates;
    }

    private function generateRules($allFrequentItems)
    {
        $rules = [];
        // Buat dictionary/lookup table untuk support agar pencarian lebih cepat
        $supportLookup = [];
        foreach ($allFrequentItems as $fi) {
            $key = implode('||', $fi['itemset']);
            $supportLookup[$key] = $fi['support'];
        }

        foreach ($allFrequentItems as $fi) {
            $itemset = $fi['itemset'];
            $n = count($itemset);
            
            // Rules hanya bisa dibentuk dari minimal 2 item (2-Itemset)
            if ($n >= 2) {
                // Dapatkan semua kemungkinan subset/kombinasi dari itemset
                $subsets = $this->getSubsets($itemset);

                foreach ($subsets as $antecedent) {
                    // Consequent = Itemset total dikurangi Antecedent
                    $consequent = array_values(array_diff($itemset, $antecedent));

                    if (!empty($consequent)) {
                        $antecedentKey = implode('||', $antecedent);
                        $consequentKey = implode('||', $consequent);
                        
                        $supportA = $supportLookup[$antecedentKey] ?? 0;
                        $supportB = $supportLookup[$consequentKey] ?? 0;
                        $supportAB = $fi['support'];

                        if ($supportA > 0) {
                            $confidence = ($supportAB / $supportA) * 100;

                            if ($confidence >= $this->minConfidence) {
                                // Hitung Lift Ratio
                                // support B harus dalam bentuk probabilitas desimal untuk perhitungan lift
                                $probB = $supportB / 100; 
                                $lift = 0;
                                if ($probB > 0) {
                                    $lift = ($confidence / 100) / $probB;
                                }

                                $rules[] = [
                                    'antecedent' => $antecedent,
                                    'consequent' => $consequent,
                                    'support'    => $supportAB,
                                    'confidence' => $confidence,
                                    'lift'       => $lift
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Urutkan rules berdasarkan confidence tertinggi, lalu support tertinggi
        usort($rules, function($a, $b) {
            if ($a['confidence'] == $b['confidence']) {
                return $b['support'] <=> $a['support'];
            }
            return $b['confidence'] <=> $a['confidence'];
        });

        return $rules;
    }

    private function getSubsets($array)
    {
        $results = [[]];
        foreach ($array as $element) {
            foreach ($results as $combination) {
                $results[] = array_merge([$element], $combination);
            }
        }
        
        // Buang himpunan kosong dan himpunan utuh (karena antecedent tidak boleh kosong atau berisi semua item)
        $validSubsets = [];
        foreach ($results as $subset) {
            if (!empty($subset) && count($subset) < count($array)) {
                sort($subset);
                if (!in_array($subset, $validSubsets)) {
                    $validSubsets[] = $subset;
                }
            }
        }
        return $validSubsets;
    }
}