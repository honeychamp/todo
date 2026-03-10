<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleDetailModel extends Model
{
    protected $table            = 'sale_details';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['sale_id', 'product_id', 'product_detail_id', 'stock_id', 'qty', 'sale_price', 'discount'];
    protected $useTimestamps    = false;
}
