<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #6366f1, #0ea5e9);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Total Investment (Cost)</div>
            <?php 
                $totalInvested = array_sum(array_map(function($p) { return $p['cost'] * $p['initial_qty']; }, $purchases));
            ?>
            <h3 class="text-white fw-800 m-0 mt-1">Rs. <?= number_format($totalInvested, 2) ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Total Sale Value (Original)</div>
            <?php 
                $totalSaleValueOriginal = array_sum(array_map(function($p) { return $p['price'] * $p['initial_qty']; }, $purchases));
            ?>
            <h3 class="text-white fw-800 m-0 mt-1">Rs. <?= number_format($totalSaleValueOriginal, 2) ?></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Potential Profit (Est.)</div>
            <h3 class="text-white fw-800 m-0 mt-1">Rs. <?= number_format($totalSaleValueOriginal - $totalInvested, 2) ?></h3>
        </div>
    </div>
</div>

<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Stock Purchase Log</h5>
                    <p class="text-muted small m-0 mt-1">Record and track stock batches received from vendors.</p>
                </div>
                <a href="<?= base_url('stocks/select_vendor') ?>" class="btn btn-vibrant rounded-pill px-4">
                    <i class="fas fa-plus me-2"></i> Add Stock
                </a>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">ID & Date</th>
                                <th class="border-0 py-3">Batch & Vendor</th>
                                <th class="border-0 py-3">Product Name</th>
                                <th class="border-0 py-3">MFG & EXP Date</th>
                                <th class="border-0 py-3">Per Unit Price</th>
                                <th class="border-0 py-3 text-center">Qty</th>
                                <th class="border-0 py-3">Pricing Analysis</th>
                                <th class="border-0 py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($purchases)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No stock records found.</td></tr>
                            <?php else: ?>
                                <?php foreach($purchases as $purchase): ?>
                                    <?php 
                                        $invested = $purchase['cost'] * $purchase['initial_qty'];
                                        $revenue = $purchase['batch_revenue'] ?? 0;
                                        $items_sold = $purchase['sold_qty'] ?? 0;
                                        $current_stock_value = $purchase['cost'] * $purchase['qty'];
                                    ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-800 text-primary">ID: <?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                            <div class="text-muted small" style="font-size: 11px;">
                                                <?php if($purchase['created_at']): ?>
                                                    <i class="far fa-calendar-alt me-1"></i> <?= date('d M, Y', strtotime($purchase['created_at'])) ?><br>
                                                    <i class="far fa-clock me-1"></i> <?= date('h:i A', strtotime($purchase['created_at'])) ?>
                                                <?php else: ?>
                                                    <i class="far fa-calendar-alt me-1"></i> No Date
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold small">Batch: <code><?= esc($purchase['batch_id']) ?></code></div>
                                            <div class="text-muted small"><?= esc($purchase['vendor_name'] ?: 'N/A') ?></div>
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
                                            <div class="small fw-bold text-muted">Cost: <span class="text-dark">Rs. <?= number_format($purchase['cost'], 2) ?></span></div>
                                            <div class="small fw-bold text-muted">Sale: <span class="text-success">Rs. <?= number_format($purchase['price'], 2) ?></span></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold fs-6 text-dark"><?= esc($purchase['initial_qty']) ?> <span class="text-muted small" style="font-size: 10px;">Bought</span></div>
                                            <div class="badge bg-light text-dark border mt-1"><?= esc($purchase['qty']) ?> <span class="small opacity-75">Left</span></div>
                                            <?php if($items_sold > 0): ?>
                                                <div class="text-primary small mt-1" style="font-size: 10px;"><?= $items_sold ?> Sold</div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-muted">Invested: <span class="text-dark float-end">Rs. <?= number_format($invested, 2) ?></span></div>
                                            <div class="small fw-bold text-muted">Earned: <span class="text-success float-end">Rs. <?= number_format($revenue, 2) ?></span></div>
                                            <div class="small fw-bold text-primary mt-1 border-top pt-1">Remaining: <span class="float-end">Rs. <?= number_format($current_stock_value, 2) ?></span></div>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('stocks/purchase_invoice/'.$purchase['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3 me-1">
                                                <i class="fas fa-print"></i> Invoice
                                            </a>
                                            <button class="btn btn-sm btn-outline-warning border-0 rounded-pill px-3 me-1 btn-edit-stock" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editPurchaseModal"
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
    </div>
</div>

<!-- Edit Record Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">Edit Stock Record</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('stocks/update_purchase') ?>" method="POST">
                <input type="hidden" name="id" id="edit_stock_id">
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
                                <option value="<?= $prod['id'] ?>">
                                    <?= esc($prod['name']) ?> [<?= esc($prod['unit_value']) ?> <?= esc($prod['unit']) ?>]
                                </option>
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
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('edit_stock_id').value = this.getAttribute('data-id');
                document.getElementById('edit_batch_id').value = this.getAttribute('data-batch');
                document.getElementById('edit_vendor_id').value = this.getAttribute('data-vendor');
                document.getElementById('edit_product_id').value = this.getAttribute('data-product');
                document.getElementById('edit_mfg_date').value = this.getAttribute('data-mfg');
                document.getElementById('edit_exp_date').value = this.getAttribute('data-exp');
                document.getElementById('edit_qty').value = this.getAttribute('data-qty');
                document.getElementById('edit_cost').value = this.getAttribute('data-cost');
                document.getElementById('edit_price').value = this.getAttribute('data-price');
            });
        });
    });
</script>

<?= $this->endSection() ?>
