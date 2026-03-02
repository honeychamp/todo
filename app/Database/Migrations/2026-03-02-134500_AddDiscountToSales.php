<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiscountToSales extends Migration
{
    public function up()
    {
        // 1. Add discount to sales
        if (!$this->db->fieldExists('discount', 'sales')) {
            $this->forge->addColumn('sales', [
                'discount' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00, 'after' => 'sale_price'],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('sales', 'discount');
    }
}
