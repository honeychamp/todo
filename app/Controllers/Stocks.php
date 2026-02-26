<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\StockModel;
use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\VendorModel;
use App\Models\VendorPaymentModel;

class Stocks extends BaseController
{
    // View all stock purchases
    public function inventory()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new StockModel();
        $data['inventory'] = $model->select('stock_purchase.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, vendors.name as vendor_name')
                                   ->join('products', 'products.id = stock_purchase.product_id')
                                   ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left')
                                   ->where('stock_purchase.qty >', 0)
                                   ->orderBy('stock_purchase.expiry_date', 'ASC')
                                   ->findAll();

        return view('stocks/inventory', $data);
    }

    public function purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new StockModel();
        // Join with products and vendors, and also join with sales to get sold qty per batch
        $db = \Config\Database::connect();
        $builder = $db->table('stock_purchase s');
        $builder->select('s.*, p.name as product_name, p.unit as product_unit, p.unit_value as product_unit_value, v.name as vendor_name, 
                         (SELECT SUM(qty) FROM sales WHERE stock_id = s.id) as sold_qty,
                         (SELECT SUM(qty * sale_price) FROM sales WHERE stock_id = s.id) as batch_revenue');
        $builder->join('products p', 'p.id = s.product_id');
        $builder->join('vendors v', 'v.id = s.vendor_id', 'left');
        $builder->orderBy('s.created_at', 'DESC');
        
        $data['purchases'] = $builder->get()->getResultArray();

        $vModel = new VendorModel();
        $pModel = new ProductModel();
        $data['vendors']  = $vModel->findAll();
        $data['products'] = $pModel->findAll();

        return view('stocks/purchase', $data);
    }

    // Step 1: Show vendor selection page before adding stock
    public function select_vendor()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vendorModel = new VendorModel();
        $data['vendors'] = $vendorModel->orderBy('name', 'ASC')->findAll();

        return view('stocks/select_vendor', $data);
    }

    // Old add page (now vendor_id is pre-set from URL)
    public function add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $vendor_id = $this->request->getGet('vendor_id');
        if (!$vendor_id) return redirect()->to(base_url('stocks/select_vendor'));

        $vendorModel = new VendorModel();
        $prodModel = new ProductModel();
        
        $vendor = $vendorModel->find($vendor_id);
        if (!$vendor) return redirect()->to(base_url('stocks/select_vendor'))->with('error', 'Vendor not found.');

        $data['vendor']   = $vendor;
        $data['vendors']  = $vendorModel->findAll();
        $data['products'] = $prodModel->findAll();

        return view('stocks/add', $data);
    }

    // Adding new stock
    public function add_purchase()
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
                    'qty'              => $qtys[$i],
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

        // Redirect back to the vendor's stock page
        $vendor_id = $this->request->getPost('redirect_vendor_id');
        if ($vendor_id) {
            return redirect()->to(base_url('stocks/vendor/' . $vendor_id))->with('success', 'Stock added successfully!');
        }

        return redirect()->to(base_url('stocks/purchase'))->with('success', 'Stock added successfully!');
    }

    // Delete a purchase record
    public function delete_purchase($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $saleModel = new SaleModel();
        
        // Safety: Don't delete if there are sales
        $salesCount = $saleModel->where('stock_id', $id)->countAllResults();
        if ($salesCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete this stock because some units have already been sold. Void those sales first.');
        }

        $purchase = $model->find($id);
        $vendor_id = $purchase['vendor_id'] ?? null;
        $model->delete($id);

        if ($vendor_id) {
            return redirect()->to(base_url('stocks/vendor/' . $vendor_id))->with('success', 'Stock record removed.');
        }
        return redirect()->to(base_url('stocks/purchase'))->with('success', 'Purchase record removed.');
    }

    // Update existing stock info
    public function update_purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $id = $this->request->getPost('id');
        $model = new StockModel();
        $saleModel = new SaleModel();

        $newInitialQty = $this->request->getPost('qty');
        
        // Calculate items sold for this specific batch
        $soldItems = $saleModel->where('stock_id', $id)->selectSum('qty')->first()['qty'] ?? 0;
        
        // Calculate new remaining stock
        $newQty = $newInitialQty - $soldItems;

        $data_to_update = [
            'batch_id'         => $this->request->getPost('batch_id'),
            'vendor_id'        => $this->request->getPost('vendor_id') ?: null,
            'product_id'       => $this->request->getPost('product_id'),
            'manufacture_date' => $this->request->getPost('manufacture_date'),
            'expiry_date'      => $this->request->getPost('expiry_date'),
            'initial_qty'      => $newInitialQty,
            'qty'              => $newQty,
            'cost'             => $this->request->getPost('cost'),
            'price'            => $this->request->getPost('price'),
        ];

        $vendor_id = $this->request->getPost('redirect_vendor_id');

        if ($model->update($id, $data_to_update)) {
            if ($vendor_id) {
                return redirect()->to(base_url('stocks/vendor/' . $vendor_id))->with('success', 'Stock data updated.');
            }
            return redirect()->to(base_url('stocks/purchase'))->with('success', 'Stock data updated.');
        }
        return redirect()->back()->with('error', 'Update failed, check fields again.');
    }

    // PER VENDOR STOCK PAGE - main new feature
    public function vendor_stock($vendor_id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vendorModel  = new VendorModel();
        $stockModel   = new StockModel();
        $paymentModel = new VendorPaymentModel();
        $prodModel    = new ProductModel();

        $vendor = $vendorModel->find($vendor_id);
        if (!$vendor) return redirect()->to(base_url('stocks/select_vendor'))->with('error', 'Vendor not found.');

        // All stock from this vendor
        $purchases = $stockModel
            ->select('stock_purchase.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value')
            ->join('products', 'products.id = stock_purchase.product_id')
            ->where('stock_purchase.vendor_id', $vendor_id)
            ->orderBy('stock_purchase.created_at', 'DESC')
            ->findAll();

        // Total stock cost (how much we owe this vendor)
        $totalStockCost = array_sum(array_map(fn($p) => $p['cost'] * $p['initial_qty'], $purchases));

        // Total paid
        $totalPaid = $paymentModel->totalPaid($vendor_id);

        // Balance due
        $balanceDue = $totalStockCost - $totalPaid;

        // Payment history
        $payments = $paymentModel->getByVendor($vendor_id);

        $data['vendor']         = $vendor;
        $data['purchases']      = $purchases;
        $data['payments']       = $payments;
        $data['totalStockCost'] = $totalStockCost;
        $data['totalPaid']      = $totalPaid;
        $data['balanceDue']     = $balanceDue;
        $data['products']       = $prodModel->findAll();
        $data['vendors']        = $vendorModel->findAll();

        return view('stocks/vendor_stock', $data);
    }

    // Add payment for a vendor
    public function add_payment()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $paymentModel = new VendorPaymentModel();
        $vendor_id = $this->request->getPost('vendor_id');

        $paymentModel->insert([
            'vendor_id'    => $vendor_id,
            'amount'       => $this->request->getPost('amount'),
            'note'         => $this->request->getPost('note'),
            'payment_date' => $this->request->getPost('payment_date'),
        ]);

        return redirect()->to(base_url('stocks/vendor/' . $vendor_id))->with('success', 'Payment recorded successfully!');
    }

    // Delete a payment
    public function delete_payment($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $paymentModel = new VendorPaymentModel();
        $payment = $paymentModel->find($id);
        $vendor_id = $payment['vendor_id'] ?? null;
        $paymentModel->delete($id);

        return redirect()->to(base_url('stocks/vendor/' . $vendor_id))->with('success', 'Payment removed.');
    }

    // Page for making a sale
    public function sales()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $data['stocks'] = $model->select('stock_purchase.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, vendors.name as vendor_name')
                               ->join('products', 'products.id = stock_purchase.product_id')
                               ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left')
                               ->where('stock_purchase.qty >', 0)
                               ->orderBy('products.name', 'ASC')
                               ->findAll();

        return view('stocks/sales', $data);
    }

    // Process the actual sale transaction
    public function process_sale()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $stockId   = $this->request->getPost('stock_id');
        $qtyToSell = $this->request->getPost('qty');

        $stockModel = new StockModel();
        $stock = $stockModel->find($stockId);

        if ($stock && $stock['qty'] >= $qtyToSell) {
            $saleModel = new SaleModel();
            $saleId = $saleModel->insert([
                'stock_id'       => $stockId,
                'product_id'     => $stock['product_id'],
                'qty'            => $qtyToSell,
                'sale_price'     => $stock['price'],
                'customer_name'  => $this->request->getPost('customer_name'),
                'customer_phone' => $this->request->getPost('customer_phone'),
                'sale_date'      => date('Y-m-d H:i:s')
            ]);

            $stockModel->update($stockId, ['qty' => $stock['qty'] - $qtyToSell]);

            return redirect()->to(base_url('stocks/sales'))->with('success', 'Sale processed successfully!')->with('last_sale_id', $saleId);
        }

        return redirect()->back()->with('error', 'Not enough stock or invalid quantity.');
    }

    public function export_sales()
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
        fputcsv($file, ['ID', 'Date', 'Product', 'Customer', 'Phone', 'Qty', 'Unit Price', 'Cost Price', 'Total Sale', 'Profit', 'Batch', 'Vendor']);

        foreach ($sales as $s) {
            $totalSale = $s['qty'] * $s['sale_price'];
            $totalCost = $s['qty'] * $s['cost_price'];
            $profit = $totalSale - $totalCost;
            
            fputcsv($file, [
                $s['id'],
                $s['sale_date'],
                $s['product_name'],
                $s['customer_name'] ?: 'Cash Customer',
                $s['customer_phone'] ?: '-',
                $s['qty'],
                $s['sale_price'],
                $s['cost_price'],
                $totalSale,
                $profit,
                $s['batch_id'],
                $s['vendor_name'] ?: 'N/A'
            ]);
        }
        fclose($file);
        exit;
    }

    public function sales_report()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $saleModel = new SaleModel();
        
        $start_date = $this->request->getGet('start_date');
        $end_date   = $this->request->getGet('end_date');

        $query = $saleModel->select('sales.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, categories.name as category_name, stock_purchase.batch_id, stock_purchase.cost as cost_price, vendors.name as vendor_name')
                          ->join('products', 'products.id = sales.product_id')
                          ->join('categories', 'categories.id = products.category_id', 'left')
                          ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                          ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left');

        if ($start_date && $end_date) {
            $query->where('DATE(sale_date) >=', $start_date)
                  ->where('DATE(sale_date) <=', $end_date);
        }

        $data['sales'] = $query->orderBy('sale_date', 'DESC')->findAll();

        $grandTotal  = 0;
        $totalProfit = 0;
        $categoryProfit = [];
        
        foreach ($data['sales'] as $sale) {
            $saleTotal = ($sale['qty'] * $sale['sale_price']);
            $saleProfit = ($sale['sale_price'] - $sale['cost_price']) * $sale['qty'];
            
            $grandTotal  += $saleTotal;
            $totalProfit += $saleProfit;
            
            $catName = $sale['category_name'] ?: 'Uncategorized';
            if (!isset($categoryProfit[$catName])) {
                $categoryProfit[$catName] = 0;
            }
            $categoryProfit[$catName] += $saleProfit;
        }

        $data['grandTotal']     = $grandTotal;
        $data['totalProfit']    = $totalProfit;
        $data['categoryProfit'] = $categoryProfit;

    // Fetch Expenses for the same period
    $expModel = new \App\Models\ExpenseModel();
    if ($start_date && $end_date) {
        $totalExp = $expModel->where('expense_date >=', $start_date)
                             ->where('expense_date <=', $end_date)
                             ->selectSum('amount')
                             ->first()['amount'] ?? 0;
    } else {
        $totalExp = $expModel->selectSum('amount')->first()['amount'] ?? 0;
    }
    
    $data['totalExpenses'] = $totalExp;
    $data['netProfit']     = $totalProfit - $totalExp;
    $data['start_date']    = $start_date;
        $data['end_date']    = $end_date;

        return view('stocks/sales_report', $data);
    }

    public function invoice($sale_id)
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $saleModel = new SaleModel();
        $invoice = $saleModel->select('sales.*, products.name as product_name, stock_purchase.batch_id')
                            ->join('products', 'products.id = sales.product_id')
                            ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                            ->where('sales.id', $sale_id)
                            ->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $data['invoice'] = $invoice;
        return view('stocks/invoice', $data);
    }

    // List of all vendors and what we owe them
    public function void_sale($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $saleModel = new SaleModel();
        $stockModel = new StockModel();

        $sale = $saleModel->find($id);
        if ($sale) {
            // Restore stock
            $stock = $stockModel->find($sale['stock_id']);
            if ($stock) {
                $stockModel->update($sale['stock_id'], ['qty' => $stock['qty'] + $sale['qty']]);
            }
            // Delete sale
            $saleModel->delete($id);
            return redirect()->to(base_url('stocks/report'))->with('success', 'Sale transaction has been voided and stock restored.');
        }

        return redirect()->to(base_url('stocks/report'))->with('error', 'Sale record not found.');
    }

    public function vendor_dues()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $vendorModel  = new VendorModel();
        $stockModel   = new StockModel();
        $paymentModel = new VendorPaymentModel();

        $vendors = $vendorModel->orderBy('name', 'ASC')->findAll();
        $duesData = [];
        $totalSystemDues = 0;

        foreach ($vendors as $v) {
            // Total purchases from this vendor
            $stockSum = $stockModel->where('vendor_id', $v['id'])
                                  ->select('SUM(cost * initial_qty) as total') // Changed qty to initial_qty
                                  ->first()['total'] ?? 0;
            
            // Total paid to this vendor
            $paidSum = $paymentModel->totalPaid($v['id']);

            $balance = $stockSum - $paidSum;
            
            if ($balance != 0) {
                $duesData[] = [
                    'id'      => $v['id'],
                    'name'    => $v['name'],
                    'phone'   => $v['phone'],
                    'total'   => $stockSum,
                    'paid'    => $paidSum,
                    'balance' => $balance
                ];
                $totalSystemDues += $balance;
            }
        }

        $data['dues'] = $duesData;
        $data['totalSystemDues'] = $totalSystemDues;

        return view('stocks/vendor_dues', $data);
    }

    public function purchase_invoice($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $purchase = $model->select('stock_purchase.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, vendors.name as vendor_name, vendors.phone as vendor_phone, vendors.address as vendor_address')
                         ->join('products', 'products.id = stock_purchase.product_id')
                         ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left')
                         ->where('stock_purchase.id', $id)
                         ->first();

        if (!$purchase) return redirect()->back()->with('error', 'Record not found.');

        $data['purchase'] = $purchase;
        return view('stocks/purchase_invoice', $data);
    }
}
