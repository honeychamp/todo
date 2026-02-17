<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="glass-card">
            <div class="card-header-premium">
                <div>
                    <h5 class="m-0">Financial Audit Logs</h5>
                    <p class="text-muted small m-0 mt-1">Full history of retail sales and revenue distribution.</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-emerald btn-sm rounded-pill px-3" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Export PDF
                    </button>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Reference #</th>
                                <th class="border-0 py-3">Checkout Time</th>
                                <th class="border-0 py-3">Description</th>
                                <th class="border-0 py-3">Batch Info</th>
                                <th class="border-0 py-3 text-center">Qty Sold</th>
                                <th class="border-0 py-3 text-end px-4">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sales)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <p class="text-muted m-0">No transaction logs available.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $grandTotal = 0;
                                foreach($sales as $index => $sale): 
                                    $amount = $sale['qty'] * $sale['sale_price'];
                                    $grandTotal += $amount;
                                ?>
                                    <tr>
                                        <td class="px-4"><code class="text-muted small">REC-<?= str_pad($sale['id'], 6, '0', STR_PAD_LEFT) ?></code></td>
                                        <td>
                                            <div class="fw-bold small"><?= date('h:i A', strtotime($sale['sale_date'])) ?></div>
                                            <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M, Y', strtotime($sale['sale_date'])) ?></div>
                                        </td>
                                        <td class="fw-bold"><?= esc($sale['product_name']) ?></td>
                                        <td><span class="badge bg-light text-dark border p-2 small"><?= esc($sale['batch_id']) ?></span></td>
                                        <td class="text-center"><b><?= esc($sale['qty']) ?></b> <small class="text-muted">Units</small></td>
                                        <td class="text-end px-4">
                                            <span class="fw-bold text-dark">$<?= number_format($amount, 2) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if(!empty($sales)): ?>
                        <tfoot class="bg-dark text-white">
                            <tr>
                                <th colspan="5" class="text-end px-4 py-3">Aggregate Revenue:</th>
                                <th class="text-end px-4 py-3 fs-5">$<?= number_format($grandTotal, 2) ?></th>
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
