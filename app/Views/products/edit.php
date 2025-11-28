<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Edit Product</h2>
    <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Products
    </a>
</div>

<?php if (isset($errors) && !empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Validation Errors:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $field => $message): ?>
                <li><?= esc($message) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= esc($product['name']) ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <input type="text" name="category" class="form-control" value="<?= esc($product['category'] ?? '') ?>" placeholder="e.g., Chicken Breast, Ground Meat">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Unit (e.g., kg, pack, piece) <span class="text-danger">*</span></label>
                        <input type="text" name="unit" class="form-control" value="<?= esc($product['unit'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Unit Price (₱) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="unit_price" class="form-control" value="<?= esc($product['unit_price']) ?>" required>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Low Stock Threshold</label>
                <input type="number" step="0.01" name="low_stock_threshold" class="form-control" value="<?= esc($product['low_stock_threshold'] ?? 0) ?>" placeholder="Alert when stock goes below this value">
                <small class="form-text text-muted">Leave empty or 0 to disable low stock alerts</small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Brief description of the product..."><?= esc($product['description'] ?? '') ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Image URL</label>
                <input type="url" name="image_url" class="form-control" value="<?= esc($product['image_url'] ?? '') ?>" placeholder="https://example.com/image.jpg">
                <small class="form-text text-muted">Enter a URL to an image of the product</small>
                <?php if (!empty($product['image_url'])): ?>
                    <div class="mt-2">
                        <img src="<?= esc($product['image_url']) ?>" alt="Product Image" style="max-width: 150px; max-height: 150px;" class="rounded">
                    </div>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle"></i> Update Product
                </button>
                <a href="<?= site_url('products') ?>" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
                <a href="<?= site_url('products/delete/' . $product['id']) ?>" class="btn btn-danger btn-lg ms-auto" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.');">
                    <i class="bi bi-trash"></i> Delete Product
                </a>
            </div>
        </form>
    </div>
</div>
