<?php
$base_path = '../';
$page_title = 'Dashboard';
$current_page = 'dashboard';
$current_user = 'Admin User';
$user_role = 'Administrator';

require_once $base_path . 'helpers/connect.php';

$total_borrowers = 0;
$total_loans = 0;
$total_disbursed = 0;
$total_collected = 0;
$pending_loans = 0;
$overdue_loans = 0;

try {
    $stmt = $con->query("SELECT COUNT(*) as count FROM borrower");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_borrowers = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_loans = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amt), 0) as total FROM loans WHERE status = 'approved'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_disbursed = $result['total'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans WHERE status = 'processing'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pending_loans = $result['count'] ?? 0;
    
    $stmt = $con->query("SELECT COUNT(*) as count FROM loans WHERE status = 'overdue' OR status = 'defaulted'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $overdue_loans = $result['count'] ?? 0;
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Overview</li>
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
                <p>Total Borrowers</p>
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
                <p>Active Loans</p>
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
                <p>Pending Loans</p>
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
                <p>Overdue Loans</p>
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
                <h3>$0</h3>
                <p>Total Savings</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card investor-card">
            <div class="stat-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <h3>$0</h3>
                <p>Total Investments</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_disbursed); ?></h3>
                <p>Total Disbursed</p>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>$<?php echo number_format($total_collected); ?></h3>
                <p>Total Collected</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-area me-2"></i>Loan Performance</h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        This Year
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Quarter</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
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
                <h5><i class="fas fa-chart-pie me-2"></i>Loan Status</h5>
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
                <h5><i class="fas fa-clock me-2"></i>Recent Activity</h5>
                <a href="#" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                <div class="activity-item">
                    <div class="activity-icon" style="background: rgba(0, 217, 126, 0.1); color: #00d97e;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="activity-content">
                        <h6>Loan Approved</h6>
                        <p>Loan #1234 for John Doe was approved</p>
                    </div>
                    <span class="activity-time">2 hours ago</span>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: rgba(44, 123, 229, 0.1); color: #2c7be5;">
                        <i class="fas fa-money-bill"></i>
                    </div>
                    <div class="activity-content">
                        <h6>Payment Received</h6>
                        <p>$500 payment received from Jane Smith</p>
                    </div>
                    <span class="activity-time">4 hours ago</span>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: rgba(246, 195, 67, 0.1); color: #f6c343;">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="activity-content">
                        <h6>New Borrower</h6>
                        <p>New borrower registration: Mike Johnson</p>
                    </div>
                    <span class="activity-time">6 hours ago</span>
                </div>
                <div class="activity-item">
                    <div class="activity-icon" style="background: rgba(230, 55, 87, 0.1); color: #e63757;">
                        <i class="fas fa-exclamation"></i>
                    </div>
                    <div class="activity-content">
                        <h6>Missed Payment</h6>
                        <p>Loan #1189 has a missed payment</p>
                    </div>
                    <span class="activity-time">Yesterday</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?php echo $base_path; ?>borrowers/add_borrower.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Add Borrower
                    </a>
                    <a href="<?php echo $base_path; ?>loans/add_loan.php" class="btn btn-outline-primary">
                        <i class="fas fa-plus-circle me-2"></i>Create Loan
                    </a>
                    <a href="<?php echo $base_path; ?>repayments/add_repayment.php" class="btn btn-outline-success">
                        <i class="fas fa-money-check me-2"></i>Record Payment
                    </a>
                    <a href="<?php echo $base_path; ?>reports/collections.php" class="btn btn-outline-secondary">
                        <i class="fas fa-file-alt me-2"></i>Generate Report
                    </a>
                    <a href="<?php echo $base_path; ?>collection_sheets/send_reminders.php" class="btn btn-outline-warning">
                        <i class="fas fa-bell me-2"></i>Send Reminders
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
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Disbursements',
            data: [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 32000, 40000, 38000, 45000],
            borderColor: '#2c7be5',
            backgroundColor: 'rgba(44, 123, 229, 0.1)',
            fill: true,
            tension: 0.4
        }, {
            label: 'Collections',
            data: [8000, 12000, 10000, 18000, 15000, 22000, 20000, 25000, 23000, 30000, 28000, 35000],
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
                        return '$' + value.toLocaleString();
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
        labels: ['Active', 'Completed', 'Pending', 'Overdue'],
        datasets: [{
            data: [45, 30, 15, 10],
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
