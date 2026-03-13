<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$statusColors = [
    'ordered'      => 'warning',
    'received'     => 'info',
    'partial_paid' => 'primary',
    'paid'         => 'success',
];
$statusLabels = [
    'ordered'      => 'Ordered',
    'received'     => 'Received',
    'partial_paid' => 'Partial Paid',
    'paid'         => 'Paid',
];

$totalSpent  = $global_stats['total_purchased'] ?? 0;
$totalOrders = count($purchases);
$paidCount   = count(array_filter($purchases, fn($p) => $p['status'] === 'paid'));
$pendingAmt  = $global_stats['outstanding'] ?? 0;
?>

<!-- ======================== STAT CARDS ======================== -->
<div class="row g-4 mb-4 animate-wow">
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm">
            <div class="text-muted extra-small fw-bold text-uppercase">Total Spent</div>
            <h2 class="fw-900 m-0 mt-1 text-dark">Rs. <?= number_format($totalSpent, 2) ?></h2>
            <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-primary" style="width: 100%"></div>
            </div>
            <div class="small text-muted mt-2">All-time purchase value</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 text-white border-0 shadow-sm" style="background: linear-gradient(135deg, #059669, #10b981);">
            <div class="opacity-75 extra-small fw-bold text-uppercase">Total Orders</div>
            <h2 class="fw-900 m-0 mt-1"><?= $totalOrders ?></h2>
            <div class="progress mt-3 bg-white bg-opacity-25" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-white" style="width: 100%"></div>
            </div>
            <div class="small opacity-75 mt-2">All purchase orders</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm">
            <div class="text-muted extra-small fw-bold text-uppercase">Paid Orders</div>
            <h2 class="fw-900 m-0 mt-1 text-success"><?= $paidCount ?></h2>
            <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-success" style="width: <?= $totalOrders > 0 ? ($paidCount / $totalOrders) * 100 : 0 ?>%"></div>
            </div>
            <div class="small text-muted mt-2">Fully settled orders</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm">
            <div class="text-muted extra-small fw-bold text-uppercase">Outstanding Amount</div>
            <h2 class="fw-900 m-0 mt-1 text-danger">Rs. <?= number_format($pendingAmt, 2) ?></h2>
            <div class="progress mt-3" style="height: 6px; border-radius: 10px;">
                <div class="progress-bar bg-danger" style="width: <?= $totalSpent > 0 ? ($pendingAmt / $totalSpent) * 100 : 0 ?>%"></div>
            </div>
            <div class="small text-muted mt-2">Unpaid / partial amounts</div>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success rounded-4 fw-bold mb-4"><i class="fas fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?></div>
<?php endif; ?>

<!-- ======================== PURCHASE TABLE ======================== -->
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white" style="overflow: visible;">
            <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h5 class="m-0 fw-900 text-dark">Purchase Orders</h5>
                    <p class="text-muted small m-0 mt-1">All supplier purchase records.</p>
                </div>
                <div class="d-flex gap-3 align-items-center flex-wrap">
                    <form action="<?= base_url('purchases') ?>" method="GET" class="d-flex gap-2 flex-wrap">
                        <input type="text" name="search" value="<?= esc($search_query ?? '') ?>" class="form-control form-control-sm border-0 bg-white shadow-sm rounded-pill px-4" placeholder="Search Order ID / Note..." style="min-width: 180px;">
                        
                        <select name="vendor_id" class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-4" onchange="this.form.submit()" style="min-width: 180px;">
                            <option value="">All Vendors...</option>
                            <?php foreach($vendors as $v): ?>
                                <option value="<?= $v['id'] ?>" <?= (($selected_vendor ?? '') == $v['id']) ? 'selected' : '' ?>><?= esc($v['name']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select name="status" class="form-select form-select-sm border-0 bg-white shadow-sm rounded-pill px-4" onchange="this.form.submit()" style="min-width: 150px;">
                            <option value="">All Status...</option>
                            <?php foreach($statusLabels as $val => $txt): ?>
                                <option value="<?= $val ?>" <?= (($selected_status ?? '') == $val) ? 'selected' : '' ?>><?= $txt ?></option>
                            <?php endforeach; ?>
                        </select>

                        <?php if(($selected_vendor ?? '') || ($selected_status ?? '') || ($search_query ?? '')): ?>
                            <a href="<?= base_url('purchases') ?>" class="btn btn-sm btn-light rounded-circle shadow-sm"><i class="fas fa-times"></i></a>
                        <?php endif; ?>
                    </form>

                    <a href="<?= base_url('purchases/export?vendor_id='.($selected_vendor ?? '').'&status='.($selected_status ?? '')) ?>" class="btn btn-sm btn-white rounded-pill px-4 fw-bold shadow-sm">
                        <i class="fas fa-file-csv text-success me-2"></i> Export
                    </a>

                    <a href="<?= base_url('purchases/select_vendor') ?>" class="btn btn-dark rounded-pill px-4 py-2 fw-bold shadow-sm">
                        <i class="fas fa-plus me-2"></i> New Purchase
                    </a>
                </div>
            </div>

            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted extra-small text-uppercase fw-900">
                            <th class="border-0 px-5 py-4">Purchase</th>
                            <th class="border-0 py-4">Vendor</th>
                            <th class="border-0 py-4 text-center">Status</th>
                            <th class="border-0 py-4">Date</th>
                            <th class="border-0 py-4">Note</th>
                            <th class="border-0 py-4 text-end">Total</th>
                            <th class="border-0 py-4 text-end px-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($purchases)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted h5">No purchase orders found.</td></tr>
                        <?php else: ?>
                        <?php foreach ($purchases as $p): ?>
                        <?php
                            $st    = $p['status'] ?? 'ordered';
                            $color = $statusColors[$st] ?? 'secondary';
                            $label = $statusLabels[$st] ?? ucfirst($st);
                        ?>
                        <tr style="position: relative;">
                            <td class="px-5 py-3">
                                <div class="fw-900 text-dark">#<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                <div class="extra-small text-muted"><?= $p['created_at'] ? date('d M, Y', strtotime($p['created_at'])) : '—' ?></div>
                            </td>
                            <td class="py-3">
                                <?php if ($p['vendor_name']): ?>
                                <a href="<?= base_url('purchases/vendor/' . $p['vendor_id']) ?>" class="fw-bold text-dark text-decoration-none hover-primary">
                                    <i class="fas fa-building-circle-check me-1 text-primary"></i> <?= esc($p['vendor_name']) ?>
                                </a>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 text-center">
                                <div class="dropdown">
                                    <span class="badge bg-<?= $color ?> px-3 py-2 rounded-pill cursor-pointer" data-bs-toggle="dropdown" style="cursor: pointer;">
                                        <?= $label ?> <i class="fas fa-caret-down ms-1 opacity-50"></i>
                                    </span>
                                    <ul class="dropdown-menu border-0 shadow-lg rounded-4 p-2">
                                        <?php foreach($statusLabels as $val => $txt): ?>
                                            <li><a class="dropdown-item rounded-3 small fw-bold" href="<?= base_url("purchases/status/{$p['id']}/{$val}") ?>"><?= $txt ?></a></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </td>
                            <td class="py-3 text-muted fw-bold small"><?= date('d M Y', strtotime($p['date'])) ?></td>
                            <td class="py-3 text-muted small"><?= esc($p['note'] ?: '—') ?></td>
                            <td class="py-3 text-end fw-900 text-dark">Rs. <?= number_format($p['total_amount'], 2) ?></td>
                            <td class="py-3 text-end px-5">
                                <div class="dropdown">
                                    <button class="btn btn-light rounded-circle hover-lift" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2">
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2" href="<?= base_url('purchases/view/' . $p['id']) ?>">
                                                <i class="fas fa-eye me-2 text-info"></i> View Items
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2" href="<?= base_url('purchases/invoice/' . $p['id']) ?>" target="_blank">
                                                <i class="fas fa-print me-2 text-primary"></i> Print Invoice
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 text-danger"
                                               href="<?= base_url('purchases/delete/' . $p['id']) ?>"
                                               onclick="return confirm('Delete this entire purchase and all its items?')">
                                                <i class="fas fa-trash-can me-2"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
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
