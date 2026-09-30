<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 bg-white rounded shadow-sm border-start border-primary border-5">
                <h4 class="mb-1">Selamat Datang di Sistem Analisis Apriori!</h4>
                <p class="text-muted mb-0">Platform ini dirancang untuk menemukan pola asosiasi keranjang belanja secara otomatis.</p>
            </div>
        </div>
    </div>

    <style>
        .card-stat { transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
        .icon-bg { font-size: 2.5rem; opacity: 0.3; position: absolute; right: 20px; bottom: 20px; }
    </style>

    <div class="row mb-4">
        <div class="col-md-6 mb-3 mb-md-0">
            <div class="card card-stat bg-primary text-white h-100 border-0 shadow-sm position-relative overflow-hidden">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold mb-2 opacity-75">Total Transaksi Tersimpan</h6>
                    <h1 class="display-5 fw-bold mb-0"><?= number_format($total_transaksi ?? 0, 0, ',', '.') ?></h1>
                </div>
                <div class="icon-bg">🛒</div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stat bg-success text-white h-100 border-0 shadow-sm position-relative overflow-hidden">
                <div class="card-body p-4">
                    <h6 class="text-uppercase fw-bold mb-2 opacity-75">Total Analisis Berhasil</h6>
                    <h1 class="display-5 fw-bold mb-0"><?= number_format($total_proses ?? 0, 0, ',', '.') ?></h1>
                </div>
                <div class="icon-bg">📊</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5 class="fw-bold mb-3 mt-2 text-dark">Aksi Cepat (Quick Actions)</h5>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <span class="fs-3">📄</span>
                    </div>
                    <h6 class="fw-bold">Upload Dataset Baru</h6>
                    <p class="text-muted small">Tambahkan data riwayat transaksi dalam format Excel/CSV ke dalam sistem.</p>
                    <a href="<?= base_url('/transaksi') ?>" class="btn btn-outline-primary btn-sm w-100 mt-2">Menuju Manajemen Transaksi</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <span class="fs-3">⚙️</span>
                    </div>
                    <h6 class="fw-bold">Eksekusi Apriori</h6>
                    <p class="text-muted small">Mulai proses data mining dengan menentukan nilai Minimum Support & Confidence.</p>
                    <a href="<?= base_url('/analisa') ?>" class="btn btn-outline-success btn-sm w-100 mt-2">Mulai Proses Apriori</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3 bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <span class="fs-3">📖</span>
                    </div>
                    <h6 class="fw-bold">Dokumentasi & Laporan</h6>
                    <p class="text-muted small">Ekspor hasil aturan asosiasi terbaik ke dalam format file PDF atau Excel.</p>
                    <button class="btn btn-outline-secondary btn-sm w-100 mt-2" onclick="alert('Masuk ke menu Proses Apriori dan lihat riwayat hasil untuk melakukan ekspor.')">Lihat Petunjuk Ekspor</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>