<?php

namespace App\Database\Seeds;

use App\Models\ProductModel;
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
            ],
            [
                'name'       => 'Chicken Wings',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 80,
            ],
            [
                'name'       => 'Chicken Feet',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 70,
            ],
            [
                'name'       => 'Baticolon',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 60,
            ],
            [
                'name'       => 'Whole Chicken',
                'category'   => 'Chicken',
                'unit'       => 'kg',
                'unit_price' => 180,
            ],
            [
                'name'       => 'Chorizo',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 120,
            ],
            [
                'name'       => 'Kikiam',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 80,
            ],
            [
                'name'       => 'Hotdog',
                'category'   => 'Processed',
                'unit'       => 'pack',
                'unit_price' => 130,
            ],
        ];

        $model = new ProductModel();

        foreach ($products as $product) {
            $model->insert($product);
        }
    }
}


