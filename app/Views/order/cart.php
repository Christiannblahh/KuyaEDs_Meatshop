<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Your Order</h2>
    <a href="<?= site_url('order') ?>" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left"></i> Continue Shopping
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($cart)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
        <h4 class="mt-3 text-muted">Your cart is empty</h4>
        <p class="text-muted">Start adding items to your cart!</p>
        <a href="<?= site_url('order') ?>" class="btn btn-primary">Browse Menu</a>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div id="cart-items">
                        <?php foreach ($cart as $item): ?>
                            <div class="cart-item border-bottom pb-3 mb-3" data-product-id="<?= $item['product_id'] ?>">
                                <div class="row align-items-center">
                                    <div class="col-3 col-md-2">
                                        <?php if (!empty($item['image_url'])): ?>
                                            <img src="<?= esc($item['image_url']) ?>" 
                                                 alt="<?= esc($item['name']) ?>" 
                                                 class="img-fluid rounded">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-9 col-md-6">
                                        <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                                        <p class="text-muted small mb-0">₱<?= number_format($item['unit_price'], 2) ?> per <?= esc($item['unit']) ?></p>
                                    </div>
                                    <div class="col-12 col-md-4 mt-3 mt-md-0">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="input-group" style="width: 120px;">
                                                <button class="btn btn-outline-secondary btn-sm quantity-decrease" type="button">-</button>
                                                <input type="number" 
                                                       class="form-control form-control-sm text-center quantity-input" 
                                                       value="<?= number_format($item['quantity'], 2) ?>" 
                                                       min="0.01" 
                                                       step="0.01"
                                                       data-product-id="<?= $item['product_id'] ?>">
                                                <button class="btn btn-outline-secondary btn-sm quantity-increase" type="button">+</button>
                                            </div>
                                            <div class="ms-3">
                                                <strong class="line-total">₱<?= number_format($item['line_total'], 2) ?></strong>
                                            </div>
                                            <button class="btn btn-sm btn-outline-danger ms-2 remove-item" 
                                                    data-product-id="<?= $item['product_id'] ?>"
                                                    title="Remove">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <strong id="cart-subtotal">₱<?= number_format($subtotal, 2) ?></strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="h5 mb-0">Total:</span>
                        <span class="h4 text-primary mb-0" id="cart-total">₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <form method="post" action="<?= site_url('order/checkout') ?>" id="checkout-form">
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-check-circle"></i> Place Order
                        </button>
                    </form>
                    <a href="<?= site_url('order') ?>" class="btn btn-outline-secondary w-100 mt-2">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quantity increase/decrease buttons
    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const step = parseFloat(input.step) || 0.01;
            const newValue = parseFloat(input.value) + step;
            input.value = newValue.toFixed(2);
            updateCartItem(input);
        });
    });
    
    document.querySelectorAll('.quantity-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const step = parseFloat(input.step) || 0.01;
            const currentValue = parseFloat(input.value);
            if (currentValue > step) {
                const newValue = currentValue - step;
                input.value = newValue.toFixed(2);
                updateCartItem(input);
            }
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            updateCartItem(this);
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            if (confirm('Remove this item from cart?')) {
                await removeFromCart(productId);
            }
        });
    });
    
    async function updateCartItem(input) {
        const productId = input.dataset.productId;
        const quantity = parseFloat(input.value);
        const cartItem = input.closest('.cart-item');
        const button = input;
        
        button.disabled = true;
        
        try {
            const response = await fetch('<?= site_url('order/updateCart') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update subtotal and total
                document.getElementById('cart-subtotal').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                document.getElementById('cart-total').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                
                // Update line total
                const lineTotal = cartItem.querySelector('.line-total');
                const unitPrice = parseFloat(cartItem.querySelector('.text-muted').textContent.match(/[\d.]+/)[0]);
                lineTotal.textContent = '₱' + (quantity * unitPrice).toFixed(2);
                
                // If quantity is 0, remove item
                if (quantity <= 0) {
                    cartItem.remove();
                    if (document.querySelectorAll('.cart-item').length === 0) {
                        location.reload();
                    }
                }
            } else {
                alert(data.message || 'Failed to update cart');
                location.reload();
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
            location.reload();
        } finally {
            button.disabled = false;
        }
    }
    
    async function removeFromCart(productId) {
        try {
            const response = await fetch('<?= site_url('order/removeFromCart') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove item from DOM
                document.querySelector(`.cart-item[data-product-id="${productId}"]`).remove();
                
                // Update totals
                document.getElementById('cart-subtotal').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                document.getElementById('cart-total').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                
                // Reload if cart is empty
                if (document.querySelectorAll('.cart-item').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.message || 'Failed to remove item');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }
    
    // Checkout form confirmation
    document.getElementById('checkout-form')?.addEventListener('submit', function(e) {
        if (!confirm('Place this order? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
});
</script>

