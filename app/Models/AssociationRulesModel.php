<?php

namespace App\Models;

use CodeIgniter\Model;

class AssociationRulesModel extends Model
{
    protected $table            = 'association_rules';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_hasil', 'antecedent', 'consequent', 'support', 'confidence', 'lift'];
    
    protected $useTimestamps    = false;
}