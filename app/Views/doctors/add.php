<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center animate-wow">
    <div class="col-xl-6">
        <div class="premium-list p-0 bg-white border-0 shadow-lg overflow-hidden" style="border-radius: 35px;">
            <div class="p-5 pb-4 border-bottom bg-light">
                <div class="text-center">
                    <div class="avatar-lg bg-primary bg-opacity-10 text-primary mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-doctor fa-2x"></i>
                    </div>
                    <h2 class="fw-900 text-dark mb-1">Register New Doctor</h2>
                    <p class="text-muted small">Fill in the professional details of the physician.</p>
                </div>
            </div>

            <form action="<?= base_url('doctors/create') ?>" method="POST" id="doctorForm" novalidate>
                <div class="p-5">
                    <div class="mb-4">
                        <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Doctor's Full Name <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-3"><i class="fas fa-user-md"></i></span>
                            <input type="text" name="name" id="doc_name" class="form-control form-control-lg bg-light border-0 py-3 rounded-end-4 fw-bold" placeholder="e.g. Dr. Waleed Khan" autocomplete="off">
                        </div>
                        <div id="doc_name_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light px-3"><i class="fas fa-phone"></i></span>
                                <input type="tel" name="phone" id="doc_phone" class="form-control form-control-lg bg-light border-0 py-3 rounded-end-4 fw-bold" maxlength="11" placeholder="03XXXXXXXXX">
                            </div>
                            <div id="doc_phone_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Clinic Address / Specialization <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-3 align-items-start pt-3"><i class="fas fa-map-location-dot"></i></span>
                            <textarea name="address" id="doc_address" class="form-control bg-light border-0 py-3 rounded-end-4 fw-bold" rows="3" placeholder="Enter clinic or hospital location..."></textarea>
                        </div>
                        <div id="doc_address_err" style="display:none; color:#ef4444; font-size:0.78rem; margin-top:5px; font-weight:700;"></div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= base_url('doctors') ?>" class="btn btn-light rounded-pill px-4 py-3 fw-800 flex-grow-1">
                            CANCEL
                        </a>
                        <button type="submit" class="btn btn-vibrant rounded-pill px-4 py-3 fw-900 shadow-lg text-uppercase tracking-widest flex-grow-1 w-100">
                            <i class="fas fa-save me-2"></i> REGISTER DOCTOR
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

const docForm = document.getElementById('doctorForm');
if (docForm) {
    docForm.addEventListener('submit', function(e) {
        let valid = true;

        const name    = document.getElementById('doc_name');
        const phone   = document.getElementById('doc_phone');
        const address = document.getElementById('doc_address');

        // Name
        if (!name.value.trim()) {
            showErr('doc_name_err', 'Doctor name is required.'); markInvalid(name); valid = false;
        } else if (name.value.trim().length < 3) {
            showErr('doc_name_err', 'Name must be at least 3 characters.'); markInvalid(name); valid = false;
        } else { hideErr('doc_name_err'); markValid(name); }

        // Phone
        const phoneVal = phone.value.trim().replace(/[^0-9]/g, '');
        phone.value = phoneVal;
        if (!phoneVal) {
            showErr('doc_phone_err', 'Phone number is required.'); markInvalid(phone); valid = false;
        } else if (phoneVal.length !== 11) {
            showErr('doc_phone_err', `Phone must be exactly 11 digits. You entered ${phoneVal.length}.`); markInvalid(phone); valid = false;
        } else { hideErr('doc_phone_err'); markValid(phone); }

        // Address
        if (!address.value.trim()) {
            showErr('doc_address_err', 'Clinic address is required.'); markInvalid(address); valid = false;
        } else { hideErr('doc_address_err'); markValid(address); }

        if (!valid) e.preventDefault();
    });
}
</script>

<?= $this->endSection() ?>
