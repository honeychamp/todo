<?php

namespace App\Models;

use CodeIgniter\Model;

class ExpenseModel extends Model
{
    protected $table         = 'expenses';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['title', 'amount', 'category', 'expense_date'];
    protected $useTimestamps = false;
}
