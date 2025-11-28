<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Record Sale</h2>
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

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Sale Items</h5>
            </div>
            <div class="card-body">
                <form method="post" id="sale-form">
                    <div class="table-responsive">
                        <table class="table table-hover" id="sale-items-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Available Stock</th>
                                    <th>Unit Price</th>
                                    <th>Quantity</th>
                                    <th>Line Total</th>
                                    <th></th>
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
                                        <td>
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
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm sticky-top" style="top: 20px;">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Sale Summary</h5>
            </div>
            <div class="card-body">
                <form method="post" id="sale-form">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Customer Name</label>
                        <input type="text" name="customer_name" class="form-control" placeholder="Optional">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="check">Check</option>
                            <option value="online">Online Transfer</option>
                        </select>
                    </div>

                    <hr>

                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span>
                            <strong id="subtotal-display">₱0.00</strong>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold">Discount (₱)</label>
                        <input type="number" step="0.01" name="discount" class="form-control" value="0" id="discount-input">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Tax (₱)</label>
                        <input type="number" step="0.01" name="tax" class="form-control" value="0" id="tax-input">
                    </div>

                    <hr>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span class="h5 mb-0">Total:</span>
                            <span class="h4 text-success mb-0" id="total-display">₱0.00</span>
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
                stockCell.textContent = parseFloat(option.dataset.stock).toFixed(2);
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
        newRow.querySelector('.stock-cell').textContent = '-';
        newRow.querySelector('.price-cell').textContent = '₱0.00';
        newRow.querySelector('.line-total').textContent = '₱0.00';
        
        saleLines.appendChild(newRow);
        setupRowHandlers(newRow);
    });

    // Update totals on discount/tax change
    discountInput.addEventListener('change', updateTotals);
    taxInput.addEventListener('change', updateTotals);

    // Form submission
    saleForm.addEventListener('submit', function(e) {
        const hasItems = Array.from(document.querySelectorAll('.product-select')).some(sel => sel.value);
        if (!hasItems) {
            e.preventDefault();
            alert('Please add at least one product to the sale.');
        }
    });
});
</script>
