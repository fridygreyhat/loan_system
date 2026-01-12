<?php
$base_path = '../';
$page_title = 'Profit & Loss Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';

$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

$total_income = 0;
$total_expenses = 0;
$interest_income = 0;
$other_income = 0;

try {
    $stmt = $con->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM loan_repayment
        WHERE paid_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $interest_income = $result['total'] ?? 0;
    
    $stmt = $con->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM other_income
        WHERE income_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $other_income = $result['total'] ?? 0;
    
    $stmt = $con->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM expenses
        WHERE expense_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_expenses = $result['total'] ?? 0;
} catch (PDOException $e) {
}

$total_income = $interest_income + $other_income;
$net_profit = $total_income - $total_expenses;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Profit & Loss Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Reports</a></li>
                <li class="breadcrumb-item active">Profit & Loss</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i>Print</button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export</button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start" class="form-control" value="<?php echo $start_date; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end" class="form-control" value="<?php echo $end_date; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_income, 2); ?></h3>
                <p>Total Income</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_expenses, 2); ?></h3>
                <p>Total Expenses</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon <?php echo $net_profit >= 0 ? 'primary' : 'warning'; ?>">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3 class="<?php echo $net_profit >= 0 ? 'text-success' : 'text-danger'; ?>">$<?php echo number_format($net_profit, 2); ?></h3>
                <p>Net <?php echo $net_profit >= 0 ? 'Profit' : 'Loss'; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Income</h5>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr>
                        <td>Interest & Loan Repayments</td>
                        <td class="text-end text-success">$<?php echo number_format($interest_income, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Other Income</td>
                        <td class="text-end text-success">$<?php echo number_format($other_income, 2); ?></td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Total Income</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($total_income, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Expenses</h5>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr>
                        <td>Operating Expenses</td>
                        <td class="text-end text-danger">$<?php echo number_format($total_expenses, 2); ?></td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>Total Expenses</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($total_expenses, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header <?php echo $net_profit >= 0 ? 'bg-primary' : 'bg-warning'; ?> text-white">
        <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>Summary</h5>
    </div>
    <div class="card-body">
        <table class="table table-lg mb-0">
            <tr>
                <td class="fs-5">Total Income</td>
                <td class="text-end fs-5 text-success">$<?php echo number_format($total_income, 2); ?></td>
            </tr>
            <tr>
                <td class="fs-5">Total Expenses</td>
                <td class="text-end fs-5 text-danger">($<?php echo number_format($total_expenses, 2); ?>)</td>
            </tr>
            <tr class="<?php echo $net_profit >= 0 ? 'table-primary' : 'table-warning'; ?>">
                <td class="fs-4"><strong>Net <?php echo $net_profit >= 0 ? 'Profit' : 'Loss'; ?></strong></td>
                <td class="text-end fs-4 <?php echo $net_profit >= 0 ? 'text-success' : 'text-danger'; ?>"><strong>$<?php echo number_format(abs($net_profit), 2); ?></strong></td>
            </tr>
        </table>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
