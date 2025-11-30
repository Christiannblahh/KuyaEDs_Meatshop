<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Inventory</h2>
    <a href="<?= site_url('inventory/quick-add') ?>" class="btn btn-success">
        <i class="bi bi-table"></i> Quick Add Stock
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>Success!</strong> <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <script>
        // Show toast notification
        document.addEventListener('DOMContentLoaded', function() {
            const message = <?= json_encode(session()->getFlashdata('success')) ?>;
            if (message) {
                showSuccessNotification(message);
            }
        });
    </script>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($products)): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
        <p class="text-muted mt-3">No products found. Please add products first.</p>
        <a href="<?= site_url('products/create') ?>" class="btn btn-primary">Add Products</a>
    </div>
<?php else: ?>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Unit</th>
                        <th>Total Stock</th>
                        <th>Low Stock Threshold</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($products as $p): ?>
                        <?php 
                        $isLowStock = isset($p['total_stock']) && isset($p['low_stock_threshold']) && 
                                     (float)$p['total_stock'] < (float)$p['low_stock_threshold'] && 
                                     (float)$p['low_stock_threshold'] > 0;
                        ?>
                        <?php 
                        $totalStock = (float)($p['total_stock'] ?? 0);
                        $rowClass = $totalStock <= 0 ? 'table-danger' : ($isLowStock ? 'table-warning' : '');
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><strong><?= esc($p['name']) ?></strong></td>
                            <td><?= esc($p['category'] ?? 'N/A') ?></td>
                            <td><?= esc($p['unit'] ?? 'N/A') ?></td>
                            <td>
                                <?php 
                                $totalStock = (float)($p['total_stock'] ?? 0);
                                $stockClass = $totalStock <= 0 ? 'text-danger' : ($isLowStock ? 'text-warning' : 'text-success');
                                ?>
                                <span class="fw-bold <?= $stockClass ?>">
                                    <?= number_format($totalStock, 2) ?>
                                </span>
                            </td>
                            <td><?= number_format($p['low_stock_threshold'] ?? 0, 2) ?></td>
                            <td>
                                <?php 
                                $totalStock = (float)($p['total_stock'] ?? 0);
                                if ($totalStock <= 0): ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php elseif ($isLowStock): ?>
                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                <?php else: ?>
                                    <span class="badge bg-success">In Stock</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('inventory/quick-add') ?>" 
                                   class="btn btn-sm btn-outline-primary" 
                                   title="Add Stock">
                                    <i class="bi bi-plus-lg"></i> Add Stock
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div id="success-toast" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999; display: none;">
    <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>Success!</strong> <span id="toast-message"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
function showSuccessNotification(message) {
    const toastContainer = document.getElementById('success-toast');
    const toastElement = toastContainer.querySelector('.toast');
    const toastMessage = document.getElementById('toast-message');
    
    if (toastMessage) {
        toastMessage.textContent = message;
    }
    
    toastContainer.style.display = 'block';
    
    const toast = new bootstrap.Toast(toastElement, {
        autohide: true,
        delay: 4000
    });
    toast.show();
    
    // Hide container after toast is hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastContainer.style.display = 'none';
    });
}
</script>

