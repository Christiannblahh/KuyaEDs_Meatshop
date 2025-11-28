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
                                            <a href="<?= site_url('inventory/add?product_id=' . $p['id']) ?>" class="btn btn-sm btn-outline-primary">
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
