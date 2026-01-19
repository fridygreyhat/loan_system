<?php
$base_path = '../';
$page_title = 'Fully Paid Loans Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$fully_paid = [];

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name,
               COALESCE((SELECT SUM(lr.amount) FROM loan_repayment lr WHERE lr.loan_id = l.id), 0) as total_paid,
               (l.principal_amount + (l.principal_amount * l.loan_interest / 100)) as total_due
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status = 'paid' OR l.status = 'completed'
        ORDER BY l.id DESC
    ");
    $fully_paid = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_principal = array_sum(array_column($fully_paid, 'principal_amount'));
$total_collected = array_sum(array_column($fully_paid, 'total_paid'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-check-circle me-2"></i><?php echo __('fully_paid_loans_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('fully_paid'); ?></li>
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
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($fully_paid); ?></h3>
                <p><?php echo __('fully_paid_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_principal, 2); ?></h3>
                <p><?php echo __('total_principal'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_collected, 2); ?></h3>
                <p><?php echo __('total_collected'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('completed_loans'); ?></h5>
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
                        <th><?php echo __('status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fully_paid)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_completed_loans'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fully_paid as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td class="text-success">TZS <?php echo number_format($loan['total_paid'] ?? 0, 2); ?></td>
                        <td><span class="badge bg-success"><?php echo __('paid'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
