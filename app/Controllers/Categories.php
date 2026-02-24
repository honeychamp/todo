<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\CategoryModel;

class Categories extends BaseController
{
    // List all types of drugs
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }
        $model = new CategoryModel();
        $data['categories'] = $model->findAll();
        return view('categories/index', $data);
    }

    // Save a new category
    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new CategoryModel();
        $name = $this->request->getPost('name');
        
        if (!empty($name)) {
            $model->insert(['name' => $name]);
            return redirect()->to(base_url('categories'))->with('success', 'Nice! Category added.');
        }
        return redirect()->back()->with('error', 'Please give a name to the category.');
    }

    // Remove a category
    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new CategoryModel();
        $id = $this->request->getPost('id');
        $model->update($id, [
            'name' => $this->request->getPost('name')
        ]);
        return redirect()->back()->with('success', 'Category updated successfully');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new CategoryModel();
        $model->delete($id);
        return redirect()->to(base_url('categories'))->with('success', 'Category removed from list.');
    }
}
