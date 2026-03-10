<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DoctorModel;
use App\Models\DoctorPaymentModel;
use App\Models\SaleModel;

class Doctors extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $db = \Config\Database::connect();
        $builder = $db->table('doctors d');
        $builder->select('d.*, 
                         (SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE doctor_id = d.id) as total_purchased,
                         (SELECT COALESCE(SUM(amount), 0) FROM doctor_payments WHERE doctor_id = d.id) as total_paid');
        $builder->orderBy('d.name', 'ASC');
        
        $data['doctors'] = $builder->get()->getResultArray();
        return view('doctors/index', $data);
    }

    public function add()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        return view('doctors/add');
    }

    public function create()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new DoctorModel();
        $model->save([
            'name'    => $this->request->getPost('name'),
            'phone'   => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
        ]);

        return redirect()->to(base_url('doctors'))->with('success', 'Doctor added successfully!');
    }

    public function ledger($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new DoctorModel();
        $doctor = $model->find($id);
        if (!$doctor) return redirect()->to(base_url('doctors'))->with('error', 'Doctor not found.');

        $db = \Config\Database::connect();
        
        // Get all sales for this doctor, grouped by sale header (invoice)
        $sBuilder = $db->table('sales s');
        $sBuilder->select('s.*, GROUP_CONCAT(p.name SEPARATOR ", ") as products_summary');
        $sBuilder->join('sale_details sd', 'sd.sale_id = s.id');
        $sBuilder->join('products p', 'p.id = sd.product_id');
        $sBuilder->where('s.doctor_id', $id);
        $sBuilder->groupBy('s.id');
        $sBuilder->orderBy('s.sale_date', 'ASC');
        $sales = $sBuilder->get()->getResultArray();

        // Get all payments for this doctor
        $pBuilder = $db->table('doctor_payments');
        $pBuilder->where('doctor_id', $id);
        $pBuilder->orderBy('payment_date', 'ASC');
        $payments = $pBuilder->get()->getResultArray();

        // Combine and Sort for Ledger
        $ledger = [];
        $total_purchased = 0;
        foreach($sales as $s) {
            $amount = $s['total_amount'];
            $total_purchased += $amount;
            $ledger[] = [
                'date' => $s['sale_date'],
                'type' => 'SALE',
                'description' => "Invoice #" . ($s['invoice_no'] ?: $s['id']) . " (" . $s['products_summary'] . ")",
                'debit' => $amount,
                'credit' => 0,
                'ref' => $s['id']
            ];
        }

        $total_paid = 0;
        foreach($payments as $pmt) {
            $total_paid += $pmt['amount'];
            $ledger[] = [
                'date' => $pmt['payment_date'],
                'type' => 'PAYMENT',
                'description' => "Payment Received: " . ($pmt['notes'] ?: 'No notes'),
                'debit' => 0,
                'credit' => $pmt['amount'],
                'ref' => $pmt['id']
            ];
        }

        usort($ledger, function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

        $running = 0;
        foreach($ledger as &$entry) {
            $running += ($entry['debit'] - $entry['credit']);
            $entry['balance'] = $running;
        }

        $data['ledger'] = array_reverse($ledger);
        $data['doctor'] = $doctor;
        $data['summary'] = [
            'total_purchased' => $total_purchased,
            'total_paid'      => $total_paid,
            'balance'         => $total_purchased - $total_paid
        ];

        return view('doctors/ledger', $data);
    }

    public function add_payment()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $pModel = new DoctorPaymentModel();
        $pModel->save([
            'doctor_id'      => $this->request->getPost('doctor_id'),
            'amount'         => $this->request->getPost('amount'),
            'payment_date'   => $this->request->getPost('payment_date'),
            'payment_method' => $this->request->getPost('payment_method'),
            'notes'          => $this->request->getPost('notes'),
        ]);

        return redirect()->back()->with('success', 'Payment recorded successfully!');
    }

    public function payments()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $pModel = new DoctorPaymentModel();
        $data['payments'] = $pModel->select('doctor_payments.*, doctors.name as doctor_name')
                                   ->join('doctors', 'doctors.id = doctor_payments.doctor_id')
                                   ->orderBy('payment_date', 'DESC')
                                   ->findAll();
        
        return view('doctors/payments', $data);
    }

    public function delete($id)
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new DoctorModel();
        $sModel = new SaleModel();
        if ($sModel->where('doctor_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Cannot delete doctor with existing sales records.');
        }

        $model->delete($id);
        return redirect()->to(base_url('doctors'))->with('success', 'Doctor removed.');
    }
}
