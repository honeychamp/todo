<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-12 text-center mb-2">
        <div class="bg-white d-inline-block p-4 px-5 rounded-pill shadow-sm border border-danger border-opacity-25">
            <h4 class="m-0 fw-900 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Pharmacy Shortage List</h4>
            <p class="text-muted small m-0 mt-1">Products that are low or out of stock and need urgent ordering.</p>
        </div>
    </div>

    <div class="col-12">
        <div class="premium-list p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-5">Product Details</th>
                            <th>Category</th>
                            <th class="text-center">Current Stock</th>
                            <th class="text-center">Status</th>
                            <th class="text-end px-5">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($shortage_items)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <h5 class="text-success fw-bold"><i class="fas fa-check-circle me-2"></i>Inventory is healthy!</h5>
                                    <p class="text-muted m-0">No products are currently low in stock.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($shortage_items as $item): ?>
                                <tr>
                                    <td class="px-5">
                                        <div class="fw-800 fs-5"><?= esc($item['name']) ?></div>
                                        <div class="text-muted small"><?= esc($item['unit_value']) ?><?= esc($item['unit']) ?> Strength</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-3"><?= esc($item['category_name'] ?: 'N/A') ?></span>
                                    </td>
                                    <td class="text-center">
                                        <div class="fw-900 fs-4 text-danger"><?= $item['total_qty'] ?? 0 ?></div>
                                        <small class="text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Units Remaining</small>
                                    </td>
                                    <td class="text-center">
                                        <?php if(($item['total_qty'] ?? 0) <= 0): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm">CRITICAL: EMPTY</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 shadow-sm">ORDER SOON</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end px-5">
                                        <a href="<?= base_url('purchases/select_vendor') ?>" class="btn btn-vibrant rounded-pill px-4 btn-sm">
                                            <i class="fas fa-plus me-1"></i> Restock Now
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

<?= $this->endSection() ?>
