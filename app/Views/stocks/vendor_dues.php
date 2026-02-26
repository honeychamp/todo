<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    
    <!-- Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="fw-800 m-0">Vendor Dues Summary</h4>
                <p class="text-muted small m-0">Track all outstanding balances with your suppliers.</p>
            </div>
            <a href="<?= base_url('stocks/purchase') ?>" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-history me-2"></i> Purchase Log
            </a>
        </div>
    </div>

    <!-- Total System Dues Card -->
    <div class="col-12">
        <div class="premium-list p-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
            <div class="row align-items-center">
                <div class="col-md-8 text-white">
                    <div class="opacity-75 small fw-bold text-uppercase">Total Outstanding Balance (System Wide)</div>
                    <h2 class="fw-900 m-0 mt-1">Rs. <?= number_format($totalSystemDues, 2) ?></h2>
                    <p class="m-0 mt-2 opacity-75 small">This is the total amount you currently owe to all vendors combined.</p>
                </div>
                <div class="col-md-4 text-end d-none d-md-block">
                    <i class="fas fa-hand-holding-dollar text-white opacity-25" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Dues Table -->
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom">
                <h5 class="m-0 fw-800">Vendors with Balances</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-5">Vendor Name</th>
                            <th>Total Purchases</th>
                            <th>Total Paid</th>
                            <th>Remaining Due</th>
                            <th class="text-end px-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($dues)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 py-4">
                                        <i class="fas fa-check-double fs-1 mb-3 text-success"></i>
                                        <p class="m-0 fw-bold">All settled! No outstanding dues.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($dues as $d): ?>
                                <tr>
                                    <td class="px-5">
                                        <div class="fw-bold"><?= esc($d['name']) ?></div>
                                        <div class="text-muted small"><?= esc($d['phone']) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">Rs. <?= number_format($d['total'], 2) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">Rs. <?= number_format($d['paid'], 2) ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-900 text-danger" style="font-size: 1.1rem;">
                                            Rs. <?= number_format($d['balance'], 2) ?>
                                        </div>
                                    </td>
                                    <td class="text-end px-5">
                                        <a href="<?= base_url('stocks/vendor/'.$d['id']) ?>" class="btn btn-vibrant rounded-pill px-4 btn-sm">
                                            <i class="fas fa-eye me-2"></i> View & Pay
                                        </a>
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
