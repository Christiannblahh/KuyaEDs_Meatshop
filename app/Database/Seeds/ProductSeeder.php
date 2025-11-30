<?php

namespace App\Database\Seeds;

use App\Models\ProductModel;
use App\Models\StockBatchModel;
use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $productModel = new ProductModel();
        $stockModel = new StockBatchModel();

        $products = [
            [
                'name'               => 'Chicken Wings',
                'category'           => 'Chicken',
                'unit'               => 'kg',
                'unit_price'         => 80.00,
                'low_stock_threshold' => 3.00,
                'stock'              => 2.00,
            ],
            [
                'name'               => 'Chicken Feet',
                'category'           => 'Chicken',
                'unit'               => 'kg',
                'unit_price'         => 70.00,
                'low_stock_threshold' => 4.00,
                'stock'              => 0.00,
            ],
            [
                'name'               => 'Baticolon',
                'category'           => 'Chicken',
                'unit'               => 'kg',
                'unit_price'         => 60.00,
                'low_stock_threshold' => 5.00,
                'stock'              => 30.00,
            ],
            [
                'name'               => 'Whole Chicken',
                'category'           => 'Chicken',
                'unit'               => 'kg',
                'unit_price'         => 180.00,
                'low_stock_threshold' => 3.00,
                'stock'              => 15.00,
            ],
            [
                'name'               => 'Chorizo',
                'category'           => 'Processed',
                'unit'               => 'pack',
                'unit_price'         => 120.00,
                'low_stock_threshold' => 5.00,
                'stock'              => 1.00,
            ],
            [
                'name'               => 'Kikiam',
                'category'           => 'Processed',
                'unit'               => 'pack',
                'unit_price'         => 80.00,
                'low_stock_threshold' => 3.00,
                'stock'              => 20.00,
            ],
            [
                'name'               => 'Hotdog',
                'category'           => 'Processed',
                'unit'               => 'pack',
                'unit_price'         => 130.00,
                'low_stock_threshold' => 4.00,
                'stock'              => 0.00,
            ],
        ];

        foreach ($products as $productData) {
            $stock = $productData['stock'];
            unset($productData['stock']);

            // Insert product
            $productId = $productModel->insert($productData);

            if ($productId && $stock > 0) {
                // Add stock batch
                $expiryDate = date('Y-m-d', strtotime('+' . rand(2, 5) . ' days'));
                
                $stockModel->insert([
                    'product_id'         => $productId,
                    'quantity'           => $stock,
                    'remaining_quantity' => $stock,
                    'expiry_date'        => $expiryDate,
                    'created_at'         => date('Y-m-d H:i:s'),
                ]);
            }
        }

        echo "Products seeded successfully!\n";
    }
}

