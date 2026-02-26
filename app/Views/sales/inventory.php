<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #10b981, #059669);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Saleable Stock Value</div>
            <?php 
                $totalAvailableValue = array_sum(array_map(function($p) { return $p['price'] * $p['available_qty']; }, $inventory));
            ?>
            <h3 class="text-white fw-800 m-0 mt-1">Rs. <?= number_format($totalAvailableValue, 2) ?></h3>
            <div class="text-white opacity-60 small mt-1">Selling value of items on shelf</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #6366f1, #0ea5e9);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Items Available</div>
            <?php 
                $totalItems = array_sum(array_column($inventory, 'available_qty'));
            ?>
            <h3 class="text-white fw-800 m-0 mt-1"><?= number_format($totalItems) ?> Units</h3>
            <div class="text-white opacity-60 small mt-1">Total loose items across all active batches</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 text-center border-0 shadow-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="text-white opacity-75 small fw-bold text-uppercase">Active Batches</div>
            <h3 class="text-white fw-800 m-0 mt-1"><?= count($inventory) ?> Batches</h3>
            <div class="text-white opacity-60 small mt-1">Different batches with available stock</div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Current Available Inventory</h5>
                    <p class="text-muted small m-0 mt-1">Calculated as: Original Purchase - Recorded Sales.</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('sales') ?>" class="btn btn-vibrant rounded-pill px-4">
                        <i class="fas fa-cart-plus me-2"></i> Make a Sale
                    </a>
                </div>
            </div>
            
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Batch & Vendor</th>
                                <th class="border-0 py-3">Product Description</th>
                                <th class="border-0 py-3">Expiry</th>
                                <th class="border-0 py-3 text-center">In Stock</th>
                                <th class="border-0 py-3 text-end">Sale Price</th>
                                <th class="border-0 py-3 text-end px-5">Stock Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($inventory)): ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">No stock available. <a href="<?= base_url('purchases/select_vendor') ?>">Add Stock</a> first.</td></tr>
                            <?php else: ?>
                                <?php foreach($inventory as $item): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold text-dark"><code><?= esc($item['batch_id']) ?></code></div>
                                            <div class="text-muted small"><?= esc($item['vendor_name'] ?: 'N/A') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-primary"><?= esc($item['product_name']) ?></div>
                                            <div class="text-muted small"><?= esc($item['product_unit_value']) ?> <?= esc($item['product_unit']) ?></div>
                                        </td>
                                        <td>
                                            <?php 
                                                $daysToExpiry = (strtotime($item['expiry_date']) - time()) / (60 * 60 * 24);
                                                $badgeClass = $daysToExpiry < 90 ? 'bg-danger' : ($daysToExpiry < 180 ? 'bg-warning' : 'bg-success');
                                            ?>
                                            <span class="badge <?= $badgeClass ?> rounded-pill px-3">
                                                <?= date('M Y', strtotime($item['expiry_date'])) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-800 fs-5"><?= number_format($item['available_qty']) ?></div>
                                            <div class="text-muted small">Units (<?= esc($item['initial_qty']) ?> Initial)</div>
                                        </td>
                                        <td class="text-end fw-bold">Rs. <?= number_format($item['price'], 2) ?></td>
                                        <td class="text-end px-5">
                                            <div class="fw-800 text-dark">Rs. <?= number_format($item['price'] * $item['available_qty'], 2) ?></div>
                                            <div class="text-muted small"><?= number_format($item['available_qty']) ?> x <?= number_format($item['price'], 2) ?></div>
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
