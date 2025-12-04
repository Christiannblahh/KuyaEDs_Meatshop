<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - Order #<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @media print {
            body { margin: 0; padding: 20px; font-family: monospace; }
            .no-print { display: none; }
        }
        
        body { 
            font-family: 'Courier New', monospace; 
            max-width: 300px; 
            margin: 0 auto; 
            padding: 20px;
            line-height: 1.4;
        }
        
        .receipt { 
            text-align: center; 
        }
        
        .header { 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 15px; 
        }
        
        .footer { 
            border-top: 2px solid #000; 
            padding-top: 10px; 
            margin-top: 15px; 
        }
        
        .item-row { 
            display: flex; 
            justify-content: space-between; 
            margin: 5px 0; 
        }
        
        .total-row { 
            border-top: 1px solid #000; 
            padding-top: 5px; 
            margin-top: 10px; 
            font-weight: bold; 
        }
        
        .change-row { 
            font-size: 1.2em; 
            color: #008000; 
            font-weight: bold; 
        }
        
        h1, h2, h3 { margin: 5px 0; }
        
        .btn { 
            margin: 10px 5px; 
            padding: 10px 20px; 
            background: #007bff; 
            color: white; 
            border: none; 
            cursor: pointer; 
            border-radius: 5px; 
        }
        
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <h2>KUYA ED'S MEATSHOP</h2>
            <p>Sales Receipt</p>
        </div>
        
        <div class="info">
            <div class="item-row">
                <span>Order #:</span>
                <span><?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="item-row">
                <span>Date:</span>
                <span><?= date('M j, Y g:i:s A', strtotime($sale['sale_date'])) ?></span>
            </div>
        </div>
        
        <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>
        
        <div class="items">
            <?php if (!empty($sale['items'])): ?>
                <?php foreach ($sale['items'] as $item): ?>
                    <div style="margin-bottom: 10px;">
                        <div class="item-row">
                            <span style="font-weight: bold;">Product #<?= $item['product_id'] ?></span>
                        </div>
                        <div class="item-row">
                            <span><?= number_format($item['quantity'], 2) ?> × ₱<?= number_format($item['unit_price'], 2) ?></span>
                            <span>₱<?= number_format($item['line_total'], 2) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>
        
        <div class="totals">
            <div class="item-row">
                <span>Subtotal:</span>
                <span>₱<?= number_format($sale['subtotal'], 2) ?></span>
            </div>
            <div class="item-row">
                <span>Discount:</span>
                <span>₱<?= number_format($sale['discount'], 2) ?></span>
            </div>
            <div class="item-row">
                <span>Tax:</span>
                <span>₱<?= number_format($sale['tax'], 2) ?></span>
            </div>
            <div class="item-row total-row">
                <span>TOTAL:</span>
                <span>₱<?= number_format($sale['total_amount'], 2) ?></span>
            </div>
        </div>
        
        <div style="border-top: 1px dashed #000; margin: 15px 0;"></div>
        
        <div class="payment">
            <div class="item-row">
                <span>Payment Method:</span>
                <span>Cash</span>
            </div>
            <?php if (!empty($sale['customer_payment'])): ?>
                <div class="item-row">
                    <span>Amount Paid:</span>
                    <span>₱<?= number_format($sale['customer_payment'], 2) ?></span>
                </div>
                <?php if (!empty($sale['change_amount']) && $sale['change_amount'] > 0): ?>
                    <div class="item-row change-row">
                        <span>Change:</span>
                        <span>₱<?= number_format($sale['change_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="footer">
            <h3>THANK YOU!</h3>
            <p>Please come again</p>
        </div>
    </div>
    
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button class="btn" onclick="window.print()">Print Receipt</button>
        <button class="btn" onclick="window.close()">Close</button>
    </div>
    
    <script>
        // Auto-print when page loads
        window.onload = function() {
            // Uncomment the line below if you want auto-print
            // window.print();
        }
    </script>
</body>
</html>