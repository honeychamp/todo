<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row g-4 mb-5 animate-wow">
    <div class="col-12">
        <div class="premium-list p-5 shadow-lg border-0 bg-white rounded-5">
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
                <div>
                    <h2 class="fw-900 m-0"><i class="fas fa-chart-pie me-2 text-primary"></i> Detailed Sales Report</h2>
                    <p class="text-muted small m-0 mt-1">Audit transactions and track profitability performance.</p>
                </div>
                <div class="text-end">
                    <form action="<?= base_url('sales/report') ?>" method="GET" class="d-flex gap-3 align-items-center bg-light p-3 rounded-4 shadow-sm">
                        <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control bg-white border-0 py-2 px-3 shadow-none fw-bold" required>
                        <i class="fas fa-arrow-right text-muted small"></i>
                        <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control bg-white border-0 py-2 px-3 shadow-none fw-bold" required>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold text-nowrap"><i class="fas fa-filter me-2 small"></i> Filter</button>
                        <a href="<?= base_url('sales/export?start_date='.($start_date ?? '').'&end_date='.($end_date ?? '')) ?>" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold text-nowrap border-0 bg-white shadow-sm hover-lift">
                            <i class="fas fa-file-csv me-2 text-success"></i> Export CSV
                        </a>
                    </form>
                </div>
            </div>

            <div class="row g-4 mb-5">
                <div class="col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 d-flex flex-column justify-content-center" style="background: rgba(99,102,241,0.05); border: 2.5px dashed rgba(99,102,241,0.2);">
                        <div class="text-muted small fw-bold text-uppercase mb-2">Total Sales Revenue</div>
                        <?php $rev = array_sum(array_map(function($s){ return $s['qty'] * $s['sale_price']; }, $sales)); ?>
                        <div class="fw-900 h2 m-0 text-primary">Rs. <?= number_format($rev, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 d-flex flex-column justify-content-center" style="background: rgba(16,185,129,0.05); border: 2.5px dashed rgba(16,185,129,0.2);">
                        <div class="text-muted small fw-bold text-uppercase mb-2">Total Profit Earned</div>
                        <?php $prof = array_sum(array_map(function($s){ return ($s['sale_price'] - $s['purchase_cost']) * $s['qty']; }, $sales)); ?>
                        <div class="fw-900 h2 m-0 text-success">Rs. <?= number_format($prof, 2) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 d-flex flex-column justify-content-center" style="background: rgba(245,158,11,0.05); border: 2.5px dashed rgba(245,158,11,0.2);">
                        <div class="text-muted small fw-bold text-uppercase mb-2">Units Sold Total</div>
                        <?php $qtyS = array_sum(array_column($sales, 'qty')); ?>
                        <div class="fw-900 h2 m-0 text-warning"><?= number_format($qtyS) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-4 rounded-4 text-center h-100 d-flex flex-column justify-content-center" style="background: rgba(15,23,42,0.05); border: 2.5px dashed rgba(15,23,42,0.2);">
                        <div class="text-muted small fw-bold text-uppercase mb-2">Total Transactions</div>
                        <div class="fw-900 h2 m-0 text-dark"><?= count($sales) ?></div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted small text-uppercase">
                            <th class="border-0 px-4 py-3">Sale ID & Date</th>
                            <th class="border-0 py-3">Product Description</th>
                            <th class="border-0 py-3 text-center">Qty Sold</th>
                            <th class="border-0 py-3 text-end">Sale Revenue</th>
                            <th class="border-0 py-3 text-end">Total Profit</th>
                            <th class="border-0 py-3 text-end px-5">Profit Card</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($sales)): ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">No transactions recorded for the selected date range.</td></tr>
                        <?php else: ?>
                            <?php foreach($sales as $sale): ?>
                                <?php 
                                    $revenue = $sale['qty'] * $sale['sale_price'];
                                    $profit = ($sale['sale_price'] - $sale['purchase_cost']) * $sale['qty'];
                                    $margin = $revenue > 0 ? ($profit / $revenue) * 100 : 0;
                                ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-800 text-dark">S-<?= str_pad($sale['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                        <div class="text-muted small" style="font-size: 0.7rem;"><?= date('d M, Y h:i A', strtotime($sale['sale_date'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold fs-6"><?= esc($sale['product_name']) ?></div>
                                        <div class="text-muted small">Batch: <?= esc($sale['batch_id']) ?></div>
                                    </td>
                                    <td class="text-center fw-bold fs-5"><?= number_format($sale['qty']) ?></td>
                                    <td class="text-end fw-bold">Rs. <?= number_format($revenue, 2) ?></td>
                                    <td class="text-end fw-bold text-success">Rs. <?= number_format($profit, 2) ?></td>
                                    <td class="text-end px-5">
                                        <span class="badge rounded-pill <?= $margin > 20 ? 'bg-success' : 'bg-primary' ?> px-3 py-1"><?= number_format($margin, 1) ?>% Margin</span>
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
