<?php
$base_path = '../';
$page_title = 'At a Glance Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$stats = [
    'total_borrowers' => 0,
    'active_loans' => 0,
    'pending_loans' => 0,
    'overdue_loans' => 0,
    'total_portfolio' => 0,
    'total_collected' => 0,
    'disbursed_this_month' => 0,
    'collected_this_month' => 0
];

try {
    $stmt = $con->query("SELECT COUNT(*) FROM borrower");
    $stats['total_borrowers'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COUNT(*) FROM loans WHERE status = 'approved'");
    $stats['active_loans'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COUNT(*) FROM loans WHERE status = 'pending'");
    $stats['pending_loans'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) FROM loans WHERE status = 'approved'");
    $stats['total_portfolio'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(amount), 0) FROM loan_repayment");
    $stats['total_collected'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COUNT(*) FROM loans WHERE status = 'approved' AND maturity_date < CURRENT_DATE");
    $stats['overdue_loans'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) FROM loans WHERE status = 'approved' AND release_date >= DATE_TRUNC('month', CURRENT_DATE)");
    $stats['disbursed_this_month'] = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(amount), 0) FROM loan_repayment WHERE paid_date >= DATE_TRUNC('month', CURRENT_DATE)");
    $stats['collected_this_month'] = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-tachometer-alt me-2"></i><?php echo __('at_a_glance_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('at_a_glance'); ?></li>
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
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['total_borrowers']); ?></h3>
                <p><?php echo __('total_borrowers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['active_loans']); ?></h3>
                <p><?php echo __('active_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['pending_loans']); ?></h3>
                <p><?php echo __('pending_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($stats['overdue_loans']); ?></h3>
                <p><?php echo __('overdue_loans'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i><?php echo __('portfolio_overview'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th><?php echo __('total_portfolio'); ?></th>
                                <td class="text-end">TZS <?php echo number_format($stats['total_portfolio'], 2); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('total_collected'); ?></th>
                                <td class="text-end text-success">TZS <?php echo number_format($stats['total_collected'], 2); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('outstanding'); ?></th>
                                <td class="text-end text-warning">TZS <?php echo number_format($stats['total_portfolio'] - $stats['total_collected'], 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calendar-alt me-2"></i><?php echo __('this_month'); ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th><?php echo __('disbursed'); ?></th>
                                <td class="text-end">TZS <?php echo number_format($stats['disbursed_this_month'], 2); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('collected'); ?></th>
                                <td class="text-end text-success">TZS <?php echo number_format($stats['collected_this_month'], 2); ?></td>
                            </tr>
                            <tr>
                                <th><?php echo __('net_cash_flow'); ?></th>
                                <td class="text-end <?php echo ($stats['collected_this_month'] - $stats['disbursed_this_month']) >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    TZS <?php echo number_format($stats['collected_this_month'] - $stats['disbursed_this_month'], 2); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
