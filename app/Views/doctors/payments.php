<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden animate-up">
    <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
        <div>
            <h4 class="fw-900 m-0 text-dark">Payment Collection Nodes</h4>
            <p class="text-muted small m-0 mt-1">Audit trail of all incoming payments from the doctor network.</p>
        </div>
        <a href="<?= base_url('doctors') ?>" class="btn btn-dark rounded-pill px-4 fw-bold">
            <i class="fas fa-plus-circle me-2"></i> New Collection
        </a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="text-muted extra-small text-uppercase">
                    <th class="border-0 px-5 py-4">Receipt Date</th>
                    <th class="border-0 py-4">Doctor / Source</th>
                    <th class="border-0 py-4">Method</th>
                    <th class="border-0 py-4 text-end">Amount Received</th>
                    <th class="border-0 py-4 px-5">Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($payments)): ?>
                    <tr><td colspan="5" class="text-center py-5 text-muted">No payment logs found.</td></tr>
                <?php else: ?>
                    <?php foreach($payments as $p): ?>
                        <tr>
                            <td class="px-5">
                                <div class="fw-800 text-dark"><?= date('d M, Y', strtotime($p['payment_date'])) ?></div>
                            </td>
                            <td>
                                <div class="fw-900 text-primary fs-6"><?= esc($p['doctor_name']) ?></div>
                            </td>
                            <td>
                                <div class="badge bg-light text-dark fw-bold border px-3"><?= esc($p['payment_method']) ?></div>
                            </td>
                            <td class="text-end">
                                <div class="fw-900 text-success fs-5">Rs. <?= number_format($p['amount'], 2) ?></div>
                            </td>
                            <td class="px-5">
                                <div class="extra-small text-muted fw-bold"><?= esc($p['notes'] ?: '—') ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
