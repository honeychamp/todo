<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Sales History</h5>
                    <p class="text-muted small m-0 mt-1">List of all products you sold.</p>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-5 py-4">Invoice & Date</th>
                                <th class="border-0 py-4">Reference Name</th>
                                <th class="border-0 py-4 text-end">Total Amount</th>
                                <th class="border-0 py-4 text-end">Total Discount</th>
                                <th class="border-0 py-4 text-end">Final Amount</th>
                                <th class="border-0 py-4 text-end px-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sales)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">No sales found.</td></tr>
                            <?php else: ?>
                                <?php foreach($sales as $sale): ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="fw-900 text-primary fs-5"><?= esc($sale['invoice_no'] ?: 'S-'.str_pad($sale['id'], 5, '0', STR_PAD_LEFT)) ?></div>
                                            <div class="text-muted small fw-bold"><?= date('d M, Y h:i A', strtotime($sale['sale_date'])) ?></div>
                                        </td>
                                        <td>
                                            <?php if(!empty($sale['doctor_name'])): ?>
                                                <div class="fw-900 text-dark"><i class="fas fa-user-md me-1 text-primary"></i> <?= esc($sale['doctor_name']) ?></div>
                                                <div class="text-muted extra-small fw-bold"><?= esc($sale['doctor_phone'] ?: 'NO PHONE') ?></div>
                                                <div class="badge bg-primary bg-opacity-10 text-primary extra-small px-2 mt-1">DOCTOR NETWORK</div>
                                            <?php else: ?>
                                                <div class="fw-bold text-dark"><i class="fas fa-user-md text-secondary me-1"></i><?= esc($sale['customer_name'] ?: 'Unregistered Doctor') ?></div>
                                                <div class="text-muted extra-small fw-bold"><?= esc($sale['customer_phone'] ?: 'CASH SALE / NO PHONE') ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold text-dark">
                                            Rs. <?= number_format($sale['gross_amount'], 2) ?>
                                        </td>
                                        <td class="text-end fw-bold text-danger">
                                            Rs. <?= number_format($sale['total_discount'] ?? 0, 2) ?>
                                        </td>
                                        <td class="text-end fw-900 text-primary fs-5">
                                            Rs. <?= number_format($sale['total_amount'], 2) ?>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('sales/invoice/'.$sale['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('sales/void/'.$sale['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Cancel this sale?')">
                                                <i class="fas fa-trash"></i>
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
</div>
<?= $this->endSection() ?>
