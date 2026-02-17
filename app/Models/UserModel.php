<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Table name for staff/users
    protected $table            = 'users';
    // Primary key
    protected $primaryKey       = 'id';
    // User login data fields
    protected $allowedFields    = ['username', 'email', 'password'];
    // Automatically manage created_at and updated_at
    protected $useTimestamps    = true;
}
