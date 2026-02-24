<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center animate-wow">
    <div class="col-lg-6">
        <div class="premium-list p-0">
            <div class="p-5 pb-4 border-bottom bg-light">
                <div class="d-flex align-items-center">
                    <div class="bg-warning text-white rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-edit fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-800 mb-0">Edit Product</h3>
                        <p class="text-muted small mb-0">Update the details of <strong><?= esc($product['name']) ?></strong>.</p>
                    </div>
                </div>
            </div>

            <form action="<?= base_url('products/update') ?>" method="POST">
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                <div class="p-5">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="name" value="<?= esc($product['name']) ?>" required style="padding: 15px;">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Category</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-layer-group"></i></span>
                            <select class="form-select border-0 bg-light" name="category_id" required style="padding: 15px;">
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                        <?= esc($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Dosage Strength & Unit</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-bolt"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="unit_value" value="<?= esc($product['unit_value']) ?>" required style="padding: 15px;">
                            <select class="form-select border-0 bg-light" name="unit" required style="max-width: 120px; padding: 15px;">
                                <?php foreach(['mg','ml','gm','cap','tab','syp'] as $u): ?>
                                    <option value="<?= $u ?>" <?= $product['unit'] == $u ? 'selected' : '' ?>><?= $u ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase">Cost Price (Rs.)</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-money-bill-wave"></i></span>
                            <input type="number" step="0.01" class="form-control border-0 bg-light" name="cost" value="<?= esc($product['cost']) ?>" required style="padding: 15px;">
                        </div>
                    </div>

                    <div class="mt-4 mb-2">
                        <h6 class="fw-bold text-primary text-uppercase">Product Registration</h6>
                        <hr class="mt-1 mb-4">
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Form 6</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control border-0 bg-light" name="form_6" value="<?= esc($product['form_6']) ?>" placeholder="Optional" style="padding: 15px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Form 7</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control border-0 bg-light" name="form_7" value="<?= esc($product['form_7']) ?>" placeholder="Optional" style="padding: 15px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= base_url('products') ?>" class="btn btn-light rounded-pill px-4 py-3 fw-bold flex-grow-1">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 py-3 fw-bold w-100 text-white">
                            <i class="fas fa-save me-2"></i> Update Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
