<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4 animate-wow">
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm">
            <div class="text-muted extra-small fw-bold text-uppercase">Total Cash Outlay</div>
            <?php $totalInvested = array_sum(array_map(fn($p) => $p['cost'] * $p['initial_qty'], $purchases)); ?>
            <h2 class="fw-900 m-0 mt-1 text-dark">Rs. <?= number_format($totalInvested, 2) ?></h2>
            <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-primary" style="width: 100%"></div>
            </div>
            <div class="small text-muted mt-2">Historical investment in stock</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #059669, #10b981);">
            <div class="opacity-75 extra-small fw-bold text-uppercase">Remaining Stock Value</div>
            <?php 
                $stockVal = array_sum(array_map(function($p) {
                    $sold = $p['sold_qty'] ?? 0;
                    return $p['cost'] * ($p['initial_qty'] - $sold);
                }, $purchases));
            ?>
            <h2 class="fw-900 m-0 mt-1">Rs. <?= number_format($stockVal, 2) ?></h2>
            <div class="progress mt-3 bg-white bg-opacity-25" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-white" style="width: <?= $totalInvested > 0 ? ($stockVal / $totalInvested) * 100 : 0 ?>%"></div>
            </div>
            <div class="small opacity-75 mt-2">Value of items still on shelf</div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12">
        <div class="premium-list p-4 bg-white border-0 shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <div class="text-muted extra-small fw-bold text-uppercase">Profit Potential (Est.)</div>
                <?php 
                    $potentialRev = array_sum(array_map(fn($p) => $p['price'] * $p['initial_qty'], $purchases));
                    $potentialProfit = $potentialRev - $totalInvested;
                ?>
                <h2 class="fw-900 m-0 mt-1 text-success">Rs. <?= number_format($potentialProfit, 2) ?></h2>
                <div class="small text-muted mt-2">Expected return on total investments</div>
            </div>
            <div class="text-end">
                <div class="h5 m-0 fw-bold text-primary"><?= number_format($totalInvested > 0 ? ($potentialProfit / $totalInvested) * 100 : 0, 1) ?>%</div>
                <div class="extra-small text-muted fw-bold">AVG MARGIN</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden">
            <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h5 class="m-0 fw-900 text-dark">Central Procurement Log</h5>
                    <p class="text-muted small m-0 mt-1">Audit and manage all incoming stock batches and vendor supplies.</p>
                </div>
                <div class="d-flex gap-3 align-items-center">
                    <form action="<?= base_url('purchases') ?>" method="GET" class="d-flex gap-2">
                        <select name="vendor_id" class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-4" onchange="this.form.submit()" style="min-width: 200px;">
                            <option value="">Filter by Vendor...</option>
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= ($selected_vendor == $v['id']) ? 'selected' : '' ?>><?= esc($v['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if($selected_vendor): ?>
                            <a href="<?= base_url('purchases') ?>" class="btn btn-sm btn-light rounded-circle"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>
                    <a href="<?= base_url('purchases/select_vendor') ?>" class="btn btn-dark rounded-pill px-4 py-2 fw-bold">
                        <i class="fas fa-plus me-2"></i> New Entry
                    </a>
                </div>
            </div>
            
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted extra-small text-uppercase">
                                <th class="border-0 px-5 py-4">Receipt & Vendor</th>
                                <th class="border-0 py-4">Product Inventory</th>
                                <th class="border-0 py-4">Date Range</th>
                                <th class="border-0 py-4 text-center">Batch Stock</th>
                                <th class="border-0 py-4">Pricing & ROI</th>
                                <th class="border-0 py-4 text-end px-5">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($purchases)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted h5">No procurement history found.</td></tr>
                            <?php else: ?>
                                <?php foreach($purchases as $purchase): ?>
                                    <?php 
                                        $invested = $purchase['cost'] * $purchase['initial_qty'];
                                        $soldCount = $purchase['sold_qty'] ?? 0;
                                        $remaining = $purchase['initial_qty'] - $soldCount;
                                        $margin = $purchase['cost'] > 0 ? (($purchase['price'] - $purchase['cost']) / $purchase['cost']) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="fw-800 text-dark">#<?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                            <a href="<?= base_url('purchases/vendor/'.$purchase['vendor_id']) ?>" class="text-decoration-none text-muted extra-small fw-bold text-uppercase hover-primary">
                                                <i class="fas fa-building-circle-check me-1"></i> <?= esc($purchase['vendor_name'] ?: 'Local Source') ?>
                                            </a>
                                            <div class="extra-small text-muted mt-1"><?= date('d M, Y', strtotime($purchase['created_at'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-900 fs-6 text-dark"><?= esc($purchase['product_name']) ?></div>
                                            <div class="badge bg-light text-dark border extra-small px-2">BATCH: <?= esc($purchase['batch_id']) ?></div>
                                        </td>
                                        <td>
                                            <div class="extra-small fw-bold text-muted">MFG: <span class="text-dark"><?= date('m/y', strtotime($purchase['manufacture_date'])) ?></span></div>
                                            <div class="extra-small fw-bold text-muted">EXP: <span class="<?= (strtotime($purchase['expiry_date']) < strtotime('+90 days')) ? 'text-danger' : 'text-success' ?>"><?= date('m/y', strtotime($purchase['expiry_date'])) ?></span></div>
                                        </td>
                                        <td class="text-center">
                                            <div class="h5 fw-900 m-0 text-dark"><?= esc($purchase['initial_qty']) ?></div>
                                            <div class="extra-small text-muted fw-bold">TOTAL UNITS</div>
                                            <div class="badge <?= $remaining == 0 ? 'bg-secondary' : ($remaining < 10 ? 'bg-danger' : 'bg-success') ?> bg-opacity-10 <?= $remaining == 0 ? 'text-secondary' : ($remaining < 10 ? 'text-danger' : 'text-success') ?> mt-1">
                                                <?= $remaining ?> ON SHELF
                                            </div>
                                        </td>
                                        <td>
                                            <div class="extra-small fw-bold text-muted">COST: Rs. <?= number_format($purchase['cost'], 2) ?></div>
                                            <div class="extra-small fw-bold text-success">SALE: Rs. <?= number_format($purchase['price'], 2) ?></div>
                                            <div class="badge bg-primary bg-opacity-10 text-primary mt-1 fw-900 px-2"><?= number_format($margin, 1) ?>% Profit</div>
                                        </td>
                                        <td class="text-end px-5">
                                            <div class="dropdown">
                                                <button class="btn btn-light rounded-circle hover-lift" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                                    <li>
                                                        <a class="dropdown-item rounded-3 py-2" href="<?= base_url('purchases/invoice/'.$purchase['id']) ?>" target="_blank">
                                                            <i class="fas fa-print me-2 text-primary"></i> Print Voucher
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item rounded-3 py-2 btn-edit-stock" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editPurchaseModal"
                                                                data-id="<?= $purchase['id'] ?>"
                                                                data-batch="<?= esc($purchase['batch_id']) ?>"
                                                                data-vendor="<?= $purchase['vendor_id'] ?>"
                                                                data-product="<?= $purchase['product_id'] ?>"
                                                                data-mfg="<?= $purchase['manufacture_date'] ?>"
                                                                data-exp="<?= $purchase['expiry_date'] ?>"
                                                                data-initial="<?= $purchase['initial_qty'] ?>"
                                                                data-cost="<?= $purchase['cost'] ?>"
                                                                data-price="<?= $purchase['price'] ?>">
                                                            <i class="fas fa-pen-to-square me-2 text-warning"></i> Edit Record
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item rounded-3 py-2 text-danger" href="<?= base_url('purchases/delete/'.$purchase['id']) ?>" onclick="return confirm('Archive this procurement record?')">
                                                            <i class="fas fa-trash-can me-2"></i> Delete Entry
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
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

<!-- Professional Edit Modal -->
<div class="modal fade" id="editPurchaseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="p-5 bg-dark text-white d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-900 m-0">Correct Purchase</h4>
                    <p class="text-white-50 m-0 mt-2 small">Audit and modify historical procurement data.</p>
                </div>
                <i class="fas fa-file-pen fa-3x opacity-25"></i>
            </div>
            <form action="<?= base_url('purchases/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_stock_id">
                <div class="modal-body p-5">
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Batch Tracking ID</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 py-3" name="batch_id" id="edit_batch_id" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Supplier</label>
                            <select class="form-select border-0 bg-light py-3" name="vendor_id" id="edit_vendor_id">
                                <option value="">Select Vendor...</option>
                                <?php foreach($vendors as $v): ?>
                                    <option value="<?= $v['id'] ?>"><?= esc($v['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Product</label>
                            <select class="form-select border-0 bg-light py-3" name="product_id" id="edit_product_id" required>
                                <?php foreach($products as $prod): ?>
                                    <option value="<?= $prod['id'] ?>">
                                        <?= esc($prod['name']) ?> [<?= esc($prod['unit_value']) ?> <?= esc($prod['unit']) ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">MFG Date</label>
                            <input type="date" class="form-control bg-light border-0 py-3" name="manufacture_date" id="edit_mfg_date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">EXP Date</label>
                            <input type="date" class="form-control bg-light border-0 py-3" name="expiry_date" id="edit_exp_date" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted text-uppercase">Original Quantity Bought</label>
                            <input type="number" class="form-control bg-light border-0 py-3 fw-bold fs-5" name="initial_qty" id="edit_initial_qty" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Unit Purchase Cost (Rs.)</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 py-3 fw-bold text-primary" name="cost" id="edit_cost" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted text-uppercase">Unit Retail Price (Rs.)</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 py-3 fw-bold text-success" name="price" id="edit_price" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-5 rounded-5 shadow-lg fw-900">COMMIT AUDIT CHANGES</button>
                    <p class="text-muted extra-small text-center w-100 mt-4 px-4">Modifying this record will recalculate financial reports and current stock value immediately.</p>
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
                document.getElementById('edit_initial_qty').value = this.getAttribute('data-initial');
                document.getElementById('edit_cost').value = this.getAttribute('data-cost');
                document.getElementById('edit_price').value = this.getAttribute('data-price');
            });
        });
    });
</script>

<?= $this->endSection() ?>
