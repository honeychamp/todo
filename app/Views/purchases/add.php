<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Products data for JS -->
<script>
    const productsData = <?= json_encode(array_column($products, null, 'detail_id')) ?>;
    const selectedVendorId = <?= $vendor['id'] ?? 0 ?>;
    const productsOptions = `<?php foreach($products as $p): ?><option value="<?= $p['detail_id'] ?>" data-cost="<?= $p['cost'] ?>"><?= esc($p['product_name']) ?> [<?= esc($p['unit_value'] ?? '') ?> <?= esc($p['unit'] ?? '') ?>]</option><?php endforeach; ?>`;
</script>

<form action="<?= base_url('purchases/process_add') ?>" method="POST" id="bulkStockForm">

<div class="row g-4 animate-wow">

    <!-- ============================================================
         HEADER BANNER
    ============================================================ -->
    <div class="col-12">
        <div class="premium-list p-5 text-white border-0 shadow-lg mb-2" style="background: #0f172a; border-radius: 40px;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                            <i class="fas fa-truck-ramp-box fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="fw-900 m-0">ADD NEW PURCHASE</h2>
                            <p class="text-white-50 m-0 mt-1">Vendor: <span class="text-white fw-bold"><?= esc($vendor['name'] ?? 'Unknown') ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block text-start p-3 px-4 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-white-50 extra-small fw-bold text-uppercase">Total Bill Value</div>
                        <h2 class="fw-900 m-0 text-primary" id="grand_total_disp">Rs. 0.00</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         PURCHASE META: date, status, note
    ============================================================ -->
    <div class="col-12">
        <div class="premium-list p-4 px-5 shadow border-0 bg-white">
            <h6 class="fw-900 text-dark mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Purchase Information</h6>
            <div class="row g-3">
                <!-- Hidden vendor -->
                <input type="hidden" name="vendor_id" value="<?= $vendor['id'] ?? '' ?>">

                <!-- Date -->
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Purchase Date</label>
                    <input type="date" name="date" class="input-field" value="<?= date('Y-m-d') ?>" required>
                </div>

                <!-- Status -->
                <div class="col-md-3">
                    <label class="form-label fw-bold text-muted small text-uppercase">Status</label>
                    <select name="status" class="input-field" required>
                        <option value="ordered">📦 Ordered</option>
                        <option value="received" selected>✅ Received</option>
                        <option value="partial_paid">💸 Partial Paid</option>
                        <option value="paid">✔️ Paid</option>
                    </select>
                </div>

                <!-- Note -->
                <div class="col-md-6">
                    <label class="form-label fw-bold text-muted small text-uppercase">Note (Optional)</label>
                    <input type="text" name="note" class="input-field" placeholder="E.g. Monthly restock, urgent order...">
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         LINE ITEMS TABLE
    ============================================================ -->
    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h5 class="fw-900 m-0 text-dark">Add Items</h5>
                    <p class="text-muted small m-0 mt-1">Add details of all products in this purchase.</p>
                </div>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="addRow()">
                    <i class="fas fa-plus-circle me-2"></i> Add Item
                </button>
            </div>

            <div class="table-responsive p-0">
                <table class="table align-middle mb-0" id="itemsTable">
                    <thead class="bg-light">
                        <tr class="text-muted extra-small text-uppercase fw-900">
                            <th class="px-5 py-4 border-0" style="width: 50px;">#</th>
                            <th class="py-4 border-0">Batch No.</th>
                            <th class="py-4 border-0">Product Name</th>
                            <th class="py-4 border-0">MFG / EXP Dates</th>
                            <th class="py-4 border-0 text-center" style="width: 100px;">Units</th>
                            <th class="py-4 border-0" style="width: 130px;">Cost (Rs.)</th>
                            <th class="py-4 border-0" style="width: 130px;">MRP (Rs.)</th>
                            <th class="py-4 border-0 text-end px-5">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- rows injected by JS -->
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-top d-flex justify-content-between align-items-center bg-light bg-opacity-20">
                <div class="d-flex gap-2">
                    <a href="<?= base_url('purchases') ?>" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-muted border">
                        <i class="fas fa-times-circle me-2"></i> CANCEL
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-end">
                        <div class="text-muted extra-small fw-bold">TOTAL PAYABLE VALUE</div>
                        <div class="h3 fw-900 m-0 text-dark" id="grand_total_footer">Rs. 0.00</div>
                    </div>
                    <button type="submit" class="btn btn-vibrant rounded-pill px-5 py-3 fw-900 shadow-xl fs-5">
                        <i class="fas fa-check-circle me-2"></i> SAVE PURCHASE
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
</form>

<style>
    .input-field {
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 14px;
        font-weight: 700;
        background: #f8fafc;
        width: 100%;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1e293b;
    }
    .input-field:focus {
        outline: none;
        border-color: #3b82f6;
        background: #fff;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    .input-field::placeholder { color: #94a3b8; font-weight: 500; }

    #itemsTable tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    #itemsTable tbody tr:hover { background-color: rgba(59, 130, 246, 0.02); }

    .remove-row-btn {
        position: absolute;
        left: 5px; top: 50%;
        transform: translateY(-50%);
        width: 30px; height: 30px;
        border-radius: 50%;
        background: #fee2e2; color: #ef4444;
        display: none; align-items: center; justify-content: center;
        border: none; cursor: pointer; z-index: 10;
        transition: all 0.2s;
    }
    #itemsTable tbody tr:hover .remove-row-btn { display: flex; }
    .remove-row-btn:hover { background: #ef4444; color: white; }

    .idx-circle {
        width: 32px; height: 32px;
        background: #f1f5f9; color: #64748b;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 900; font-size: 12px;
    }
</style>

<script>
let rowIndex = 0;

function addRow() {
    rowIndex++;
    const tbody = document.getElementById('itemsBody');
    const tr    = document.createElement('tr');
    tr.id = 'row_' + rowIndex;
    tr.innerHTML = `
        <td class="px-5">
            <button type="button" class="remove-row-btn" onclick="removeRow(${rowIndex})"><i class="fas fa-trash-can"></i></button>
            <div class="idx-circle">${rowIndex}</div>
        </td>
        <td>
            <input type="text" name="batch_id[]" class="input-field" placeholder="E.g. BAT-001" required>
        </td>
        <td>
            <select name="product_id[]" class="input-field" required onchange="autoCost(this, ${rowIndex})">
                <option value="">Select Item...</option>
                ${productsOptions}
            </select>
        </td>
        <td>
            <div class="d-flex gap-2">
                <input type="date" name="mfg_date[]" class="input-field" required title="MFG Date">
                <input type="date" name="exp_date[]" class="input-field text-danger" required title="EXP Date">
            </div>
        </td>
        <td><input type="number" name="qty[]" class="input-field text-center" placeholder="0" min="1" required oninput="calcSubtotal(${rowIndex})"></td>
        <td><input type="number" step="0.01" name="cost[]" class="input-field" id="cost_${rowIndex}" placeholder="0.00" required oninput="calcSubtotal(${rowIndex})"></td>
        <td><input type="number" step="0.01" name="price[]" class="input-field" placeholder="0.00" required></td>
        <td class="text-end px-5">
            <div class="fw-900 text-dark fs-5" id="subtotal_${rowIndex}">Rs. 0.00</div>
        </td>
    `;
    tbody.appendChild(tr);
    calcGrandTotal();
}

function removeRow(id) {
    const row = document.getElementById('row_' + id);
    if (row) row.remove();
    calcGrandTotal();
}

function autoCost(select, idx) {
    const opt  = select.options[select.selectedIndex];
    const cost = opt.getAttribute('data-cost');
    if (cost) {
        const costInput = document.getElementById('cost_' + idx);
        if (costInput) { costInput.value = cost; calcSubtotal(idx); }
    }
}

function calcSubtotal(idx) {
    const tr     = document.getElementById('row_' + idx);
    const qty    = tr.querySelector('input[name="qty[]"]').value || 0;
    const cost   = tr.querySelector('input[name="cost[]"]').value || 0;
    const sub    = qty * cost;
    document.getElementById('subtotal_' + idx).innerText = 'Rs. ' + sub.toLocaleString(undefined, {minimumFractionDigits: 2});
    calcGrandTotal();
}

function calcGrandTotal() {
    const costs = document.getElementsByName('cost[]');
    const qtys  = document.getElementsByName('qty[]');
    let total   = 0;
    for (let i = 0; i < costs.length; i++) {
        total += (parseFloat(costs[i].value) || 0) * (parseFloat(qtys[i].value) || 0);
    }
    const fmt = 'Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('grand_total_disp').innerText   = fmt;
    document.getElementById('grand_total_footer').innerText = fmt;
}

document.addEventListener('DOMContentLoaded', function () { 
    addRow(); 
    const preId = <?= $preSelectId ?: 'null' ?>;
    if (preId) {
        const firstRowSelect = document.querySelector('select[name="product_id[]"]');
        if (firstRowSelect) {
            firstRowSelect.value = preId;
            autoCost(firstRowSelect, 1);
            // Focus on quantity for easier working
            const qtyInput = document.querySelector('input[name="qty[]"]');
            if (qtyInput) qtyInput.focus();
        }
    }
});
</script>

<?= $this->endSection() ?>
