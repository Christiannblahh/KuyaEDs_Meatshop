<h2>Add Product</h2>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Category</label>
        <input type="text" name="category" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Unit (e.g. kg, pack)</label>
        <input type="text" name="unit" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Unit Price</label>
        <input type="number" step="0.01" name="unit_price" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Low Stock Threshold</label>
        <input type="number" step="0.01" name="low_stock_threshold" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3" placeholder="Brief description of the product..."></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Image URL</label>
        <input type="url" name="image_url" class="form-control" placeholder="https://example.com/image.jpg">
        <small class="form-text text-muted">Enter a URL to an image of the product</small>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
    <a href="<?= site_url('products') ?>" class="btn btn-secondary">Cancel</a>
</form>


