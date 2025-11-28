<div class="row mb-4">
    <div class="col">
        <h1 class="mb-2">Kuya EDs Meatshop</h1>
        <p class="text-muted">Sales and Inventory Management System</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-start border-5 border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Products</h6>
                        <h3 class="mb-0"><?= $totalProducts ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-box-seam" style="font-size: 2rem; color: #0d6efd;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-5 border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Total Stock Value</h6>
                        <h3 class="mb-0">₱<?= number_format($totalStockValue ?? 0, 2) ?></h3>
                    </div>
                    <i class="bi bi-graph-up" style="font-size: 2rem; color: #198754;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-5 border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Low Stock Items</h6>
                        <h3 class="mb-0"><?= $lowStockCount ?? 0 ?></h3>
                    </div>
                    <i class="bi bi-exclamation-triangle" style="font-size: 2rem; color: #ffc107;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow-sm border-start border-5 border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Today's Sales</h6>
                        <h3 class="mb-0">₱<?= number_format($todaysSales ?? 0, 2) ?></h3>
                    </div>
                    <i class="bi bi-cash-coin" style="font-size: 2rem; color: #dc3545;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-lightning-fill text-warning me-2"></i>Quick Actions</h5>
                <div class="d-grid gap-2">
                    <a href="<?= site_url('order') ?>" class="btn btn-primary">
                        <i class="bi bi-cart3"></i> New Order
                    </a>
                    <a href="<?= site_url('sales/create') ?>" class="btn btn-success">
                        <i class="bi bi-receipt"></i> Record Sale
                    </a>
                    <a href="<?= site_url('inventory/add') ?>" class="btn btn-info">
                        <i class="bi bi-plus-circle"></i> Add Stock
                    </a>
                    <a href="<?= site_url('products/create') ?>" class="btn btn-secondary">
                        <i class="bi bi-plus-lg"></i> Add Product
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3"><i class="bi bi-info-circle-fill text-info me-2"></i>System Status</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <span class="badge bg-primary">Products</span>
                        <span class="float-end"><?= $totalProducts ?? 0 ?> items</span>
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-warning">Low Stock</span>
                        <span class="float-end"><?= $lowStockCount ?? 0 ?> items</span>
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-danger">Expiring Soon</span>
                        <span class="float-end"><?= $expiringCount ?? 0 ?> batches</span>
                    </li>
                    <li class="mb-2">
                        <span class="badge bg-success">Total Sales</span>
                        <span class="float-end">₱<?= number_format($totalSalesAllTime ?? 0, 2) ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main Features -->
<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm product-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-box-seam text-primary me-2"></i>Products</h5>
                <p class="card-text">Manage meat products, prices, categories, and low-stock thresholds.</p>
                <a href="<?= site_url('products') ?>" class="btn btn-primary">Manage Products</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm product-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-stack text-success me-2"></i>Inventory</h5>
                <p class="card-text">Add stock with quantities and expiry dates, track current inventory levels.</p>
                <a href="<?= site_url('inventory') ?>" class="btn btn-success">View Inventory</a>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm product-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-receipt text-info me-2"></i>Sales</h5>
                <p class="card-text">Record customer purchases with automatic totals and stock deduction.</p>
                <a href="<?= site_url('sales/create') ?>" class="btn btn-info">Record Sale</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <div class="card h-100 shadow-sm product-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-graph-up text-warning me-2"></i>Sales Reports</h5>
                <p class="card-text">View daily and monthly sales totals, track revenue trends.</p>
                <a href="<?= site_url('reports/sales') ?>" class="btn btn-outline-primary">View Reports</a>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 shadow-sm product-card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Alerts</h5>
                <p class="card-text">Monitor low-stock and near-expiry products to manage inventory efficiently.</p>
                <a href="<?= site_url('reports/alerts') ?>" class="btn btn-outline-danger">View Alerts</a>
            </div>
        </div>
    </div>
</div>
