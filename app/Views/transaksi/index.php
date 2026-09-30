<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    .badge-produk { font-size: 0.85rem; font-weight: normal; margin: 2px; display: inline-block; }
</style>
<?= $this->endSection() ?>

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

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h5 class="mb-0 fw-bold">Manajemen Data Transaksi</h5>
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalManual">
                    + Input Manual
                </button>
                <button type="button" class="btn btn-success btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#modalUpload">
                    Upload CSV/Excel
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelTransaksi" class="table table-bordered table-hover align-middle w-100">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">ID Transaksi</th>
                            <th width="55%">Item / Produk</th>
                            <th width="10%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($transaksi)): ?>
                            <?php $no = 1; foreach($transaksi as $row): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td data-sort="<?= date('Y-m-d', strtotime($row['tanggal'])) ?>">
                                    <?= date('d-M-Y', strtotime($row['tanggal'])); ?>
                                </td>
                                <td><span class="badge bg-secondary"><?= $row['id_transaksi']; ?></span></td>
                                <td>
                                    <?php if(!empty($row['produk'])): ?>
                                        <?php foreach($row['produk'] as $p): ?>
                                            <span class="badge bg-secondary text-white badge-produk"><?= $p ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-danger fst-italic">Tidak ada item</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/transaksi/hapus/' . $row['id_transaksi']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus transaksi <?= $row['id_transaksi'] ?> beserta isi produknya?');">
                                        Hapus
                                    </a>
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

<div class="modal fade" id="modalUpload" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?= base_url('/transaksi/upload') ?>" method="POST" enctype="multipart/form-data">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Upload Dataset Transaksi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="alert alert-info py-2">
                Format yang didukung: .csv, .xls, .xlsx.<br>
                Kolom A: Tanggal | Kolom B: ID Transaksi | Kolom C: Produk (dipisah koma)
            </div>
            <div class="mb-3">
                <label for="file_excel" class="form-label">Pilih File</label>
                <input class="form-control" type="file" id="file_excel" name="file_excel" accept=".csv, .xls, .xlsx" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Proses Upload</button>
        </div>
        </div>
    </form>
  </div>
</div>

<div class="modal fade" id="modalManual" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?= base_url('/transaksi/manual') ?>" method="POST">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Input Transaksi Manual</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Tanggal Transaksi</label>
                <input type="date" class="form-control" name="tanggal" required>
            </div>
            <div class="mb-3">
                <label class="form-label">ID Transaksi</label>
                <input type="text" class="form-control" name="id_transaksi" placeholder="Contoh: TRX001" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Daftar Produk (Pisahkan dengan koma)</label>
                <textarea class="form-control" name="produk" rows="3" placeholder="Contoh: Paracetamol, Vitamin C" required></textarea>
                <div class="form-text">Produk akan otomatis di-uppercase dan trim spasi.</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
        </div>
        </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        $('#tabelTransaksi').DataTable({
            // Konfigurasi dropdown jumlah entri sesuai permintaan (10, 25, 50, 100, 150, 200, dan Semua)
            "lengthMenu": [[10, 25, 50, 100, 150, 200, -1], [10, 25, 50, 100, 150, 200, "Semua"]],
            "pageLength": 10, // Default yang ditampilkan awal
            
            // Mengubah bahasa teks tabel menjadi Bahasa Indonesia
            "language": {
                "search": "Cari Data:",
                "lengthMenu": "Tampilkan _MENU_ entri",
                "info": "Menampilkan _START_ hingga _END_ dari total _TOTAL_ transaksi",
                "infoEmpty": "Menampilkan 0 hingga 0 dari 0 transaksi",
                "infoFiltered": "(disaring dari total _MAX_ transaksi)",
                "zeroRecords": "Tidak ditemukan data yang sesuai dengan pencarian Anda.",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            
            // Menonaktifkan sorting panah atas/bawah pada kolom No(0) dan Aksi(4) agar tidak membingungkan
            "columnDefs": [
                { "orderable": false, "targets": [0, 4] } 
            ]
        });
    });
</script>
<?= $this->endSection() ?>