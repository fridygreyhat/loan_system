<?php
$base_path = '../';
$page_title = 'Loan Fees';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$fees = [];
try {
    $stmt = $con->query("SELECT * FROM loan_fees ORDER BY name ASC");
    $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Loan Fees</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Loan Fees</li>
            </ol>
        </nav>
    </div>
    <a href="add_loan_fee.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Fee</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-percentage me-2"></i>All Loan Fees</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Fee Name</th>
                        <th>Amount/Rate</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-percentage fa-3x mb-3 d-block"></i>
                            No loan fees configured yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fees as $index => $fee): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($fee['name']); ?></strong></td>
                        <td><?php echo $fee['rate'] ?? $fee['amount'] ?? '-'; ?></td>
                        <td><?php echo ucfirst($fee['type'] ?? 'Fixed'); ?></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="add_loan_fee_edit.html?id=<?php echo $fee['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
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
