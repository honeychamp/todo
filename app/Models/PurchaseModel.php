<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseModel extends Model
{
    protected $table         = 'purchases';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['vendor_id', 'total_amount', 'note', 'date', 'status'];
    protected $useTimestamps = true;
}
