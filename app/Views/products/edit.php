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

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show text-center fw-bold" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('products/edit/' . $product['id']) ?>" enctype="multipart/form-data">
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

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Low Stock Threshold</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="low_stock_threshold" class="form-control" value="<?= esc($product['low_stock_threshold'] ?? 0) ?>" placeholder="0.00" min="0">
                            <span class="input-group-text" id="unit-threshold-display"><?= esc($product['unit'] ?? '-') ?></span>
                        </div>
                        <small class="form-text text-muted">Optional: Alert when stock goes below this value. Leave empty to disable.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Image</label>
                        <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <small class="form-text text-muted">Upload new image to replace current (JPG, PNG, GIF - Max 2MB)</small>
                        <?php 
                        // Helper function to get image URL
                        helper('image');
                        $imagePath = !empty($product['image_url']) ? product_image_url($product['image_url']) : '';
                        ?>
                        <?php if (!empty($imagePath)): ?>
                            <div class="mt-2">
                                <p class="small text-muted mb-1">Current Image:</p>
                                <img src="<?= esc($imagePath) ?>" alt="Current Product Image" id="current-image" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" name="remove_image" id="removeImage" value="1">
                                    <label class="form-check-label" for="removeImage">Remove current image</label>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div id="image-preview" class="mt-2" style="display: none;">
                            <p class="small text-muted mb-1">New Image Preview:</p>
                            <img id="preview-img" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            <button type="button" class="btn btn-sm btn-danger ms-2" onclick="removeImage()">
                                <i class="bi bi-x-circle"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const imageInput = document.getElementById('product_image');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Validate image file size (2MB max)
            if (imageInput && imageInput.files.length > 0) {
                const file = imageInput.files[0];
                const maxSize = 2 * 1024 * 1024; // 2MB in bytes
                if (file.size > maxSize) {
                    e.preventDefault();
                    alert('Image file size must be less than 2MB. Please choose a smaller image.');
                    imageInput.focus();
                    return false;
                }
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
        });
    }
    
    // Image preview function
    window.previewImage = function(input) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const currentImage = document.getElementById('current-image');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                if (currentImage) {
                    currentImage.style.opacity = '0.5';
                }
            };
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
            if (currentImage) {
                currentImage.style.opacity = '1';
            }
        }
    };
    
    // Remove image function
    window.removeImage = function() {
        const imageInput = document.getElementById('product_image');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const currentImage = document.getElementById('current-image');
        
        imageInput.value = '';
        previewImg.src = '';
        preview.style.display = 'none';
        if (currentImage) {
            currentImage.style.opacity = '1';
        }
    };
});
</script>
