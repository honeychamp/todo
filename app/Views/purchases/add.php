<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<!-- Products data for JS -->
<script>
    const productsData = <?= json_encode(array_column($products, null, 'id')) ?>;
    const selectedVendorId = <?= $vendor['id'] ?>;
    const productsOptions = `<?php foreach($products as $p): ?><option value="<?= $p['id'] ?>" data-cost="<?= $p['cost'] ?>"><?= esc($p['name']) ?> [<?= esc($p['unit_value']) ?> <?= esc($p['unit']) ?>]</option><?php endforeach; ?>`;
</script>

<form action="<?= base_url('purchases/process_add') ?>" method="POST" id="bulkStockForm">
<input type="hidden" name="redirect_vendor_id" value="<?= $vendor['id'] ?>">

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="premium-list p-5 text-white border-0 shadow-lg mb-2" style="background: #0f172a; border-radius: 40px;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-4">
                        <div class="rounded-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 70px; height: 70px; background: linear-gradient(135deg, #0ea5e9, #6366f1);">
                            <i class="fas fa-truck-ramp-box fa-2x"></i>
                        </div>
                        <div>
                            <h2 class="fw-900 m-0">STOCK PROCUREMENT</h2>
                            <p class="text-white-50 m-0 mt-1">Vendor: <span class="text-white fw-bold"><?= esc($vendor['name']) ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="d-inline-block text-start p-3 px-4 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                        <div class="text-white-50 extra-small fw-bold text-uppercase">Projected Total Value</div>
                        <h2 class="fw-900 m-0 text-primary" id="grand_total_disp">Rs. 0.00</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="premium-list p-0 shadow-lg border-0 bg-white overflow-hidden">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-30">
                <div>
                    <h5 class="fw-900 m-0 text-dark">Material Entry Terminal</h5>
                    <p class="text-muted small m-0 mt-1">Multi-batch procurement grid for bulk stock intake.</p>
                </div>
                <button type="button" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" onclick="addStockRow()">
                    <i class="fas fa-plus-circle me-2"></i> Add Item Batch
                </button>
            </div>

            <div class="table-responsive p-0">
                <table class="table align-middle mb-0" id="stockTable">
                    <thead class="bg-light">
                        <tr class="text-muted extra-small text-uppercase fw-900">
                            <th class="px-5 py-4 border-0" style="width: 50px;">Index</th>
                            <th class="py-4 border-0">Batch Identifer</th>
                            <th class="py-4 border-0">Medicine / Material</th>
                            <th class="py-4 border-0">Life Dates (MFG/EXP)</th>
                            <th class="py-4 border-0 text-center" style="width: 100px;">Units</th>
                            <th class="py-4 border-0" style="width: 130px;">Cost (Rs.)</th>
                            <th class="py-4 border-0" style="width: 130px;">MRP (Rs.)</th>
                            <th class="py-4 border-0 text-end px-5">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="stockBody">
                        <!-- rows injected by JS -->
                    </tbody>
                </table>
            </div>

            <div class="p-5 border-top d-flex justify-content-between align-items-center bg-light bg-opacity-20">
                <div class="d-flex gap-2">
                    <a href="<?= base_url('purchases') ?>" class="btn btn-light rounded-pill px-5 py-3 fw-bold text-muted border">
                        <i class="fas fa-times-circle me-2"></i> DISCARD LOG
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="text-end">
                        <div class="text-muted extra-small fw-bold">TOTAL PAYABLE VALUE</div>
                        <div class="h3 fw-900 m-0 text-dark" id="grand_total_footer">Rs. 0.00</div>
                    </div>
                    <button type="submit" class="btn btn-vibrant rounded-pill px-5 py-3 fw-900 shadow-xl fs-5">
                        <i class="fas fa-database me-2"></i> COMMIT TO DATABASE
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
    
    #stockTable tbody tr {
        transition: all 0.2s;
        border-bottom: 1px solid #f1f5f9;
        position: relative;
    }
    #stockTable tbody tr:hover { background-color: rgba(59, 130, 246, 0.02); }
    
    .remove-row-btn {
        position: absolute;
        left: 5px;
        top: 50%;
        transform: translateY(-50%);
        width: 30px; height: 30px;
        border-radius: 50%;
        background: #fee2e2;
        color: #ef4444;
        display: none;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s;
    }
    #stockTable tbody tr:hover .remove-row-btn { display: flex; }
    .remove-row-btn:hover { background: #ef4444; color: white; }
    
    .idx-circle {
        width: 32px; height: 32px;
        background: #f1f5f9;
        color: #64748b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 12px;
    }
</style>

<script>
let rowIndex = 0;

function addStockRow() {
    rowIndex++;
    const tbody = document.getElementById('stockBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowIndex;
    tr.innerHTML = `
        <td class="px-5">
            <button type="button" class="remove-row-btn" onclick="removeRow(${rowIndex})"><i class="fas fa-trash-can"></i></button>
            <div class="idx-circle">${rowIndex}</div>
        </td>
        <td>
            <input type="hidden" name="vendor_id[]" value="${selectedVendorId}">
            <input type="text" name="batch_id[]" class="input-field" placeholder="E.g. BAT-102" required>
        </td>
        <td>
            <select name="product_id[]" class="input-field" required onchange="autoCost(this, ${rowIndex})">
                <option value="">Select Material...</option>
                ${productsOptions}
            </select>
        </td>
        <td>
            <div class="d-flex gap-2">
                <input type="date" name="manufacture_date[]" class="input-field" required title="MFG Date">
                <input type="date" name="expiry_date[]" class="input-field text-danger" required title="EXP Date">
            </div>
        </td>
        <td><input type="number" name="qty[]" class="input-field text-center" placeholder="0" min="1" required oninput="calculateSubtotal(${rowIndex})"></td>
        <td><input type="number" step="0.01" name="cost[]" class="input-field" id="cost_${rowIndex}" placeholder="0.00" required oninput="calculateSubtotal(${rowIndex})"></td>
        <td><input type="number" step="0.01" name="price[]" class="input-field" placeholder="0.00" required></td>
        <td class="text-end px-5">
            <div class="fw-900 text-dark fs-5" id="subtotal_${rowIndex}">Rs. 0.00</div>
        </td>
    `;
    tbody.appendChild(tr);
    calculateGrandTotal();
}

function removeRow(id) {
    const row = document.getElementById('row_' + id);
    if (row) row.remove();
    calculateGrandTotal();
}

function autoCost(select, idx) {
    const opt = select.options[select.selectedIndex];
    const cost = opt.getAttribute('data-cost');
    if (cost) {
        const costInput = document.getElementById('cost_' + idx);
        if (costInput) {
            costInput.value = cost;
            calculateSubtotal(idx);
        }
    }
}

function calculateSubtotal(idx) {
    const tr = document.getElementById('row_' + idx);
    const qty = tr.querySelector('input[name="qty[]"]').value || 0;
    const cost = tr.querySelector('input[name="cost[]"]').value || 0;
    const subtotal = qty * cost;
    document.getElementById('subtotal_' + idx).innerText = 'Rs. ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
    calculateGrandTotal();
}

function calculateGrandTotal() {
    const costs = document.getElementsByName('cost[]');
    const qtys = document.getElementsByName('qty[]');
    let grandTotal = 0;
    for(let i=0; i<costs.length; i++) {
        grandTotal += (parseFloat(costs[i].value) || 0) * (parseFloat(qtys[i].value) || 0);
    }
    const formatted = 'Rs. ' + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2});
    document.getElementById('grand_total_disp').innerText = formatted;
    document.getElementById('grand_total_footer').innerText = formatted;
}

document.addEventListener('DOMContentLoaded', function() {
    addStockRow();
});
</script>

<?= $this->endSection() ?>
