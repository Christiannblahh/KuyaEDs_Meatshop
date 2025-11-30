<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'sale_date',
        'customer_name',
        'payment_method',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'created_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'sale_date'     => 'required|valid_date',
        'customer_name' => 'permit_empty|max_length[100]',
        'payment_method' => 'required|max_length[50]|in_list[cash,card,check,online,digital,other]',
        'subtotal'      => 'required|decimal|greater_than_equal_to[0]',
        'discount'      => 'required|decimal|greater_than_equal_to[0]',
        'tax'           => 'required|decimal|greater_than_equal_to[0]',
        'total_amount'  => 'required|decimal|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'sale_date' => [
            'required' => 'Sale date is required.',
            'valid_date' => 'Sale date must be a valid date.',
        ],
        'payment_method' => [
            'required' => 'Payment method is required.',
            'in_list' => 'Payment method must be one of: cash, card, digital, other.',
        ],
        'subtotal' => [
            'required' => 'Subtotal is required.',
            'decimal' => 'Subtotal must be a valid decimal number.',
            'greater_than_equal_to' => 'Subtotal cannot be negative.',
        ],
        'total_amount' => [
            'required' => 'Total amount is required.',
            'decimal' => 'Total amount must be a valid decimal number.',
            'greater_than_equal_to' => 'Total amount cannot be negative.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Data casting
    protected array $casts = [
        'id'          => 'int',
        'subtotal'    => 'float',
        'discount'    => 'float',
        'tax'         => 'float',
        'total_amount' => 'float',
    ];

    /**
     * Get aggregated sales between dates, grouped by day.
     */
    public function daily(string $startDate, string $endDate): array
    {
        $results = $this->select('DATE(sale_date) AS date, SUM(total_amount) AS total, COUNT(*) AS transaction_count')
            ->where('sale_date >=', $startDate . ' 00:00:00')
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('DATE(sale_date)')
            ->orderBy('DATE(sale_date)', 'ASC')
            ->findAll();
        
        // Ensure total is a float
        foreach ($results as &$result) {
            $result['total'] = (float) ($result['total'] ?? 0);
            $result['transaction_count'] = (int) ($result['transaction_count'] ?? 0);
        }
        
        return $results;
    }

    /**
     * Get aggregated sales per month for a given year.
     */
    public function monthly(int $year): array
    {
        $results = $this->select('DATE_FORMAT(sale_date, "%Y-%m") AS month, SUM(total_amount) AS total, COUNT(*) AS transaction_count')
            ->where('YEAR(sale_date)', $year)
            ->groupBy('DATE_FORMAT(sale_date, "%Y-%m")')
            ->orderBy('month', 'ASC')
            ->findAll();
        
        // Ensure total is a float
        foreach ($results as &$result) {
            $result['total'] = (float) ($result['total'] ?? 0);
            $result['transaction_count'] = (int) ($result['transaction_count'] ?? 0);
        }
        
        return $results;
    }

    /**
     * Get sales by payment method
     */
    public function byPaymentMethod(): array
    {
        return $this->select('payment_method, COUNT(*) AS count, SUM(total_amount) AS total')
            ->groupBy('payment_method')
            ->findAll();
    }

    /**
     * Get recent sales
     */
    public function recent(int $limit = 10): array
    {
        return $this->orderBy('sale_date', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get sale with its items
     */
    public function withItems(int $saleId): ?array
    {
        $sale = $this->find($saleId);
        if (!$sale) {
            return null;
        }

        $saleItemModel = new SaleItemModel();
        $sale['items'] = $saleItemModel->where('sale_id', $saleId)->findAll();

        return $sale;
    }

    /**
     * Get all sales with their items
     */
    public function allWithItems(): array
    {
        $sales = $this->orderBy('sale_date', 'DESC')->findAll();
        $saleItemModel = new SaleItemModel();

        foreach ($sales as &$sale) {
            $sale['items'] = $saleItemModel->where('sale_id', $sale['id'])->findAll();
        }

        return $sales;
    }
}
