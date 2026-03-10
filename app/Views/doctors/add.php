<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center animate-wow">
    <div class="col-xl-6">
        <div class="premium-list p-5 bg-white border-0 shadow-lg" style="border-radius: 35px;">
            <div class="text-center mb-5">
                <div class="avatar-lg bg-primary bg-opacity-10 text-primary mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 100px; height: 100px;">
                    <i class="fas fa-user-doctor fa-3x"></i>
                </div>
                <h2 class="fw-900 text-dark">Register New Doctor</h2>
                <p class="text-muted">Fill in the details to add a new node to your network.</p>
            </div>

            <form action="<?= base_url('doctors/create') ?>" method="POST" class="px-3">
                <div class="mb-4">
                    <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Doctor's Full Name</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0 py-3 rounded-4 fw-bold" placeholder="e.g. Dr. Muhammad Ali" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Primary Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0 rounded-start-4 px-3 text-muted"><i class="fas fa-phone-alt"></i></span>
                        <input type="tel" name="phone" class="form-control form-control-lg bg-light border-0 py-3 rounded-end-4 phone-input fw-bold" maxlength="11" placeholder="03XXXXXXXXX" required>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="form-label extra-small fw-900 text-uppercase text-muted px-2">Clinic Address / Specialization</label>
                    <textarea name="address" class="form-control bg-light border-0 py-3 rounded-4 fw-bold" rows="3" placeholder="Enter clinic or hospital location..."></textarea>
                </div>

                <div class="d-flex gap-3">
                    <a href="<?= base_url('doctors') ?>" class="btn btn-light w-50 py-3 rounded-pill fw-800">
                        CANCEL
                    </a>
                    <button type="submit" class="btn btn-vibrant w-50 py-3 rounded-pill fw-900 shadow-lg text-uppercase tracking-widest">
                        <i class="fas fa-save me-2"></i> AUTHORIZE
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
