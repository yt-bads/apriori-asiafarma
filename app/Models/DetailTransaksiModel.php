<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailTransaksiModel extends Model
{
    // Nama tabel di database
    protected $table            = 'detail_transaksi';
    
    // Primary key dari tabel
    protected $primaryKey       = 'id';
    
    // Format balikan data (array lebih ringan untuk Apriori)
    protected $returnType       = 'array';
    
    // Field yang diizinkan untuk diisi (mencegah mass assignment vulnerability)
    protected $allowedFields    = ['id_transaksi', 'nama_produk'];
    
    // Mengaktifkan pengisian otomatis untuk kolom created_at
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    
    // Kosongkan karena kita tidak memakai kolom updated_at di tabel ini
    protected $updatedField     = '';
}