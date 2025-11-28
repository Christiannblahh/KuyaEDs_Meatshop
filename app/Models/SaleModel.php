<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table         = 'sales';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'sale_date',
        'customer_name',
        'payment_method',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
    ];

    public $useTimestamps = false;

    /**
     * Get aggregated sales between dates, grouped by day.
     */
    public function daily(string $startDate, string $endDate): array
    {
        return $this->select('DATE(sale_date) AS date, SUM(total_amount) AS total, COUNT(*) AS transaction_count')
            ->where('sale_date >=', $startDate . ' 00:00:00')
            ->where('sale_date <=', $endDate . ' 23:59:59')
            ->groupBy('DATE(sale_date)')
            ->orderBy('DATE(sale_date)', 'ASC')
            ->findAll();
    }

    /**
     * Get aggregated sales per month for a given year.
     */
    public function monthly(int $year): array
    {
        return $this->select('DATE_FORMAT(sale_date, "%Y-%m") AS month, SUM(total_amount) AS total, COUNT(*) AS transaction_count')
            ->where('YEAR(sale_date)', $year)
            ->groupBy('DATE_FORMAT(sale_date, "%Y-%m")')
            ->orderBy('month', 'ASC')
            ->findAll();
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
}
