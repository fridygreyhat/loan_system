<?php
$base_path = '../';
$page_title = 'Portfolio at Risk';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';

$total_portfolio = 0;
$par_30 = 0;
$par_60 = 0;
$par_90 = 0;
$current_loans = 0;

try {
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) as total FROM loans WHERE status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_portfolio = $result['total'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans WHERE status = 'active'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_loans = $result['count'] ?? 0;
    
    $stmt = $con->query("
        SELECT COALESCE(SUM(principal_amount), 0) as total 
        FROM loans 
        WHERE status = 'active' 
        AND due_date < CURRENT_DATE - INTERVAL '30 days'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $par_30 = $result['total'] ?? 0;
    
    $stmt = $con->query("
        SELECT COALESCE(SUM(principal_amount), 0) as total 
        FROM loans 
        WHERE status = 'active' 
        AND due_date < CURRENT_DATE - INTERVAL '60 days'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $par_60 = $result['total'] ?? 0;
    
    $stmt = $con->query("
        SELECT COALESCE(SUM(principal_amount), 0) as total 
        FROM loans 
        WHERE status = 'active' 
        AND due_date < CURRENT_DATE - INTERVAL '90 days'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $par_90 = $result['total'] ?? 0;
} catch (PDOException $e) {
}

$par_30_pct = $total_portfolio > 0 ? ($par_30 / $total_portfolio) * 100 : 0;
$par_60_pct = $total_portfolio > 0 ? ($par_60 / $total_portfolio) * 100 : 0;
$par_90_pct = $total_portfolio > 0 ? ($par_90 / $total_portfolio) * 100 : 0;

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Portfolio at Risk</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Reports</a></li>
                <li class="breadcrumb-item active">Portfolio at Risk</li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i>Print</button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i>Export</button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_portfolio, 2); ?></h3>
                <p>Total Portfolio</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $current_loans; ?></h3>
                <p>Active Loans</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($par_30_pct, 1); ?>%</h3>
                <p>PAR > 30 Days</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($par_90_pct, 1); ?>%</h3>
                <p>PAR > 90 Days</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-chart-bar me-2"></i>Portfolio at Risk Analysis</h5>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Outstanding Amount</th>
                    <th>% of Portfolio</th>
                    <th>Risk Level</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Current (On-time)</strong></td>
                    <td>TZS <?php echo number_format($total_portfolio - $par_30, 2); ?></td>
                    <td><?php echo number_format(100 - $par_30_pct, 1); ?>%</td>
                    <td><span class="badge bg-success">Low</span></td>
                </tr>
                <tr>
                    <td><strong>PAR > 30 Days</strong></td>
                    <td>TZS <?php echo number_format($par_30, 2); ?></td>
                    <td><?php echo number_format($par_30_pct, 1); ?>%</td>
                    <td><span class="badge bg-warning">Medium</span></td>
                </tr>
                <tr>
                    <td><strong>PAR > 60 Days</strong></td>
                    <td>TZS <?php echo number_format($par_60, 2); ?></td>
                    <td><?php echo number_format($par_60_pct, 1); ?>%</td>
                    <td><span class="badge bg-orange text-white" style="background-color: #fd7e14;">High</span></td>
                </tr>
                <tr>
                    <td><strong>PAR > 90 Days</strong></td>
                    <td>TZS <?php echo number_format($par_90, 2); ?></td>
                    <td><?php echo number_format($par_90_pct, 1); ?>%</td>
                    <td><span class="badge bg-danger">Critical</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5><i class="fas fa-info-circle me-2"></i>Understanding PAR</h5>
    </div>
    <div class="card-body">
        <p><strong>Portfolio at Risk (PAR)</strong> measures the quality of a loan portfolio. It represents the outstanding balance of all loans with overdue payments as a percentage of the total portfolio.</p>
        <ul class="mb-0">
            <li><strong>PAR > 30:</strong> Loans overdue by more than 30 days</li>
            <li><strong>PAR > 60:</strong> Loans overdue by more than 60 days</li>
            <li><strong>PAR > 90:</strong> Loans overdue by more than 90 days (considered high risk)</li>
        </ul>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
