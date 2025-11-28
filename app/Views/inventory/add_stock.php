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
        <form method="post">
            <div class="mb-3">
                <label class="form-label fw-bold">Product <span class="text-danger">*</span></label>
                <select name="product_id" class="form-select form-select-lg" required>
                    <option value="">-- Select product --</option>
                    <?php 
                    $selectedProductId = $selectedProductId ?? null;
                    foreach ($products as $p): 
                    ?>
                        <option value="<?= $p['id'] ?>" <?= $selectedProductId == $p['id'] ? 'selected' : '' ?>>
                            <?= esc($p['name']) ?> 
                            (<?= esc($p['category'] ?? 'N/A') ?>, 
                            Current Stock: <?= number_format($p['total_stock'] ?? 0, 2) ?> <?= esc($p['unit'] ?? '') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                <div class="input-group input-group-lg">
                    <input type="number" 
                           step="0.01" 
                           name="quantity" 
                           class="form-control" 
                           placeholder="0.00"
                           min="0.01"
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
    const productSelect = document.querySelector('select[name="product_id"]');
    const unitDisplay = document.getElementById('unit-display');
    
    // Update unit display when product is selected
    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const optionText = selectedOption.text;
        
        // Extract unit from option text (format: "Product Name (Category, Current Stock: X.XX unit)")
        const unitMatch = optionText.match(/Current Stock: [\d.]+ (\w+)\)/);
        if (unitMatch) {
            unitDisplay.textContent = unitMatch[1];
        } else {
            unitDisplay.textContent = '-';
        }
    });
    
    // Trigger on page load if product is pre-selected
    if (productSelect.value) {
        productSelect.dispatchEvent(new Event('change'));
    }
    
    // Form submission handling
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submit-btn');
    
    form.addEventListener('submit', function(e) {
        // Validate form
        const productId = productSelect.value;
        const quantity = document.querySelector('input[name="quantity"]').value;
        
        if (!productId || productId === '') {
            e.preventDefault();
            alert('Please select a product.');
            return false;
        }
        
        if (!quantity || parseFloat(quantity) <= 0) {
            e.preventDefault();
            alert('Please enter a valid quantity greater than 0.');
            return false;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Adding...';
    });
    
    // Show success toast if redirected from successful submission
    <?php if (session()->getFlashdata('success')): ?>
        showSuccessToast();
    <?php endif; ?>
    
    function showSuccessToast() {
        const toastContainer = document.getElementById('success-toast');
        const toastElement = toastContainer.querySelector('.toast');
        toastContainer.style.display = 'block';
        
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
        
        // Hide container after toast is hidden
        toastElement.addEventListener('hidden.bs.toast', function() {
            toastContainer.style.display = 'none';
        });
    }
});
</script>


