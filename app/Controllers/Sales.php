<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\StockBatchModel;

class Sales extends BaseController
{
    protected ProductModel $products;
    protected SaleModel $sales;
    protected SaleItemModel $items;
    protected StockBatchModel $batches;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->sales    = new SaleModel();
        $this->items    = new SaleItemModel();
        $this->batches  = new StockBatchModel();
    }

    public function create()
    {
        if ($this->request->getMethod() === 'post') {
            $customerName = $this->request->getPost('customer_name');
            $paymentMethod = $this->request->getPost('payment_method') ?? 'cash';
            $discount = (float) ($this->request->getPost('discount') ?? 0);
            $tax = (float) ($this->request->getPost('tax') ?? 0);
            
            $productIds = (array) $this->request->getPost('product_id');
            $quantities = (array) $this->request->getPost('quantity');

            $totalAmount = 0;
            $lines       = [];

            foreach ($productIds as $index => $pid) {
                if (!$pid) {
                    continue;
                }

                $qty = (float) ($quantities[$index] ?? 0);
                if ($qty <= 0) {
                    continue;
                }

                $product   = $this->products->find($pid);
                if (!$product) {
                    continue;
                }
                
                $unitPrice = (float) $product['unit_price'];
                $lineTotal = $unitPrice * $qty;

                $totalAmount += $lineTotal;

                $lines[] = [
                    'product_id' => (int) $pid,
                    'quantity'   => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => $lineTotal,
                ];
            }

            if (empty($lines)) {
                return redirect()->back()->with('error', 'Please add at least one product.');
            }

            // Apply discount and tax
            $subtotal = $totalAmount;
            $totalAmount = $totalAmount - $discount + $tax;

            $db = db_connect();
            $db->transStart();

            $saleId = $this->sales->insert([
                'sale_date'      => date('Y-m-d H:i:s'),
                'customer_name'  => $customerName,
                'payment_method' => $paymentMethod,
                'subtotal'       => $subtotal,
                'discount'       => $discount,
                'tax'            => $tax,
                'total_amount'   => $totalAmount,
            ]);

            foreach ($lines as $line) {
                if (!$this->batches->deductStock($line['product_id'], $line['quantity'])) {
                    $db->transRollback();
                    return redirect()->back()->with('error', 'Not enough stock for one of the products.');
                }

                $line['sale_id'] = $saleId;
                $this->items->insert($line);
            }

            $db->transComplete();

            if (! $db->transStatus()) {
                return redirect()->back()->with('error', 'Could not save sale.');
            }

            return redirect()->to('/sales/create')->with('success', 'Sale recorded successfully! Total: ₱' . number_format($totalAmount, 2));
        }

        $data['products'] = $this->products->withStock();

        return view('layout', [
            'title'   => 'Record Sale - Kuya EDs',
            'content' => view('sales/create', $data),
        ]);
    }
}
