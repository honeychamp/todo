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
        // Latest ones at the top
        $data['vendors'] = $model->orderBy('id', 'DESC')->findAll();
        
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
    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new VendorModel();
        $model->delete($id);
        return redirect()->to(base_url('vendors'))->with('success', 'Vendor removed.');
    }
}
