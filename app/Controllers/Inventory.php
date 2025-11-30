<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\StockBatchModel;

class Inventory extends BaseController
{
    protected ProductModel $products;
    protected StockBatchModel $batches;

    public function __construct()
    {
        $this->products = new ProductModel();
        $this->batches  = new StockBatchModel();
    }

    /**
     * Display inventory list
     */
    public function index(): string
    {
        $data['products'] = $this->products->withStock();

        return view('layout', [
            'title'   => 'Inventory - Kuya EDs',
            'content' => view('inventory/index', $data),
        ]);
    }

    /**
     * Quick add stock via table interface
     */
    public function quickAdd()
    {
        // Check if this is an AJAX/JSON request
        $isAjax = $this->request->isAJAX() || 
                  $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                  $this->request->getMethod() === 'post';
        
        if ($this->request->getMethod() === 'post' || $isAjax) {
            // Set JSON response type
            $this->response->setContentType('application/json');
            
            // Log incoming request for debugging
            log_message('info', 'Quick Add Stock - POST data: ' . json_encode($this->request->getPost()));
            log_message('info', 'Quick Add Stock - Is AJAX: ' . ($isAjax ? 'yes' : 'no'));
            log_message('info', 'Quick Add Stock - Method: ' . $this->request->getMethod());
            
            $productId = (int) $this->request->getPost('product_id');
            $quantity = (float) $this->request->getPost('quantity');
            $expiryDate = $this->request->getPost('expiry_date');
            
            // Validation
            if ($productId <= 0) {
                log_message('error', 'Quick Add Stock - Invalid product_id: ' . $productId);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select a valid product'
                ]);
            }
            
            if ($quantity <= 0) {
                log_message('error', 'Quick Add Stock - Invalid quantity: ' . $quantity);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quantity must be greater than 0'
                ]);
            }
            
            // Verify product exists
            $product = $this->products->find($productId);
            if (!$product) {
                log_message('error', 'Quick Add Stock - Product not found: ' . $productId);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }
            
            // Prepare batch data
            $batchData = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'remaining_quantity' => $quantity,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            
            if (!empty($expiryDate) && trim($expiryDate) !== '') {
                $batchData['expiry_date'] = trim($expiryDate);
            }
            
            // Insert stock batch
            try {
                log_message('info', 'Quick Add Stock - Attempting insert: ' . json_encode($batchData));
                
                $this->batches->skipValidation(true);
                $insertId = $this->batches->insert($batchData);
                $this->batches->skipValidation(false);
                
                // Check for model errors
                $errors = $this->batches->errors();
                if (!empty($errors)) {
                    $errorMsg = implode(', ', $errors);
                    log_message('error', 'Quick Add Stock - Model errors: ' . $errorMsg);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation error: ' . $errorMsg
                    ]);
                }
                
                if ($insertId && $insertId > 0) {
                    log_message('info', 'Quick Add Stock - Success! Insert ID: ' . $insertId);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Successfully added {$quantity} {$product['unit']} of {$product['name']}",
                        'insert_id' => $insertId
                    ]);
                } else {
                    // Get database error
                    $db = db_connect();
                    $error = $db->error();
                    $errorMsg = !empty($error['message']) ? $error['message'] : 'Failed to insert stock batch';
                    log_message('error', 'Quick Add Stock - Insert failed: ' . json_encode($error));
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Database error: ' . $errorMsg
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Quick Add Stock - Exception: ' . $e->getMessage());
                log_message('error', 'Quick Add Stock - Stack trace: ' . $e->getTraceAsString());
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        
        // If we reach here and it's a POST request, something went wrong
        if ($this->request->getMethod() === 'post') {
            $this->response->setContentType('application/json');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method or missing data'
            ]);
        }
        
        // Show table interface
        $data['products'] = $this->products->withStock();
        
        return view('layout', [
            'title' => 'Quick Add Stock - Kuya EDs',
            'content' => view('inventory/quick_add', $data),
        ]);
    }

    /**
     * Add stock to a product (Advanced form - now uses AJAX like Quick Add)
     */
    public function addStock()
    {
        // Check if this is an AJAX/JSON request
        $isAjax = $this->request->isAJAX() || 
                  $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                  $this->request->getMethod() === 'post';
        
        if ($this->request->getMethod() === 'post' || $isAjax) {
            // Set JSON response type
            $this->response->setContentType('application/json');
            
            // Get POST data - try multiple sources for product_id
            $productId  = $this->request->getPost('product_id');
            if (empty($productId)) {
                $productId = $this->request->getPost('product_id_select'); // Fallback
            }
            
            $quantity   = $this->request->getPost('quantity');
            $expiryDate = $this->request->getPost('expiry_date');

            // Log incoming request for debugging
            log_message('info', 'Add Stock - POST data: ' . json_encode($this->request->getPost()));
            log_message('info', 'Add Stock - Is AJAX: ' . ($isAjax ? 'yes' : 'no'));
            log_message('info', 'Add Stock - Method: ' . $this->request->getMethod());

            // Validation
            if (empty($productId) || trim($productId) === '' || (int)$productId <= 0) {
                log_message('error', 'Add Stock - Invalid product_id: ' . $productId);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please select a product'
                ]);
            }

            if (empty($quantity) || trim($quantity) === '' || (float)$quantity <= 0) {
                log_message('error', 'Add Stock - Invalid quantity: ' . $quantity);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Quantity must be greater than 0'
                ]);
            }

            $productId = (int) $productId;
            $quantity = (float) $quantity;
            
            // Process expiry date - convert to Y-m-d format if provided
            $expiryDateFormatted = null;
            if (!empty($expiryDate) && trim($expiryDate) !== '') {
                // Date input sends Y-m-d format, but let's ensure it's valid
                $dateParts = explode('-', trim($expiryDate));
                if (count($dateParts) === 3 && checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
                    $expiryDateFormatted = trim($expiryDate);
                } else {
                    log_message('warning', 'Add Stock - Invalid expiry date format: ' . $expiryDate);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Invalid expiry date format. Please use the date picker.'
                    ]);
                }
            }

            // Verify product exists
            $product = $this->products->find($productId);
            if (!$product) {
                log_message('error', 'Add Stock - Product not found: ' . $productId);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product not found'
                ]);
            }

            // Prepare data for insertion
            $batchData = [
                'product_id'         => $productId,
                'quantity'           => $quantity,
                'remaining_quantity' => $quantity,
                'created_at'         => date('Y-m-d H:i:s'),
            ];

            // Only add expiry_date if provided and valid
            if ($expiryDateFormatted !== null) {
                $batchData['expiry_date'] = $expiryDateFormatted;
            }

            // Insert stock batch
            try {
                log_message('info', 'Add Stock - Attempting insert: ' . json_encode($batchData));
                
                $this->batches->skipValidation(true);
                $insertId = $this->batches->insert($batchData);
                $this->batches->skipValidation(false);
                
                // Check for model errors
                $errors = $this->batches->errors();
                if (!empty($errors)) {
                    $errorMsg = implode(', ', $errors);
                    log_message('error', 'Add Stock - Model errors: ' . $errorMsg);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation error: ' . $errorMsg
                    ]);
                }
                
                if ($insertId && $insertId > 0) {
                    log_message('info', 'Add Stock - Success! Insert ID: ' . $insertId);
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "Successfully added {$quantity} {$product['unit']} of {$product['name']} to inventory.",
                        'insert_id' => $insertId
                    ]);
                } else {
                    // Get database error
                    $db = db_connect();
                    $error = $db->error();
                    $errorMsg = !empty($error['message']) ? $error['message'] : 'Failed to insert stock batch';
                    log_message('error', 'Add Stock - Insert failed: ' . json_encode($error));
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Database error: ' . $errorMsg
                    ]);
                }
            } catch (\Exception $e) {
                log_message('error', 'Add Stock - Exception: ' . $e->getMessage());
                log_message('error', 'Add Stock - Stack trace: ' . $e->getTraceAsString());
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        }
        
        // Fallback: If POST request but didn't return JSON above, return error
        if ($this->request->getMethod() === 'post') {
            $this->response->setContentType('application/json');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request or missing data'
            ]);
        }

        // Show form
        $data['products'] = $this->products->withStock();
        $data['selectedProductId'] = $this->request->getGet('product_id');

        return view('layout', [
            'title'   => 'Add Stock - Kuya EDs',
            'content' => view('inventory/add_stock', $data),
        ]);
    }
}

