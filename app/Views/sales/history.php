<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Sales History</h5>
                    <p class="text-muted small m-0 mt-1">Audit log of all successful sales transactions.</p>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">ID & Date</th>
                                <th class="border-0 py-3">Batch Info</th>
                                <th class="border-0 py-3">Product Sold</th>
                                <th class="border-0 py-3">Customer</th>
                                <th class="border-0 py-3 text-center">Qty</th>
                                <th class="border-0 py-3 text-end">Sale Amount</th>
                                <th class="border-0 py-3 text-end px-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($sales)): ?>
                                <tr><td colspan="7" class="text-center py-5 text-muted">No sales found.</td></tr>
                            <?php else: ?>
                                <?php foreach($sales as $sale): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-800 text-primary">S-<?= str_pad($sale['id'], 5, '0', STR_PAD_LEFT) ?></div>
                                            <div class="text-muted small"><?= date('d M, Y h:i A', strtotime($sale['sale_date'])) ?></div>
                                        </td>
                                        <td>
                                            <div class="badge bg-light text-dark border"><i class="fas fa-barcode me-1"></i><?= esc($sale['batch_id']) ?></div>
                                        </td>
                                        <td class="fw-bold">
                                            <?= esc($sale['product_name']) ?>
                                            <div class="text-muted small fw-normal"><?= esc($sale['product_unit_value']) ?> <?= esc($sale['product_unit']) ?></div>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= esc($sale['customer_name'] ?: 'Guest') ?></div>
                                            <div class="text-muted small"><?= esc($sale['customer_phone'] ?: '-') ?></div>
                                        </td>
                                        <td class="text-center fw-bold fs-5"><?= number_format($sale['qty']) ?></td>
                                        <td class="text-end fw-bold">Rs. <?= number_format($sale['qty'] * $sale['sale_price'], 2) ?></td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('sales/invoice/'.$sale['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('sales/void/'.$sale['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Void this sale?')">
                                                <i class="fas fa-trash"></i>
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
<?= $this->endSection() ?>
