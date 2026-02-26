<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    // Table name for recorded sales
    protected $table            = 'sales';
    // Primary key
    protected $primaryKey       = 'id';
    // Fields we save when a sale happens
    protected $allowedFields    = ['stock_id', 'product_id', 'qty', 'sale_price', 'sale_date', 'customer_name', 'customer_phone'];
}
