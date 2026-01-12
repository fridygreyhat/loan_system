<?php
$base_path = '../';
$page_title = 'View Loans';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';

$loans = [];
try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        ORDER BY l.id DESC
    ");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Loans</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Loans</li>
            </ol>
        </nav>
    </div>
    <a href="add_loan.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Loan
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($loans); ?></h3>
                <p>Total Loans</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Loans</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-filter"></i></button>
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Loan #</th>
                        <th>Borrower</th>
                        <th>Product</th>
                        <th>Principal</th>
                        <th>Interest</th>
                        <th>Status</th>
                        <th>Release Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No loans found. <a href="add_loan.php">Create one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td><?php echo $loan['loan_interest'] ?? 0; ?>%</td>
                        <td>
                            <?php 
                            $status = $loan['status'] ?? 'pending';
                            $badge_class = 'secondary';
                            if ($status === 'approved') $badge_class = 'success';
                            elseif ($status === 'pending') $badge_class = 'warning';
                            elseif ($status === 'denied') $badge_class = 'danger';
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        <td><?php echo $loan['release_date'] ? date('M d, Y', strtotime($loan['release_date'])) : '-'; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_loan.php?id=<?php echo $loan['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
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
