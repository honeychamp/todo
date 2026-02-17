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
        // Grabbing all purchases with product names so we know what is what
        $data['purchases'] = $model->select('stock_purchase.*, products.name as product_name')
                                  ->join('products', 'products.id = stock_purchase.product_id')
                                  ->orderBy('stock_purchase.id', 'DESC')
                                  ->findAll();

        $prodModel = new ProductModel();
        $data['products'] = $prodModel->findAll();

        return view('stocks/purchase', $data);
    }

    // Adding new stock to the systems
    public function add_purchase()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        
        $model = new StockModel();
        $data = [
            'batch_id'         => $this->request->getPost('batch_id'),
            'product_id'       => $this->request->getPost('product_id'),
            'manufacture_date' => $this->request->getPost('manufacture_date'),
            'expiry_date'      => $this->request->getPost('expiry_date'),
            'qty'              => $this->request->getPost('qty'),
            'cost'             => $this->request->getPost('cost'),
            'price'            => $this->request->getPost('price'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('stocks/purchase'))->with('success', 'Stock added successfully!');
        }
        return redirect()->back()->with('error', 'Something went wrong while adding stock.');
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
        $data['stocks'] = $model->select('stock_purchase.*, products.name as product_name')
                               ->join('products', 'products.id = stock_purchase.product_id')
                               ->where('stock_purchase.qty >', 0)
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
            $saleModel->insert([
                'stock_id'   => $stockId,
                'product_id' => $stock['product_id'],
                'qty'        => $qtyToSell,
                'sale_price' => $stock['price'],
                'sale_date'  => date('Y-m-d H:i:s')
            ]);

            // Deduct from stock
            $stockModel->update($stockId, ['qty' => $stock['qty'] - $qtyToSell]);

            return redirect()->to(base_url('stocks/sales'))->with('success', 'Sale processed successfully.');
        }

        return redirect()->back()->with('error', 'Not enough stock or invalid quantity.');
    }

    public function sales_report()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to(base_url('auth/login'));
        }

        $saleModel = new SaleModel();
        $data['sales'] = $saleModel->select('sales.*, products.name as product_name, stock_purchase.batch_id')
                                  ->join('products', 'products.id = sales.product_id')
                                  ->join('stock_purchase', 'stock_purchase.id = sales.stock_id')
                                  ->orderBy('sales.sale_date', 'DESC')
                                  ->findAll();

        return view('stocks/sales_report', $data);
    }
}
