<?php

namespace App\Controllers;

use App\Models\VendorModel;

class Vendors extends BaseController
{
    // See all the suppliers we deal with
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new VendorModel();
        $db = \Config\Database::connect();
        
        // Latest ones at the top
        $vendors = $model->orderBy('id', 'DESC')->findAll();
        
        // Calculate total dues and top creditors
        $total_dues = 0;
        foreach($vendors as &$v) {
            $purchase_total = $db->table('purchases')
                                ->where('vendor_id', $v['id'])
                                ->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;
            $payment_total = $db->table('vendor_payments')
                               ->where('vendor_id', $v['id'])
                               ->selectSum('amount')->get()->getRow()->amount ?? 0;
            $v['balance'] = $purchase_total - $payment_total;
            $total_dues += $v['balance'];
        }

        $data['vendors'] = $vendors;
        $data['total_dues'] = $total_dues;
        
        // Top 3 Creditors (where we owe most)
        usort($vendors, function($a, $b) { return $b['balance'] - $a['balance']; });
        $data['top_creditors'] = array_slice(array_filter($vendors, function($v) { return $v['balance'] > 0; }), 0, 3);
        
        return view('vendors/index', $data);
    }

    // Show the add vendor form page
    public function add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        return view('vendors/add');
    }

    // Add a new company we buy from
    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $name    = trim($this->request->getPost('name'));
        $phone   = trim($this->request->getPost('phone'));
        $email   = trim($this->request->getPost('email'));
        $address = trim($this->request->getPost('address'));

        // --- Step 1: Required field checks ---
        if (empty($name)) {
            return redirect()->back()->with('error', 'Vendor name is required.');
        }
        if (strlen($name) < 3) {
            return redirect()->back()->with('error', 'Vendor name must be at least 3 characters long.');
        }
        if (empty($phone)) {
            return redirect()->back()->with('error', 'Phone number is required.');
        }
        if (!is_numeric($phone)) {
            return redirect()->back()->with('error', 'Phone number must contain digits only.');
        }
        if (strlen($phone) !== 11) {
            return redirect()->back()->with('error', 'Phone number must be exactly 11 digits (e.g. 03001234567). You entered ' . strlen($phone) . ' digit(s).');
        }
        if (empty($email)) {
            return redirect()->back()->with('error', 'Email address is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email address (e.g. name@domain.com).');
        }
        if (empty($address)) {
            return redirect()->back()->with('error', 'Office address is required.');
        }

        // --- Step 2: Duplicate check ---
        $model = new VendorModel();
        $existing = $model->where('name', $name)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'A vendor with the name "' . esc($name) . '" already exists.');
        }

        // --- Step 3: Save (skip model-level validation since we did it manually) ---
        $saved = $model->skipValidation(true)->insert([
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email ?: null,
            'address' => $address ?: null,
        ]);

        if ($saved) {
            return redirect()->to(base_url('vendors'))->with('success', 'Vendor "' . esc($name) . '" added successfully!');
        }

        return redirect()->back()->with('error', 'Database error: Could not save vendor. Please try again.');
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $id      = (int) $this->request->getPost('id');
        $name    = trim($this->request->getPost('name'));
        $phone   = trim($this->request->getPost('phone'));
        $email   = trim($this->request->getPost('email'));
        $address = trim($this->request->getPost('address'));

        // --- Validation ---
        if (empty($name)) {
            return redirect()->back()->with('error', 'Vendor name is required.');
        }
        if (strlen($name) < 3) {
            return redirect()->back()->with('error', 'Vendor name must be at least 3 characters long.');
        }
        if (empty($phone)) {
            return redirect()->back()->with('error', 'Phone number is required.');
        }
        if (!is_numeric($phone)) {
            return redirect()->back()->with('error', 'Phone number must contain digits only.');
        }
        if (strlen($phone) !== 11) {
            return redirect()->back()->with('error', 'Phone number must be exactly 11 digits (e.g. 03001234567). You entered ' . strlen($phone) . ' digit(s).');
        }
        if (empty($email)) {
            return redirect()->back()->with('error', 'Email address is required.');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Please enter a valid email address (e.g. name@domain.com).');
        }
        if (empty($address)) {
            return redirect()->back()->with('error', 'Office address is required.');
        }

        $model = new VendorModel();
        $model->skipValidation(true)->update($id, [
            'name'    => $name,
            'phone'   => $phone,
            'email'   => $email ?: null,
            'address' => $address ?: null,
        ]);
        return redirect()->back()->with('success', 'Vendor "' . esc($name) . '" updated successfully!');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new VendorModel();
        $model->delete($id);
        return redirect()->to(base_url('vendors'))->with('success', 'Vendor removed.');
    }
}
