<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    public function dashboard()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $model = new ProductModel();
        $catModel = new CategoryModel();
        $stockModel = new \App\Models\StockModel();
        $saleModel = new \App\Models\SaleModel();

        // Basic Stats
        $data['total_products']   = $model->countAllResults();
        $data['total_categories'] = $catModel->countAllResults();

        $vendorModel = new \App\Models\VendorModel();
        $data['total_vendors'] = $vendorModel->countAllResults();

        // Use raw DB query for 100% accurate stock figures
        // Only count items for products that still exist and have qty > 0
        $db = \Config\Database::connect();

        $stockRow = $db->query("SELECT SUM(s.qty) as total_qty, SUM(s.qty * s.cost) as stock_value 
                                FROM stock_purchase s 
                                JOIN products p ON p.id = s.product_id 
                                WHERE s.qty > 0")->getRow();
        $data['total_items_in_stock'] = $stockRow->total_qty ?? 0;
        $data['total_stock_value']    = $stockRow->stock_value ?? 0;

        $today = date('Y-m-d');
        $data['today_sales'] = $saleModel->where('DATE(sale_date)', $today)->countAllResults();

        // Calculate Today's Profit
        $todaySalesData = $saleModel->select('sales.*, stock_purchase.cost as cost_price')
                                   ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                                   ->where('DATE(sale_date)', $today)
                                   ->findAll();
        
        $todayProfit = 0;
        foreach($todaySalesData as $ts) {
            $todayProfit += ($ts['sale_price'] - $ts['cost_price']) * $ts['qty'];
        }
        $data['today_profit'] = $todayProfit;

        // 1. Real Chart Data (Last 7 Days Sales)
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
        $chartSales = $saleModel->select("DATE(sale_date) as day, SUM(qty * sale_price) as total")
                                ->where('sale_date >=', $sevenDaysAgo)
                                ->groupBy('day')
                                ->orderBy('day', 'ASC')
                                ->findAll();
        
        $chartLabels = [];
        $chartValues = [];
        for($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('D', strtotime($date));
            $chartLabels[] = $label;
            
            $val = 0;
            foreach($chartSales as $cs) {
                if($cs['day'] == $date) {
                    $val = $cs['total'];
                    break;
                }
            }
            $chartValues[] = $val;
        }
        $data['chart_labels'] = json_encode($chartLabels);
        $data['chart_values'] = json_encode($chartValues);

        // 2. Real Alerts: Expiring Soon (Next 30 days)
        $oneMonthFromNow = date('Y-m-d', strtotime('+30 days'));
        $data['expiring_soon'] = $stockModel->select('stock_purchase.*, products.name as product_name')
                                          ->join('products', 'products.id = stock_purchase.product_id')
                                          ->where('expiry_date <=', $oneMonthFromNow)
                                          ->where('expiry_date >=', date('Y-m-d'))
                                          ->where('qty >', 0)
                                          ->limit(3)
                                          ->findAll();

        // 3. Real Alerts: Low Stock (Less than 10 units)
        $data['low_stock'] = $stockModel->select('stock_purchase.*, products.name as product_name')
                                      ->join('products', 'products.id = stock_purchase.product_id')
                                      ->where('qty <', 10)
                                      ->where('qty >', 0)
                                      ->limit(3)
                                      ->findAll();

        return view('products/dashboard', $data);
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $model = new ProductModel();
        $catModel = new CategoryModel();

        // Product list for table
        $data['products'] = $model->select('products.*, categories.name as category_name')
                                 ->join('categories', 'categories.id = products.category_id', 'left')
                                 ->findAll();
        
        $data['categories'] = $catModel->findAll();
        
        return view('products/index', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $catModel = new CategoryModel();
        $data['categories'] = $catModel->findAll();

        return view('products/add', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $data = [
            'name'        => $this->request->getPost('name'),
            'cost'        => $this->request->getPost('cost'),
            'category_id' => $this->request->getPost('category_id'),
            'unit'        => $this->request->getPost('unit'),
            'unit_value'  => $this->request->getPost('unit_value'),
            'form_6'      => $this->request->getPost('form_6'),
            'form_7'      => $this->request->getPost('form_7'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('products'))->with('success', 'Product added successfully');
        }
        return redirect()->back()->with('error', 'Failed to add product');
    }

    public function edit($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $catModel = new CategoryModel();
        
        $data['product'] = $model->find($id);
        $data['categories'] = $catModel->findAll();
        
        if (!$data['product']) return redirect()->to(base_url('products'))->with('error', 'Product not found');
        
        return view('products/edit', $data);
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $id = $this->request->getPost('id');
        
        $data = [
            'name'        => $this->request->getPost('name'),
            'cost'        => $this->request->getPost('cost'),
            'category_id' => $this->request->getPost('category_id'),
            'unit'        => $this->request->getPost('unit'),
            'unit_value'  => $this->request->getPost('unit_value'),
            'form_6'      => $this->request->getPost('form_6'),
            'form_7'      => $this->request->getPost('form_7'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('products'))->with('success', 'Product updated successfully');
        }
        return redirect()->back()->with('error', 'Failed to update product');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new ProductModel();
        $model->delete($id);
        return redirect()->to(base_url('products'))->with('success', 'Product deleted successfully');
    }
}
