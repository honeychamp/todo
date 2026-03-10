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
        $PurchaseDetailModel = new \App\Models\PurchaseDetailModel();
        $saleModel = new \App\Models\SaleModel();

        // Basic Stats
        $data['total_products']   = $model->countAllResults();
        $data['total_categories'] = $catModel->countAllResults();

        $vendorModel = new \App\Models\VendorModel();
        $data['total_vendors'] = $vendorModel->countAllResults();

        // Calculate total units (Sum of all Initial - Sum of all Sold)
        // Calculate total value (Sum of currently held items * their cost)
        // Calculate total investment (Sum of all Initial * their cost)
        $db = \Config\Database::connect();
        $stockRow = $db->query("SELECT 
                                (SELECT COALESCE(SUM(qty), 0) FROM purchase_details) - (SELECT COALESCE(SUM(qty), 0) FROM sale_details) as total_qty, 
                                (SELECT SUM((sp.qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = sp.id), 0)) * sp.cost) FROM purchase_details sp) as stock_value, 
                                (SELECT COALESCE(SUM(qty * cost), 0) FROM purchase_details) as total_investment 
                            ")->getRow();

        $data['total_items_in_stock'] = $stockRow->total_qty ?? 0;
        $data['total_stock_value']    = $stockRow->stock_value ?? 0;
        $data['total_investment']     = $stockRow->total_investment ?? 0;

        $today = date('Y-m-d');
        $data['today_sales_count'] = $saleModel->where('DATE(sale_date)', $today)->countAllResults();

        // Today's Revenue and Profit
        $todaySalesData = $db->table('sale_details sd')
                             ->select('sd.*, purchase_details.cost as cost_price')
                             ->join('sales s', 's.id = sd.sale_id')
                             ->join('purchase_details', 'purchase_details.id = sd.stock_id', 'left')
                             ->where('DATE(s.sale_date)', $today)
                             ->get()->getResultArray();

        $todayRevenue = 0;
        $todayProfit = 0;
        foreach ($todaySalesData as $ts) {
            $subtotal = $ts['sale_price'] * $ts['qty'];
            $netSale = $subtotal - ($ts['discount'] ?? 0);
            $todayRevenue += $netSale;
            $cost = $ts['cost_price'] ?? 0;
            $todayProfit += ($netSale - ($cost * $ts['qty']));
        }
        $data['today_revenue'] = $todayRevenue;
        $data['today_profit'] = $todayProfit;

        // Today's Expenses
        $expenseModel = new \App\Models\ExpenseModel();
        $expenseRow = $expenseModel->selectSum('amount')
                                   ->where('DATE(expense_date)', $today)
                                   ->get()->getRow();
        $todayExpenses = $expenseRow->amount ?? 0;
        $data['today_expenses'] = $todayExpenses;
        $data['today_net_profit'] = $todayProfit - $todayExpenses;

        // Lifetime Stats
        $lifetimeData = $db->table('sale_details sd')
                           ->select('SUM((sd.qty * sd.sale_price) - sd.discount) as total_rev, SUM(((sd.sale_price * sd.qty) - sd.discount) - (pd.cost * sd.qty)) as total_prof')
                           ->join('purchase_details pd', 'pd.id = sd.stock_id', 'left')
                           ->get()->getRow();
        
        $data['lifetime_sales'] = $lifetimeData->total_rev ?? 0;
        $totalLifeProfit        = $lifetimeData->total_prof ?? 0;
        
        $totalAllExpensesRow = $expenseModel->selectSum('amount')->get()->getRow();
        $totalAllExpenses    = $totalAllExpensesRow->amount ?? 0;
        $data['lifetime_net_profit'] = $totalLifeProfit - $totalAllExpenses;

        // Doctor Stats
        $doctorModel = new \App\Models\DoctorModel();
        $data['total_doctors'] = $doctorModel->countAllResults();
        $data['total_doctor_receivables'] = $db->query("SELECT (SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE doctor_id IS NOT NULL) - (SELECT COALESCE(SUM(amount), 0) FROM doctor_payments) as net_receivable")->getRow()->net_receivable ?? 0;

        // Chart Data (Last 7 Days Sales)
        $sevenDaysAgo = date('Y-m-d', strtotime('-6 days'));
        $chartSales = $db->table('sale_details sd')
                           ->select("DATE(s.sale_date) as day, SUM((sd.qty * sd.sale_price) - sd.discount) as total")
                           ->join('sales s', 's.id = sd.sale_id')
                           ->where('s.sale_date >=', $sevenDaysAgo)
                           ->groupBy('day')
                           ->orderBy('day', 'ASC')
                           ->get()->getResultArray();
    
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

        // Expiring Soon (Next 90 days)
        $threeMonthsFromNow = date('Y-m-d', strtotime('+90 days'));
        $data['expiring_soon'] = $db->query("SELECT s.*, p.name as product_name, 
                                            (s.qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = s.id), 0)) as current_qty
                                            FROM purchase_details s 
                                            JOIN products p ON p.id = s.product_id 
                                            WHERE s.exp_date <= ? AND s.exp_date >= ?
                                            HAVING current_qty > 0 LIMIT 3", [$threeMonthsFromNow, date('Y-m-d')])->getResultArray();

        // Low Stock (Less than 10 units)
        $data['low_stock'] = $db->query("SELECT s.*, p.name as product_name, 
                                            (s.qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = s.id), 0)) as current_qty
                                            FROM purchase_details s 
                                            JOIN products p ON p.id = s.product_id 
                                            HAVING current_qty < 10 AND current_qty > 0 LIMIT 3")->getResultArray();

        // Top 5 Selling Products (Last 30 Days)
        $data['top_products'] = $db->query("SELECT p.name, SUM(sd.qty) as total_units, SUM(sd.qty * sd.sale_price - sd.discount) as total_revenue
                                            FROM sale_details sd
                                            JOIN sales s ON s.id = sd.sale_id
                                            JOIN products p ON p.id = sd.product_id
                                            WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                                            GROUP BY sd.product_id
                                            ORDER BY total_units DESC
                                            LIMIT 5")->getResultArray();

        $data['today_profit_margin'] = ($todayRevenue > 0) ? ($todayProfit / $todayRevenue) * 100 : 0;

        return view('products/dashboard', $data);
    }

    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        
        // Fetch from product_details and join with products
        $data['products'] = $db->table('product_details pd')
            ->select('pd.*, p.name as name, p.category_id, c.name as category_name, 
                    (COALESCE((SELECT SUM(qty) FROM purchase_details purd JOIN purchases pr ON pr.id = purd.purchase_id WHERE purd.product_detail_id = pd.id AND pr.status IN ("received","partial_paid","paid")), 0) - COALESCE((SELECT SUM(qty) FROM sale_details WHERE product_detail_id = pd.id), 0)) as current_stock,
                    COALESCE((SELECT SUM(qty) FROM sale_details WHERE product_detail_id = pd.id), 0) as total_sold_units,
                    COALESCE((SELECT SUM(qty * sale_price) FROM sale_details WHERE product_detail_id = pd.id), 0) as total_revenue', false)
            ->join('products p', 'p.id = pd.product_id')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->get()->getResultArray();

        $catModel = new CategoryModel();
        $data['categories'] = $catModel->findAll();
        
        return view('products/index', $data);
    }

    public function shortage_list()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        
        $data['shortage_items'] = $db->table('product_details pd')
            ->select('pd.*, p.name as name, c.name as category_name, 
                     (COALESCE((SELECT SUM(qty) FROM purchase_details WHERE product_detail_id = pd.id), 0) - 
                      COALESCE((SELECT SUM(qty) FROM sale_details WHERE product_detail_id = pd.id), 0)) as total_qty')
            ->join('products p', 'p.id = pd.product_id')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->having('total_qty <', 10)
            ->orHaving('total_qty IS NULL')
            ->get()->getResultArray();

        return view('products/shortage', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $catModel = new CategoryModel();
        $data['categories'] = $catModel->findAll();
        return view('products/add', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $productModel = new ProductModel();
        $detailModel = new \App\Models\ProductDetailModel();
        
        $name = $this->request->getPost('name');
        $categoryId = $this->request->getPost('category_id');
        
        // 1. Check if product already exists by name and category
        $product = $productModel->where('name', $name)->where('category_id', $categoryId)->first();
        
        if (!$product) {
            $productId = $productModel->insert([
                'name' => $name,
                'category_id' => $categoryId
            ]);
        } else {
            $productId = $product['id'];
        }
        
        // 2. Add product details (variant)
        $detailData = [
            'product_id' => $productId,
            'cost'       => $this->request->getPost('cost'),
            'unit'       => $this->request->getPost('unit'),
            'unit_value' => $this->request->getPost('unit_value'),
            'form_6'     => $this->request->getPost('form_6'),
            'form_7'     => $this->request->getPost('form_7'),
        ];

        if ($detailModel->insert($detailData)) {
            return redirect()->to(base_url('products'))->with('success', 'Product and details added successfully');
        }
        return redirect()->back()->with('error', 'Failed to add product');
    }

    public function view($detail_id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $db = \Config\Database::connect();
        
        // Product Detail & Master Data
        $product = $db->table('product_details pd')
                         ->select('pd.*, p.name as name, categories.name as category_name')
                         ->join('products p', 'p.id = pd.product_id')
                         ->join('categories', 'categories.id = p.category_id', 'left')
                         ->where('pd.id', $detail_id)
                         ->get()->getRowArray();
                         
        if (!$product) return redirect()->to(base_url('products'))->with('error', 'Product not found');

        // Stock Data (Batches) - Filter by product_detail_id
        $batches = $db->table('purchase_details pd')
                      ->select('pd.*, pr.date as purchase_date, pr.status as purchase_status, v.name as vendor_name, 
                                (pd.qty - COALESCE((SELECT SUM(qty) FROM sale_details sd WHERE sd.stock_id = pd.id), 0)) as remaining_qty')
                      ->join('purchases pr', 'pr.id = pd.purchase_id')
                      ->join('vendors v', 'v.id = pr.vendor_id', 'left')
                      ->where('pd.product_detail_id', $detail_id)
                      ->orderBy('pr.date', 'DESC')
                      ->get()->getResultArray();

        // Sales Data - Filter by product_detail_id
        $sales = $db->table('sale_details sd')
                    ->select('sd.*, s.sale_date, s.manual_dr_name as customer_name, s.manual_dr_phone as customer_phone, pd.batch_id, pd.cost, d.name as doctor_name, d.phone as doctor_phone')
                    ->join('sales s', 's.id = sd.sale_id')
                    ->join('purchase_details pd', 'pd.id = sd.stock_id', 'left')
                    ->join('doctors d', 'd.id = s.doctor_id', 'left')
                    ->where('sd.product_detail_id', $detail_id)
                    ->orderBy('s.sale_date', 'DESC')
                    ->get()->getResultArray();

        // Stats
        $totalPurchased = 0;
        $totalSold = 0;
        $totalRevenue = 0;
        $totalCostOfSold = 0;

        foreach ($batches as $b) {
            if (in_array($b['purchase_status'], ['received', 'partial_paid', 'paid'])) {
                $totalPurchased += $b['qty'];
            }
        }
        foreach ($sales as $s) {
            $totalSold += $s['qty'];
            $totalRevenue += ($s['qty'] * $s['sale_price']) - ($s['discount'] ?? 0);
            $totalCostOfSold += ($s['qty'] * $s['cost']);
        }

        $data = [
            'product' => $product,
            'batches' => $batches,
            'sales' => $sales,
            'stats' => [
                'current_stock' => $totalPurchased - $totalSold,
                'total_sold' => $totalSold,
                'revenue' => $totalRevenue,
                'profit' => $totalRevenue - $totalCostOfSold
            ]
        ];

        return view('products/view', $data);
    }

    public function edit($detail_id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $db = \Config\Database::connect();
        $product = $db->table('product_details pd')
                      ->select('pd.*, p.name as name, p.category_id')
                      ->join('products p', 'p.id = pd.product_id')
                      ->where('pd.id', $detail_id)
                      ->get()->getRowArray();
        
        $catModel = new CategoryModel();
        $data['categories'] = $catModel->findAll();
        $data['product'] = $product;
        
        if (!$data['product']) return redirect()->to(base_url('products'))->with('error', 'Product variant not found');
        
        return view('products/edit', $data);
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $productModel = new ProductModel();
        $detailModel = new \App\Models\ProductDetailModel();
        
        $detailId = $this->request->getPost('id'); // This is product_detail_id
        $detail = $detailModel->find($detailId);
        
        if(!$detail) return redirect()->back()->with('error', 'Product variant not found');
        
        $productId = $detail['product_id'];
        
        // 1. Update Master Product
        $productModel->update($productId, [
            'name'        => $this->request->getPost('name'),
            'category_id' => $this->request->getPost('category_id'),
        ]);

        // 2. Update Details
        $detailData = [
            'cost'        => $this->request->getPost('cost'),
            'unit'        => $this->request->getPost('unit'),
            'unit_value'  => $this->request->getPost('unit_value'),
            'form_6'      => $this->request->getPost('form_6'),
            'form_7'      => $this->request->getPost('form_7'),
        ];

        if ($detailModel->update($detailId, $detailData)) {
            return redirect()->to(base_url('products'))->with('success', 'Product updated successfully');
        }
        return redirect()->back()->with('error', 'Failed to update product');
    }

    public function delete($detail_id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $detailModel = new \App\Models\ProductDetailModel();
        
        // Before deleting, check if this variant has any sales or purchases
        $db = \Config\Database::connect();
        $hasHistory = $db->table('purchase_details')->where('product_detail_id', $detail_id)->countAllResults() > 0;
        $hasSales   = $db->table('sale_details')->where('product_detail_id', $detail_id)->countAllResults() > 0;
        
        if ($hasHistory || $hasSales) {
            return redirect()->to(base_url('products'))->with('error', 'Cannot delete product variant with transaction history.');
        }

        if ($detailModel->delete($detail_id)) {
            return redirect()->to(base_url('products'))->with('success', 'Product variant deleted successfully');
        }
        return redirect()->to(base_url('products'))->with('error', 'Failed to delete product variant');
    }
}
