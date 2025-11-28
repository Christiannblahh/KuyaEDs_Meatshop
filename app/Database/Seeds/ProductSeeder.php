<?php

namespace App\Database\Seeds;

use App\Models\ProductModel;
use App\Models\StockBatchModel;
use CodeIgniter\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name'       => 'Breast Chicken',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 130,
                'low_stock_threshold' => 5,
                'stock' => 50,
            ],
            [
                'name'       => 'Chicken Wings',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 80,
                'low_stock_threshold' => 3,
                'stock' => 2,
            ],
            [
                'name'       => 'Chicken Feet',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 70,
                'low_stock_threshold' => 4,
                'stock' => 0,
            ],
            [
                'name'       => 'Baticolon',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 60,
                'low_stock_threshold' => 5,
                'stock' => 30,
            ],
            [
                'name'       => 'Whole Chicken',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 180,
                'low_stock_threshold' => 3,
                'stock' => 15,
            ],
            [
                'name'       => 'Chorizo',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 120,
                'low_stock_threshold' => 5,
                'stock' => 1,
            ],
            [
                'name'       => 'Kikiam',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 80,
                'low_stock_threshold' => 3,
                'stock' => 20,
            ],
            [
                'name'       => 'Hotdog',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 130,
                'low_stock_threshold' => 4,
                'stock' => 0,
            ],
        ];

        $productModel = new ProductModel();
        $stockModel = new StockBatchModel();

        foreach ($products as $product) {
            $stock = $product['stock'];
            unset($product['stock']);

            $productId = $productModel->insert($product);

            if ($stock > 0) {
                $expiryDate = date('Y-m-d', strtotime('+' . rand(2, 5) . ' days'));
                
                $stockModel->insert([
                    'product_id' => $productId,
                    'quantity' => $stock,
                    'remaining_quantity' => $stock,
                    'expiry_date' => $expiryDate,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}
