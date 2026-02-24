<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-up">
    <!-- Quick Add Vendor -->
    <div class="col-lg-4">
        <div class="premium-list">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-3 p-3" style="background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                    <i class="fas fa-building-columns text-white fs-5"></i>
                </div>
                <div>
                    <h5 class="m-0 fw-800">Add Vendor</h5>
                    <p class="text-muted small m-0">Add new suppliers to your network.</p>
                </div>
            </div>
                <form action="<?= base_url('vendors/create') ?>" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Vendor Name</label>
                        <input type="text" name="name" class="form-control bg-light border-0" placeholder="e.g. Pfizer Global" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Phone Number</label>
                        <input type="text" name="phone" class="form-control bg-light border-0" placeholder="+1..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" name="email" class="form-control bg-light border-0" placeholder="orders@vendor.com">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small">Address</label>
                        <textarea name="address" class="form-control bg-light border-0" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-premium w-100">
                        <i class="fas fa-plus me-2"></i> Add Vendor
                    </button>
                </form>
        </div>
    </div>

    <!-- Vendor Table -->
    <div class="col-lg-8">
        <div class="premium-list p-0" style="border-radius: 28px;">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-800">Vendors List</h5>
                <span class="badge rounded-pill px-4 py-2 fw-bold" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">Total: <?= count($vendors) ?></span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Vendor Name</th>
                                <th class="border-0 py-3">Contact</th>
                                <th class="border-0 py-3">Address</th>
                                <th class="border-0 py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($vendors)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="opacity-25 py-4">
                                            <i class="fas fa-truck-ramp-box fs-1 mb-3"></i>
                                            <p class="m-0">No vendors added yet.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($vendors as $vendor): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold"><?= esc($vendor['name']) ?></div>
                                            <div class="text-muted small"><?= esc($vendor['email'] ?: 'No email') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold small"><?= esc($vendor['phone']) ?></div>
                                            <div class="small text-muted">Vendor</div>
                                        </td>
                                        <td class="small text-muted" style="max-width: 200px;"><?= esc($vendor['address'] ?: 'N/A') ?></td>
                                        <td class="text-end px-4">
                                            <button class="btn btn-sm btn-outline-warning border-0 rounded-pill px-3 me-1"
                                                onclick="openEditVendor(<?= $vendor['id'] ?>, '<?= esc($vendor['name'], 'js') ?>', '<?= esc($vendor['phone'], 'js') ?>', '<?= esc($vendor['email'], 'js') ?>', '<?= esc($vendor['address'], 'js') ?>')"
                                                data-bs-toggle="modal" data-bs-target="#editVendorModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="<?= base_url('vendors/delete/'.$vendor['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Delete this vendor?')">
                                                <i class="fas fa-trash-alt"></i>
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

<!-- Edit Vendor Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-warning"></i> Edit Vendor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('vendors/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_vendor_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Vendor Name</label>
                        <input type="text" class="form-control bg-light border-0" name="name" id="edit_vendor_name" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Phone</label>
                        <input type="text" class="form-control bg-light border-0" name="phone" id="edit_vendor_phone" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Email</label>
                        <input type="email" class="form-control bg-light border-0" name="email" id="edit_vendor_email" style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Address</label>
                        <textarea class="form-control bg-light border-0" name="address" id="edit_vendor_address" rows="2" style="padding: 12px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-white rounded-4">
                        <i class="fas fa-save me-2"></i> Save Changes
                    </button>
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
