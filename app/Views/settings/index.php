<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-lg-8">
        <div class="premium-list p-0 shadow-lg">
            <div class="p-5 border-bottom" style="background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); color: white; border-radius: 28px 28px 0 0;">
                <h2 class="fw-900 m-0"><i class="fas fa-gears me-3 opacity-50"></i>System Configuration</h2>
                <p class="opacity-75 m-0 mt-1">Customize your pharmacy branding and operational defaults.</p>
            </div>
            
            <form action="<?= base_url('settings/update') ?>" method="POST" class="p-5">
                <div class="row g-4">
                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark small">Pharmacy Branding Name</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-4"><i class="fas fa-hospital text-primary"></i></span>
                            <input type="text" name="pharmacy_name" class="form-control bg-light border-0 py-3" value="<?= esc($settings['pharmacy_name'] ?? 'Galaxy Pharmacy') ?>" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">Contact Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-4"><i class="fas fa-phone text-primary"></i></span>
                            <input type="text" name="pharmacy_phone" class="form-control bg-light border-0 py-3" value="<?= esc($settings['pharmacy_phone'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark small">System Currency Symbol</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-4"><i class="fas fa-coins text-primary"></i></span>
                            <input type="text" name="currency_symbol" class="form-control bg-light border-0 py-3" value="<?= esc($settings['currency_symbol'] ?? 'Rs.') ?>">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold text-dark small">Business Address (Shown on Invoices)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light px-4"><i class="fas fa-location-dot text-primary"></i></span>
                            <textarea name="pharmacy_address" class="form-control bg-light border-0 py-3" rows="3"><?= esc($settings['pharmacy_address'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="col-12 mt-5">
                        <button type="submit" class="btn btn-vibrant w-100 py-3 shadow-lg fs-5 fw-bold">
                            <i class="fas fa-save me-2"></i> Update System Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="premium-list bg-dark text-white shadow-none border-0" style="background: #0f172a !important;">
            <h5 class="fw-800 mb-4">System Info</h5>
            <div class="mb-4 pb-4 border-bottom border-light border-opacity-10">
                <div class="text-white-50 small mb-1">Software Version</div>
                <div class="fw-bold fs-5">v2.1.0 (Professional Edition)</div>
            </div>
            <div class="mb-4 pb-4 border-bottom border-light border-opacity-10">
                <div class="text-white-50 small mb-1">Environment</div>
                <div class="badge bg-success rounded-pill px-3">Production</div>
            </div>
            <div class="mb-4">
                <div class="text-white-50 small mb-1">Last Database Sync</div>
                <div class="fw-bold"><?= date('d M, Y h:i A') ?></div>
            </div>
            <div class="p-4 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-20">
                <p class="small m-0 text-white-50">Need technical assistance? Contact our engineering team for specialized support.</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
