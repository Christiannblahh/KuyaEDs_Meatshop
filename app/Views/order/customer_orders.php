<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Customer Orders</h2>
    <a href="<?= site_url('order') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> New Order
    </a>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (empty($sales) || count($sales) === 0): ?>
    <div class="text-center py-5">
        <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
        <h4 class="mt-3 text-muted">No customer orders found</h4>
        <p class="text-muted">Start taking customer orders to see them here.</p>
        <a href="<?= site_url('order') ?>" class="btn btn-primary">Take New Order</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($sales as $sale): ?>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="mb-0">
                                    <i class="bi bi-person-circle text-primary me-2"></i>
                                    <?= esc($sale['customer_name'] ?? 'Walk-in Customer') ?>
                                </h5>
                                <small class="text-muted">
                                    Order #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?> • 
                                    <?= date('M j, Y g:i A', strtotime($sale['sale_date'])) ?>
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <span class="badge bg-success fs-6">₱<?= number_format($sale['total_amount'], 2) ?></span>
                                <span class="badge bg-primary ms-2">
                                    <i class="bi bi-cash"></i> Cash
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($sale['items'])): ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Price</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sale['items'] as $item): ?>
                                            <tr>
                                                <td>
                                                    <strong>Product #<?= $item['product_id'] ?></strong>
                                                    <br>
                                                    <small class="text-muted">₱<?= number_format($item['unit_price'], 2) ?> per unit</small>
                                                </td>
                                                <td class="text-center"><?= number_format($item['quantity'], 2) ?></td>
                                                <td class="text-end">₱<?= number_format($item['unit_price'], 2) ?></td>
                                                <td class="text-end">
                                                    <strong>₱<?= number_format($item['line_total'], 2) ?></strong>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <?php if (!empty($sale['customer_payment'])): ?>
                                    <small class="text-muted">
                                        <i class="bi bi-cash-coin"></i> 
                                        Paid: ₱<?= number_format($sale['customer_payment'], 2) ?>
                                        <?php if (!empty($sale['change_amount']) && $sale['change_amount'] > 0): ?>
                                            • Change: ₱<?= number_format($sale['change_amount'], 2) ?>
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <div class="btn-group" role="group">
                                    <a href="<?= site_url('order/receipt/' . $sale['id']) ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-receipt"></i> View Receipt
                                    </a>
                                    <a href="<?= site_url('order/printReceipt/' . $sale['id']) ?>" class="btn btn-outline-secondary btn-sm" target="_blank">
                                        <i class="bi bi-printer"></i> Print
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>