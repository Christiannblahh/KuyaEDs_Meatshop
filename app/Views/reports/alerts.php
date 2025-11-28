<h2>Inventory Alerts</h2>

<h4 class="mt-3">Low Stock</h4>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Product</th>
        <th>Current Stock</th>
        <th>Threshold</th>
    </tr>
    </thead>
    <tbody>
    <?php if (! empty($lowStock)): ?>
        <?php foreach ($lowStock as $p): ?>
            <tr class="table-warning">
                <td><?= esc($p['name']) ?></td>
                <td><?= number_format($p['total_stock'], 2) ?></td>
                <td><?= number_format($p['low_stock_threshold'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="3">No low-stock items.</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<h4 class="mt-4">Expiring Soon / Expired</h4>
<table class="table table-striped">
    <thead>
    <tr>
        <th>Product</th>
        <th>Remaining Qty</th>
        <th>Expiry Date</th>
    </tr>
    </thead>
    <tbody>
    <?php if (! empty($expiringSoon)): ?>
        <?php foreach ($expiringSoon as $b): ?>
            <tr class="<?= (strtotime($b['expiry_date']) < time()) ? 'table-danger' : 'table-warning' ?>">
                <td><?= esc($b['product_name']) ?></td>
                <td><?= number_format($b['remaining_quantity'], 2) ?></td>
                <td><?= esc($b['expiry_date']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="3">No batches near expiry.</td></tr>
    <?php endif; ?>
    </tbody>
</table>


