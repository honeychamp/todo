<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorModel extends Model
{
    /**
     * VendorModel: Manages pharmaceutical suppliers.
     * Structured to provide clean data for inventory linking.
     */
    protected $table            = 'vendors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['name', 'phone', 'email', 'address', 'created_at', 'updated_at'];

    // Timestamps for audit trails
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Custom validation messages that sound more "human" and helpful.
     */
    protected $validationRules = [
        'name'  => 'required|min_length[3]|is_unique[vendors.name]',
        'phone' => 'required|min_length[10]'
    ];

    protected $validationMessages = [
        'name' => [
            'required'  => 'We need the vendor name to identify who we are buying from.',
            'is_unique' => 'This vendor is already registered in our system.'
        ],
        'phone' => [
            'required' => 'A contact number is vital for procurement coordination.'
        ]
    ];
}
