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
            // The following lines appear to be malformed HTML/PHP intended for a view,
            // but were provided as part of a controller method edit.
            // Inserting them directly would cause a syntax error.
            // Assuming the intent was to modify the success message or add a comment related to dashboard alerts.
            // As per instructions to make the file syntactically correct,
            // and given the instruction "refine the dashboard alert label",
            // I will interpret this as a comment or a placeholder for a future view change,
            // or a misunderstanding in the provided edit.
            // For now, I will keep the original redirect line as it is syntactically correct
            // and the provided snippet is not.
            // If the intent was to change the success message, please provide the correct PHP string.
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
        
        $pModel = new \App\Models\ProductModel();
        $hasProducts = $pModel->where('category_id', $id)->countAllResults();

        if ($hasProducts > 0) {
            return redirect()->back()->with('error', 'Cannot delete! This category has '.$hasProducts.' products assigned to it.');
        }

        $model = new CategoryModel();
        $model->delete($id);
        return redirect()->to(base_url('categories'))->with('success', 'Category removed from list.');
    }
}
