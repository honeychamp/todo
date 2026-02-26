<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockModel;
use App\Models\ProductModel;
use App\Models\VendorModel;
use App\Models\VendorPaymentModel;

class Purchases extends BaseController
{
    // View all stock purchases (The Purchase Log)
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vendor_filter = $this->request->getGet('vendor_id');

        $model = new StockModel();
        // Join with products and vendors, and also join with sales to get sold qty per batch
        $db = \Config\Database::connect();
        $builder = $db->table('stock_purchase s');
        $builder->select('s.*, p.name as product_name, p.unit as product_unit, p.unit_value as product_unit_value, v.name as vendor_name, 
                         (SELECT SUM(qty) FROM sales WHERE stock_id = s.id) as sold_qty,
                         (SELECT SUM(qty * sale_price) FROM sales WHERE stock_id = s.id) as batch_revenue');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->join('vendors v', 'v.id = s.vendor_id', 'left');
        
        if ($vendor_filter) {
            $builder->where('s.vendor_id', $vendor_filter);
        }

        $builder->orderBy('s.created_at', 'DESC');
        
        $data['purchases'] = $builder->get()->getResultArray();
        $data['selected_vendor'] = $vendor_filter;


        $vModel = new VendorModel();
        $pModel = new ProductModel();
        $data['vendors']  = $vModel->findAll();
        $data['products'] = $pModel->findAll();

        return view('purchases/index', $data);
    }

    // Step 1: Show vendor selection page before adding stock
    public function select_vendor()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vModel = new VendorModel();
        $data['vendors'] = $vModel->findAll();
        return view('purchases/select_vendor', $data);
    }

    // Page for adding new stock for a specific vendor
    public function add($vendor_id = null)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $pModel = new ProductModel();
        $vModel = new VendorModel();
        
        $data['products'] = $pModel->findAll();
        $data['vendor']   = $vendor_id ? $vModel->find($vendor_id) : null;
        $data['vendors']  = $vModel->findAll();

        return view('purchases/add', $data);
    }

    // Process adding the new stock
    public function process_add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();

        $batchIds   = $this->request->getPost('batch_id');
        $vendorIds  = $this->request->getPost('vendor_id');
        $productIds = $this->request->getPost('product_id');
        $mfgDates   = $this->request->getPost('manufacture_date');
        $expDates   = $this->request->getPost('expiry_date');
        $qtys       = $this->request->getPost('qty');
        $costs      = $this->request->getPost('cost');
        $prices     = $this->request->getPost('price');

        if (is_array($batchIds)) {
            $rows = [];
            foreach ($batchIds as $i => $batch_id) {
                if (empty($productIds[$i])) continue;
                $rows[] = [
                    'batch_id'         => $batch_id,
                    'vendor_id'        => $vendorIds[$i] ?: null,
                    'product_id'       => $productIds[$i],
                    'initial_qty'      => $qtys[$i],
                    'manufacture_date' => $mfgDates[$i],
                    'expiry_date'      => $expDates[$i],
                    'qty'              => $qtys[$i], // Legacy qty, we will stop decrementing it
                    'cost'             => $costs[$i],
                    'price'            => $prices[$i],
                ];
            }
            if (!empty($rows)) {
                $model->insertBatch($rows);
            }
        } else {
            $model->insert([
                'batch_id'         => $batchIds,
                'vendor_id'        => $vendorIds ?: null,
                'product_id'       => $productIds,
                'initial_qty'      => $qtys,
                'manufacture_date' => $mfgDates,
                'expiry_date'      => $expDates,
                'qty'              => $qtys,
                'cost'             => $costs,
                'price'            => $prices,
            ]);
        }

        return redirect()->to(base_url('purchases'))->with('success', 'Purchase recorded successfully!');
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new StockModel();
        $model->delete($id);
        return redirect()->to(base_url('purchases'))->with('success', 'Record deleted.');
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $id = $this->request->getPost('id');
        
        $newInitialQty = $this->request->getPost('initial_qty');

        $data = [
            'batch_id'         => $this->request->getPost('batch_id'),
            'vendor_id'        => $this->request->getPost('vendor_id') ?: null,
            'product_id'       => $this->request->getPost('product_id'),
            'manufacture_date' => $this->request->getPost('manufacture_date'),
            'expiry_date'      => $this->request->getPost('expiry_date'),
            'initial_qty'      => $newInitialQty,
            'qty'              => $newInitialQty, // keep legacy qty in sync
            'cost'             => $this->request->getPost('cost'),
            'price'            => $this->request->getPost('price'),
        ];

        $model->update($id, $data);
        return redirect()->to(base_url('purchases'))->with('success', 'Purchase record updated.');
    }

    // Vendor Dues listing
    public function dues()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $builder = $db->table('vendors v');
        $builder->select('v.*, 
                         (SELECT SUM(cost * initial_qty) FROM stock_purchase WHERE vendor_id = v.id) as total_purchase_value,
                         (SELECT SUM(amount) FROM vendor_payments WHERE vendor_id = v.id) as total_paid');
        
        $data['vendors'] = $builder->get()->getResultArray();
        return view('purchases/dues', $data);
    }

    public function add_payment()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $paymentModel = new VendorPaymentModel();
        $paymentModel->insert([
            'vendor_id'    => $this->request->getPost('vendor_id'),
            'amount'       => $this->request->getPost('amount'),
            'payment_date' => $this->request->getPost('payment_date'),
            'notes'        => $this->request->getPost('notes'),
        ]);

        return redirect()->back()->with('success', 'Payment recorded.');
    }

    public function invoice($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $model = new StockModel();
        $data['purchase'] = $model->select('stock_purchase.*, products.name as product_name, vendors.name as vendor_name, vendors.phone as vendor_phone, vendors.address as vendor_address')
                                  ->join('products', 'products.id = stock_purchase.product_id')
                                  ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left')
                                  ->find($id);
        return view('purchases/invoice', $data);
    }
    public function vendor_history($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vModel = new VendorModel();
        $vendor = $vModel->find($id);
        if (!$vendor) return redirect()->to(base_url('vendors'))->with('error', 'Vendor not found.');

        $db = \Config\Database::connect();
        
        // 1. Get all purchases for this vendor
        $builder = $db->table('stock_purchase s');
        $builder->select('s.*, p.name as product_name, p.unit, p.unit_value, 
                         (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE stock_id = s.id) as sold_qty');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->where('s.vendor_id', $id);
        $builder->orderBy('s.created_at', 'ASC'); // ASC for ledger calculation
        $purchases = $builder->get()->getResultArray();

        // 2. Get all payments for this vendor
        $pBuilder = $db->table('vendor_payments');
        $pBuilder->where('vendor_id', $id);
        $pBuilder->orderBy('payment_date', 'ASC');
        $payments = $pBuilder->get()->getResultArray();

        // 3. Prepare Ledger: Combine and Sort
        $ledger = [];
        $total_purchased = 0;
        $total_items = 0;
        foreach($purchases as $p) {
            $amount = $p['initial_qty'] * $p['cost'];
            $total_purchased += $amount;
            $total_items += $p['initial_qty'];
            $ledger[] = [
                'date' => $p['created_at'],
                'type' => 'PURCHASE',
                'description' => "Purchased " . $p['product_name'] . " (" . $p['batch_id'] . ")",
                'debit' => $amount, // Amount we owe
                'credit' => 0,
                'ref' => $p['id']
            ];
        }

        $total_paid = 0;
        foreach($payments as $pmt) {
            $total_paid += $pmt['amount'];
            $ledger[] = [
                'date' => $pmt['payment_date'],
                'type' => 'PAYMENT',
                'description' => "Payment Dispatched: " . ($pmt['notes'] ?? 'No notes'),
                'debit' => 0,
                'credit' => $pmt['amount'], // Amount we paid
                'ref' => $pmt['id']
            ];
        }

        // Sort ledger by date
        usort($ledger, function($a, $b) {
            $dateA = strtotime($a['date'] ?? '1970-01-01');
            $dateB = strtotime($b['date'] ?? '1970-01-01');
            return $dateA - $dateB;
        });

        // Add running balance
        $running = 0;
        foreach($ledger as &$entry) {
            $running += ($entry['debit'] - $entry['credit']);
            $entry['balance'] = $running;
        }

        // 4. Get currently active inventory from this vendor
        $builder = $db->table('stock_purchase s');
        $builder->select('p.name as product_name, p.unit, p.unit_value, s.batch_id, s.expiry_date,
                         (s.initial_qty - COALESCE((SELECT SUM(qty) FROM sales WHERE stock_id = s.id), 0)) as on_shelf');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->where('s.vendor_id', $id);
        $builder->having('on_shelf >', 0);
        $data['active_inventory'] = $builder->get()->getResultArray();

        // 5. Get top supplied products
        $builder = $db->table('stock_purchase s');
        $builder->select('p.name as product_name, SUM(s.initial_qty) as total_units, SUM(s.initial_qty * s.cost) as total_value');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->where('s.vendor_id', $id);
        $builder->groupBy('s.product_id');
        $builder->orderBy('total_units', 'DESC');
        $builder->limit(5);
        $data['top_products'] = $builder->get()->getResultArray();

        // 6. Monthly Supply Trend (Last 6 Months)
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        $builder = $db->table('stock_purchase');
        $builder->select("DATE_FORMAT(created_at, '%b %Y') as month, SUM(initial_qty * cost) as total");
        $builder->where('vendor_id', $id);
        $builder->where('created_at >=', $sixMonthsAgo);
        $builder->groupBy('month');
        $builder->orderBy('created_at', 'ASC');
        $data['supply_trend'] = $builder->get()->getResultArray();

        $data['ledger'] = array_reverse($ledger); // Latest on top for view
        $data['vendor'] = $vendor;
        $data['summary'] = [
            'total_purchased' => $total_purchased,
            'total_paid'      => $total_paid,
            'balance'         => $total_purchased - $total_paid,
            'items_count'     => $total_items
        ];

        return view('purchases/vendor_history', $data);
    }

    public function delete_payment($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $paymentModel = new VendorPaymentModel();
        $payment = $paymentModel->find($id);
        $vendor_id = $payment['vendor_id'] ?? null;
        $paymentModel->delete($id);

        return redirect()->to(base_url('purchases/vendor/' . $vendor_id))->with('success', 'Payment removed.');
    }
}
