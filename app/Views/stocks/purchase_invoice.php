<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Receipt #<?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #1e293b; margin: 0; padding: 40px; background: #f8fafc; }
        .receipt { max-width: 800px; margin: auto; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f1f5f9; padding-bottom: 30px; margin-bottom: 30px; }
        .brand h1 { margin: 0; color: #6366f1; font-size: 24px; font-weight: 800; }
        .info { text-align: right; }
        .info div { font-size: 14px; color: #64748b; margin-top: 4px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .meta h5 { margin: 0 0 10px 0; color: #94a3b8; text-uppercase; font-size: 12px; letter-spacing: 1px; }
        .meta p { margin: 0; font-weight: 600; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        .totals { margin-left: auto; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 10px 0; font-size: 14px; }
        .grand-total { border-top: 2px solid #f1f5f9; margin-top: 10px; padding-top: 15px; font-weight: 800; font-size: 18px; color: #6366f1; }
        .footer { margin-top: 60px; text-align: center; color: #94a3b8; font-size: 12px; border-top: 1px dashed #e2e8f0; padding-top: 20px; }
        @media print {
            body { background: white; padding: 0; }
            .receipt { box-shadow: none; width: 100%; max-width: 100%; padding: 0; }
            .no-print { display: none; }
        }
        .btn-print { background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-weight: 600; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="no-print" style="max-width: 800px; margin: auto; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
    <a href="<?= base_url('stocks/purchase') ?>" class="btn-print" style="text-decoration: none; background: #64748b;">&#8592; Go Back</a>
    <button class="btn-print" onclick="window.print()">Print Receipt</button>
</div>

<div class="receipt">
    <div class="header">
        <div class="brand">
            <h1><?= strtoupper(esc(get_setting('pharmacy_name', 'GALAXY PHARMACY'))) ?></h1>
            <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b;">Stock Purchase Voucher</p>
        </div>
        <div class="info">
            <div style="font-weight: 800; color: #1e293b; font-size: 18px;">ID: #<?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></div>
            <?php if($purchase['created_at']): ?>
                <div>Date: <?= date('d M, Y', strtotime($purchase['created_at'])) ?></div>
                <div>Time: <?= date('h:i A', strtotime($purchase['created_at'])) ?></div>
            <?php else: ?>
                <div>Date: N/A</div>
            <?php endif; ?>
        </div>
    </div>

    <div class="meta">
        <div>
            <h5>Supplier Information</h5>
            <p><?= esc($purchase['vendor_name'] ?: 'Local Supplier') ?></p>
            <div style="font-size: 13px; color: #64748b; margin-top: 5px;">
                <?= esc($purchase['vendor_phone']) ?><br>
                <?= esc($purchase['vendor_address']) ?>
            </div>
        </div>
        <div style="text-align: right;">
            <h5>Batch Information</h5>
            <p>Batch ID: <?= esc($purchase['batch_id']) ?></p>
            <div style="font-size: 13px; color: #64748b; margin-top: 5px;">
                MFG: <?= date('d/m/Y', strtotime($purchase['manufacture_date'])) ?><br>
                EXP: <?= date('d/m/Y', strtotime($purchase['expiry_date'])) ?>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th>Unit Price</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div style="font-weight: 700;"><?= esc($purchase['product_name']) ?></div>
                    <div style="font-size: 12px; color: #64748b;"><?= esc($purchase['product_unit_value']) ?> <?= esc($purchase['product_unit']) ?> Strength</div>
                </td>
                <td>Rs. <?= number_format($purchase['cost'], 2) ?></td>
                <td style="text-align: center;"><?= $purchase['qty'] ?></td>
                <td style="text-align: right; font-weight: 700;">Rs. <?= number_format($purchase['cost'] * $purchase['qty'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span>Total Cost Price</span>
            <span>Rs. <?= number_format($purchase['cost'] * $purchase['qty'], 2) ?></span>
        </div>
        <div class="total-row" style="color: #10b981;">
            <span>Expected Sale Price</span>
            <span>Rs. <?= number_format($purchase['price'] * $purchase['qty'], 2) ?></span>
        </div>
        <div class="total-row grand-total">
            <span>Expected Profit</span>
            <span>Rs. <?= number_format(($purchase['price'] - $purchase['cost']) * $purchase['qty'], 2) ?></span>
        </div>
    </div>

    <div class="footer">
        <p>This is a computer generated purchase receipt.</p>
        <p>&copy; <?= date('Y') ?> Galaxy Pharmacy Management System</p>
    </div>
</div>

</body>
</html>
