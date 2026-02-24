<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

use App\Models\StockModel;
use App\Models\ProductModel;
use App\Models\SaleModel;

class Stocks extends BaseController
{
    // View all stock purchases
    public function purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $data['purchases'] = $model->select('stock_purchase.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, vendors.name as vendor_name')
                                  ->join('products', 'products.id = stock_purchase.product_id')
                                  ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left')
                                  ->orderBy('created_at', 'DESC')
                                  ->findAll();

        $vendorModel = new \App\Models\VendorModel();
        $prodModel = new \App\Models\ProductModel();
        $data['vendors'] = $vendorModel->findAll();
        $data['products'] = $prodModel->findAll();

        return view('stocks/purchase', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $vendorModel = new \App\Models\VendorModel();
        $prodModel = new \App\Models\ProductModel();
        
        $data['vendors'] = $vendorModel->findAll();
        $data['products'] = $prodModel->findAll();

        return view('stocks/add', $data);
    }

    // Adding new stock to the systems
    public function add_purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();

        $batchIds        = $this->request->getPost('batch_id');
        $vendorIds       = $this->request->getPost('vendor_id');
        $productIds      = $this->request->getPost('product_id');
        $mfgDates        = $this->request->getPost('manufacture_date');
        $expDates        = $this->request->getPost('expiry_date');
        $qtys            = $this->request->getPost('qty');
        $costs           = $this->request->getPost('cost');
        $prices          = $this->request->getPost('price');

        // Support both single (old modal) and multiple (new table) submissions
        if (is_array($batchIds)) {
            $rows = [];
            foreach ($batchIds as $i => $batch_id) {
                if (empty($productIds[$i])) continue;
                $rows[] = [
                    'batch_id'         => $batch_id,
                    'vendor_id'        => $vendorIds[$i] ?: null,
                    'product_id'       => $productIds[$i],
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
                'manufacture_date' => $mfgDates,
                'expiry_date'      => $expDates,
                'qty'              => $qtys,
                'cost'             => $costs,
                'price'            => $prices,
            ]);
        }

        return redirect()->to(base_url('stocks/purchase'))->with('success', 'Stock added successfully!');
    }

    // Delete a purchase record
    public function delete_purchase($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $model->delete($id);
        return redirect()->to(base_url('stocks/purchase'))->with('success', 'Purchase record removed.');
    }

    // Update existing stock info
    public function update_purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $id = $this->request->getPost('id');
        $data = [
            'batch_id'         => $this->request->getPost('batch_id'),
            'vendor_id'        => $this->request->getPost('vendor_id'),
            'product_id'       => $this->request->getPost('product_id'),
            'manufacture_date' => $this->request->getPost('manufacture_date'),
            'expiry_date'      => $this->request->getPost('expiry_date'),
            'qty'              => $this->request->getPost('qty'),
            'cost'             => $this->request->getPost('cost'),
            'price'            => $this->request->getPost('price'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('stocks/purchase'))->with('success', 'Stock data updated.');
        }
        return redirect()->back()->with('error', 'Update failed, check fields again.');
    }

    // Page for making a sale
    public function sales()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        // We only show items that are actually in stock
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
        
        $stockId = $this->request->getPost('stock_id');
        $qtyToSell = $this->request->getPost('qty');

        $stockModel = new StockModel();
        $stock = $stockModel->find($stockId);

        // Making sure we don't sell more than we have!
        if ($stock && $stock['qty'] >= $qtyToSell) {
            // Log the sale
            $saleModel = new SaleModel();
            $saleId = $saleModel->insert([
                'stock_id'   => $stockId,
                'product_id' => $stock['product_id'],
                'qty'        => $qtyToSell,
                'sale_price' => $stock['price'],
                'sale_date'  => date('Y-m-d H:i:s')
            ]);

            // Deduct from stock
            $stockModel->update($stockId, ['qty' => $stock['qty'] - $qtyToSell]);

            return redirect()->to(base_url('stocks/sales'))->with('success', 'Sale processed successfully!')->with('last_sale_id', $saleId);
        }

        return redirect()->back()->with('error', 'Not enough stock or invalid quantity.');
    }

    public function sales_report()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $saleModel = new SaleModel();
        
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        $query = $saleModel->select('sales.*, products.name as product_name, products.unit as product_unit, products.unit_value as product_unit_value, stock_purchase.batch_id, stock_purchase.cost as cost_price, vendors.name as vendor_name')
                          ->join('products', 'products.id = sales.product_id')
                          ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                          ->join('vendors', 'vendors.id = stock_purchase.vendor_id', 'left');

        if ($start_date && $end_date) {
            $query->where('DATE(sale_date) >=', $start_date)
                  ->where('DATE(sale_date) <=', $end_date);
        }

        $data['sales'] = $query->orderBy('sale_date', 'DESC')->findAll();

        $grandTotal = 0;
        $totalProfit = 0;
        foreach ($data['sales'] as $sale) {
            $grandTotal += ($sale['qty'] * $sale['sale_price']);
            // Profit = (Sale Price - Cost Price) * Qty
            $totalProfit += ($sale['sale_price'] - $sale['cost_price']) * $sale['qty'];
        }

        $data['grandTotal'] = $grandTotal;
        $data['totalProfit'] = $totalProfit;
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

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
