<style>
    #product-dropdown {
        margin-top: 2px;
    }
    #product-dropdown .list-group-item {
        cursor: pointer;
        border-left: none;
        border-right: none;
    }
    #product-dropdown .list-group-item:first-child {
        border-top: none;
    }
    #product-dropdown .list-group-item:hover {
        background-color: #f8f9fa;
        z-index: 1001;
    }
    #product-dropdown .list-group-item.active {
        background-color: #0d6efd;
        color: white;
    }
    #product-dropdown .badge {
        font-size: 0.75rem;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Add Stock</h2>
    <a href="<?= site_url('inventory') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to Inventory
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
    <div id="success-toast" class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
        <div class="toast align-items-center bg-success bg-gradient text-white border-0 rounded-pill shadow-lg show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body text-center w-100" style="font-size:1.25rem;letter-spacing:1px;">
                    <i class="bi bi-check-circle-fill me-2" style="font-size:2rem;"></i> Stock added!
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto ms-n2" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        var toastEl = document.querySelector('.toast');
        var bsToast = new bootstrap.Toast(toastEl, {delay: 2300, autohide: true});
        bsToast.show();
      });
    </script>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post" action="<?= site_url('inventory/add') ?>" id="add-stock-form" novalidate>
            <?= csrf_field() ?>
            <!-- Hidden input to ensure product_id is always submitted - THIS IS CRITICAL -->
            <input type="hidden" name="product_id" id="product-id-hidden" value="<?= esc($selectedProductId ?? '') ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" 
                           class="form-control form-control-lg" 
                           id="product-search" 
                           placeholder="Search products by name or category..."
                           autocomplete="off"
                           required>
                    <select name="product_id_select" class="form-select form-select-lg" id="product-select" style="display: none;">
                        <option value="">-- Select product --</option>
                        <?php 
                        $selectedProductId = $selectedProductId ?? null;
                        foreach ($products as $p): 
                        ?>
                            <option value="<?= $p['id'] ?>" 
                                    data-name="<?= esc($p['name']) ?>"
                                    data-category="<?= esc($p['category'] ?? 'N/A') ?>"
                                    data-stock="<?= number_format($p['total_stock'] ?? 0, 2) ?>"
                                    data-unit="<?= esc($p['unit'] ?? '') ?>"
                                    <?= $selectedProductId == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['name']) ?> 
                                (<?= esc($p['category'] ?? 'N/A') ?>, 
                                Current Stock: <?= number_format($p['total_stock'] ?? 0, 2) ?> <?= esc($p['unit'] ?? '') ?>) 
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Also add a visible select as fallback -->
                    <select name="product_id" class="form-select form-select-lg d-none" id="product-select-visible">
                        <option value="">-- Select product --</option>
                        <?php 
                        foreach ($products as $p): 
                        ?>
                            <option value="<?= $p['id'] ?>" <?= ($selectedProductId ?? null) == $p['id'] ? 'selected' : '' ?>>
                                <?= esc($p['name']) ?> (<?= esc($p['category'] ?? 'N/A') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="product-dropdown" class="list-group position-absolute w-100" style="display: none; z-index: 1000; max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; background: white; box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);"></div>
                </div>
                <small class="form-text text-muted">Start typing to search for a product</small>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <input type="number" 
                           step="0.01" 
                           name="quantity" 
                           class="form-control" 
                           placeholder="Enter quantity"
                           min="0.01"
                           value=""
                           required>
                    <span class="input-group-text" id="unit-display">-</span>
                </div>
                <small class="form-text text-muted">Enter the quantity to add</small>
            </div>
            
            <div class="mb-4">
                <label class="form-label fw-bold">Expiry Date (optional)</label>
                <input type="date" 
                       name="expiry_date" 
                       class="form-control form-control-lg"
                       min="<?= date('Y-m-d') ?>">
                <small class="form-text text-muted">When does this batch expire? Leave blank if no expiry.</small>
            </div>
            
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                    <i class="bi bi-check-circle"></i> Add Stock
                </button>
                <a href="<?= site_url('inventory') ?>" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<div id="success-toast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong> Stock has been added successfully.
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product-select');
    const productSearch = document.getElementById('product-search');
    const productDropdown = document.getElementById('product-dropdown');
    const unitDisplay = document.getElementById('unit-display');
    const allOptions = Array.from(productSelect.options).slice(1); // Skip first empty option
    
    let selectedProduct = null;
    
    // Function to update unit display
    function updateUnitDisplay(productId) {
        const option = productSelect.querySelector(`option[value="${productId}"]`);
        if (option) {
            const unit = option.getAttribute('data-unit');
            unitDisplay.textContent = unit || '-';
        } else {
            unitDisplay.textContent = '-';
        }
    }
    
    // Function to filter and display products
    function filterProducts(searchTerm) {
        const term = searchTerm.toLowerCase().trim();
        productDropdown.innerHTML = '';
        
        if (term === '') {
            productDropdown.style.display = 'none';
            return;
        }
        
        const filtered = allOptions.filter(option => {
            const name = option.getAttribute('data-name').toLowerCase();
            const category = option.getAttribute('data-category').toLowerCase();
            return name.includes(term) || category.includes(term);
        });
        
        if (filtered.length === 0) {
            productDropdown.innerHTML = '<div class="list-group-item text-muted">No products found</div>';
            productDropdown.style.display = 'block';
            return;
        }
        
        filtered.forEach(option => {
            const item = document.createElement('a');
            item.href = '#';
            item.className = 'list-group-item list-group-item-action';
            item.innerHTML = `
                <div class="d-flex w-100 justify-content-between">
                    <h6 class="mb-1">${option.getAttribute('data-name')}</h6>
                </div>
                <p class="mb-1 text-muted small">
                    <span class="badge bg-secondary">${option.getAttribute('data-category')}</span>
                    <span class="ms-2">Stock: ${option.getAttribute('data-stock')} ${option.getAttribute('data-unit')}</span>
                </p>
            `;
            
            item.addEventListener('click', function(e) {
                e.preventDefault();
                selectProduct(option.value, option.getAttribute('data-name'));
            });
            
            productDropdown.appendChild(item);
        });
        
        productDropdown.style.display = 'block';
    }
    
    // Function to select a product
    function selectProduct(productId, productName) {
        if (!productId || productId === '' || productId === '0') {
            console.error('Invalid product ID:', productId);
            return;
        }
        
        // Update all product ID fields
        if (productSelect) productSelect.value = productId;
        if (productSearch) productSearch.value = productName;
        if (productDropdown) productDropdown.style.display = 'none';
        selectedProduct = productId;
        updateUnitDisplay(productId);
        
        // CRITICAL: Update hidden input field - this is what gets submitted
        const hiddenInput = document.getElementById('product-id-hidden');
        const visibleSelect = document.getElementById('product-select-visible');
        
        if (hiddenInput) {
            hiddenInput.value = productId;
            console.log('✓ Hidden input updated with product_id:', productId);
        } else {
            console.error('✗ Hidden input field not found!');
        }
        
        if (visibleSelect) {
            visibleSelect.value = productId;
        }
        
        // Trigger change event
        if (productSelect) {
            productSelect.dispatchEvent(new Event('change'));
        }
    }
    
    // Search input handling
    productSearch.addEventListener('input', function() {
        filterProducts(this.value);
    });
    
    productSearch.addEventListener('focus', function() {
        if (this.value.trim() !== '') {
            filterProducts(this.value);
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!productSearch.contains(e.target) && !productDropdown.contains(e.target)) {
            productDropdown.style.display = 'none';
        }
    });
    
    // Update unit display when product is selected via dropdown
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                if (productSearch) productSearch.value = option.getAttribute('data-name');
                updateUnitDisplay(this.value);
                
                // CRITICAL: Update hidden input field
                const hiddenInput = document.getElementById('product-id-hidden');
                const visibleSelect = document.getElementById('product-select-visible');
                
                if (hiddenInput) {
                    hiddenInput.value = this.value;
                    console.log('✓ Product changed - Hidden input updated:', this.value);
                }
                
                if (visibleSelect) {
                    visibleSelect.value = this.value;
                }
            }
        });
    }
    
    // Initialize if product is pre-selected
    if (productSelect.value) {
        const option = productSelect.options[productSelect.selectedIndex];
        productSearch.value = option.getAttribute('data-name');
        updateUnitDisplay(productSelect.value);
        
        // Update hidden input field
        const hiddenInput = document.getElementById('product-id-hidden');
        if (hiddenInput) {
            hiddenInput.value = productSelect.value;
        }
    }
    
    // Form submission handling - SIMPLIFIED
    const form = document.getElementById('add-stock-form');
    const submitBtn = document.getElementById('submit-btn');
    
    // Form submission handling - AJAX like Quick Add
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Get product ID from multiple sources
            const hiddenInput = document.getElementById('product-id-hidden');
            const visibleSelect = document.getElementById('product-select-visible');
            let productId = '';
            
            // Priority: hidden input > visible select > hidden select
            if (hiddenInput && hiddenInput.value) {
                productId = hiddenInput.value;
            } else if (visibleSelect && visibleSelect.value) {
                productId = visibleSelect.value;
                if (hiddenInput) hiddenInput.value = productId;
            } else if (productSelect && productSelect.value) {
                productId = productSelect.value;
                if (hiddenInput) hiddenInput.value = productId;
            }
            
            // Ensure product is selected
            if (!productId || productId === '' || productId === '0') {
                showError('Please select a product. Type a product name and click on it from the dropdown list.');
                if (productSearch) productSearch.focus();
                return false;
            }
            
            // Validate quantity
            const quantityInput = document.querySelector('input[name="quantity"]');
            const expiryInput = document.querySelector('input[name="expiry_date"]');
            const quantity = quantityInput ? parseFloat(quantityInput.value) : 0;
            const expiryDate = expiryInput ? expiryInput.value : '';
            
            if (!quantity || quantity <= 0 || isNaN(quantity)) {
                showError('Please enter a valid quantity greater than 0.');
                if (quantityInput) quantityInput.focus();
                return false;
            }
            
            // FINAL CHECK: Ensure hidden input has the product ID
            if (hiddenInput) {
                hiddenInput.value = productId;
            }
            
            // Disable button and show loading
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            if (expiryDate) {
                formData.append('expiry_date', expiryDate);
            }
            
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
            fetch('<?= site_url('inventory/add') ?>', {
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
                    if (quantityInput) quantityInput.value = '';
                    if (expiryInput) expiryInput.value = '';
                    if (productSearch) productSearch.value = '';
                    if (hiddenInput) hiddenInput.value = '';
                    if (productSelect) productSelect.value = '';
                    if (visibleSelect) visibleSelect.value = '';
                    if (unitDisplay) unitDisplay.textContent = '-';
                    
                    // Redirect to inventory after 1.5 seconds
                    setTimeout(() => {
                        window.location.href = '<?= site_url('inventory') ?>';
                    }, 1500);
                } else {
                    showError(data.message || 'Failed to add stock.');
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
        });
    }
    
    // Toast notification functions
    function showSuccess(message) {
        const toastContainer = document.getElementById('success-toast');
        const toastElement = toastContainer.querySelector('.toast');
        const toastBody = toastElement.querySelector('.toast-body');
        
        if (toastBody) {
            toastBody.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>Success!</strong> ' + message;
        }
        
        toastContainer.style.display = 'block';
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 3000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastContainer.style.display = 'none';
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
});
</script>


