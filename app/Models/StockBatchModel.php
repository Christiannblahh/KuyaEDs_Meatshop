<?php

namespace App\Models;

use CodeIgniter\Model;

class StockBatchModel extends Model
{
    protected $table         = 'stock_batches';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'product_id',
        'quantity',
        'remaining_quantity',
        'cost_price',
        'expiry_date',
    ];

    public $useTimestamps = false;

    /**
     * Reduce stock using FIFO batches.
     * Returns true on success, false if not enough stock.
     */
    public function deductStock(int $productId, float $quantity): bool
    {
        $db = $this->db;
        $db->transStart();

        $remaining = $quantity;

        $batches = $this->where('product_id', $productId)
            ->where('remaining_quantity >', 0)
            ->orderBy('expiry_date IS NULL, expiry_date ASC, id ASC')
            ->lockForUpdate()
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
}


