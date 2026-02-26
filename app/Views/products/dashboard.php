<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<style>
    .stat-card {
        border-radius: 24px;
        padding: 24px 28px;
        position: relative;
        overflow: hidden;
        border: none;
        transition: all 0.35s;
        min-height: 130px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px -12px rgba(0,0,0,0.18); }

    .card-purple  { background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%); color: white; }
    .card-cyan    { background: linear-gradient(135deg, #0ea5e9 0%, #38bdf8 100%); color: white; }
    .card-emerald { background: linear-gradient(135deg, #059669 0%, #34d399 100%); color: white; }
    .card-rose    { background: linear-gradient(135deg, #e11d48 0%, #fb7185 100%); color: white; }
    .card-amber   { background: linear-gradient(135deg, #d97706 0%, #fbbf24 100%); color: white; }
    .card-slate   { background: linear-gradient(135deg, #334155 0%, #64748b 100%); color: white; }

    .icon-bg {
        position: absolute;
        right: -10px;
        bottom: -10px;
        font-size: 5rem;
        opacity: 0.12;
        transform: rotate(-15deg);
    }

    .stat-value { font-size: 1.9rem; font-weight: 800; letter-spacing: -1px; line-height: 1; }
    .stat-label { font-size: 0.82rem; font-weight: 600; opacity: 0.8; text-transform: uppercase; letter-spacing: 0.5px; }
    .action-badge {
        background: rgba(255, 255, 255, 0.2);
        padding: 3px 10px;
        border-radius: 30px;
        font-size: 0.68rem;
        font-weight: 600;
        backdrop-filter: blur(5px);
        display: inline-block;
        margin-bottom: 8px;
    }
</style>

<!-- Row 1: 4 Cards -->
<div class="row g-4 mb-4 animate-wow">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-purple shadow-lg">
            <i class="fas fa-pills icon-bg"></i>
            <span class="action-badge">Inventory</span>
            <div>
                <div class="stat-value"><?= number_format($total_items_in_stock) ?></div>
                <div class="stat-label">Units Available</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-slate shadow-lg">
            <i class="fas fa-money-bill-transfer icon-bg"></i>
            <span class="action-badge">Invested</span>
            <div>
                <div class="stat-value" style="font-size: 1.4rem;">Rs. <?= number_format($total_investment, 0) ?></div>
                <div class="stat-label">Total Investment</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-emerald shadow-lg">
            <i class="fas fa-warehouse icon-bg"></i>
            <span class="action-badge">On Shelf</span>
            <div>
                <div class="stat-value" style="font-size: 1.4rem;">Rs. <?= number_format($total_stock_value, 0) ?></div>
                <div class="stat-label">Stock Value (Current)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-rose shadow-lg">
            <i class="fas fa-exclamation-triangle icon-bg"></i>
            <span class="action-badge">Shortfall</span>
            <div>
                <div class="stat-value"><?= count($low_stock) + count($expiring_soon) ?></div>
                <div class="stat-label">Active Alerts</div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Today's detailed financial performance -->
<div class="row g-4 mb-4 animate-wow" style="animation-delay: 0.1s;">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-cyan shadow-lg">
            <i class="fas fa-cash-register icon-bg"></i>
            <span class="action-badge">Revenue</span>
            <div>
                <div class="stat-value" style="font-size: 1.6rem;">Rs. <?= number_format($today_revenue, 2) ?></div>
                <div class="stat-label">Total Cash Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-emerald shadow-lg">
            <i class="fas fa-chart-line icon-bg"></i>
            <span class="action-badge">Gross Profit</span>
            <div>
                <div class="stat-value" style="font-size: 1.6rem;">Rs. <?= number_format($today_profit, 2) ?></div>
                <div class="stat-label">Profit Today (Before Exp)</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-rose shadow-lg">
            <i class="fas fa-wallet icon-bg"></i>
            <span class="action-badge">Expenses</span>
            <div>
                <div class="stat-value" style="font-size: 1.6rem;">Rs. <?= number_format($today_expenses, 2) ?></div>
                <div class="stat-label">Operational Costs Today</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card card-purple shadow-lg" style="background: linear-gradient(135deg, #8b5cf6, #d946ef);">
            <i class="fas fa-sack-dollar icon-bg"></i>
            <span class="action-badge">Net Profit</span>
            <div>
                <div class="stat-value" style="font-size: 1.6rem;">Rs. <?= number_format($today_net_profit, 2) ?></div>
                <div class="stat-label">Actual Gain Today</div>
            </div>
        </div>
    </div>
</div>

<!-- Lifetime Performance Section -->
<div class="row g-4 mb-4 animate-wow" style="animation-delay: 0.15s;">
    <div class="col-12">
        <div class="premium-list p-4 bg-light border-0 shadow-sm" style="border-radius: 20px;">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h6 class="text-muted small fw-bold text-uppercase mb-1">Lifetime Business Health</h6>
                    <p class="small m-0">Overall statistics since project start.</p>
                </div>
                <div class="col-md-3 text-center border-start">
                    <div class="text-muted small">Total Sales Volume</div>
                    <div class="fw-800 text-dark fs-5">Rs. <?= number_format($lifetime_sales, 2) ?></div>
                </div>
                <div class="col-md-3 text-center border-start">
                    <div class="text-muted small">Total Historical Investment</div>
                    <div class="fw-800 text-dark fs-5">Rs. <?= number_format($total_investment, 2) ?></div>
                </div>
                <div class="col-md-3 text-center border-start">
                    <div class="text-muted small">Cumulative Net Profit</div>
                    <div class="fw-800 text-success fs-5">Rs. <?= number_format($lifetime_net_profit, 2) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Chart + Alerts -->
<div class="row g-4 animate-wow" style="animation-delay: 0.2s;">
    <div class="col-lg-8">
        <div class="premium-list" style="height: 380px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h5 class="fw-800 mb-0 m-0">Revenue — Last 7 Days</h5>
                    <p class="text-muted small m-0">Daily sales revenue overview</p>
                </div>
                <a href="<?= base_url('sales/report') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="fas fa-chart-bar me-1"></i> Full Report
                </a>
            </div>
            <div style="height: 270px; width: 100%;">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="premium-list bg-dark text-white shadow-none" style="background: #0f172a !important; height: 380px;">
            <h5 class="fw-700 mb-3">Quick Actions</h5>

            <div class="d-grid gap-2 mb-4">
                <a href="<?= base_url('sales') ?>" class="btn btn-vibrant py-2 small">
                    <i class="fas fa-bolt me-1 text-warning"></i> Make a Sale
                </a>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?= base_url('purchases/select_vendor') ?>" class="btn btn-outline-light w-100 py-2 border-0 rounded-4 small" style="background: rgba(255,255,255,0.07);">
                             Add Stock
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= base_url('expenses') ?>" class="btn btn-outline-light w-100 py-2 border-0 rounded-4 small" style="background: rgba(255,255,255,0.07);">
                             Log Expense
                        </a>
                    </div>
                </div>
                <a href="<?= base_url('purchases/dues') ?>" class="btn btn-outline-light py-2 border-0 rounded-4 small" style="background: rgba(255,255,255,0.07);">
                    <i class="fas fa-hand-holding-dollar me-1"></i> View Vendor Dues
                </a>
            </div>

            <div style="overflow-y: auto; max-height: 200px; padding-right: 5px;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <p class="text-muted small text-uppercase fw-800 m-0">Alerts</p>
                    <a href="<?= base_url('products/shortage') ?>" class="text-warning small text-decoration-none fw-bold" style="font-size: 0.65rem;">View All</a>
                </div>
                <div class="list-group list-group-flush">
                    <?php if(empty($expiring_soon) && empty($low_stock)): ?>
                        <div class="text-muted small p-2">
                            <i class="fas fa-check-circle text-success me-1"></i> No active alerts. All good!
                        </div>
                    <?php endif; ?>

                    <?php foreach($expiring_soon as $exp): ?>
                        <div class="bg-transparent border-0 d-flex gap-3 mb-3 p-0">
                            <div class="bg-warning rounded-3 p-2 text-dark" style="width: 40px; height: 40px; text-align: center; flex-shrink:0;">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-warning">Upcoming Expiration</div>
                                <div class="text-white-50" style="font-size: 0.75rem;"><?= esc($exp['product_name']) ?> (<?= date('d M, Y', strtotime($exp['expiry_date'])) ?>)</div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($low_stock as $ls): ?>
                        <div class="bg-transparent border-0 d-flex gap-3 mb-3 p-0">
                            <div class="bg-danger rounded-3 p-2 text-white" style="width: 40px; height: 40px; text-align: center; flex-shrink:0;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-danger">Low Stock</div>
                                <div class="text-white-50" style="font-size: 0.75rem;"><?= esc($ls['product_name']) ?>: <?= $ls['current_qty'] ?> units left</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('mainChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= $chart_labels ?>,
                datasets: [{
                    label: 'Revenue',
                    data: <?= $chart_values ?>,
                    borderColor: '#6366f1',
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 3,
                    pointRadius: 5,
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
                        grid: { color: '#f1f5f9' },
                        border: { display: false },
                        ticks: { callback: value => 'Rs. ' + value }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
