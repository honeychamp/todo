<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center animate-wow">
    <div class="col-lg-6">
        <div class="premium-list p-0">
            <div class="p-5 pb-4 border-bottom bg-light">
                <div class="d-flex align-items-center">
                    <div class="bg-dark text-white rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-truck fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-800 mb-0">Add New Vendor</h3>
                        <p class="text-muted small mb-0">Register a new supplier to your network.</p>
                    </div>
                </div>
            </div>
            
            <form action="<?= base_url('vendors/create') ?>" method="POST" id="addVendorForm" novalidate>
                <div class="p-5">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Vendor Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-building"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="name" id="add_name" placeholder="e.g. pharmacy name" required style="padding: 15px;">
                        </div>
                        <div id="add_name_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control border-0 bg-light" name="phone" id="add_phone" maxlength="11" placeholder="03XXXXXXXXX" required style="padding: 15px;">
                            </div>
                            <div id="add_phone_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Email Address <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control border-0 bg-light" name="email" id="add_email" placeholder="example@mail.com" required style="padding: 15px;">
                            </div>
                            <div id="add_email_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Office Address <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-map-marker-alt"></i></span>
                            <textarea class="form-control border-0 bg-light" name="address" id="add_address" rows="3" placeholder="Full address..." required style="padding: 15px;"></textarea>
                        </div>
                        <div id="add_address_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= base_url('vendors') ?>" class="btn btn-light rounded-pill px-4 py-3 fw-bold flex-grow-1">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-dark rounded-pill px-4 py-3 fw-bold flex-grow-2 w-100 shadow-lg">
                            <i class="fas fa-save me-2"></i> Save Vendor
                        </button>
                    </div>
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
    if (input) { 
        input.parentElement.style.border = '2px solid #ef4444'; 
        input.parentElement.style.borderRadius = '12px';
    }
}
function markValid(input) {
    if (input) { 
        input.parentElement.style.border = '2px solid #10b981'; 
        input.parentElement.style.borderRadius = '12px';
    }
}

const addForm = document.getElementById('addVendorForm');
if (addForm) {
    addForm.addEventListener('submit', function(e) {
        let valid = true;

        const name    = document.getElementById('add_name');
        const phone   = document.getElementById('add_phone');
        const email   = document.getElementById('add_email');
        const address = document.getElementById('add_address');

        // Name
        if (!name.value.trim()) {
            showErr('add_name_err', 'Vendor name is required.'); markInvalid(name); valid = false;
        } else if (name.value.trim().length < 3) {
            showErr('add_name_err', 'Name must be at least 3 characters.'); markInvalid(name); valid = false;
        } else { hideErr('add_name_err'); markValid(name); }

        // Phone
        const phoneVal = phone.value.trim().replace(/[^0-9]/g, '');
        phone.value = phoneVal;
        if (!phoneVal) {
            showErr('add_phone_err', 'Phone number is required.'); markInvalid(phone); valid = false;
        } else if (phoneVal.length !== 11) {
            showErr('add_phone_err', `Phone must be exactly 11 digits. You entered ${phoneVal.length}.`); markInvalid(phone); valid = false;
        } else { hideErr('add_phone_err'); markValid(phone); }

        // Email
        if (!email.value.trim()) {
            showErr('add_email_err', 'Email address is required.'); markInvalid(email); valid = false;
        } else if (!/^[^@]+@[^@]+\.[^@]+$/.test(email.value.trim())) {
            showErr('add_email_err', 'Enter a valid email (e.g. name@domain.com).'); markInvalid(email); valid = false;
        } else { hideErr('add_email_err'); markValid(email); }

        // Address
        if (!address.value.trim()) {
            showErr('add_address_err', 'Address is required.'); markInvalid(address); valid = false;
        } else { hideErr('add_address_err'); markValid(address); }

        if (!valid) e.preventDefault();
    });
}
</script>

<?= $this->endSection() ?>
