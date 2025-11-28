<h2>Sales Reports</h2>

<form class="row g-3 mb-3">
    <div class="col-auto">
        <select name="range" class="form-select" onchange="this.form.submit()">
            <option value="daily" <?= ($range ?? '') === 'daily' ? 'selected' : '' ?>>Daily</option>
            <option value="monthly" <?= ($range ?? '') === 'monthly' ? 'selected' : '' ?>>Monthly</option>
        </select>
    </div>

    <?php if (($range ?? '') === 'monthly'): ?>
        <div class="col-auto">
            <input type="number" name="year" class="form-control" value="<?= esc($year ?? date('Y')) ?>">
        </div>
    <?php else: ?>
        <div class="col-auto">
            <input type="date" name="start" class="form-control" value="<?= esc($start ?? date('Y-m-d')) ?>">
        </div>
        <div class="col-auto">
            <input type="date" name="end" class="form-control" value="<?= esc($end ?? date('Y-m-d')) ?>">
        </div>
    <?php endif; ?>

    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>

<table class="table table-striped">
    <thead>
    <tr>
        <th><?= ($range ?? '') === 'monthly' ? 'Month' : 'Date' ?></th>
        <th>Total Sales</th>
    </tr>
    </thead>
    <tbody>
    <?php if (! empty($rows)): ?>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= esc($row['month'] ?? $row['date']) ?></td>
                <td><?= number_format($row['total'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="2">No data.</td></tr>
    <?php endif; ?>
    </tbody>
</table>


