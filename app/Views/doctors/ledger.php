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
        <button class="btn btn-dark rounded-pill px-4 fw-bold" onclick="window.print()">
            <i class="fas fa-print me-2"></i> Print Transcript
        </button>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr class="text-muted extra-small text-uppercase">
                    <th class="border-0 px-5 py-4">Transaction Date</th>
                    <th class="border-0 py-4">Description</th>
                    <th class="border-0 py-4 text-end">Debit (+)</th>
                    <th class="border-0 py-4 text-end">Credit (-)</th>
                    <th class="border-0 py-4 text-end px-5">Balance</th>
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
                                <div class="fw-bold text-dark"><?= esc($row['description']) ?></div>
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

<?= $this->endSection() ?>
