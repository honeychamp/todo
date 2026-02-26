<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 mb-4 animate-wow">
    <div class="col-md-4">
        <div class="premium-list p-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #1e293b, #334155); color: white;">
            <div class="opacity-75 small fw-bold text-uppercase">Total Company Debt</div>
            <h1 class="fw-900 m-0 mt-2">Rs. <?= number_format(array_sum(array_map(fn($v) => ($v['total_purchase_value'] ?? 0) - ($v['total_paid'] ?? 0), $vendors)), 2) ?></h1>
            <p class="small opacity-50 m-0 mt-2">Combined outstanding balance for all vendors.</p>
        </div>
    </div>
    <div class="col-md-8">
        <div class="premium-list p-4 bg-white border-0 shadow-sm">
            <h6 class="fw-800 mb-3"><i class="fas fa-info-circle text-primary me-2"></i> Payment Management Tips</h6>
            <div class="row">
                <div class="col-md-6 border-end">
                    <p class="small text-muted m-0">Settling dues regularly maintains your warehouse reputation and ensures priority stock delivery from suppliers.</p>
                </div>
                <div class="col-md-6">
                    <p class="small text-muted m-0">Click on <b>"View Ledger"</b> to see a full item-by-item history of what was bought and when payments were dispatched.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800 text-dark">Supplier Balances & Settlement</h5>
                    <p class="text-muted small m-0 mt-1">Real-time ledger tracking for your pharmacy suppliers.</p>
                </div>
                <div class="text-end">
                    <i class="fas fa-building-user fa-2x opacity-10"></i>
                </div>
            </div>
            
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-5 py-4 border-0">Vendor Details</th>
                                <th class="py-4 border-0 text-end">Total Purchased</th>
                                <th class="py-4 border-0 text-end">Total Paid</th>
                                <th class="py-4 border-0 text-end">Remaining Balance</th>
                                <th class="py-4 border-0 text-end px-5">Financial Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($vendors as $v): ?>
                                <?php $balance = ($v['total_purchase_value'] ?? 0) - ($v['total_paid'] ?? 0); ?>
                                <tr>
                                    <td class="px-5">
                                        <div class="fw-800 text-dark fs-5"><?= esc($v['name']) ?></div>
                                        <div class="text-muted small mt-1"><i class="fas fa-phone me-1 small"></i> <?= esc($v['phone']) ?></div>
                                    </td>
                                    <td class="text-end fw-bold text-muted">Rs. <?= number_format($v['total_purchase_value'] ?? 0, 2) ?></td>
                                    <td class="text-end text-success fw-bold">Rs. <?= number_format($v['total_paid'] ?? 0, 2) ?></td>
                                    <td class="text-end">
                                        <?php if($balance > 0): ?>
                                            <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fs-6 fw-800">
                                                Rs. <?= number_format($balance, 2) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fs-6 fw-800">
                                                Cleared
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end px-5">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <a href="<?= base_url('purchases/vendor/'.$v['id']) ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3 border-0 bg-light hover-lift">
                                                <i class="fas fa-file-invoice me-1"></i> View Ledger
                                            </a>
                                            <button class="btn btn-vibrant rounded-pill px-4 btn-sm shadow-sm" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#payModal"
                                                    onclick="settlePayment(<?= $v['id'] ?>, '<?= esc($v['name'], 'js') ?>', <?= $balance ?>)">
                                                <i class="fas fa-money-bill-transfer me-1"></i> Pay Vendor
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Single Payment Modal (Improved) -->
<div class="modal fade" id="payModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 30px;">
            <div class="p-5 bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="m-0 fw-900" id="modalVendorName">Vendor Name</h4>
                        <p class="text-white-50 m-0 mt-1 small">Record a new payment to clear your dues.</p>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form action="<?= base_url('purchases/add_payment') ?>" method="POST">
                <input type="hidden" name="vendor_id" id="modalVendorId">
                <div class="modal-body p-5">
                    <div class="mb-4 text-center p-4 rounded-4" style="background: rgba(14,165,233,0.05); border: 1.5px dashed rgba(14,165,233,0.2);">
                        <div class="text-muted small fw-bold text-uppercase mb-1">Current Outstanding</div>
                        <h2 class="fw-900 text-primary m-0" id="modalBalance">Rs. 0.00</h2>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Amount to Pay (Rs.)</label>
                        <input type="number" step="0.01" class="form-control form-control-lg bg-light border-0 py-3 fw-800" name="amount" id="pay_amount" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-dark">Payment Date</label>
                            <input type="date" class="form-control bg-light border-0 py-3" name="payment_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark">Notes / Transaction Reference</label>
                        <textarea class="form-control bg-light border-0 py-3" name="notes" rows="2" placeholder="e.g. Paid via Bank Transfer #12345"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 rounded-4 fw-bold shadow-lg fs-5">Confirm Dispatch Payment</button>
                    <p class="text-muted extra-small text-center w-100 mt-3"><i class="fas fa-shield-halved me-1"></i> This will be recorded instantly in the vendor ledger.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function settlePayment(id, name, balance) {
    document.getElementById('modalVendorId').value = id;
    document.getElementById('modalVendorName').innerText = name;
    document.getElementById('modalBalance').innerText = 'Rs. ' + balance.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('pay_amount').value = balance;
    document.getElementById('pay_amount').max = balance;
}
</script>

<?= $this->endSection() ?>
