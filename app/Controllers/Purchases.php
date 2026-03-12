<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchaseModel;
use App\Models\PurchaseDetailModel;
use App\Models\ProductModel;
use App\Models\VendorModel;
use App\Models\VendorPaymentModel;

class Purchases extends BaseController
{
    // ---------------------------------------------------------------
    // PURCHASE LOG (index — shows all purchase headers)
    // ---------------------------------------------------------------
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $builder = $db->table('purchases p');
        $builder->select('p.*, v.name as vendor_name');
        $builder->join('vendors v', 'v.id = p.vendor_id', 'left');

        $vendor_filter = $this->request->getGet('vendor_id');
        $search        = $this->request->getGet('search');
        $status_filter = $this->request->getGet('status');

        if ($vendor_filter) {
            $builder->where('p.vendor_id', $vendor_filter);
        }
        if ($status_filter) {
            $builder->where('p.status', $status_filter);
        }
        if ($search) {
            $builder->groupStart()
                    ->like('p.note', $search)
                    ->orWhere('p.id', $search)
                    ->groupEnd();
        }

        $builder->orderBy('p.date', 'DESC');
        $purchase_data = $builder->get()->getResultArray();
        
        // Accurate financial stats
        $db = \Config\Database::connect();
        $total_purchased = $db->table('purchases')->selectSum('total_amount')->get()->getRow()->total_amount ?? 0;
        $total_paid      = $db->table('vendor_payments')->selectSum('amount')->get()->getRow()->amount ?? 0;

        $data['purchases']       = $purchase_data;
        $data['selected_vendor'] = $vendor_filter;
        $data['selected_status'] = $status_filter;
        $data['search_query']    = $search;
        $data['global_stats']    = [
            'total_purchased' => $total_purchased,
            'total_paid'      => $total_paid,
            'outstanding'     => $total_purchased - $total_paid
        ];

        $vModel          = new VendorModel();
        $data['vendors'] = $vModel->findAll();

        return view('purchases/index', $data);
    }

    // ---------------------------------------------------------------
    // VENDOR SELECTION (step 1 of add flow)
    // ---------------------------------------------------------------
    public function select_vendor()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vModel          = new VendorModel();
        $data['vendors'] = $vModel->findAll();
        $data['product_id'] = $this->request->getGet('product_id');
        return view('purchases/select_vendor', $data);
    }

    // ---------------------------------------------------------------
    // ADD PURCHASE FORM (step 2)
    // ---------------------------------------------------------------
    public function add($vendor_id = null)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $vModel = new VendorModel();

        $data['vendor']   = $vModel->find($vendor_id);
        
        // Fetch product variants (details joined with product names)
        $data['products'] = $db->table('product_details pd')
            ->select('pd.id as detail_id, p.name as product_name, pd.unit, pd.unit_value, pd.cost')
            ->join('products p', 'p.id = pd.product_id')
            ->orderBy('p.name', 'ASC')
            ->get()->getResultArray();
            
        $data['preSelectId'] = $this->request->getGet('product_id'); // This will be the detail_id now

        if ($vendor_id && !$data['vendor']) {
            return redirect()->to(base_url('purchases/select_vendor'))->with('error', 'Vendor not found.');
        }

        return view('purchases/add', $data);
    }

    // ---------------------------------------------------------------
    // PROCESS ADD: saves to `purchases` + `purchase_details`
    // ---------------------------------------------------------------
    public function process_add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $purchaseModel = new PurchaseModel();
        $detailModel   = new PurchaseDetailModel();

        // --- Header fields ---
        $vendor_id    = $this->request->getPost('vendor_id');
        $note         = $this->request->getPost('note');
        $date         = $this->request->getPost('date') ?: date('Y-m-d');
        $status       = $this->request->getPost('status') ?: 'ordered';

        // --- Line item arrays ---
        $qtyArr      = $this->request->getPost('qty');
        $costArr     = $this->request->getPost('cost');
        $priceArr    = $this->request->getPost('price');
        $batchArr    = $this->request->getPost('batch_id');
        $mfgArr      = $this->request->getPost('mfg_date');
        $expArr      = $this->request->getPost('exp_date');
        $prodArr     = $this->request->getPost('product_id');

        if (empty($qtyArr) || empty($prodArr)) {
            return redirect()->back()->withInput()->with('error', 'Please add at least one item to the purchase.');
        }

        // Calculate total and validate individual items
        $total = 0;
        foreach ($qtyArr as $i => $q) {
            $cost   = (float)($costArr[$i] ?? 0);
            $qty    = (int)($q ?? 0);
            $total += ($qty * $cost);
            
            if ($qty <= 0) {
                return redirect()->back()->withInput()->with('error', 'Quantity must be greater than zero for all items.');
            }
            if (empty($prodArr[$i])) {
                return redirect()->back()->withInput()->with('error', 'Please select a valid product for all rows.');
            }
        }

        // 1. Insert purchase header
        $purchaseId = $purchaseModel->insert([
            'vendor_id'    => $vendor_id ?: null,
            'total_amount' => $total,
            'note'         => $note,
            'date'         => $date,
            'status'       => $status,
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        // 2. Prepare and Insert Details
        $details = [];
        $db = \Config\Database::connect();
        
        foreach ($prodArr as $i => $did) { // did is product_detail_id
            if (empty($did)) continue;
            
            // Get original product_id from detail
            $detailInfo = $db->table('product_details')->where('id', $did)->get()->getRow();
            
            $details[] = [
                'purchase_id'       => $purchaseId,
                'product_id'        => $detailInfo->product_id,
                'product_detail_id' => $did,
                'batch_id'          => $batchArr[$i] ?? '',
                'qty'               => $qtyArr[$i] ?? 0,
                'cost'              => $costArr[$i] ?? 0,
                'price'             => $priceArr[$i] ?? 0,
                'mfg_date'          => $mfgArr[$i] ?: null,
                'exp_date'          => $expArr[$i] ?: null,
            ];
        }

        if (!empty($details)) {
            $detailModel->insertBatch($details);
        }

        return redirect()->to(base_url('purchases'))->with('success', 'Purchase recorded successfully!');
    }

    // ---------------------------------------------------------------
    // VIEW a single purchase with its line items
    // ---------------------------------------------------------------
    public function view_purchase($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();

        // Header
        $purchase = $db->table('purchases p')
            ->select('p.*, v.name as vendor_name, v.phone as vendor_phone, v.address as vendor_address')
            ->join('vendors v', 'v.id = p.vendor_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        if (!$purchase) {
            return redirect()->to(base_url('purchases'))->with('error', 'Purchase not found.');
        }

        // Line items
        $items = $db->table('purchase_details pd')
            ->select('pd.*, p.name as product_name, pdt.unit, pdt.unit_value')
            ->join('products p', 'p.id = pd.product_id')
            ->join('product_details pdt', 'pdt.id = pd.product_detail_id')
            ->where('pd.purchase_id', $id)
            ->get()->getResultArray();

        // How much of this specific purchase has been paid?
        $amountPaid = $db->table('vendor_payments')
            ->selectSum('amount')
            ->where('purchase_id', $id)
            ->get()->getRow()->amount ?? 0;

        $purchase['paid_amount'] = $amountPaid;
        $purchase['due_amount']  = max(0, $purchase['total_amount'] - $amountPaid);

        $data['purchase'] = $purchase;
        $data['items']    = $items;
        
        $data['products'] = $db->table('product_details pd')
            ->select('pd.id as detail_id, p.name as product_name, pd.unit, pd.unit_value')
            ->join('products p', 'p.id = pd.product_id')
            ->orderBy('p.name', 'ASC')
            ->get()->getResultArray();

        return view('purchases/view', $data);
    }

    // ---------------------------------------------------------------
    // ADD A SINGLE ITEM to an existing purchase
    // ---------------------------------------------------------------
    public function add_item()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $detailModel = new PurchaseDetailModel();
        $purchase_id = $this->request->getPost('purchase_id');
        $qty         = $this->request->getPost('qty');
        $cost        = $this->request->getPost('cost');
        $prod_id     = $this->request->getPost('product_id');

        if (empty($prod_id) || $qty <= 0) {
            return redirect()->back()->with('error', 'Invalid product or quantity.');
        }

        $db = \Config\Database::connect();
        $detailInfo = $db->table('product_details')->where('id', $prod_id)->get()->getRow();
        if(!$detailInfo) return redirect()->back()->with('error', 'Product variation not found.');

        $data = [
            'purchase_id'       => $purchase_id,
            'product_id'        => $detailInfo->product_id,
            'product_detail_id' => $prod_id,
            'batch_id'          => $this->request->getPost('batch_id'),
            'qty'               => $qty,
            'cost'              => $cost,
            'price'             => $this->request->getPost('price'),
            'mfg_date'          => $this->request->getPost('mfg_date'),
            'exp_date'          => $this->request->getPost('exp_date'),
        ];

        $detailModel->insert($data);
        $this->recalcTotal($purchase_id);

        return redirect()->to(base_url('purchases/view/' . $purchase_id))->with('success', 'New item added successfully!');
    }

    // ---------------------------------------------------------------
    // EDIT PURCHASE HEADER (status, note, date)
    // ---------------------------------------------------------------
    public function edit($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $purchaseModel = new PurchaseModel();
        $vModel        = new VendorModel();

        $data['purchase'] = $purchaseModel->find($id);
        $data['vendors']  = $vModel->findAll();

        if (!$data['purchase']) {
            return redirect()->to(base_url('purchases'))->with('error', 'Purchase not found.');
        }

        return view('purchases/edit', $data);
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $purchaseModel = new PurchaseModel();
        $id = $this->request->getPost('id');

        // Build update data — never overwrite vendor_id with null
        $updateData = [
            'date'   => $this->request->getPost('date'),
            'status' => $this->request->getPost('status'),
            'note'   => $this->request->getPost('note'),
        ];

        // Only update vendor_id if it was explicitly submitted and is not empty
        $submittedVendorId = $this->request->getPost('vendor_id');
        if (!empty($submittedVendorId)) {
            $updateData['vendor_id'] = $submittedVendorId;
        }

        $purchaseModel->update($id, $updateData);
        $this->syncVendorPayment($id);

        return redirect()->to(base_url('purchases/view/' . $id))->with('success', 'Purchase updated successfully!');
    }

    // ---------------------------------------------------------------
    // DELETE PURCHASE
    // ---------------------------------------------------------------
    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $purchaseModel = new PurchaseModel();
        $detailModel   = new PurchaseDetailModel();

        // 1. Delete details
        $detailModel->where('purchase_id', $id)->delete();
        
        // 2. Delete header
        $purchaseModel->delete($id);

        return redirect()->to(base_url('purchases'))->with('success', 'Purchase deleted successfully!');
    }

    // ---------------------------------------------------------------
    // DELETE a single line item
    // ---------------------------------------------------------------
    public function delete_item($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $detailModel = new PurchaseDetailModel();
        $item        = $detailModel->find($id);

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found.');
        }

        $purchase_id = $item['purchase_id'];
        $detailModel->delete($id);

        // Recalculate purchase total
        $this->recalcTotal($purchase_id);

        return redirect()->back()->with('success', 'Item removed.');
    }

    // ---------------------------------------------------------------
    // UPDATE a single line item
    // ---------------------------------------------------------------
    public function update_item()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $detailModel = new PurchaseDetailModel();
        $id          = $this->request->getPost('id');
        $purchase_id = $this->request->getPost('purchase_id');

        $detailModel->update($id, [
            'batch_id'   => $this->request->getPost('batch_id'),
            'qty'        => $this->request->getPost('qty'),
            'cost'       => $this->request->getPost('cost'),
            'price'      => $this->request->getPost('price'),
            'mfg_date'   => $this->request->getPost('mfg_date') ?: null,
            'exp_date'   => $this->request->getPost('exp_date') ?: null,
        ]);

        $this->recalcTotal($purchase_id);

        return redirect()->to(base_url('purchases/view/' . $purchase_id))->with('success', 'Item updated.');
    }

    // ---------------------------------------------------------------
    // EXPORT: CSV of current view
    // ---------------------------------------------------------------
    public function export_csv()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $builder = $db->table('purchases p');
        $builder->select('p.id, v.name as vendor, p.date, p.total_amount, p.status, p.note');
        $builder->join('vendors v', 'v.id = p.vendor_id', 'left');

        $vendor_id = $this->request->getGet('vendor_id');
        $status    = $this->request->getGet('status');
        if($vendor_id) $builder->where('p.vendor_id', $vendor_id);
        if($status)    $builder->where('p.status', $status);
        
        $results = $builder->orderBy('p.date', 'DESC')->get()->getResultArray();

        $filename = "purchases_export_".date('Ymd').".csv";
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");

        $file = fopen('php://output', 'w');
        fputcsv($file, ['Order ID', 'Vendor', 'Date', 'Total Amount', 'Status', 'Note']);
        foreach ($results as $r) {
            fputcsv($file, $r);
        }
        fclose($file);
        exit;
    }

    // ---------------------------------------------------------------
    // UPDATE STATUS: Quick toggle from list/view
    // ---------------------------------------------------------------
    public function update_status($id, $status)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        $purchaseModel = new PurchaseModel();
        $purchaseModel->update($id, ['status' => $status]);
        $this->syncVendorPayment($id);
        return redirect()->back()->with('success', 'Status updated to: ' . ucfirst(str_replace('_', ' ', $status)));
    }
    private function recalcTotal($purchase_id)
    {
        $db    = \Config\Database::connect();
        $total = $db->table('purchase_details')
            ->select('SUM(qty * cost) as total', false)
            ->where('purchase_id', $purchase_id)
            ->get()->getRow()->total ?? 0;

        // Use raw query to avoid allowedFields restriction
        $db->table('purchases')->where('id', $purchase_id)->update(['total_amount' => $total]);
        $this->syncVendorPayment($purchase_id);
    }

    private function syncVendorPayment($purchase_id)
    {
        $db = \Config\Database::connect();
        $purchase = $db->table('purchases')->where('id', $purchase_id)->get()->getRow();
        if (!$purchase) return;

        $paymentModel = new VendorPaymentModel();

        if ($purchase->status === 'paid') {
            $existing = $paymentModel->where('purchase_id', $purchase_id)->first();
            if ($existing) {
                $paymentModel->update($existing['id'], [
                    'amount' => $purchase->total_amount,
                    'vendor_id' => $purchase->vendor_id,
                    'payment_date' => $purchase->date
                ]);
            } else {
                $paymentModel->insert([
                    'vendor_id' => $purchase->vendor_id,
                    'purchase_id' => $purchase_id,
                    'amount' => $purchase->total_amount,
                    'payment_date' => $purchase->date,
                    'notes' => 'Auto-payment for full Purchase #' . $purchase_id
                ]);
            }
        } else {
            $paymentModel->where('purchase_id', $purchase_id)->delete();
        }
    }

    // ---------------------------------------------------------------
    // VENDOR DUES
    // ---------------------------------------------------------------
    public function dues()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db      = \Config\Database::connect();
        $builder = $db->table('vendors v');
        $builder->select('v.*,
            (SELECT SUM(total_amount) FROM purchases WHERE vendor_id = v.id) as total_purchase_value,
            (SELECT SUM(amount) FROM vendor_payments WHERE vendor_id = v.id) as total_paid');

        $data['vendors'] = $builder->get()->getResultArray();
        return view('purchases/dues', $data);
    }

    // ---------------------------------------------------------------
    // ADD VENDOR PAYMENT
    // ---------------------------------------------------------------
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

    // ---------------------------------------------------------------
    // VENDOR HISTORY / LEDGER
    // ---------------------------------------------------------------
    public function vendor_history($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vModel = new VendorModel();
        $vendor = $vModel->find($id);
        if (!$vendor) return redirect()->to(base_url('vendors'))->with('error', 'Vendor not found.');

        $db = \Config\Database::connect();

        // Purchases for this vendor
        $purchases = $db->table('purchases p')
            ->select('p.*')
            ->where('p.vendor_id', $id)
            ->orderBy('p.date', 'ASC')
            ->get()->getResultArray();

        // Payments
        $payments = $db->table('vendor_payments')
            ->where('vendor_id', $id)
            ->orderBy('payment_date', 'ASC')
            ->get()->getResultArray();

        // Build ledger
        $ledger          = [];
        $total_purchased = 0;
        foreach ($purchases as $p) {
            $total_purchased += $p['total_amount'];
            $ledger[] = [
                'date'        => $p['date'],
                'type'        => 'PURCHASE',
                'description' => 'Purchase #' . $p['id'] . ' — ' . ucfirst(str_replace('_', ' ', $p['status'])),
                'debit'       => $p['total_amount'],
                'credit'      => 0,
                'ref'         => $p['id'],
            ];
        }

        $total_paid = 0;
        foreach ($payments as $pmt) {
            $total_paid += $pmt['amount'];
            $ledger[] = [
                'date'        => $pmt['payment_date'],
                'type'        => 'PAYMENT',
                'description' => 'Payment: ' . ($pmt['notes'] ?? 'No notes'),
                'debit'       => 0,
                'credit'      => $pmt['amount'],
                'ref'         => $pmt['id'] ?? null,
            ];
        }

        usort($ledger, fn($a, $b) => strtotime($a['date'] ?? '1970-01-01') - strtotime($b['date'] ?? '1970-01-01'));

        $running = 0;
        foreach ($ledger as &$entry) {
            $running         += ($entry['debit'] - $entry['credit']);
            $entry['balance'] = $running;
        }

        $activeBuilder = $db->table('purchase_details s')
            ->select('p.name as product_name, pdt.unit, pdt.unit_value, s.batch_id, s.exp_date as expiry_date,
                     (s.qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = s.id), 0)) as on_shelf')
            ->join('products p', 'p.id = s.product_id')
            ->join('product_details pdt', 'pdt.id = s.product_detail_id')
            ->join('purchases pr', 'pr.id = s.purchase_id')
            ->where('pr.vendor_id', $id)
            ->having('on_shelf >', 0);
        $data['active_inventory'] = $activeBuilder->get()->getResultArray();

        // Top products
        $topBuilder = $db->table('purchase_details pd')
            ->select('pr.name as product_name, pdt.unit, pdt.unit_value, SUM(pd.qty) as total_units, SUM(pd.qty * pd.cost) as total_value')
            ->join('purchases p', 'p.id = pd.purchase_id')
            ->join('products pr', 'pr.id = pd.product_id')
            ->join('product_details pdt', 'pdt.id = pd.product_detail_id')
            ->where('p.vendor_id', $id)
            ->groupBy('pd.product_detail_id')
            ->orderBy('total_units', 'DESC')
            ->limit(5);
        $data['top_products'] = $topBuilder->get()->getResultArray();

        // Monthly trend (ensure year is included in group to avoid name collisions)
        $sixMonthsAgo    = date('Y-m-d', strtotime('-6 months'));
        $trendBuilder    = $db->table('purchases')
            ->select("DATE_FORMAT(date, '%b %Y') as month_label, DATE_FORMAT(date, '%Y-%m') as month_key, SUM(total_amount) as total")
            ->where('vendor_id', $id)
            ->where('date >=', $sixMonthsAgo)
            ->groupBy('month_key')
            ->orderBy('month_key', 'ASC');
        
        $trendResults = $trendBuilder->get()->getResultArray();
        $data['supply_trend'] = array_map(fn($r) => ['month' => $r['month_label'], 'total' => $r['total']], $trendResults);

        $data['ledger'] = array_reverse($ledger);
        $data['vendor'] = $vendor;
        $data['summary'] = [
            'total_purchased' => $total_purchased,
            'total_paid'      => $total_paid,
            'balance'         => $total_purchased - $total_paid,
            'items_count'     => count($purchases),
        ];

        return view('purchases/vendor_history', $data);
    }

    public function delete_payment($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $paymentModel = new VendorPaymentModel();
        $payment      = $paymentModel->find($id);
        $vendor_id    = $payment['vendor_id'] ?? null;
        $paymentModel->delete($id);

        return redirect()->to(base_url('purchases/vendor/' . $vendor_id))->with('success', 'Payment removed.');
    }

    // Invoice for a single purchase header
    public function invoice($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();

        $purchase = $db->table('purchases p')
            ->select('p.*, v.name as vendor_name, v.phone as vendor_phone, v.address as vendor_address')
            ->join('vendors v', 'v.id = p.vendor_id', 'left')
            ->where('p.id', $id)
            ->get()->getRowArray();

        $items = $db->table('purchase_details pd')
            ->select('pd.*, pr.name as product_name, pdt.unit, pdt.unit_value')
            ->join('products pr', 'pr.id = pd.product_id')
            ->join('product_details pdt', 'pdt.id = pd.product_detail_id')
            ->where('pd.purchase_id', $id)
            ->get()->getResultArray();

        $data['purchase'] = $purchase;
        $data['items']    = $items;
        return view('purchases/invoice', $data);
    }
}
