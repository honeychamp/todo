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
    <!-- Vendors Header -->
    <div class="row g-4 mb-5">
        <div class="col-lg-7">
            <div class="network-summary-card h-100 d-flex flex-column justify-content-center">
                <h2 class="fw-900 m-0">Vendor List</h2>
                <p class="text-muted m-0 mt-2">Manage your suppliers and their payments.</p>
                <div class="mt-4 d-flex gap-4">
                    <div>
                        <div class="text-muted extra-small fw-900 text-uppercase tracking-widest">Total Payable</div>
                        <div class="h2 fw-900 text-danger m-0">Rs. <?= number_format($total_dues, 0) ?></div>
                    </div>
                    <div class="border-start ps-4">
                        <div class="text-muted extra-small fw-900 text-uppercase tracking-widest">Total Vendors</div>
                        <div class="h2 fw-900 text-primary m-0"><?= count($vendors) ?> Vendors</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="network-summary-card h-100">
                <h6 class="fw-900 mb-3"><i class="fas fa-triangle-exclamation text-warning me-2"></i> Payment Reminders</h6>
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

    <!-- List Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <h4 class="fw-900 m-0">Vendors</h4>
        <a href="<?= base_url('vendors/add') ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-900 shadow-lg">
            <i class="fas fa-plus-circle me-2"></i> ADD NEW VENDOR
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
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2" href="#" onclick="openEditVendor(<?= $vendor['id'] ?>, '<?= esc($vendor['name'], 'js') ?>', '<?= esc($vendor['phone'], 'js') ?>', '<?= esc($vendor['email'], 'js') ?>', '<?= esc($vendor['address'], 'js') ?>')" data-bs-target="#editVendorModal" data-bs-toggle="modal"><i class="fas fa-edit me-2 text-warning"></i> Edit Vendor</a></li>
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2" href="<?= base_url('purchases/vendor/'.$vendor['id']) ?>"><i class="fas fa-history me-2 text-primary"></i> Trade History</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item rounded-3 fw-bold py-2 text-danger" href="<?= base_url('vendors/delete/'.$vendor['id']) ?>" onclick="return confirm('Are you sure you want to delete this vendor?')"><i class="fas fa-trash-alt me-2"></i> Delete Vendor</a></li>
                                </ul>
                            </div>
                        </div>
                        <h4 class="fw-900 text-dark mb-1"><?= esc($vendor['name']) ?></h4>
                        <div class="text-muted small fw-bold mb-3 d-flex align-items-center gap-2">
                            <i class="fas fa-envelope text-primary"></i> <?= esc($vendor['email'] ?: 'No primary email') ?>
                        </div>
                        <div class="extra-small text-muted fw-bold mb-3">
                            <i class="fas fa-calendar-check me-1"></i> REGISTERED: <?= date('d M, Y', strtotime($vendor['created_at'])) ?>
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
                        <div class="fw-900 small text-muted">BALANCE STATUS</div>
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



<!-- Edit Vendor Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="modal-header bg-dark text-white border-0 p-5 pb-4">
                <h4 class="modal-title fw-900">Edit Vendor</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('vendors/update') ?>" method="POST" id="editVendorForm" novalidate>
                <input type="hidden" name="id" id="edit_vendor_id">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Vendor Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_vendor_name" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill">
                        <div id="edit_name_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Phone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="edit_vendor_phone" class="form-control bg-light border-0 px-4 py-3 rounded-pill" maxlength="11">
                            <div id="edit_phone_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Email <span class="text-danger">*</span></label>
                            <input type="text" name="email" id="edit_vendor_email" class="form-control bg-light border-0 px-4 py-3 rounded-pill">
                            <div id="edit_email_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Address <span class="text-danger">*</span></label>
                        <textarea name="address" id="edit_vendor_address" class="form-control bg-light border-0 px-4 py-3 rounded-4" rows="3"></textarea>
                        <div id="edit_address_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" id="editVendorBtn" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg">UPDATE VENDOR</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showErr(id, msg) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = '⚠ ' + msg;
    el.style.display = 'block';
}
function hideErr(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}
function markInvalid(input) {
    if (input) { input.style.border = '2px solid #ef4444'; input.style.background = '#fff5f5'; }
}
function markValid(input) {
    if (input) { input.style.border = '2px solid #10b981'; input.style.background = '#f0fdf4'; }
}
function markClear(input) {
    if (input) { input.style.border = ''; input.style.background = ''; }
}



// ─── EDIT VENDOR: Validate on Submit ───
const editForm = document.getElementById('editVendorForm');
if (editForm) {
    editForm.addEventListener('submit', function(e) {
        let valid = true;

        const name    = document.getElementById('edit_vendor_name');
        const phone   = document.getElementById('edit_vendor_phone');
        const email   = document.getElementById('edit_vendor_email');
        const address = document.getElementById('edit_vendor_address');

        if (!name.value.trim()) {
            showErr('edit_name_err', 'Vendor name is required.'); markInvalid(name); valid = false;
        } else if (name.value.trim().length < 3) {
            showErr('edit_name_err', 'Name must be at least 3 characters.'); markInvalid(name); valid = false;
        } else { hideErr('edit_name_err'); markValid(name); }

        const phoneVal = phone.value.trim().replace(/[^0-9]/g, '');
        phone.value = phoneVal;
        if (!phoneVal) {
            showErr('edit_phone_err', 'Phone number is required.'); markInvalid(phone); valid = false;
        } else if (phoneVal.length !== 11) {
            showErr('edit_phone_err', `Phone must be exactly 11 digits. You entered ${phoneVal.length}.`); markInvalid(phone); valid = false;
        } else { hideErr('edit_phone_err'); markValid(phone); }

        if (!email.value.trim()) {
            showErr('edit_email_err', 'Email address is required.'); markInvalid(email); valid = false;
        } else if (!/^[^@]+@[^@]+\.[^@]+$/.test(email.value.trim())) {
            showErr('edit_email_err', 'Enter a valid email (e.g. name@domain.com).'); markInvalid(email); valid = false;
        } else { hideErr('edit_email_err'); markValid(email); }

        if (!address.value.trim()) {
            showErr('edit_address_err', 'Address is required.'); markInvalid(address); valid = false;
        } else { hideErr('edit_address_err'); markValid(address); }

        if (!valid) e.preventDefault();
    });
}

// ─── Open Edit Modal with data ───
function openEditVendor(id, name, phone, email, address) {
    document.getElementById('edit_vendor_id').value      = id;
    document.getElementById('edit_vendor_name').value    = name;
    document.getElementById('edit_vendor_phone').value   = phone;
    document.getElementById('edit_vendor_email').value   = email;
    document.getElementById('edit_vendor_address').value = address;
    // Clear error states
    ['edit_vendor_name','edit_vendor_phone','edit_vendor_email','edit_vendor_address'].forEach(function(fid) {
        markClear(document.getElementById(fid));
    });
    ['edit_name_err','edit_phone_err','edit_email_err','edit_address_err'].forEach(hideErr);
}
</script>

<?= $this->endSection() ?>
