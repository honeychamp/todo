<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<style>
    .stat-card {
        border-radius: 24px;
        padding: 20px;
        position: relative;
        overflow: hidden;
        border: none;
        transition: all 0.4s;
        min-height: 140px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.15); }
    
    .card-purple { background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); color: white; }
    .card-cyan { background: linear-gradient(135deg, #2dd4bf 0%, #0ea5e9 100%); color: white; }
    .card-emerald { background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: white; }
    .card-rose { background: linear-gradient(135deg, #f43f5e 0%, #fb7185 100%); color: white; }

    .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.15;
        transform: rotate(-15deg);
    }
    
    .stat-value { font-size: 2.2rem; font-weight: 800; letter-spacing: -1px; }
    .stat-label { font-size: 0.9rem; font-weight: 500; opacity: 0.8; }

    .action-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 10px;
        border-radius: 30px;
        font-size: 0.7rem;
        backdrop-filter: blur(5px);
    }


</style>

<div class="row g-4 mb-5 animate-wow">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-purple shadow-lg">
            <i class="fas fa-pills icon-bg"></i>
            <div>
                <span class="action-badge">Products</span>
            </div>
            <div>
                <div class="stat-value"><?= $total_products ?></div>
                <div class="stat-label">Total Products</div>
            </div>
        </div>
    </div>

    <!-- Today's Profit -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-cyan shadow-lg">
            <i class="fas fa-sack-dollar icon-bg"></i>
            <div>
                <span class="action-badge">Earnings</span>
            </div>
            <div>
                <div class="stat-value">Rs. <?= number_format($today_profit, 2) ?></div>
                <div class="stat-label">Profit Today</div>
            </div>
        </div>
    </div>

    <!-- Items in Stock -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-emerald shadow-lg">
            <i class="fas fa-warehouse icon-bg"></i>
            <div>
                <span class="action-badge">Stock</span>
            </div>
            <div>
                <div class="stat-value"><?= $total_items_in_stock ?></div>
                <div class="stat-label">Items in Stock</div>
            </div>
        </div>
    </div>

    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-rose shadow-lg">
            <i class="fas fa-cash-register icon-bg"></i>
            <div>
                <span class="action-badge">Today</span>
            </div>
            <div>
                <div class="stat-value"><?= $today_sales ?></div>
                <div class="stat-label">Sales Today</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-wow" style="animation-delay: 0.2s;">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="premium-list" style="height: 400px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-800 mb-0 m-0">Sales Overview</h5>
                    <p class="text-muted small m-0">Revenue & inventory tracking</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-vibrant rounded-pill px-3 btn-sm fw-600">Weekly View</button>
                </div>
            </div>
            <div style="height: 280px; width: 100%;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Right Interaction Panel -->
    <div class="col-lg-4">
        <div class="premium-list bg-dark text-white shadow-none" style="background: #0f172a !important; height: 400px;">
            <h5 class="fw-700 mb-3">Quick Actions</h5>
            
            <div class="d-grid gap-2 mb-4">
                <button class="btn btn-vibrant py-2 small" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Product
                </button>
                <a href="<?= base_url('stocks/sales') ?>" class="btn btn-outline-light py-2 border-0 rounded-4 small" style="background: rgba(255,255,255,0.05);">
                    <i class="fas fa-bolt me-1 text-warning"></i> Make a Sale
                </a>
            </div>

            <div style="overflow-y: auto; max-height: 220px; padding-right: 5px;">
                <p class="text-muted small text-uppercase fw-800 tracking-wider mb-2">Alerts</p>
                <div class="list-group list-group-flush">
                    <?php if(empty($expiring_soon) && empty($low_stock)): ?>
                        <div class="text-muted small p-2">No active alerts. System healthy.</div>
                    <?php endif; ?>

                    <?php foreach($expiring_soon as $exp): ?>
                        <div class="bg-transparent border-0 d-flex gap-3 mb-3 p-0">
                            <div class="bg-warning rounded-3 p-2 text-dark" style="width: 40px; height: 40px; text-align: center;"><i class="fas fa-calendar-times"></i></div>
                            <div>
                                <div class="fw-bold small text-warning">Expiring Soon</div>
                                <div class="text-white-50" style="font-size: 0.75rem;"><?= esc($exp['product_name']) ?> (<?= date('d M', strtotime($exp['expiry_date'])) ?>)</div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($low_stock as $ls): ?>
                        <div class="bg-transparent border-0 d-flex gap-3 mb-3 p-0">
                            <div class="bg-danger rounded-3 p-2 text-white" style="width: 40px; height: 40px; text-align: center;"><i class="fas fa-exclamation-triangle"></i></div>
                            <div>
                                <div class="fw-bold small text-danger">Low Stock</div>
                                <div class="text-white-50" style="font-size: 0.75rem;"><?= esc($ls['product_name']) ?>: <?= $ls['qty'] ?> units left</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Registry Table -->
    <div class="col-12 mt-5">
        <div class="premium-table-card">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-800 m-0">Products List</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm border-0 bg-light rounded-pill px-4" placeholder="Search products...">
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-5">Product Name</th>
                                <th>Category</th>
                                <th class="text-center">Cost Price</th>
                                <th>Product Registration</th>
                                <th class="text-end px-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                                <tr><td colspan="6" class="text-center py-5">No products added yet.</td></tr>
                            <?php else: ?>
                                <?php foreach($products as $product): ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="fw-bold d-flex align-items-center">
                                                <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 small"><i class="fas fa-capsules"></i></div>
                                                <?= esc($product['name']) ?>
                                                <small class="text-muted ms-2">(<?= esc($product['unit_value']) . esc($product['unit']) ?>)</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border-primary p-2 px-3 fw-600 rounded-pill">
                                                <?= esc($product['category_name']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center fw-800 text-indigo">Rs. <?= number_format($product['cost'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-dark-subtle text-dark-emphasis rounded-3 px-3">
                                                <?= esc($product['form_6']) ?> / <?= esc($product['form_7']) ?>
                                            </span>
                                        </td>
                                        <td class="text-end px-5">
                                            <a href="<?= base_url('products/delete/'.$product['id']) ?>" class="btn btn-outline-danger btn-sm border-0 rounded-circle" onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash"></i>
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

<!-- Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl" style="border-radius: 40px; overflow: hidden;">
            <div class="modal-header border-0 p-5 pb-0">
                <h3 class="modal-title fw-800">Add New Product</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('products/create') ?>" method="POST">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">PRODUCT NAME</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Paracetamol Extra" required>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">CATEGORY</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">DOSAGE STRENGTH & UNIT</label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="unit_value" placeholder="e.g. 500" required>
                            <select class="form-select" name="unit" required style="max-width: 120px;">
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
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">COST PRICE (Rs.)</label>
                            <input type="number" step="0.01" class="form-control" name="cost" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small text-primary">PRODUCT REGISTRATION</label>
                        <hr class="mt-0 mb-3">
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">FORM 6 (Optional)</label>
                            <input type="text" class="form-control" name="form_6">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">FORM 7 (Optional)</label>
                            <input type="text" class="form-control" name="form_7">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-5">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(14, 165, 233, 0.2)');
        gradient.addColorStop(1, 'rgba(14, 165, 233, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: 'Revenue Flow',
                    data: <?= $chart_values ?>,
                    borderColor: '#0ea5e9',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { 
                        grid: { color: '#f1f5f9', drawBorder: false },
                        border: { display: false },
                        ticks: { callback: value => 'Rs. ' + value }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
