<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row g-4 animate-wow">
    <div class="col-md-10 mx-auto">
        <div class="bg-white p-5 rounded-4 shadow-lg border border-light" id="printableInvoice">
            <div class="d-flex justify-content-between align-items-center mb-5 border-bottom pb-4">
                <div>
                    <h1 class="fw-900 text-vibrant mb-0"><?= esc(get_setting('pharmacy_name', 'Galaxy Pharmacy')) ?></h1>
                    <p class="text-muted small m-0 mb-1"><?= esc(get_setting('pharmacy_tagline', 'Your Health, Our Priority')) ?></p>
                    <div class="text-muted extra-small">
                        <i class="fas fa-location-dot me-1"></i> <?= esc(get_setting('pharmacy_address', 'Address not set')) ?><br>
                        <i class="fas fa-phone me-1"></i> <?= esc(get_setting('pharmacy_phone', 'Phone not set')) ?>
                    </div>
                </div>
                <div class="text-end">
                    <h4 class="fw-800 text-dark mb-0">SALES RECEIPT</h4>
                    <p class="text-muted small m-0">#<?= esc($sale['invoice_no'] ?: 'S-'.str_pad($sale['id'], 5, '0', STR_PAD_LEFT)) ?></p>
                    <p class="text-muted small m-0"><?= date('d M, Y h:i A', strtotime($sale['sale_date'])) ?></p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase fw-bold small">Payer Information</h6>
                    <?php if(!empty($sale['doctor_name'])): ?>
                        <div class="fw-900 text-primary fs-5"><i class="fas fa-user-md me-1"></i> <?= esc($sale['doctor_name']) ?></div>
                        <div class="text-muted small fw-bold mb-1"><?= esc($sale['doctor_phone'] ?: 'No Contact Provided') ?></div>
                        <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 extra-small">RECORDED TO DOCTOR LEDGER</div>
                    <?php else: ?>
                        <div class="fw-800 text-dark"><i class="fas fa-user-md me-1 text-secondary"></i><?= esc($sale['customer_name'] ?: 'Unregistered Doctor') ?></div>
                        <div class="text-muted small"><?= esc($sale['customer_phone'] ?: 'No Contact Provided') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-muted text-uppercase fw-bold small">Transaction Status</h6>
                    <?php if(!empty($sale['doctor_id'])): ?>
                        <div class="badge bg-warning text-dark rounded-pill px-3 py-2 fs-6">CREDIT / ON ACCOUNT</div>
                    <?php else: ?>
                        <div class="badge bg-success text-white rounded-pill px-3 py-2 fs-6">PAID (CASH)</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="table-responsive mb-5">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th class="border-top-0 border-bottom">Description</th>
                            <th class="border-top-0 border-bottom">Batch</th>
                            <th class="border-top-0 border-bottom text-center">Qty</th>
                            <th class="border-top-0 border-bottom text-end">Unit Price</th>
                            <th class="border-top-0 border-bottom text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($items as $item): ?>
                        <tr>
                            <td>
                                <div class="fw-800 text-dark"><?= esc($item['product_name']) ?></div>
                                <div class="text-muted small"><?= esc($item['unit_value']) ?> <?= esc($item['unit']) ?> Strength</div>
                            </td>
                            <td><code class="text-primary"><?= esc($item['batch_id']) ?></code></td>
                            <td class="text-center fw-bold fs-5"><?= number_format($item['qty']) ?></td>
                            <td class="text-end">Rs. <?= number_format($item['sale_price'], 2) ?></td>
                            <td class="text-end fw-900 fs-5">Rs. <?= number_format($item['qty'] * $item['sale_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end border-0 pt-4 fw-800 text-muted">Gross Total:</td>
                            <td class="text-end border-0 pt-4 fw-800 text-dark">Rs. <?= number_format($sale['total_amount'] + ($sale['total_discount'] ?? 0), 2) ?></td>
                        </tr>
                        <?php if($sale['total_discount'] > 0): ?>
                        <tr>
                            <td colspan="4" class="text-end border-0 pt-1 fw-800 text-danger">Discount Given (-):</td>
                            <td class="text-end border-0 pt-1 fw-800 text-danger">Rs. <?= number_format($sale['total_discount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <td colspan="4" class="text-end border-0 pt-3 fw-900 h4 m-0">Net Payable:</td>
                            <td class="text-end border-0 pt-3 fw-900 h2 m-0 text-primary">Rs. <?= number_format($sale['total_amount'], 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="alert alert-light border-0 rounded-4 text-center mt-5 mb-0 p-4">
                <p class="m-0 text-muted small"><i class="fas fa-info-circle me-1"></i> Medicines cannot be returned or exchanged after sale without a valid batch defect.</p>
                <div class="fw-800 mt-2 text-dark">Thank you for choosing <?= esc(get_setting('pharmacy_name', 'Galaxy Pharmacy')) ?>!</div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-5 py-3 shadow-lg me-2">
                <i class="fas fa-print me-2"></i> Print Receipt
            </button>
            <a href="<?= base_url('sales') ?>" class="btn btn-outline-primary rounded-pill px-5 py-3 shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to POS
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    #sidebar, header, .btn, .alert, .bg-blob { display: none !important; }
    #content { margin-left: 0 !important; padding: 0 !important; }
    #printableInvoice { border: none !important; box-shadow: none !important; padding: 0 !important; }
}
</style>

<?= $this->endSection() ?>
