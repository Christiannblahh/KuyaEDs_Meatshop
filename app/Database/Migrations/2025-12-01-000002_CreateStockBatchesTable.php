<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockBatchesTable extends Migration
{
    public function up(): void
    {
        // Drop table if exists
        $db = \Config\Database::connect();
        if ($db->tableExists('stock_batches')) {
            $this->forge->dropTable('stock_batches', true);
        }

        // Stock batches table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'product_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'remaining_quantity' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'null'       => false,
            ],
            'cost_price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'expiry_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('product_id');
        $this->forge->addKey('expiry_date');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stock_batches', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('stock_batches', true);
    }
}

