<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductDetailModel extends Model
{
    protected $table            = 'product_details';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['product_id', 'cost', 'unit', 'unit_value', 'form_6', 'form_7'];
    protected $useTimestamps    = true;
}
