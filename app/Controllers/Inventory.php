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
     * Add stock to a product
     */
    public function addStock()
    {
        if ($this->request->getMethod() === 'post') {
            $productId  = (int) $this->request->getPost('product_id');
            $quantity   = (float) $this->request->getPost('quantity');
            $expiryDate = $this->request->getPost('expiry_date');
            $expiryDate = (!empty($expiryDate)) ? $expiryDate : null;

            // Validation
            if ($productId <= 0) {
                return redirect()->back()->with('error', 'Please select a product.');
            }

            if ($quantity <= 0) {
                return redirect()->back()->with('error', 'Quantity must be greater than 0.');
            }

            // Verify product exists
            $product = $this->products->find($productId);
            if (!$product) {
                return redirect()->back()->with('error', 'Product not found.');
            }

            // Prepare data for insertion
            $batchData = [
                'product_id'         => $productId,
                'quantity'           => $quantity,
                'remaining_quantity' => $quantity,
                'created_at'         => date('Y-m-d H:i:s'),
            ];

            // Only add expiry_date if provided
            if ($expiryDate !== null && !empty($expiryDate)) {
                $batchData['expiry_date'] = $expiryDate;
            }

            // Create stock batch
            try {
                $insertId = $this->batches->insert($batchData);
                
                // Check for errors
                $errors = $this->batches->errors();
                if (!empty($errors)) {
                    $errorMessage = implode(', ', $errors);
                    return redirect()->back()->with('error', 'Error: ' . $errorMessage);
                }
                
                // Verify insertion was successful
                if ($insertId === false) {
                    return redirect()->back()->with('error', 'Failed to add stock. Please try again.');
                }

                return redirect()->to('/inventory')->with('success', 
                    "Successfully added {$quantity} {$product['unit']} of {$product['name']} to inventory.");
            } catch (\Exception $e) {
                log_message('error', 'Stock addition error: ' . $e->getMessage());
                return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
            }
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

