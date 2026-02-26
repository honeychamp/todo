<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixSchema extends Migration
{
    public function up()
    {
        // 1. Add initial_qty to stock_purchase if missing
        if (!$this->db->fieldExists('initial_qty', 'stock_purchase')) {
            $this->forge->addColumn('stock_purchase', [
                'initial_qty' => ['type' => 'INT', 'constraint' => 11, 'after' => 'product_id', 'default' => 0],
            ]);
            // Set initial_qty = qty for existing records
            $this->db->query("UPDATE stock_purchase SET initial_qty = qty");
        }

        // 2. Add customer info to sales if missing
        if (!$this->db->fieldExists('customer_name', 'sales')) {
            $this->forge->addColumn('sales', [
                'customer_name'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'sale_price'],
                'customer_phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'customer_name'],
            ]);
        }

        // 3. Create Expenses table if missing
        if (!$this->db->tableExists('expenses')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title'        => ['type' => 'VARCHAR', 'constraint' => 255],
                'amount'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'expense_date' => ['type' => 'DATE'],
                'category'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'notes'        => ['type' => 'TEXT', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('expenses');
        }

        // 4. Create Vendor Payments table if missing
        if (!$this->db->tableExists('vendor_payments')) {
            $this->forge->addField([
                'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'vendor_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'amount'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
                'payment_date' => ['type' => 'DATE'],
                'notes'        => ['type' => 'TEXT', 'null' => true],
                'created_at'   => ['type' => 'DATETIME', 'null' => true],
                'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addForeignKey('vendor_id', 'vendors', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('vendor_payments');
        }

        // 5. Create Settings table if missing
        if (!$this->db->tableExists('settings')) {
            $this->forge->addField([
                'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'setting_key'   => ['type' => 'VARCHAR', 'constraint' => 100],
                'setting_value' => ['type' => 'TEXT'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('setting_key');
            $this->forge->createTable('settings');
            
            // Default settings
            $this->db->table('settings')->insertBatch([
                ['setting_key' => 'pharmacy_name', 'setting_value' => 'Galaxy Pharmacy'],
                ['setting_key' => 'pharmacy_phone', 'setting_value' => '+92 300 0000000'],
                ['setting_key' => 'currency_symbol', 'setting_value' => 'Rs.'],
            ]);
        }
    }

    public function down()
    {
        // No down for fix-it migrations to prevent data loss
    }
}
