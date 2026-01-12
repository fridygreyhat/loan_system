<?php
$base_path = '../';
$page_title = 'View Repayments';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';

$repayments = [];
try {
    $stmt = $con->query("
        SELECT lr.*, l.id as loan_id, b.fname, b.lname
        FROM loan_repayment lr
        LEFT JOIN loans l ON lr.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        ORDER BY lr.paid_date DESC
        LIMIT 100
    ");
    $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Repayments</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Repayments</li>
            </ol>
        </nav>
    </div>
    <a href="add_repayment.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Record Repayment</a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-money-bill-wave me-2"></i>All Repayments</h5>
        <input type="text" class="form-control w-25" placeholder="Search repayments..." id="searchInput">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Borrower</th>
                        <th>Loan #</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($repayments)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No repayments recorded yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($repayments as $index => $item): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo date('M d, Y', strtotime($item['paid_date'])); ?></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td><a href="<?php echo $base_path; ?>loans/view_loan.php?id=<?php echo $item['loan_id']; ?>">#<?php echo $item['loan_id']; ?></a></td>
                        <td class="text-success"><strong>$<?php echo number_format($item['amount'] ?? 0, 2); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['payment_method'] ?? 'Cash'); ?></td>
                        <td>
                            <a href="view_repayment.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="edit_repayment.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
