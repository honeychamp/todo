<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .category-entry-panel {
        background: white;
        border-radius: 40px;
        padding: 45px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        height: 100%;
    }
    .classification-list {
        background: white;
        border-radius: 40px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .category-token {
        width: 45px; height: 45px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        color: #3b82f6;
    }
    .category-row {
        padding: 20px 30px;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.2s;
    }
    .category-row:hover { background: #fcfdfe; }
    .category-row:last-child { border-bottom: none; }
</style>

<div class="row g-5 animate-wow">
    <!-- Classification Desk (Add Form) -->
    <div class="col-lg-4">
        <div class="category-entry-panel animate-up">
            <div class="mb-5">
                <div class="bg-primary text-white rounded-4 d-inline-flex p-3 mb-4 shadow-lg shadow-primary-subtle">
                    <i class="fas fa-layer-group fs-4"></i>
                </div>
                <h3 class="fw-900 m-0">Define Classification</h3>
                <p class="text-muted mt-2">Create logical groups for your pharmacy inventory.</p>
            </div>

            <form action="<?= base_url('categories/create') ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Classification Label</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" placeholder="e.g. Antibiotics" required>
                </div>
                <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg">
                    <i class="fas fa-plus-circle me-2"></i> RECORD CATEGORY
                </button>
            </form>
            
            <div class="mt-5 p-4 rounded-4" style="background: rgba(59, 130, 246, 0.05); border: 1px dashed rgba(59, 130, 246, 0.2);">
                <div class="small fw-900 text-primary mb-2"><i class="fas fa-circle-info me-2"></i>System Note</div>
                <p class="small text-muted m-0 fw-500">Classification helps in generating specialized sales reports and inventory audits.</p>
            </div>
        </div>
    </div>

    <!-- Category Database -->
    <div class="col-lg-8">
        <div class="classification-list animate-up">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center bg-white">
                <div>
                    <h4 class="fw-900 m-0">Library Database</h4>
                    <p class="text-muted small m-0 mt-1">Currently registered product classifications.</p>
                </div>
                <span class="badge bg-primary bg-opacity-10 text-primary px-4 py-2 rounded-pill fw-900">
                    TOTAL: <?= count($categories) ?>
                </span>
            </div>
            
            <div class="p-0">
                <?php if(empty($categories)): ?>
                    <div class="text-center py-5 text-muted fw-bold">No classifications defined yet.</div>
                <?php else: ?>
                    <?php foreach($categories as $category): ?>
                        <div class="category-row d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="category-token"><?= substr($category['name'], 0, 1) ?></div>
                                <div>
                                    <div class="fw-900 text-dark"><?= esc($category['name']) ?></div>
                                    <div class="text-muted extra-small fw-bold">REGISTERED: <?= date('d M, Y') ?></div>
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-light rounded-pill px-3 fw-bold"
                                    onclick="openEditCat(<?= $category['id'] ?>, '<?= esc($category['name'], 'js') ?>')"
                                    data-bs-toggle="modal" data-bs-target="#editCatModal">
                                    <i class="fas fa-pen-nib me-1"></i> Edit
                                </button>
                                <a href="<?= base_url('categories/delete/'.$category['id']) ?>" 
                                   class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" 
                                   onclick="return confirm('Archive this classification?')">
                                    <i class="fas fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCatModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="modal-header bg-dark text-white border-0 p-5 pb-4">
                <h4 class="modal-title fw-900"><i class="fas fa-pen-nib text-warning me-2"></i> Update Label</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('categories/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_cat_id">
                <div class="modal-body p-5">
                    <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">New Classification Name</label>
                    <input type="text" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" name="name" id="edit_cat_name" required>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg">
                        <i class="fas fa-save me-2"></i> APPLY CHANGES
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
