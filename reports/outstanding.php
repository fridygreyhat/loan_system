<?php
$base_path = '../';
$page_title = 'Outstanding Loans Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$outstanding = [];
$total_outstanding = 0;

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name,
               COALESCE((SELECT SUM(lr.amount) FROM loan_repayment lr WHERE lr.loan_id = l.id), 0) as total_paid,
               (l.principal_amount + (l.principal_amount * l.loan_interest / 100)) as total_due
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status = 'approved'
        ORDER BY l.id DESC
    ");
    $outstanding = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($outstanding as &$loan) {
        $loan['balance'] = max(0, ($loan['total_due'] ?? 0) - ($loan['total_paid'] ?? 0));
        $total_outstanding += $loan['balance'];
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('outstanding_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('outstanding'); ?></li>
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
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($outstanding); ?></h3>
                <p><?php echo __('active_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_outstanding, 2); ?></h3>
                <p><?php echo __('total_outstanding'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('outstanding_loans'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('total_due'); ?></th>
                        <th><?php echo __('total_paid'); ?></th>
                        <th><?php echo __('balance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($outstanding)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_outstanding_loans'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($outstanding as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['total_due'] ?? 0, 2); ?></td>
                        <td class="text-success">TZS <?php echo number_format($loan['total_paid'] ?? 0, 2); ?></td>
                        <td class="text-warning"><strong>TZS <?php echo number_format($loan['balance'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
