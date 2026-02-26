<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    /* Premium Intelligence Hub Styles */
    .intel-hub {
        background: #f1f5f9;
        margin: -30px;
        padding: 40px;
        min-height: 100vh;
    }

    .header-panel-dark {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 45px;
        padding: 60px;
        color: white;
        margin-bottom: 40px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.05);
        position: relative;
        overflow: hidden;
    }

    .header-panel-dark::after {
        content: '';
        position: absolute;
        top: -100px;
        right: -100px;
        width: 300px; height: 300px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .period-pill {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 12px 28px;
        border-radius: 100px;
        color: rgba(255,255,255,0.7);
        font-weight: 700;
        cursor: pointer;
        transition: all 0.35s;
        font-size: 0.9rem;
    }
    .period-pill.active {
        background: #3b82f6;
        color: white;
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.35);
        border-color: #3b82f6;
    }

    .audit-bar {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 30px;
        padding: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 40px;
        backdrop-filter: blur(10px);
    }

    .audit-item { text-align: center; flex: 1; border-right: 1px solid rgba(255,255,255,0.1); }
    .audit-item:last-child { border-right: none; }
    .audit-val { font-size: 1.8rem; font-weight: 900; line-height: 1.1; margin-top: 5px; }
    .audit-lbl { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: rgba(255,255,255,0.4); letter-spacing: 1px; }

    .kpi-card-premium {
        background: white;
        border-radius: 40px;
        padding: 40px;
        height: 100%;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        position: relative;
    }
    .kpi-card-premium:hover { transform: translateY(-10px); box-shadow: 0 30px 60px rgba(0,0,0,0.08); }

    .kpi-icon-v2 {
        width: 65px; height: 65px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        margin-bottom: 30px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.04);
    }

    .card-revenue { color: #3b82f6; background: rgba(59, 130, 246, 0.08); }
    .card-stock { color: #f59e0b; background: rgba(245, 158, 11, 0.08); }
    .card-expense { color: #ef4444; background: rgba(239, 68, 68, 0.08); }
    .card-profit { color: #10b981; background: rgba(16, 185, 129, 0.08); }

    .kpi-amount { font-size: 2.2rem; font-weight: 900; letter-spacing: -1.5px; color: #0f172a; margin-top: 8px; }
    .kpi-desc { font-size: 0.82rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
    .kpi-extra { font-size: 0.85rem; font-weight: 700; margin-top: 20px; color: #64748b; }

    .table-container {
        background: white;
        border-radius: 40px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
    }

    .product-row {
        padding: 15px 0;
        border-bottom: 1px dashed #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .product-row:last-child { border-bottom: none; }
</style>

<div class="intel-hub">
    <!-- MASTER LOG PANEL -->
    <div class="header-panel-dark animate-wow">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <span class="badge bg-primary px-3 py-2 rounded-pill fw-900 extra-small tracking-widest mb-3">FINANCIAL AUDIT HUB</span>
                <h1 class="fw-900 m-0 display-4">Business Command Center</h1>
                <p class="text-white-50 mt-2 fs-5">Track every rupee moving through Galaxy Pharmacy in real-time.</p>
                <div class="d-flex gap-2 mt-4">
                    <button class="period-pill active" onclick="switchPeriod('today')">Today</button>
                    <button class="period-pill" onclick="switchPeriod('week')">Weekly</button>
                    <button class="period-pill" onclick="switchPeriod('month')">Monthly</button>
                    <button class="period-pill" onclick="switchPeriod('year')">Yearly Audit</button>
                </div>
            </div>
            <div class="col-lg-5 text-lg-end">
                <div class="d-inline-flex flex-column gap-1 text-start p-4 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10">
                    <div class="small fw-900 text-white-50">LIFETIME CASH VOLUME</div>
                    <div class="h2 fw-900 text-success m-0">Rs. <?= number_format($lifetime['total_sales'], 0) ?></div>
                    <div class="text-white-50 small fw-bold">Verified Transaction History</div>
                </div>
            </div>
        </div>

        <!-- Master Stock Flow Reconciliation -->
        <div class="audit-bar mt-5">
            <div class="audit-item">
                <div class="audit-lbl">Opening Stock <i class="fas fa-box-open ms-1 opacity-50"></i></div>
                <div class="audit-val"><?= number_format($audit['opening']) ?></div>
            </div>
            <div class="audit-item text-primary">
                <div class="audit-lbl text-primary">Inbound (Bought) <i class="fas fa-arrow-down ms-1 opacity-50"></i></div>
                <div class="audit-val">+ <?= number_format($audit['in']) ?></div>
            </div>
            <div class="audit-item text-warning">
                <div class="audit-lbl text-warning">Outbound (Sold) <i class="fas fa-arrow-up ms-1 opacity-50"></i></div>
                <div class="audit-val">- <?= number_format($audit['out']) ?></div>
            </div>
            <div class="audit-item">
                <div class="audit-lbl">Current Shelf Stock <i class="fas fa-warehouse ms-1 opacity-50"></i></div>
                <div class="audit-val"><?= number_format($audit['closing']) ?></div>
            </div>
        </div>
    </div>

    <!-- MAIN KPI ANALYTICS -->
    <?php foreach($stats as $period => $data): ?>
    <div class="period-section animate-up" id="sec_<?= $period ?>" style="<?= $period == 'today' ? '' : 'display:none;' ?>">
        <div class="row g-4">
            <!-- Revenue -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-premium">
                    <div class="kpi-icon-v2 card-revenue"><i class="fas fa-money-bill-trend-up"></i></div>
                    <div class="kpi-desc">Total Sales Revenue</div>
                    <div class="kpi-amount text-primary">Rs. <?= number_format($data['revenue'], 0) ?></div>
                    <div class="kpi-extra"><i class="fas fa-check-double text-success me-1"></i> <?= $data['tx_count'] ?> Deliveries Verified</div>
                </div>
            </div>
            <!-- Stock Buy -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-premium">
                    <div class="kpi-icon-v2 card-stock"><i class="fas fa-truck-ramp-box"></i></div>
                    <div class="kpi-desc">Stock Inbound Cost</div>
                    <div class="kpi-amount" style="color:#d97706;">Rs. <?= number_format($data['purchases'], 0) ?></div>
                    <div class="kpi-extra text-muted">Capital Invested in Inventory</div>
                </div>
            </div>
            <!-- Expenses -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-premium">
                    <div class="kpi-icon-v2 card-expense"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="kpi-desc">Operating Bills</div>
                    <div class="kpi-amount text-danger">Rs. <?= number_format($data['expenses'], 0) ?></div>
                    <div class="kpi-extra text-muted">Rent, Salaries & Utility Bills</div>
                </div>
            </div>
            <!-- NET PROFIT -->
            <div class="col-xl-3 col-md-6">
                <div class="kpi-card-premium" style="background: #10b981; color: white;">
                    <div class="kpi-icon-v2 bg-white text-success"><i class="fas fa-sack-dollar"></i></div>
                    <div class="kpi-desc text-white opacity-75">Clean Net Gain</div>
                    <div class="kpi-amount text-white">Rs. <?= number_format($data['net_profit'], 0) ?></div>
                    <div class="mt-4 pt-3 border-top border-white border-opacity-10">
                        <div class="fw-900 fs-5"><?= $data['revenue'] > 0 ? number_format(($data['net_profit'] / $data['revenue']) * 100, 1) : 0 ?>% Margin</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4 row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-5 border shadow-sm d-flex align-items-center gap-4">
                    <div class="h3 fw-900 m-0 text-dark">Rs. <?= number_format($data['avg_order'], 2) ?></div>
                    <div class="small fw-900 text-muted uppercase tracking-widest">Average Transaction Value</div>
                </div>
            </div>
            <div class="col-md-4">
               <div class="p-4 bg-white rounded-5 border shadow-sm d-flex align-items-center gap-4">
                    <div class="h3 fw-900 m-0 text-dark"><?= number_format($data['tx_count']) ?></div>
                    <div class="small fw-900 text-muted uppercase tracking-widest">Sales Order Volume</div>
                </div>
            </div>
            <div class="col-md-4">
               <div class="p-4 bg-white rounded-5 border shadow-sm d-flex align-items-center gap-4 text-success">
                    <div class="h3 fw-900 m-0 text-success">Rs. <?= number_format($data['gross_profit'], 0) ?></div>
                    <div class="small fw-900 text-success opacity-75 uppercase tracking-widest">Gross Profit (Stock Gain)</div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <!-- DEEP REVEAL ANALYTICS -->
    <div class="row g-4 mt-5">
        <div class="col-xl-8">
            <div class="table-container shadow-2xl">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div>
                        <h4 class="fw-900 m-0">Performance Momentum</h4>
                        <p class="text-muted small m-0 mt-1">Comparing monthly Revenue against Clean Profits.</p>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="small fw-900 d-flex align-items-center gap-2"><span class="rounded-circle" style="width:10px;height:10px;background:#3b82f6;"></span> Revenue</div>
                        <div class="small fw-900 d-flex align-items-center gap-2"><span class="rounded-circle" style="width:10px;height:10px;background:#10b981;"></span> Profit</div>
                    </div>
                </div>
                <div style="height: 400px;">
                    <canvas id="deepChart"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4">
            <div class="table-container h-100">
                <h5 class="fw-900 m-0 mb-4"><i class="fas fa-crown text-warning me-2"></i> High Performance SKUs</h5>
                <div class="mt-4">
                    <?php foreach($top_selling_products as $index => $p): ?>
                        <div class="product-row">
                            <div class="d-flex align-items-center gap-3">
                                <div class="fw-900 text-muted small" style="width: 25px;">0<?= $index+1 ?></div>
                                <div>
                                    <div class="fw-900 text-dark"><?= esc($p['name']) ?></div>
                                    <div class="text-muted extra-small fw-bold"><?= number_format($p['units']) ?> UNITS MOVED</div>
                                </div>
                            </div>
                            <div class="text-end fw-900 text-primary">
                                Rs. <?= number_format($p['revenue']/1000, 1) ?>K
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-5 pt-3 border-top">
                     <h5 class="fw-900 m-0 mb-4 text-dark"><i class="fas fa-database text-info me-2"></i> Inventory Status Hub</h5>
                     <div class="p-4 bg-light rounded-4 border-0">
                         <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                             <div class="small fw-800 text-muted">Total Stock Value:</div>
                             <div class="fw-900 text-dark">Rs. <?= number_format($lifetime['current_stock_valuation'], 0) ?></div>
                         </div>
                         <div class="d-flex justify-content-between mb-0">
                             <div class="small fw-800 text-muted">Warehouse Net Worth:</div>
                             <div class="fw-900 text-success">Rs. <?= number_format($lifetime['current_stock_valuation'] * 1.25, 0) ?> (Est.)</div>
                         </div>
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function switchPeriod(p) {
        document.querySelectorAll('.period-section').forEach(el => el.style.display = 'none');
        document.getElementById('sec_'+p).style.display = 'block';
        
        document.querySelectorAll('.period-pill').forEach(el => el.classList.remove('active'));
        event.target.classList.add('active');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('deepChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.15)');
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months_trend['labels']) ?>,
                datasets: [
                    {
                        label: 'Gross Revenue',
                        data: <?= json_encode($months_trend['revenue']) ?>,
                        borderColor: '#3b82f6',
                        borderWidth: 4,
                        tension: 0.45,
                        fill: false,
                        pointRadius: 5
                    },
                    {
                        label: 'Net Profits',
                        data: <?= json_encode($months_trend['profit']) ?>,
                        borderColor: '#10b981',
                        backgroundColor: gradient,
                        borderWidth: 5,
                        tension: 0.45,
                        fill: true,
                        pointRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10b981',
                        pointBorderWidth: 3
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
                        ticks: { callback: v => 'Rs. ' + (v/1000).toFixed(0) + 'K' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>
