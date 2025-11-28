<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Our Menu</h2>
    <a href="<?= site_url('order/cart') ?>" class="btn btn-primary position-relative">
        <i class="bi bi-cart3"></i> View Cart
        <?php if (isset($cartCount) && $cartCount > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?= $cartCount ?>
            </span>
        <?php endif; ?>
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php 
// Debug: Check if products variable exists
if (!isset($products)) {
    $products = [];
}
?>

<?php if (empty($products) || count($products) === 0): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
        <h4 class="mt-3 text-muted">No products available</h4>
        <p class="text-muted">Please add products to the system first.</p>
        <a href="<?= site_url('products/create') ?>" class="btn btn-primary">Add Products</a>
        <p class="text-muted mt-3 small">
            <a href="<?= site_url('products') ?>" class="text-decoration-none">View all products</a> | 
            <a href="<?= site_url('inventory') ?>" class="text-decoration-none">Add inventory</a>
        </p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <?php 
            $hasStock = isset($product['total_stock']) && (float)$product['total_stock'] > 0;
            $stockAmount = isset($product['total_stock']) ? (float)$product['total_stock'] : 0;
            ?>
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="product-card card h-100 shadow-sm <?= !$hasStock ? 'opacity-75' : '' ?>">
                    <?php if (!$hasStock): ?>
                        <div class="position-absolute top-0 start-0 w-100 bg-warning text-dark text-center py-1" style="z-index: 10;">
                            <small><strong>Out of Stock</strong></small>
                        </div>
                    <?php endif; ?>
                    <div class="product-image-wrapper">
                        <?php if (!empty($product['image_url'])): ?>
                            <img src="<?= esc($product['image_url']) ?>" 
                                 alt="<?= esc($product['name']) ?>" 
                                 class="product-image card-img-top">
                        <?php else: ?>
                            <div class="product-image-placeholder card-img-top">
                                <i class="bi bi-image"></i>
                                <span>No Image</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-secondary"><?= esc($product['category'] ?? 'Uncategorized') ?></span>
                        </div>
                        <h5 class="card-title mb-2"><?= esc($product['name']) ?></h5>
                        <?php if (!empty($product['description'])): ?>
                            <p class="card-text text-muted small mb-3 flex-grow-1"><?= esc($product['description']) ?></p>
                        <?php else: ?>
                            <p class="card-text text-muted small mb-3 flex-grow-1">Fresh and quality <?= esc(strtolower($product['name'])) ?> available now.</p>
                        <?php endif; ?>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <span class="h4 text-primary mb-0">₱<?= number_format($product['unit_price'] ?? 0, 2) ?></span>
                                    <small class="text-muted d-block">per <?= esc($product['unit'] ?? 'unit') ?></small>
                                </div>
                            </div>
                            <div class="text-muted small mb-3">
                                <i class="bi bi-box-seam"></i> Stock: <?= number_format($stockAmount, 2) ?> <?= esc($product['unit'] ?? 'unit') ?>
                            </div>
                            <?php if ($hasStock): ?>
                                <form class="add-to-cart-form" data-product-id="<?= $product['id'] ?>" data-product-name="<?= esc($product['name']) ?>">
                                    <div class="input-group mb-2">
                                        <input type="number" 
                                               class="form-control quantity-input" 
                                               name="quantity" 
                                               value="1" 
                                               min="0.01" 
                                               step="0.01" 
                                               max="<?= $stockAmount ?>"
                                               required>
                                        <span class="input-group-text"><?= esc($product['unit'] ?? 'unit') ?></span>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </button>
                                </form>
                            <?php else: ?>
                                <button type="button" class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-x-circle"></i> Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.add-to-cart-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const productId = form.dataset.productId;
            const productName = form.dataset.productName;
            const quantity = parseFloat(formData.get('quantity'));
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.innerHTML;
            
            // Disable button
            button.disabled = true;
            button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
            
            try {
                const response = await fetch('<?= site_url('order/addToCart') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Show success message
                    showToast('Added to cart!', 'success');
                    
                    // Update cart count in header if exists
                    const cartBadge = document.querySelector('.cart-badge-count');
                    if (cartBadge) {
                        cartBadge.textContent = data.cartCount;
                        if (data.cartCount > 0) {
                            cartBadge.parentElement.style.display = 'inline-block';
                        }
                    }
                    
                    // Reset form
                    form.querySelector('.quantity-input').value = 1;
                } else {
                    showToast(data.message || 'Failed to add to cart', 'error');
                }
            } catch (error) {
                showToast('An error occurred. Please try again.', 'error');
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    });
    
    function showToast(message, type) {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
});
</script>

