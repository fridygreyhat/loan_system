<?php
$base_path = '../';
$page_title = 'Monthly Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$selected_month = $_GET['month'] ?? date('Y-m');
$monthly_disbursed = 0;
$monthly_collected = 0;
$new_loans = 0;
$new_borrowers = 0;

try {
    $stmt = $con->prepare("SELECT COALESCE(SUM(principal_amount), 0) FROM loans WHERE TO_CHAR(release_date, 'YYYY-MM') = ? AND status = 'approved'");
    $stmt->execute([$selected_month]);
    $monthly_disbursed = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) FROM loan_repayment WHERE TO_CHAR(paid_date, 'YYYY-MM') = ?");
    $stmt->execute([$selected_month]);
    $monthly_collected = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->prepare("SELECT COUNT(*) FROM loans WHERE TO_CHAR(release_date, 'YYYY-MM') = ? AND status = 'approved'");
    $stmt->execute([$selected_month]);
    $new_loans = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->prepare("SELECT COUNT(*) FROM borrower WHERE TO_CHAR(created_at, 'YYYY-MM') = ?");
    $stmt->execute([$selected_month]);
    $new_borrowers = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-calendar-alt me-2"></i><?php echo __('monthly_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('monthly'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('month'); ?></label>
                <input type="month" name="month" class="form-control" value="<?php echo $selected_month; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($monthly_disbursed, 2); ?></h3>
                <p><?php echo __('disbursed'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($monthly_collected, 2); ?></h3>
                <p><?php echo __('collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $new_loans; ?></h3>
                <p><?php echo __('new_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $new_borrowers; ?></h3>
                <p><?php echo __('new_borrowers'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-bar me-2"></i><?php echo __('monthly_summary'); ?> - <?php echo date('F Y', strtotime($selected_month . '-01')); ?></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th><?php echo __('total_disbursed'); ?></th>
                        <td class="text-end">TZS <?php echo number_format($monthly_disbursed, 2); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('total_collected'); ?></th>
                        <td class="text-end text-success">TZS <?php echo number_format($monthly_collected, 2); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('net_cash_flow'); ?></th>
                        <td class="text-end <?php echo ($monthly_collected - $monthly_disbursed) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            TZS <?php echo number_format($monthly_collected - $monthly_disbursed, 2); ?>
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo __('new_loans'); ?></th>
                        <td class="text-end"><?php echo $new_loans; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo __('new_borrowers'); ?></th>
                        <td class="text-end"><?php echo $new_borrowers; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
