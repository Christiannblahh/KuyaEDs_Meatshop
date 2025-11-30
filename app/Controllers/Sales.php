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
            // Check if this is an AJAX request
            $isAjax = $this->request->isAJAX() || 
                      $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest';
            
            // Set JSON response type for all POST requests (we're using AJAX now)
            $this->response->setContentType('application/json');
            
            log_message('info', '=== SALE RECORDING STARTED ===');
            log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
            log_message('info', 'Is AJAX: ' . ($isAjax ?? 'yes'));
            
            $customerName = trim($this->request->getPost('customer_name') ?? '');
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
                log_message('error', 'Sale recording - No products added');
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please add at least one product with quantity greater than 0.'
                ]);
            }

            // Apply discount and tax
            $subtotal = $totalAmount;
            $totalAmount = $totalAmount - $discount + $tax;

            $db = db_connect();
            $db->transStart();

            try {
                // Skip model validation since we already validated
                $this->sales->skipValidation(true);
                
                $saleId = $this->sales->insert([
                    'sale_date'      => date('Y-m-d H:i:s'),
                    'customer_name'  => !empty($customerName) ? $customerName : null,
                    'payment_method' => $paymentMethod,
                    'subtotal'       => $subtotal,
                    'discount'       => $discount,
                    'tax'            => $tax,
                    'total_amount'   => $totalAmount,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                
                // Re-enable validation
                $this->sales->skipValidation(false);
                
                // Check for errors
                $errors = $this->sales->errors();
                if (!empty($errors)) {
                    $db->transRollback();
                    $errorMsg = implode(', ', $errors);
                    log_message('error', 'Sale creation errors: ' . $errorMsg);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Error saving sale: ' . $errorMsg
                    ]);
                }
                
                if (!$saleId || $saleId === false) {
                    $db->transRollback();
                    log_message('error', 'Sale creation failed - no insert ID');
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to save sale. Please try again.'
                    ]);
                }

                foreach ($lines as $line) {
                    // Check stock before deducting
                    $stock = $this->getProductStock($line['product_id']);
                    if ($line['quantity'] > $stock) {
                        $db->transRollback();
                        $product = $this->products->find($line['product_id']);
                        $errorMsg = "Not enough stock for {$product['name']}. Available: {$stock} {$product['unit']}";
                        log_message('error', 'Sale recording - ' . $errorMsg);
                        $this->response->setContentType('application/json');
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => $errorMsg
                        ]);
                    }
                    
                    // Deduct stock
                    if (!$this->batches->deductStock($line['product_id'], $line['quantity'])) {
                        $db->transRollback();
                        log_message('error', 'Sale recording - Stock deduction failed for product ID: ' . $line['product_id']);
                        $this->response->setContentType('application/json');
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Not enough stock for one of the products.'
                        ]);
                    }

                    $line['sale_id'] = $saleId;
                    
                    // Skip model validation for sale items
                    $this->items->skipValidation(true);
                    $this->items->insert($line);
                    $this->items->skipValidation(false);
                    
                    // Check for errors
                    $itemErrors = $this->items->errors();
                    if (!empty($itemErrors)) {
                        $db->transRollback();
                        $errorMsg = implode(', ', $itemErrors);
                        log_message('error', 'Sale item creation errors: ' . $errorMsg);
                        $this->response->setContentType('application/json');
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Error saving sale items: ' . $errorMsg
                        ]);
                    }
                }

                $db->transComplete();

                if (! $db->transStatus()) {
                    log_message('error', 'Sale recording - Transaction failed');
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Could not save sale. Please try again.'
                    ]);
                }

                log_message('info', 'Sale recorded successfully - ID: ' . $saleId . ', Total: ' . $totalAmount);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sale recorded successfully! Total: ₱' . number_format($totalAmount, 2),
                    'sale_id' => $saleId,
                    'total_amount' => $totalAmount
                ]);
                
            } catch (\Exception $e) {
                $db->transRollback();
                log_message('error', 'Sale recording exception: ' . $e->getMessage());
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'An error occurred: ' . $e->getMessage()
                ]);
            }
        }

        $data['products'] = $this->products->withStock();

        return view('layout', [
            'title'   => 'Record Sale - Kuya EDs',
            'content' => view('sales/create', $data),
        ]);
    }

    /**
     * Get current stock for a product
     */
    private function getProductStock(int $productId): float
    {
        $products = $this->products->withStock();
        foreach ($products as $p) {
            if ($p['id'] == $productId) {
                return (float) ($p['total_stock'] ?? 0);
            }
        }
        return 0;
    }
}
