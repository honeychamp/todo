<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #<?= $invoice['id'] ?></title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            padding-top: 50px;
        }
        .invoice-card {
            background: white;
            width: 300px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 18px; font-weight: bold; }
        .header p { margin: 2px 0; font-size: 12px; }
        .details {
            text-align: left;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            font-size: 12px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }
        .meta h5 {
            margin: 0 0 5px 0;
            font-size: 13px;
            font-weight: bold;
        }
        .meta p {
            margin: 0;
            font-size: 12px;
        }
        .item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }
        .total-row {
            border-top: 1px dashed #000;
            padding-top: 10px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 20px;
            font-size: 10px;
            text-align: center;
        }
        @media print {
            body { background: white; padding: 0; }
            .invoice-card { box-shadow: none; width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-card">
        <div class="header">
            <h2><?= strtoupper(esc(get_setting('pharmacy_name', 'GALAXY PHARMACY'))) ?></h2>
            <p><?= esc(get_setting('pharmacy_address', '123 Main Street, City Center')) ?></p>
            <p>Tel: <?= esc(get_setting('pharmacy_phone', '+123 456 7890')) ?></p>
        </div>
        
        <div class="details">
            <div><strong>Inv #:</strong> <?= str_pad($invoice['id'], 6, '0', STR_PAD_LEFT) ?></div>
            <div><strong>Date:</strong> <?= date('d/m/Y h:i A', strtotime($invoice['sale_date'])) ?></div>
            <div><strong>Cust:</strong> <?= esc($invoice['customer_name'] ?: 'Cash Customer') ?></div>
            <?php if($invoice['customer_phone']): ?>
                <div><strong>Tel:</strong> <?= esc($invoice['customer_phone']) ?></div>
            <?php endif; ?>
        </div>

        <div style="border-bottom: 1px solid #eee; margin-bottom: 10px;"></div>

        <div class="item-row">
            <span style="flex: 1; text-align: left;"><?= esc($invoice['product_name']) ?></span>
        </div>
        <div class="item-row" style="color: #666;">
            <span><?= $invoice['qty'] ?> x Rs. <?= number_format($invoice['sale_price'], 2) ?></span>
            <span>Rs. <?= number_format($invoice['qty'] * $invoice['sale_price'], 2) ?></span>
        </div>

        <div style="border-bottom: 1px solid #eee; margin: 10px 0;"></div>

        <div class="total-row">
            <span>TOTAL</span>
            <span>Rs. <?= number_format($invoice['qty'] * $invoice['sale_price'], 2) ?></span>
        </div>

        <div class="footer">
            <p>THANK YOU FOR YOUR VISIT!</p>
            <p>Please keep this receipt for any returns.</p>
        </div>
    </div>
</body>
</html>
