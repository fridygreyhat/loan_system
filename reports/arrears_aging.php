<?php
$base_path = '../';
$page_title = 'Loan Arrears Aging Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$arrears_30 = [];
$arrears_60 = [];
$arrears_90 = [];
$arrears_over_90 = [];

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname,
               CURRENT_DATE - l.maturity_date AS days_overdue
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE l.status = 'approved' AND l.maturity_date < CURRENT_DATE
        ORDER BY days_overdue DESC
    ");
    $all_arrears = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($all_arrears as $loan) {
        $days = $loan['days_overdue'] ?? 0;
        if ($days <= 30) {
            $arrears_30[] = $loan;
        } elseif ($days <= 60) {
            $arrears_60[] = $loan;
        } elseif ($days <= 90) {
            $arrears_90[] = $loan;
        } else {
            $arrears_over_90[] = $loan;
        }
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-clock me-2"></i><?php echo __('loan_arrears_aging_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('arrears_aging'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($arrears_30); ?></h3>
                <p>1-30 <?php echo __('days'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-week"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($arrears_60); ?></h3>
                <p>31-60 <?php echo __('days'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($arrears_90); ?></h3>
                <p>61-90 <?php echo __('days'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon dark">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($arrears_over_90); ?></h3>
                <p>90+ <?php echo __('days'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('arrears_aging'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('maturity_date'); ?></th>
                        <th><?php echo __('days_overdue'); ?></th>
                        <th><?php echo __('aging_bucket'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $all_loans = array_merge($arrears_over_90, $arrears_90, $arrears_60, $arrears_30);
                    if (empty($all_loans)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_arrears_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($all_loans as $loan): 
                        $days = $loan['days_overdue'] ?? 0;
                        if ($days <= 30) { $bucket = '1-30'; $badge = 'success'; }
                        elseif ($days <= 60) { $bucket = '31-60'; $badge = 'warning'; }
                        elseif ($days <= 90) { $bucket = '61-90'; $badge = 'danger'; }
                        else { $bucket = '90+'; $badge = 'dark'; }
                    ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td><?php echo $loan['maturity_date'] ? date('M d, Y', strtotime($loan['maturity_date'])) : '-'; ?></td>
                        <td><?php echo $days; ?> <?php echo __('days'); ?></td>
                        <td><span class="badge bg-<?php echo $badge; ?>"><?php echo $bucket; ?> <?php echo __('days'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
