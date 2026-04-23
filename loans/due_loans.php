<?php
$base_path = '../';
$page_title = 'Due Loans';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$due_loans = [];
$total_due = 0;
$total_loans = 0;
try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, b.mobile_no, lp.name as product_name,
               COALESCE(
                   (SELECT SUM(lr.amount) FROM loan_repayment lr WHERE lr.loan_id = l.id),
                   0
               ) as paid_amount,
               l.principal_amt as total_due_amount
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status NOT IN ('paid','closed','written_off')
        ORDER BY l.release_date ASC
    ");
    $due_loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_loans = count($due_loans);
    foreach ($due_loans as $loan) {
        $total_due += ($loan['principal_amt'] ?? 0) - ($loan['paid_amount'] ?? 0);
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('due_loans'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('due_loans'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_loans; ?></h3>
                <p><?php echo __('due_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_due, 2); ?></h3>
                <p><?php echo __('total_outstanding'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-clock me-2"></i><?php echo __('due_loans'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width:220px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="dueLoansTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?php echo __('loan_no'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('paid'); ?></th>
                        <th><?php echo __('outstanding'); ?></th>
                        <th><?php echo __('release_date'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($due_loans)): ?>
                    <tr>
                        <td colspan="11" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0"><?php echo __('no_due_loans'); ?></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $i = 1; foreach ($due_loans as $loan):
                        $outstanding = ($loan['principal_amt'] ?? 0) - ($loan['paid_amount'] ?? 0);
                        $pct = $loan['principal_amt'] > 0 ? round(($loan['paid_amount'] / $loan['principal_amt']) * 100) : 0;
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="fw-semibold text-primary"><?php echo htmlspecialchars($loan['loan_no'] ?? 'L-' . $loan['id']); ?></a></td>
                        <td><?php echo htmlspecialchars($loan['fname'] . ' ' . $loan['lname']); ?></td>
                        <td><?php echo htmlspecialchars($loan['mobile_no'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td class="fw-semibold">TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?></td>
                        <td class="text-success">TZS <?php echo number_format($loan['paid_amount'] ?? 0, 2); ?></td>
                        <td class="text-danger fw-semibold">TZS <?php echo number_format($outstanding, 2); ?></td>
                        <td><?php echo htmlspecialchars($loan['release_date'] ?? '-'); ?></td>
                        <td>
                            <?php
                            $status = strtolower($loan['status'] ?? 'active');
                            $badgeClass = match($status) {
                                'active' => 'bg-success',
                                'pending','pending_approval' => 'bg-warning text-dark',
                                'overdue','arrears' => 'bg-danger',
                                default => 'bg-secondary'
                            };
                            ?>
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars(ucfirst($loan['status'] ?? 'Active')); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="btn btn-outline-primary btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                <a href="../repayments/add_repayment.php?loan_id=<?php echo $loan['id']; ?>" class="btn btn-outline-success btn-sm" title="Record Payment"><i class="fas fa-plus"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#dueLoansTable tbody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
