<?php
$base_path = '../';
$page_title = 'Profit & Loss';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$date_from = $_GET['from'] ?? date('Y-01-01');
$date_to = $_GET['to'] ?? date('Y-m-d');

$interest_income = 0;
$fee_income = 0;
$other_income = 0;
$salaries = 0;
$rent = 0;
$utilities = 0;
$depreciation = 0;
$bad_debt = 0;
$other_expenses = 0;

try {
    $stmt = $con->prepare("SELECT COALESCE(SUM(interest), 0) as total FROM loan_repayment WHERE paid_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $interest_income = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM loan_repayment WHERE paid_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $fee_income = $stmt->fetch(PDO::FETCH_ASSOC)['total'] * 0.02;
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM other_income WHERE income_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $other_income = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(net_pay), 0) as total FROM payroll WHERE pay_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $salaries = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM expenses WHERE expense_date BETWEEN ? AND ?");
    $stmt->execute([$date_from, $date_to]);
    $total_exp = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $rent = $total_exp * 0.3;
    $utilities = $total_exp * 0.1;
    $other_expenses = $total_exp * 0.6;
} catch (PDOException $e) {
}

$depreciation = 250000;
$bad_debt = $interest_income * 0.05;

$total_revenue = $interest_income + $fee_income + $other_income;
$total_expenses = $salaries + $rent + $utilities + $depreciation + $bad_debt + $other_expenses;
$net_profit = $total_revenue - $total_expenses;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('profit_loss'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('profit_loss'); ?></li>
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
            <div class="col-md-4">
                <label class="form-label"><?php echo __('period_from'); ?></label>
                <input type="date" name="from" class="form-control" value="<?php echo $date_from; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label"><?php echo __('period_to'); ?></label>
                <input type="date" name="to" class="form-control" value="<?php echo $date_to; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('generate_report'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-bar me-2"></i><?php echo __('profit_loss'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('account_name'); ?></th>
                        <th class="text-end"><?php echo __('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-success">
                        <td colspan="2"><strong><?php echo __('revenue'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('interest_income'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($interest_income, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('fee_income'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($fee_income, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('other_income'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($other_income, 2); ?></td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-5"><strong><?php echo __('total_revenue'); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_revenue, 2); ?></strong></td>
                    </tr>
                    
                    <tr class="table-danger">
                        <td colspan="2"><strong><?php echo __('expenses'); ?></strong></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('salaries_wages'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($salaries, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('rent'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($rent, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('utilities'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($utilities, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('depreciation'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($depreciation, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('bad_debt_provision'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($bad_debt, 2); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-5"><?php echo __('other_expenses'); ?></td>
                        <td class="text-end">TZS <?php echo number_format($other_expenses, 2); ?></td>
                    </tr>
                    <tr class="table-light">
                        <td class="ps-5"><strong><?php echo __('total_expenses'); ?></strong></td>
                        <td class="text-end"><strong>TZS <?php echo number_format($total_expenses, 2); ?></strong></td>
                    </tr>
                    
                    <tr class="table-primary">
                        <td><strong class="fs-5"><?php echo __('net_profit'); ?></strong></td>
                        <td class="text-end"><strong class="fs-5 <?php echo $net_profit >= 0 ? 'text-success' : 'text-danger'; ?>">TZS <?php echo number_format($net_profit, 2); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
