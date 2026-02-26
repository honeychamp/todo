<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;

class Expenses extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new ExpenseModel();
        
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        if ($start_date && $end_date) {
            $data['expenses'] = $model->where('expense_date >=', $start_date)
                                      ->where('expense_date <=', $end_date)
                                      ->orderBy('expense_date', 'DESC')
                                      ->findAll();
        } else {
            $data['expenses'] = $model->orderBy('expense_date', 'DESC')->findAll();
        }

        $data['total_expense'] = array_sum(array_column($data['expenses'], 'amount'));
        $data['start_date'] = $start_date;
        $data['end_date'] = $end_date;

        return view('expenses/index', $data);
    }

    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new ExpenseModel();
        $data = [
            'title'        => $this->request->getPost('title'),
            'amount'       => $this->request->getPost('amount'),
            'category'     => $this->request->getPost('category'),
            'expense_date' => $this->request->getPost('expense_date'),
        ];

        if ($model->insert($data)) {
            return redirect()->to(base_url('expenses'))->with('success', 'Expense logged successfully');
        }
        return redirect()->back()->with('error', 'Failed to log expense');
    }

    public function export_expenses()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new ExpenseModel();
        
        $start_date = $this->request->getGet('start_date');
        $end_date = $this->request->getGet('end_date');

        if ($start_date && $end_date) {
            $expenses = $model->where('expense_date >=', $start_date)
                              ->where('expense_date <=', $end_date)
                              ->orderBy('expense_date', 'DESC')
                              ->findAll();
        } else {
            $expenses = $model->orderBy('expense_date', 'DESC')->findAll();
        }

        $filename = 'expenses_report_'.date('Ymd').'.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv;");

        $file = fopen('php://output', 'w');
        fputcsv($file, ['ID', 'Date', 'Title', 'Category', 'Amount']);

        foreach ($expenses as $e) {
            fputcsv($file, [
                $e['id'],
                $e['expense_date'],
                $e['title'],
                $e['category'],
                $e['amount']
            ]);
        }
        fclose($file);
        exit;
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new ExpenseModel();
        $model->delete($id);
        return redirect()->to(base_url('expenses'))->with('success', 'Expense removed');
    }
}
