<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-up">
    <!-- Quick Add Vendor -->
    <div class="col-lg-4">
        <div class="glass-card">
            <div class="card-header-premium">
                <h5 class="m-0">Onboard Supplier</h5>
            </div>
            <div class="p-4">
                <p class="text-muted small mb-4">Register new pharmaceutical suppliers to manage your procurement channels efficiently.</p>
                <form action="<?= base_url('vendors/create') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Business Name</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="e.g. Pfizer Global" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Contact Number</label>
                        <input type="text" name="phone" class="form-control bg-light border-0" placeholder="+1..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Communication Email</label>
                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="orders@vendor.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Distribution Office Address</label>
                        <textarea name="address" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-premium w-100">
                        <i class="fas fa-handshake me-2"></i> Register Supplier
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Vendor Table -->
    <div class="col-lg-8">
        <div class="glass-card">
            <div class="card-header-premium">
                <h5 class="m-0">Supplier Registry</h5>
                <span class="badge bg-soft-emerald p-2" style="background: rgba(16, 185, 129, 0.1); color: var(--primary-emerald);">Active Channels: <?= count($vendors) ?></span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Vendor / Business</th>
                                <th class="border-0 py-3">Core Contact</th>
                                <th class="border-0 py-3">Location</th>
                                <th class="border-0 py-3 text-end px-4">Archiving</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($vendors)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="opacity-25 py-4">
                                            <i class="fas fa-truck-ramp-box fs-1 mb-3"></i>
                                            <p class="m-0">No supply partners registered.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($vendors as $vendor): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold"><?= esc($vendor['name']) ?></div>
                                            <div class="text-muted small"><?= esc($vendor['email'] ?: 'No email log') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold small"><?= esc($vendor['phone']) ?></div>
                                            <div class="small text-muted">Procurement Lead</div>
                                        </td>
                                        <td class="small text-muted" style="max-width: 200px;"><?= esc($vendor['address'] ?: 'Remote Distribution') ?></td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('vendors/delete/'.$vendor['id']) ?>" 
                                               class="btn btn-outline-danger btn-sm rounded-pill p-2"
                                               onclick="return confirm('Remove this supply chain link?')">
                                                <i class="fas fa-trash-alt mx-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
