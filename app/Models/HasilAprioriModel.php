<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilAprioriModel extends Model
{
    protected $table            = 'hasil_apriori';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['min_support', 'min_confidence', 'tanggal_proses'];
    
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
}