<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --premium-bg: #fdfdfe;
        --glass-bg: rgba(255, 255, 255, 0.85);
        --accent-gradient: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --success-gradient: linear-gradient(135deg, #22c55e 0%, #10b981 100%);
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    body { background-color: #f8fafc; font-family: 'Inter', system-ui, -apple-system, sans-serif; }

    .pos-container { 
        display: flex; gap: 24px; height: calc(100vh - 160px); min-height: 700px; 
        padding: 10px 0;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .product-sidebar { flex: 0 0 380px; display: flex; flex-direction: column; gap: 24px; }
    .billing-section { flex: 1; display: flex; flex-direction: column; gap: 24px; }

    /* Glassmorphism Cards */
    .premium-card { 
        height: 100%; border-radius: 24px; background: #fff; border: 1px solid #f1f5f9; 
        box-shadow: var(--card-shadow); overflow: hidden; display: flex; flex-direction: column;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .premium-card:hover { transform: translateY(-2px); box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.08); }

    .product-list-area { overflow-y: auto; flex: 1; padding: 20px; scrollbar-width: thin; scrollbar-color: #e2e8f0 transparent; }
    
    .item-select-node {
        padding: 16px; border-radius: 20px; border: 1px solid #f1f5f9; margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; display: flex; 
        justify-content: space-between; align-items: center; background: #fff;
    }
    .item-select-node:hover { 
        background: #f8faff; border-color: #6366f1; transform: scale(1.02);
        box-shadow: 0 8px 20px -8px rgba(99, 102, 241, 0.2);
    }
    .item-select-node .badge { padding: 6px 12px; font-weight: 700; border-radius: 100px; }

    /* Billing Section Overhaul */
    .bill-header { padding: 30px; border-bottom: 1px solid #f1f5f9; background: linear-gradient(to bottom, #fcfdfe, #fff); }
    .bill-footer { padding: 30px; background: #fff; border-top: 2px solid #f1f5f9; border-radius: 0 0 24px 24px; }

    .cart-table-area { flex: 1; overflow-y: auto; padding: 0; }
    .cart-item-row td { padding: 20px 15px !important; vertical-align: middle; border-bottom: 1px solid #f8fafc; }
    .cart-item-row { transition: all 0.3s ease; }
    .cart-item-row:hover { background-color: #fcfdfe; }
    
    .input-cart { 
        width: 100%; border: 1.5px solid #eef2f6; border-radius: 12px; padding: 10px 12px; 
        font-weight: 700; text-align: center; color: #1e293b; background: #f8fafc;
        transition: all 0.2s;
    }
    .input-cart:focus { border-color: #6366f1; outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    
    .text-premium { font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
    .total-labels { font-size: 0.85rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; margin-bottom: 4px; display: block; }
    .total-values { font-size: 1.25rem; font-weight: 900; color: #1e293b; }

    /* Buttons */
    .btn-premium-confirm {
        background: var(--accent-gradient); color: white; border: none; padding: 16px 48px;
        border-radius: 100px; font-weight: 900; font-size: 1.1rem; letter-spacing: 0.5px;
        box-shadow: 0 10px 25px -5px rgba(99, 102, 241, 0.4); transition: all 0.3s;
    }
    .btn-premium-confirm:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(99, 102, 241, 0.5); color: #fff; }
    .btn-premium-confirm:active { transform: translateY(-1px); }

    .btn-add-item {
        background: #f1f5f9; color: #64748b; border: 2px dashed #cbd5e1;
        width: 100%; padding: 15px; border-radius: 20px; font-weight: 800;
        transition: all 0.2s;
    }
    .btn-add-item:hover { background: #e0e7ff; color: #4338ca; border-color: #4338ca; border-style: solid; }

    .sidebar-search-container { position: relative; }
    .sidebar-search-container .input-group-text { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); z-index: 10; padding: 0; background: transparent !important; }
    .sidebar-search-container input { padding-left: 45px !important; border-radius: 16px !important; font-weight: 600; }

    /* Extra Micro-Interactions */
    .animate-wow { animation: slideIn 0.3s ease-out; }
    @keyframes slideIn { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }
</style>

<form id="posForm" action="<?= base_url('sales/process') ?>" method="POST">
    <div class="pos-container">
        
        <!-- SIDEBAR: Product Selection -->
        <div class="product-sidebar">
            <div class="premium-card" style="flex: 2;">
                <div class="p-4 border-bottom bg-light bg-opacity-10">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h6 class="text-premium m-0 fw-900 fs-5"><i class="fas fa-boxes-stacked me-2 text-primary"></i>Stock</h6>
                        <span class="badge bg-primary bg-opacity-10 text-primary border-primary extra-small text-uppercase fw-bold rounded-pill">Search</span>
                    </div>
                    <div class="sidebar-search-container">
                        <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="productSearch" class="form-control border-0 py-3 shadow-sm" placeholder="Search product or batch..." onkeyup="searchPanel()">
                    </div>
                </div>
                
                <div class="product-list-area" id="searchNodes">
                    <?php foreach($stocks as $s): ?>
                    <div class="item-select-node shadow-sm" 
                         onclick="addToCart(<?= htmlspecialchars(json_encode($s)) ?>)"
                         data-name="<?= esc($s['product_name']) ?>" 
                         data-batch="<?= esc($s['batch_id']) ?>">
                        <div style="flex: 1;">
                            <div class="fw-900 text-dark small"><?= esc($s['product_name']) ?></div>
                            <div class="text-muted extra-small fw-bold">
                                <?= esc($s['product_unit_value']) ?> <?= esc($s['unit']) ?> | <span class="text-primary"><?= esc($s['batch_id']) ?></span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="badge <?= $s['available_qty'] < 10 ? 'bg-danger' : 'bg-primary' ?> rounded-pill mb-1">
                                <?= esc($s['available_qty']) ?> left
                            </div>
                            <div class="text-primary fw-800 small">Rs. <?= number_format($s['price'], 2) ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Recent Sales Card -->
            <div class="premium-card" style="flex: 1; min-height: 250px;">
                <div class="p-4 border-bottom bg-light bg-opacity-10 d-flex justify-content-between align-items-center">
                    <h6 class="text-premium m-0 small fw-900 text-uppercase"><i class="fas fa-clock-rotate-left me-2 text-muted"></i>Recent Sales</h6>
                    <a href="<?= base_url('sales/history') ?>" class="extra-small fw-bold text-primary text-decoration-none">ALL <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="p-0 overflow-auto" style="max-height: 250px;">
                    <div class="list-group list-group-flush" id="recentSalesList">
                        <!-- We will fetch this via PHP or JS. For now, let's use the $last_sales passed from controller if available -->
                        <?php 
                            $db_q = \Config\Database::connect();
                            $recent = $db_q->table('sales')
                                           ->select('sales.*, doctors.name as dr_name')
                                           ->join('doctors', 'doctors.id = sales.doctor_id', 'left')
                                           ->orderBy('sales.sale_date', 'DESC')
                                           ->limit(5)
                                           ->get()->getResultArray();
                            foreach($recent as $rs):
                        ?>
                        <a href="<?= base_url('sales/invoice/'.$rs['id']) ?>" target="_blank" class="list-group-item list-group-item-action border-0 py-2 px-3 small d-flex align-items-center gap-2">
                            <i class="fas fa-print text-muted"></i>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-dark"><?= esc($rs['invoice_no']) ?></span>
                                    <span class="extra-small text-muted"><?= date('h:i A', strtotime($rs['sale_date'])) ?></span>
                                </div>
                                <div class="extra-small text-muted">
                                    <?= esc($rs['dr_name'] ?: ($rs['manual_dr_name'] ?: 'Retail Guest')) ?>
                                    <span class="float-end fw-bold text-primary">Rs.<?= number_format($rs['total_amount'], 0) ?></span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="p-2 border-top bg-light text-center">
                    <a href="<?= base_url('sales/history') ?>" class="extra-small fw-bold text-decoration-none">View All History <i class="fas fa-chevron-right ms-1"></i></a>
                </div>
            </div>
        </div>

        <!-- MAIN: Billing & Cart -->
        <div class="billing-section">
            <div class="premium-card">
                <!-- Bill Header -->
                <div class="bill-header">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="text-premium m-0 fs-4"><i class="fas fa-receipt me-3 text-primary"></i>Sale Terminal</h5>
                        <div class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">INV: #<?= 'NEW-'.date('is') ?></div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="total-labels">Reference / Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-select border-0 bg-light py-3 rounded-4 fw-bold">
                                <option value="">Walking Customer (Retail)</option>
                                <?php foreach($doctors as $dr): ?>
                                    <option value="<?= $dr['id'] ?>"><?= esc($dr['name']) ?> (<?= esc($dr['phone']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="total-labels">Manual Dr Name</label>
                            <input type="text" name="manual_dr_name" class="form-control border-0 bg-light py-3 rounded-4 fw-bold" placeholder="Optional Name">
                        </div>
                        <div class="col-md-3">
                            <label class="total-labels">Phone</label>
                            <input type="text" name="manual_dr_phone" class="form-control border-0 bg-light py-3 rounded-4 fw-bold" placeholder="03XXXXXXXXX" maxlength="11" pattern="\d{11}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                        </div>
                    </div>
                </div>

                <!-- Bill Cart Items -->
                <div class="cart-table-area">
                    <table class="table mb-0" id="cartTable">
                        <thead class="bg-light bg-opacity-30">
                            <tr class="extra-small text-uppercase text-muted">
                                <th class="border-0 px-4 py-4" style="width: 30%;">Item Detail</th>
                                <th class="border-0 py-4 text-center" style="width: 15%;">Unit Value</th>
                                <th class="border-0 py-4 text-center" style="width: 15%;">Unit Price</th>
                                <th class="border-0 py-4 text-center" style="width: 10%;">Qty</th>
                                <th class="border-0 py-4 text-center" style="width: 20%;">Discount</th>
                                <th class="border-0 py-4 text-end px-4" style="width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartBody">
                            <!-- Items will be added here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="6" class="px-4 py-3 border-0 bg-light bg-opacity-50">
                                    <button type="button" onclick="addEmptyRow()" class="btn btn-primary rounded-pill px-4 fw-800 shadow-sm btn-sm">
                                        <i class="fas fa-plus-circle me-1"></i> ADD NEW ITEM
                                    </button>
                                    <small class="text-muted ms-3">You can also select products from the sidebar</small>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Bill Totals -->
                <div class="bill-footer">
                    <div class="row align-items-end">
                        <div class="col-md-7">
                            <div class="row g-4 mb-2">
                                <div class="col-auto">
                                    <span class="total-labels">Total Amount</span>
                                    <div class="total-values" id="sumSubtotal">Rs. 0.00</div>
                                </div>
                                <div class="col-auto">
                                    <span class="total-labels text-danger">Discount</span>
                                    <div class="total-values text-danger" id="sumDiscount">-Rs. 0.00</div>
                                </div>
                                <div class="col-auto border-start ps-4 ms-2">
                                    <span class="total-labels">Cart Items</span>
                                    <div class="total-values" id="topItemCount">0</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="d-flex align-items-center justify-content-end gap-3">
                                <button type="button" onclick="clearCart()" class="btn btn-light text-danger p-3 rounded-circle" title="Clear All">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                                <div class="text-end me-4">
                                    <div class="total-labels">Final Amount</div>
                                    <div class="h1 fw-900 text-dark m-0" id="sumGrandTotal">Rs. 0</div>
                                </div>
                                <button type="submit" class="btn btn-premium-confirm px-5 py-3">
                                    CONFIRM 
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<!-- Sale Success Modal -->
<?php if(session()->getFlashdata('last_sale_id')): ?>
<div class="modal fade show" id="successModal" tabindex="-1" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px);">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-5 overflow-hidden">
            <div class="modal-body p-5 text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h2 class="fw-900 mb-2">Sale Successful!</h2>
                <p class="text-muted mb-4">Invoice #<?= esc(session()->getFlashdata('last_sale_invoice') ?: 'Generated') ?> has been recorded.</p>
                
                <div class="d-grid gap-3">
                    <a href="<?= base_url('sales/invoice/' . session()->getFlashdata('last_sale_id')) ?>" target="_blank" class="btn btn-primary py-3 rounded-4 fw-900 fs-5 shadow-sm">
                        <i class="fas fa-print me-2"></i> PRINT INVOICE
                    </a>
                    <button type="button" class="btn btn-light py-3 rounded-4 fw-800" onclick="document.getElementById('successModal').style.display='none'">
                        CONTINUE TO POS
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
    let cart = [];

    // Keyboard Shortcuts
    document.addEventListener('keydown', function(e) {
        // F1: Focus Search
        if(e.key === 'F1') {
            e.preventDefault();
            document.getElementById('productSearch').focus();
        }
        // F2: Confirm Sale
        if(e.key === 'F2') {
            e.preventDefault();
            if(cart.length > 0) document.getElementById('posForm').submit();
        }
        // ESC: Clear Search
        if(e.key === 'Escape') {
            if(document.activeElement.id === 'productSearch') {
                document.getElementById('productSearch').value = '';
                searchPanel();
            }
        }
        // F4: Add Empty Row
        if(e.key === 'F4') {
            e.preventDefault();
            addEmptyRow();
        }
    });

    function searchPanel() {
        const query = document.getElementById('productSearch').value.toUpperCase();
        const nodes = document.querySelectorAll('.item-select-node');
        nodes.forEach(node => {
            const txt = (node.dataset.name + ' ' + node.dataset.batch).toUpperCase();
            node.style.display = txt.includes(query) ? '' : 'none';
        });
    }

    function addToCart(item) {
        // Remove empty row
        const empty = document.getElementById('emptyCartRow');
        if(empty) empty.remove();

        // Check if item already exists in cart (by stock_id)
        let existing = cart.find(c => c.id === item.id);
        if(existing) {
            existing.qty++;
            renderCart();
            // Optional: Visual pulse for existing item
            const row = document.querySelector(`tr[data-id="${item.id}"]`);
            if(row) {
                row.classList.add('bg-primary', 'bg-opacity-10');
                setTimeout(() => row.classList.remove('bg-primary', 'bg-opacity-10'), 300);
            }
            return;
        }

        // Add new item
        cart.push({
            id: item.id,
            name: item.product_name,
            strength: item.product_unit_value + ' ' + item.unit,
            price: parseFloat(item.price),
            qty: 1,
            max: parseInt(item.available_qty),
            discount: 0,
            disc_type: 'flat' // flat or percent
        });

        renderCart();
    }

    function clearCart() {
        if(confirm('Are you sure you want to clear the entire cart?')) {
            cart = [];
            renderCart();
        }
    }

    function removeRow(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function updateItem(index, field, value) {
        const item = cart[index];
        if(field === 'qty') {
            let q = parseInt(value) || 0;
            if(q > item.max) q = item.max;
            if(q < 1) q = 1;
            item.qty = q;
        } else if(field === 'price') {
            item.price = parseFloat(value) || 0;
        } else if(field === 'strength') {
            item.strength = value;
        } else if(field === 'discount') {
            item.discount = parseFloat(value) || 0;
        } else if(field === 'disc_type') {
            item.disc_type = value;
        }
        renderCart();
    }

    // List of all products for manual search
    const allProducts = <?= json_encode($all_products) ?>;

    function addEmptyRow() {
        cart.push({
            id: 0,
            name: '',
            strength: '',
            price: 0,
            qty: 1,
            max: 9999,
            discount: 0,
            disc_type: 'flat',
            is_manual: true
        });
        renderCart();
        
        // Focus the newly added row's product selector
        setTimeout(() => {
            const selects = document.querySelectorAll('.cart-product-search');
            if(selects.length > 0) selects[selects.length - 1].focus();
        }, 100);
    }

    function selectManualProduct(index, productId) {
        const product = allProducts.find(p => p.product_id == productId);
        if(product) {
            cart[index] = {
                id: product.stock_id || 0,
                name: product.product_name,
                strength: product.unit_value + ' ' + product.unit,
                price: parseFloat(product.last_price || 0),
                qty: 1,
                max: 9999,
                discount: 0,
                disc_type: 'flat'
            };
            renderCart();
        }
    }

    function renderCart() {
        const body = document.getElementById('cartBody');
        if(cart.length === 0) {
            body.innerHTML = `<tr><td colspan="6" class="text-center py-5 text-muted small"><i class="fas fa-arrow-up me-2"></i>Click "ADD NEW ITEM" to start or select from left</td></tr>`;
            updateTotals();
            return;
        }

        let html = '';
        cart.forEach((item, i) => {
            if(item.is_manual && !item.name) {
                // Render an empty row with a product selector
                html += `
                    <tr class="cart-item-row" style="background: #fffcf0;">
                        <td class="px-4">
                            <select class="form-select border-primary border-2 cart-product-search" onchange="selectManualProduct(${i}, this.value)">
                                <option value="">--- Search Product ---</option>
                                ${allProducts.map(p => `<option value="${p.product_id}">${p.product_name} (${p.unit_value} ${p.unit})</option>`).join('')}
                            </select>
                        </td>
                        <td colspan="4" class="text-muted small italic">Select a product to fill details</td>
                        <td class="text-end px-4">
                            <button type="button" onclick="removeRow(${i})" class="btn btn-light text-danger btn-sm rounded-circle"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                `;
            } else {
                html += `
                    <tr class="cart-item-row animate-wow" data-id="${item.id}" style="transition: background 0.3s ease;">
                        <td class="px-4">
                            <input type="hidden" name="stock_id[]" value="${item.id}">
                            <div class="fw-900 text-dark">${item.name}</div>
                            <div class="text-muted extra-small fw-bold">ID: #${item.id}</div>
                        </td>
                        <td>
                            <input type="text" name="strength[]" class="input-cart bg-light border-0 py-2" value="${item.strength}" onchange="updateItem(${i}, 'strength', this.value)">
                        </td>
                        <td>
                            <input type="number" step="0.01" name="price[]" class="input-cart bg-light border-0 py-2" value="${item.price}" onchange="updateItem(${i}, 'price', this.value)">
                        </td>
                        <td>
                            <input type="number" name="qty[]" class="input-cart bg-white border-primary border-2 py-2" value="${item.qty}" min="1" max="${item.max}" onchange="updateItem(${i}, 'qty', this.value)">
                        </td>
                        <td>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control border-light shadow-sm fw-bold" style="min-width: 80px;" value="${item.discount}" onchange="updateItem(${i}, 'discount', this.value)">
                                <select class="form-select border-light shadow-sm fw-bold" style="max-width: 80px;" onchange="updateItem(${i}, 'disc_type', this.value)">
                                    <option value="flat" ${item.disc_type === 'flat' ? 'selected' : ''}>Rs.</option>
                                    <option value="percent" ${item.disc_type === 'percent' ? 'selected' : ''}>%</option>
                                </select>
                            </div>
                            <input type="hidden" name="discount[]" value="${calculateLineDiscount(item)}">
                        </td>
                        <td class="text-end px-4">
                            <button type="button" onclick="removeRow(${i})" class="btn btn-light text-danger btn-sm rounded-circle"><i class="fas fa-times"></i></button>
                        </td>
                    </tr>
                `;
            }
        });
        body.innerHTML = html;
        updateTotals();
    }

    function calculateLineDiscount(item) {
        let lineTotal = item.price * item.qty;
        let d = 0;
        if(item.disc_type === 'percent') {
            d = (lineTotal * item.discount) / 100;
        } else {
            d = item.discount;
        }
        return Math.min(d, lineTotal).toFixed(2);
    }

    function updateTotals() {
        let subtotal = 0;
        let discount = 0;
        
        cart.forEach(item => {
            let lineSub = item.price * item.qty;
            subtotal += lineSub;
            discount += parseFloat(calculateLineDiscount(item));
        });

        const grand = subtotal - discount;
        
        document.getElementById('sumSubtotal').innerText = 'Rs. ' + subtotal.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('sumDiscount').innerText = '-Rs. ' + discount.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('sumGrandTotal').innerText = 'Rs. ' + grand.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('topItemCount').innerText = cart.length;
    }

    // Auto-focus search on start & Auto-Invoice Popup
    window.onload = () => {
        document.getElementById('productSearch').focus();

        <?php if(session()->getFlashdata('last_sale_id')): ?>
            // Open invoice in new window
            const url = '<?= base_url("sales/invoice/" . session()->getFlashdata("last_sale_id")) ?>';
            window.open(url, '_blank', 'width=800,height=900');
        <?php endif; ?>
    };
</script>

<?= $this->endSection() ?>

