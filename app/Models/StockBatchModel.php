<?php

namespace App\Models;

use CodeIgniter\Model;

class StockBatchModel extends Model
{
    protected $table            = 'stock_batches';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'quantity',
        'remaining_quantity',
        'cost_price',
        'expiry_date',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'product_id'        => 'required|integer|is_natural_no_zero',
        'quantity'          => 'required|decimal|greater_than[0]',
        'remaining_quantity' => 'required|decimal|greater_than_equal_to[0]',
        'cost_price'        => 'permit_empty|decimal|greater_than_equal_to[0]',
        'expiry_date'       => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
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
        'remaining_quantity' => [
            'required' => 'Remaining quantity is required.',
            'decimal' => 'Remaining quantity must be a valid decimal number.',
            'greater_than_equal_to' => 'Remaining quantity cannot be negative.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Data casting
    // Note: cost_price is not included in casts because it can be NULL
    protected array $casts = [
        'id'                => 'int',
        'product_id'        => 'int',
        'quantity'          => 'float',
        'remaining_quantity' => 'float',
    ];

    /**
     * Reduce stock using FIFO batches.
     * Returns true on success, false if not enough stock.
     */
    public function deductStock(int $productId, float $quantity): bool
    {
        $db = $this->db;
        $db->transStart();

        $remaining = $quantity;

        // Use raw query builder for complex ordering
        $batches = $this->where('product_id', $productId)
            ->where('remaining_quantity >', 0)
            ->orderBy('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END', 'ASC', false)
            ->orderBy('expiry_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();

        // Calculate total available
        $totalAvailable = 0;
        foreach ($batches as $batch) {
            $totalAvailable += (float) $batch['remaining_quantity'];
        }

        if ($totalAvailable < $quantity) {
            $db->transRollback();
            return false;
        }

        foreach ($batches as $batch) {
            if ($remaining <= 0) {
                break;
            }

            $take = min($remaining, (float) $batch['remaining_quantity']);

            $this->update($batch['id'], [
                'remaining_quantity' => (float) $batch['remaining_quantity'] - $take,
            ]);

            $remaining -= $take;
        }

        $db->transComplete();

        return $db->transStatus();
    }

    /**
     * Batches that are expired or near expiry.
     */
    public function expiringSoon(int $days = 3): array
    {
        $today     = date('Y-m-d');
        $threshold = date('Y-m-d', strtotime("+{$days} days"));

        return $this->select('stock_batches.*, products.name AS product_name')
            ->join('products', 'products.id = stock_batches.product_id')
            ->where('remaining_quantity >', 0)
            ->where('expiry_date IS NOT NULL')
            ->where('expiry_date <=', $threshold)
            ->orderBy('expiry_date ASC')
            ->findAll();
    }

    /**
     * Get batch with product information
     */
    public function withProduct(int $batchId): ?array
    {
        $batch = $this->find($batchId);
        if (!$batch) {
            return null;
        }

        $productModel = new ProductModel();
        $batch['product'] = $productModel->find($batch['product_id']);

        return $batch;
    }

    /**
     * Get all batches for a product
     */
    public function byProduct(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->orderBy('expiry_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    /**
     * Get batches with remaining stock
     */
    public function withRemainingStock(int $productId): array
    {
        return $this->where('product_id', $productId)
            ->where('remaining_quantity >', 0)
            ->orderBy('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END', 'ASC', false)
            ->orderBy('expiry_date', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}


