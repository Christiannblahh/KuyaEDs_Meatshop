<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Sales Reports</h2>
    <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-house"></i> Dashboard
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Report Type</label>
                <select name="range" class="form-select" onchange="this.form.submit()">
                    <option value="daily" <?= ($range ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
                    <option value="monthly" <?= ($range ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                </select>
            </div>

            <?php if (($range ?? '') === 'monthly'): ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Year</label>
                    <input type="number" name="year" class="form-control" value="<?= esc($year ?? date('Y')) ?>" min="2020">
                </div>
            <?php else: ?>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Start Date</label>
                    <input type="date" name="start" class="form-control" value="<?= esc($start ?? date('Y-m-d')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">End Date</label>
                    <input type="date" name="end" class="form-control" value="<?= esc($end ?? date('Y-m-d')) ?>">
                </div>
            <?php endif; ?>

            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><?= ($range ?? '') === 'monthly' ? 'Monthly Sales' : 'Daily Sales' ?></h5>
    </div>
    <div class="card-body">
        <?php if (!empty($rows)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th><?= ($range ?? '') === 'monthly' ? 'Month' : 'Date' ?></th>
                            <th class="text-center">Transactions</th>
                            <th class="text-end">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0;
                        $totalTransactions = 0;
                        foreach ($rows as $row): 
                            $total = (float) ($row['total'] ?? 0);
                            $grandTotal += $total;
                            $totalTransactions += (int) ($row['transaction_count'] ?? 0);
                        ?>
                            <tr>
                                <td>
                                    <strong><?= esc($row['month'] ?? $row['date']) ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= (int) ($row['transaction_count'] ?? 0) ?></span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success">₱<?= number_format($total, 2) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light fw-bold">
                            <td>TOTAL</td>
                            <td class="text-center">
                                <span class="badge bg-primary"><?= $totalTransactions ?></span>
                            </td>
                            <td class="text-end">
                                <span class="badge bg-primary">₱<?= number_format($grandTotal, 2) ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card border-success">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Revenue</h6>
                            <h3 class="text-success mb-0">₱<?= number_format($grandTotal, 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-info">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Total Transactions</h6>
                            <h3 class="text-info mb-0"><?= $totalTransactions ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-warning">
                        <div class="card-body">
                            <h6 class="text-muted mb-1">Average <?= ($range ?? '') === 'monthly' ? 'Monthly' : 'Daily' ?> Sales</h6>
                            <h3 class="text-warning mb-0">₱<?= number_format(count($rows) > 0 ? $grandTotal / count($rows) : 0, 2) ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-graph-up" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3 mb-2">No sales data available for the selected period.</p>
                <?php if (($range ?? '') === 'daily'): ?>
                    <p class="text-muted small">
                        Selected period: <?= esc($start ?? date('Y-m-d')) ?> to <?= esc($end ?? date('Y-m-d')) ?>
                    </p>
                <?php else: ?>
                    <p class="text-muted small">
                        Selected year: <?= esc($year ?? date('Y')) ?>
                    </p>
                <?php endif; ?>
                <p class="text-muted small mt-2">
                    <a href="<?= site_url('sales/create') ?>" class="text-decoration-none">
                        <i class="bi bi-plus-circle"></i> Record a sale to see data here
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
