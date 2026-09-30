<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .badge-produk { font-size: 0.85rem; font-weight: normal; margin-right: 4px; margin-bottom: 4px; display: inline-block;}
    .lift-high { color: #198754; font-weight: bold; }
    .lift-low { color: #dc3545; font-weight: bold; }
    .chart-container { position: relative; height: 350px; width: 100%; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container pb-5">
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <strong>Sukses!</strong> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-white rounded">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1 text-primary">Hasil Analisis Apriori</h4>
                    <p class="text-muted mb-0">Diproses pada: <?= date('d F Y, H:i', strtotime($header['tanggal_proses'])) ?> WIB</p>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="badge bg-secondary p-2 me-2">Min Support: <?= $header['min_support'] ?>%</span>
                    <span class="badge bg-secondary p-2">Min Confidence: <?= $header['min_confidence'] ?>%</span>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs mb-4" id="resultTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold" id="rules-tab" data-bs-toggle="tab" data-bs-target="#rules" type="button">Association Rules (Hasil Akhir)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold" id="itemset-tab" data-bs-toggle="tab" data-bs-target="#itemset" type="button">Frequent Itemsets (Proses Iterasi)</button>
        </li>
    </ul>

    <div class="tab-content" id="resultTabsContent">
        <div class="tab-pane fade show active" id="rules" role="tabpanel">
            
            <?php if(!empty($rules)): ?>
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Grafik Top 10 Association Rules (Berdasarkan Confidence)</h6>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="aprioriChart"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Daftar Lengkap Aturan Asosiasi</h5>
                    <div>
                        <a href="<?= base_url('/hasil/export_excel/' . $header['id']) ?>" class="btn btn-success btn-sm">Export Excel</a>
                        <a href="<?= base_url('/hasil/export_pdf/' . $header['id']) ?>" class="btn btn-danger btn-sm">Export PDF</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="35%">Jika Beli (Antecedent)</th>
                                    <th width="35%">Maka Beli (Consequent)</th>
                                    <th width="8%" class="text-center">Support</th>
                                    <th width="8%" class="text-center">Confidence</th>
                                    <th width="9%" class="text-center">Lift Ratio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($rules)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada aturan yang memenuhi syarat.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no=1; foreach($rules as $rule): ?>
                                        <?php 
                                            $antecedentArr = json_decode($rule['antecedent'], true);
                                            $consequentArr = json_decode($rule['consequent'], true);
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no++ ?></td>
                                            <td>
                                                <?php foreach($antecedentArr as $item): ?>
                                                    <span class="badge bg-primary badge-produk"><?= $item ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>
                                                <?php foreach($consequentArr as $item): ?>
                                                    <span class="badge bg-success badge-produk"><?= $item ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td class="text-center"><?= number_format($rule['support'], 2) ?>%</td>
                                            <td class="text-center"><?= number_format($rule['confidence'], 2) ?>%</td>
                                            <td class="text-center">
                                                <span class="<?= ($rule['lift'] > 1) ? 'lift-high' : 'lift-low' ?>">
                                                    <?= number_format($rule['lift'], 3) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="itemset" role="tabpanel">
            <?php if(empty($frequent_itemsets)): ?>
                <div class="alert alert-warning">Data iterasi itemset kosong.</div>
            <?php else: ?>
                <?php foreach($frequent_itemsets as $iterasi => $itemsets): ?>
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-light py-3">
                            <h6 class="mb-0 fw-bold">Iterasi <?= $iterasi ?> (Memenuhi Support >= <?= $header['min_support'] ?>%)</h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th width="5%" class="text-center">No</th>
                                            <th width="65%">Itemset (Kombinasi Produk)</th>
                                            <th width="15%" class="text-center">Jumlah Muncul</th>
                                            <th width="15%" class="text-center">Support (%)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $no=1; foreach($itemsets as $row): ?>
                                            <?php $produkArr = json_decode($row['itemset'], true); ?>
                                            <tr>
                                                <td class="text-center"><?= $no++ ?></td>
                                                <td>
                                                    <?php foreach($produkArr as $item): ?>
                                                        <span class="badge bg-info text-dark badge-produk"><?= $item ?></span>
                                                    <?php endforeach; ?>
                                                </td>
                                                <td class="text-center"><?= $row['support_count'] ?> kali</td>
                                                <td class="text-center"><?= number_format($row['support_persen'], 2) ?>%</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if(!empty($rules)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('aprioriChart').getContext('2d');
        const chartLabels = <?= $chartLabels ?>;
        const chartData = <?= $chartData ?>;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Confidence (%)',
                    data: chartData,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6
                }]
            },
            options: {
                indexAxis: 'y',
                maintainAspectRatio: false,
                scales: {
                    x: { beginAtZero: true, max: 100, title: { display: true, text: 'Nilai Confidence (%)' } },
                    y: { ticks: { autoSkip: false, font: { size: 11 } } }
                },
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return ' Confidence: ' + context.parsed.x + '%'; }
                        }
                    }
                }
            }
        });
    });
</script>
<?php endif; ?>
<?= $this->endSection() ?>