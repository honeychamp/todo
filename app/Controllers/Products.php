<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Products extends BaseController
{
    /**
     * Professional Integrated Dashboard
     * Merges Business Intelligence, Stock Monitoring, and Sales Performance into ONE view.
     */
    public function dashboard()
    {
        // 1. Auth Guard
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        
        // 2. LOAD COMPREHENSIVE DATA
        $data = [];

        // --- SECTION A: CORE BUSINESS AUDIT ---
        // Basic Counts
        $data['total_products']   = $db->table('product_details')->countAllResults();
        $data['total_categories'] = $db->table('categories')->countAllResults();
        $data['total_vendors']    = $db->table('vendors')->countAllResults();
        $data['total_doctors']    = $db->table('doctors')->countAllResults();

        // Stock Valuations
        $stockRow = $db->query("SELECT 
            (SELECT COALESCE(SUM(qty), 0) FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE pr.status IN ('received','partial_paid','paid')) - (SELECT COALESCE(SUM(qty), 0) FROM sale_details) as total_qty, 
            (SELECT SUM((pd.qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = pd.id), 0)) * pd.cost) FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE pr.status IN ('received','partial_paid','paid')) as stock_value, 
            (SELECT COALESCE(SUM(qty * cost), 0) FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE pr.status IN ('received','partial_paid','paid')) as global_investment 
        ")->getRow();

        $data['total_items_in_stock'] = (int)($stockRow->total_qty ?? 0);
        $data['total_stock_value']    = (float)($stockRow->stock_value ?? 0);
        $data['global_investment']    = (float)($stockRow->global_investment ?? 0);

        // Doctor Receivables
        $data['total_doctor_receivables'] = $db->query("SELECT (SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE doctor_id IS NOT NULL) - (SELECT COALESCE(SUM(amount), 0) FROM doctor_payments) as net_receivable")->getRow()->net_receivable ?? 0;

        // Global Profit Calculation (Revenue - Cost of Goods Sold - Expenses)
        $globalSales = $db->query("SELECT 
            COALESCE(SUM((sd.qty * sd.sale_price) - sd.discount), 0) as revenue,
            COALESCE(SUM(sd.qty * pd.cost), 0) as cogs
            FROM sale_details sd 
            JOIN purchase_details pd ON pd.id = sd.stock_id")->getRow();
        
        $globalExpenses = $db->query("SELECT SUM(amount) as total FROM expenses")->getRow()->total ?? 0;
        
        $data['lifetime_sales'] = (float)$globalSales->revenue;
        $data['lifetime_net_profit'] = (float)($globalSales->revenue - $globalSales->cogs - $globalExpenses);

        // --- SECTION B: PERIOD PERFORMANCE (Today, Week, Month, Year) ---
        $periods = [
            'today' => ['start' => $today, 'end' => $today],
            'week'  => ['start' => date('Y-m-d', strtotime('monday this week')), 'end' => date('Y-m-d', strtotime('sunday this week'))],
            'month' => ['start' => date('Y-m-01'), 'end' => date('Y-m-t')],
            'year'  => ['start' => date('Y-01-01'), 'end' => date('Y-12-31')]
        ];
        
        $stats = [];
        foreach ($periods as $key => $p) {
            $stats[$key] = $this->getDetailedMetrics($db, $p['start'], $p['end']);
        }
        $data['stats'] = $stats;
        
        // --- SECTION C: CHARTS & TRENDS ---
        // 7-Day Revenue Chart
        $data['chart_labels'] = [];
        $data['chart_values'] = [];
        for($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $data['chart_labels'][] = date('D', strtotime($d));
            $data['chart_values'][] = $db->table('sale_details sd')
                                        ->join('sales s', 's.id = sd.sale_id')
                                        ->select('SUM((sd.qty * sd.sale_price) - sd.discount) as total')
                                        ->where('DATE(s.sale_date)', $d)
                                        ->get()->getRow()->total ?? 0;
        }
        $data['chart_labels'] = json_encode($data['chart_labels']);
        $data['chart_values'] = json_encode($data['chart_values']);

        // 12-Month Profitability Trend
        $monthsTrend = ['labels' => [], 'revenue' => [], 'profit' => []];
        for($i = 11; $i >= 0; $i--) {
            $ms = date('Y-m-01', strtotime("-$i months"));
            $me = date('Y-m-t', strtotime("-$i months"));
            $mm = $this->getDetailedMetrics($db, $ms, $me);
            $monthsTrend['labels'][]  = date('M Y', strtotime($ms));
            $monthsTrend['revenue'][] = $mm['revenue'];
            $monthsTrend['profit'][]  = $mm['net_profit'];
        }
        $data['months_trend'] = $monthsTrend;

        // --- SECTION D: INVENTORY ALERTS ---
        // Expiring Soon (90 Days)
        $data['expiring_soon'] = $db->query("SELECT pd.id, pd.batch_id, pd.exp_date, p.name as product_name, 
            (pd.qty - COALESCE((SELECT SUM(qty) FROM sale_details sd WHERE sd.stock_id = pd.id), 0)) as current_qty
            FROM purchase_details pd JOIN products p ON p.id = pd.product_id 
            WHERE pd.exp_date <= ? AND pd.exp_date >= ?
            HAVING current_qty > 0 ORDER BY pd.exp_date ASC LIMIT 5", [date('Y-m-d', strtotime('+90 days')), $today])->getResultArray();

        // Low Stock (Under 10 units)
        $data['low_stock'] = $db->query("SELECT pd.id, pd.batch_id, p.name as product_name, 
            (pd.qty - COALESCE((SELECT SUM(qty) FROM sale_details sd WHERE sd.stock_id = pd.id), 0)) as current_qty
            FROM purchase_details pd JOIN products p ON p.id = pd.product_id 
            HAVING current_qty < 10 AND current_qty > 0 ORDER BY current_qty ASC LIMIT 5")->getResultArray();

        // --- SECTION E: RANKINGS & LISTS ---
        // Top 5 Products (Last 30 Days)
        $data['top_products'] = $db->query("SELECT p.name, SUM(sd.qty) as total_units, SUM(sd.qty * sd.sale_price - sd.discount) as total_revenue
            FROM sale_details sd JOIN sales s ON s.id = sd.sale_id JOIN products p ON p.id = sd.product_id
            WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY sd.product_id ORDER BY total_units DESC LIMIT 5")->getResultArray();

        // Vendor Spending
        $data['vendor_spending'] = $db->query("SELECT v.name, SUM(p.total_amount) as total 
            FROM purchases p JOIN vendors v ON v.id = p.vendor_id
            GROUP BY p.vendor_id ORDER BY total DESC LIMIT 5")->getResultArray();

        // Recent Sales
        $data['recent_sales'] = $db->table('sales s')
            ->select('s.*, d.name as doctor_name')
            ->join('doctors d', 'd.id = s.doctor_id', 'left')
            ->orderBy('s.id', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        // --- SECTION F: STOCK FLOW AUDIT ---
        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');

        // Opening Qty = (Purchases before this month) - (Sales before this month)
        $opQty = $db->query("SELECT (
            SELECT COALESCE(SUM(pd.qty), 0) FROM purchase_details pd 
            JOIN purchases pr ON pr.id = pd.purchase_id 
            WHERE DATE(pr.date) < ? AND pr.status IN('received','partial_paid','paid')
        ) - (
            SELECT COALESCE(SUM(sd.qty), 0) FROM sale_details sd 
            JOIN sales s ON s.id = sd.sale_id 
            WHERE DATE(s.sale_date) < ?
        ) as opening", [$monthStart, $monthStart])->getRow()->opening ?? 0;

        // In Qty = Purchases this month
        $inQty = $db->query("SELECT COALESCE(SUM(pd.qty), 0) as total 
            FROM purchase_details pd 
            JOIN purchases pr ON pr.id = pd.purchase_id 
            WHERE (DATE(pr.date) BETWEEN ? AND ?) AND pr.status IN('received','partial_paid','paid')", [$monthStart, $monthEnd])->getRow()->total ?? 0;

        // Out Qty = Sales this month
        $outQty = $db->query("SELECT COALESCE(SUM(sd.qty), 0) as total 
            FROM sale_details sd 
            JOIN sales s ON s.id = sd.sale_id 
            WHERE (DATE(s.sale_date) BETWEEN ? AND ?)", [$monthStart, $monthEnd])->getRow()->total ?? 0;

        $data['audit'] = [
            'opening' => $opQty,
            'in'      => $inQty,
            'out'     => $outQty,
            'closing' => ($opQty + $inQty - $outQty)
        ];

        // All time top sellers
        $data['top_selling_products'] = $db->query("SELECT p.name, SUM(sd.qty) as units, SUM(sd.qty * sd.sale_price - sd.discount) as revenue
            FROM sale_details sd JOIN products p ON p.id = sd.product_id
            GROUP BY sd.product_id ORDER BY units DESC LIMIT 10")->getResultArray();

        // Legacy values for view compatibility (if any)
        
        return view('products/dashboard', $data);
    }

    /**
     * Easy helper for fetching financial metrics
     */
    private function getDetailedMetrics($db, $start, $end)
    {
        // Purchases (Inflow cost)
        $purchases = $db->query("SELECT SUM(pd.qty * pd.cost) as total 
            FROM purchase_details pd 
            JOIN purchases pr ON pr.id = pd.purchase_id 
            WHERE (DATE(pr.date) BETWEEN ? AND ?) AND pr.status IN ('received','partial_paid','paid')", [$start, $end])->getRow()->total ?? 0;

        // Sales Metrics
        $salesRow = $db->query("SELECT 
            COALESCE(SUM((sd.qty * sd.sale_price) - sd.discount), 0) as revenue,
            COALESCE(SUM(((sd.sale_price * sd.qty) - sd.discount) - (sp.cost * sd.qty)), 0) as gross_profit,
            COUNT(DISTINCT s.id) as tx
            FROM sale_details sd 
            JOIN sales s ON s.id = sd.sale_id
            JOIN purchase_details sp ON sp.id = sd.stock_id
            WHERE DATE(s.sale_date) BETWEEN ? AND ?", [$start, $end])->getRow();

        // Expenses
        $expenses = $db->query("SELECT SUM(amount) as total FROM expenses WHERE expense_date BETWEEN ? AND ?", [$start, $end])->getRow()->total ?? 0;

        return [
            'purchases'    => (float)$purchases,
            'revenue'      => (float)$salesRow->revenue,
            'gross_profit' => (float)$salesRow->gross_profit,
            'expenses'     => (float)$expenses,
            'net_profit'   => (float)($salesRow->gross_profit - $expenses),
            'tx_count'     => (int)$salesRow->tx,
            'avg_order'    => $salesRow->tx > 0 ? (float)($salesRow->revenue / $salesRow->tx) : 0
        ];
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
        
        $rules = [
            'name'        => 'required',
            'category_id' => 'required',
            'cost'        => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Product name, category, and cost are required.');
        }

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
        
        $rules = [
            'name'        => 'required',
            'category_id' => 'required',
            'cost'        => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('error', 'Product name, category, and cost are required.');
        }

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
