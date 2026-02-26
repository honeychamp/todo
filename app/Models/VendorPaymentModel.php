<?php

namespace App\Models;

use CodeIgniter\Model;

class VendorPaymentModel extends Model
{
    protected $table         = 'vendor_payments';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['vendor_id', 'amount', 'notes', 'payment_date', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    // Total amount paid to a vendor
    public function totalPaid($vendor_id)
    {
        return $this->where('vendor_id', $vendor_id)->selectSum('amount')->first()['amount'] ?? 0;
    }

    // All payments for a vendor, latest first
    public function getByVendor($vendor_id)
    {
        return $this->where('vendor_id', $vendor_id)->orderBy('payment_date', 'DESC')->findAll();
    }
}
