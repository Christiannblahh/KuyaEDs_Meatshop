<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Current Order</h2>
    <div class="d-flex gap-2">
        <a href="<?= site_url('order') ?>" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Continue Shopping
        </a>
        <?php if (!empty($order)): ?>
            <button type="button" class="btn btn-outline-danger" onclick="clearOrder()">
                <i class="bi bi-trash"></i> Clear Order
            </button>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($order)): ?>
    <div class="text-center py-5">
        <i class="bi bi-cart-x" style="font-size: 4rem; color: #ccc;"></i>
        <h4 class="mt-3 text-muted">Your order is empty</h4>
        <p class="text-muted">Start adding items to your order!</p>
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
                    <div id="order-items">
                        <?php foreach ($order as $item): ?>
                            <div class="order-item border-bottom pb-3 mb-3" data-product-id="<?= $item['product_id'] ?>">
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
                    <h5 class="mb-0">Payment</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('order/processPayment') ?>" id="payment-form">
                        <div class="mb-3">
                            <label for="customer_payment" class="form-label">Cash Payment *</label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="customer_payment" name="customer_payment" 
                                       min="<?= $subtotal ?>" step="0.01" required>
                            </div>
                            <small class="text-muted">Minimum: ₱<?= number_format($subtotal, 2) ?></small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Order Total</label>
                            <div class="h4 text-primary" id="order-total">₱<?= number_format($subtotal, 2) ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Change</label>
                            <div class="h5 text-success" id="change-amount">₱0.00</div>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-cash-coin"></i> Complete Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment calculation
    const paymentInput = document.getElementById('customer_payment');
    const changeDisplay = document.getElementById('change-amount');
    const orderTotal = <?= $subtotal ?>;
    
    paymentInput?.addEventListener('input', function() {
        const payment = parseFloat(this.value) || 0;
        const change = payment - orderTotal;
        
        if (change >= 0) {
            changeDisplay.textContent = '₱' + change.toFixed(2);
            changeDisplay.className = 'h5 text-success';
        } else {
            changeDisplay.textContent = 'Insufficient payment';
            changeDisplay.className = 'h5 text-danger';
        }
    });
    
    // Quantity increase/decrease buttons
    document.querySelectorAll('.quantity-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            const step = parseFloat(input.step) || 0.01;
            const newValue = parseFloat(input.value) + step;
            input.value = newValue.toFixed(2);
            updateOrderItem(input);
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
                updateOrderItem(input);
            }
        });
    });
    
    // Quantity input change
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('change', function() {
            updateOrderItem(this);
        });
    });
    
    // Remove item
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            if (confirm('Remove this item from order?')) {
                await removeFromOrder(productId);
            }
        });
    });
    
    async function updateOrderItem(input) {
        const productId = input.dataset.productId;
        const quantity = parseFloat(input.value);
        const orderItem = input.closest('.order-item');
        const button = input;
        
        button.disabled = true;
        
        try {
            const response = await fetch('<?= site_url('order/updateOrder') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update total
                document.getElementById('order-total').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                
                // Update change calculation
                const payment = parseFloat(paymentInput.value) || 0;
                const change = payment - data.subtotal;
                if (change >= 0) {
                    changeDisplay.textContent = '₱' + change.toFixed(2);
                    changeDisplay.className = 'h5 text-success';
                } else {
                    changeDisplay.textContent = 'Insufficient payment';
                    changeDisplay.className = 'h5 text-danger';
                }
                
                // Update line total
                const lineTotal = orderItem.querySelector('.line-total');
                const unitPrice = parseFloat(orderItem.querySelector('.text-muted').textContent.match(/[\d.]+/)[0]);
                lineTotal.textContent = '₱' + (quantity * unitPrice).toFixed(2);
                
                // If quantity is 0, remove item
                if (quantity <= 0) {
                    orderItem.remove();
                    if (document.querySelectorAll('.order-item').length === 0) {
                        location.reload();
                    }
                }
            } else {
                alert(data.message || 'Failed to update order');
                location.reload();
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
            location.reload();
        } finally {
            button.disabled = false;
        }
    }
    
    async function removeFromOrder(productId) {
        try {
            const response = await fetch('<?= site_url('order/removeFromOrder') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Remove item from DOM
                document.querySelector(`.order-item[data-product-id="${productId}"]`).remove();
                
                // Update totals
                document.getElementById('order-total').textContent = '₱' + parseFloat(data.subtotal).toFixed(2);
                
                // Update change calculation
                const payment = parseFloat(paymentInput.value) || 0;
                const change = payment - data.subtotal;
                if (change >= 0) {
                    changeDisplay.textContent = '₱' + change.toFixed(2);
                    changeDisplay.className = 'h5 text-success';
                } else {
                    changeDisplay.textContent = 'Insufficient payment';
                    changeDisplay.className = 'h5 text-danger';
                }
                
                // Reload if order is empty
                if (document.querySelectorAll('.order-item').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.message || 'Failed to remove item');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }
    
    // Payment form confirmation
    document.getElementById('payment-form')?.addEventListener('submit', function(e) {
        const payment = parseFloat(document.getElementById('customer_payment').value) || 0;
        
        if (payment < orderTotal) {
            e.preventDefault();
            alert('Insufficient payment amount.');
            return;
        }
        
        if (!confirm('Complete this order? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
    
    // Clear order function
    window.clearOrder = function() {
        if (confirm('Clear all items from the current order?')) {
            fetch('<?= site_url('order/clearOrder') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to clear order.');
                }
            });
        }
    };
});
</script>