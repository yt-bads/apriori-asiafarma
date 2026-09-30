<?php

namespace App\Models;

use CodeIgniter\Model;

class FrequentItemsetModel extends Model
{
    protected $table            = 'frequent_itemset';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_hasil', 'iterasi', 'itemset', 'support_count', 'support_persen'];
    
    // Kita matikan timestamps karena tabel ini tidak memiliki kolom created_at/updated_at
    protected $useTimestamps    = false;
}