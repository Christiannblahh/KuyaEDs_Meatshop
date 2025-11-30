<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table            = 'sale_items';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'sale_id'    => 'required|integer|is_natural_no_zero',
        'product_id' => 'required|integer|is_natural_no_zero',
        'quantity'   => 'required|decimal|greater_than[0]',
        'unit_price' => 'required|decimal|greater_than_equal_to[0]',
        'line_total' => 'required|decimal|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'sale_id' => [
            'required' => 'Sale ID is required.',
            'integer' => 'Sale ID must be a valid integer.',
            'is_natural_no_zero' => 'Sale ID must be a positive integer.',
        ],
        'product_id' => [
            'required' => 'Product ID is required.',
            'integer' => 'Product ID must be a valid integer.',
            'is_natural_no_zero' => 'Product ID must be a positive integer.',
        ],
        'quantity' => [
            'required' => 'Quantity is required.',
            'decimal' => 'Quantity must be a valid decimal number.',
            'greater_than' => 'Quantity must be greater than 0.',
        ],
        'unit_price' => [
            'required' => 'Unit price is required.',
            'decimal' => 'Unit price must be a valid decimal number.',
            'greater_than_equal_to' => 'Unit price cannot be negative.',
        ],
        'line_total' => [
            'required' => 'Line total is required.',
            'decimal' => 'Line total must be a valid decimal number.',
            'greater_than_equal_to' => 'Line total cannot be negative.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Data casting
    protected array $casts = [
        'id'         => 'int',
        'sale_id'    => 'int',
        'product_id' => 'int',
        'quantity'   => 'float',
        'unit_price' => 'float',
        'line_total' => 'float',
    ];

    /**
     * Get sale items with product information
     */
    public function withProducts(int $saleId): array
    {
        return $this->select('sale_items.*, products.name AS product_name, products.category, products.unit')
            ->join('products', 'products.id = sale_items.product_id')
            ->where('sale_items.sale_id', $saleId)
            ->findAll();
    }

    /**
     * Get all items for a sale
     */
    public function bySale(int $saleId): array
    {
        return $this->where('sale_id', $saleId)->findAll();
    }

    /**
     * Get items by product
     */
    public function byProduct(int $productId): array
    {
        return $this->where('product_id', $productId)->findAll();
    }
}


