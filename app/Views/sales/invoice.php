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
                    <p class="text-muted small m-0">#S-<?= str_pad($sale['id'], 5, '0', STR_PAD_LEFT) ?></p>
                    <p class="text-muted small m-0"><?= date('d M, Y h:i A', strtotime($sale['sale_date'])) ?></p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase fw-bold small">Customer Info</h6>
                    <div class="fw-800 text-dark"><?= esc($sale['customer_name'] ?: 'Guest Customer') ?></div>
                    <div class="text-muted small"><?= esc($sale['customer_phone'] ?: 'No Phone Provided') ?></div>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-muted text-uppercase fw-bold small">Payment Status</h6>
                    <div class="badge bg-success rounded-pill px-3 py-2 fs-6">PAID IN FULL</div>
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
                        <tr>
                            <td>
                                <div class="fw-800 text-dark"><?= esc($sale['product_name']) ?></div>
                                <div class="text-muted small"><?= esc($sale['product_unit_value']) ?> <?= esc($sale['product_unit']) ?> Strength</div>
                            </td>
                            <td><code class="text-primary"><?= esc($sale['batch_id']) ?></code></td>
                            <td class="text-center fw-bold fs-5"><?= number_format($sale['qty']) ?></td>
                            <td class="text-end">Rs. <?= number_format($sale['sale_price'], 2) ?></td>
                            <td class="text-end fw-900 fs-5">Rs. <?= number_format($sale['qty'] * $sale['sale_price'], 2) ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end border-0 pt-4 fw-800 h4 m-0">Grand Total Payable:</td>
                            <td class="text-end border-0 pt-4 fw-900 h3 m-0 text-primary">Rs. <?= number_format($sale['qty'] * $sale['sale_price'], 2) ?></td>
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
