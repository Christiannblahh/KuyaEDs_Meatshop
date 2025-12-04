<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomerPaymentFieldsToSales extends Migration
{
    public function up()
    {
        // Add customer_payment and change_amount columns to sales table
        $this->forge->addColumn('sales', [
            'customer_payment' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Amount paid by customer',
            ],
            'change_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Change given to customer',
            ],
        ]);
    }

    public function down()
    {
        // Remove the columns if needed
        $this->forge->dropColumn('sales', ['customer_payment', 'change_amount']);
    }
}