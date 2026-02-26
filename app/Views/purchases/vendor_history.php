<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .ledger-card {
        border-radius: 35px;
        border: none;
        background: white;
        box-shadow: 0 15px 50px rgba(0,0,0,0.06);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .ledger-head {
        background: #0f172a;
        padding: 50px;
        color: white;
        position: relative;
    }
    .ledger-head::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; left: 0;
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1) 0%, transparent 100%);
        pointer-events: none;
    }
    .summary-box {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        padding: 25px;
        backdrop-filter: blur(10px);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .summary-box:hover {
        background: rgba(255,255,255,0.07);
        transform: translateY(-8px);
        border-color: rgba(255,255,255,0.2);
    }
    .transaction-row {
        transition: all 0.2s;
        border-left: 5px solid transparent;
    }
    .transaction-row:hover {
        background-color: #f1f5f9;
        transform: scale(1.002);
    }
    .type-purchase { border-left-color: #3b82f6; }
    .type-payment { border-left-color: #10b981; }
    
    .badge-purchase { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .badge-payment { background: rgba(16, 185, 129, 0.1); color: #10b981; }

    @media print {
        .sidebar, .navbar, .btn-print, .actions-column, .back-btn { display: none !important; }
        .ledger-card { box-shadow: none; border: 1px solid #ddd; }
        body { background: white !important; }
        .main-content { margin: 0 !important; padding: 20px !important; }
    }
</style>

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="ledger-card shadow-2xl">
            <!-- Header Section -->
            <div class="ledger-head">
                <div class="row align-items-center position-relative" style="z-index: 2;">
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-4">
                            <div class="rounded-5 d-flex align-items-center justify-content-center shadow-lg" style="width: 90px; height: 90px; background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                                <i class="fas fa-building-circle-check fa-2x text-white"></i>
                            </div>
                            <div>
                                <h1 class="fw-900 m-0 fs-1 tracking-tight"><?= esc($vendor['name']) ?></h1>
                                <div class="d-flex gap-3 mt-2">
                                    <span class="text-white-50 small"><i class="fas fa-phone-volume me-2"></i><?= esc($vendor['phone']) ?></span>
                                    <span class="text-white-50 small"><i class="fas fa-envelope me-2"></i><?= esc($vendor['email'] ?: 'No email') ?></span>
                                </div>
                                <p class="text-white-50 m-0 mt-2 small">
                                    <i class="fas fa-location-dot me-2"></i><?= esc($vendor['address'] ?: 'Verified Supplier') ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 text-md-end mt-4 mt-md-0">
                        <div class="d-flex gap-3 justify-content-md-end">
                            <button onclick="window.print()" class="btn btn-white rounded-pill px-4 py-3 fw-900 btn-print shadow-sm text-dark">
                                <i class="fas fa-print me-2 text-primary"></i> STATEMENT
                            </button>
                            <a href="<?= base_url('purchases/add/'.$vendor['id']) ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-900 shadow-lg border-0" style="background: #0ea5e9;">
                                <i class="fas fa-cart-shopping me-2"></i> RE-STOCK
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Stats HUD -->
                <div class="row g-4 mt-5 position-relative" style="z-index: 2;">
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-box">
                            <div class="text-white-50 extra-small fw-bold text-uppercase tracking-wider mb-2">Cumulative Debt</div>
                            <div class="h2 fw-900 m-0">Rs. <?= number_format($summary['total_purchased'], 2) ?></div>
                            <div class="badge bg-primary bg-opacity-20 text-primary mt-2"><?= number_format($summary['items_count']) ?> Items Invoiced</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-box">
                            <div class="text-white-50 extra-small fw-bold text-uppercase tracking-wider mb-2">Total Dispatched</div>
                            <div class="h2 fw-900 m-0 text-success">Rs. <?= number_format($summary['total_paid'], 2) ?></div>
                            <div class="small mt-2 text-success opacity-75 fw-bold">Verified Payments</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-box" style="background: rgba(255,255,255,0.1); border-color: rgba(255,255,255,0.2);">
                            <div class="text-white extra-small fw-bold text-uppercase tracking-wider mb-2">Current Balance</div>
                            <div class="h2 fw-900 m-0 <?= $summary['balance'] > 0 ? 'text-warning' : 'text-white' ?>">
                                Rs. <?= number_format($summary['balance'], 2) ?>
                            </div>
                            <div class="small mt-2 fw-900 text-uppercase" style="font-size: 0.7rem; color: #fbbf24;">
                                <?= $summary['balance'] > 0 ? 'PAYMENT OUTSTANDING' : 'ACCOUNT CLEAR' ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="summary-box">
                            <div class="text-white-50 extra-small fw-bold text-uppercase tracking-wider mb-2">Trust Score</div>
                            <div class="d-flex align-items-center gap-2 mt-1">
                                <?php if($summary['balance'] <= 0): ?>
                                    <div class="h3 fw-900 m-0 text-success">Excellent</div>
                                    <i class="fas fa-shield-check text-success fs-4"></i>
                                <?php else: ?>
                                    <div class="h3 fw-900 m-0 text-warning">Active</div>
                                    <i class="fas fa-circle-check text-warning fs-4"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ledger Table Section -->
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-muted extra-small text-uppercase fw-900">
                                <th class="border-0 px-5 py-4">Financial Event</th>
                                <th class="border-0 py-4">Activity Description</th>
                                <th class="border-0 py-4 text-center">Batch Ref</th>
                                <th class="border-0 py-4 text-end">Debit (-)</th>
                                <th class="border-0 py-4 text-end">Credit (+)</th>
                                <th class="border-0 py-4 text-end px-5">Balance HUD</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($ledger)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="opacity-20 py-5">
                                            <i class="fas fa-receipt fa-4x mb-3"></i>
                                            <p class="h4 fw-900">Stationery Record Empty</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($ledger as $row): ?>
                                    <tr class="transaction-row type-<?= strtolower($row['type']) ?>">
                                        <td class="px-5">
                                            <div class="fw-900 text-dark"><?= date('d M, Y', strtotime($row['date'])) ?></div>
                                            <div class="text-muted extra-small fw-bold"><?= date('h:i A', strtotime($row['date'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold fs-6 text-dark"><?= esc($row['description']) ?></div>
                                            <div class="badge <?= strtolower($row['type']) == 'purchase' ? 'badge-purchase' : 'badge-payment' ?> extra-small px-3 mt-1">
                                                <?= strtoupper($row['type']) ?> RECORD
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <code class="text-primary fw-bold">#<?= esc($row['ref']) ?></code>
                                        </td>
                                        <td class="text-end fw-900 text-dark">
                                            <?= $row['debit'] > 0 ? 'Rs. ' . number_format($row['debit'], 2) : '—' ?>
                                        </td>
                                        <td class="text-end fw-900 text-success">
                                            <?= $row['credit'] > 0 ? 'Rs. ' . number_format($row['credit'], 2) : '—' ?>
                                        </td>
                                        <td class="text-end px-5">
                                            <div class="fw-900 fs-5 <?= $row['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
                                                Rs. <?= number_format($row['balance'], 2) ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Analysis Overlay -->
            <div class="p-5 bg-light border-top">
                <div class="row g-5">
                    <div class="col-lg-4">
                        <div class="p-4 rounded-5 bg-white shadow-sm border h-100">
                            <h6 class="fw-900 mb-4 text-dark text-uppercase small tracking-widest"><i class="fas fa-chart-pie text-primary me-2"></i> Material Insight</h6>
                            <?php if(empty($top_products)): ?>
                                <p class="text-muted small">Insufficient data for analysis.</p>
                            <?php else: ?>
                                <?php foreach($top_products as $index => $tp): ?>
                                    <div class="d-flex align-items-center justify-content-between <?= $index < count($top_products)-1 ? 'mb-4' : '' ?>">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-pill bg-light d-flex align-items-center justify-content-center fw-900 text-primary small" style="width: 32px; height: 32px;">
                                                <?= $index + 1 ?>
                                            </div>
                                            <div>
                                                <div class="fw-900 text-dark fs-6"><?= esc($tp['product_name']) ?></div>
                                                <div class="text-muted extra-small fw-bold"><?= number_format($tp['total_units']) ?> UNITS TOTAL</div>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-900 text-dark">Rs. <?= number_format($tp['total_value'] / 1000, 1) ?>K</div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                         <div class="p-4 rounded-5 bg-white shadow-sm border h-100">
                             <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-900 text-dark text-uppercase small tracking-widest m-0"><i class="fas fa-warehouse text-primary me-2"></i> Current Verified Inventory</h6>
                                <span class="badge bg-primary rounded-pill px-3 py-2 fw-900">Live Status</span>
                             </div>
                             <div class="table-responsive">
                                 <table class="table table-borderless table-sm align-middle mb-0">
                                     <thead>
                                         <tr class="extra-small text-muted text-uppercase fw-900 border-bottom">
                                             <th class="py-2">Batch ID</th>
                                             <th class="py-2">Medicine SKU</th>
                                             <th class="py-2 text-center">Available</th>
                                             <th class="py-2 text-end">Expiry</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         <?php if(empty($active_inventory)): ?>
                                             <tr><td colspan="4" class="text-center py-4 text-muted small">No active stock from this supplier found.</td></tr>
                                         <?php else: ?>
                                             <?php foreach(array_slice($active_inventory, 0, 6) as $ai): ?>
                                                 <tr>
                                                     <td class="py-3"><span class="badge bg-light text-dark border-0 px-3 fs-6">#<?= esc($ai['batch_id']) ?></span></td>
                                                     <td class="py-3 fw-900 text-dark fs-6"><?= esc($ai['product_name']) ?></td>
                                                     <td class="py-3 text-center">
                                                         <div class="fw-900 fs-5 text-primary"><?= number_format($ai['on_shelf']) ?></div>
                                                     </td>
                                                     <td class="py-3 text-end fw-bold <?= (strtotime($ai['expiry_date']) < strtotime('+90 days')) ? 'text-danger' : 'text-muted' ?>">
                                                         <?= date('M, Y', strtotime($ai['expiry_date'])) ?>
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

                <div class="row mt-4">
                    <div class="col-12">
                         <div class="p-4 rounded-5 bg-white shadow-sm border">
                             <div class="d-flex justify-content-between align-items-center mb-4">
                                <h6 class="fw-900 text-dark text-uppercase small tracking-widest m-0"><i class="fas fa-timeline text-primary me-2"></i> Supply Volume Analytics</h6>
                             </div>
                             <canvas id="supplyChart" height="100"></canvas>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Global Footer -->
            <div class="bg-dark p-5 d-flex flex-column flex-md-row justify-content-between align-items-center text-center text-md-start">
                <div class="mb-4 mb-md-0">
                    <h6 class="text-white fw-900 m-0">Confidential Financial Statement</h6>
                    <p class="text-white-50 small m-0 mt-1">Generated by Galaxy Pharmacy Management System Core Engine</p>
                </div>
                <div class="d-flex gap-3">
                   <a href="<?= base_url('purchases/dues') ?>" class="btn btn-outline-light rounded-pill px-5 py-2 fw-900 back-btn">
                       <i class="fas fa-arrow-left-long me-2"></i> RETURN TO BALANCES
                   </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('supplyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($supply_trend, 'month')) ?>,
            datasets: [{
                label: 'Procurement Intensity (Rs.)',
                data: <?= json_encode(array_column($supply_trend, 'total')) ?>,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.08)',
                borderWidth: 4,
                tension: 0.45,
                fill: true,
                pointRadius: 6,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: 'rgba(0,0,0,0.05)', drawBorder: false },
                    ticks: { font: { weight: 'bold' } }
                },
                x: { 
                    grid: { display: false },
                    ticks: { font: { weight: 'bold' } }
                }
            }
        }
    });
});
</script>

<?= $this->endSection() ?>
