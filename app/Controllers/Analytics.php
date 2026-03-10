<?php

namespace App\Controllers;

class Analytics extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();

        // 1. Time Period Definitions (Standard for Pharmacy Audit)
        $periods = [
            'today' => ['start' => date('Y-m-d'), 'end' => date('Y-m-d'), 'label' => 'Today'],
            'week'  => ['start' => date('Y-m-d', strtotime('monday this week')), 'end' => date('Y-m-d', strtotime('sunday this week')), 'label' => 'This Week'],
            'month' => ['start' => date('Y-m-01'), 'end' => date('Y-m-t'), 'label' => 'This Month'],
            'year'  => ['start' => date('Y-01-01'), 'end' => date('Y-12-31'), 'label' => 'This Year']
        ];

        $stats = [];
        foreach ($periods as $key => $p) {
            $stats[$key] = $this->getDetailedMetrics($db, $p['start'], $p['end']);
            $stats[$key]['label'] = $p['label'];
        }

        // 2. STOCK FLOW AUDIT
        $mStart = date('Y-m-01');
        $mEnd   = date('Y-m-t');

        // Opening Stock (Total units effectively held before this month)
        $openingQty = $db->query("SELECT 
                                    (SELECT COALESCE(SUM(qty), 0) FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE DATE(pd.created_at) < ? AND pr.status IN ('received','partial_paid','paid')) - 
                                    (SELECT COALESCE(SUM(sd.qty), 0) FROM sale_details sd JOIN sales s ON s.id = sd.sale_id WHERE DATE(s.sale_date) < ?) as opening", [$mStart, $mStart])->getRow()->opening ?? 0;
        
        // Purchases In
        $purchasedQty = $db->query("SELECT COALESCE(SUM(qty), 0) as total FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND pr.status IN ('received','partial_paid','paid')", [$mStart, $mEnd])->getRow()->total ?? 0;

        // Sales Out
        $soldQty = $db->query("SELECT COALESCE(SUM(sd.qty), 0) as total FROM sale_details sd JOIN sales s ON s.id = sd.sale_id WHERE DATE(s.sale_date) BETWEEN ? AND ?", [$mStart, $mEnd])->getRow()->total ?? 0;

        $data['audit'] = [
            'opening' => $openingQty,
            'in'      => $purchasedQty,
            'out'     => $soldQty,
            'closing' => ($openingQty + $purchasedQty - $soldQty)
        ];

        // 3. MASTER COMMAND STATS
        $data['lifetime'] = [
            'total_sales'      => $db->query("SELECT SUM((qty * sale_price) - discount) as total FROM sale_details")->getRow()->total ?? 0,
            'total_investment' => $db->query("SELECT SUM(qty * cost) as total FROM purchase_details")->getRow()->total ?? 0,
            'total_expenses'   => $db->query("SELECT SUM(amount) as total FROM expenses")->getRow()->total ?? 0,
            'current_stock_valuation' => $db->query("SELECT SUM((qty - COALESCE((SELECT SUM(qty) FROM sale_details WHERE sale_details.stock_id = sp.id), 0)) * sp.cost) as total FROM purchase_details sp")->getRow()->total ?? 0,
        ];

        // 4. TOP PERFORMERS (Products & Vendors)
        $data['top_selling_products'] = $db->query("SELECT p.name, pdt.unit, pdt.unit_value, SUM(sd.qty) as units, SUM((sd.qty * sd.sale_price) - sd.discount) as revenue 
                                                  FROM sale_details sd 
                                                  JOIN products p ON p.id = sd.product_id 
                                                  JOIN product_details pdt ON pdt.id = sd.product_detail_id
                                                  GROUP BY sd.product_detail_id 
                                                  ORDER BY units DESC LIMIT 5")->getResultArray();

        $data['vendor_spending'] = $db->query("SELECT v.name, SUM(p.total_amount) as total 
                                             FROM purchases p 
                                             JOIN vendors v ON v.id = p.vendor_id 
                                             GROUP BY p.vendor_id 
                                             ORDER BY total DESC LIMIT 5")->getResultArray();

        // 5. PERIOD TREND (Last 12 Months)
        $months = [];
        for($i = 11; $i >= 0; $i--) {
            $ms = date('Y-m-01', strtotime("-$i months"));
            $me = date('Y-m-t', strtotime("-$i months"));
            $ml = date('M Y', strtotime($ms));
            $mm = $this->getDetailedMetrics($db, $ms, $me);
            $months['labels'][] = $ml;
            $months['profit'][] = $mm['net_profit'];
            $months['revenue'][] = $mm['revenue'];
        }

        $data['stats'] = $stats;
        $data['months_trend'] = $months;
        
        return view('analytics/index', $data);
    }

    private function getDetailedMetrics($db, $start, $end)
    {
        $purchases = $db->query("SELECT SUM(pd.qty * pd.cost) as total FROM purchase_details pd JOIN purchases pr ON pr.id = pd.purchase_id WHERE (DATE(pd.created_at) BETWEEN ? AND ?) AND pr.status IN ('received','partial_paid','paid')", [$start, $end])->getRow()->total ?? 0;

        $salesRow = $db->query("SELECT 
                                    COALESCE(SUM((sd.qty * sd.sale_price) - sd.discount), 0) as revenue,
                                    COALESCE(SUM(((sd.sale_price * sd.qty) - sd.discount) - (sp.cost * sd.qty)), 0) as gross_profit,
                                    COUNT(DISTINCT s.id) as tx
                                 FROM sale_details sd 
                                 JOIN sales s ON s.id = sd.sale_id
                                 JOIN purchase_details sp ON sp.id = sd.stock_id
                                 WHERE DATE(s.sale_date) BETWEEN ? AND ?", [$start, $end])->getRow();
        
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
}
