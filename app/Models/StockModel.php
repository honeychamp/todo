<?php

namespace App\Models;

use CodeIgniter\Model;

class StockModel extends Model
{
    // Table name for stock purchases
    protected $table            = 'stock_purchase';
    // Primary key
    protected $primaryKey       = 'id';
    // All the data we keep for stock entries
    protected $allowedFields    = ['batch_id', 'vendor_id', 'product_id', 'manufacture_date', 'expiry_date', 'qty', 'cost', 'price'];
}
