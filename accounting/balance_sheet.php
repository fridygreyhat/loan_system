<?php
$base_path = '../';
$page_title = 'Balance Sheet';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$as_of_date = $_GET['date'] ?? date('Y-m-d');

$cash_bank = 0;
$loans_receivable = 0;
$accounts_receivable = 0;
$prepaid_expenses = 0;
$property_equipment = 5000000;
$accumulated_depreciation = 500000;
$accounts_payable = 0;
$accrued_expenses = 0;
$long_term_debt = 0;
$capital = 10000000;
$retained_earnings = 0;

try {
    $stmt = $con->prepare("SELECT COALESCE(SUM(balance), 0) as total FROM savings_accounts WHERE status = 'active'");
    $stmt->execute();
    $cash_bank = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(principal_amount - COALESCE((SELECT SUM(principal) FROM loan_repayment WHERE loan_id = loans.id), 0)), 0) as total FROM loans WHERE status IN ('active', 'approved')");
    $stmt->execute();
    $loans_receivable = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(investment_amount), 0) as total FROM investor_accounts WHERE status = 'active'");
    $stmt->execute();
    $long_term_debt = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM loan_repayment");
    $stmt->execute();
    $retained_earnings = $stmt->fetch(PDO::FETCH_ASSOC)['total'] * 0.1;
} catch (PDOException $e) {
}

$total_current_assets = $cash_bank + $loans_receivable + $accounts_receivable + $prepaid_expenses;
$total_fixed_assets = $property_equipment - $accumulated_depreciation;
$total_assets = $total_current_assets + $total_fixed_assets;

$total_current_liabilities = $accounts_payable + $accrued_expenses;
$total_long_term_liabilities = $long_term_debt;
$total_liabilities = $total_current_liabilities + $total_long_term_liabilities;

$total_equity = $capital + $retained_earnings;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('balance_sheet'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('balance_sheet'); ?></li>
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
            <div class="col-md-6">
                <label class="form-label"><?php echo __('as_of_date'); ?></label>
                <input type="date" name="date" class="form-control" value="<?php echo $as_of_date; ?>">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('generate_report'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-coins me-2"></i><?php echo __('assets'); ?></h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr class="table-light">
                            <td colspan="2"><strong><?php echo __('current_assets'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('cash_bank'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($cash_bank, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('loans_receivable'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($loans_receivable, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('accounts_receivable'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($accounts_receivable, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('prepaid_expenses'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($prepaid_expenses, 2); ?></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="2"><strong><?php echo __('fixed_assets'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('property_equipment'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($property_equipment, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('accumulated_depreciation'); ?></td>
                            <td class="text-end">(TZS <?php echo number_format($accumulated_depreciation, 2); ?>)</td>
                        </tr>
                        <tr class="table-success">
                            <td><strong><?php echo __('total_assets'); ?></strong></td>
                            <td class="text-end"><strong>TZS <?php echo number_format($total_assets, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('liabilities'); ?> & <?php echo __('equity'); ?></h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <tbody>
                        <tr class="table-light">
                            <td colspan="2"><strong><?php echo __('current_liabilities'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('accounts_payable'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($accounts_payable, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('accrued_expenses'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($accrued_expenses, 2); ?></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="2"><strong><?php echo __('long_term_liabilities'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('long_term_debt'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($long_term_debt, 2); ?></td>
                        </tr>
                        <tr class="table-warning">
                            <td><strong><?php echo __('total_liabilities'); ?></strong></td>
                            <td class="text-end"><strong>TZS <?php echo number_format($total_liabilities, 2); ?></strong></td>
                        </tr>
                        <tr class="table-light">
                            <td colspan="2"><strong><?php echo __('equity'); ?></strong></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('capital'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($capital, 2); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-5"><?php echo __('retained_earnings'); ?></td>
                            <td class="text-end">TZS <?php echo number_format($retained_earnings, 2); ?></td>
                        </tr>
                        <tr class="table-primary">
                            <td><strong><?php echo __('total_equity'); ?></strong></td>
                            <td class="text-end"><strong>TZS <?php echo number_format($total_equity, 2); ?></strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
