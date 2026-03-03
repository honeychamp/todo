<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table            = 'doctors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'phone', 'address', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;

    protected $validationRules = [
        'name'  => 'required|min_length[3]',
        'phone' => 'required|exact_length[11]|numeric'
    ];
}
