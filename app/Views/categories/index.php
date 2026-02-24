<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-5 animate-wow">
    <!-- Quick Classification Entry -->
    <div class="col-lg-4">
        <div class="premium-list p-5 h-100">
            <div class="mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-3 shadow-lg">
                    <i class="fas fa-tags fs-4"></i>
                </div>
                <h3 class="fw-800">Add Category</h3>
                <p class="text-muted small">Add a new category to group your medicines.</p>
            </div>

            <form action="<?= base_url('categories/create') ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">CATEGORY NAME</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-white border-2" placeholder="e.g. Antibiotics" required>
                </div>
                <button type="submit" class="btn btn-vibrant w-100 py-3">
                    <i class="fas fa-plus-circle me-2"></i> Add Category
                </button>
            </form>
            
            <div class="mt-5 p-4 bg-primary-subtle rounded-4">
                <div class="small fw-bold text-primary mb-2"><i class="fas fa-lightbulb me-2"></i>Tip</div>
                <p class="small text-primary-emphasis m-0 opacity-75">Organizing medicines into categories makes it easier to manage your inventory.</p>
            </div>
        </div>
    </div>

    <!-- Category Database -->
    <div class="col-lg-8">
        <div class="premium-table-card border-0">
            <div class="p-4 px-5 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h4 class="fw-800 m-0">All Categories</h4>
                <span class="badge bg-indigo-subtle text-indigo p-2 px-4 rounded-pill fw-bold">Total: <?= count($categories) ?></span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-5">Category Name</th>
                                <th class="text-center">Date Added</th>
                                <th class="text-end px-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas fa-folder-open fs-1 text-muted opacity-25 mb-3"></i>
                                        <p class="text-muted">No categories added yet.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($categories as $category): ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded-3 p-2 me-3 border"><i class="fas fa-tag text-indigo"></i></div>
                                                <div class="fw-bold"><?= esc($category['name']) ?></div>
                                            </div>
                                        </td>
                                        <td class="text-center text-muted small"><?= date('D, d M Y') ?></td>
                                        <td class="text-end px-5">
                                            <button class="btn btn-sm btn-outline-warning border-0 rounded-pill px-3 me-1"
                                                onclick="openEditCat(<?= $category['id'] ?>, '<?= esc($category['name'], 'js') ?>')"
                                                data-bs-toggle="modal" data-bs-target="#editCatModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="<?= base_url('categories/delete/'.$category['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Delete this category?')">
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

<!-- Edit Category Modal -->
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-warning"></i> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('categories/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_cat_id">
                <div class="modal-body p-4">
                    <label class="form-label fw-bold small">Category Name</label>
                    <input type="text" class="form-control bg-light border-0" name="name" id="edit_cat_name" required style="padding: 14px;">
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
function openEditCat(id, name) {
    document.getElementById('edit_cat_id').value = id;
    document.getElementById('edit_cat_name').value = name;
}
</script>

<?= $this->endSection() ?>
