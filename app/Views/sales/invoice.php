<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 mb-5 animate-wow">
    <div class="col-md-10 mx-auto">
        <!-- Control Panel -->
        <div class="d-flex justify-content-end gap-3 mb-4 d-print-none">
            <button onclick="window.print()" class="btn btn-dark rounded-pill px-4 py-2 shadow-sm fw-bold">
                <i class="fas fa-print me-2"></i> Print Professional Invoice
            </button>
            <a href="<?= base_url('sales') ?>" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold">
                <i class="fas fa-cash-register me-2"></i> Back to POS
            </a>
        </div>

        <!-- Printable Area -->
        <div class="invoice-container bg-white p-5 shadow-lg border-0" id="printableInvoice">
            
            <!-- Header section -->
            <div class="row mb-5 border-bottom pb-4">
                <div class="col-6">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary text-white rounded p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-prescription-bottle-medical fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="fw-900 text-dark m-0 text-uppercase tracking-widest" style="letter-spacing: 1px;"><?= esc(get_setting('pharmacy_name', 'Galaxy Pharmacy')) ?></h2>
                            <p class="text-muted small m-0 fw-bold"><?= esc(get_setting('pharmacy_tagline', 'Your Health, Our Priority')) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-end">
                    <h1 class="fw-900 text-uppercase text-primary m-0" style="letter-spacing: 2px;">INVOICE</h1>
                    <div class="mt-3 text-dark">
                        <span class="fw-bold me-2">Invoice No:</span> 
                        <span class="fs-5 fw-900">#<?= esc($sale['invoice_no'] ?: 'S-'.str_pad($sale['id'], 5, '0', STR_PAD_LEFT)) ?></span>
                    </div>
                    <div class="text-muted small">
                        <span class="fw-bold me-2">Date & Time:</span> <?= date('d M, Y - h:i A', strtotime($sale['sale_date'])) ?>
                    </div>
                    <div class="text-muted small">
                        <span class="fw-bold me-2">Salesperson:</span> <?= session()->get('username') ?: 'Admin (System)' ?>
                    </div>
                    <div class="text-muted small">
                        <span class="fw-bold me-2">Payment Mode:</span> <?= !empty($sale['doctor_id']) ? 'Credit/Account' : 'Cash' ?>
                    </div>
                </div>
            </div>

            <!-- Address and Payer section -->
            <div class="row mb-5">
                <div class="col-6">
                    <h6 class="text-uppercase fw-900 text-muted small mb-3 tracking-widest border-bottom pb-2 d-inline-block">Billed From:</h6>
                    <div class="fw-bold text-dark fs-5"><?= esc(get_setting('pharmacy_name', 'Galaxy Pharmacy')) ?></div>
                    <div class="text-muted small mt-2" style="max-width: 250px;">
                        <i class="fas fa-map-marker-alt me-2 text-primary" style="width:15px"></i><?= esc(get_setting('pharmacy_address', 'Address not set')) ?>
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-phone-alt me-2 text-primary" style="width:15px"></i><?= esc(get_setting('pharmacy_phone', 'Phone not set')) ?>
                    </div>
                    <div class="text-muted small mt-1">
                        <i class="fas fa-envelope me-2 text-primary" style="width:15px"></i>support@galaxypharmacy.com
                    </div>
                </div>
                
                <div class="col-6 text-end">
                    <div class="p-4 rounded-4 border <?= !empty($sale['doctor_id']) ? 'border-primary bg-primary bg-opacity-10' : 'border-success bg-success bg-opacity-10' ?> d-inline-block text-start w-100" style="max-width: 350px;">
                        <h6 class="text-uppercase fw-900 text-dark small mb-2 tracking-widest border-bottom border-dark border-opacity-25 pb-2">Billed To (Customer):</h6>
                        <?php if(!empty($sale['doctor_name'])): ?>
                            <div class="fw-900 text-dark fs-5 mb-1"><i class="fas fa-user-md text-primary me-2"></i><?= esc($sale['doctor_name']) ?></div>
                            <div class="text-muted small mb-2"><i class="fas fa-phone-alt text-muted me-2"></i><?= esc($sale['doctor_phone'] ?: 'No Contact Provided') ?></div>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary-subtle rounded-pill px-3 py-1 mt-1"><i class="fas fa-book-medical me-1"></i> Doctor Account</span>
                        <?php else: ?>
                            <div class="fw-900 text-dark fs-5 mb-1"><i class="fas fa-user text-secondary me-2"></i><?= esc($sale['customer_name'] ?: 'Walk-in Customer') ?></div>
                            <div class="text-muted small mb-2"><i class="fas fa-phone-alt text-muted me-2"></i><?= esc($sale['customer_phone'] ?: 'No Contact Provided') ?></div>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle rounded-pill px-3 py-1 mt-1"><i class="fas fa-money-bill-wave me-1"></i> Cash Sale</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <div class="mb-4 rounded-4 overflow-hidden border">
                <table class="table invoice-table m-0">
                    <thead style="background-color: #f1f5f9;">
                        <tr class="text-uppercase text-dark tracking-widest" style="font-size: 0.8rem;">
                            <th class="py-3 px-4 border-0">#</th>
                            <th class="py-3 border-0">Item Description</th>
                            <th class="py-3 border-0">Batch ID</th>
                            <th class="py-3 text-center border-0">Qty</th>
                            <th class="py-3 text-end border-0">Rate (Rs)</th>
                            <th class="py-3 px-4 text-end border-0">Net Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; foreach($items as $item): ?>
                        <tr>
                            <td class="px-4 text-muted fw-bold"><?= $i++ ?></td>
                            <td>
                                <div class="fw-bold text-dark fs-6"><?= esc($item['product_name']) ?></div>
                                <div class="text-muted small"><?= esc($item['strength'] ?: $item['unit_value'] . ' ' . $item['unit']) ?></div>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace text-muted"><?= esc($item['batch_id']) ?></span></td>
                            <td class="text-center fw-900 fs-5"><?= number_format($item['qty']) ?></td>
                            <td class="text-end fw-bold text-muted">Rs. <?= number_format($item['sale_price'], 2) ?></td>
                            <td class="text-end px-4 fw-900 text-dark fs-6">Rs. <?= number_format($item['qty'] * $item['sale_price'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="row mt-4 pt-4">
                <div class="col-md-7">
                    <div class="pe-5 text-muted small mt-4 pt-2">
                        <div class="fw-bold text-dark mb-2 text-uppercase tracking-widest"><i class="fas fa-file-contract me-2"></i>Terms & Conditions</div>
                        <ul class="ps-3 mb-0" style="line-height: 1.8;">
                            <li>Medicines cannot be returned or exchanged after sale without a valid batch defect or expiry issue.</li>
                            <li>Please verify all items and quantities before leaving the counter.</li>
                            <li>In case of discrepancy, contact support within 24 hours of receipt generation.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="bg-light p-4 rounded-4 border">
                        <table class="table table-borderless m-0 summary-table">
                            <tr>
                                <td class="text-muted fw-bold pb-2">Subtotal Amount:</td>
                                <td class="text-end fw-bold text-dark pb-2">Rs. <?= number_format($sale['gross_amount'], 2) ?></td>
                            </tr>
                            <?php if($sale['total_discount'] > 0): ?>
                            <tr>
                                <td class="text-danger fw-bold pb-2">Discount Applied (-):</td>
                                <td class="text-end fw-bold text-danger pb-2">Rs. <?= number_format($sale['total_discount'], 2) ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="2"><div class="border-top border-dark opacity-10 my-2"></div></td>
                            </tr>
                            <tr>
                                <td class="text-dark fw-900 fs-5 pt-2">Net Payable Bill:</td>
                                <td class="text-end fw-900 fs-4 text-primary pt-2">Rs. <?= number_format($sale['total_amount'], 2) ?></td>
                            </tr>
                        </table>
                    </div>

                    <?php if(!empty($sale['doctor_id'])): ?>
                    <div class="mt-4 border border-danger-subtle rounded-4 overflow-hidden position-relative">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-danger opacity-5" style="z-index: 0;"></div>
                        <div class="p-3 bg-danger text-white text-center fw-bold text-uppercase tracking-widest" style="font-size: 0.8rem; z-index: 1; position: relative;">
                            Doctor Account Statement
                        </div>
                        <div class="p-4 bg-white position-relative" style="z-index: 1;">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted fw-bold small">Previous Balance:</span>
                                <span class="fw-bold text-dark">Rs. <?= number_format($previous_balance ?? 0, 2) ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span class="text-muted fw-bold small">Current Bill Added:</span>
                                <span class="fw-bold text-dark">+ Rs. <?= number_format($current_bill ?? 0, 2) ?></span>
                            </div>
                            <div class="border-top mb-3"></div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-danger fw-900 text-uppercase" style="font-size: 0.85rem;">Total Outstanding Debt:</span>
                                <span class="fw-900 text-danger fs-4">Rs. <?= number_format($doctor_balance ?? 0, 2) ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer / Signatures -->
            <div class="row pt-5 mt-5">
                <div class="col-4 text-center">
                    <div style="font-family: 'Courier New', Courier, monospace; font-size: 24px; font-weight: bold; letter-spacing: -2px; opacity: 0.3; transform: scaleY(2);">
                        ||||| |||| || ||| |||
                    </div>
                    <div class="extra-small text-muted mt-1"><?= esc($sale['invoice_no'] ?: 'S-'.str_pad($sale['id'], 5, '0', STR_PAD_LEFT)) ?></div>
                </div>
                <div class="col-4 text-center">
                    <div class="fw-900 text-dark fs-5 mt-3">Thank You!</div>
                    <div class="text-muted small">Wishing you good health</div>
                </div>
                <div class="col-4 text-center">
                    <div class="border-bottom border-dark w-75 mx-auto opacity-25" style="margin-top: 35px;"></div>
                    <div class="mt-2 text-dark fw-bold small text-uppercase tracking-widest">Authorized Signature</div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .tracking-widest { letter-spacing: 0.1em; }
    .invoice-container { border-radius: 20px; }
    .invoice-table th { font-weight: 800; color: #334155; }
    .invoice-table td { padding-top: 1rem; padding-bottom: 1rem; vertical-align: middle; border-bottom: 1px dotted #cbd5e1; }
    .invoice-table tbody tr:hover { background-color: #f8fafc; }
    .invoice-table tbody tr:last-child td { border-bottom: none; }
    .summary-table td { padding: 0.35rem 0; }
    
    @media print {
        @page { size: A4; margin: 0; }
        body { background: white !important; margin: 0; padding: 15px; }
        #sidebar, header, .btn, .d-print-none, .bg-blob { display: none !important; }
        #content { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        .invoice-container { box-shadow: none !important; padding: 0 !important; border-radius: 0 !important; border: none !important; }
        .bg-light, thead { background-color: #f1f5f9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-primary { background-color: #0ea5e9 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .bg-danger { background-color: #ef4444 !important; color: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .text-white { color: white !important; }
        .border { border: 1px solid #e2e8f0 !important; }
    }
</style>

<?= $this->endSection() ?>
