<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorPaymentModel extends Model
{
    protected $table            = 'doctor_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['doctor_id', 'amount', 'payment_date', 'payment_method', 'notes', 'created_at', 'updated_at'];
    protected $useTimestamps    = true;
}
