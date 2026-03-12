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
    protected $allowedFields    = ['invoice_no', 'doctor_id', 'gross_amount', 'total_amount', 'total_discount', 'sale_date', 'manual_dr_name', 'manual_dr_phone'];
}
