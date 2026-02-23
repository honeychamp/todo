<?= $this->extend('layout') ?>

<?= $this->section('content') ?>
<div class="row g-4 animate-wow">
    <div class="col-lg-6 mx-auto">
        <div class="glass-card">
            <div class="card-header-premium">
                <h5 class="m-0">User Profile</h5>
            </div>
            <div class="p-4">
                <div class="text-center mb-4">
                    <div class="bg-indigo-subtle text-indigo rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-user-circle fs-1"></i>
                    </div>
                    <h4><?= session()->get('username') ?></h4>
                    <p class="text-muted small">System Administrator</p>
                </div>

                <hr class="opacity-10 mb-4">

                <h6 class="fw-bold mb-3">Change Password</h6>
                
                <?php if(session()->getFlashdata('errors')): ?>
                    <div class="alert alert-danger border-0 small rounded-3 mb-4">
                        <ul class="mb-0">
                        <?php foreach(session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('auth/updatePassword') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">NEW PASSWORD</label>
                        <input type="password" name="password" class="form-control bg-light border-0" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">CONFIRM NEW PASSWORD</label>
                        <input type="password" name="confpassword" class="form-control bg-light border-0" placeholder="Re-type password" required>
                    </div>
                    <button type="submit" class="btn btn-premium w-100 py-3">
                        <i class="fas fa-key me-2"></i> Update Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
