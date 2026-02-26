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
    $db = \Config\Database::connect();

    // Calculate total units (Sum of all Initial - Sum of all Sold)
    // Calculate total value (Sum of currently held items * their cost)
    // Calculate total investment (Sum of all Initial * their cost)
    $stockRow = $db->query("SELECT 
                               (SELECT COALESCE(SUM(initial_qty), 0) FROM stock_purchase) - (SELECT COALESCE(SUM(qty), 0) FROM sales) as total_qty, 
                               (SELECT SUM((sp.initial_qty - COALESCE((SELECT SUM(qty) FROM sales WHERE stock_id = sp.id), 0)) * sp.cost) FROM stock_purchase sp) as stock_value, 
                               (SELECT COALESCE(SUM(initial_qty * cost), 0) FROM stock_purchase) as total_investment 
                           ")->getRow();

    
    $data['total_items_in_stock'] = $stockRow->total_qty ?? 0;
    $data['total_stock_value']    = $stockRow->stock_value ?? 0;
    $data['total_investment']     = $stockRow->total_investment ?? 0;

    $today = date('Y-m-d');
    $data['today_sales_count'] = $saleModel->where('DATE(sale_date)', $today)->countAllResults();

    // Today's Revenue and Profit
    $todaySalesData = $saleModel->select('sales.*, stock_purchase.cost as cost_price')
                           ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                           ->where('DATE(sale_date)', $today)
                           ->findAll();

    $todayRevenue = 0;
    $todayProfit = 0;
    foreach($todaySalesData as $ts) {
        $todayRevenue += $ts['sale_price'] * $ts['qty'];
        $todayProfit += ($ts['sale_price'] - $ts['cost_price']) * $ts['qty'];
    }
    $data['today_revenue'] = $todayRevenue;
    $data['today_profit'] = $todayProfit;

    // Today's Expenses
    $expenseModel = new \App\Models\ExpenseModel();
    $todayExpenses = $expenseModel->selectSum('amount')
                             ->where('DATE(expense_date)', $today)
                             ->first()['amount'] ?? 0;
    $data['today_expenses'] = $todayExpenses;
    $data['today_net_profit'] = $todayProfit - $todayExpenses;

    // Lifetime Stats
    $data['lifetime_sales'] = $saleModel->select('SUM(qty * sale_price) as total')->first()['total'] ?? 0;
    $allProfitData = $saleModel->select('sales.qty, sales.sale_price, stock_purchase.cost')
                              ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                              ->findAll();
    $totalLifeProfit = 0;
    foreach($allProfitData as $ap) {
        $totalLifeProfit += ($ap['sale_price'] - $ap['cost']) * $ap['qty'];
    }
    $totalAllExpenses = $expenseModel->selectSum('amount')->first()['amount'] ?? 0;
    $data['lifetime_net_profit'] = $totalLifeProfit - $totalAllExpenses;

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

    // 2. Real Alerts: Expiring Soon (Next 90 days)
    $threeMonthsFromNow = date('Y-m-d', strtotime('+90 days'));
    $data['expiring_soon'] = $db->query("SELECT s.*, p.name as product_name, 
                                        (s.initial_qty - COALESCE((SELECT SUM(qty) FROM sales WHERE stock_id = s.id), 0)) as current_qty
                                        FROM stock_purchase s 
                                        JOIN products p ON p.id = s.product_id 
                                        WHERE s.expiry_date <= ? AND s.expiry_date >= ?
                                        HAVING current_qty > 0 LIMIT 3", [$threeMonthsFromNow, date('Y-m-d')])->getResultArray();

    // 3. Real Alerts: Low Stock (Less than 10 units)
    $data['low_stock'] = $db->query("SELECT s.*, p.name as product_name, 
                                        (s.initial_qty - COALESCE((SELECT SUM(qty) FROM sales WHERE stock_id = s.id), 0)) as current_qty
                                        FROM stock_purchase s 
                                        JOIN products p ON p.id = s.product_id 
                                        HAVING current_qty < 10 AND current_qty > 0 LIMIT 3")->getResultArray();


        return view('products/dashboard', $data);
    }

    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $model = new ProductModel();
        $catModel = new CategoryModel();

        // Product list for table with current stock levels - Calculated dynamically
    $data['products'] = $model->select('products.*, categories.name as category_name, 
                                        (COALESCE(SUM(stock_purchase.initial_qty), 0) - COALESCE((SELECT SUM(qty) FROM sales WHERE product_id = products.id), 0)) as current_stock,
                                        COALESCE((SELECT SUM(qty) FROM sales WHERE product_id = products.id), 0) as total_sold_units,
                                        COALESCE((SELECT SUM(qty * sale_price) FROM sales WHERE product_id = products.id), 0) as total_revenue')
                             ->join('categories', 'categories.id = products.category_id', 'left')
                             ->join('stock_purchase', 'stock_purchase.product_id = products.id', 'left')
                             ->groupBy('products.id')
                             ->findAll();

        
        $data['categories'] = $catModel->findAll();
        
        return view('products/index', $data);
    }

    public function shortage_list()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new ProductModel();
        
        // Items where total stock (calculated dynamically) is less than 10
    $data['shortage_items'] = $model->select('products.*, categories.name as category_name, 
                                              (COALESCE(SUM(stock_purchase.initial_qty), 0) - COALESCE((SELECT SUM(qty) FROM sales WHERE product_id = products.id), 0)) as total_qty')
                                   ->join('categories', 'categories.id = products.category_id', 'left')
                                   ->join('stock_purchase', 'stock_purchase.product_id = products.id', 'left')
                                   ->groupBy('products.id')
                                   ->having('total_qty <', 10)
                                   ->orHaving('total_qty IS NULL')
                                   ->findAll();


        return view('products/shortage', $data);
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
