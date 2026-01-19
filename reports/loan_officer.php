<?php
$base_path = '../';
$page_title = 'Loan Officer Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$officers = [];
$total_disbursed = 0;

try {
    $stmt = $con->query("
        SELECT s.*, 
               COUNT(DISTINCT l.id) as loans_processed,
               COALESCE(SUM(l.principal_amount), 0) as total_disbursed,
               COUNT(DISTINCT CASE WHEN l.status = 'approved' THEN l.id END) as approved_count
        FROM staff s
        LEFT JOIN loans l ON l.loan_officer_id = s.id
        WHERE s.role = 'LoanOfficer' OR s.role = 'loan_officer'
        GROUP BY s.id
        ORDER BY total_disbursed DESC
    ");
    $officers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_disbursed = array_sum(array_column($officers, 'total_disbursed'));
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-tie me-2"></i><?php echo __('loan_officer_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loan_officer'); ?></li>
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
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($officers); ?></h3>
                <p><?php echo __('total_loan_officers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_disbursed, 2); ?></h3>
                <p><?php echo __('total_disbursed'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('loan_officer_performance'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('officer_name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('loans_processed'); ?></th>
                        <th><?php echo __('approved'); ?></th>
                        <th><?php echo __('total_disbursed'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($officers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_loan_officers_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($officers as $officer): ?>
                    <tr>
                        <td><?php echo $officer['id']; ?></td>
                        <td><?php echo htmlspecialchars(($officer['fname'] ?? '') . ' ' . ($officer['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($officer['email'] ?? '-'); ?></td>
                        <td><?php echo $officer['loans_processed']; ?></td>
                        <td><span class="badge bg-success"><?php echo $officer['approved_count']; ?></span></td>
                        <td class="text-success"><strong>TZS <?php echo number_format($officer['total_disbursed'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
