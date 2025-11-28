<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleModel;
use App\Models\StockBatchModel;

class Reports extends BaseController
{
    protected SaleModel $sales;
    protected ProductModel $products;
    protected StockBatchModel $batches;

    public function __construct()
    {
        $this->sales    = new SaleModel();
        $this->products = new ProductModel();
        $this->batches  = new StockBatchModel();
    }

    public function sales()
    {
        $range = $this->request->getGet('range') ?? 'daily';

        $data['range'] = $range;

        if ($range === 'daily') {
            $start = $this->request->getGet('start') ?? date('Y-m-d');
            $end   = $this->request->getGet('end') ?? date('Y-m-d');

            $data['start'] = $start;
            $data['end']   = $end;
            $data['rows']  = $this->sales->daily($start, $end);
        } elseif ($range === 'monthly') {
            $year          = (int) ($this->request->getGet('year') ?? date('Y'));
            $data['year']  = $year;
            $data['rows']  = $this->sales->monthly($year);
        }

        return view('layout', [
            'title'   => 'Sales Reports - Kuya EDs',
            'content' => view('reports/sales', $data),
        ]);
    }

    public function alerts()
    {
        $data['lowStock']    = $this->products->lowStock();
        $data['expiringSoon'] = $this->batches->expiringSoon(7);

        return view('layout', [
            'title'   => 'Inventory Alerts - Kuya EDs',
            'content' => view('reports/alerts', $data),
        ]);
    }
}


