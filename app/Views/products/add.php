<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center animate-wow">
    <div class="col-lg-6">
        <div class="premium-list p-0">
            <div class="p-5 pb-4 border-bottom bg-light">
                <div class="d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle p-3 me-3" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-capsules fs-3"></i>
                    </div>
                    <div>
                        <h3 class="fw-800 mb-0">Add New Product</h3>
                        <p class="text-muted small mb-0">Enter the details of the new product to register it.</p>
                    </div>
                </div>
            </div>
            
            <form action="<?= base_url('products/create') ?>" method="POST">
                <div class="p-5">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Product Name</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-tag"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="name" placeholder="e.g. Chinavit" required style="padding: 15px;">
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Category</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-layer-group"></i></span>
                                <select class="form-select border-0 bg-light" name="category_id" required style="padding: 15px;">
                                    <option value="" disabled selected>Select Category</option>
                                    <?php foreach($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Dosage Strength & Unit</label>
                        <div class="input-group">
                            <span class="input-group-text border-0 bg-light"><i class="fas fa-bolt"></i></span>
                            <input type="text" class="form-control border-0 bg-light" name="unit_value" placeholder="e.g. 500" required style="padding: 15px;">
                            <select class="form-select border-0 bg-light" name="unit" required style="max-width: 120px; padding: 15px;">
                                <option value="mg">mg</option>
                                <option value="ml">ml</option>
                                <option value="gm">gm</option>
                                <option value="cap">Cap</option>
                                <option value="tab">Tab</option>
                                <option value="syp">Syp</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Cost Price (Rs.)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-money-bill-wave"></i></span>
                                <input type="number" step="0.01" class="form-control border-0 bg-light" name="cost" required style="padding: 15px;">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 mb-2">
                        <h6 class="fw-bold text-primary text-uppercase tracking-widest">Product Registration</h6>
                        <hr class="mt-1 mb-4">
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Form 6 (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control border-0 bg-light" name="form_6" placeholder="Optional" style="padding: 15px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase tracking-wider">Form 7 (Optional)</label>
                            <div class="input-group">
                                <span class="input-group-text border-0 bg-light"><i class="fas fa-file-alt"></i></span>
                                <input type="text" class="form-control border-0 bg-light" name="form_7" placeholder="Optional" style="padding: 15px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3">
                        <a href="<?= base_url('products') ?>" class="btn btn-light rounded-pill px-4 py-3 fw-bold flex-grow-1">
                            <i class="fas fa-arrow-left me-2"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-vibrant rounded-pill px-4 py-3 fw-bold flex-grow-2 w-100">
                            <i class="fas fa-save me-2"></i> Save Product
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
