<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class Sales extends BaseController
{
    // Inventory view - what is available for sale
    public function inventory()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new StockModel();
        // We select batches and calculate sold quantity for each
        $db = \Config\Database::connect();
        $builder = $db->table('stock_purchase s');
        $builder->select('s.*, p.name as product_name, p.unit as product_unit, p.unit_value as product_unit_value, v.name as vendor_name, 
                         (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = s.id) as total_sold');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->join('vendors v', 'v.id = s.vendor_id', 'left');
        $builder->orderBy('s.expiry_date', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Filter those where initial_qty - total_sold > 0
        $available = [];
        foreach ($results as $item) {
            $item['available_qty'] = $item['initial_qty'] - $item['total_sold'];
            if ($item['available_qty'] > 0) {
                // Update legacy qty in memory for current views (they expect 'qty')
                $item['qty'] = $item['available_qty'];
                $available[] = $item;
            }
        }

        $data['inventory'] = $available;
        return view('sales/inventory', $data);
    }

    // Page for making a sale
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $sModel = new StockModel();
        // Only show batches with stock available
        $db = \Config\Database::connect();
        $builder = $db->table('stock_purchase s');
        $builder->select('s.*, p.name as product_name, p.unit_value as product_unit_value, p.unit, v.name as vendor_name, 
                         (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = s.id) as total_sold');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->join('vendors v', 'v.id = s.vendor_id', 'left');
        $builder->orderBy('p.name', 'ASC');
        
        $results = $builder->get()->getResultArray();
        $available = [];
        foreach ($results as $item) {
            $left = $item['initial_qty'] - $item['total_sold'];
            if ($left > 0) {
                $item['available_qty'] = $left;
                $item['qty'] = $left; // Legacy support
                $available[] = $item;
            }
        }

        $dModel = new \App\Models\DoctorModel();
        $data['doctors'] = $dModel->orderBy('name', 'ASC')->findAll();
        $data['stocks'] = $available;
        return view('sales/index', $data);
    }

    // Process the actual sale transaction
    public function process()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $stockId   = $this->request->getPost('stock_id');
        $qtyToSell = $this->request->getPost('qty');
        $doctorId  = $this->request->getPost('doctor_id');

        $stockModel = new StockModel();
        $stock = $stockModel->find($stockId);

        if (!$stock) {
            return redirect()->back()->with('error', 'Batch not found.');
        }

        // Calculate actual available stock
        $saleModel = new SaleModel();
        $sold = $saleModel->where('stock_id', $stockId)->selectSum('qty')->get()->getRow()->qty ?? 0;
        $available = $stock['initial_qty'] - $sold;

        if ($available >= $qtyToSell) {
            $discount = $this->request->getPost('discount') ?: 0;
            $saleId = $saleModel->insert([
                'stock_id'       => $stockId,
                'product_id'     => $stock['product_id'],
                'doctor_id'      => !empty($doctorId) ? $doctorId : null,
                'qty'            => $qtyToSell,
                'sale_price'     => $stock['price'],
                'discount'       => $discount,
                'customer_name'  => $this->request->getPost('customer_name'),
                'customer_phone' => $this->request->getPost('customer_phone'),
                'sale_date'      => date('Y-m-d H:i:s')
            ]);

            return redirect()->to(base_url('sales'))->with('success', 'Sale processed successfully!')
                                                  ->with('last_sale_id', $saleId);
        }

        return redirect()->back()->with('error', 'Not enough stock available.');
    }

    public function history()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $saleModel = new SaleModel();
        $data['sales'] = $saleModel->select('sales.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, stock_purchase.batch_id, doctors.name as doctor_name')
                                   ->join('products', 'products.id = sales.product_id')
                                   ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                                   ->join('doctors', 'doctors.id = sales.doctor_id', 'left')
                                   ->orderBy('sales.sale_date', 'DESC')
                                   ->findAll();

        return view('sales/history', $data);
    }

    public function report()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $start_date = $this->request->getGet('start_date') ?: date('Y-m-01');
        $end_date   = $this->request->getGet('end_date')   ?: date('Y-m-d');

        $db = \Config\Database::connect();
        $builder = $db->table('sales s');
        $builder->select('s.*, p.name as product_name, st.batch_id, st.cost as purchase_cost, d.name as doctor_name');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->join('stock_purchase st', 'st.id = s.stock_id');
        $builder->join('doctors d', 'd.id = s.doctor_id', 'left');
        $builder->where('DATE(s.sale_date) >=', $start_date);
        $builder->where('DATE(s.sale_date) <=', $end_date);
        $builder->orderBy('s.sale_date', 'DESC');
        
        $data['sales']      = $builder->get()->getResultArray();
        $data['start_date'] = $start_date;
        $data['end_date']   = $end_date;

        return view('sales/report', $data);
    }

    public function invoice($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $saleModel = new SaleModel();
        $data['sale'] = $saleModel->select('sales.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, stock_purchase.batch_id, doctors.name as doctor_name')
                                 ->join('products', 'products.id = sales.product_id')
                                 ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                                 ->join('doctors', 'doctors.id = sales.doctor_id', 'left')
                                 ->find($id);
        return view('sales/invoice', $data);
    }

    public function void($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $saleModel = new SaleModel();
        $saleModel->delete($id);
        // We don't need to add back to stock as it's calculated dynamically now
        return redirect()->back()->with('success', 'Sale voided successfully.');
    }
    public function export()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $saleModel = new SaleModel();
        
        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $query = $saleModel->select('sales.*, products.name as product_name, stock_purchase.batch_id, stock_purchase.cost as cost_price, vendors.name as vendor_name')
                          ->join('products', 'products.id = sales.product_id')
                          ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                          ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left');

        if ($start_date && $end_date) {
            $query->where('DATE(sale_date) >=', $start_date)
                  ->where('DATE(sale_date) <=', $end_date);
        }

        $sales = $query->orderBy('sale_date', 'DESC')->findAll();

        $filename = 'sales_report_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");

        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'Date', 'Product', 'Customer/Doctor', 'Qty', 'Unit Price', 'Cost Price', 'Discount', 'Total Net Sale', 'Profit', 'Batch', 'Vendor']);

        foreach ($sales as $s) {
            $subtotal = $s['qty'] * $s['sale_price'];
            $totalSale = $subtotal - ($s['discount'] ?? 0);
            $totalCost = $s['qty'] * $s['cost_price'];
            $profit = $totalSale - $totalCost;
            
            fputcsv($file, [
                $s['id'],
                $s['sale_date'],
                $s['product_name'],
                $s['doctor_name'] ?: ($s['customer_name'] ?: 'Cash Customer'),
                $s['qty'],
                $s['sale_price'],
                $s['cost_price'],
                $s['discount'],
                $totalSale,
                $profit,
                $s['batch_id'],
                $s['vendor_name'] ?: 'Local'
            ]);
        }
        fclose($file);
        exit;
    }
}
