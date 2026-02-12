<?php

namespace App\Controllers;

use App\Models\TaskModel;
use CodeIgniter\Controller;

class Tasks extends Controller
{
    
    public function index()
    {
        $userId = session('user_id');
        $model = new TaskModel();
        
        $data['tasks'] = $userId ? $model->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll() : [];
        
        return view('tasks/index', $data);
    }

    
    public function create()
    {
        if (!session('logged_in')) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Please login first.');
        }

        $title = $this->request->getPost('title');
        
        if (!empty($title)) {
            (new TaskModel())->insert([
                'title'   => $title,
                'status'  => 'pending',
                'user_id' => session('user_id')
            ]);
            return redirect()->to(base_url('/'))->with('success', 'Task added successfully!');
        }

        return redirect()->to(base_url('/'))->with('error', 'Task title cannot be empty.');
    }

    
    public function update($id)
    {
        if (!session('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new TaskModel();
        $task = $model->where('user_id', session('user_id'))->find($id);

        if ($task) {
            $model->update($id, ['status' => ($task['status'] == 'pending' ? 'completed' : 'pending')]);
            return redirect()->to(base_url('/'))->with('success', 'Task status updated!');
        }

        return redirect()->to(base_url('/'))->with('error', 'Access denied.');
    }

    
    public function delete($id)
    {
        if (!session('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new TaskModel();
        if ($model->where('user_id', session('user_id'))->find($id)) {
            $model->delete($id);
            return redirect()->to(base_url('/'))->with('success', 'Task deleted!');
        }

        return redirect()->to(base_url('/'))->with('error', 'Access denied.');
    }

   
    public function history()
    {
        if (!session('logged_in')) {
            return redirect()->to(base_url('auth/login'))->with('error', 'Please login first.');
        }

        $data['all_tasks'] = (new TaskModel())->where('user_id', session('user_id'))->orderBy('created_at', 'DESC')->findAll();
        
        return view('tasks/history', $data);
    }
}
