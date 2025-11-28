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

        // Calculate total stock value
        $totalStockValue = 0;
        foreach ($productsWithStock as $product) {
            $totalStockValue += ($product['total_stock'] * $product['unit_price']);
        }

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
            'totalStockValue' => $totalStockValue,
            'todaysSales' => $todaysSales,
            'totalSalesAllTime' => $totalSalesAllTime,
        ];

        $content = view('dashboard', $data);

        return view('layout', [
            'title'   => 'Kuya EDs Meatshop',
            'content' => $content,
        ]);
    }
}
