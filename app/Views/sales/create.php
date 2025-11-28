<h2>Record Sale</h2>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form method="post">
    <table class="table">
        <thead>
        <tr>
            <th>Product</th>
            <th>Available Stock</th>
            <th>Unit Price</th>
            <th>Quantity</th>
        </tr>
        </thead>
        <tbody id="sale-lines">
        <?php for ($i = 0; $i < 5; $i++): ?>
            <tr>
                <td>
                    <select name="product_id[]" class="form-select">
                        <option value="">Select</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= $p['id'] ?>"
                                    data-price="<?= $p['unit_price'] ?>"
                                    data-stock="<?= $p['total_stock'] ?>">
                                <?= esc($p['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td class="stock-cell"></td>
                <td class="price-cell"></td>
                <td>
                    <input type="number" step="0.01" name="quantity[]" class="form-control">
                </td>
            </tr>
        <?php endfor; ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Save Sale</button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('#sale-lines select').forEach(function (select) {
            select.addEventListener('change', function () {
                const option = this.selectedOptions[0];
                const row = this.closest('tr');
                if (!option) return;
                row.querySelector('.price-cell').innerText = parseFloat(option.dataset.price || 0).toFixed(2);
                row.querySelector('.stock-cell').innerText = parseFloat(option.dataset.stock || 0).toFixed(2);
            });
        });
    });
</script>


