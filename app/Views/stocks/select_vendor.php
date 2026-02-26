<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-up">

    <!-- Page Header -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-800 m-0">Stock Add - Step 1</h4>
                <p class="text-muted small m-0">Pehle vendor select karein, phir us vendor ka stock add hoga.</p>
            </div>
            <a href="<?= base_url('stocks/purchase') ?>" class="btn btn-light rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Back to Purchase Log
            </a>
        </div>
    </div>

    <!-- Vendor Cards -->
    <?php if (empty($vendors)): ?>
        <div class="col-12">
            <div class="premium-list p-5 text-center">
                <div class="opacity-25 py-4">
                    <i class="fas fa-truck-ramp-box fs-1 mb-3"></i>
                    <p class="m-0 fw-bold">Koi vendor registered nahi hai.</p>
                    <p class="text-muted small">Pehle <a href="<?= base_url('vendors') ?>">Vendors</a> section mein vendor add karein.</p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($vendors as $vendor): ?>
            <div class="col-md-4 col-lg-3">
                <div class="vendor-select-card h-100" onclick="window.location='<?= base_url('stocks/add?vendor_id=' . $vendor['id']) ?>'">
                    <div class="vendor-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="vendor-name"><?= esc($vendor['name']) ?></div>
                    <div class="vendor-info">
                        <i class="fas fa-phone me-1"></i> <?= esc($vendor['phone']) ?>
                    </div>
                    <?php if ($vendor['email']): ?>
                        <div class="vendor-info">
                            <i class="fas fa-envelope me-1"></i> <?= esc($vendor['email']) ?>
                        </div>
                    <?php endif; ?>
                    <div class="select-btn mt-3">
                        <i class="fas fa-plus me-2"></i> Is Vendor Ka Stock Add Karein
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

<style>
.vendor-select-card {
    background: white;
    border-radius: 20px;
    padding: 28px 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #f1f5f9;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    text-align: center;
}
.vendor-select-card:hover {
    border-color: #6366f1;
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(99, 102, 241, 0.15);
}
.vendor-icon {
    width: 64px;
    height: 64px;
    border-radius: 18px;
    background: linear-gradient(135deg, #6366f1, #0ea5e9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin: 0 auto 16px auto;
    transition: all 0.3s;
}
.vendor-select-card:hover .vendor-icon {
    transform: scale(1.1) rotate(5deg);
}
.vendor-name {
    font-size: 17px;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 6px;
}
.vendor-info {
    font-size: 12px;
    color: #94a3b8;
    margin-bottom: 3px;
}
.select-btn {
    background: linear-gradient(135deg, #6366f1, #0ea5e9);
    color: white;
    border-radius: 50px;
    padding: 8px 18px;
    font-size: 12px;
    font-weight: 700;
    display: inline-block;
    transition: all 0.3s;
}
.vendor-select-card:hover .select-btn {
    background: linear-gradient(135deg, #4f46e5, #0284c7);
    box-shadow: 0 6px 20px rgba(99,102,241,0.4);
}
</style>

<?= $this->endSection() ?>
