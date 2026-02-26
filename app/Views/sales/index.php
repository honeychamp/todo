<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 mb-4 animate-wow">
    <div class="col-xl-3 col-md-6">
        <div class="premium-list p-4 bg-dark text-white border-0 shadow-sm" style="background: #0f172a !important;">
            <div class="text-white-50 small fw-bold text-uppercase">Items Available</div>
            <div class="d-flex align-items-center gap-3 mt-2">
                <i class="fas fa-boxes-stacked fs-1 text-primary"></i>
                <h1 class="fw-900 m-0"><?= number_format(count($stocks)) ?></h1>
            </div>
            <div class="small text-white-50 mt-2">Active batches for sale</div>
        </div>
    </div>
    <div class="col-xl-9 col-md-6">
        <div class="premium-list p-4 bg-white border-0 shadow-sm d-flex align-items-center justify-content-between h-100">
            <div>
                <h5 class="fw-800 m-0 text-dark">Live Stock Audit</h5>
                <p class="text-muted small m-0 mt-1">Real-time inventory data directly from your purchase logs.</p>
            </div>
            <div class="d-flex gap-4">
                <div class="text-center">
                    <div class="text-muted extra-small fw-bold">LOW STOCK</div>
                    <div class="h4 fw-900 m-0 text-danger"><?= count(array_filter($stocks, fn($s) => $s['available_qty'] < 10)) ?></div>
                </div>
                <div class="text-center border-start ps-4">
                    <div class="text-muted extra-small fw-bold">EXPIRING SOON</div>
                    <div class="h4 fw-900 m-0 text-warning"><?= count(array_filter($stocks, fn($s) => (strtotime($s['expiry_date']) - time()) < (30 * 24 * 60 * 60))) ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0 overflow-hidden shadow-lg border-0 bg-white">
            <div class="p-5 border-bottom d-flex justify-content-between align-items-center bg-light bg-opacity-50">
                <div class="flex-grow-1">
                    <label class="form-label fw-800 small text-uppercase text-primary mb-2">Inventory Search</label>
                    <div class="input-group input-group-lg" style="max-width: 600px;">
                        <span class="input-group-text border-0 bg-white px-4 shadow-sm"><i class="fas fa-magnifying-glass text-muted"></i></span>
                        <input type="text" id="searchInput" class="form-control border-0 bg-white px-3 shadow-sm rounded-end-4" placeholder="Scan barcode or type medicine name..." onkeyup="filterTable()" autofocus>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="badge bg-primary bg-opacity-10 text-primary p-3 rounded-4 border-0">
                        <i class="fas fa-cash-register me-2"></i> POS TERMINAL ACTIVE
                    </div>
                </div>
            </div>

            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted extra-small text-uppercase">
                                <th class="border-0 py-4 px-5">Batch & Supplier</th>
                                <th class="border-0 py-4">Product Specification</th>
                                <th class="border-0 py-4 text-center">Expiry Status</th>
                                <th class="border-0 py-4 text-center">Stock Level</th>
                                <th class="border-0 py-4 text-center">Unit Price</th>
                                <th class="border-0 py-4 text-end px-5">Settle Sale</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($stocks)): ?>
                                <tr><td colspan="6" class="text-center py-5">
                                    <div class="opacity-25 py-5">
                                        <i class="fas fa-box-open fs-1 mb-3"></i>
                                        <p class="m-0 h5">Zero Stock Available</p>
                                        <small>Please add new inventory via the Purchase module.</small>
                                    </div>
                                </td></tr>
                            <?php else: ?>
                                <?php foreach($stocks as $stock): ?>
                                    <tr class="stock-row">
                                        <td class="px-5">
                                            <div class="fw-bold mb-1 text-dark">#<?= esc($stock['batch_id']) ?></div>
                                            <div class="text-muted extra-small text-uppercase fw-bold"><i class="fas fa-truck me-1"></i><?= esc($stock['vendor_name'] ?: 'Internal Purchase') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-900 fs-5 text-dark"><?= esc($stock['product_name']) ?></div>
                                            <div class="text-muted small fw-bold"><?= esc($stock['unit_value']) ?> <?= esc($stock['unit']) ?></div>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                            $expDate = strtotime($stock['expiry_date']);
                                            $days = floor(($expDate - time()) / (60 * 60 * 24));
                                            if ($days < 30) : ?>
                                                <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill small">
                                                    <i class="fas fa-clock-rotate-left me-1"></i> <?= date('M Y', $expDate) ?>
                                                </div>
                                            <?php elseif ($days < 90) : ?>
                                                <div class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-3 py-2 rounded-pill small">
                                                    <i class="fas fa-triangle-exclamation me-1"></i> <?= date('M Y', $expDate) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 rounded-pill small">
                                                    <i class="fas fa-circle-check me-1"></i> <?= date('M Y', $expDate) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-900 fs-4 <?= $stock['available_qty'] < 10 ? 'text-danger' : 'text-dark' ?>"><?= esc($stock['available_qty']) ?></div>
                                            <div class="extra-small text-uppercase text-muted fw-bold">UNITS LEFT</div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-900 h4 m-0 text-primary">Rs. <?= number_format($stock['price'], 2) ?></div>
                                        </td>
                                        <td class="text-end px-5">
                                            <button class="btn btn-dark rounded-4 p-3 px-4 shadow-sm hover-lift" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#saleModal" 
                                                    onclick="setupSale(<?= $stock['id'] ?>, '<?= esc($stock['product_name'], 'js') ?> [<?= esc($stock['unit_value'], 'js') ?> <?= esc($stock['unit'], 'js') ?>]', <?= $stock['price'] ?>, <?= $stock['available_qty'] ?>)">
                                                <i class="fas fa-cart-plus me-2"></i> SELL NOW
                                            </button>
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
</div>

<!-- Transaction Modal (Redesigned) -->
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="p-5" style="background: #0f172a; color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="fw-900 m-0">Customer Invoice</h3>
                        <p class="text-white-50 m-0 mt-2 small">Processing unit sale from batch inventory.</p>
                    </div>
                    <i class="fas fa-receipt fa-3x opacity-25"></i>
                </div>
            </div>
            <form action="<?= base_url('sales/process') ?>" method="POST">
                <input type="hidden" name="stock_id" id="modal_stock_id">
                <div class="modal-body p-5">
                    <div class="mb-5 d-flex align-items-center gap-3">
                        <div class="avatar-lg bg-primary rounded-4 text-white p-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="fas fa-prescription-bottle-pill fs-2"></i>
                        </div>
                        <div>
                            <div class="text-muted extra-small fw-bold text-uppercase">Active Product</div>
                            <h2 class="fw-900 text-dark mb-1 m-0" id="modal_product_name">Product Name</h2>
                            <div class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill" id="modal_unit_price">Price: Rs. 0.00</div>
                        </div>
                    </div>
                    
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Buyer Name</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 py-3" name="customer_name" placeholder="Walk-in Customer">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted text-uppercase">Contact Num</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 py-3" name="customer_phone" placeholder="03XXXXXXXXX">
                        </div>
                    </div>

                    <div class="row align-items-center mb-5">
                        <div class="col-7">
                            <label class="form-label fw-bold small text-muted text-uppercase">Quantity to Sell</label>
                            <input type="number" class="form-control form-control-lg fs-2 fw-900 bg-white border-bottom border-primary border-4 rounded-0 shadow-none py-3" name="qty" id="sale_qty" min="1" required oninput="calcTotal()" value="1">
                        </div>
                        <div class="col-5">
                            <div class="text-center p-3 rounded-4 bg-light">
                                <span class="text-muted extra-small d-block fw-bold">STOCK MAX</span>
                                <span class="fw-900 h4 m-0" id="modal_max_qty">0</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 rounded-5" style="background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted fw-bold">SUB-TOTAL</span>
                            <span class="fw-bold fs-5" id="sub_total_disp">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top border-dark border-opacity-10 pt-3">
                            <span class="fw-900 h5 m-0 text-dark">GRAND TOTAL</span>
                            <span class="fw-900 h1 m-0 text-primary" id="modal_total_amount">Rs. 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-4 rounded-5 shadow-lg fw-900">
                        <i class="fas fa-print me-2"></i> COMPLETE SALE
                    </button>
                    <p class="text-muted extra-small text-center w-100 mt-4"><i class="fas fa-circle-info me-1"></i> Inventory balances will be adjusted automatically on completion.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPrice = 0;

    function setupSale(id, name, price, max) {
        document.getElementById('modal_stock_id').value = id;
        document.getElementById('modal_product_name').innerText = name;
        document.getElementById('modal_unit_price').innerText = 'Price: Rs. ' + price.toFixed(2);
        document.getElementById('modal_max_qty').innerText = max;
        document.getElementById('sale_qty').max = max;
        document.getElementById('sale_qty').value = 1;
        currentPrice = price;
        calcTotal();
    }

    function calcTotal() {
        const qtyBox = document.getElementById('sale_qty');
        const max = parseInt(qtyBox.max);
        let qty = parseInt(qtyBox.value) || 0;
        
        if(qty > max) {
            qty = max;
            qtyBox.value = max;
        }
        
        const total = qty * currentPrice;
        document.getElementById('modal_total_amount').innerText = 'Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
        document.getElementById('sub_total_disp').innerText = 'Rs. ' + total.toLocaleString(undefined, {minimumFractionDigits: 2});
    }

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const tr = document.querySelectorAll('.stock-row');

        tr.forEach(row => {
            const nameCell = row.querySelectorAll('td')[1];
            const batchCell = row.querySelectorAll('td')[0];
            const text = (nameCell.textContent + batchCell.textContent).toUpperCase();
            row.style.display = text.indexOf(filter) > -1 ? '' : 'none';
        });
    }
</script>

<?= $this->endSection() ?>
