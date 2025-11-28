<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

class ProductModel extends Model
{
    protected $table         = 'products';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'name',
        'category',
        'unit',
        'unit_price',
        'low_stock_threshold',
        'description',
        'image_url',
    ];

    protected $useTimestamps = true;

    /**
     * Get list of products with current total stock.
     */
    public function withStock(): array
    {
        $builder = $this->select('products.*, COALESCE(SUM(stock_batches.remaining_quantity), 0) AS total_stock')
            ->join('stock_batches', 'stock_batches.product_id = products.id', 'left')
            ->groupBy('products.id');

        return $builder->findAll();
    }

    /**
     * Products currently below their low_stock_threshold.
     */
    public function lowStock(): array
    {
        $builder = $this->select('products.*, COALESCE(SUM(stock_batches.remaining_quantity), 0) AS total_stock')
            ->join('stock_batches', 'stock_batches.product_id = products.id', 'left')
            ->groupBy('products.id')
            ->having('total_stock < low_stock_threshold')
            ->having('low_stock_threshold >', new RawSql('0'));

        return $builder->findAll();
    }
}


