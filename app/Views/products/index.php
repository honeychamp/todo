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
    
    .card-purple { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); color: white; }
    .card-cyan { background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%); color: white; }
    .card-emerald { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); color: white; }
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

    .premium-list {
        background: white;
        border-radius: 24px;
        padding: 25px;
        box-shadow: var(--card-shadow);
    }
</style>

<div class="row g-4 mb-5 animate-wow">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-purple shadow-lg">
            <i class="fas fa-pills icon-bg"></i>
            <div>
                <span class="action-badge">Inventory Hub</span>
            </div>
            <div>
                <div class="stat-value"><?= $total_products ?></div>
                <div class="stat-label">Active Formulations</div>
            </div>
        </div>
    </div>

    <!-- Total Categories -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-cyan shadow-lg">
            <i class="fas fa-layer-group icon-bg"></i>
            <div>
                <span class="action-badge">Managed Classes</span>
            </div>
            <div>
                <div class="stat-value"><?= $total_categories ?></div>
                <div class="stat-label">Stock Classifications</div>
            </div>
        </div>
    </div>

    <!-- Items in Stock -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-emerald shadow-lg">
            <i class="fas fa-warehouse icon-bg"></i>
            <div>
                <span class="action-badge">Warehouse Units</span>
            </div>
            <div>
                <div class="stat-value"><?= $total_items_in_stock ?></div>
                <div class="stat-label">Stock Strength</div>
            </div>
        </div>
    </div>

    <!-- Today's Sales -->
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-rose shadow-lg">
            <i class="fas fa-cash-register icon-bg"></i>
            <div>
                <span class="action-badge">Live Revenue Today</span>
            </div>
            <div>
                <div class="stat-value"><?= $today_sales ?></div>
                <div class="stat-label">Retail Transactions</div>
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
                    <h5 class="fw-800 mb-0 m-0">Performance Insights</h5>
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
            <h5 class="fw-700 mb-3">Operations Hub</h5>
            
            <div class="d-grid gap-2 mb-4">
                <button class="btn btn-vibrant py-2 small" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="fas fa-plus-circle me-1"></i> New Drug Entry
                </button>
                <a href="<?= base_url('stocks/sales') ?>" class="btn btn-outline-light py-2 border-0 rounded-4 small" style="background: rgba(255,255,255,0.05);">
                    <i class="fas fa-bolt me-1 text-warning"></i> Sales Terminal
                </a>
            </div>

            <div>
                <p class="text-muted small text-uppercase fw-800 tracking-wider mb-2">System Pulse</p>
                <div class="list-group list-group-flush">
                    <div class="bg-transparent border-0 d-flex gap-3 mb-2 p-0">
                        <div class="bg-warning rounded-3 p-2 text-dark" style="width: 40px; text-align: center;"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="fw-bold small">Expiring Soon</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">Batch #843 (15 Days)</div>
                        </div>
                    </div>
                    <div class="bg-transparent border-0 d-flex gap-3 p-0">
                        <div class="bg-info rounded-3 p-2 text-white" style="width: 40px; text-align: center;"><i class="fas fa-truck-fast"></i></div>
                        <div>
                            <div class="fw-bold small">Incoming Stock</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">GSK Logistics Order</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Registry Table -->
    <div class="col-12 mt-5">
        <div class="premium-table-card">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <h5 class="fw-800 m-0">Drug Registry Database</h5>
                <div class="d-flex gap-2">
                    <input type="text" class="form-control form-control-sm border-0 bg-light rounded-pill px-4" placeholder="Search pharmacy logs...">
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="px-5">Product Matrix</th>
                                <th>Category</th>
                                <th>Supplier Info</th>
                                <th class="text-center">Market Price</th>
                                <th>Regulatory Code</th>
                                <th class="text-end px-5">Archive</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                                <tr><td colspan="6" class="text-center py-5">No laboratory data matched your search.</td></tr>
                            <?php else: ?>
                                <?php foreach($products as $product): ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="fw-bold d-flex align-items-center">
                                                <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 small"><i class="fas fa-capsules"></i></div>
                                                <?= esc($product['name']) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-primary border-primary p-2 px-3 fw-600 rounded-pill">
                                                <?= esc($product['category_name']) ?>
                                            </span>
                                        </td>
                                        <td><i class="fas fa-building-user text-muted me-2 small"></i><?= esc($product['vendor']) ?></td>
                                        <td class="text-center fw-800 text-indigo">$<?= number_format($product['cost'], 2) ?></td>
                                        <td><span class="badge bg-dark-subtle text-dark-emphasis rounded-3 px-3">FORM7-<?= esc($product['reg_number']) ?></span></td>
                                        <td class="text-end px-5">
                                            <a href="<?= base_url('products/delete/'.$product['id']) ?>" class="btn btn-outline-danger btn-sm border-0 rounded-circle" onclick="return confirm('Archive record?')">
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
                <h3 class="modal-title fw-800">New Clinical Entry</h3>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('products/create') ?>" method="POST">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-muted">DRUG NOMENCLATURE</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Paracetamol Extra" required>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">CLASSIFICATION</label>
                            <select class="form-select" name="category_id" required>
                                <?php foreach($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">PRINCIPAL VENDOR</label>
                            <input type="text" class="form-control" name="vendor" required>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">UNIT COST ($)</label>
                            <input type="number" step="0.01" class="form-control" name="cost" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small text-muted">REG LICENSE #</label>
                            <input type="text" class="form-control" name="reg_number" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-5">Initialize Deployment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['01 Feb', '05 Feb', '10 Feb', '15 Feb', '20 Feb', 'Today'],
                datasets: [{
                    label: 'Revenue Flow',
                    data: [1200, 2500, 1800, 3200, 2800, 4100],
                    borderColor: '#6366f1',
                    borderWidth: 4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
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
                        ticks: { callback: value => '$' + value }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
