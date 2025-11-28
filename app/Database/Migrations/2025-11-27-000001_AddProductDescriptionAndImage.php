<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProductDescriptionAndImage extends Migration
{
    public function up(): void
    {
        $fields = [
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Product description',
            ],
            'image_url' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'URL or path to product image',
            ],
        ];

        $this->forge->addColumn('products', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('products', ['description', 'image_url']);
    }
}

