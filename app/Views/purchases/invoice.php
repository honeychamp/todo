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
                    <h4 class="fw-800 text-dark mb-0 text-uppercase">Purchase Voucher</h4>
                    <p class="text-muted small m-0">#PV-<?= str_pad($purchase['id'], 5, '0', STR_PAD_LEFT) ?></p>
                    <p class="text-muted small m-0"><?= date('d M, Y h:i A', strtotime($purchase['created_at'])) ?></p>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-md-6">
                    <h6 class="text-muted text-uppercase fw-bold small">Supplier Details</h6>
                    <div class="fw-800 text-dark"><?= esc($purchase['vendor_name'] ?: 'Unknown Vendor') ?></div>
                    <div class="text-muted small"><?= esc($purchase['vendor_phone'] ?: 'No Phone Provided') ?></div>
                    <div class="text-muted small"><?= esc($purchase['vendor_address'] ?: 'No Address Provided') ?></div>
                </div>
                <div class="col-md-6 text-end">
                    <h6 class="text-muted text-uppercase fw-bold small">Batch Reference</h6>
                    <div class="badge bg-primary rounded-pill px-3 py-2 fs-6 mb-2">BATCH: <?= esc($purchase['batch_id']) ?></div>
                    <div class="text-muted small">Expiry: <span class="fw-bold text-dark"><?= date('M Y', strtotime($purchase['expiry_date'])) ?></span></div>
                </div>
            </div>

            <div class="table-responsive mb-5">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr class="text-muted small text-uppercase">
                            <th class="border-top-0 border-bottom">Particulars</th>
                            <th class="border-top-0 border-bottom text-center">Unit Cost</th>
                            <th class="border-top-0 border-bottom text-center">Qty Bought</th>
                            <th class="border-top-0 border-bottom text-end">Total Line Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $total_amount = $purchase['initial_qty'] * $purchase['cost'];
                        ?>
                        <tr>
                            <td>
                                <div class="fw-800 text-dark"><?= esc($purchase['product_name']) ?></div>
                                <div class="text-muted small">Registered Inventory Stock Entry</div>
                            </td>
                            <td class="text-center">Rs. <?= number_format($purchase['cost'], 2) ?></td>
                            <td class="text-center fw-bold fs-5"><?= number_format($purchase['initial_qty']) ?> Units</td>
                            <td class="text-end fw-900 fs-5">Rs. <?= number_format($total_amount, 2) ?></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end border-0 pt-4 fw-800 h4 m-0 text-muted">Voucher Net Total:</td>
                            <td class="text-end border-0 pt-4 fw-900 h3 m-0 text-primary">Rs. <?= number_format($total_amount, 2) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="alert alert-light border-0 rounded-4 text-center mt-5 mb-0 p-4">
                <p class="m-0 text-muted small"><i class="fas fa-file-invoice-dollar me-1"></i> This is an official inventory purchase record. For financial clearance, check the vendor ledger.</p>
                <div class="fw-800 mt-2 text-dark">Verified by Store Manager</div>
            </div>
        </div>

        <div class="mt-4 text-center">
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-5 py-3 shadow-lg me-2">
                <i class="fas fa-print me-2"></i> Print Voucher
            </button>
            <a href="<?= base_url('purchases') ?>" class="btn btn-outline-primary rounded-pill px-5 py-3 shadow-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to Logs
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
