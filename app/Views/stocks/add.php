<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Products data for JS -->
<script>
    const productsData = <?= json_encode(array_column($products, null, 'id')) ?>;
    const selectedVendorId = <?= $vendor['id'] ?>;
    const productsOptions = `<?php foreach($products as $p): ?><option value="<?= $p['id'] ?>" data-cost="<?= $p['cost'] ?>"><?= esc($p['name']) ?> [<?= esc($p['unit_value']) ?> <?= esc($p['unit']) ?>]</option><?php endforeach; ?>`;
</script>

<form action="<?= base_url('stocks/add_purchase') ?>" method="POST" id="bulkStockForm">
<!-- Hidden: redirect back to this vendor after save -->
<input type="hidden" name="redirect_vendor_id" value="<?= $vendor['id'] ?>">

<div class="row g-4 animate-wow">

    <!-- Page Title -->
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-800 m-0">Stock Add Karein</h4>
                <p class="text-muted small m-0">
                    <i class="fas fa-building me-1 text-primary"></i>
                    Vendor: <strong class="text-primary"><?= esc($vendor['name']) ?></strong>
                    &nbsp;&bull;&nbsp;
                    <a href="<?= base_url('stocks/select_vendor') ?>" class="text-muted text-decoration-none small">
                        <i class="fas fa-exchange-alt me-1"></i> Vendor Change Karein
                    </a>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('stocks/vendor/' . $vendor['id']) ?>" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-2"></i> Back
                </a>
                <button type="submit" class="btn btn-vibrant rounded-pill px-4">
                    <i class="fas fa-check-circle me-2"></i> Save All Stock
                </button>
            </div>
        </div>
    </div>

    <!-- Vendor Info Banner -->
    <div class="col-12">
        <div class="p-3 px-4 rounded-4 d-flex align-items-center gap-3" style="background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(14,165,233,0.08)); border: 1.5px solid rgba(99,102,241,0.15);">
            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#0ea5e9);display:flex;align-items:center;justify-content:center;">
                <i class="fas fa-building text-white"></i>
            </div>
            <div>
                <div class="fw-800 text-dark"><?= esc($vendor['name']) ?></div>
                <div class="small text-muted">
                    <i class="fas fa-phone me-1"></i><?= esc($vendor['phone']) ?>
                    <?php if ($vendor['email']): ?> &nbsp;&bull;&nbsp; <i class="fas fa-envelope me-1"></i><?= esc($vendor['email']) ?><?php endif; ?>
                    <?php if ($vendor['address']): ?> &nbsp;&bull;&nbsp; <i class="fas fa-location-dot me-1"></i><?= esc($vendor['address']) ?><?php endif; ?>
                </div>
            </div>
            <div class="ms-auto">
                <span class="badge rounded-pill px-3 py-2 fw-bold" style="background:rgba(99,102,241,0.1);color:#6366f1;">
                    Yahi vendor ka stock add ho raha hai
                </span>
            </div>
        </div>
    </div>

    <!-- Stock Rows Card -->
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-800 m-0">Stock Items</h5>
                    <p class="text-muted small m-0">Har row ek product batch entry hai.</p>
                </div>
                <button type="button" class="btn btn-outline-primary rounded-pill px-4" onclick="addStockRow()">
                    <i class="fas fa-plus me-2"></i> Row Add Karein
                </button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0" id="stockTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 border-0" style="min-width:50px;">#</th>
                            <th class="py-3 border-0" style="min-width:130px;">Batch ID</th>
                            <th class="py-3 border-0" style="min-width:220px;">Product</th>
                            <th class="py-3 border-0" style="min-width:140px;">MFG Date</th>
                            <th class="py-3 border-0" style="min-width:140px;">EXP Date</th>
                            <th class="py-3 border-0" style="min-width:90px;">Qty</th>
                            <th class="py-3 border-0" style="min-width:110px;">Cost (Rs.)</th>
                            <th class="py-3 border-0" style="min-width:110px;">Price (Rs.)</th>
                            <th class="py-3 border-0 text-center" style="min-width:60px;"></th>
                        </tr>
                    </thead>
                    <tbody id="stockBody">
                        <!-- rows injected by JS -->
                    </tbody>
                </table>
            </div>

            <div class="p-4 px-5 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted small" id="rowCount">0 item(s) added</span>
                <button type="submit" class="btn btn-vibrant rounded-pill px-5 py-2 fw-bold">
                    <i class="fas fa-save me-2"></i> Save All Stock
                </button>
            </div>
        </div>
    </div>

</div>
</form>

<style>
    #stockTable td input,
    #stockTable td select {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 10px;
        font-size: 13px;
        background: #f8fafc;
        width: 100%;
        transition: border-color 0.2s;
    }
    #stockTable td input:focus,
    #stockTable td select:focus {
        outline: none;
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    #stockTable tbody tr {
        border-bottom: 1px solid #f1f5f9;
    }
    .row-num {
        width: 28px; height: 28px;
        background: linear-gradient(135deg, #6366f1, #0ea5e9);
        color: white;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }
    .btn-remove-row {
        width: 28px; height: 28px;
        border-radius: 50%;
        border: none;
        background: #fee2e2;
        color: #ef4444;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-remove-row:hover { background: #ef4444; color: white; }
</style>

<script>
let rowIndex = 0;

function addStockRow() {
    rowIndex++;
    const tbody = document.getElementById('stockBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowIndex;
    tr.innerHTML = `
        <td class="px-4"><span class="row-num">${rowIndex}</span></td>
        <td><input type="hidden" name="vendor_id[]" value="${selectedVendorId}">
            <input type="text" name="batch_id[]" placeholder="BATCH-101" required>
        </td>
        <td>
            <select name="product_id[]" required onchange="autoCost(this, ${rowIndex})">
                <option value="">— Select —</option>
                ${productsOptions}
            </select>
        </td>
        <td><input type="date" name="manufacture_date[]" required></td>
        <td><input type="date" name="expiry_date[]" required></td>
        <td><input type="number" name="qty[]" placeholder="0" min="1" required></td>
        <td><input type="number" step="0.01" name="cost[]" id="cost_${rowIndex}" placeholder="0.00" required></td>
        <td><input type="number" step="0.01" name="price[]" placeholder="0.00" required></td>
        <td class="text-center">
            <button type="button" class="btn-remove-row" onclick="removeRow(${rowIndex})">
                <i class="fas fa-times"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    updateRowCount();
}

function removeRow(id) {
    const row = document.getElementById('row_' + id);
    if (row) row.remove();
    updateRowCount();
}

function autoCost(select, idx) {
    const opt = select.options[select.selectedIndex];
    const cost = opt.getAttribute('data-cost');
    if (cost) {
        const costInput = document.getElementById('cost_' + idx);
        if (costInput) costInput.value = cost;
    }
}

function updateRowCount() {
    const count = document.getElementById('stockBody').children.length;
    document.getElementById('rowCount').textContent = count + ' item(s) added';
}

document.addEventListener('DOMContentLoaded', function() {
    addStockRow();
});
</script>

<?= $this->endSection() ?>
