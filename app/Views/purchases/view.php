<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<?php
$statusColors = [
    'ordered'      => 'warning',
    'received'     => 'info',
    'partial_paid' => 'primary',
    'paid'         => 'success',
];
$statusIcons = [
    'ordered'      => 'fa-box',
    'received'     => 'fa-check',
    'partial_paid' => 'fa-coins',
    'paid'         => 'fa-circle-check',
];
$statusLabels = [
    'ordered'      => 'Ordered',
    'received'     => 'Received',
    'partial_paid' => 'Partial Paid',
    'paid'         => 'Paid',
];
$st    = $purchase['status'] ?? 'ordered';
$color = $statusColors[$st] ?? 'secondary';
$icon  = $statusIcons[$st]  ?? 'fa-box';
$label = $statusLabels[$st] ?? ucfirst($st);
?>

<div class="row g-4 animate-wow">

    <!-- ===================== HEADER ===================== -->
    <div class="col-12">
        <div class="premium-list p-5 text-white border-0 shadow-lg" style="background: #0f172a; border-radius: 40px;">
            <div class="row align-items-center gy-3">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                            <i class="fas fa-file-invoice fa-2x"></i>
                        </div>
                        <div>
                            <p class="text-white-50 m-0 extra-small fw-bold text-uppercase">Purchase #<?= $purchase['id'] ?></p>
                            <h2 class="fw-900 m-0"><?= esc($purchase['vendor_name'] ?? 'Unknown Vendor') ?></h2>
                            <span class="badge bg-<?= $color ?> mt-2 px-3 py-2 rounded-pill">
                                <i class="fas <?= $icon ?> me-1"></i><?= $label ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end d-flex flex-column align-items-md-end gap-3">
                    <div>
                        <div class="text-white-50 extra-small fw-bold text-uppercase">Total Amount</div>
                        <h2 class="fw-900 m-0 text-primary mb-2">Rs. <?= number_format($purchase['total_amount'], 2) ?></h2>
                        <?php if ($purchase['paid_amount'] > 0 || $purchase['status'] == 'paid'): ?>
                            <div class="d-flex justify-content-md-end gap-3 align-items-center">
                                <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                    <i class="fas fa-check-circle me-1"></i> Paid: Rs. <?= number_format($purchase['paid_amount'], 2) ?>
                                </div>
                                <?php if ($purchase['due_amount'] > 0): ?>
                                    <div class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                        Due: Rs. <?= number_format($purchase['due_amount'], 2) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex gap-2 justify-content-md-end mt-2">
                        <a href="<?= base_url('purchases/invoice/' . $purchase['id']) ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                        <button class="btn btn-sm btn-warning rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editHeaderModal">
                            <i class="fas fa-pen me-1"></i> Edit
                        </button>
                        <a href="<?= base_url('purchases') ?>" class="btn btn-sm btn-outline-light rounded-pill px-3">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===================== META CARDS ===================== -->
    <div class="col-md-4">
        <div class="premium-list p-4 h-100 bg-white shadow border-0">
            <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-calendar me-2 text-primary"></i>Purchase Date</div>
            <div class="fw-900 fs-5 text-dark"><?= date('d M Y', strtotime($purchase['date'])) ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 h-100 bg-white shadow border-0">
            <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-tag me-2 text-primary"></i>Status</div>
            <span class="badge bg-<?= $color ?> fs-6 px-3 py-2 rounded-pill"><i class="fas <?= $icon ?> me-1"></i><?= $label ?></span>
        </div>
    </div>
    <div class="col-md-4">
        <div class="premium-list p-4 h-100 bg-white shadow border-0">
            <div class="text-muted small fw-bold text-uppercase mb-1"><i class="fas fa-note-sticky me-2 text-primary"></i>Note</div>
            <div class="fw-700 text-dark"><?= esc($purchase['note'] ?? '—') ?></div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="col-12">
        <div class="alert alert-success rounded-4 fw-bold"><i class="fas fa-circle-check me-2"></i><?= session()->getFlashdata('success') ?></div>
    </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="col-12">
        <div class="alert alert-danger rounded-4 fw-bold"><i class="fas fa-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?></div>
    </div>
    <?php endif; ?>

    <!-- ===================== LINE ITEMS ===================== -->
    <div class="col-12">
        <div class="premium-list bg-white shadow border-0 overflow-hidden">
            <div class="px-5 py-4 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h5 class="fw-900 m-0 text-dark">Items in this Purchase</h5>
                    <p class="text-muted small m-0 mt-1"><?= count($items) ?> product(s)</p>
                </div>
                <button class="btn btn-dark rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-plus-circle me-1"></i> Add Item
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0 hover-rows">
                    <thead class="bg-light">
                        <tr class="text-muted extra-small text-uppercase fw-900">
                            <th class="px-4 py-4 border-0">#</th>
                            <th class="py-4 border-0">Product</th>
                            <th class="py-4 border-0">Batch</th>
                            <th class="py-4 border-0">MFG</th>
                            <th class="py-4 border-0">EXP</th>
                            <th class="py-4 border-0 text-center">Qty</th>
                            <th class="py-4 border-0 text-end">Cost</th>
                            <th class="py-4 border-0 text-end">MRP</th>
                            <th class="py-4 border-0 text-end">Subtotal</th>
                            <th class="py-4 border-0 text-center px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">No items found.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($items as $idx => $item): ?>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="idx-badge"><?= $idx + 1 ?></div>
                            </td>
                            <td class="py-3 fw-bold text-dark"><?= esc($item['product_name']) ?></td>
                            <td class="py-3"><span class="badge bg-light text-dark border fw-bold"><?= esc($item['batch_id']) ?></span></td>
                            <td class="py-3 text-muted small"><?= $item['mfg_date'] ? date('d M Y', strtotime($item['mfg_date'])) : '—' ?></td>
                            <td class="py-3 small">
                                <?php
                                $exp = $item['exp_date'] ? strtotime($item['exp_date']) : null;
                                $expStr = $exp ? date('d M Y', $exp) : '—';
                                $expClass = ($exp && $exp < time()) ? 'text-danger fw-bold' : 'text-muted';
                                ?>
                                <span class="<?= $expClass ?>"><?= $expStr ?></span>
                            </td>
                            <td class="py-3 text-center fw-bold"><?= number_format($item['qty']) ?></td>
                            <td class="py-3 text-end text-muted">Rs. <?= number_format($item['cost'], 2) ?></td>
                            <td class="py-3 text-end text-muted">Rs. <?= number_format($item['price'], 2) ?></td>
                            <td class="py-3 text-end fw-900 text-dark">Rs. <?= number_format($item['qty'] * $item['cost'], 2) ?></td>
                            <td class="py-3 text-center px-4">
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-2"
                                        onclick='openEditItem(<?= json_encode($item) ?>)'
                                        title="Edit">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <a href="<?= base_url('purchases/delete_item/' . $item['id']) ?>"
                                       class="btn btn-sm btn-outline-danger rounded-pill px-2"
                                       onclick="return confirm('Remove this item?')"
                                       title="Delete">
                                        <i class="fas fa-trash-can"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <!-- Total row -->
                        <tr class="bg-light fw-900">
                            <td colspan="8" class="text-end px-4 py-4 text-uppercase text-muted">Grand Total</td>
                            <td class="text-end py-4 text-dark fs-5">Rs. <?= number_format($purchase['total_amount'], 2) ?></td>
                            <td></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===================== EDIT HEADER MODAL ===================== -->
<div class="modal fade" id="editHeaderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-900">Edit Purchase Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('purchases/update') ?>" method="POST">
                <div class="modal-body p-4 pt-3">
                    <input type="hidden" name="id" value="<?= $purchase['id'] ?>">
                    <input type="hidden" name="vendor_id" value="<?= $purchase['vendor_id'] ?>">

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Date</label>
                        <input type="date" name="date" class="form-control" value="<?= $purchase['date'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="ordered"      <?= $purchase['status'] == 'ordered'      ? 'selected' : '' ?>>📦 Ordered</option>
                            <option value="received"     <?= $purchase['status'] == 'received'     ? 'selected' : '' ?>>✅ Received</option>
                            <option value="partial_paid" <?= $purchase['status'] == 'partial_paid' ? 'selected' : '' ?>>💸 Partial Paid</option>
                            <option value="paid"         <?= $purchase['status'] == 'paid'         ? 'selected' : '' ?>>✔️ Paid</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small text-uppercase">Note</label>
                        <textarea name="note" class="form-control" rows="3"><?= esc($purchase['note'] ?? '') ?></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== EDIT ITEM MODAL ===================== -->
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-900">Edit Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('purchases/update_item') ?>" method="POST">
                <div class="modal-body p-4 pt-3">
                    <input type="hidden" name="id" id="edit_item_id">
                    <input type="hidden" name="purchase_id" value="<?= $purchase['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Batch ID</label>
                            <input type="text" name="batch_id" id="edit_batch_id" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Qty</label>
                            <input type="number" name="qty" id="edit_qty" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Cost (Rs.)</label>
                            <input type="number" step="0.01" name="cost" id="edit_cost" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">MRP (Rs.)</label>
                            <input type="number" step="0.01" name="price" id="edit_price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">MFG Date</label>
                            <input type="date" name="mfg_date" id="edit_mfg_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">EXP Date</label>
                            <input type="date" name="exp_date" id="edit_exp_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ===================== ADD ITEM MODAL ===================== -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-0">
                <h5 class="modal-title fw-900">Add New Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('purchases/add_item') ?>" method="POST">
                <div class="modal-body p-4 pt-3">
                    <input type="hidden" name="purchase_id" value="<?= $purchase['id'] ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Product</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">Select Product...</option>
                                <?php foreach($products as $p): ?>
                                    <option value="<?= $p['detail_id'] ?>"><?= esc($p['product_name']) ?> [<?= esc($p['unit_value'] ?? '') ?> <?= esc($p['unit'] ?? '') ?>]</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Batch ID</label>
                            <input type="text" name="batch_id" class="form-control" placeholder="BAT-001" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Qty</label>
                            <input type="number" name="qty" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Cost (Rs.)</label>
                            <input type="number" step="0.01" name="cost" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">MRP (Rs.)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">MFG Date</label>
                            <input type="date" name="mfg_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">EXP Date</label>
                            <input type="date" name="exp_date" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-5 fw-bold">Add Item</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.idx-badge {
    width: 30px; height: 30px;
    background: #f1f5f9; color: #64748b;
    border-radius: 50%;
    display: inline-flex; align-items: center; justify-content: center;
    font-weight: 900; font-size: 12px;
}
.hover-rows tbody tr { transition: background 0.15s; }
.hover-rows tbody tr:hover { background: #f8fafc; }
</style>

<script>
function openEditItem(item) {
    document.getElementById('edit_item_id').value   = item.id;
    document.getElementById('edit_batch_id').value  = item.batch_id;
    document.getElementById('edit_qty').value       = item.qty;
    document.getElementById('edit_cost').value      = item.cost;
    document.getElementById('edit_price').value     = item.price;
    document.getElementById('edit_mfg_date').value  = item.mfg_date || '';
    document.getElementById('edit_exp_date').value  = item.exp_date || '';
    new bootstrap.Modal(document.getElementById('editItemModal')).show();
}
</script>

<?= $this->endSection() ?>
