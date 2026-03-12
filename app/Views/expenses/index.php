<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<style>
    .cost-audit-header {
        background: white;
        border-radius: 45px;
        padding: 45px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .expense-entry-desk {
        background: white;
        border-radius: 40px;
        padding: 40px;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        height: 100%;
    }
    .expense-log-table {
        background: white;
        border-radius: 40px;
        padding: 0;
        border: 1px solid rgba(0,0,0,0.03);
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .expense-table .thead th {
        background: #f8fafc;
        padding: 22px 30px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 1px;
    }
    .expense-table .tbody td {
        padding: 22px 30px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .category-token-sm {
        padding: 4px 12px;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="animate-wow">
    <!-- Expenses Header -->
    <div class="cost-audit-header d-flex flex-column flex-md-row gap-4">
        <div>
            <h2 class="fw-900 m-0">Expenses</h2>
            <p class="text-muted m-0 mt-1">Track all your daily shop costs here.</p>
        </div>
        <div class="text-md-end d-flex flex-column align-items-md-end">
            <div class="text-muted extra-small fw-900 text-uppercase tracking-widest mb-1">Total Expenses</div>
            <div class="h1 fw-900 text-danger m-0">Rs. <?= number_format($total_expense, 0) ?></div>
            <?php if($start_date && $end_date): ?>
                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill mt-2 small fw-bold">Period: <?= $start_date ?> — <?= $end_date ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4 animate-up">
        <!-- Classification Desk (Entry) -->
        <div class="col-lg-4">
            <div class="expense-entry-desk">
                <div class="mb-5">
                    <div class="bg-danger text-white rounded-4 d-inline-flex p-3 mb-4 shadow-lg shadow-danger-subtle">
                        <i class="fas fa-wallet fs-4"></i>
                    </div>
                    <h4 class="fw-900 m-0">Add Expense</h4>
                    <p class="text-muted mt-2 small">Enter details for bills, rent, or salaries.</p>
                </div>

                <form action="<?= base_url('expenses/create') ?>" method="POST">
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Description</label>
                        <input type="text" name="title" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" placeholder="e.g. Petrol" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Amount (Rs.)</label>
                        <input type="number" step="0.01" name="amount" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" placeholder="0.00" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Expense Category</label>
                        <select name="category" class="form-select form-select-lg bg-light border-0 px-4 py-3 rounded-pill" required>
                            <option value="Utility">Electricity/Water/Gas</option>
                            <option value="Rent">Shop Rent</option>
                            <option value="Salary">Staff Salary</option>
                            <option value="Inventory">Shipping/Delivery</option>
                            <option value="Admin">Maintenance</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-5">
                        <label class="form-label fw-900 small text-muted text-uppercase tracking-widest">Transaction Date</label>
                        <input type="date" name="expense_date" class="form-control form-control-lg bg-light border-0 px-4 py-3 rounded-pill" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-dark w-100 py-3 rounded-pill fw-900 shadow-lg text-uppercase tracking-widest">
                        <i class="fas fa-plus-circle me-2"></i> ADD EXPENSE
                    </button>
                </form>
            </div>
        </div>

        <!-- Ledger View (Log) -->
        <div class="col-lg-8">
            <div class="expense-log-table">
                <div class="p-4 px-5 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 bg-white">
                    <div>
                        <h4 class="fw-900 m-0">Expense History</h4>
                        <p class="text-muted small m-0 mt-1">List of all recorded expenses.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="" method="get" class="d-flex gap-2">
                            <input type="date" name="start_date" class="form-control form-control-sm bg-light border-0 rounded-pill px-3" value="<?= $start_date ?>">
                            <input type="date" name="end_date" class="form-control form-control-sm bg-light border-0 rounded-pill px-3" value="<?= $end_date ?>">
                            <button type="submit" class="btn btn-sm btn-dark rounded-pill px-3"><i class="fas fa-filter"></i></button>
                            <a href="<?= base_url('expenses/export?start_date='.($start_date ?? '').'&end_date='.($end_date ?? '')) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-file-csv"></i> EXPORT
                            </a>
                            <?php if($start_date): ?>
                                <a href="<?= base_url('expenses') ?>" class="btn btn-sm btn-light rounded-pill px-3"><i class="fas fa-times"></i></a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 expense-table">
                        <thead class="bg-light thead">
                            <tr>
                                <th class="px-5">Date</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th class="text-end px-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            <?php if(empty($expenses)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted fw-bold">No expenses found.</td></tr>
                            <?php else: ?>
                                <?php foreach($expenses as $exp): ?>
                                    <tr>
                                        <td class="px-5 fw-900 text-dark small"><?= $exp['expense_date'] ? date('d M, Y', strtotime($exp['expense_date'])) : '—' ?></td>
                                        <td class="fw-900 text-dark"><?= esc($exp['title']) ?></td>
                                        <td><span class="category-token-sm"><?= esc($exp['category']) ?></span></td>
                                        <td class="fw-900 text-danger">Rs. <?= number_format($exp['amount'], 0) ?></td>
                                        <td class="text-end px-5">
                                            <a href="<?= base_url('expenses/delete/'.$exp['id']) ?>" 
                                               class="btn btn-sm btn-outline-danger border-0 rounded-pill px-3" 
                                               onclick="return confirm('Are you sure you want to delete this expense?')">
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
    </div>
</div>

<?= $this->endSection() ?>

