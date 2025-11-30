<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--primary-color);">Our Products</h2>
        <p class="text-muted small mb-0">Manage your product inventory</p>
    </div>
    <a href="<?= site_url('products/create') ?>" class="btn btn-primary" style="background: var(--secondary-color); border: none; color: #333; font-weight: 600; box-shadow: var(--cta-shadow);">
        <i class="bi bi-plus-circle"></i> Add Product
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Success!</strong> <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error!</strong> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card mb-4 shadow-sm" style="border: 2px solid #FFF4C6; border-radius: 2rem;">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text" style="background: var(--secondary-color); border: none; color: #333;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?= esc($search ?? '') ?>">
                </div>
            </div>
            <div class="col-md-4">
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['category']) ?>" <?= ($category ?? '') === $cat['category'] ? 'selected' : '' ?>>
                            <?= esc($cat['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="background: var(--primary-color); border: none; font-weight: 600;">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .product-card {
        overflow: hidden;
        border-radius: 1rem;
        border: 2px solid #f0f0f0;
        transition: all 0.3s ease;
        background: #fff;
    }
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12) !important;
        border-color: var(--secondary-color);
    }
    .product-card.border-warning {
        border-color: #ffc107 !important;
        border-width: 2px;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
    }
    .product-card.border-warning:hover {
        box-shadow: 0 8px 20px rgba(255, 193, 7, 0.25) !important;
    }
    .product-image-wrapper {
        height: 200px;
        overflow: hidden;
        background: linear-gradient(135deg, #fff9e5 0%, #fffbe9 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border-bottom: 3px solid var(--secondary-color);
    }
    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .product-card:hover .product-image {
        transform: scale(1.08);
    }
    .product-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f8f8 0%, #e8e8e8 100%);
        color: #999;
        font-size: 0.85rem;
        padding: 2rem;
    }
    .product-image-placeholder i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        opacity: 0.4;
        color: #bbb;
    }
    .product-image-placeholder span {
        font-weight: 500;
        letter-spacing: 0.5px;
        color: #999;
    }
    .product-card .card-body {
        padding: 1.15rem;
        background: #fff;
    }
    .product-card .card-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        line-height: 1.3;
    }
    .product-card .card-text {
        font-size: 0.8rem;
        line-height: 1.5;
        min-height: 2.2rem;
        color: #6c757d;
    }
    .product-card .badge {
        font-size: 0.7rem;
        padding: 0.4em 0.7em;
        font-weight: 600;
        border-radius: 0.5rem;
    }
    .product-price {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--primary-color);
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }
    .product-stock {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        font-weight: 500;
        color: #495057;
    }
    .product-stock i {
        color: var(--primary-color);
        font-size: 1rem;
    }
    .product-stock strong {
        color: #2c3e50;
        font-weight: 600;
    }
    .product-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e9ecef;
    }
    .product-actions .btn {
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.5rem 0.75rem;
        transition: all 0.2s ease;
    }
    .product-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .low-stock-badge {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.45rem 0.7rem;
        box-shadow: 0 3px 10px rgba(255, 193, 7, 0.4);
        z-index: 10;
        border-radius: 0.5rem;
        letter-spacing: 0.3px;
    }
    .category-badge {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
        color: white !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .product-card small.text-muted {
        font-size: 0.75rem;
        color: #6c757d;
    }
</style>

<div class="row g-4">
    <?php foreach ($products as $product): ?>
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="product-card card h-100 shadow-sm <?= ($product['total_stock'] < $product['low_stock_threshold']) ? 'border-warning' : '' ?>">
                <div class="product-image-wrapper position-relative">
                    <?php 
                    helper('image');
                    $imageUrl = !empty($product['image_url']) ? product_image_url($product['image_url']) : '';
                    ?>
                    <?php if (!empty($imageUrl)): ?>
                        <img src="<?= esc($imageUrl) ?>" 
                             alt="<?= esc($product['name']) ?>" 
                             class="product-image"
                             onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="product-image-placeholder" style="display: none;">
                            <i class="bi bi-image"></i>
                            <span>No Image</span>
                        </div>
                    <?php else: ?>
                        <div class="product-image-placeholder">
                            <i class="bi bi-image"></i>
                            <span>No Image</span>
                        </div>
                    <?php endif; ?>
                    <?php if ($product['total_stock'] < $product['low_stock_threshold']): ?>
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2 low-stock-badge">
                            <i class="bi bi-exclamation-triangle-fill"></i> Low Stock
                        </span>
                    <?php endif; ?>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="mb-2">
                        <span class="badge category-badge"><?= esc($product['category'] ?? 'Uncategorized') ?></span>
                    </div>
                    <h5 class="card-title"><?= esc($product['name']) ?></h5>
                    <p class="card-text text-muted small mb-3 flex-grow-1">
                        Fresh and quality <?= esc(strtolower($product['name'])) ?> available now.
                    </p>
                    <div class="mt-auto">
                        <div class="mb-3">
                            <div class="product-price">₱<?= number_format($product['unit_price'], 2) ?></div>
                            <small class="text-muted">per <?= esc($product['unit']) ?></small>
                        </div>
                        <div class="product-stock text-muted mb-3">
                            <i class="bi bi-box-seam"></i>
                            <span>Stock: <strong><?= number_format($product['total_stock'], 2) ?> <?= esc($product['unit']) ?></strong></span>
                        </div>
                        <div class="product-actions d-flex gap-2">
                            <a href="<?= site_url('products/edit/' . $product['id']) ?>" class="btn btn-sm btn-outline-primary flex-grow-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="<?= site_url('products/delete/' . $product['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this product?');" title="Delete">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
    <div class="col-12">
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-inbox" style="font-size: 4rem; color: #ddd;"></i>
            </div>
            <h4 class="text-muted mb-2">No products found</h4>
            <p class="text-muted mb-4"><?= !empty($search) || !empty($category) ? 'Try adjusting your search or filter criteria.' : 'Get started by adding your first product.' ?></p>
            <a href="<?= site_url('products/create') ?>" class="btn btn-primary" style="background: var(--secondary-color); border: none; color: #333; font-weight: 600; box-shadow: var(--cta-shadow);">
                <i class="bi bi-plus-circle"></i> Add Your First Product
            </a>
        </div>
    </div>
<?php endif; ?>
