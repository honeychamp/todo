<?php

namespace App\Controllers;

use App\Models\TaskModel;
use CodeIgniter\Controller;

class Tasks extends Controller
{
    public function index()
    {
        $model = new TaskModel();
        $data['tasks'] = $model->orderBy('created_at', 'DESC')->findAll();
        
        return view('todo_list', $data);
    }

    public function create()
    {
        $model = new TaskModel();
        $title = $this->request->getPost('title');
        
        if (!empty($title)) {
            $model->save([
                'title' => $title,
                'status' => 'pending'
            ]);
        }
        
        return redirect()->to(base_url('/'));
    }

    public function update($id)
    {
        $model = new TaskModel();
        $task = $model->find($id);
        
        if ($task) {
            $newStatus = ($task['status'] === 'pending') ? 'completed' : 'pending';
            $model->update($id, ['status' => $newStatus]);
        }
        
        return redirect()->to(base_url('/'));
    }

    public function delete($id)
    {
        $model = new TaskModel();
        $model->delete($id);
        return redirect()->to(base_url('/'));
    }
}
