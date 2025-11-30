<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Quick Add Stock</h2>
    <div>
        <a href="<?= site_url('inventory') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Inventory
        </a>
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Hidden CSRF token for AJAX requests -->
<?= csrf_field() ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-table"></i> Add Stock to Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="stock-table">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Current Stock</th>
                        <th>Unit</th>
                        <th>Quantity to Add</th>
                        <th>Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr data-product-id="<?= $p['id'] ?>">
                            <td><strong><?= esc($p['name']) ?></strong></td>
                            <td><?= esc($p['category'] ?? 'N/A') ?></td>
                            <td>
                                <span class="badge <?= (float)($p['total_stock'] ?? 0) <= 0 ? 'bg-danger' : ((float)($p['total_stock'] ?? 0) < (float)($p['low_stock_threshold'] ?? 0) ? 'bg-warning text-dark' : 'bg-success') ?>">
                                    <?= number_format($p['total_stock'] ?? 0, 2) ?>
                                </span>
                            </td>
                            <td><?= esc($p['unit'] ?? 'N/A') ?></td>
                            <td>
                                <input type="number" 
                                       step="0.01" 
                                       min="0.01" 
                                       class="form-control form-control-sm quantity-input" 
                                       placeholder="0.00"
                                       data-product-id="<?= $p['id'] ?>"
                                       style="width: 100px;">
                            </td>
                            <td>
                                <input type="date" 
                                       class="form-control form-control-sm expiry-input" 
                                       min="<?= date('Y-m-d') ?>"
                                       data-product-id="<?= $p['id'] ?>"
                                       style="width: 150px;">
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-sm btn-success add-stock-btn" 
                                        data-product-id="<?= $p['id'] ?>"
                                        data-product-name="<?= esc($p['name']) ?>"
                                        data-product-unit="<?= esc($p['unit'] ?? '') ?>">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Success Toast -->
<div id="success-toast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong> <span id="toast-message">Stock added successfully!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Error Toast -->
<div id="error-toast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Error!</strong> <span id="error-message">Failed to add stock.</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addStockButtons = document.querySelectorAll('.add-stock-btn');
    
    addStockButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const productName = this.getAttribute('data-product-name');
            const productUnit = this.getAttribute('data-product-unit');
            
            // Get quantity from the input in the same row
            const row = this.closest('tr');
            const quantityInput = row.querySelector('.quantity-input');
            const expiryInput = row.querySelector('.expiry-input');
            
            const quantity = parseFloat(quantityInput.value) || 0;
            const expiryDate = expiryInput.value || '';
            
            // Validation
            if (quantity <= 0) {
                showError('Please enter a quantity greater than 0.');
                quantityInput.focus();
                return;
            }
            
            // Disable button and show loading
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            if (expiryDate) {
                formData.append('expiry_date', expiryDate);
            }
            
            // Get CSRF token from the page
            const csrfName = '<?= csrf_token() ?>';
            let csrfValue = '';
            
            // Try to get from hidden input first
            const csrfInput = document.querySelector('input[name="' + csrfName + '"]');
            if (csrfInput) {
                csrfValue = csrfInput.value;
            }
            
            // Fallback to meta tag
            if (!csrfValue) {
                const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                if (csrfMeta) {
                    csrfValue = csrfMeta.content;
                }
            }
            
            // Add CSRF token to form data
            if (csrfValue) {
                formData.append(csrfName, csrfValue);
                console.log('CSRF token added:', csrfName);
            } else {
                console.warn('CSRF token not found!');
            }
            
            // Send AJAX request
            fetch('<?= site_url('inventory/quick-add') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                // Check if response is OK
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                // Try to parse as JSON
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
                    // Clear inputs
                    quantityInput.value = '';
                    expiryInput.value = '';
                    // Reload page after 1 second to update stock display
                    setTimeout(() => {
                        window.location.reload();
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
                this.disabled = false;
                this.innerHTML = originalHtml;
            });
        });
    });
    
    function showSuccess(message) {
        const toastContainer = document.getElementById('success-toast');
        const toastMessage = document.getElementById('toast-message');
        const toastElement = toastContainer.querySelector('.toast');
        
        toastMessage.textContent = message;
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
        const toastContainer = document.getElementById('error-toast');
        const errorMessage = document.getElementById('error-message');
        const toastElement = toastContainer.querySelector('.toast');
        
        errorMessage.textContent = message;
        toastContainer.style.display = 'block';
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastContainer.style.display = 'none';
        });
    }
    
    // Allow Enter key to submit
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const row = this.closest('tr');
                const addBtn = row.querySelector('.add-stock-btn');
                if (addBtn) {
                    addBtn.click();
                }
            }
        });
    });
});
</script>

