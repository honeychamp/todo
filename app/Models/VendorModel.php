<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    protected $table            = 'vendors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'phone', 'email', 'address', 'created_at', 'updated_at'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'  => 'required|min_length[3]|is_unique[vendors.name]',
        'phone' => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'name' => [
            'required'  => 'Vendor name is required.',
            'is_unique' => 'This vendor is already registered.'
        ],
        'phone' => [
            'required' => 'Phone number is required.'
        ]
    ];
}
