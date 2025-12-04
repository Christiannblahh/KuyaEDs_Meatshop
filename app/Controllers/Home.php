<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\StockBatchModel;

class Home extends BaseController
{
    public function index(): string
    {
        $productModel = new ProductModel();
        $saleModel = new SaleModel();
        $batchModel = new StockBatchModel();

        // Get statistics
        $allProducts = $productModel->findAll();
        $productsWithStock = $productModel->withStock();
        $lowStockProducts = $productModel->lowStock();
        $expiringBatches = $batchModel->expiringSoon(7);

        // Calculate totals
        $totalProducts = count($allProducts);
        $lowStockCount = count($lowStockProducts);
        $expiringCount = count($expiringBatches);

        // Calculate weekly expenses (expenses from the past 7 days)
        $weeklyExpenses = $this->calculateWeeklyExpenses();

        // Get today's sales
        $todaysSales = 0;
        $todaysSalesData = $saleModel->daily(date('Y-m-d'), date('Y-m-d'));
        if (!empty($todaysSalesData)) {
            $todaysSales = $todaysSalesData[0]['total'] ?? 0;
        }

        // Get total sales all time
        $totalSalesAllTime = 0;
        $allSalesData = $saleModel->findAll();
        foreach ($allSalesData as $sale) {
            $totalSalesAllTime += $sale['total_amount'];
        }

        $data = [
            'totalProducts' => $totalProducts,
            'lowStockCount' => $lowStockCount,
            'expiringCount' => $expiringCount,
            'weeklyExpenses' => $weeklyExpenses,
            'todaysSales' => $todaysSales,
            'totalSalesAllTime' => $totalSalesAllTime,
        ];

        $content = view('dashboard', $data);

        return view('layout', [
            'title'   => 'Kuya EDs Meatshop',
            'content' => $content,
        ]);
    }

    /**
     * Calculate expenses from stock purchases in the past 7 days
     */
    private function calculateWeeklyExpenses(): float
    {
        $batchModel = new StockBatchModel();
        
        // Get stock batches from the past 7 days
        $sevenDaysAgo = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        $weeklyBatches = $batchModel->where('created_at >=', $sevenDaysAgo)
            ->where('cost_price >', 0)
            ->findAll();
        
        $totalExpenses = 0.0;
        
        foreach ($weeklyBatches as $batch) {
            $expense = (float) $batch['quantity'] * (float) $batch['cost_price'];
            $totalExpenses += $expense;
        }
        
        return $totalExpenses;
    }
}
