<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$products_indexed = array_column($products, null, 'id');
$vendors_indexed  = array_column($vendors, null, 'id');
?>

<!-- JS Data -->
<script>
    const productsData = <?= json_encode(array_column($products, null, 'id')) ?>;
    const vendorsOptions = `<?php foreach($vendors as $v): ?><option value="<?= $v['id'] ?>"><?= esc($v['name']) ?></option><?php endforeach; ?>`;
    const productsOptions = `<?php foreach($products as $p): ?><option value="<?= $p['id'] ?>" data-cost="<?= $p['cost'] ?>"><?= esc($p['name']) ?> [<?= esc($p['unit_value']) ?> <?= esc($p['unit']) ?>]</option><?php endforeach; ?>`;
    const currentVendorId = <?= $vendor['id'] ?>;
</script>

<div class="row g-4 animate-up">

    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-800 m-0">
                    <i class="fas fa-building me-2 text-primary"></i><?= esc($vendor['name']) ?>
                </h4>
                <p class="text-muted small m-0">Vendor ka stock aur payment management</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="<?= base_url('stocks/select_vendor') ?>" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Vendors
                </a>
                <a href="<?= base_url('stocks/add?vendor_id=' . $vendor['id']) ?>" class="btn btn-vibrant rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i> Stock Add Karein
                </a>
                <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-money-bill-wave me-2"></i> Payment Karein
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="col-12">
            <div class="alert alert-success rounded-4 border-0 py-3" style="background: rgba(16,185,129,0.1);">
                <i class="fas fa-check-circle me-2 text-success"></i> <?= session()->getFlashdata('success') ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="col-12">
            <div class="alert alert-danger rounded-4 border-0 py-3" style="background: rgba(239,68,68,0.1);">
                <i class="fas fa-exclamation-circle me-2 text-danger"></i> <?= session()->getFlashdata('error') ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="col-md-4">
        <div class="premium-list p-4 text-center" style="background: linear-gradient(135deg, #6366f1, #0ea5e9);">
            <div class="text-white opacity-75 small fw-bold text-uppercase mb-1">Total Stock Cost</div>
            <h3 class="text-white fw-800 m-0">Rs. <?= number_format($totalStockCost, 2) ?></h3>
            <div class="text-white opacity-60 small mt-1">Is vendor se liya hua total maal</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center" style="background: linear-gradient(135deg, #10b981, #059669);">
            <div class="text-white opacity-75 small fw-bold text-uppercase mb-1">Total Paid</div>
            <h3 class="text-white fw-800 m-0">Rs. <?= number_format($totalPaid, 2) ?></h3>
            <div class="text-white opacity-60 small mt-1">Ab tak ki gayi payments</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center" style="background: linear-gradient(135deg, <?= $balanceDue > 0 ? '#ef4444, #dc2626' : '#6366f1, #0ea5e9' ?>);">
            <div class="text-white opacity-75 small fw-bold text-uppercase mb-1">
                <?= $balanceDue > 0 ? 'Baaki Due' : 'Completed' ?>
            </div>
            <h3 class="text-white fw-800 m-0">Rs. <?= number_format(abs($balanceDue), 2) ?></h3>
            <div class="text-white opacity-60 small mt-1">
                <?= $balanceDue > 0 ? 'Is vendor ko dene hain' : ($balanceDue < 0 ? 'Zyada pay kar diya' : 'Poora pay ho gaya!') ?>
            </div>
        </div>
    </div>

    <!-- Stock Table for this vendor -->
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Stock Records</h5>
                    <p class="text-muted small m-0 mt-1"><?= esc($vendor['name']) ?> se liya hua maal</p>
                </div>
                <span class="badge rounded-pill px-4 py-2 fw-bold" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    <?= count($purchases) ?> Records
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">ID & Date</th>
                            <th class="border-0 py-3">Batch</th>
                            <th class="border-0 py-3">Product</th>
                            <th class="border-0 py-3">MFG & EXP</th>
                            <th class="border-0 py-3">Unit Price</th>
                            <th class="border-0 py-3 text-center">Qty</th>
                            <th class="border-0 py-3">Total</th>
                            <th class="border-0 py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="fas fa-box-open fs-3 d-block mb-2 opacity-25"></i>
                                    Abhi koi stock record nahi. <a href="<?= base_url('stocks/add?vendor_id=' . $vendor['id']) ?>">Stock Add Karein</a>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($purchases as $purchase): ?>
                                <?php
                                    $rowTotal = $purchase['cost'] * $purchase['initial_qty'];
                                ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-800 text-primary">ID: <?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                        <div class="text-muted small" style="font-size:11px;">
                                            <?php if ($purchase['created_at']): ?>
                                                <i class="far fa-calendar-alt me-1"></i><?= date('d M, Y', strtotime($purchase['created_at'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?= esc($purchase['batch_id']) ?></code>
                                    </td>
                                    <td class="fw-bold">
                                        <?= esc($purchase['product_name']) ?>
                                        <div class="text-muted small fw-normal"><?= esc($purchase['product_unit_value']) ?> <?= esc($purchase['product_unit']) ?></div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">M: <span class="text-muted"><?= date('d/m/y', strtotime($purchase['manufacture_date'])) ?></span></div>
                                        <div class="small fw-bold">E: <span class="<?= (strtotime($purchase['expiry_date']) < strtotime('+3 months')) ? 'text-danger' : 'text-success' ?>"><?= date('d/m/y', strtotime($purchase['expiry_date'])) ?></span></div>
                                    </td>
                                    <td>
                                        <div class="small text-muted">Cost: <span class="text-dark fw-bold">Rs. <?= number_format($purchase['cost'], 2) ?></span></div>
                                        <div class="small text-muted">Sale: <span class="text-success fw-bold">Rs. <?= number_format($purchase['price'], 2) ?></span></div>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-bold fs-6"><?= esc($purchase['initial_qty']) ?> <span class="text-muted" style="font-size: 10px;">Purchased</span></div>
                                        <div class="badge bg-light text-dark border mt-1"><?= esc($purchase['qty']) ?> <span class="small opacity-75">Left</span></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">Rs. <?= number_format($rowTotal, 2) ?></div>
                                        <div class="text-muted small" style="font-size: 10px;">Total Invested</div>
                                    </td>
                                    <td class="text-end px-4">
                                        <a href="<?= base_url('stocks/purchase_invoice/'.$purchase['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3 me-1">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-warning border-0 rounded-pill px-3 me-1 btn-edit-stock"
                                                data-bs-toggle="modal" data-bs-target="#editStockModal"
                                                data-id="<?= $purchase['id'] ?>"
                                                data-batch="<?= esc($purchase['batch_id']) ?>"
                                                data-vendor="<?= $purchase['vendor_id'] ?>"
                                                data-product="<?= $purchase['product_id'] ?>"
                                                data-mfg="<?= $purchase['manufacture_date'] ?>"
                                                data-exp="<?= $purchase['expiry_date'] ?>"
                                                data-qty="<?= $purchase['qty'] ?>"
                                                data-cost="<?= $purchase['cost'] ?>"
                                                data-price="<?= $purchase['price'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="<?= base_url('stocks/delete_purchase/'.$purchase['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Delete this record?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800"><i class="fas fa-money-bill-wave me-2 text-success"></i>Payment History</h5>
                    <p class="text-muted small m-0 mt-1"><?= esc($vendor['name']) ?> ko ki gayi payments</p>
                </div>
                <button class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addPaymentModal">
                    <i class="fas fa-plus me-2"></i> New Payment
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Date</th>
                            <th class="border-0 py-3">Amount</th>
                            <th class="border-0 py-3">Note</th>
                            <th class="border-0 py-3 text-end px-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-hand-holding-dollar d-block fs-3 mb-2 opacity-25"></i>
                                    Abhi koi payment record nahi hai.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold"><?= date('d M, Y', strtotime($payment['payment_date'])) ?></div>
                                        <div class="text-muted small"><?= date('l', strtotime($payment['payment_date'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="fw-800 text-success" style="font-size:16px;">Rs. <?= number_format($payment['amount'], 2) ?></span>
                                    </td>
                                    <td class="text-muted small"><?= esc($payment['note'] ?: '—') ?></td>
                                    <td class="text-end px-4">
                                        <a href="<?= base_url('stocks/delete_payment/'.$payment['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Delete this payment?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($payments)): ?>
                        <tfoot>
                            <tr style="background:#f8fafc;">
                                <td class="px-4 fw-800">Total Paid</td>
                                <td class="fw-800 text-success" style="font-size:16px;">Rs. <?= number_format($totalPaid, 2) ?></td>
                                <td></td><td></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-money-bill-wave me-2 text-success"></i>
                    <?= esc($vendor['name']) ?> - Payment Darj Karein
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('stocks/add_payment') ?>" method="POST">
                <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Amount (Rs.)</label>
                        <input type="number" step="0.01" name="amount" class="form-control bg-light border-0" placeholder="5000.00" required style="padding:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control bg-light border-0" value="<?= date('Y-m-d') ?>" required style="padding:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Note (Optional)</label>
                        <input type="text" name="note" class="form-control bg-light border-0" placeholder="e.g. Cheque #1234" style="padding:12px;">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-success w-100 py-3 fw-bold rounded-4">
                        <i class="fas fa-check me-2"></i> Payment Save Karein
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Stock Modal -->
<div class="modal fade" id="editStockModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Edit Stock Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('stocks/update_purchase') ?>" method="POST">
                <input type="hidden" name="id" id="edit_stock_id">
                <input type="hidden" name="redirect_vendor_id" value="<?= $vendor['id'] ?>">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Batch ID</label>
                        <input type="text" class="form-control bg-light border-0" name="batch_id" id="edit_batch_id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Vendor</label>
                        <select class="form-select bg-light border-0" name="vendor_id" id="edit_vendor_id">
                            <option value="">Select Vendor...</option>
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>"><?= esc($v['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Product</label>
                        <select class="form-select bg-light border-0" name="product_id" id="edit_product_id" required>
                            <?php foreach($products as $prod): ?>
                                <option value="<?= $prod['id'] ?>"><?= esc($prod['name']) ?> [<?= esc($prod['unit_value']) ?> <?= esc($prod['unit']) ?>]</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label small fw-bold">MFG</label>
                            <input type="date" class="form-control bg-light border-0" name="manufacture_date" id="edit_mfg_date" required>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">EXP</label>
                            <input type="date" class="form-control bg-light border-0" name="expiry_date" id="edit_exp_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Remaining Units</label>
                        <input type="number" class="form-control bg-light border-0" name="qty" id="edit_qty" required>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="form-label small fw-bold">Unit Cost</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0" name="cost" id="edit_cost" required>
                        </div>
                        <div class="col">
                            <label class="form-label small fw-bold">Selling Price</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0" name="price" id="edit_price" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-premium w-100 py-3">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-edit-stock');
    editButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_stock_id').value    = this.dataset.id;
            document.getElementById('edit_batch_id').value   = this.dataset.batch;
            document.getElementById('edit_vendor_id').value  = this.dataset.vendor;
            document.getElementById('edit_product_id').value = this.dataset.product;
            document.getElementById('edit_mfg_date').value   = this.dataset.mfg;
            document.getElementById('edit_exp_date').value   = this.dataset.exp;
            document.getElementById('edit_qty').value        = this.dataset.qty;
            document.getElementById('edit_cost').value       = this.dataset.cost;
            document.getElementById('edit_price').value      = this.dataset.price;
        });
    });
});
</script>

<?= $this->endSection() ?>
