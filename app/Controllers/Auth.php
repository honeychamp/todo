<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends BaseController
{
    // Just show the login page
    public function login() 
    { 
        return view('auth/login'); 
    }

    // Just show the registration page
    public function register() 
    { 
        return view('auth/register'); 
    }
    
    // This part handles the registration form
    public function process_register()
    {
        // Rules for form validation
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'confpassword' => 'matches[password]'
        ];

        // If validation fails, go back with errors
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Save New User to Database
        $userModel = new UserModel();
        $userModel->save([
            'username' => $this->request->getPost('username'),
            'email'    => $this->request->getPost('email'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);
        
        return redirect()->to(base_url('auth/login'))->with('success', 'Registration Done! You can login now.');
    }

    // This part handles the login form
    public function process_login()
    {
        $user_input = $this->request->getVar('username');
        $pass_input = $this->request->getVar('password');
        
        $model = new UserModel();
        // Look for user by username or email
        $user = $model->where('username', $user_input)->orWhere('email', $user_input)->first();
        
        // If user exists and password is correct
        if ($user && password_verify($pass_input, $user['password'])) {
            // Start the session
            session()->set([
                'user_id'   => $user['id'],
                'username'  => $user['username'],
                'logged_in' => true
            ]);
            return redirect()->to(base_url('/'));
        }

        // If login fails
        return redirect()->back()->with('error', 'Wrong username or password');
    }

    // Kill the session to logout
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'));
    }

    public function profile()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));
        return view('auth/profile');
    }

    public function updatePassword()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $rules = [
            'password' => 'required|min_length[6]',
            'confpassword' => 'matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $userId = session()->get('user_id');
        $model->update($userId, [
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ]);

        return redirect()->to(base_url('auth/profile'))->with('success', 'Password updated successfully!');
    }
}
