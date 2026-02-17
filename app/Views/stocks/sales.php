<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="premium-list p-0 overflow-hidden" style="background: white;">
            <div class="p-5 d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #1e1b4b 0%, #312e81 100%); color: white;">
                <div>
                    <h2 class="fw-800 m-0">RETAIL TERMINAL</h2>
                    <p class="opacity-75 m-0 mt-1">Direct dispensing engine. Security encryption active.</p>
                </div>
                <div class="text-end">
                    <div class="h5 m-0 fw-bold"><i class="fas fa-barcode me-2"></i>POS-SECURE-843</div>
                    <small class="opacity-50">Local time sync active</small>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-5 d-flex gap-3">
                    <div class="flex-grow-1">
                        <input type="text" class="form-control form-control-lg bg-light border-0 rounded-4 px-4 shadow-sm" placeholder="Scan barcode or type drug name...">
                    </div>
                    <button class="btn btn-dark rounded-4 px-4 shadow-sm"><i class="fas fa-search"></i></button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="border-0 pb-4 px-0">Batch Reference</th>
                                <th class="border-0 pb-4">Nomenclature</th>
                                <th class="border-0 pb-4">Stability Log (EXP)</th>
                                <th class="border-0 pb-4 text-center">In-Stock units</th>
                                <th class="border-0 pb-4">Retail Point</th>
                                <th class="border-0 pb-4 text-end">Checkout</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($stocks)): ?>
                                <tr><td colspan="6" class="text-center py-5">No inventory matches your search criteria.</td></tr>
                            <?php else: ?>
                                <?php foreach($stocks as $stock): ?>
                                    <tr>
                                        <td class="px-0"><span class="badge bg-light text-dark border p-2 px-3 rounded-3"><?= esc($stock['batch_id']) ?></span></td>
                                        <td class="fw-800 fs-5"><?= esc($stock['product_name']) ?></td>
                                        <td>
                                            <?php 
                                            $expDate = strtotime($stock['expiry_date']);
                                            $days = floor(($expDate - time()) / (60 * 60 * 24));
                                            if ($days < 30) {
                                                echo '<span class="text-danger fw-bold"><i class="fas fa-triangle-exclamation me-1"></i> '.date('M Y', $expDate).'</span>';
                                            } else {
                                                echo '<span class="text-success fw-bold"><i class="fas fa-circle-check me-1"></i> '.date('M Y', $expDate).'</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold fs-4"><?= esc($stock['qty']) ?></div>
                                            <small class="text-muted">AVAILABLE</small>
                                        </td>
                                        <td class="fw-800 text-indigo fs-5">$<?= number_format($stock['price'], 2) ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-vibrant rounded-4 p-3 px-4 shadow-none" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#saleModal" 
                                                    onclick="setupSale(<?= $stock['id'] ?>, '<?= esc($stock['product_name']) ?>', <?= $stock['price'] ?>, <?= $stock['qty'] ?>)">
                                                <i class="fas fa-cart-plus me-2"></i> Dispense
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

<!-- Transaction Modal -->
<div class="modal fade" id="saleModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="p-5" style="background: #1e293b; color: white;">
                <h4 class="fw-800 m-0">Checkout Confirmation</h4>
                <p class="text-white-50 m-0 mt-2 small">Finalizing retail transaction for customer.</p>
            </div>
            <form action="<?= base_url('stocks/process_sale') ?>" method="POST">
                <input type="hidden" name="stock_id" id="modal_stock_id">
                <div class="modal-body p-5">
                    <div class="mb-5">
                        <small class="text-muted text-uppercase fw-bold">Selected Formulation</small>
                        <h2 class="fw-900 text-primary mb-1 m-0" id="modal_product_name">Product Name</h2>
                        <div class="badge bg-light text-dark border" id="modal_unit_price">Price: $0.00</div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label fw-bold small text-muted">DISPENSE QUANTITY (MAX: <span id="modal_max_qty">0</span>)</label>
                        <input type="number" class="form-control form-control-lg fs-3 fw-800 bg-light border-0 py-3 text-center" name="qty" id="sale_qty" min="1" required oninput="calcTotal()">
                    </div>

                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 2px dashed #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Sub-Total Value</span>
                            <span class="fw-bold" id="sub_total_disp">$0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold h5 m-0">Total Payable</span>
                            <span class="fw-900 h2 m-0 text-primary" id="modal_total_amount">$0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-5 shadow-lg">
                        <i class="fas fa-lock me-2"></i> AUTHORIZE & PRINT
                    </button>
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
        document.getElementById('modal_unit_price').innerText = 'Price: $' + price.toFixed(2);
        document.getElementById('modal_max_qty').innerText = max;
        document.getElementById('sale_qty').max = max;
        document.getElementById('sale_qty').value = 1;
        currentPrice = price;
        calcTotal();
    }

    function calcTotal() {
        const qty = document.getElementById('sale_qty').value;
        const total = qty * currentPrice;
        document.getElementById('modal_total_amount').innerText = '$' + total.toFixed(2);
        document.getElementById('sub_total_disp').innerText = '$' + total.toFixed(2);
    }
</script>

<?= $this->endSection() ?>
