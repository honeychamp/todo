<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    // Table name in database
    protected $table            = 'categories';
    // Primary key
    protected $primaryKey       = 'id';
    // Fields allowed to be saved
    protected $allowedFields    = ['name'];
}
