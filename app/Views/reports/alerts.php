<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Inventory Alerts</h2>
    <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-house"></i> Dashboard
    </a>
</div>

<div class="row g-4">
    <!-- Low Stock Alert -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Low Stock Items
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($lowStock)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Threshold</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lowStock as $p): ?>
                                    <tr class="table-warning">
                                        <td>
                                            <strong><?= esc($p['name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($p['category'] ?? 'N/A') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= number_format($p['total_stock'], 2) ?> <?= esc($p['unit']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?= number_format($p['low_stock_threshold'], 2) ?> <?= esc($p['unit']) ?>
                                        </td>
                                        <td>
                                            <a href="<?= site_url('inventory/quick-add?product_id=' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-plus-lg"></i> Add Stock
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                        <p class="text-muted mt-2">All products are well-stocked!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Expiring Soon Alert -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-x-fill me-2"></i>Expiring Soon / Expired
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($expiringSoon)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th>Remaining Qty</th>
                                    <th>Expiry Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($expiringSoon as $b): ?>
                                    <?php 
                                    $isExpired = strtotime($b['expiry_date']) < time();
                                    $daysUntilExpiry = ceil((strtotime($b['expiry_date']) - time()) / (60 * 60 * 24));
                                    ?>
                                    <tr class="<?= $isExpired ? 'table-danger' : 'table-warning' ?>">
                                        <td>
                                            <strong><?= esc($b['product_name']) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= number_format($b['remaining_quantity'], 2) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= esc($b['expiry_date']) ?></strong>
                                        </td>
                                        <td>
                                            <?php if ($isExpired): ?>
                                                <span class="badge bg-danger">EXPIRED</span>
                                            <?php elseif ($daysUntilExpiry <= 1): ?>
                                                <span class="badge bg-danger">EXPIRES TODAY</span>
                                            <?php elseif ($daysUntilExpiry <= 3): ?>
                                                <span class="badge bg-warning text-dark">EXPIRES IN <?= $daysUntilExpiry ?> DAYS</span>
                                            <?php else: ?>
                                                <span class="badge bg-info">EXPIRES IN <?= $daysUntilExpiry ?> DAYS</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($daysUntilExpiry <= 3): ?>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger discard-btn" 
                                                        data-batch-id="<?= $b['id'] ?>"
                                                        data-product="<?= esc($b['product_name']) ?>"
                                                        data-quantity="<?= $b['remaining_quantity'] ?>"
                                                        data-expiry-date="<?= esc($b['expiry_date']) ?>">
                                                    <i class="bi bi-trash"></i> Discard
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="bi bi-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                        <p class="text-muted mt-2">No batches near expiry!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Discard Confirmation Modal -->
<div class="modal fade" id="discardModal" tabindex="-1" aria-labelledby="discardModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="discardModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Discard
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to discard this stock batch?</p>
                <div class="alert alert-warning">
                    <strong>Product:</strong> <span id="discardProduct"></span><br>
                    <strong>Quantity:</strong> <span id="discardQuantity"></span><br>
                    <strong>Expiry Date:</strong> <span id="discardExpiryDate"></span><br>
                    <strong>Note:</strong> This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDiscardBtn">
                    <i class="bi bi-trash"></i> Discard Stock
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Summary Stats -->
<div class="row g-3 mt-2">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Low Stock Items</h6>
                <h3 class="text-warning mb-0"><?= count($lowStock ?? []) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Expiring Soon</h6>
                <h3 class="text-danger mb-0"><?= count($expiringSoon ?? []) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-info">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Action Required</h6>
                <h3 class="text-info mb-0"><?= (count($lowStock ?? []) + count($expiringSoon ?? [])) ?></h3>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const discardBtns = document.querySelectorAll('.discard-btn');
    const discardModal = new bootstrap.Modal(document.getElementById('discardModal'));
    const confirmDiscardBtn = document.getElementById('confirmDiscardBtn');
    
    let selectedBatchId = null;
    
    discardBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            selectedBatchId = this.getAttribute('data-batch-id');
            const product = this.getAttribute('data-product');
            const quantity = this.getAttribute('data-quantity');
            const expiryDate = this.getAttribute('data-expiry-date');
            
            document.getElementById('discardProduct').textContent = product;
            document.getElementById('discardQuantity').textContent = quantity + ' units';
            document.getElementById('discardExpiryDate').textContent = expiryDate;
            
            discardModal.show();
        });
    });
    
    confirmDiscardBtn.addEventListener('click', function() {
        if (!selectedBatchId) return;
        
        // Disable button and show loading
        const originalHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Discarding...';
        
        // Prepare form data
        const formData = new FormData();
        formData.append('batch_id', selectedBatchId);
        
        // Get CSRF token
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
        fetch('<?= site_url('inventory/discard-batch') ?>', {
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
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Hide modal
                discardModal.hide();
                
                // Show success message
                alert('Stock batch discarded successfully!');
                
                // Reload page to update the list
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                alert('Error: ' + (data.message || 'Failed to discard stock batch.'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Error: ' + error.message);
        })
        .finally(() => {
            // Re-enable button
            this.disabled = false;
            this.innerHTML = originalHtml;
        });
    });
});
</script>
