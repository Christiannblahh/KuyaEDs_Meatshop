<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\SaleItemModel;
use App\Models\SaleModel;
use App\Models\StockBatchModel;

class Order extends BaseController
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

    /**
     * Display the menu with products for ordering
     */
    public function index(): string
    {
        try {
            $data['products'] = $this->products->withStock();
            
            // Ensure products is an array
            if (!is_array($data['products'])) {
                $data['products'] = [];
            }
        } catch (\Exception $e) {
            // Log error and show empty products
            log_message('error', 'Order menu error: ' . $e->getMessage());
            $data['products'] = [];
        }

        return view('layout', [
            'title'   => 'Place Order - Kuya EDs',
            'content' => view('order/ordering', $data),
        ]);
    }

    /**
     * List all customer orders
     */
    public function orders(): string
    {
        try {
            // Get all sales with customer names
            $sales = $this->sales->orderBy('sale_date', 'DESC')->findAll();
            
            // Get sale items for each sale
            foreach ($sales as &$sale) {
                $sale['items'] = $this->items->where('sale_id', $sale['id'])->findAll();
            }
            
            $data['sales'] = $sales ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Order list error: ' . $e->getMessage());
            $data['sales'] = [];
        }

        return view('layout', [
            'title'   => 'Customer Orders - Kuya EDs',
            'content' => view('order/customer_orders', $data),
        ]);
    }

    /**
     * Add item to order
     */
    public function addToOrder()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (float) $this->request->getPost('quantity');

        if ($productId <= 0 || $quantity <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid product or quantity',
            ]);
        }

        $product = $this->products->find($productId);
        if (!$product) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found',
            ]);
        }

        // Check stock
        $stock = $this->getProductStock($productId);
        if ($quantity > $stock) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Not enough stock available',
            ]);
        }

        $order = session()->get('current_order') ?? [];
        
        // If product already in order, update quantity
        if (isset($order[$productId])) {
            $newQuantity = $order[$productId]['quantity'] + $quantity;
            if ($newQuantity > $stock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ]);
            }
            $order[$productId]['quantity'] = $newQuantity;
        } else {
            $order[$productId] = [
                'product_id' => $productId,
                'name'      => $product['name'],
                'unit'      => $product['unit'],
                'unit_price'=> (float) $product['unit_price'],
                'quantity'  => $quantity,
                'image_url' => $product['image_url'] ?? null,
            ];
        }

        // Recalculate line total
        $order[$productId]['line_total'] = $order[$productId]['quantity'] * $order[$productId]['unit_price'];

        session()->set('current_order', $order);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Added to order',
            'orderCount' => $this->getOrderCount(),
        ]);
    }

    /**
     * Display current order
     */
    public function viewOrder(): string
    {
        $data['order'] = session()->get('current_order') ?? [];
        $data['subtotal'] = $this->getOrderSubtotal();

        return view('layout', [
            'title'   => 'Current Order - Kuya EDs',
            'content' => view('order/view_order', $data),
        ]);
    }

    /**
     * Process cash payment and complete order
     */
    public function processPayment()
    {
        $customerName = trim($this->request->getPost('customer_name')) ?: 'Walk-in Customer';
        $customerPayment = (float) $this->request->getPost('customer_payment');
        $order = session()->get('current_order') ?? [];

        if (empty($order)) {
            return redirect()->to('/order')->with('error', 'Your order is empty.');
        }

        $totalAmount = $this->getOrderSubtotal();

        if ($customerPayment < $totalAmount) {
            return redirect()->to('/order/viewOrder')->with('error', 
                'Insufficient payment. Required: ₱' . number_format($totalAmount, 2));
        }

        // Calculate change
        $change = $customerPayment - $totalAmount;

        // Validate stock for all items
        foreach ($order as $item) {
            $stock = $this->getProductStock($item['product_id']);
            if ($item['quantity'] > $stock) {
                return redirect()->to('/order/viewOrder')->with('error', 
                    "Not enough stock for {$item['name']}. Available: {$stock} {$item['unit']}");
            }
        }

        $lines = [];
        foreach ($order as $item) {
            $lines[] = [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ];
        }

        $db = db_connect();
        $db->transStart();

        // Skip model validation since we're setting all required fields
        $this->sales->skipValidation(true);
        
        // Generate unique receipt number
        $receiptNumber = 'R' . date('Ymd') . str_pad($saleId ?? rand(1, 9999), 4, '0', STR_PAD_LEFT);
        
        $saleId = $this->sales->insert([
            'sale_date'      => date('Y-m-d H:i:s'),
            'customer_name'  => $customerName,
            'payment_method' => 'cash',
            'subtotal'       => $totalAmount,
            'discount'       => 0.00,
            'tax'            => 0.00,
            'total_amount'   => $totalAmount,
            'receipt_number' => $receiptNumber,
            'customer_payment' => $customerPayment,
            'change_amount'  => $change,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
        
        // Re-enable validation
        $this->sales->skipValidation(false);

        foreach ($lines as $line) {
            // Deduct stock
            if (!$this->batches->deductStock($line['product_id'], $line['quantity'])) {
                $db->transRollback();
                return redirect()->to('/order/viewOrder')->with('error', 
                    'Not enough stock for one of the products.');
            }

            $line['sale_id'] = $saleId;
            
            // Skip model validation for sale items
            $this->items->skipValidation(true);
            $this->items->insert($line);
            $this->items->skipValidation(false);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            log_message('error', 'Order payment transaction failed');
            return redirect()->to('/order/viewOrder')->with('error', 'Could not process order. Please try again.');
        }
        
        // Verify sale was created
        if (!$saleId || $saleId === false) {
            log_message('error', 'Order payment - sale ID not returned');
            return redirect()->to('/order/viewOrder')->with('error', 'Could not process order. Please try again.');
        }

        log_message('info', 'Order payment successful - Sale ID: ' . $saleId . ', Total: ' . $totalAmount);

        // Clear current order
        session()->remove('current_order');

        // Generate receipt and redirect
        return redirect()->to('/order/receipt/' . $saleId);
    }

    /**
     * Display receipt
     */
    public function receipt($saleId): string
    {
        try {
            $sale = $this->sales->find($saleId);
            if (!$sale) {
                return redirect()->to('/order/orders')->with('error', 'Sale not found.');
            }

            $sale['items'] = $this->items->where('sale_id', $saleId)->findAll();
            
            $data['sale'] = $sale;
        } catch (\Exception $e) {
            log_message('error', 'Receipt error: ' . $e->getMessage());
            return redirect()->to('/order/orders')->with('error', 'Could not load receipt.');
        }

        return view('layout', [
            'title'   => 'Receipt - Kuya EDs',
            'content' => view('order/receipt', $data),
        ]);
    }

    /**
     * Print receipt
     */
    public function printReceipt($saleId): string
    {
        try {
            $sale = $this->sales->find($saleId);
            if (!$sale) {
                return redirect()->to('/order/orders')->with('error', 'Sale not found.');
            }

            $sale['items'] = $this->items->where('sale_id', $saleId)->findAll();
            
            $data['sale'] = $sale;
        } catch (\Exception $e) {
            log_message('error', 'Print receipt error: ' . $e->getMessage());
            return redirect()->to('/order/orders')->with('error', 'Could not load receipt.');
        }

        return view('order/print_receipt', $data);
    }

    /**
     * Clear current order
     */
    public function clearOrder()
    {
        session()->remove('current_order');
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Order cleared',
        ]);
    }

    /**
     * Update order item quantity (for view_order.php)
     */
    public function updateOrder()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (float) $this->request->getPost('quantity');

        $order = session()->get('current_order') ?? [];

        if (!isset($order[$productId])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item not in order',
            ]);
        }

        if ($quantity <= 0) {
            unset($order[$productId]);
        } else {
            // Check stock
            $stock = $this->getProductStock($productId);
            if ($quantity > $stock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ]);
            }

            $order[$productId]['quantity'] = $quantity;
            $order[$productId]['line_total'] = $quantity * $order[$productId]['unit_price'];
        }

        session()->set('current_order', $order);

        return $this->response->setJSON([
            'success' => true,
            'orderCount' => $this->getOrderCount(),
            'subtotal' => $this->getOrderSubtotal(),
        ]);
    }

    /**
     * Remove item from order (for view_order.php)
     */
    public function removeFromOrder()
    {
        $productId = (int) $this->request->getPost('product_id');

        $order = session()->get('current_order') ?? [];
        unset($order[$productId]);
        session()->set('current_order', $order);

        return $this->response->setJSON([
            'success' => true,
            'orderCount' => $this->getOrderCount(),
            'subtotal' => $this->getOrderSubtotal(),
        ]);
    }

    /**
     * Get order item count
     */
    private function getOrderCount(): int
    {
        $order = session()->get('current_order') ?? [];
        $count = 0;
        foreach ($order as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * Get order subtotal
     */
    private function getOrderSubtotal(): float
    {
        $order = session()->get('current_order') ?? [];
        $total = 0;
        foreach ($order as $item) {
            $total += $item['line_total'];
        }
        return $total;
    }

    /**
     * Get current stock for a product
     */
    private function getProductStock(int $productId): float
    {
        $product = $this->products->withStock();
        foreach ($product as $p) {
            if ($p['id'] == $productId) {
                return (float) ($p['total_stock'] ?? 0);
            }
        }
        return 0;
    }
}

