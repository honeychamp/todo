<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.75);
        --glass-border: rgba(255, 255, 255, 0.3);
        --slate-900: #0f172a;
        --indigo-600: #4f46e5;
        --emerald-500: #10b981;
        --soft-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.01);
    }

    .dashboard-viewport {
        margin: -30px;
        padding: 40px;
        background: #f1f5f9;
        min-height: 100vh;
        font-family: 'Outfit', sans-serif;
    }

    /* Command Center Hero */
    .hero-center {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 32px;
        padding: 45px;
        color: #fff;
        margin-bottom: 35px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.3);
    }

    .hero-center::before {
        content: '';
        position: absolute;
        top: -100px; right: -100px;
        width: 350px; height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 75%);
        border-radius: 50%;
    }

    .hero-badge {
        background: rgba(14, 165, 233, 0.1);
        border: 1px solid rgba(14, 165, 233, 0.2);
        color: #38bdf8;
        padding: 8px 18px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        margin-bottom: 20px;
        display: inline-block;
    }

    .audit-strip {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 24px;
        padding: 25px;
        margin-top: 35px;
        display: flex;
        justify-content: space-around;
        align-items: center;
        backdrop-filter: blur(10px);
    }

    .audit-node { text-align: center; flex: 1; border-right: 1px solid rgba(255, 255, 255, 0.08); }
    .audit-node:last-child { border-right: none; }
    .audit-label { font-size: 0.7rem; text-transform: uppercase; color: #94a3b8; font-weight: 800; letter-spacing: 1px; margin-bottom: 8px; }
    .audit-value { font-size: 1.7rem; font-weight: 900; line-height: 1; color: #fff; }

    /* Glass KPI Cards */
    .glass-kpi {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 24px;
        padding: 30px;
        height: 100%;
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), box-shadow 0.4s;
        box-shadow: var(--soft-shadow);
        position: relative;
        overflow: hidden;
    }

    .glass-kpi:hover {
        transform: translateY(-8px);
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.1);
    }

    .kpi-icon {
        width: 58px; height: 58px; border-radius: 18px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem; margin-bottom: 25px;
    }

    .ki-blue { background: #eff6ff; color: #3b82f6; }
    .ki-emerald { background: #f0fdf4; color: #10b981; }
    .ki-amber { background: #fffbeb; color: #f59e0b; }
    .ki-rose { background: #fff1f2; color: #f43f5e; }

    .kpi-title { font-size: 0.82rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-amount { font-size: 2rem; font-weight: 900; color: #0f172a; margin-top: 5px; }

    /* Period Pill Menu */
    .period-menu { display: flex; gap: 12px; margin-bottom: 25px; }
    .period-btn {
        background: #fff;
        border: 1px solid #e2e8f0;
        padding: 10px 24px;
        border-radius: 100px;
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.3s;
    }

    .period-btn.active {
        background: #0f172a;
        color: #fff;
        border-color: #0f172a;
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.2);
    }

    /* Tables & Lists */
    .modern-panel {
        background: #fff;
        border-radius: 28px;
        padding: 35px;
        border: 1px solid #f1f5f9;
        box-shadow: var(--soft-shadow);
    }

    .table-luxury thead th {
        background: #f8fafc;
        border: none;
        padding: 18px 20px;
        font-size: 0.72rem;
        font-weight: 800;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .table-luxury tbody td {
        padding: 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #475569;
    }

    .luxury-avatar {
        width: 42px; height: 42px; border-radius: 12px;
        background: #f1f5f9; display: flex; align-items: center; justify-content: center;
        font-weight: 800; color: #64748b;
    }

    /* Chart Overrides */
    .chart-box { height: 350px; width: 100%; position: relative; }

    /* Animations */
    .fade-panel { animation: fadeInUp 0.6s ease-out forwards; }
</style>

<div class="dashboard-viewport">
    
    <!-- COMMAND CENTER HERO -->
    <div class="hero-center animate-wow">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-badge">PHARMACEUTICAL COMMAND CENTER</div>
                <h1 class="display-5 fw-900 mb-2">Pharmacy Dashboard</h1>
                <p class="opacity-50 fw-500">Real-time business audit — <?= date('l, d F Y') ?></p>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="text-start p-3 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-4">
                            <div class="small fw-800 text-white-50 text-uppercase" style="letter-spacing:1px; font-size: 0.65rem;">Global Investment</div>
                            <div class="h4 fw-900 text-white mb-0">Rs. <?= number_format($global_investment, 0) ?></div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-start p-3 bg-white bg-opacity-5 border border-white border-opacity-10 rounded-4">
                            <div class="small fw-800 text-white-50 text-uppercase" style="letter-spacing:1px; font-size: 0.65rem;">Global Profit</div>
                            <div class="h4 fw-900 text-success mb-0">Rs. <?= number_format($lifetime_net_profit, 0) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LIVE AUDIT STRIP -->
        <div class="audit-strip">
            <div class="audit-node">
                <div class="audit-label">Shelf Opening</div>
                <div class="audit-value"><?= number_format($audit['opening']) ?></div>
            </div>
            <div class="audit-node">
                <div class="audit-label">Inbound (Stock)</div>
                <div class="audit-value text-info">+ <?= number_format($audit['in']) ?></div>
            </div>
            <div class="audit-node">
                <div class="audit-label">Outbound (Sales)</div>
                <div class="audit-value text-warning">- <?= number_format($audit['out']) ?></div>
            </div>
            <div class="audit-node">
                <div class="audit-label">Current Assets</div>
                <div class="audit-value text-emerald"><?= number_format($audit['closing']) ?></div>
            </div>
        </div>
    </div>

    <!-- ENTITY SUMMARY STRIP -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="modern-panel text-center p-4">
                <div class="p-3 rounded-circle bg-primary bg-opacity-10 text-primary d-inline-block mb-3" style="width:60px;height:60px;"><i class="fas fa-pills fs-3"></i></div>
                <div class="h3 fw-900 mb-0"><?= number_format($total_products) ?></div>
                <div class="small text-muted fw-bold">TOTAL PRODUCTS</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-panel text-center p-4">
                <div class="p-3 rounded-circle bg-info bg-opacity-10 text-info d-inline-block mb-3" style="width:60px;height:60px;"><i class="fas fa-truck-field fs-3"></i></div>
                <div class="h3 fw-900 mb-0"><?= number_format($total_vendors) ?></div>
                <div class="small text-muted fw-bold">ACTIVE VENDORS</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-panel text-center p-4">
                <div class="p-3 rounded-circle bg-warning bg-opacity-10 text-warning d-inline-block mb-3" style="width:60px;height:60px;"><i class="fas fa-layer-group fs-3"></i></div>
                <div class="h3 fw-900 mb-0"><?= number_format($total_categories) ?></div>
                <div class="small text-muted fw-bold">CATEGORIES</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="modern-panel text-center p-4">
                <div class="p-3 rounded-circle bg-success bg-opacity-10 text-success d-inline-block mb-3" style="width:60px;height:60px;"><i class="fas fa-user-doctor fs-3"></i></div>
                <div class="h3 fw-900 mb-0"><?= number_format($total_doctors) ?></div>
                <div class="small text-muted fw-bold">TOTAL DOCTORS</div>
            </div>
        </div>
    </div>

    <!-- KPI PERIOD SECTION -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-900 text-slate-900 m-0">Performance Matrix</h4>
            <div class="period-menu">
                <button class="period-btn active" onclick="togglePeriod('today', this)">Today</button>
                <button class="period-btn" onclick="togglePeriod('week', this)">Weekly</button>
                <button class="period-btn" onclick="togglePeriod('month', this)">Monthly</button>
                <button class="period-btn" onclick="togglePeriod('year', this)">Yearly</button>
            </div>
        </div>

        <?php foreach($stats as $p => $d): ?>
        <div class="period-data animate-up" id="sec_<?= $p ?>" style="<?= $p == 'today' ? '' : 'display:none;' ?>">
            <div class="row g-4">
                <div class="col-xl-3 col-md-6">
                    <div class="glass-kpi">
                        <div class="kpi-icon ki-blue"><i class="fas fa-hand-holding-dollar"></i></div>
                        <div class="kpi-title">Gross Revenue</div>
                        <div class="kpi-amount">Rs. <?= number_format($d['revenue'], 0) ?></div>
                        <div class="mt-3 small fw-bold text-muted"><?= $d['tx_count'] ?> Total Transactions</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="glass-kpi">
                        <div class="kpi-icon ki-amber"><i class="fas fa-box-archive"></i></div>
                        <div class="kpi-title">Procurement</div>
                        <div class="kpi-amount">Rs. <?= number_format($d['purchases'], 0) ?></div>
                        <div class="mt-3 small fw-bold text-muted">Stock Refill Cost</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="glass-kpi">
                        <div class="kpi-icon ki-rose"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div class="kpi-title">Direct Expense</div>
                        <div class="kpi-amount">Rs. <?= number_format($d['expenses'], 0) ?></div>
                        <div class="mt-3 small fw-bold text-muted">Utility & Operations</div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="glass-kpi" style="background: var(--slate-900); color: #fff;">
                        <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.15); color: #10b981;"><i class="fas fa-sack-dollar"></i></div>
                        <div class="kpi-title text-white-50">Net Earnings</div>
                        <div class="kpi-amount text-white text-emerald">Rs. <?= number_format($d['net_profit'], 0) ?></div>
                        <div class="mt-3 small fw-bold text-success"><?= $d['revenue'] > 0 ? round(($d['net_profit'] / $d['revenue']) * 100, 1) : 0 ?>% Profit Margin</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- MID SECTION: CHARTS & NETWORK -->
    <div class="row g-4 mb-5">
        <div class="col-xl-8">
            <div class="modern-panel h-100">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h4 class="fw-900 m-0">Commercial Momentum</h4>
                        <p class="small text-muted m-0">Monthly Revenue vs Net Earnings</p>
                    </div>
                    <div class="d-flex gap-4">
                        <span class="small fw-bold d-flex align-items-center gap-2"><i class="fas fa-circle text-primary" style="font-size:8px;"></i> Revenue</span>
                        <span class="small fw-bold d-flex align-items-center gap-2"><i class="fas fa-circle text-success" style="font-size:8px;"></i> Profit</span>
                    </div>
                </div>
                <div class="chart-box">
                    <canvas id="luxuryTrendChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <!-- Doctor Network -->
            <div class="modern-panel mb-4" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); color: #fff; border: none;">
                <h6 class="fw-800 text-uppercase mb-4" style="color: rgba(255,255,255,0.6); letter-spacing: 1px; font-size: 0.75rem;">Medical Network</h6>
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <div class="h2 fw-900 mb-0"><?= number_format($total_doctors) ?></div>
                        <div class="small opacity-75 fw-bold">Active Doctors</div>
                    </div>
                    <div class="p-3 rounded-4 bg-white bg-opacity-10 fs-3"><i class="fas fa-user-doctor"></i></div>
                </div>
                <hr style="border-top: 1px solid rgba(255,255,255,0.15);">
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="small fw-bold opacity-75">Receivables Log</div>
                    <div class="h5 fw-900 m-0">Rs. <?= number_format($total_doctor_receivables, 0) ?></div>
                </div>
            </div>
            <!-- Stock Alerts -->
            <div class="modern-panel" style="background: #0f172a; color: #fff; border: none;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-800 text-uppercase m-0" style="color: rgba(255,255,255,0.6); letter-spacing: 1px; font-size: 0.75rem;">Inventory Guard</h6>
                    <span class="badge bg-danger rounded-pill"><?= count($low_stock) + count($expiring_soon) ?></span>
                </div>
                <div class="alert-list">
                    <?php if(empty($low_stock) && empty($expiring_soon)): ?>
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                            <div class="small opacity-50 fw-bold">All items securely stocked</div>
                        </div>
                    <?php endif; ?>

                    <?php foreach($low_stock as $ls): ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-2 rounded-3 bg-danger bg-opacity-10 text-danger"><i class="fas fa-exclamation-triangle"></i></div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-800 text-truncate"><?= esc($ls['product_name']) ?></div>
                                <div class="text-white-50" style="font-size:0.65rem;"><?= $ls['current_qty'] ?> Units Left</div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php foreach($expiring_soon as $es): ?>
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="p-2 rounded-3 bg-warning bg-opacity-10 text-warning"><i class="fas fa-calendar-xmark"></i></div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-800 text-truncate text-warning"><?= esc($es['product_name']) ?></div>
                                <div class="text-white-50" style="font-size:0.65rem;">Exp: <?= date('d M, Y', strtotime($es['exp_date'])) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= base_url('products/shortage') ?>" class="btn btn-outline-light w-100 mt-3 btn-sm fw-bold border-opacity-10 rounded-3 py-2">AUDIT FULL STOCK</a>
            </div>
        </div>
    </div>

    <!-- LOWER SECTION: SALES & RANKINGS -->
    <div class="row g-4">
        <div class="col-xl-9">
            <div class="modern-panel">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <h4 class="fw-900 m-0">Recent Sales Records</h4>
                    <a href="<?= base_url('sales/history') ?>" class="btn btn-light btn-sm fw-bold px-3 py-2 rounded-3 border">View All <i class="fas fa-arrow-right-long ms-2"></i></a>
                </div>
                <div class="table-responsive">
                    <table class="table table-luxury">
                        <thead>
                            <tr>
                                <th>Invoice ID</th>
                                <th>Physician / Customer</th>
                                <th>Transaction Date</th>
                                <th class="text-end">Billing Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_sales as $rs): ?>
                            <tr>
                                <td class="fw-900 text-dark">#<?= $rs['invoice_no'] ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="luxury-avatar"><?= substr($rs['doctor_name'] ?? 'W', 0, 1) ?></div>
                                        <div>
                                            <div class="fw-900 text-dark"><?= esc($rs['doctor_name'] ?? 'Walk-in Customer') ?></div>
                                            <div class="small text-muted"><?= $rs['manual_dr_phone'] ?: 'Retail Client' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="small fw-700 text-muted"><?= date('d M, Y @ H:i', strtotime($rs['sale_date'])) ?></td>
                                <td class="text-end fw-900 text-slate-900">Rs. <?= number_format($rs['total_amount'], 0) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-xl-3">
            <div class="modern-panel h-100">
                <h6 class="fw-800 text-uppercase mb-4" style="color: #94a3b8; letter-spacing: 1px; font-size: 0.75rem;">Elite SKU Ranking</h6>
                <div class="ranking-stack">
                    <?php foreach($top_selling_products as $idx => $tp): ?>
                        <div class="d-flex align-items-center gap-3 p-3 rounded-4 bg-light mb-3 transition-all hover-lift">
                            <div class="h4 m-0 fw-900 text-muted px-2"><?= $idx+1 ?></div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-900 text-dark text-truncate small"><?= esc($tp['name']) ?></div>
                                <div class="small text-muted fw-bold" style="font-size:0.65rem;"><?= number_format($tp['units']) ?> UNIT VOLUME</div>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-indigo-600 bg-opacity-10 text-indigo-600 small" style="font-size:0.6rem;">LEADER</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function togglePeriod(p, btn) {
        document.querySelectorAll('.period-data').forEach(row => row.style.display = 'none');
        document.getElementById('sec_' + p).style.display = 'block';
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const trendCtx = document.getElementById('luxuryTrendChart').getContext('2d');
        
        // Gradient Fill
        const gradRev = trendCtx.createLinearGradient(0, 0, 0, 350);
        gradRev.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
        gradRev.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months_trend['labels']) ?>,
                datasets: [
                    {
                        label: 'Total Revenue',
                        data: <?= json_encode($months_trend['revenue']) ?>,
                        borderColor: '#4f46e5',
                        backgroundColor: gradRev,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.45,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Net Earnings',
                        data: <?= json_encode($months_trend['profit']) ?>,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        tension: 0.45,
                        pointRadius: 0,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f1f5f9' },
                        ticks: { 
                            callback: val => 'Rs. ' + (val/1000).toFixed(0) + 'K',
                            font: { weight: '700' }
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { weight: '700' } }
                    }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>

