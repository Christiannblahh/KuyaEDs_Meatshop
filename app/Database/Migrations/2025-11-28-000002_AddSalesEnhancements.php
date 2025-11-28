<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSalesEnhancements extends Migration
{
    public function up(): void
    {
        // Add new columns to sales table
        $fields = [
            'customer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sale_date',
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'cash',
                'after'      => 'customer_name',
            ],
            'subtotal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'payment_method',
            ],
            'discount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'subtotal',
            ],
            'tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0,
                'after'      => 'discount',
            ],
        ];

        $this->forge->addColumn('sales', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('sales', ['customer_name', 'payment_method', 'subtotal', 'discount', 'tax']);
    }
}
