<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $model = new ProductModel();
        
        $data['products'] = $model->select('products.*, categories.name as category_name')
                                 ->join('categories', 'categories.id = products.category_id', 'left')
                                 ->findAll();
        
        $catModel = new CategoryModel();
        $data['categories'] = $catModel->findAll();

        $stockModel = new \App\Models\StockModel();
        $saleModel = new \App\Models\SaleModel();

        $data['total_products'] = $model->countAllResults();
        $data['total_categories'] = $catModel->countAllResults();
        
        $stockData = $stockModel->selectSum('qty')->first();
        $data['total_items_in_stock'] = $stockData['qty'] ?? 0;

        $today = date('Y-m-d');
        $data['today_sales'] = $saleModel->where('DATE(sale_date)', $today)->countAllResults();
        
        return view('products/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $data = [
            'name'        => $this->request->getPost('name'),
            'vendor'      => $this->request->getPost('vendor'),
            'cost'        => $this->request->getPost('cost'),
            'reg_number'  => $this->request->getPost('reg_number'),
            'category_id' => $this->request->getPost('category_id'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('products'))->with('success', 'Product added successfully');
        }
        return redirect()->back()->with('error', 'Failed to add product');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $model->delete($id);
        return redirect()->to(base_url('products'))->with('success', 'Product deleted successfully');
    }
}
