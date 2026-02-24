<?= $this->extend('layout') ?>

<?= $this->section('content') ?>

<div class="row g-4 animate-wow">
    <div class="col-12">
        <div class="premium-table-card">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-800 m-0">Products Registry</h4>
                    <p class="text-muted small m-0">Manage and view all registered medicines.</p>
                </div>
                <div class="d-flex gap-3">
                    <div class="input-group" style="width: 300px;">
                        <span class="input-group-text border-0 bg-light rounded-start-pill px-3"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" id="productSearch" class="form-control border-0 bg-light rounded-end-pill py-2" placeholder="Search products..." onkeyup="filterProducts()">
                    </div>
                    <a href="<?= base_url('products/add') ?>" class="btn btn-vibrant rounded-pill px-4">
                        <i class="fas fa-plus-circle me-2"></i> Add New
                    </a>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="productsTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-5 border-0">Product Name</th>
                                <th class="border-0">Category</th>
                                <th class="border-0 text-center">Cost Price</th>
                                <th class="border-0">Form Details</th>
                                <th class="border-0 text-end px-5">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($products)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="fas fa-box-open fs-1 text-muted opacity-25 mb-3"></i>
                                        <p class="text-muted">No products found in the registry.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($products as $product): ?>
                                    <tr>
                                        <td class="px-5">
                                            <div class="fw-bold d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-3 p-2 me-3 small shadow-sm" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-capsules"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-800"><?= esc($product['name']) ?></div>
                                                    <div class="text-muted small"><?= esc($product['unit_value']) . esc($product['unit']) ?> Strength</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark p-2 px-3 fw-600 rounded-pill">
                                                <?= esc($product['category_name']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="fw-800 text-dark">Rs. <?= number_format($product['cost'], 2) ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2 ">
                                                <span class="badge bg-light text-dark border rounded-3">F6: <?= esc($product['form_6'] ?: '-') ?></span>
                                                <span class="badge bg-light text-dark border rounded-3">F7: <?= esc($product['form_7'] ?: '-') ?></span>
                                            </div>
                                        </td>
                                        <td class="text-end px-5">
                                            <button type="button" class="btn btn-sm btn-outline-warning border-0 rounded-pill px-3 me-1" 
                                                onclick="openEditProduct(<?= $product['id'] ?>, '<?= esc($product['name'], 'js') ?>', <?= $product['category_id'] ?>, '<?= esc($product['unit_value'], 'js') ?>', '<?= esc($product['unit'], 'js') ?>', <?= $product['cost'] ?>, '<?= esc($product['form_6'], 'js') ?>', '<?= esc($product['form_7'], 'js') ?>')"
                                                data-bs-toggle="modal" data-bs-target="#editProductModal">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <a href="<?= base_url('products/delete/'.$product['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Delete this product?')">
                                                <i class="fas fa-trash-alt"></i>
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
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2 text-warning"></i> Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('products/update') ?>" method="POST">
                <input type="hidden" name="id" id="edit_product_id">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Product Name</label>
                        <input type="text" class="form-control bg-light border-0" name="name" id="edit_product_name" required style="padding: 12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Category</label>
                        <select class="form-select bg-light border-0" name="category_id" id="edit_product_category" required style="padding: 12px;">
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= esc($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-7">
                            <label class="form-label fw-bold small">Strength</label>
                            <input type="text" class="form-control bg-light border-0" name="unit_value" id="edit_product_unit_value" required style="padding: 12px;">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold small">Unit</label>
                            <select class="form-select bg-light border-0" name="unit" id="edit_product_unit" required style="padding: 12px;">
                                <?php foreach(['mg','ml','gm','cap','tab','syp'] as $u): ?>
                                    <option value="<?= $u ?>"><?= $u ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Cost Price (Rs.)</label>
                        <input type="number" step="0.01" class="form-control bg-light border-0" name="cost" id="edit_product_cost" required style="padding: 12px;">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Form 6</label>
                            <input type="text" class="form-control bg-light border-0" name="form_6" id="edit_product_form_6" style="padding: 12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">Form 7</label>
                            <input type="text" class="form-control bg-light border-0" name="form_7" id="edit_product_form_7" style="padding: 12px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-white rounded-4">
                        <i class="fas fa-save me-2"></i> Save Changes
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
        let td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            let txtValue = td.textContent || td.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

<?= $this->endSection() ?>
