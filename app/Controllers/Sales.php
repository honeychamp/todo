<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseDetailModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class Sales extends BaseController
{
    // Inventory view - what is available for sale
    public function inventory()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        
        // We select purchase_details and calculate sold quantity for each
        $builder = $db->table('purchase_details s');
        $builder->select('s.*, s.exp_date as expiry_date, s.mfg_date as manufacture_date, s.qty as initial_qty, 
                         p.name as product_name, pdt.unit as product_unit, pdt.unit_value as product_unit_value, v.name as vendor_name, 
                         (SELECT COALESCE(SUM(qty), 0) FROM sale_details WHERE sale_details.stock_id = s.id) as total_sold');
        $builder->join('products p', 'p.id = s.product_id')
        ->join('product_details pdt', 'pdt.id = s.product_detail_id')
        ->join('purchases pr', 'pr.id = s.purchase_id');
        $builder->whereIn('pr.status', ['received', 'partial_paid', 'paid']);
        $builder->join('vendors v', 'v.id = pr.vendor_id', 'left');
        $builder->orderBy('s.exp_date', 'ASC');
        
        $results = $builder->get()->getResultArray();
        
        // Filter those where qty - total_sold > 0
        $available = [];
        foreach ($results as $item) {
            $item['available_qty'] = $item['initial_qty'] - $item['total_sold'];
            if ($item['available_qty'] > 0) {
                // Update legacy qty in memory for current views (they expect 'qty' for shelf current qty)
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

        $db = \Config\Database::connect();
        $builder = $db->table('purchase_details s');
        $builder->select('s.*, s.exp_date as expiry_date, s.mfg_date as manufacture_date, s.qty as initial_qty, 
                         p.name as product_name, pdt.unit_value as product_unit_value, pdt.unit, v.name as vendor_name, 
                         (SELECT COALESCE(SUM(qty), 0) FROM sale_details WHERE sale_details.stock_id = s.id) as total_sold');
        $builder->join('products p', 'p.id = s.product_id')
        ->join('product_details pdt', 'pdt.id = s.product_detail_id')
        ->join('purchases pr', 'pr.id = s.purchase_id');
        $builder->whereIn('pr.status', ['received', 'partial_paid', 'paid']);
        $builder->join('vendors v', 'v.id = pr.vendor_id', 'left');
        $builder->orderBy('p.name', 'ASC');
        
        $results = $builder->get()->getResultArray();
        $available = [];
        foreach ($results as $item) {
            $left = $item['initial_qty'] - $item['total_sold'];
            if ($left > 0) {
                $item['available_qty'] = $left;
                $item['qty'] = $left; // shelf qty
                $available[] = $item;
            }
        }

        $dModel = new \App\Models\DoctorModel();
        
        // Fetch all products for the in-cart manual search/select
        $db = \Config\Database::connect();
        $allProducts = $db->table('products p')
            ->select('p.id as product_id, p.name as product_name, pdt.id as product_detail_id, pdt.unit, pdt.unit_value, 
                      (SELECT price FROM purchase_details pd WHERE pd.product_id = p.id ORDER BY pd.id DESC LIMIT 1) as last_price,
                      (SELECT id FROM purchase_details pd WHERE pd.product_id = p.id ORDER BY pd.id DESC LIMIT 1) as stock_id')
            ->join('product_details pdt', 'pdt.product_id = p.id', 'left')
            ->get()->getResultArray();

        $data['doctors']      = $dModel->orderBy('name', 'ASC')->findAll();
        $data['stocks']       = $available;
        $data['all_products'] = $allProducts;
        
        return view('sales/index', $data);
    }

    // Process the actual sale transaction
    public function process()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $stockIds   = $this->request->getPost('stock_id');
        $qtys       = $this->request->getPost('qty');
        $discounts  = $this->request->getPost('discount');
        $prices     = $this->request->getPost('price');
        $strengths  = $this->request->getPost('strength'); // Custom strengths from POS
        $doctorId   = $this->request->getPost('doctor_id');

        if (empty($stockIds)) return redirect()->back()->with('error', 'No items selected.');

        $db = \Config\Database::connect();
        $db->transStart();

        $saleModel = new SaleModel();
        $detailModel = new \App\Models\SaleDetailModel();
        $PurchaseDetailModel = new PurchaseDetailModel();
        $doctorModel = new \App\Models\DoctorModel();

        $manualName  = trim($this->request->getPost('manual_dr_name'));
        $manualPhone = trim($this->request->getPost('manual_dr_phone'));

        if (empty($doctorId) && !empty($manualName)) {
            // Check if doctor exists by phone (if phone provided) or name
            if (!empty($manualPhone)) {
                $existingDoctor = $doctorModel->where('phone', $manualPhone)->first();
            } else {
                $existingDoctor = $doctorModel->where('name', $manualName)->first();
            }

            if ($existingDoctor) {
                $doctorId = $existingDoctor['id'];
                $manualName = '';
                $manualPhone = '';
            } else {
                $doctorId = $doctorModel->insert([
                    'name'  => $manualName,
                    'phone' => $manualPhone,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                $manualName = '';
                $manualPhone = '';
            }
        }

        // Create Sale Header
        $saleData = [
            'invoice_no'       => 'INV-' . strtoupper(substr(uniqid(), -6)),
            'doctor_id'        => $doctorId ?: null,
            'manual_dr_name'   => $manualName ?: null,
            'manual_dr_phone'  => $manualPhone ?: null,
            'sale_date'        => date('Y-m-d H:i:s'),
            'total_amount'     => 0,
            'total_discount'   => 0
        ];
        $saleId = $saleModel->insert($saleData);

        $totalGross = 0;
        $totalDisc = 0;

        foreach ($stockIds as $idx => $sid) {
            $qty = (int)$qtys[$idx];
            $disc = (float)($discounts[$idx] ?? 0);
            $price = (float)($prices[$idx] ?? 0);
            $strength = $strengths[$idx] ?? ''; 
            
            if ($qty <= 0) continue;

            $stock = $PurchaseDetailModel->find($sid);

            // Robust check for IDs (Manual or Stocked)
            $pid = 0;
            $pdid = 0;
            
            if ($stock) {
                $pid = $stock['product_id'];
                $pdid = $stock['product_detail_id'];
            }

            $detailModel->insert([
                'sale_id'           => $saleId,
                'stock_id'          => $sid,
                'product_id'        => $pid,
                'product_detail_id' => $pdid,
                'strength'          => $strength,
                'qty'               => $qty,
                'sale_price'        => $price,
                'discount'          => $disc
            ]);

            // Gross = Qty * Price
            $totalGross += ($qty * $price);
            $totalDisc += $disc;
        }

        // Net = Gross - Discount
        $totalNet = $totalGross - $totalDisc;
        
        // Final sanity check: Total amount cannot be negative
        if ($totalNet < 0) $totalNet = 0;

        // Update Header with exact totals
        $saleModel->update($saleId, [
            'gross_amount'   => $totalGross,
            'total_amount'   => $totalNet,
            'total_discount' => $totalDisc
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Sale failed due to a database error.');
        } else {
            $db->transCommit();
            session()->setFlashdata('last_sale_id', $saleId);
            session()->setFlashdata('last_sale_invoice', $saleData['invoice_no']);
            return redirect()->to(base_url('sales'))->with('success', 'Sale completed successfully!');
        }
    }

    public function history()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $saleModel = new SaleModel();
        $data['sales'] = $saleModel->select('sales.*, sales.manual_dr_name as customer_name, sales.manual_dr_phone as customer_phone, doctors.name as doctor_name, doctors.phone as doctor_phone')
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
        $builder = $db->table('sale_details sd');
        $builder->select('sd.*, s.sale_date, s.manual_dr_name as customer_name, s.manual_dr_phone as customer_phone, s.invoice_no, p.name as product_name, pdt.unit, pdt.unit_value, st.batch_id, st.cost as purchase_cost, d.name as doctor_name, d.phone as doctor_phone');
        $builder->join('sales s', 's.id = sd.sale_id')
        ->join('products p', 'p.id = sd.product_id')
        ->join('product_details pdt', 'pdt.id = sd.product_detail_id')
        ->join('purchase_details st', 'st.id = sd.stock_id')
        ->join('doctors d', 'd.id = s.doctor_id', 'left');
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
        $data['sale'] = $saleModel->select('sales.*, sales.manual_dr_name as customer_name, sales.manual_dr_phone as customer_phone, doctors.name as doctor_name, doctors.phone as doctor_phone')
                                 ->join('doctors', 'doctors.id = sales.doctor_id', 'left')
                                 ->find($id);
        
        $db = \Config\Database::connect();
        $data['items'] = $db->table('sale_details sd')
                           ->select('sd.*, p.name as product_name, pdt.unit, pdt.unit_value, pd.batch_id')
                           ->join('products p', 'p.id = sd.product_id')
                           ->join('product_details pdt', 'pdt.id = sd.product_detail_id')
                           ->join('purchase_details pd', 'pd.id = sd.stock_id')
                           ->where('sd.sale_id', $id)
                           ->get()->getResultArray();

        // Professional Ledger Summary for Doctor
        if (!empty($data['sale']['doctor_id'])) {
            $docId = $data['sale']['doctor_id'];
            
            // Getting total purchased from ALL sales up to this one (we can just use all sales since ledger covers all)
            $totalPurchased = clone $db;
            $docPurchased = $totalPurchased->table('sales')->where('doctor_id', $docId)->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;
            
            $totalPaid = clone $db;
            $docPaid = $totalPaid->table('doctor_payments')->where('doctor_id', $docId)->selectSum('amount')->get()->getRow()->amount ?? 0;
            
            $currentBill = $data['sale']['total_amount'];
            $totalOutstanding = $docPurchased - $docPaid;
            $previousBalance = $totalOutstanding - $currentBill; // Because current bill is included in totalPurchased
            
            $data['previous_balance'] = $previousBalance;
            $data['current_bill'] = $currentBill;
            $data['doctor_balance'] = $totalOutstanding;
        } else {
            $data['previous_balance'] = 0;
            $data['current_bill'] = $data['sale']['total_amount'] ?? 0;
            $data['doctor_balance'] = 0;
        }

        return view('sales/invoice', $data);
    }

    public function void($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $db = \Config\Database::connect();
        $db->transStart();
        
        // Delete details first
        $db->table('sale_details')->where('sale_id', $id)->delete();
        
        // Delete master
        $db->table('sales')->where('id', $id)->delete();
        
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Failed to cancel sale.');
        }

        return redirect()->back()->with('success', 'Sale voided successfully.');
    }

    public function export()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $builder = $db->table('sale_details sd');
        $builder->select('sd.*, s.sale_date, s.manual_dr_name as customer_name, s.manual_dr_phone as customer_phone, p.name as product_name, pdt.unit, pdt.unit_value, pd.batch_id, pd.cost as cost_price, vs.name as vendor_name, d.name as doctor_name')
                  ->join('sales s', 's.id = sd.sale_id')
                  ->join('products p', 'p.id = sd.product_id')
                  ->join('product_details pdt', 'pdt.id = sd.product_detail_id')
                  ->join('purchase_details pd', 'pd.id = sd.stock_id', 'left')
                  ->join('purchases ps', 'ps.id = pd.purchase_id', 'left')
                  ->join('vendors vs', 'vs.id = ps.vendor_id', 'left')
                  ->join('doctors d', 'd.id = s.doctor_id', 'left');

        if ($start_date && $end_date) {
            $builder->where('DATE(s.sale_date) >=', $start_date)
                    ->where('DATE(s.sale_date) <=', $end_date);
        }

        $sales = $builder->orderBy('s.sale_date', 'DESC')->get()->getResultArray();

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
                $s['doctor_name'] ?: ($s['customer_name'] ?: 'Unregistered Doctor'),
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
