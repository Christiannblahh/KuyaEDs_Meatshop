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
     * Display the menu with products
     */
    public function index(): string
    {
        try {
            $data['products'] = $this->products->withStock();
            $data['cartCount'] = $this->getCartCount();
            
            // Ensure products is an array
            if (!is_array($data['products'])) {
                $data['products'] = [];
            }
        } catch (\Exception $e) {
            // Log error and show empty products
            log_message('error', 'Order menu error: ' . $e->getMessage());
            $data['products'] = [];
            $data['cartCount'] = 0;
        }

        return view('layout', [
            'title'   => 'Order - Kuya EDs',
            'content' => view('order/menu', $data),
        ]);
    }

    /**
     * Add item to cart
     */
    public function addToCart()
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

        $cart = session()->get('cart') ?? [];
        
        // If product already in cart, update quantity
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            if ($newQuantity > $stock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ]);
            }
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name'      => $product['name'],
                'unit'      => $product['unit'],
                'unit_price'=> (float) $product['unit_price'],
                'quantity'  => $quantity,
                'image_url' => $product['image_url'] ?? null,
            ];
        }

        // Recalculate line total
        $cart[$productId]['line_total'] = $cart[$productId]['quantity'] * $cart[$productId]['unit_price'];

        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Added to cart',
            'cartCount' => $this->getCartCount(),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart()
    {
        $productId = (int) $this->request->getPost('product_id');
        $quantity  = (float) $this->request->getPost('quantity');

        $cart = session()->get('cart') ?? [];

        if (!isset($cart[$productId])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Item not in cart',
            ]);
        }

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            // Check stock
            $stock = $this->getProductStock($productId);
            if ($quantity > $stock) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Not enough stock available',
                ]);
            }

            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['line_total'] = $quantity * $cart[$productId]['unit_price'];
        }

        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'cartCount' => $this->getCartCount(),
            'subtotal' => $this->getCartSubtotal(),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart()
    {
        $productId = (int) $this->request->getPost('product_id');

        $cart = session()->get('cart') ?? [];
        unset($cart[$productId]);
        session()->set('cart', $cart);

        return $this->response->setJSON([
            'success' => true,
            'cartCount' => $this->getCartCount(),
            'subtotal' => $this->getCartSubtotal(),
        ]);
    }

    /**
     * Display cart
     */
    public function cart(): string
    {
        $data['cart'] = session()->get('cart') ?? [];
        $data['subtotal'] = $this->getCartSubtotal();

        return view('layout', [
            'title'   => 'Your Order - Kuya EDs',
            'content' => view('order/cart', $data),
        ]);
    }

    /**
     * Checkout - finalize the order
     */
    public function checkout()
    {
        $cart = session()->get('cart') ?? [];

        if (empty($cart)) {
            return redirect()->to('/order/cart')->with('error', 'Your cart is empty.');
        }

        // Validate stock for all items
        foreach ($cart as $item) {
            $stock = $this->getProductStock($item['product_id']);
            if ($item['quantity'] > $stock) {
                return redirect()->to('/order/cart')->with('error', 
                    "Not enough stock for {$item['name']}. Available: {$stock} {$item['unit']}");
            }
        }

        $totalAmount = $this->getCartSubtotal();
        $lines = [];

        foreach ($cart as $item) {
            $lines[] = [
                'product_id' => $item['product_id'],
                'quantity'   => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'line_total' => $item['line_total'],
            ];
        }

        $db = db_connect();
        $db->transStart();

        $saleId = $this->sales->insert([
            'sale_date'    => date('Y-m-d H:i:s'),
            'total_amount' => $totalAmount,
        ]);

        foreach ($lines as $line) {
            // Deduct stock
            if (!$this->batches->deductStock($line['product_id'], $line['quantity'])) {
                $db->transRollback();
                return redirect()->to('/order/cart')->with('error', 
                    'Not enough stock for one of the products.');
            }

            $line['sale_id'] = $saleId;
            $this->items->insert($line);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            return redirect()->to('/order/cart')->with('error', 'Could not process order.');
        }

        // Clear cart
        session()->remove('cart');

        return redirect()->to('/order')->with('success', 'Order placed successfully! Thank you for your order.');
    }

    /**
     * Get cart item count
     */
    private function getCartCount(): int
    {
        $cart = session()->get('cart') ?? [];
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'];
        }
        return $count;
    }

    /**
     * Get cart subtotal
     */
    private function getCartSubtotal(): float
    {
        $cart = session()->get('cart') ?? [];
        $total = 0;
        foreach ($cart as $item) {
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

