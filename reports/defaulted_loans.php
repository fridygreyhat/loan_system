<?php
$base_path = '../';
$page_title = 'Defaulted Loans Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$defaulted = [];

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name,
               COALESCE((SELECT SUM(lr.amount) FROM loan_repayment lr WHERE lr.loan_id = l.id), 0) as total_paid,
               (l.principal_amount + (l.principal_amount * l.loan_interest / 100)) as total_due,
               CURRENT_DATE - l.maturity_date AS days_overdue
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status = 'defaulted' 
           OR (l.status = 'approved' AND l.maturity_date < CURRENT_DATE - INTERVAL '90 days')
        ORDER BY days_overdue DESC
    ");
    $defaulted = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_defaulted = 0;
foreach ($defaulted as $loan) {
    $total_defaulted += max(0, ($loan['total_due'] ?? 0) - ($loan['total_paid'] ?? 0));
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-exclamation-triangle me-2"></i><?php echo __('defaulted_loans_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('defaulted'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($defaulted); ?></h3>
                <p><?php echo __('defaulted_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_defaulted, 2); ?></h3>
                <p><?php echo __('total_defaulted_amount'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('defaulted_loans'); ?></h5>
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
                        <th><?php echo __('balance'); ?></th>
                        <th><?php echo __('days_overdue'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($defaulted)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_defaulted_loans'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($defaulted as $loan): 
                        $balance = max(0, ($loan['total_due'] ?? 0) - ($loan['total_paid'] ?? 0));
                    ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td class="text-danger"><strong>TZS <?php echo number_format($balance, 2); ?></strong></td>
                        <td><span class="badge bg-danger"><?php echo $loan['days_overdue'] ?? 0; ?> <?php echo __('days'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
