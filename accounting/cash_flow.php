<?php
$base_path = '../';
$page_title = 'Cash Flow';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$date_from = $_GET['from'] ?? date('Y-01-01');
$date_to = $_GET['to'] ?? date('Y-m-d');
$view_type = $_GET['view'] ?? 'accumulated';

$loan_repayments = 0;
$interest_income = 0;
$fee_income = 0;
$loan_disbursements = 0;
$operating_expenses = 0;
$salaries = 0;

try {
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM loan_repayment WHERE paid_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $loan_repayments = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(interest), 0) as total FROM loan_repayment WHERE paid_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $interest_income = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(principal_amount), 0) as total FROM loans WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $loan_disbursements = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $operating_expenses = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(net_salary), 0) as total FROM payroll WHERE pay_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $salaries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
}

$fee_income = $loan_repayments * 0.02;
$total_inflows = $loan_repayments + $interest_income + $fee_income;
$total_outflows = $loan_disbursements + $operating_expenses + $salaries;
$net_cash_flow = $total_inflows - $total_outflows;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('cash_flow'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('cash_flow'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label"><?php echo __('period_from'); ?></label>
                <input type="date" name="from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo __('period_to'); ?></label>
                <input type="date" name="to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label"><?php echo __('view'); ?></label>
                <select name="view" class="form-select">
                    <option value="accumulated" <?php echo $view_type === 'accumulated' ? 'selected' : ''; ?>><?php echo __('accumulated'); ?></option>
                    <option value="monthly" <?php echo $view_type === 'monthly' ? 'selected' : ''; ?>><?php echo __('monthly'); ?></option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('generate_report'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_inflows, 2); ?></h3>
                <p><?php echo __('total_inflows'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_outflows, 2); ?></h3>
                <p><?php echo __('total_outflows'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stat-info">
                <h3 class="<?php echo $net_cash_flow >= 0 ? 'text-success' : 'text-danger'; ?>">TZS <?php echo number_format($net_cash_flow, 2); ?></h3>
                <p><?php echo __('net_cash_flow'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-line me-2"></i><?php echo __('cash_flow'); ?> - <?php echo __($view_type); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('category'); ?></th>
                        <th class="text-end"><?php echo __('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-success">
                        <td colspan="2"><strong><?php echo __('cash_inflows'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('loan_repayments'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($loan_repayments, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('interest_income'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($interest_income, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('fee_income'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($fee_income, 2); ?></td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-5"><strong><?php echo __('total_inflows'); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_inflows, 2); ?></strong></td>
                    </tr>
                    
                    <tr class="table-danger">
                        <td colspan="2"><strong><?php echo __('cash_outflows'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('loan_disbursements'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($loan_disbursements, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('operating_expenses'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($operating_expenses, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('salaries_wages'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($salaries, 2); ?></td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-5"><strong><?php echo __('total_outflows'); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_outflows, 2); ?></strong></td>
                    </tr>
                    
                    <tr class="table-primary">
                        <td><strong class="fs-5"><?php echo __('net_cash_flow'); ?></strong></td>
                        <td class="text-end"><strong class="fs-5 <?php echo $net_cash_flow >= 0 ? 'text-success' : 'text-danger'; ?>">TZS <?php echo number_format($net_cash_flow, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
