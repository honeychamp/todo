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
        'phone' => 'required|exact_length[11]|numeric'
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Vendor name is required.',
            'min_length' => 'Vendor name must be at least 3 characters.',
            'is_unique'  => 'This vendor name is already registered. Use a different name.'
        ],
        'phone' => [
            'required'      => 'Phone number is required.',
            'exact_length'  => 'Phone number must be exactly 11 digits (e.g. 03001234567).',
            'numeric'       => 'Phone number must contain digits only.'
        ]
    ];
}
