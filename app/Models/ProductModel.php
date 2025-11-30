<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\RawSql;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey        = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'category',
        'unit',
        'unit_price',
        'low_stock_threshold',
        'image_url',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'                => 'required|max_length[100]',
        'category'            => 'permit_empty|max_length[50]',
        'unit'                => 'required|max_length[20]',
        'unit_price'          => 'required|decimal|greater_than[0]',
        'low_stock_threshold' => 'permit_empty|decimal|greater_than_equal_to[0]',
        'image_url'           => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Product name is required.',
            'max_length' => 'Product name cannot exceed 100 characters.',
        ],
        'unit_price' => [
            'required' => 'Unit price is required.',
            'decimal' => 'Unit price must be a valid decimal number.',
            'greater_than' => 'Unit price must be greater than 0.',
        ],
        'unit' => [
            'required' => 'Unit is required.',
            'max_length' => 'Unit cannot exceed 20 characters.',
        ],
        'low_stock_threshold' => [
            'decimal' => 'Low stock threshold must be a valid decimal number.',
            'greater_than_equal_to' => 'Low stock threshold must be greater than or equal to 0.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Data casting
    protected array $casts = [
        'id'                 => 'int',
        'unit_price'         => 'float',
        'low_stock_threshold' => 'float',
    ];

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

    /**
     * Get product with stock batches
     */
    public function withBatches(int $productId): ?array
    {
        $product = $this->find($productId);
        if (!$product) {
            return null;
        }

        $stockBatchModel = new StockBatchModel();
        $product['batches'] = $stockBatchModel->where('product_id', $productId)
            ->orderBy('expiry_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        return $product;
    }

    /**
     * Get product with current stock total
     */
    public function withStockTotal(int $productId): ?array
    {
        $product = $this->find($productId);
        if (!$product) {
            return null;
        }

        $stockBatchModel = new StockBatchModel();
        $totalStock = $stockBatchModel->selectSum('remaining_quantity')
            ->where('product_id', $productId)
            ->first();

        $product['total_stock'] = (float) ($totalStock['remaining_quantity'] ?? 0);

        return $product;
    }
}


