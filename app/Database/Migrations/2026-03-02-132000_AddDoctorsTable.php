<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDoctorsTable extends Migration
{
    public function up()
    {
        // 1. Create Doctors table
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'     => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('doctors');

        // 2. Add doctor_id to sales
        $this->forge->addColumn('sales', [
            'doctor_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'after' => 'product_id'],
        ]);
        // Add Foreign key constraint
        $this->db->query("ALTER TABLE sales ADD CONSTRAINT sales_doctor_id_fk FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE SET NULL ON UPDATE CASCADE");

        // 3. Create Doctor Payments table
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'doctor_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'amount'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'payment_date' => ['type' => 'DATE'],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Cash'],
            'notes'        => ['type' => 'TEXT', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('doctor_id', 'doctors', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('doctor_payments');
    }

    public function down()
    {
        $this->forge->dropTable('doctor_payments');
        $this->db->query("ALTER TABLE sales DROP FOREIGN KEY sales_doctor_id_fk");
        $this->forge->dropColumn('sales', 'doctor_id');
        $this->forge->dropTable('doctors');
    }
}
