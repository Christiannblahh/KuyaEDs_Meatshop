<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add New Product</h2>
    <a href="<?= site_url('products') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Products
    </a>
</div>

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
        <form method="post" action="<?= site_url('products/create') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g., Chicken Breast" required autofocus>
                        <small class="form-text text-muted">Enter the product name</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Category</label>
                        <select name="category" class="form-select" id="category-select">
                            <option value="">-- Select or type category --</option>
                            <option value="Chicken">Chicken</option>
                            <option value="Pork">Pork</option>
                            <option value="Beef">Beef</option>
                            <option value="Fish">Fish</option>
                            <option value="Seafood">Seafood</option>
                            <option value="Processed">Processed</option>
                            <option value="Other">Other</option>
                        </select>
                        <input type="text" name="category_custom" class="form-control mt-2" id="category-custom" placeholder="Or type custom category" style="display: none;">
                        <small class="form-text text-muted">Select from common categories or type your own</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Unit <span class="text-danger">*</span></label>
                        <select name="unit" class="form-select" id="unit-select" required>
                            <option value="kg" selected>Kilogram (kg)</option>
                            <option value="">-- Select unit --</option>
                            <option value="g">Gram (g)</option>
                            <option value="pack">Pack</option>
                            <option value="piece">Piece</option>
                            <option value="box">Box</option>
                            <option value="bundle">Bundle</option>
                            <option value="dozen">Dozen</option>
                            <option value="liter">Liter (L)</option>
                            <option value="ml">Milliliter (ml)</option>
                            <option value="">-- Custom --</option>
                        </select>
                        <input type="text" name="unit_custom" class="form-control mt-2" id="unit-custom" placeholder="Type custom unit (e.g., tray, can, bottle)" style="display: none;">
                        <small class="form-text text-muted">Select unit of measurement or enter custom</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Unit Price (₱) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" name="unit_price" class="form-control" placeholder="0.00" min="0.01" required>
                        </div>
                        <small class="form-text text-muted">Price per unit</small>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Low Stock Threshold</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="low_stock_threshold" class="form-control" placeholder="0.00" value="" min="0">
                            <span class="input-group-text" id="unit-threshold-display">-</span>
                        </div>
                        <small class="form-text text-muted">Optional: Alert when stock goes below this value. Leave empty to disable.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Product Image <span class="text-muted">(Optional)</span></label>
                        <input type="file" name="product_image" id="product_image" class="form-control" accept="image/*" onchange="previewImage(this)">
                        <small class="form-text text-muted">Optional: Upload product image (JPG, PNG, GIF - Max 2MB). You can add this later.</small>
                        <div id="image-preview" class="mt-2" style="display: none;">
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
                    <i class="bi bi-check-circle"></i> Save Product
                </button>
                <a href="<?= site_url('products') ?>" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Product create form JavaScript loaded');
    
    // Category dropdown handling
    const categorySelect = document.getElementById('category-select');
    const categoryCustom = document.getElementById('category-custom');
    
    categorySelect.addEventListener('change', function() {
        if (this.value === 'Other' || this.value === '') {
            categoryCustom.style.display = 'block';
            categoryCustom.required = true;
            if (this.value === 'Other') {
                categoryCustom.focus();
            }
        } else {
            categoryCustom.style.display = 'none';
            categoryCustom.required = false;
            categoryCustom.value = '';
        }
    });
    
    // Unit dropdown handling
    const unitSelect = document.getElementById('unit-select');
    const unitCustom = document.getElementById('unit-custom');
    
    unitSelect.addEventListener('change', function() {
        // Check if "Custom" option is selected (last option with empty value)
        const selectedIndex = this.selectedIndex;
        const selectedOption = this.options[selectedIndex];
        
        if (selectedOption.value === '' && selectedOption.textContent.includes('Custom')) {
            unitCustom.style.display = 'block';
            unitCustom.required = true;
            unitCustom.focus();
            // Remove required from select since we'll use custom
            this.removeAttribute('required');
        } else {
            unitCustom.style.display = 'none';
            unitCustom.required = false;
            unitCustom.value = '';
            this.setAttribute('required', 'required');
        }
    });
    
    // Update unit display for threshold
    const unitThresholdDisplay = document.getElementById('unit-threshold-display');
    unitSelect.addEventListener('change', function() {
        updateThresholdUnit();
    });
    
    function updateThresholdUnit() {
        const selectedUnit = unitSelect.value || unitCustom.value;
        if (selectedUnit) {
            unitThresholdDisplay.textContent = selectedUnit;
        } else {
            unitThresholdDisplay.textContent = '-';
        }
    }
    
    // Form submission handling - AJAX like Add Stock
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    const imageInput = document.getElementById('product_image');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            try {
                // Handle custom category
                if (categoryCustom && categoryCustom.style.display === 'block' && categoryCustom.value && categoryCustom.value.trim() !== '') {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'category';
                    hiddenInput.value = categoryCustom.value.trim();
                    form.appendChild(hiddenInput);
                    if (categorySelect) categorySelect.disabled = true;
                }
                
                // Handle custom unit
                if (unitCustom && unitCustom.style.display === 'block' && unitCustom.value && unitCustom.value.trim() !== '') {
                    if (unitSelect) unitSelect.removeAttribute('required');
                }
                
                // Validate unit
                const unitValue = unitSelect ? unitSelect.value : (unitCustom ? unitCustom.value : '');
                if (!unitValue || unitValue === '') {
                    showError('Please select or enter a unit.');
                    if (unitSelect) unitSelect.focus();
                    return false;
                }
                
                // Validate required fields
                const nameInput = document.querySelector('input[name="name"]');
                const unitPriceInput = document.querySelector('input[name="unit_price"]');
                
                if (!nameInput || !nameInput.value || nameInput.value.trim().length < 3) {
                    showError('Product name is required and must be at least 3 characters.');
                    if (nameInput) nameInput.focus();
                    return false;
                }
                
                if (!unitPriceInput || !unitPriceInput.value || parseFloat(unitPriceInput.value) <= 0) {
                    showError('Unit price is required and must be greater than 0.');
                    if (unitPriceInput) unitPriceInput.focus();
                    return false;
                }
                
                // Validate image size if provided
                if (imageInput && imageInput.files && imageInput.files.length > 0) {
                    const file = imageInput.files[0];
                    if (file.size > 2 * 1024 * 1024) {
                        showError('Image file size must be less than 2MB.');
                        if (imageInput) imageInput.focus();
                        return false;
                    }
                }
                
                // Disable button and show loading
                const originalHtml = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
                
                // Prepare form data
                const formData = new FormData(form);
                
                // Get CSRF token
                const csrfName = '<?= csrf_token() ?>';
                let csrfValue = '';
                const csrfInput = document.querySelector('input[name="' + csrfName + '"]');
                if (csrfInput) {
                    csrfValue = csrfInput.value;
                } else {
                    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                    if (csrfMeta) {
                        csrfValue = csrfMeta.content;
                    }
                }
                
                if (csrfValue) {
                    formData.append(csrfName, csrfValue);
                }
                
                // Send AJAX request
                fetch('<?= site_url('products/create') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('HTTP error! status: ' + response.status);
                    }
                    return response.text().then(text => {
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Response is not JSON:', text);
                            throw new Error('Invalid response from server: ' + text.substring(0, 100));
                        }
                    });
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showSuccess(data.message);
                        // Clear form
                        form.reset();
                        if (categoryCustom) categoryCustom.style.display = 'none';
                        if (unitCustom) unitCustom.style.display = 'none';
                        if (unitThresholdDisplay) unitThresholdDisplay.textContent = '-';
                        const preview = document.getElementById('image-preview');
                        if (preview) preview.style.display = 'none';
                        
                        // Redirect to products page after 1.5 seconds
                        setTimeout(() => {
                            window.location.href = '<?= site_url('products') ?>';
                        }, 1500);
                    } else {
                        // Show validation errors if any
                        if (data.errors) {
                            let errorMsg = data.message || 'Validation failed';
                            const errorList = Object.values(data.errors).join(', ');
                            if (errorList) {
                                errorMsg += ': ' + errorList;
                            }
                            showError(errorMsg);
                        } else {
                            showError(data.message || 'Failed to save product.');
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    showError('Error: ' + error.message);
                })
                .finally(() => {
                    // Re-enable button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                });
                
                return false;
            } catch (err) {
                console.error('Error in form handler:', err);
                showError('An error occurred. Please try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Save Product';
                return false;
            }
        });
    }
    
    // Toast notification functions
    function showSuccess(message) {
        // Create or get success toast
        let successToast = document.getElementById('success-toast');
        if (!successToast) {
            successToast = document.createElement('div');
            successToast.id = 'success-toast';
            successToast.className = 'toast-container position-fixed top-0 end-0 p-3';
            successToast.style.zIndex = '9999';
            successToast.style.display = 'none';
            successToast.innerHTML = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong>Success!</strong> <span id="success-message">${message}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(successToast);
        }
        
        const successMessage = successToast.querySelector('#success-message');
        if (successMessage) {
            successMessage.textContent = message;
        }
        
        successToast.style.display = 'block';
        const toastElement = successToast.querySelector('.toast');
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            successToast.style.display = 'none';
        });
    }
    
    function showError(message) {
        // Create or get error toast
        let errorToast = document.getElementById('error-toast');
        if (!errorToast) {
            errorToast = document.createElement('div');
            errorToast.id = 'error-toast';
            errorToast.className = 'toast-container position-fixed top-0 end-0 p-3';
            errorToast.style.zIndex = '9999';
            errorToast.style.display = 'none';
            errorToast.innerHTML = `
                <div class="toast align-items-center text-white bg-danger border-0" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Error!</strong> <span id="error-message">${message}</span>
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;
            document.body.appendChild(errorToast);
        }
        
        const errorMessage = errorToast.querySelector('#error-message');
        if (errorMessage) {
            errorMessage.textContent = message;
        }
        
        errorToast.style.display = 'block';
        const toastElement = errorToast.querySelector('.toast');
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            errorToast.style.display = 'none';
        });
    }
    
    // Image preview function
    window.previewImage = function(input) {
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.style.display = 'none';
        }
    };
    
    // Remove image function
    window.removeImage = function() {
        const imageInput = document.getElementById('product_image');
        const preview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        
        imageInput.value = '';
        previewImg.src = '';
        preview.style.display = 'none';
    };
    
    // Initialize threshold unit display
    updateThresholdUnit();
});
</script>

