<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table         = 'sales';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'sale_date',
        'total_amount',
    ];

    public $useTimestamps = false;

    /**
     * Get aggregated sales between dates, grouped by day.
     */
    public function daily(string $startDate, string $endDate): array
    {
        return $this->select('DATE(sale_date) AS date, SUM(total_amount) AS total')
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
        return $this->select('DATE_FORMAT(sale_date, "%Y-%m") AS month, SUM(total_amount) AS total')
            ->where('YEAR(sale_date)', $year)
            ->groupBy('DATE_FORMAT(sale_date, "%Y-%m")')
            ->orderBy('month', 'ASC')
            ->findAll();
    }
}


