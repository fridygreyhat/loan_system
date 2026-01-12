<?php
$base_path = '../';
$page_title = 'Cash Flow Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';

$start_date = $_GET['start'] ?? date('Y-m-01');
$end_date = $_GET['end'] ?? date('Y-m-d');

$inflows = [];
$outflows = [];
$total_inflow = 0;
$total_outflow = 0;

try {
    $stmt = $con->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM loan_repayment
        WHERE paid_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_inflow = $result['total'] ?? 0;
    
    $stmt = $con->prepare("
        SELECT COALESCE(SUM(amount), 0) as total
        FROM expenses
        WHERE expense_date BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_outflow = $result['total'] ?? 0;
} catch (PDOException $e) {
}

$net_cash_flow = $total_inflow - $total_outflow;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Cash Flow Report</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Reports</a></li>
                <li class="breadcrumb-item active">Cash Flow</li>
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
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_inflow, 2); ?></h3>
                <p>Total Inflow</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_outflow, 2); ?></h3>
                <p>Total Outflow</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon <?php echo $net_cash_flow >= 0 ? 'primary' : 'warning'; ?>">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stat-info">
                <h3 class="<?php echo $net_cash_flow >= 0 ? 'text-success' : 'text-danger'; ?>">$<?php echo number_format($net_cash_flow, 2); ?></h3>
                <p>Net Cash Flow</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Cash Inflows</h5>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr>
                        <td>Loan Repayments</td>
                        <td class="text-end text-success"><strong>$<?php echo number_format($total_inflow, 2); ?></strong></td>
                    </tr>
                    <tr class="table-success">
                        <td><strong>Total Inflow</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($total_inflow, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Cash Outflows</h5>
            </div>
            <div class="card-body">
                <table class="table mb-0">
                    <tr>
                        <td>Expenses</td>
                        <td class="text-end text-danger"><strong>$<?php echo number_format($total_outflow, 2); ?></strong></td>
                    </tr>
                    <tr class="table-danger">
                        <td><strong>Total Outflow</strong></td>
                        <td class="text-end"><strong>$<?php echo number_format($total_outflow, 2); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
