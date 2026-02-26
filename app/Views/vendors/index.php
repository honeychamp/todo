<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .network-summary-card {
        background: white;
        border-radius: 40px;
        padding: 40px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
    }
    .creditor-pill {
        background: #fef2f2;
        border: 1px solid #fee2e2;
        border-radius: 100px;
        padding: 5px 15px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 800;
        font-size: 0.75rem;
        color: #ef4444;
    }
    .vendor-card-v2 {
        background: white;
        border-radius: 35px;
        padding: 30px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        height: 100%;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .vendor-card-v2:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px rgba(0,0,0,0.08);
        border-color: #3b82f6;
    }
    .vendor-icon {
        width: 55px; height: 55px;
        border-radius: 18px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 900;
        color: #3b82f6;
    }
</style>

<div class="animate-wow">
    <!-- Supply Network Header -->
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="network-summary-card h-100 d-flex flex-column justify-content-center">
                <h2 class="fw-900 m-0">Supply Network</h2>
                <p class="text-muted m-0 mt-2">Managing relationships and debts with all medical wholesalers.</p>
                <div class="mt-4 d-flex gap-4">
                    <div>
                        <div class="text-muted extra-small fw-900 text-uppercase tracking-widest">Total Active Dues</div>
                        <div class="h2 fw-900 text-danger m-0">Rs. <?= number_format($total_dues, 0) ?></div>
                    </div>
                    <div class="border-start ps-4">
                        <div class="text-muted extra-small fw-900 text-uppercase tracking-widest">Linked Suppliers</div>
                        <div class="h2 fw-900 text-primary m-0"><?= count($vendors) ?> Entities</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="network-summary-card h-100">
                <h6 class="fw-900 mb-3"><i class="fas fa-triangle-exclamation text-warning me-2"></i> High Priority Creditors</h6>
                <div class="d-flex flex-column gap-2">
                    <?php if(empty($top_creditors)): ?>
                        <div class="p-3 bg-light rounded-4 text-center text-muted small fw-bold">No outstanding dues. Cash flow is clean!</div>
                    <?php else: ?>
                        <?php foreach($top_creditors as $tc): ?>
                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-4">
                                <span class="fw-900 text-dark"><?= esc($tc['name']) ?></span>
                                <span class="creditor-pill">Rs. <?= number_format($tc['balance'], 0) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Management Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h4 class="fw-900 m-0">All Suppliers</h4>
        <button class="btn btn-primary rounded-pill px-5 py-3 fw-900 shadow-lg" data-bs-toggle="modal" data-bs-target="#addVendorModal">
            <i class="fas fa-plus-circle me-2"></i> REGISTER NEW SUPPLIER
        </a>
    </div>

    <!-- Vendor Grid v2 -->
    <div class="row g-4 animate-up">
        <?php foreach($vendors as $vendor): ?>
            <div class="col-xl-4 col-md-6">
                <div class="vendor-card-v2">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="vendor-icon"><?= substr($vendor['name'], 0, 1) ?></div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown" style="width:35px;height:35px;"><i class="fas fa-ellipsis-v"></i></button>
                                <ul class="dropdown-menu dropdown-menu-end rounded-4 border-0 shadow-lg p-2">
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2" href="#" onclick="openEditVendor(<?= $vendor['id'] ?>, '<?= esc($vendor['name'], 'js') ?>', '<?= esc($vendor['phone'], 'js') ?>', '<?= esc($vendor['email'], 'js') ?>', '<?= esc($vendor['address'], 'js') ?>')" data-bs-toggle="modal" data-bs-target="#editVendorModal"><i class="fas fa-edit me-2 text-warning"></i> Edit Details</a></li>
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2" href="<?= base_url('purchases/vendor/'.$vendor['id']) ?>"><i class="fas fa-history me-2 text-primary"></i> Trade History</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2 text-danger" href="<?= base_url('vendors/delete/'.$vendor['id']) ?>" onclick="return confirm('Archive this supplier?')"><i class="fas fa-trash-alt me-2"></i> Remove Record</a></li>
                                </ul>
                            </div>
                        </div>
                        <h4 class="fw-900 text-dark mb-1"><?= esc($vendor['name']) ?></h4>
                        <div class="text-muted small fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-envelope text-primary"></i> <?= esc($vendor['email'] ?: 'No primary email') ?>
                        </div>
                        <div class="p-3 bg-light rounded-4 mb-4">
                            <div class="d-flex align-items-center gap-3 mb-2">
                                <i class="fas fa-phone-volume text-success"></i>
                                <span class="fw-900 text-dark small"><?= esc($vendor['phone']) ?></span>
                            </div>
                            <div class="d-flex align-items-start gap-3">
                                <i class="fas fa-map-location-dot text-info"></i>
                                <span class="text-muted extra-small fw-bold lh-sm"><?= esc($vendor['address'] ?: 'Global Supplier Address') ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div class="fw-900 small text-muted">ACCOUNT STANDING</div>
                        <?php if($vendor['balance'] > 0): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill fw-900">Rs. <?= number_format($vendor['balance'], 0) ?> Due</span>
                        <?php else: ?>
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill fw-900">Cleared</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Add Vendor Modal -->
<div class="modal fade" id="addVendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="modal-header bg-dark text-white border-0 p-5 pb-4">
                <h4 class="modal-title fw-900">Register Supplier</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('vendors/create') ?>" method="POST">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Business Name</label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" placeholder="e.g. Pfizer Dist." required>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Phone / WhatsApp</label>
                            <input type="text" name="phone" class="form-control bg-light border-0 px-4 py-3 rounded-pill" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Primary Email</label>
                            <input type="email" name="email" class="form-control bg-light border-0 px-4 py-3 rounded-pill">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Office Address</label>
                        <textarea name="address" class="form-control bg-light border-0 px-4 py-3 rounded-4" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg">CONFIRM REGISTRATION</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vendor Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="modal-header bg-dark text-white border-0 p-5 pb-4">
                <h4 class="modal-title fw-900">Update Supplier Info</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('vendors/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_vendor_id">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Business Name</label>
                        <input type="text" name="name" id="edit_vendor_name" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" required>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Phone</label>
                            <input type="text" name="phone" id="edit_vendor_phone" class="form-control bg-light border-0 px-4 py-3 rounded-pill" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Email</label>
                            <input type="email" name="email" id="edit_vendor_email" class="form-control bg-light border-0 px-4 py-3 rounded-pill">
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Address</label>
                        <textarea name="address" id="edit_vendor_address" class="form-control bg-light border-0 px-4 py-3 rounded-4" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg">SAVE ALTERATIONS</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditVendor(id, name, phone, email, address) {
    document.getElementById('edit_vendor_id').value = id;
    document.getElementById('edit_vendor_name').value = name;
    document.getElementById('edit_vendor_phone').value = phone;
    document.getElementById('edit_vendor_email').value = email;
    document.getElementById('edit_vendor_address').value = address;
}
</script>

<?= $this->endSection() ?>

