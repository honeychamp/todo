<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-5 animate-wow">
    <!-- Quick Classification Entry -->
    <div class="col-lg-4">
        <div class="premium-list p-5 h-100" style="background: linear-gradient(180deg, #ffffff 0%, #f1f5f9 100%);">
            <div class="mb-4">
                <div class="bg-primary text-white rounded-circle d-inline-flex p-3 mb-3 shadow-lg">
                    <i class="fas fa-tags fs-4"></i>
                </div>
                <h3 class="fw-800">New Category</h3>
                <p class="text-muted small">Group your drugs into therapeutic classes for easier management.</p>
            </div>

            <form action="<?= base_url('categories/create') ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label fw-bold small text-muted">CLASS NAME</label>
                    <input type="text" name="name" class="form-control form-control-lg bg-white border-2" placeholder="e.g. Antibiotics" required>
                </div>
                <button type="submit" class="btn btn-vibrant w-100 py-3">
                    <i class="fas fa-plus-circle me-2"></i> Confirm Classification
                </button>
            </form>
            
            <div class="mt-5 p-4 bg-primary-subtle rounded-4">
                <div class="small fw-bold text-primary mb-2"><i class="fas fa-lightbulb me-2"></i>Pro Tip</div>
                <p class="small text-primary-emphasis m-0 opacity-75">Well-structured categories reduce dispensing errors by 40%.</p>
            </div>
        </div>
    </div>

    <!-- Category Database -->
    <div class="col-lg-8">
        <div class="premium-table-card border-0">
            <div class="p-4 px-5 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h4 class="fw-800 m-0">Classification Registry</h4>
                <span class="badge bg-indigo-subtle text-indigo p-2 px-4 rounded-pill fw-bold">Active Classes: <?= count($categories) ?></span>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-5">Name / Identifier</th>
                                <th class="text-center">System Log Date</th>
                                <th class="text-end px-5">Management</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($categories)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5">
                                        <i class="fas fa-folder-open fs-1 text-muted opacity-25 mb-3"></i>
                                        <p class="text-muted">No classification data recorded yet.</p>
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
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm rounded-pill p-2 px-3 border-0" type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2" style="border-radius: 12px;">
                                                    <li><a class="dropdown-item rounded-3 text-danger fw-bold" href="<?= base_url('categories/delete/'.$category['id']) ?>" onclick="return confirm('Archive classification?')">Archive Record</a></li>
                                                </ul>
                                            </div>
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
