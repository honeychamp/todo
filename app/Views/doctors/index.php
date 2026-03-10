<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden animate-up">
            <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h2 class="fw-900 m-0 text-dark"><i class="fas fa-user-doctor me-2 text-primary"></i> Doctor Network</h2>
                    <p class="text-muted small m-0 mt-1">Manage bulk purchasers and their financial standing.</p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill fw-900">
                        NETWORK SIZE: <?= count($doctors) ?>
                    </span>
                    <a href="<?= base_url('doctors/add') ?>" class="btn btn-vibrant rounded-pill px-4 fw-900">
                        <i class="fas fa-plus me-2"></i> ADD NEW DOCTOR
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted extra-small text-uppercase">
                            <th class="border-0 px-5 py-4">Doctor identity</th>
                            <th class="border-0 py-4">Phone No.</th>
                            <th class="border-0 py-4">Address / Clinic</th>
                            <th class="border-0 py-4 text-end">Financial Balance</th>
                            <th class="border-0 py-4 text-end px-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($doctors)): ?>
                            <tr><td colspan="5" class="text-center py-5 text-muted h6">No doctors registered.</td></tr>
                        <?php else: ?>
                            <?php foreach($doctors as $d): ?>
                                <?php $balance = $d['total_purchased'] - $d['total_paid']; ?>
                                <tr>
                                    <td class="px-5">
                                        <div class="fw-900 text-primary fs-5 mb-1">
                                            <i class="fas fa-user-md me-2 opacity-75"></i><?= esc($d['name']) ?>
                                        </div>
                                        <div class="text-muted extra-small fw-bold">ID: #DR-<?= str_pad($d['id'], 3, '0', STR_PAD_LEFT) ?></div>
                                        <div class="text-muted extra-small fw-bold">REG: <?= date('d M, Y', strtotime($d['created_at'])) ?></div>
                                    </td>
                                    <td>
                                        <div class="badge bg-light text-dark fw-bold border px-3"><?= esc($d['phone']) ?></div>
                                    </td>
                                    <td>
                                        <div class="extra-small text-muted fw-bold" style="max-width: 200px;"><?= esc($d['address'] ?: 'Not Specified') ?></div>
                                    </td>
                                    <td class="text-end">
                                        <div class="fw-900 <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                            Rs. <?= number_format($balance, 2) ?>
                                        </div>
                                        <div class="extra-small text-muted fw-bold">PURCHASED: Rs. <?= number_format($d['total_purchased'], 2) ?></div>
                                    </td>
                                    <td class="text-end px-5">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?= base_url('doctors/ledger/'.$d['id']) ?>" class="btn btn-sm btn-light rounded-pill px-3 fw-900 border-0 hover-lift">
                                                <i class="fas fa-file-invoice-dollar me-1 text-primary"></i> LEDGER
                                            </a>
                                            <button class="btn btn-sm btn-light text-success rounded-pill px-3 fw-900 border-0 hover-lift btn-pay" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#paymentModal"
                                                    data-id="<?= $d['id'] ?>"
                                                    data-name="<?= esc($d['name']) ?>"
                                                    data-balance="<?= $balance ?>">
                                                <i class="fas fa-hand-holding-dollar me-1"></i> PAY
                                            </button>
                                            <a href="<?= base_url('doctors/delete/'.$d['id']) ?>" 
                                               class="btn btn-sm btn-light text-danger rounded-circle border-0 p-2" 
                                               onclick="return confirm('Authorize deletion?')">
                                                <i class="fas fa-trash-can"></i>
                                            </a>
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

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 40px; overflow: hidden;">
            <div class="p-5 bg-dark text-white d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-900 m-0">Financial Settlement</h4>
                    <p class="text-white-50 m-0 mt-2 small">Process incoming payment from <span id="pay_doctor_name" class="text-white fw-bold"></span>.</p>
                </div>
                <i class="fas fa-receipt fa-3x opacity-25"></i>
            </div>
            <form action="<?= base_url('doctors/add_payment') ?>" method="POST">
                <input type="hidden" name="doctor_id" id="pay_doctor_id">
                <div class="modal-body p-5">
                    <div class="bg-light p-4 rounded-4 mb-4 border-start border-primary border-5">
                       <div class="small fw-900 text-muted text-uppercase mb-1">Outstanding Balance</div>
                       <h2 class="fw-900 m-0" id="pay_balance_view">Rs. 0</h2>
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
                            <label class="form-label extra-small fw-900 text-uppercase text-muted">Settlement Date</label>
                            <input type="date" name="payment_date" class="form-control bg-light border-0 py-3" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label extra-small fw-900 text-uppercase text-muted">Reference / Transaction Notes</label>
                        <textarea name="notes" class="form-control bg-light border-0 py-3 rounded-4" rows="2" placeholder="Bank ref, cheque number..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-4 fs-6 rounded-pill shadow-lg fw-900 text-uppercase tracking-widest">CONFIRM SETTLEMENT</button>
                    <p class="text-muted extra-small text-center w-100 mt-4 px-4">Modifying this ledger will update the doctor's outstanding balance instantly.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const payButtons = document.querySelectorAll('.btn-pay');
        payButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const balance = this.getAttribute('data-balance');
                
                document.getElementById('pay_doctor_id').value = id;
                document.getElementById('pay_doctor_name').innerText = name;
                document.getElementById('pay_balance_view').innerText = 'Rs. ' + parseFloat(balance).toLocaleString();
            });
        });
    });
</script>

<?= $this->endSection() ?>
