<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="premium-list p-0 overflow-hidden">
            <div class="p-5 d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #0f172a 0%, #0ea5e9 100%); color: white;">
                <div>
                    <h2 class="fw-800 m-0">SALES TERMINAL</h2>
                    <p class="opacity-75 m-0 mt-1">Process medicine sales from available stock.</p>
                </div>
                <div class="text-end">
                    <div class="h5 m-0 fw-bold"><i class="fas fa-cash-register me-2"></i>POS SYSTEM</div>
                    <small class="opacity-50">Galaxy Pharmacy</small>
                </div>
            </div>

            <div class="p-5">
                <div class="mb-5 d-flex gap-3">
                    <div class="flex-grow-1">
                        <input type="text" id="searchInput" class="form-control form-control-lg bg-light border-0 rounded-4 px-4 shadow-sm" placeholder="Search medicine name..." onkeyup="filterTable()">
                    </div>
                    <button class="btn btn-dark rounded-4 px-4 shadow-sm"><i class="fas fa-search"></i></button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="border-0 pb-4 px-0">Batch ID</th>
                                <th class="border-0 pb-4">Product Name</th>
                                <th class="border-0 pb-4">Expiry Date</th>
                                <th class="border-0 pb-4 text-center">In Stock</th>
                                <th class="border-0 pb-4">Price</th>
                                <th class="border-0 pb-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($stocks)): ?>
                                <tr><td colspan="6" class="text-center py-5">No stock available.</td></tr>
                            <?php else: ?>
                                <?php foreach($stocks as $stock): ?>
                                    <tr>
                                        <td class="px-0">
                                            <span class="badge bg-light text-dark border p-2 px-3 rounded-3 mb-1 d-block"><?= esc($stock['batch_id']) ?></span>
                                            <div class="text-muted" style="font-size: 0.7rem;"><i class="fas fa-truck me-1"></i><?= esc($stock['vendor_name'] ?: 'Local') ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-800 fs-5"><?= esc($stock['product_name']) ?></div>
                                            <div class="text-muted small"><?= esc($stock['product_unit_value']) ?> <?= esc($stock['product_unit']) ?></div>
                                        </td>
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
                                        <td class="fw-800 text-indigo fs-5">Rs. <?= number_format($stock['price'], 2) ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-vibrant rounded-4 p-3 px-4 shadow-none" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#saleModal" 
                                                    onclick="setupSale(<?= $stock['id'] ?>, '<?= esc($stock['product_name']) ?> [<?= esc($stock['product_unit_value']) ?> <?= esc($stock['product_unit']) ?>]', <?= $stock['price'] ?>, <?= $stock['qty'] ?>)">
                                                <i class="fas fa-cart-plus me-2"></i> Sell
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
                <h4 class="fw-800 m-0">Confirm Sale</h4>
                <p class="text-white-50 m-0 mt-2 small">Enter the quantity to sell.</p>
            </div>
            <form action="<?= base_url('stocks/process_sale') ?>" method="POST">
                <input type="hidden" name="stock_id" id="modal_stock_id">
                <div class="modal-body p-5">
                    <div class="mb-5">
                        <small class="text-muted text-uppercase fw-bold">Selected Product</small>
                        <h2 class="fw-900 text-primary mb-1 m-0" id="modal_product_name">Product Name</h2>
                        <div class="badge bg-light text-dark border" id="modal_unit_price">Price: Rs. 0.00</div>
                    </div>
                    
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Customer Name (Optional)</label>
                            <input type="text" class="form-control" name="customer_name" placeholder="e.g. Ali Khan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Phone Number</label>
                            <input type="text" class="form-control" name="customer_phone" placeholder="0300...">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label fw-bold small text-muted">QUANTITY (MAX: <span id="modal_max_qty">0</span>)</label>
                        <input type="number" class="form-control form-control-lg fs-3 fw-800 bg-light border-0 py-3 text-center" name="qty" id="sale_qty" min="1" required oninput="calcTotal()">
                    </div>

                    <div class="p-4 rounded-4" style="background: #f8fafc; border: 2px dashed #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Sub-Total Value</span>
                            <span class="fw-bold" id="sub_total_disp">Rs. 0.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold h5 m-0">Total Payable</span>
                            <span class="fw-900 h2 m-0 text-primary" id="modal_total_amount">Rs. 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-vibrant w-100 py-4 fs-5 shadow-lg">
                        <i class="fas fa-check me-2"></i> Confirm Sale
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
        document.getElementById('modal_unit_price').innerText = 'Price: Rs. ' + price.toFixed(2);
        document.getElementById('modal_max_qty').innerText = max;
        document.getElementById('sale_qty').max = max;
        document.getElementById('sale_qty').value = 1;
        currentPrice = price;
        calcTotal();
    }

    function calcTotal() {
        const qty = document.getElementById('sale_qty').value;
        const total = qty * currentPrice;
        document.getElementById('modal_total_amount').innerText = 'Rs. ' + total.toFixed(2);
        document.getElementById('sub_total_disp').innerText = 'Rs. ' + total.toFixed(2);
    }

    function filterTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toUpperCase();
        const table = document.querySelector('table tbody');
        const tr = table.getElementsByTagName('tr');

        for (let i = 0; i < tr.length; i++) {
            const td = tr[i].getElementsByTagName('td')[1]; // Product Name column
            if (td) {
                const txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
</script>

<?= $this->endSection() ?>
