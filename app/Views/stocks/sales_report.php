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
                    <a href="<?= base_url('stocks/export_sales?start_date='.($start_date ?? '').'&end_date='.($end_date ?? '')) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                        <i class="fas fa-file-csv me-1"></i> Export CSV
                    </a>
                    <button class="btn btn-outline-emerald btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>
            
            <!-- Summary Stats -->
            <div class="row g-4 p-5 pb-0">
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light border">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Total Revenue</div>
                        <h3 class="fw-900 m-0 text-primary">Rs. <?= number_format($grandTotal, 2) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light border">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Gross Profit</div>
                        <h3 class="fw-900 m-0 text-success">Rs. <?= number_format($totalProfit, 2) ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-light border">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Net Profit</div>
                        <h3 class="fw-900 m-0 text-indigo">Rs. <?= number_format($netProfit, 2) ?></h3>
                    </div>
                </div>
                
                <?php if(!empty($categoryProfit)): ?>
                <div class="col-12 mt-4">
                    <div class="p-4 rounded-4 border bg-white shadow-sm">
                        <h6 class="fw-800 mb-3"><i class="fas fa-chart-pie me-2"></i>Profit by Category</h6>
                        <div class="d-flex flex-wrap gap-3">
                            <?php foreach($categoryProfit as $cat => $prof): ?>
                                <div class="bg-light p-2 px-3 rounded-pill border small">
                                    <span class="text-muted"><?= esc($cat) ?>:</span> 
                                    <span class="fw-bold ms-1 text-dark">Rs. <?= number_format($prof, 2) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Receipt ID</th>
                                <th class="border-0 py-3">Date & Time</th>
                                <th class="border-0 py-3">Product Name</th>
                                <th class="border-0 py-3">Customer</th>
                                <th class="border-0 py-3">Vendor / Batch</th>
                                <th class="border-0 py-3 text-center">Qty</th>
                                <th class="border-0 py-3 text-end">Sale Amount</th>
                                <th class="border-0 py-3 text-end px-4">Action</th>
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
                                            <div class="fw-bold small"><?= esc($sale['customer_name'] ?: 'Cash Customer') ?></div>
                                            <?php if($sale['customer_phone']): ?>
                                                <div class="text-muted" style="font-size: 0.75rem;"><i class="fas fa-phone small me-1"></i><?= esc($sale['customer_phone']) ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="fw-bold small"><?= esc($sale['vendor_name'] ?: 'N/A') ?></div>
                                            <div class="text-muted small" style="font-size: 0.65rem;">Batch: <?= esc($sale['batch_id']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark p-2 border"><?= esc($sale['qty']) ?> Units</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="fw-bold text-dark">Rs. <?= number_format($amount, 2) ?></span>
                                            <div class="text-muted" style="font-size: 0.65rem;">at Rs. <?= number_format($sale['sale_price'], 2) ?>/unit</div>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('stocks/void_sale/'.$sale['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Void this sale and RESTORE stock? Use this for errors only.')" title="Void Sale">
                                                <i class="fas fa-undo"></i> Void
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($sales)): ?>
                        <tfoot class="bg-dark text-white">
                            <tr>
                                <th colspan="7" class="text-end px-4 py-1 small opacity-75">Gross Profit:</th>
                                <th class="text-end px-4 py-1 fs-6 text-warning fw-bold">Rs. <?= number_format($totalProfit, 2) ?></th>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end px-4 py-1 small opacity-75">Business Expenses:</th>
                                <th class="text-end px-4 py-1 fs-6 text-danger fw-bold">Rs. <?= number_format($totalExpenses, 2) ?></th>
                            </tr>
                            <tr>
                                <th colspan="7" class="text-end px-4 py-1 small opacity-75">Net Profit:</th>
                                <th class="text-end px-4 py-1 fs-6 text-success fw-bold">Rs. <?= number_format($netProfit, 2) ?></th>
                            </tr>
                            <tr style="border-top: 2px solid rgba(255,255,255,0.1);">
                                <th colspan="7" class="text-end px-4 py-3">Total Revenue:</th>
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
