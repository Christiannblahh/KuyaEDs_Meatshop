<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Our Products</h2>
    <a href="<?= site_url('products/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

<div class="row g-4">
    <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="product-card card h-100 shadow-sm <?= ($product['total_stock'] < $product['low_stock_threshold']) ? 'border-warning' : '' ?>">
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
                    <?php if ($product['total_stock'] < $product['low_stock_threshold']): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">Low Stock</span>
                    <?php endif; ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge bg-secondary"><?= esc($product['category']) ?></span>
                    </div>
                    <h5 class="card-title mb-2"><?= esc($product['name']) ?></h5>
                    <?php if (!empty($product['description'])): ?>
                        <p class="card-text text-muted small mb-3 flex-grow-1"><?= esc($product['description']) ?></p>
                    <?php else: ?>
                        <p class="card-text text-muted small mb-3 flex-grow-1">Fresh and quality <?= esc(strtolower($product['name'])) ?> available now.</p>
                    <?php endif; ?>
                    <div class="mt-auto">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="h4 text-primary mb-0">₱<?= number_format($product['unit_price'], 2) ?></span>
                                <small class="text-muted d-block">per <?= esc($product['unit']) ?></small>
                            </div>
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-box-seam"></i> Stock: <?= number_format($product['total_stock'], 2) ?> <?= esc($product['unit']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
        <p class="text-muted mt-3">No products available yet.</p>
        <a href="<?= site_url('products/create') ?>" class="btn btn-primary">Add Your First Product</a>
    </div>
<?php endif; ?>
