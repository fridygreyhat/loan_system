<?php
$base_path = '../';
$page_title = 'Principal Outstanding';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$loans = [];
$total_outstanding = 0;
try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name,
               COALESCE(SUM(r.amount), 0) as total_paid,
               (l.principal_amount - COALESCE(SUM(r.amount), 0)) as outstanding
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        LEFT JOIN repayments r ON l.id = r.loan_id
        WHERE l.status = 'approved'
        GROUP BY l.id, b.fname, b.lname, lp.name
        HAVING (l.principal_amount - COALESCE(SUM(r.amount), 0)) > 0
        ORDER BY outstanding DESC
    ");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($loans as $loan) {
        $total_outstanding += $loan['outstanding'];
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-money-bill-wave text-primary me-2"></i><?php echo __('principal_outstanding'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('principal_outstanding'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($loans); ?></h3>
                <p><?php echo __('active_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_outstanding, 0); ?></h3>
                <p><?php echo __('total'); ?> <?php echo __('outstanding'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('principal_outstanding'); ?></h5>
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
                        <th><?php echo __('total_paid'); ?></th>
                        <th><?php echo __('outstanding'); ?></th>
                        <th>%</th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_loans_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($loans as $loan): 
                        $pct = $loan['principal_amount'] > 0 ? ($loan['outstanding'] / $loan['principal_amount']) * 100 : 0;
                    ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td class="text-success">TZS <?php echo number_format($loan['total_paid'] ?? 0, 2); ?></td>
                        <td class="text-danger fw-bold">TZS <?php echo number_format($loan['outstanding'], 2); ?></td>
                        <td>
                            <div class="progress" style="height: 6px; width: 60px;">
                                <div class="progress-bar bg-danger" style="width: <?php echo $pct; ?>%"></div>
                            </div>
                        </td>
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
