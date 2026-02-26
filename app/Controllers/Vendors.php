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
        
        // Calculate total dues and top creditors for the professional summary
        $total_dues = 0;
        foreach($vendors as &$v) {
            $purchase_total = $db->table('stock_purchase')
                                ->where('vendor_id', $v['id'])
                                ->select('SUM(initial_qty * cost) as total')
                                ->get()->getRow()->total ?? 0;
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

    // Add a new company we buy from
    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new VendorModel();
        $data = [
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'email'   => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ];

        // Trying to save the new vendor
        if ($model->save($data)) {
            return redirect()->to(base_url('vendors'))->with('success', 'Vendor added to the list.');
        }
        
        return redirect()->back()->with('errors', $model->errors());
    }

    // Delete vendor if we don't need them anymore
    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new \App\Models\VendorModel();
        $id = $this->request->getPost('id');
        $model->update($id, [
            'name' => $this->request->getPost('name'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ]);
        return redirect()->back()->with('success', 'Vendor updated successfully');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new VendorModel();
        $model->delete($id);
        return redirect()->to(base_url('vendors'))->with('success', 'Vendor removed.');
    }
}
