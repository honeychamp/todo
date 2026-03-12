<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 mb-5 animate-wow">
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm border-start border-5 border-primary">
            <div class="text-muted extra-small fw-bold text-uppercase">Total Inventory Lifted</div>
            <h2 class="fw-900 m-0 mt-1 text-dark">Rs. <?= number_format($summary['total_purchased'], 2) ?></h2>
            <div class="small text-muted mt-2">Lifetime supplies for this node</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm border-start border-5 border-success">
            <div class="text-muted extra-small fw-bold text-uppercase">Total Liquidity Settled</div>
            <h2 class="fw-900 m-0 mt-1 text-success">Rs. <?= number_format($summary['total_paid'], 2) ?></h2>
            <div class="small text-muted mt-2">Payments received to date</div>
        </div>
    </div>
    <div class="col-xl-6 col-md-12">
        <div class="premium-list p-4 bg-dark text-white border-0 shadow-lg d-flex justify-content-between align-items-center">
            <div>
                <div class="opacity-50 extra-small fw-bold text-uppercase tracking-widest">Outstanding Liability</div>
                <h2 class="fw-900 m-0 mt-1" style="color: #fbbf24;">Rs. <?= number_format($summary['balance'], 2) ?></h2>
                <div class="small opacity-50 mt-2">Current debt standing on system</div>
            </div>
            <div class="text-end">
                <div class="h5 m-0 fw-900 text-uppercase"><?= esc($doctor['name']) ?></div>
                <div class="extra-small opacity-50 fw-bold">NODE: #DR-<?= str_pad($doctor['id'], 3, '0', STR_PAD_LEFT) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden animate-up">
    <div class="p-5 border-bottom d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-900 m-0">Transaction Audit log</h4>
            <p class="text-muted small m-0 mt-1">Detailed record of supplies and financial settlements.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <i class="fas fa-hand-holding-dollar me-2"></i> Receive Payment
            </button>
            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
                <i class="fas fa-print me-2"></i> Print Transcript
            </button>
        </div>
    </div>
    
    <div class="table-responsive rounded-4 border overflow-hidden mt-4">
        <table class="table table-hover align-middle mb-0">
            <thead style="background-color: #f8fafc;">
                <tr class="text-muted text-uppercase tracking-widest" style="font-size: 0.75rem;">
                    <th class="border-0 px-5 py-4 fw-900">Transaction Date</th>
                    <th class="border-0 py-4 fw-900">Description</th>
                    <th class="border-0 py-4 text-end fw-900">Debit (+)</th>
                    <th class="border-0 py-4 text-end fw-900">Credit (-)</th>
                    <th class="border-0 py-4 text-end px-5 fw-900">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($ledger)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No transactions found for this node.</td></tr>
                <?php else: ?>
                    <?php foreach($ledger as $row): ?>
                        <tr>
                            <td class="px-5">
                                <div class="fw-800 text-dark"><?= date('d M, Y', strtotime($row['date'])) ?></div>
                                <div class="extra-small text-muted"><?= date('h:i A', strtotime($row['date'])) ?></div>
                            </td>
                            <td>
                                <div class="badge <?= $row['type'] == 'SALE' ? 'bg-primary' : 'bg-success' ?> bg-opacity-10 <?= $row['type'] == 'SALE' ? 'text-primary' : 'text-success' ?> extra-small px-3 fw-bold mb-1">
                                    <?= $row['type'] ?>
                                </div>
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <?= esc($row['description']) ?>
                                    <?php if($row['type'] == 'SALE'): ?>
                                        <a href="<?= base_url('sales/invoice/'.$row['ref']) ?>" class="badge bg-light text-primary border border-primary text-decoration-none ms-2" target="_blank" title="View Full Details">
                                            <i class="fas fa-eye me-1"></i> View Items
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end fw-bold text-danger">
                                <?= $row['debit'] > 0 ? '+ Rs.' . number_format($row['debit'], 2) : '—' ?>
                            </td>
                            <td class="text-end fw-bold text-success">
                                <?= $row['credit'] > 0 ? '- Rs.' . number_format($row['credit'], 2) : '—' ?>
                            </td>
                            <td class="text-end px-5 fw-900 <?= $row['balance'] > 0 ? 'text-dark' : 'text-success' ?>">
                                Rs. <?= number_format($row['balance'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Payment Modal (Quick Ledger Settlement) -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 40px; overflow: hidden;">
            <div class="p-5 bg-dark text-white d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-900 m-0">Financial Settlement</h4>
                    <p class="text-white-50 m-0 mt-2 small">Process incoming payment from <span class="text-white fw-bold"><?= esc($doctor['name']) ?></span>.</p>
                </div>
                <i class="fas fa-receipt fa-3x opacity-25"></i>
            </div>
            <form action="<?= base_url('doctors/add_payment') ?>" method="POST">
                <!-- Redirect back context manually if needed, though controller does redirect()->back() -->
                <input type="hidden" name="doctor_id" value="<?= $doctor['id'] ?>">
                <div class="modal-body p-5">
                    <div class="bg-light p-4 rounded-4 mb-4 border-start border-primary border-5">
                       <div class="small fw-900 text-muted text-uppercase mb-1">Outstanding Balance</div>
                       <h2 class="fw-900 m-0">Rs. <?= number_format($summary['balance'], 2) ?></h2>
                    </div>

                    <div class="mb-4">
                        <label class="form-label extra-small fw-900 text-uppercase text-muted">Payment Amount (Rs.)</label>
                        <input type="number" step="0.01" class="form-control form-control-lg bg-light border-0 py-3 fw-bold fs-4" name="amount" required>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label extra-small fw-900 text-uppercase text-muted">Method</label>
                            <select name="payment_method" class="form-select bg-light border-0 py-3">
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Easypaisa/Jazzcash">Easypaisa/Jazzcash</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label extra-small fw-900 text-uppercase text-muted">Date</label>
                            <input type="date" name="payment_date" class="form-control bg-light border-0 py-3" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label extra-small fw-900 text-uppercase text-muted">Reference / Notes</label>
                        <textarea name="notes" class="form-control bg-light border-0 py-3 rounded-4" rows="2" placeholder="Bank ref, cheque number..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-4 fs-6 rounded-pill shadow-lg fw-900 text-uppercase tracking-widest">SUBMIT PAYMENT</button>
                    <p class="text-muted extra-small text-center w-100 mt-4 px-4">Modifying this ledger will update the doctor's outstanding balance instantly.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
