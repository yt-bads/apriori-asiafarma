<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container pb-5">
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">Konfigurasi Algoritma Apriori</h5>
                </div>
                <div class="card-body p-4">
                    <form action="<?= base_url('/analisa/proses') ?>" method="POST">
                        <div class="mb-4">
                            <label for="min_support" class="form-label fw-semibold">Minimum Support (%)</label>
                            <input type="number" step="0.01" class="form-control" id="min_support" name="min_support" placeholder="Contoh: 10" required>
                            <div class="form-text mt-2">
                                <strong>Support:</strong> Persentase minimum seberapa sering kombinasi produk muncul dari total transaksi.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="min_confidence" class="form-label fw-semibold">Minimum Confidence (%)</label>
                            <input type="number" step="0.01" class="form-control" id="min_confidence" name="min_confidence" placeholder="Contoh: 50" required>
                            <div class="form-text mt-2">
                                <strong>Confidence:</strong> Tingkat kepastian sebuah aturan.
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" onclick="this.innerHTML='Memproses Data...'; this.classList.add('disabled');">
                                Eksekusi Analisis Apriori
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold">Riwayat Hasil Analisis</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Waktu Proses</th>
                                    <th width="15%" class="text-center">Support</th>
                                    <th width="15%" class="text-center">Confidence</th>
                                    <th width="35%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($riwayat)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            Belum ada riwayat. Silakan lakukan eksekusi analisis di samping.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($riwayat as $row): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold d-block"><?= date('d M Y', strtotime($row['tanggal_proses'])) ?></span>
                                            <span class="small text-muted"><?= date('H:i:s', strtotime($row['tanggal_proses'])) ?> WIB</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $row['min_support'] ?>%</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary"><?= $row['min_confidence'] ?>%</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/hasil/detail/' . $row['id']) ?>" class="btn btn-info btn-sm text-white shadow-sm mb-1">Lihat Hasil & Export</a>
                                            <a href="<?= base_url('/analisa/hapus/' . $row['id']) ?>" class="btn btn-outline-danger btn-sm mb-1" onclick="return confirm('Yakin ingin menghapus riwayat analisis tanggal <?= date('d M Y', strtotime($row['tanggal_proses'])) ?> ini?');">Hapus</a>
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
    </div>
</div>
<?= $this->endSection() ?>