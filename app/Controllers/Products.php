<?php

namespace App\Controllers;

use App\Models\ProductModel;

class Products extends BaseController
{
    protected ProductModel $products;

    public function __construct()
    {
        $this->products = new ProductModel();
    }

    public function index(): string
    {
        $search = $this->request->getGet('search') ?? '';
        $category = $this->request->getGet('category') ?? '';
        
        $query = $this->products;
        
        if (!empty($search)) {
            $query = $query->like('name', $search);
        }
        
        if (!empty($category)) {
            $query = $query->where('category', $category);
        }
        
        $data['products'] = $query->withStock();
        $data['search'] = $search;
        $data['category'] = $category;
        
        // Get distinct categories
        $categoryResults = $this->products->distinct()->select('category')->where('category IS NOT NULL')->findAll();
        $data['categories'] = is_array($categoryResults) ? $categoryResults : [];

        return view('layout', [
            'title'   => 'Products - Kuya EDs',
            'content' => view('products/index', $data),
        ]);
    }

    public function create()
    {
        // Check if this is an AJAX/JSON request
        $isAjax = $this->request->isAJAX() || 
                  $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest' ||
                  $this->request->getMethod() === 'post';
        
        if ($this->request->getMethod() === 'post' || $isAjax) {
            // Set JSON response type
            $this->response->setContentType('application/json');
            
            // Log all POST data for debugging
            log_message('info', '=== PRODUCT CREATION STARTED ===');
            log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
            log_message('info', 'FILES data: ' . json_encode($this->request->getFiles()));
            log_message('info', 'Is AJAX: ' . ($isAjax ? 'yes' : 'no'));
            
            // Get and clean input data
            $name = trim($this->request->getPost('name') ?? '');
            $category = trim($this->request->getPost('category_custom') ?? '');
            if (empty($category)) {
                $category = trim($this->request->getPost('category') ?? '') ?: null;
            }
            
            // Get unit - check for custom unit first, then regular unit
            $unit = trim($this->request->getPost('unit_custom') ?? '');
            if (empty($unit)) {
                $unit = trim($this->request->getPost('unit') ?? '');
            }
            
            $unitPrice = $this->request->getPost('unit_price');
            $lowStockThreshold = $this->request->getPost('low_stock_threshold');
            
            // Basic validation
            $errors = [];
            
            if (empty($name) || strlen($name) < 3) {
                $errors['name'] = 'Product name is required and must be at least 3 characters.';
            }
            
            if (empty($unit)) {
                $errors['unit'] = 'Unit is required. Please select or enter a unit.';
            }
            
            if (empty($unitPrice) || (float)$unitPrice <= 0) {
                $errors['unit_price'] = 'Unit price is required and must be greater than 0.';
            }
            
            if (!empty($lowStockThreshold) && (float)$lowStockThreshold < 0) {
                $errors['low_stock_threshold'] = 'Low stock threshold cannot be negative.';
            }
            
            // Validate image if uploaded
            $file = $this->request->getFile('product_image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                if ($file->getSize() > 2 * 1024 * 1024) {
                    $errors['product_image'] = 'Image file size must be less than 2MB.';
                }
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    $errors['product_image'] = 'Image must be JPG, PNG, or GIF format.';
                }
            }
            
            // If there are errors, return JSON
            if (!empty($errors)) {
                log_message('error', 'Validation errors: ' . json_encode($errors));
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ]);
            }

            try {
                
                // Handle image upload
                $imageUrl = null;
                $file = $this->request->getFile('product_image');
                
                // Handle image upload
                $imageUrl = null;
                $file = $this->request->getFile('product_image');
                
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    // Create uploads directory if it doesn't exist
                    $uploadPath = WRITEPATH . 'uploads/products/';
                    if (!is_dir($uploadPath)) {
                        if (!mkdir($uploadPath, 0755, true)) {
                            log_message('error', 'Failed to create upload directory: ' . $uploadPath);
                            $this->response->setContentType('application/json');
                            return $this->response->setJSON([
                                'success' => false,
                                'message' => 'Failed to create upload directory. Please check permissions.'
                            ]);
                        }
                    }
                    
                    // Generate unique filename
                    $newName = $file->getRandomName();
                    if (!$file->move($uploadPath, $newName)) {
                        $errorMsg = $file->getErrorString();
                        log_message('error', 'File upload failed: ' . $errorMsg);
                        $this->response->setContentType('application/json');
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Failed to upload image: ' . $errorMsg
                        ]);
                    }
                    
                    // Store relative path for database
                    $imageUrl = 'uploads/products/' . $newName;
                }
                
                // Prepare data
                $data = [
                    'name'               => $name,
                    'category'           => $category,
                    'unit'               => $unit,
                    'unit_price'         => (float) $unitPrice,
                    'low_stock_threshold'=> !empty($lowStockThreshold) ? (float)$lowStockThreshold : 0.00,
                ];
                
                // Add image_url only if file was uploaded
                if (!empty($imageUrl)) {
                    $data['image_url'] = $imageUrl;
                }
                
                // Log data before insertion
                log_message('info', 'Prepared data for insertion: ' . json_encode($data));

                // Skip model validation since we already validated in controller
                $this->products->skipValidation(true);
                
                // Insert the product
                $insertId = $this->products->insert($data);
                
                // Re-enable validation for future operations
                $this->products->skipValidation(false);
                
                // Check for model errors
                $modelErrors = $this->products->errors();
                if (!empty($modelErrors)) {
                    log_message('error', 'Product creation model errors: ' . json_encode($modelErrors));
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Validation error',
                        'errors' => $modelErrors
                    ]);
                }
                
                // Check for database errors
                $db = \Config\Database::connect();
                $dbError = $db->error();
                if (!empty($dbError) && !empty($dbError['message'])) {
                    log_message('error', 'Database error: ' . json_encode($dbError));
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Database error: ' . $dbError['message']
                    ]);
                }
                
                // Verify insertion was successful
                if ($insertId === false || $insertId === 0 || $insertId === null) {
                    log_message('error', 'Product creation failed - no insert ID returned');
                    $this->response->setContentType('application/json');
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Failed to save product. Please check your input and try again.'
                    ]);
                }

                log_message('info', '=== PRODUCT CREATED SUCCESSFULLY ===');
                log_message('info', 'Product ID: ' . $insertId);
                $this->response->setContentType('application/json');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Product added successfully!',
                    'insert_id' => $insertId,
                    'product_name' => $name
                ]);
            } catch (\Exception $e) {
                log_message('error', 'Product creation exception: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
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

        return view('layout', [
            'title'   => 'Add Product - Kuya EDs',
            'content' => view('products/create'),
        ]);
    }

    public function edit($id = null)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Product not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $validationRules = [
                'name'        => 'required|min_length[3]|max_length[100]',
                'category'    => 'permit_empty|max_length[50]',
                'unit'        => 'required|max_length[20]',
                'unit_price'  => 'required|numeric|greater_than[0]',
                'low_stock_threshold' => 'permit_empty|numeric|greater_than_equal_to[0]',
            ];
            
            // Only validate image if file is uploaded
            $file = $this->request->getFile('product_image');
            if ($file && $file->isValid() && !$file->hasMoved()) {
                $validationRules['product_image'] = 'uploaded[product_image]|max_size[product_image,2048]|is_image[product_image]';
            }

            $validation = $this->validate($validationRules);

            if (!$validation) {
                return view('layout', [
                    'title'   => 'Edit Product - Kuya EDs',
                    'content' => view('products/edit', ['product' => $product, 'errors' => $this->validator->getErrors()]),
                ]);
            }

            try {
                // Handle image upload
                $imageUrl = $product['image_url'] ?? null; // Keep existing image by default
                $file = $this->request->getFile('product_image');
                
                // Check if user wants to remove image
                if ($this->request->getPost('remove_image')) {
                    // Delete old image if exists
                    if (!empty($product['image_url']) && file_exists(WRITEPATH . $product['image_url'])) {
                        @unlink(WRITEPATH . $product['image_url']);
                    }
                    $imageUrl = null;
                } elseif ($file && $file->isValid() && !$file->hasMoved()) {
                    // Delete old image if exists
                    if (!empty($product['image_url']) && file_exists(WRITEPATH . $product['image_url'])) {
                        @unlink(WRITEPATH . $product['image_url']);
                    }
                    
                    // Create uploads directory if it doesn't exist
                    $uploadPath = WRITEPATH . 'uploads/products/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }
                    
                    // Generate unique filename
                    $newName = $file->getRandomName();
                    if ($file->move($uploadPath, $newName)) {
                        // Store relative path for database
                        $imageUrl = 'uploads/products/' . $newName;
                    } else {
                        log_message('error', 'Failed to move uploaded file: ' . $file->getErrorString());
                        return redirect()->back()->with('error', 'Failed to upload image. Please try again.');
                    }
                }
                
                // Prepare data
                $data = [
                    'name'               => trim($this->request->getPost('name')),
                    'category'           => trim($this->request->getPost('category')) ?: null,
                    'unit'               => trim($this->request->getPost('unit')),
                    'unit_price'         => (float) $this->request->getPost('unit_price'),
                    'low_stock_threshold'=> $this->request->getPost('low_stock_threshold') ? (float)$this->request->getPost('low_stock_threshold') : 0,
                    'image_url'          => $imageUrl,
                ];

                // Skip model validation since we already validated in controller
                $this->products->skipValidation(true);
                
                // Update the product
                $result = $this->products->update($id, $data);
                
                // Re-enable validation for future operations
                $this->products->skipValidation(false);
                
                // Check for errors
                $errors = $this->products->errors();
                if (!empty($errors)) {
                    log_message('error', 'Product update validation errors: ' . json_encode($errors));
                    return view('layout', [
                        'title'   => 'Edit Product - Kuya EDs',
                        'content' => view('products/edit', ['product' => $product, 'errors' => $errors]),
                    ]);
                }
                
                // Verify update was successful
                if ($result === false) {
                    log_message('error', 'Product update failed for ID: ' . $id);
                    return redirect()->back()->with('error', 'Failed to update product. Please check your input and try again.');
                }

                log_message('info', 'Product updated successfully with ID: ' . $id);
                return redirect()->to('/products')->with('success', 'Product updated successfully!');
            } catch (\Exception $e) {
                log_message('error', 'Product update exception: ' . $e->getMessage());
                log_message('error', 'Stack trace: ' . $e->getTraceAsString());
                return redirect()->back()->with('error', 'An error occurred while updating the product. Please try again.');
            }
        }

        return view('layout', [
            'title'   => 'Edit Product - Kuya EDs',
            'content' => view('products/edit', ['product' => $product]),
        ]);
    }

    public function delete($id = null)
    {
        $product = $this->products->find($id);
        if (!$product) {
            return redirect()->to('/products')->with('error', 'Product not found.');
        }

        // Delete associated image if exists
        if (!empty($product['image_url']) && strpos($product['image_url'], 'uploads/') === 0) {
            $imagePath = WRITEPATH . $product['image_url'];
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $this->products->delete($id);
        return redirect()->to('/products')->with('success', 'Product deleted successfully!');
    }

    /**
     * Serve uploaded images
     */
    public function serveImage($path = null)
    {
        if (empty($path)) {
            return $this->response->setStatusCode(404);
        }

        // Security: Only allow images from uploads/products directory
        $fullPath = WRITEPATH . 'uploads/products/' . $path;
        
        // Prevent directory traversal
        $realPath = realpath($fullPath);
        $basePath = realpath(WRITEPATH . 'uploads/products');
        
        if ($realPath === false || strpos($realPath, $basePath) !== 0) {
            return $this->response->setStatusCode(404);
        }

        if (!file_exists($realPath) || !is_file($realPath)) {
            return $this->response->setStatusCode(404);
        }

        // Get mime type
        $mimeType = mime_content_type($realPath);
        if (strpos($mimeType, 'image/') !== 0) {
            return $this->response->setStatusCode(404);
        }

        // Serve the file with proper headers
        $this->response->setContentType($mimeType);
        $this->response->setHeader('Cache-Control', 'public, max-age=31536000');
        $this->response->setBody(file_get_contents($realPath));
        
        return $this->response;
    }
}


