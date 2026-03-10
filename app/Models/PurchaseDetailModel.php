<?php

namespace App\Models;

use CodeIgniter\Model;

class PurchaseDetailModel extends Model
{
    protected $table         = 'purchase_details';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['purchase_id', 'product_id', 'product_detail_id', 'batch_id', 'qty', 'cost', 'price', 'mfg_date', 'exp_date'];
    protected $useTimestamps = true;
}
