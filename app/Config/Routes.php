<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ====================================================================
// 1. ROUTE AUTENTIKASI (LOGIN & LOGOUT)
// ====================================================================
$routes->get('/', 'Auth::index');
$routes->get('/login', 'Auth::index');
$routes->post('/login/process', 'Auth::process');
$routes->get('/logout', 'Auth::logout');

// ====================================================================
// 2. ROUTE DASHBOARD (Hanya bisa diakses jika sudah login)
// ====================================================================
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'auth']);

// ====================================================================
// 3. ROUTE MANAJEMEN TRANSAKSI (Tahap 3 & 4)
// -> Ini adalah rute yang memicu error 404 Anda sebelumnya
// ====================================================================
$routes->get('/transaksi', 'Transaksi::index', ['filter' => 'auth']);
$routes->post('/transaksi/upload', 'Transaksi::upload_proses', ['filter' => 'auth']);
$routes->post('/transaksi/manual', 'Transaksi::simpan_manual', ['filter' => 'auth']);
$routes->get('/transaksi/hapus/(:segment)', 'Transaksi::hapus/$1', ['filter' => 'auth']);

// ====================================================================
// 4. ROUTE ANALISA APRIORI (Tahap 6)
// ====================================================================
$routes->get('/analisa', 'Analisa::index', ['filter' => 'auth']);
$routes->post('/analisa/proses', 'Analisa::proses', ['filter' => 'auth']);
$routes->get('/analisa/hapus/(:num)', 'Analisa::hapus/$1', ['filter' => 'auth']);

// ====================================================================
// 5. ROUTE HASIL & EXPORT REPORT (Tahap 7, 8, & 9)
// ====================================================================
$routes->get('/hasil/detail/(:num)', 'Hasil::detail/$1', ['filter' => 'auth']);
$routes->get('/hasil/export_excel/(:num)', 'Hasil::export_excel/$1', ['filter' => 'auth']);
$routes->get('/hasil/export_pdf/(:num)', 'Hasil::export_pdf/$1', ['filter' => 'auth']);