<?php
$base_path = '../';
$page_title = 'No Repayments';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$loans = [];
try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        LEFT JOIN repayments r ON l.id = r.loan_id
        WHERE l.status = 'approved'
        GROUP BY l.id, b.fname, b.lname, lp.name
        HAVING COUNT(r.id) = 0
        ORDER BY l.release_date DESC
    ");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-times-circle text-danger me-2"></i><?php echo __('no_repayments'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('no_repayments'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($loans); ?></h3>
                <p><?php echo __('no_repayments'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('no_repayments'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('release_date'); ?></th>
                        <th><?php echo __('maturity_date'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_loans_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td><?php echo $loan['release_date'] ? date('M d, Y', strtotime($loan['release_date'])) : '-'; ?></td>
                        <td><?php echo $loan['maturity_date'] ? date('M d, Y', strtotime($loan['maturity_date'])) : '-'; ?></td>
                        <td>
                            <a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
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

<?php include $base_path . 'includes/footer.php'; ?>
