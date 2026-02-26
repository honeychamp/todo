<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .inventory-header {
        background: white;
        border-radius: 40px;
        padding: 40px;
        margin-bottom: 35px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        border: 1px solid rgba(0,0,0,0.03);
    }
    .search-pill {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 100px;
        padding: 12px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        width: 100%;
        max-width: 450px;
        transition: all 0.3s;
    }
    .search-pill:focus-within {
        background: white;
        border-color: #3b82f6;
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.1);
    }
    .search-pill input {
        border: none;
        background: transparent;
        outline: none;
        width: 100%;
        font-weight: 500;
        color: #1e293b;
    }

    .premium-data-card {
        background: white;
        border-radius: 40px;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
    }

    .table thead th {
        background: #f8fafc;
        padding: 22px 30px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
        border-bottom: 1px solid #f1f5f9;
    }
    .table tbody td {
        padding: 22px 30px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .status-chip {
        padding: 8px 18px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    .chip-success { background: #ecfdf5; color: #10b981; }
    .chip-warning { background: #fff7ed; color: #f59e0b; }
    .chip-danger { background: #fef2f2; color: #ef4444; }

    .action-btn {
        width: 42px; height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        border: none;
        background: #f8fafc;
        color: #64748b;
    }
    .action-edit:hover { background: #fff7ed; color: #f59e0b; transform: rotate(15deg); }
    .action-delete:hover { background: #fef2f2; color: #ef4444; transform: rotate(-15deg); }
</style>

<div class="animate-wow">
    <!-- Header -->
    <div class="inventory-header d-flex flex-column flex-lg-row justify-content-between align-items-center gap-4">
        <div>
            <h2 class="fw-900 m-0">Products Registry</h2>
            <p class="text-muted m-0 mt-1">Full database of Galaxy Pharmacy stock SKU items.</p>
        </div>
        <div class="d-flex flex-column flex-md-row gap-3 w-100 w-lg-auto align-items-center">
            <div class="search-pill">
                <i class="fas fa-search text-muted opacity-50"></i>
                <input type="text" id="productSearch" placeholder="Search by name, category or strength..." onkeyup="filterProducts()">
            </div>
            <a href="<?= base_url('products/add') ?>" class="btn btn-primary rounded-pill px-5 py-3 fw-900 shadow-lg text-nowrap">
                <i class="fas fa-plus-circle me-2"></i> ADD NEW PRODUCT
            </a>
        </div>
    </div>

    <!-- Data Table -->
    <div class="premium-data-card animate-up">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="productsTable">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Classification</th>
                        <th class="text-center">Inventory Level</th>
                        <th class="text-center">Financial Performance</th>
                        <th>Compliance Form</th>
                        <th class="text-end">Management</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($products)): ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted fw-bold">No products registered yet.</td></tr>
                    <?php else: ?>
                        <?php foreach($products as $product): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-4 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-900" style="width: 50px; height: 50px;">
                                            <?= substr($product['name'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <div class="fw-900 text-dark"><?= esc($product['name']) ?></div>
                                            <div class="text-muted small fw-bold">
                                                <?= esc($product['unit_value']) ?><?= esc($product['unit']) ?> · Cost: Rs. <?= number_format($product['cost'], 0) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-800 small">
                                        <?= esc($product['category_name']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php 
                                        $stock = $product['current_stock'] ?? 0;
                                        if ($stock <= 0) {
                                            echo '<span class="status-chip chip-danger">Stock Out</span>';
                                        } elseif ($stock < 10) {
                                            echo '<span class="status-chip chip-warning">Low: '.$stock.'</span>';
                                        } else {
                                            echo '<span class="status-chip chip-success">'.$stock.' Units</span>';
                                        }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <div class="fw-900 text-dark"><?= number_format($product['total_sold_units']) ?> SOLD</div>
                                    <div class="text-success small fw-900">Rs. <?= number_format($product['total_revenue'], 0) ?></div>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <div class="bg-light px-2 py-1 rounded-2 border extra-small fw-900">F6: <?= esc($product['form_6'] ?: 'N/A') ?></div>
                                        <div class="bg-light px-2 py-1 rounded-2 border extra-small fw-900">F7: <?= esc($product['form_7'] ?: 'N/A') ?></div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <button onclick="openEditProduct(<?= $product['id'] ?>, '<?= esc($product['name'], 'js') ?>', <?= $product['category_id'] ?>, '<?= esc($product['unit_value'], 'js') ?>', '<?= esc($product['unit'], 'js') ?>', <?= $product['cost'] ?>, '<?= esc($product['form_6'], 'js') ?>', '<?= esc($product['form_7'], 'js') ?>')"
                                            data-bs-toggle="modal" data-bs-target="#editProductModal"
                                            class="action-btn action-edit me-2 shadow-sm">
                                        <i class="fas fa-pen-nib"></i>
                                    </button>
                                    <a href="<?= base_url('products/delete/'.$product['id']) ?>" 
                                       onclick="return confirm('Archive this product SKU?')"
                                       class="action-btn action-delete shadow-sm">
                                        <i class="fas fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl overflow-hidden" style="border-radius: 40px;">
            <div class="modal-header bg-dark text-white border-0 p-5 pb-4">
                <h4 class="modal-title fw-900"><i class="fas fa-edit text-warning me-2"></i> Edit Product SKU</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('products/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_product_id">
                <div class="modal-body p-5">
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">General Product Name</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0 px-4 py-3" name="name" id="edit_product_name" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Medical Category</label>
                        <select class="form-select form-select-lg bg-light border-0 px-4 py-3" name="category_id" id="edit_product_category" required>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-8">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Potency / Strength</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0 px-4 py-3" name="unit_value" id="edit_product_unit_value" required>
                        </div>
                        <div class="col-4">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Unit Type</label>
                            <select class="form-select form-select-lg bg-light border-0 px-4 py-3" name="unit" id="edit_product_unit" required>
                                <?php foreach(['mg','ml','gm','cap','tab','syp'] as $u): ?>
                                    <option value="<?= $u ?>"><?= $u ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Cost Basis (Rs.)</label>
                        <input type="number" step="0.01" class="form-control form-control-lg bg-light border-0 px-4 py-3" name="cost" id="edit_product_cost" required>
                    </div>
                    <div class="row g-4">
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Form 6</label>
                            <input type="text" class="form-control bg-light border-0 px-4 py-3" name="form_6" id="edit_product_form_6">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Form 7</label>
                            <input type="text" class="form-control bg-light border-0 px-4 py-3" name="form_7" id="edit_product_form_7">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-5 pt-0">
                    <button type="submit" class="btn btn-dark w-100 py-3 fw-900 rounded-pill shadow-lg">
                        <i class="fas fa-save me-2"></i> UPDATE REGISTRY
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditProduct(id, name, catId, unitVal, unit, cost, f6, f7) {
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_product_category').value = catId;
    document.getElementById('edit_product_unit_value').value = unitVal;
    document.getElementById('edit_product_unit').value = unit;
    document.getElementById('edit_product_cost').value = cost;
    document.getElementById('edit_product_form_6').value = f6;
    document.getElementById('edit_product_form_7').value = f7;
}

function filterProducts() {
    let input = document.getElementById("productSearch");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("productsTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let isMatch = false;
        let tds = tr[i].getElementsByTagName("td");
        for(let j=0; j<2; j++) { // Search in name and category
            if(tds[j]) {
                if(tds[j].textContent.toLowerCase().indexOf(filter) > -1) isMatch = true;
            }
        }
        tr[i].style.display = isMatch ? "" : "none";
    }
}
</script>

<?= $this->endSection() ?>
