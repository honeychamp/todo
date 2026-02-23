<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Sales Report</h5>
                    <p class="text-muted small m-0 mt-1">Full history of all sales transactions.</p>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <form action="<?= base_url('stocks/sales_report') ?>" method="GET" class="d-flex gap-2 align-items-center">
                        <input type="date" name="start_date" class="form-control form-control-sm rounded-pill" value="<?= $start_date ?? '' ?>">
                        <span class="text-muted small">to</span>
                        <input type="date" name="end_date" class="form-control form-control-sm rounded-pill" value="<?= $end_date ?? '' ?>">
                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">Filter</button>
                    </form>
                    <button class="btn btn-outline-emerald btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Receipt ID</th>
                                <th class="border-0 py-3">Date & Time</th>
                                <th class="border-0 py-3">Product Name</th>
                                <th class="border-0 py-3">Vendor / Batch</th>
                                <th class="border-0 py-3 text-center">Qty</th>
                                <th class="border-0 py-3 text-end px-4">Sale Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sales)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted m-0">No sales recorded yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($sales as $index => $sale): 
                                    $amount = $sale['qty'] * $sale['sale_price'];
                                ?>
                                    <tr>
                                        <td class="px-4"><code class="text-muted small">REC-<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></code></td>
                                        <td>
                                            <div class="fw-bold small"><?= date('h:i A', strtotime($sale['sale_date'])) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M, Y', strtotime($sale['sale_date'])) ?></div>
                                        </td>
                                        <td class="fw-bold">
                                            <?= esc($sale['product_name']) ?>
                                            <div class="text-muted small fw-normal"><?= esc($sale['product_unit_value']) ?> <?= esc($sale['product_unit']) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold small"><?= esc($sale['vendor_name'] ?: 'N/A') ?></div>
                                            <div class="text-muted small" style="font-size: 0.65rem;">Batch: <?= esc($sale['batch_id']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark p-2 border"><?= esc($sale['qty']) ?> Units</span>
                                        </td>
                                        <td class="text-end px-4">
                                            <span class="fw-bold text-dark">Rs. <?= number_format($amount, 2) ?></span>
                                            <div class="text-muted" style="font-size: 0.65rem;">at Rs. <?= number_format($sale['sale_price'], 2) ?>/unit</div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($sales)): ?>
                        <tfoot class="bg-dark text-white">
                            <tr>
                                <th colspan="5" class="text-end px-4 py-1 small opacity-75">Net Profit:</th>
                                <th class="text-end px-4 py-1 fs-6 text-success fw-bold">Rs. <?= number_format($totalProfit, 2) ?></th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end px-4 py-3">Total Revenue:</th>
                                <th class="text-end px-4 py-3 fs-5 text-indigo-light">Rs. <?= number_format($grandTotal, 2) ?></th>
                            </tr>
                        </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
