<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<div class="row g-4 animate-up">
    <div class="col-12">
        <div class="premium-list p-0">
            <div class="p-4 px-5 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="m-0 fw-800">Sales History</h5>
                    <p class="text-muted small m-0 mt-1">List of all products you sold.</p>
                </div>
            </div>
            <div class="p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 px-4 py-3">Order ID & Date</th>
                                <th class="border-0 py-3">Batch</th>
                                <th class="border-0 py-3">Product Name</th>
                                <th class="border-0 py-3">Customer</th>
                                <th class="border-0 py-3 text-center">Qty</th>
                                <th class="border-0 py-3 text-end">Amount</th>
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
                                            <?php if(!empty($sale['doctor_name'])): ?>
                                                <div class="fw-900 text-primary"><i class="fas fa-user-md me-1"></i> <?= esc($sale['doctor_name']) ?></div>
                                                <div class="badge bg-primary bg-opacity-10 text-primary extra-small px-2">DOCTOR NETWORK</div>
                                            <?php else: ?>
                                                <div class="fw-bold"><?= esc($sale['customer_name'] ?: 'Retail Guest') ?></div>
                                                <div class="text-muted extra-small fw-bold"><?= esc($sale['customer_phone'] ?: 'CASH SALE') ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center fw-bold fs-5"><?= number_format($sale['qty']) ?></td>
                                        <td class="text-end fw-bold">
                                            <?php 
                                                $net_total = ($sale['qty'] * $sale['sale_price']) - $sale['discount'];
                                            ?>
                                            <div class="text-dark">Rs. <?= number_format($net_total, 2) ?></div>
                                            <?php if($sale['discount'] > 0): ?>
                                                <div class="text-danger extra-small" style="font-size: 0.7rem;">-Rs. <?= number_format($sale['discount'], 2) ?> Disc</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end px-4">
                                            <a href="<?= base_url('sales/invoice/'.$sale['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary border-0 rounded-pill px-3">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            <a href="<?= base_url('sales/void/'.$sale['id']) ?>" class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" onclick="return confirm('Cancel this sale?')">
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
