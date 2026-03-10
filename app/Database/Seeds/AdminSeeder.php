<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();

        // Create or Update admin
        $admin = $userModel->where('username', 'admin')->first();
        $baseData = [
            'username' => 'admin',
            'email'    => 'admin@galaxy.com',
            'password' => password_hash('admin', PASSWORD_DEFAULT)
        ];

        if ($admin) {
            $userModel->update($admin['id'], $baseData);
        } else {
            $userModel->save($baseData);
        }
    }
}
