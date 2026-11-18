<?php
$base_path = '../';
$page_title = 'Dashboard';
$current_page = 'dashboard';
$current_user = 'Admin User';
$user_role = 'Administrator';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$total_borrowers = 0;
$total_loans = 0;
$total_disbursed = 0;
$total_collected = 0;
$total_savings = 0;
$total_investments = 0;
$monthly_releases = array_fill(0, 12, 0);
$monthly_collections = array_fill(0, 12, 0);
$status_counts = [0, 0, 0, 0];   // active, completed, pending, overdue
$pending_loans = 0;
$overdue_loans = 0;

try {
    $stmt = $con->query("SELECT COUNT(*) as count FROM borrower");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_borrowers = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_loans = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) as total FROM loans WHERE status = 'approved'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_disbursed = $result['total'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans WHERE status = 'pending'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pending_loans = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans WHERE status = 'defaulted' OR (status = 'approved' AND maturity_date < CURRENT_DATE)");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $overdue_loans = $result['count'] ?? 0;

    $total_collected = (float)$con->query("SELECT COALESCE(SUM(amount), 0) FROM loan_repayment WHERE COALESCE(status, '') <> 'rejected'")->fetchColumn();
    $total_savings = (float)$con->query("SELECT COALESCE(SUM(balance), 0) FROM savings_accounts WHERE status = 'active'")->fetchColumn();
    $total_investments = (float)$con->query("SELECT COALESCE(SUM(investment_amount), 0) FROM investor_accounts WHERE status = 'active'")->fetchColumn();

    // Charts: this year's releases and collections by month, and loans by status bucket.
    $year = (int)date('Y');
    $stmt = $con->prepare("SELECT EXTRACT(MONTH FROM release_date)::int AS m, SUM(principal_amount) FROM loans
                           WHERE status IN ('approved', 'paid', 'defaulted') AND EXTRACT(YEAR FROM release_date) = ? GROUP BY 1");
    $stmt->execute([$year]);
    foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$m, $total]) {
        $monthly_releases[$m - 1] = (float)$total;
    }
    $stmt = $con->prepare("SELECT EXTRACT(MONTH FROM paid_date)::int AS m, SUM(amount) FROM loan_repayment
                           WHERE COALESCE(status, '') <> 'rejected' AND EXTRACT(YEAR FROM paid_date) = ? GROUP BY 1");
    $stmt->execute([$year]);
    foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$m, $total]) {
        $monthly_collections[$m - 1] = (float)$total;
    }
    $stmt = $con->query("
        SELECT CASE
                 WHEN status = 'paid' THEN 1
                 WHEN status = 'pending' THEN 2
                 WHEN status = 'defaulted' OR (status = 'approved' AND maturity_date < CURRENT_DATE) THEN 3
                 WHEN status = 'approved' THEN 0
               END AS bucket, COUNT(*)
        FROM loans GROUP BY 1
    ");
    foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$bucket, $count]) {
        if ($bucket !== null) {
            $status_counts[$bucket] = (int)$count;
        }
    }
} catch (PDOException $e) {
    $load_error = 'Could not load dashboard data: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>

<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>
<div class="page-header">
    <h1><?php echo __('dashboard'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active"><?php echo __('overview'); ?></li>
        </ol>
    </nav>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_borrowers); ?></h3>
                <p><?php echo __('total_borrowers'); ?></p>
            </div>
            <span class="stat-change up"><i class="fas fa-arrow-up"></i> 12%</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_loans); ?></h3>
                <p><?php echo __('active_loans'); ?></p>
            </div>
            <span class="stat-change up"><i class="fas fa-arrow-up"></i> 8%</span>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($pending_loans); ?></h3>
                <p><?php echo __('pending_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($overdue_loans); ?></h3>
                <p><?php echo __('overdue_loans'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_savings); ?></h3>
                <p><?php echo __('total_savings'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card investor-card">
            <div class="stat-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_investments); ?></h3>
                <p><?php echo __('total_investments'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_disbursed); ?></h3>
                <p><?php echo __('total_disbursed'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_collected); ?></h3>
                <p><?php echo __('total_collected'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-area me-2"></i><?php echo __('loan_performance'); ?></h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <?php echo __('this_year'); ?>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><?php echo __('this_month'); ?></a></li>
                        <li><a class="dropdown-item" href="#"><?php echo __('this_quarter'); ?></a></li>
                        <li><a class="dropdown-item" href="#"><?php echo __('this_year'); ?></a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="loanPerformanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i><?php echo __('loan_status'); ?></h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="loanStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock me-2"></i><?php echo __('recent_activity'); ?></h5>
                <a href="#" class="btn btn-sm btn-primary"><?php echo __('view_all'); ?></a>
            </div>
            <div class="card-body">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-clock fa-3x mb-3 opacity-50"></i>
                    <p><?php echo __('no_recent_activity'); ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2"></i><?php echo __('quick_actions'); ?></h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo $base_path; ?>borrowers/add_borrower.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i><?php echo __('add_borrower'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>loans/add_loan.php" class="btn btn-outline-primary">
                        <i class="fas fa-plus-circle me-2"></i><?php echo __('create_loan'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>repayments/add_repayment.php" class="btn btn-outline-success">
                        <i class="fas fa-money-check me-2"></i><?php echo __('record_payment'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>reports/collections.php" class="btn btn-outline-secondary">
                        <i class="fas fa-file-alt me-2"></i><?php echo __('generate_report'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>collection_sheets/send_reminders.php" class="btn btn-outline-warning">
                        <i class="fas fa-bell me-2"></i><?php echo __('send_reminders'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const loanPerformanceCtx = document.getElementById('loanPerformanceChart').getContext('2d');
new Chart(loanPerformanceCtx, {
    type: 'line',
    data: {
        labels: ['<?php echo __('jan'); ?>', '<?php echo __('feb'); ?>', '<?php echo __('mar'); ?>', '<?php echo __('apr'); ?>', '<?php echo __('may'); ?>', '<?php echo __('jun'); ?>', '<?php echo __('jul'); ?>', '<?php echo __('aug'); ?>', '<?php echo __('sep'); ?>', '<?php echo __('oct'); ?>', '<?php echo __('nov'); ?>', '<?php echo __('dec'); ?>'],
        datasets: [{
            label: '<?php echo __('disbursements'); ?>',
            data: <?php echo json_encode($monthly_releases); ?>,
            borderColor: '#2c7be5',
            backgroundColor: 'rgba(44, 123, 229, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: '<?php echo __('collections'); ?>',
            data: <?php echo json_encode($monthly_collections); ?>,
            borderColor: '#00d97e',
            backgroundColor: 'rgba(0, 217, 126, 0.1)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'TZS ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

const loanStatusCtx = document.getElementById('loanStatusChart').getContext('2d');
new Chart(loanStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['<?php echo __('active'); ?>', '<?php echo __('completed'); ?>', '<?php echo __('pending'); ?>', '<?php echo __('overdue'); ?>'],
        datasets: [{
            data: <?php echo json_encode(array_values($status_counts)); ?>,
            backgroundColor: ['#2c7be5', '#00d97e', '#f6c343', '#e63757'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        cutout: '70%'
    }
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
