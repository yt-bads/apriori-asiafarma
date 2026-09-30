<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Sistem Apriori Asiafarma') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-light pb-5">

<?php 
    $uri = service('uri'); 
    $segment = $uri->getSegment(1); 
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?= base_url('/dashboard') ?>">Asiafarma Apriori</a>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($segment == 'dashboard') ? 'active fw-bold' : '' ?>" href="<?= base_url('/dashboard') ?>">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($segment == 'transaksi') ? 'active fw-bold' : '' ?>" href="<?= base_url('/transaksi') ?>">Data Transaksi</a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= ($segment == 'analisa' || $segment == 'hasil') ? 'active fw-bold' : '' ?>" href="<?= base_url('/analisa') ?>">Proses Apriori</a>
        </li>
      </ul>
      
      <div class="d-flex align-items-center">
          <span class="navbar-text text-white me-3 d-none d-md-block">
              Halo, <strong><?= session()->get('nama'); ?></strong>
          </span>
          <button type="button" class="btn btn-outline-light btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
              Logout
          </button>
      </div>
    </div>
  </div>
</nav>

<?= $this->renderSection('content') ?>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title fw-bold" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body py-4 text-center">
        <div class="mb-3">
            <span style="font-size: 3rem;">👋</span>
        </div>
        <h5 class="fw-bold text-dark">Apakah Anda yakin ingin keluar?</h5>
        <p class="text-muted mb-0">Sesi Anda akan diakhiri dan Anda harus login kembali untuk masuk ke sistem.</p>
      </div>
      <div class="modal-footer bg-light justify-content-center">
        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
        <a href="<?= base_url('/logout') ?>" class="btn btn-danger px-4 fw-bold">Ya, Keluar Sekarang</a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?= $this->renderSection('scripts') ?>

</body>
</html>