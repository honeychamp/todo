<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4 animate-wow">
    <!-- Header Card -->
    <div class="col-12">
        <div class="premium-list p-5 text-white border-0 shadow-lg position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a, #1e293b); border-radius: 40px;">
            <div class="position-absolute end-0 top-0 h-100 opacity-25 w-50" style="background: radial-gradient(circle at right, #3b82f633, transparent);"></div>
            <div class="row align-items-center position-relative z-1">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 80px; height: 80px; background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                            <i class="fas fa-pills fa-2x"></i>
                        </div>
                        <div>
                            <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold mb-2">
                                <?= esc($product['category_name'] ?? 'Uncategorized') ?>
                            </span>
                            <h2 class="fw-900 m-0 display-6"><?= esc($product['name']) ?></h2>
                            <p class="text-white-50 mt-1 mb-0"><i class="fas fa-box me-2"></i><?= esc($product['unit_value']) ?> <?= esc($product['unit']) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end d-flex flex-column align-items-md-end gap-3 mt-4 mt-md-0">
                    <div class="d-flex gap-3 text-end">
                        <div>
                            <div class="text-white-50 extra-small fw-bold text-uppercase">Form 6</div>
                            <div class="fs-5 fw-bold"><?= esc($product['form_6'] ?: 'N/A') ?></div>
                        </div>
                        <div class="border-start border-secondary opacity-50"></div>
                        <div>
                            <div class="text-white-50 extra-small fw-bold text-uppercase">Form 7</div>
                            <div class="fs-5 fw-bold"><?= esc($product['form_7'] ?: 'N/A') ?></div>
                        </div>
                    </div>
                    <a href="<?= base_url('products') ?>" class="btn btn-outline-light rounded-pill px-4 btn-sm mt-3">
                        <i class="fas fa-arrow-left me-2"></i> Back to Catalog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-4 animate-up">
    <div class="col-md-3">
        <div class="card premium-list border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="text-muted fw-bold text-uppercase small mb-2"><i class="fas fa-cubes text-primary me-2"></i>Current Stock</div>
                <h2 class="fw-900 mb-0"><?= number_format($stats['current_stock']) ?> <span class="fs-6 text-muted fw-normal">Units</span></h2>
                <?php if($stats['current_stock'] <= 0): ?>
                    <div class="badge bg-danger mt-2 rounded-pill px-3">Out of Stock</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-list border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="text-muted fw-bold text-uppercase small mb-2"><i class="fas fa-chart-line text-success me-2"></i>Total Sold</div>
                <h2 class="fw-900 mb-0"><?= number_format($stats['total_sold']) ?> <span class="fs-6 text-muted fw-normal">Units</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-list border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="text-muted fw-bold text-uppercase small mb-2"><i class="fas fa-coins text-warning me-2"></i>Total Revenue</div>
                <h2 class="fw-900 mb-0 text-success">Rs. <?= number_format($stats['revenue'], 2) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card premium-list border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="text-muted fw-bold text-uppercase small mb-2"><i class="fas fa-piggy-bank text-info me-2"></i>Total Profit</div>
                <h2 class="fw-900 mb-0 text-primary">Rs. <?= number_format($stats['profit'], 2) ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up" style="animation-delay: 0.1s;">
    <!-- Batch Inventory Ledger -->
    <div class="col-lg-6">
        <div class="premium-list p-0 shadow-sm border-0 bg-white h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold m-0"><i class="fas fa-boxes-packing text-primary me-2"></i>Inventory Batches</h5>
                    <p class="text-muted small m-0 mt-1">Track specific purchases and shelf life</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Batch & Vendor</th>
                            <th class="border-0 py-3">Purchase Date</th>
                            <th class="border-0 py-3 text-end">Cost</th>
                            <th class="border-0 px-4 py-3 text-end">On Shelf</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($batches)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">No inventory data found.</td></tr>
                        <?php else: ?>
                            <?php foreach($batches as $batch): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-dark"><?= esc($batch['batch_id'] ?: 'N/A') ?></div>
                                        <div class="text-muted small"><?= esc($batch['vendor_name'] ?? 'Unknown Vendor') ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= date('d M Y', strtotime($batch['purchase_date'])) ?></div>
                                        <?php if($batch['exp_date']): ?>
                                            <div class="text-danger small"><i class="fas fa-hourglass-end me-1"></i>Exp: <?= date('M Y', strtotime($batch['exp_date'])) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end fw-bold text-muted">Rs. <?= number_format($batch['cost'], 2) ?></td>
                                    <td class="px-4 text-end">
                                        <?php if($batch['remaining_qty'] > 0): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-bold fs-6">
                                                <?= $batch['remaining_qty'] ?> / <?= $batch['qty'] ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2 rounded-pill fw-bold">
                                                Sold Out
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sales Ledger -->
    <div class="col-lg-6">
        <div class="premium-list p-0 shadow-sm border-0 bg-white h-100">
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold m-0"><i class="fas fa-money-bill-wave text-success me-2"></i>Sales History</h5>
                    <p class="text-muted small m-0 mt-1">Recent sales transactions</p>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 px-4 py-3">Date</th>
                            <th class="border-0 py-3">Customer</th>
                            <th class="border-0 py-3 text-center">Batch / Qty</th>
                            <th class="border-0 px-4 py-3 text-end">Amount Paid</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($sales)): ?>
                            <tr><td colspan="4" class="text-center py-5 text-muted">No sales history yet.</td></tr>
                        <?php else: ?>
                            <?php foreach($sales as $sale): ?>
                                <tr>
                                    <td class="px-4 fw-bold"><?= date('d M Y', strtotime($sale['sale_date'])) ?></td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            <?php if($sale['doctor_name']): ?>
                                                <i class="fas fa-user-md text-primary me-1"></i><?= esc($sale['doctor_name']) ?>
                                            <?php else: ?>
                                                <i class="fas fa-user-md text-secondary me-1"></i><?= esc($sale['customer_name'] ?: 'Walk-in Doctor / Cash') ?>
                                            <?php endif; ?>
                                        </div>
                                        <?php if($sale['doctor_phone'] || $sale['customer_phone']): ?>
                                            <div class="text-muted small"><?= esc($sale['doctor_phone'] ?: $sale['customer_phone']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="badge bg-light text-dark border px-2 py-1"><?= esc($sale['batch_id'] ?: 'Unknown') ?></div>
                                        <div class="fw-bold mt-1 text-primary"><?= $sale['qty'] ?> Units</div>
                                    </td>
                                    <td class="px-4 text-end">
                                        <div class="fw-900 text-success">
                                            Rs. <?= number_format(($sale['qty'] * $sale['sale_price']) - $sale['discount'], 2) ?>
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

<?= $this->endSection() ?>
