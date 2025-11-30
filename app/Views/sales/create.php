<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--primary-color);">Record Sale</h2>
        <p class="text-muted small mb-0">Process customer transactions</p>
    </div>
    <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-house"></i> Dashboard
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Error!</strong> <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Success!</strong> <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<style>
    .sale-items-card {
        border: 2px solid #FFF4C6;
        border-radius: 2rem;
    }
    .sale-items-card .card-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-radius: 2rem 2rem 0 0;
        padding: 1rem 1.5rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .sale-summary-card {
        border: 2px solid #FFF4C6;
        border-radius: 2rem;
    }
    .sale-summary-card .card-header {
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border-radius: 2rem 2rem 0 0;
        padding: 1rem 1.5rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    #sale-items-table {
        margin-bottom: 0;
    }
    #sale-items-table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 3px solid var(--secondary-color);
        font-weight: 700;
        color: #333;
        padding: 1rem 0.75rem;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    #sale-items-table tbody tr {
        transition: background-color 0.2s ease;
    }
    #sale-items-table tbody tr:hover {
        background-color: #fff9e5;
    }
    #sale-items-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    .product-select {
        min-width: 200px;
        border-radius: 1rem;
        font-weight: 500;
    }
    .quantity-input {
        border-radius: 1rem;
        text-align: center;
        font-weight: 600;
        max-width: 120px;
    }
    .stock-cell {
        color: var(--primary-color);
        font-size: 1rem;
    }
    .price-cell, .line-total {
        color: #198754;
        font-size: 1rem;
        font-weight: 700;
    }
    .remove-line {
        border-radius: 1rem;
        transition: all 0.2s ease;
    }
    .remove-line:hover {
        transform: scale(1.1);
    }
    #add-line-btn {
        border-radius: 2rem;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        margin-top: 1rem;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        transition: all 0.2s ease;
    }
    #add-line-btn:hover {
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 46, 46, 0.2);
    }
    .sale-summary-card .form-control,
    .sale-summary-card .form-select {
        border-radius: 1rem;
        border: 2px solid #e0e0e0;
        transition: border-color 0.2s ease;
    }
    .sale-summary-card .form-control:focus,
    .sale-summary-card .form-select:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 212, 59, 0.25);
    }
    .sale-summary-card hr {
        border-top: 2px solid #FFD43B;
        opacity: 0.5;
        margin: 1.5rem 0;
    }
    #subtotal-display {
        color: #333;
        font-size: 1.1rem;
    }
    #total-display {
        color: #198754;
        font-size: 2rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(25, 135, 84, 0.1);
    }
    #submit-btn {
        border-radius: 2rem;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 1rem;
        background: linear-gradient(135deg, #198754 0%, #146c43 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        transition: all 0.2s ease;
    }
    #submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(25, 135, 84, 0.4);
    }
    #submit-btn:disabled {
        opacity: 0.7;
        transform: none;
    }
</style>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4 sale-items-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-cart-check"></i> Sale Items</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="sale-items-table">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Available Stock</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Line Total</th>
                                <th class="text-center" style="width: 60px;"></th>
                            </tr>
                        </thead>
                        <tbody id="sale-lines">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <tr class="sale-line">
                                    <td>
                                        <select name="product_id[]" class="form-select form-select-sm product-select">
                                            <option value="">-- Select Product --</option>
                                            <?php foreach ($products as $p): ?>
                                                <option value="<?= $p['id'] ?>"
                                                        data-price="<?= $p['unit_price'] ?>"
                                                        data-stock="<?= $p['total_stock'] ?>"
                                                        data-name="<?= esc($p['name']) ?>">
                                                    <?= esc($p['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="stock-cell text-center fw-bold">-</td>
                                    <td class="price-cell text-end">₱0.00</td>
                                    <td>
                                        <input type="number" step="0.01" name="quantity[]" class="form-control form-control-sm quantity-input" value="0" min="0">
                                    </td>
                                    <td class="line-total text-end fw-bold">₱0.00</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger remove-line" title="Remove">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-outline-primary" id="add-line-btn">
                    <i class="bi bi-plus-circle"></i> Add Another Item
                </button>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm sticky-top sale-summary-card" style="top: 20px;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Sale Summary</h5>
            </div>
            <div class="card-body">
                <form method="post" id="sale-form" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--primary-color);">
                            <i class="bi bi-person"></i> Customer Name
                        </label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Optional (leave blank for walk-in)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--primary-color);">
                            <i class="bi bi-credit-card"></i> Payment Method
                        </label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" selected>Cash</option>
                            <option value="card">Card</option>
                            <option value="check">Check</option>
                            <option value="online">Online Transfer</option>
                            <option value="digital">Digital Payment</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold" style="color: #666;">Subtotal:</span>
                            <strong id="subtotal-display" class="fs-5">₱0.00</strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold" style="color: var(--primary-color);">
                            <i class="bi bi-tag"></i> Discount (₱)
                        </label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="0" id="discount-input" min="0">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--primary-color);">
                            <i class="bi bi-percent"></i> Tax (₱)
                        </label>
                        <input type="number" step="0.01" name="tax" class="form-control" value="0" id="tax-input" min="0">
                    </div>

                    <hr>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center p-3 rounded" style="background: linear-gradient(135deg, #fff9e5 0%, #fffbe9 100%); border: 2px solid var(--secondary-color);">
                            <span class="h5 mb-0 fw-bold" style="color: #333;">Total:</span>
                            <span id="total-display" class="mb-0">₱0.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-lg w-100" id="submit-btn">
                        <i class="bi bi-check-circle"></i> Save Sale
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const saleForm = document.getElementById('sale-form');
    const addLineBtn = document.getElementById('add-line-btn');
    const saleLines = document.getElementById('sale-lines');
    const discountInput = document.getElementById('discount-input');
    const taxInput = document.getElementById('tax-input');

    function updateLineTotal(row) {
        const priceCell = row.querySelector('.price-cell');
        const quantityInput = row.querySelector('.quantity-input');
        const lineTotalCell = row.querySelector('.line-total');

        const price = parseFloat(priceCell.textContent.replace('₱', '')) || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const lineTotal = price * quantity;

        lineTotalCell.textContent = '₱' + lineTotal.toFixed(2);
        updateTotals();
    }

    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.sale-line').forEach(row => {
            const lineTotalText = row.querySelector('.line-total').textContent;
            const lineTotal = parseFloat(lineTotalText.replace('₱', '')) || 0;
            subtotal += lineTotal;
        });

        const discount = parseFloat(discountInput.value) || 0;
        const tax = parseFloat(taxInput.value) || 0;
        const total = subtotal - discount + tax;

        document.getElementById('subtotal-display').textContent = '₱' + subtotal.toFixed(2);
        document.getElementById('total-display').textContent = '₱' + total.toFixed(2);
    }

    function setupRowHandlers(row) {
        const productSelect = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const removeBtn = row.querySelector('.remove-line');

        productSelect.addEventListener('change', function() {
            const option = this.selectedOptions[0];
            const stockCell = row.querySelector('.stock-cell');
            const priceCell = row.querySelector('.price-cell');

            if (option.value) {
                const stock = parseFloat(option.dataset.stock);
                stockCell.innerHTML = '<span class="badge ' + (stock <= 0 ? 'bg-danger' : (stock < 10 ? 'bg-warning text-dark' : 'bg-success')) + '">' + stock.toFixed(2) + '</span>';
                priceCell.textContent = '₱' + parseFloat(option.dataset.price).toFixed(2);
                quantityInput.max = option.dataset.stock;
            } else {
                stockCell.textContent = '-';
                priceCell.textContent = '₱0.00';
                quantityInput.value = 0;
            }
            updateLineTotal(row);
        });

        quantityInput.addEventListener('change', function() {
            updateLineTotal(row);
        });

        removeBtn.addEventListener('click', function() {
            if (saleLines.querySelectorAll('.sale-line').length > 1) {
                row.remove();
                updateTotals();
            } else {
                alert('At least one item is required.');
            }
        });
    }

    // Setup existing rows
    document.querySelectorAll('.sale-line').forEach(row => {
        setupRowHandlers(row);
    });

    // Add new line
    addLineBtn.addEventListener('click', function() {
        const newRow = document.querySelector('.sale-line').cloneNode(true);
        newRow.querySelectorAll('input, select').forEach(el => {
            el.value = el.type === 'number' ? '0' : '';
        });
        const stockCell = newRow.querySelector('.stock-cell');
        if (stockCell) stockCell.textContent = '-';
        newRow.querySelector('.price-cell').textContent = '₱0.00';
        newRow.querySelector('.line-total').textContent = '₱0.00';
        
        saleLines.appendChild(newRow);
        setupRowHandlers(newRow);
    });

    // Update totals on discount/tax change
    discountInput.addEventListener('change', updateTotals);
    taxInput.addEventListener('change', updateTotals);

    // Toast notification functions
    function showSuccess(message) {
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
            delay: 5000
        });
        toast.show();
        
        toastElement.addEventListener('hidden.bs.toast', function() {
            successToast.style.display = 'none';
        });
    }
    
    function showError(message) {
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

    // Form submission - AJAX like Add Product
    if (saleForm) {
        saleForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            
            // Validate that at least one product with quantity > 0 is selected
            let hasValidItems = false;
            document.querySelectorAll('.sale-line').forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                if (productSelect && productSelect.value && quantityInput && parseFloat(quantityInput.value) > 0) {
                    hasValidItems = true;
                }
            });
            
            if (!hasValidItems) {
                showError('Please add at least one product with quantity greater than 0.');
                return false;
            }
            
            // Validate stock availability
            let stockError = false;
            let stockMessage = '';
            document.querySelectorAll('.sale-line').forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                
                if (productSelect && productSelect.value && quantityInput) {
                    const selectedOption = productSelect.selectedOptions[0];
                    const availableStock = parseFloat(selectedOption.dataset.stock) || 0;
                    const requestedQty = parseFloat(quantityInput.value) || 0;
                    
                    if (requestedQty > availableStock) {
                        stockError = true;
                        stockMessage = `Not enough stock for ${selectedOption.dataset.name}. Available: ${availableStock}`;
                    }
                }
            });
            
            if (stockError) {
                showError(stockMessage);
                return false;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('submit-btn');
            const originalHtml = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            }
            
            // Prepare form data
            const formData = new FormData(saleForm);
            
            // Collect all product IDs and quantities from the table
            const productIds = [];
            const quantities = [];
            document.querySelectorAll('.sale-line').forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                if (productSelect && productSelect.value && quantityInput && parseFloat(quantityInput.value) > 0) {
                    productIds.push(productSelect.value);
                    quantities.push(quantityInput.value);
                }
            });
            
            // Add product IDs and quantities to form data
            productIds.forEach(id => {
                formData.append('product_id[]', id);
            });
            quantities.forEach(qty => {
                formData.append('quantity[]', qty);
            });
            
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
            fetch('<?= site_url('sales/create') ?>', {
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
                    showSuccess(data.message || 'Sale recorded successfully!');
                    // Clear form after 1.5 seconds and redirect
                    setTimeout(() => {
                        saleForm.reset();
                        // Reset all sale lines
                        document.querySelectorAll('.sale-line').forEach((row, index) => {
                            if (index > 0) {
                                row.remove();
                            } else {
                                const productSelect = row.querySelector('.product-select');
                                const quantityInput = row.querySelector('.quantity-input');
                                const stockCell = row.querySelector('.stock-cell');
                                const priceCell = row.querySelector('.price-cell');
                                const lineTotalCell = row.querySelector('.line-total');
                                
                                if (productSelect) productSelect.value = '';
                                if (quantityInput) quantityInput.value = '0';
                                if (stockCell) stockCell.textContent = '-';
                                if (priceCell) priceCell.textContent = '₱0.00';
                                if (lineTotalCell) lineTotalCell.textContent = '₱0.00';
                            }
                        });
                        updateTotals();
                        // Optionally redirect to sales reports
                        // window.location.href = '<?= site_url('reports/sales') ?>';
                    }, 1500);
                } else {
                    showError(data.message || 'Failed to record sale.');
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showError('Error: ' + error.message);
            })
            .finally(() => {
                // Re-enable button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                }
            });
            
            return false;
        });
    }
});
</script>
