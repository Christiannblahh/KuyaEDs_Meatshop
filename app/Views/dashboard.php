<div class="row mb-4">
    <div class="col">
        <h1 class="mb-3">Kuya EDs Meatshop</h1>
        <p class="text-muted">Simple sales and inventory system built on CodeIgniter 4.</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Products</h5>
                <p class="card-text">Manage meat products, prices, and low-stock thresholds.</p>
                <a href="<?= site_url('products') ?>" class="btn btn-primary">Go to Products</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Inventory</h5>
                <p class="card-text">Add stock with quantities and expiry dates, and see current levels.</p>
                <a href="<?= site_url('inventory') ?>" class="btn btn-primary">Go to Inventory</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Record Sale</h5>
                <p class="card-text">Record customer purchases with automatic total and stock deduction.</p>
                <a href="<?= site_url('sales/create') ?>" class="btn btn-primary">Record a Sale</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Sales Reports</h5>
                <p class="card-text">View daily and monthly sales totals for Kuya EDs.</p>
                <a href="<?= site_url('reports/sales') ?>" class="btn btn-outline-primary">View Sales Reports</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h5 class="card-title">Alerts</h5>
                <p class="card-text">See low-stock and near-expiry meat products so you can restock or sell fast.</p>
                <a href="<?= site_url('reports/alerts') ?>" class="btn btn-outline-danger">View Alerts</a>
            </div>
        </div>
    </div>
</div>


