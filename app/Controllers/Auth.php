<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    
    public function login() 
    { 
        return view('auth/login'); 
    }

    
    public function register() 
    { 
        return view('auth/register'); 
    }
    
    
    public function process_register()
    {
        $rules = [
            'username' => [
                'rules'  => 'required|min_length[3]|is_unique[users.username]',
                'errors' => [
                    'is_unique' => 'This username is already taken.',
                    'min_length' => 'Username must be at least 3 characters long.'
                ]
            ],
            'email' => [
                'rules'  => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'is_unique' => 'This email is already registered.',
                    'valid_email' => 'Please provide a valid email address.'
                ]
            ],
            'password' => [
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'min_length' => 'Password must be at least 6 characters long.'
                ]
            ],
            'confpassword' => [
                'rules'  => 'matches[password]',
                'errors' => [
                    'matches' => 'Passwords do not match.'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->save([
            'username' => trim($this->request->getPost('username')),
            'email'    => trim($this->request->getPost('email')),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);
        
        return redirect()->to(base_url('auth/login'))->with('success', 'Account created! Please login.');
    }

    
    public function process_login()
    {
        $input = trim($this->request->getVar('username'));
        $password = $this->request->getVar('password');
        
        $user = (new UserModel())->where('username', $input)->orWhere('email', $input)->first();
        
        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'email'     => $user['email'],
                'logged_in' => true
            ]);
            return redirect()->to(base_url('/'));
        }

        return redirect()->back()->with('error', 'Invalid username or password');
    }

    
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('/'));
    }
}
