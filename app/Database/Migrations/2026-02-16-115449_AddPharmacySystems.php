<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPharmacySystems extends Migration
{
    public function up()
    {
        // Categories
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');

        // Products
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'vendor'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'cost'        => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'reg_number'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('products');

        // Stock Purchase
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'batch_id'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'product_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'manufacture_date' => ['type' => 'DATE'],
            'expiry_date'      => ['type' => 'DATE'],
            'qty'              => ['type' => 'INT', 'constraint' => 11],
            'cost'             => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'price'            => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_purchase');

        // Sales
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'stock_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'qty'        => ['type' => 'INT', 'constraint' => 11],
            'sale_price' => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'sale_date'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('stock_id', 'stock_purchase', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('sales');
    }

    public function down()
    {
        $this->forge->dropTable('sales');
        $this->forge->dropTable('stock_purchase');
        $this->forge->dropTable('products');
        $this->forge->dropTable('categories');
    }
}
