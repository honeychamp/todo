<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    // Table name in database
    protected $table            = 'products';
    // Primary key of the table
    protected $primaryKey       = 'id';
    // Fields that we are allowed to save
    protected $allowedFields    = ['name', 'vendor', 'cost', 'reg_number', 'category_id'];
}
