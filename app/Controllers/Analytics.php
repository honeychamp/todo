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
            'today' => [
                'start' => date('Y-m-d'),
                'end'   => date('Y-m-d'),
                'label' => 'Today'
            ],
            'week' => [
                'start' => date('Y-m-d', strtotime('monday this week')),
                'end'   => date('Y-m-d', strtotime('sunday this week')),
                'label' => 'This Week'
            ],
            'month' => [
                'start' => date('Y-m-01'),
                'end'   => date('Y-m-t'),
                'label' => 'This Month'
            ],
            'year' => [
                'start' => date('Y-01-01'),
                'end'   => date('Y-12-31'),
                'label' => 'This Year'
            ]
        ];

        $stats = [];
        foreach ($periods as $key => $p) {
            $stats[$key] = $this->getDetailedMetrics($db, $p['start'], $p['end']);
            $stats[$key]['label'] = $p['label'];
        }

        // 2. STOCK FLOW AUDIT (The "Accountant" view)
        // Opening Stock + Purchases - Sales = Closing Stock
        // For 'This Month'
        $mStart = date('Y-m-01');
        $mEnd   = date('Y-m-t');

        // Opening Stock (Total units effectively held before this month)
        $openingQty = $db->query("SELECT 
                                    (SELECT COALESCE(SUM(initial_qty), 0) FROM stock_purchase WHERE DATE(created_at) < ?) - 
                                    (SELECT COALESCE(SUM(qty), 0) FROM sales WHERE DATE(sale_date) < ?) as opening", [$mStart, $mStart])->getRow()->opening ?? 0;
        
        // Purchases In
        $purchasedQty = $db->query("SELECT COALESCE(SUM(initial_qty), 0) as total FROM stock_purchase WHERE DATE(created_at) BETWEEN ? AND ?", [$mStart, $mEnd])->getRow()->total ?? 0;

        // Sales Out
        $soldQty = $db->query("SELECT COALESCE(SUM(qty), 0) as total FROM sales WHERE DATE(sale_date) BETWEEN ? AND ?", [$mStart, $mEnd])->getRow()->total ?? 0;

        $data['audit'] = [
            'opening' => $openingQty,
            'in'      => $purchasedQty,
            'out'     => $soldQty,
            'closing' => ($openingQty + $purchasedQty - $soldQty)
        ];

        // 3. MASTER COMMAND STATS
        $data['lifetime'] = [
            'total_sales'      => $db->query("SELECT SUM(qty * sale_price) as total FROM sales")->getRow()->total ?? 0,
            'total_investment' => $db->query("SELECT SUM(initial_qty * cost) as total FROM stock_purchase")->getRow()->total ?? 0,
            'total_expenses'   => $db->query("SELECT SUM(amount) as total FROM expenses")->getRow()->total ?? 0,
            'current_stock_valuation' => $db->query("SELECT SUM((initial_qty - COALESCE((SELECT SUM(qty) FROM sales WHERE stock_id = sp.id), 0)) * sp.cost) as total FROM stock_purchase sp")->getRow()->total ?? 0,
        ];

        // 4. TOP PERFORMERS
        $data['top_selling_products'] = $db->query("SELECT p.name, SUM(s.qty) as units, SUM(s.qty * s.sale_price) as revenue 
                                                  FROM sales s 
                                                  JOIN products p ON p.id = s.product_id 
                                                  GROUP BY s.product_id 
                                                  ORDER BY units DESC LIMIT 5")->getResultArray();

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
        $purchases = $db->query("SELECT SUM(initial_qty * cost) as total FROM stock_purchase WHERE DATE(created_at) BETWEEN ? AND ?", [$start, $end])->getRow()->total ?? 0;

        $salesRow = $db->query("SELECT 
                                    COALESCE(SUM(s.qty * s.sale_price), 0) as revenue,
                                    COALESCE(SUM((s.sale_price - sp.cost) * s.qty), 0) as gross_profit,
                                    COUNT(DISTINCT s.id) as tx
                                 FROM sales s 
                                 JOIN stock_purchase sp ON sp.id = s.stock_id
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
