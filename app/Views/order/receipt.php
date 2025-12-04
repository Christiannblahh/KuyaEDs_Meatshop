<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-white text-center border-bottom">
                <h3 class="mb-1">Kuya ED's Meatshop</h3>
                <p class="text-muted mb-0">Sales Receipt</p>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h4>Order #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></h4>
                    <p class="text-muted mb-0">
                        <i class="bi bi-calendar3"></i> 
                        <?= date('F j, Y g:i:s A', strtotime($sale['sale_date'])) ?>
                    </p>
                </div>

                <div class="border-top border-bottom py-3 mb-3">
                    <div class="row fw-bold">
                        <div class="col-6">Item</div>
                        <div class="col-2 text-center">Qty</div>
                        <div class="col-2 text-end">Price</div>
                        <div class="col-2 text-end">Total</div>
                    </div>
                </div>

                <?php if (!empty($sale['items'])): ?>
                    <?php foreach ($sale['items'] as $item): ?>
                        <div class="row mb-2">
                            <div class="col-6">
                                <div>
                                    <strong>Product #<?= $item['product_id'] ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        ₱<?= number_format($item['unit_price'], 2) ?> × 
                                        <?= number_format($item['quantity'], 2) ?> units
                                    </small>
                                </div>
                            </div>
                            <div class="col-2 text-center"><?= number_format($item['quantity'], 2) ?></div>
                            <div class="col-2 text-end">₱<?= number_format($item['unit_price'], 2) ?></div>
                            <div class="col-2 text-end">
                                <strong>₱<?= number_format($item['line_total'], 2) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <div class="border-top pt-3 mt-3">
                    <div class="row">
                        <div class="col-6">
                            <strong>Subtotal:</strong>
                        </div>
                        <div class="col-6 text-end">
                            ₱<?= number_format($sale['subtotal'], 2) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Discount:</strong>
                        </div>
                        <div class="col-6 text-end">
                            ₱<?= number_format($sale['discount'], 2) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <strong>Tax:</strong>
                        </div>
                        <div class="col-6 text-end">
                            ₱<?= number_format($sale['tax'], 2) ?>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <strong>Total Amount:</strong>
                        </div>
                        <div class="col-6 text-end h5 text-primary">
                            ₱<?= number_format($sale['total_amount'], 2) ?>
                        </div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3">
                    <div class="row">
                        <div class="col-6">
                            <strong>Payment Method:</strong>
                        </div>
                        <div class="col-6 text-end">
                            <i class="bi bi-cash-coin"></i> Cash
                        </div>
                    </div>
                    <?php if (!empty($sale['customer_payment'])): ?>
                        <div class="row">
                            <div class="col-6">
                                <strong>Amount Paid:</strong>
                            </div>
                            <div class="col-6 text-end">
                                ₱<?= number_format($sale['customer_payment'], 2) ?>
                            </div>
                        </div>
                        <?php if (!empty($sale['change_amount']) && $sale['change_amount'] > 0): ?>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Change:</strong>
                                </div>
                                <div class="col-6 text-end h5 text-success">
                                    ₱<?= number_format($sale['change_amount'], 2) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="text-center mt-4 pt-3 border-top">
                    <p class="mb-1"><strong>Thank you for your business!</strong></p>
                    <p class="text-muted small mb-0">Please come again</p>
                </div>
            </div>
            <div class="card-footer bg-white text-center">
                <div class="btn-group" role="group">
                    <a href="<?= site_url('order/printReceipt/' . $sale['id']) ?>" class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-printer"></i> Print Receipt
                    </a>
                    <a href="<?= site_url('order/orders') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> All Orders
                    </a>
                    <a href="<?= site_url('order') ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> New Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>