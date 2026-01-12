<?php
$base_path = '../';
$page_title = 'Daily Collection Sheet';
$current_page = 'collection_sheets';

require_once $base_path . 'helpers/connect.php';

$collections = [];
$today = date('Y-m-d');
try {
    $stmt = $con->prepare("
        SELECT lr.*, l.id as loan_id, l.principal_amount, b.fname, b.lname, b.mobile
        FROM loan_repayment lr
        LEFT JOIN loans l ON lr.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE DATE(lr.due_date) = ?
        ORDER BY lr.due_date ASC
    ");
    $stmt->execute([$today]);
    $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Daily Collection Sheet</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Daily Collection</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <input type="date" class="form-control" value="<?php echo $today; ?>" id="collectionDate">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i>Print</button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export</button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collections); ?></h3>
                <p>Due Today</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format(array_sum(array_column($collections, 'amount')), 2); ?></h3>
                <p>Expected Amount</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>Collections for <?php echo date('F d, Y', strtotime($today)); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Borrower</th>
                        <th>Phone</th>
                        <th>Loan #</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collections)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            No collections due for today.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collections as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['mobile'] ?? '-'); ?></td>
                        <td>#<?php echo $item['loan_id']; ?></td>
                        <td>$<?php echo number_format($item['amount'] ?? 0, 2); ?></td>
                        <td>
                            <?php 
                            $status = $item['status'] ?? 'pending';
                            $badge_class = $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $base_path; ?>repayments/add_repayment.php?loan_id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-success" title="Record Payment">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="<?php echo $base_path; ?>loans/view_loan_details.php?id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-primary" title="View Loan">
                                    <i class="fas fa-eye"></i>
                                </a>
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

<?php include $base_path . 'includes/footer.php'; ?>
