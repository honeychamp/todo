<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 mb-5 animate-wow">
    <div class="col-xl-4 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm border-start border-5 border-success">
            <div class="text-muted extra-small fw-bold text-uppercase">Lifetime Settlements</div>
            <h2 class="fw-900 m-0 mt-1 text-dark">Rs. <?= number_format(array_sum(array_column($payments, 'amount')), 2) ?></h2>
            <div class="small text-muted mt-2">Total cash flow from doctor network</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm border-start border-5 border-primary">
            <div class="text-muted extra-small fw-bold text-uppercase">Total Receipt Nodes</div>
            <h2 class="fw-900 m-0 mt-1 text-primary"><?= count($payments) ?> Transactions</h2>
            <div class="small text-muted mt-2">Historical audit trail count</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12">
        <div class="premium-list p-4 bg-dark text-white border-0 shadow-lg d-flex justify-content-between align-items-center">
            <div>
                <div class="opacity-50 extra-small fw-bold text-uppercase tracking-widest">Network Liquidity</div>
                <h2 class="fw-900 m-0 mt-1" style="color: #10b981;">Rs. <?= number_format(array_sum(array_filter(array_column($payments, 'amount'), function($val){ return true; })), 2) ?></h2>
            </div>
            <i class="fas fa-hand-holding-dollar fa-3x opacity-25"></i>
        </div>
    </div>
</div>

<div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden animate-up">
    <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
        <div>
            <h2 class="fw-900 m-0 text-dark"><i class="fas fa-receipt me-2 text-primary"></i> Doctor Payment Logs</h2>
            <p class="text-muted small m-0 mt-1">Audit trail of all incoming payments from the doctor network.</p>
        </div>
        <a href="<?= base_url('doctors') ?>" class="btn btn-vibrant rounded-pill px-4 fw-900 shadow-sm">
            <i class="fas fa-user-doctor me-2"></i> VIEW NETWORK
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="text-muted extra-small text-uppercase">
                    <th class="border-0 px-5 py-4">Receipt Details</th>
                    <th class="border-0 py-4">Doctor / Source</th>
                    <th class="border-0 py-4">Settle Method</th>
                    <th class="border-0 py-4 text-end">Amount Received</th>
                    <th class="border-0 py-4 px-5">Notes / Reference</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($payments)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted h6">
                        <div class="opacity-25 py-5 text-center">
                            <i class="fas fa-file-invoice-dollar fs-1 mb-3"></i>
                            <p class="m-0">No payment logs found in system audit.</p>
                        </div>
                    </td></tr>
                <?php else: ?>
                    <?php foreach($payments as $p): ?>
                        <tr>
                            <td class="px-5">
                                <div class="fw-800 text-dark fs-6"><?= date('d M, Y', strtotime($p['payment_date'])) ?></div>
                                <div class="extra-small text-muted fw-bold"><?= date('h:i A', strtotime($p['payment_date'] ?? $p['created_at'] ?? '')) ?></div>
                            </td>
                            <td>
                                <div class="fw-900 text-primary fs-5 mb-1">
                                    <i class="fas fa-user-md me-1 opacity-75"></i> <?= esc($p['doctor_name']) ?>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary extra-small px-2">DOCTOR LEDGER ENTRY</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php 
                                        $icon = 'fa-money-bill-wave';
                                        $color = 'text-success';
                                        if(strpos($p['payment_method'], 'Bank') !== false) { $icon = 'fa-building-columns'; $color = 'text-primary'; }
                                        if(strpos($p['payment_method'], 'Cheque') !== false) { $icon = 'fa-money-check-alt'; $color = 'text-warning'; }
                                        if(strpos($p['payment_method'], 'Easypaisa') !== false) { $icon = 'fa-mobile-screen-button'; $color = 'text-success'; }
                                    ?>
                                    <i class="fas <?= $icon ?> <?= $color ?> fs-5"></i>
                                    <div class="fw-bold text-dark"><?= esc($p['payment_method']) ?></div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="fw-900 text-success fs-4">Rs. <?= number_format($p['amount'], 2) ?></div>
                                <div class="extra-small text-muted fw-bold">SETTLED IN FULL</div>
                            </td>
                            <td class="px-5">
                                <div class="p-2 px-3 rounded-4 bg-light border-0 small text-muted italic fw-500" style="max-width: 250px;">
                                    <i class="fas fa-quote-left extra-small me-1 opacity-50"></i>
                                    <?= esc($p['notes'] ?: 'No additional reference recorded') ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
