<?php
$base_path = '../';
$page_title = 'Savings Charts';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$monthly_data = [];
$total_deposits = 0;
$total_withdrawals = 0;

try {
    $stmt = $con->query("
        SELECT 
            TO_CHAR(created_at, 'YYYY-MM') as month,
            SUM(CASE WHEN type = 'deposit' THEN amount ELSE 0 END) as deposits,
            SUM(CASE WHEN type = 'withdrawal' THEN amount ELSE 0 END) as withdrawals
        FROM savings_transactions
        WHERE created_at >= CURRENT_DATE - INTERVAL '12 months'
        GROUP BY TO_CHAR(created_at, 'YYYY-MM')
        ORDER BY month
    ");
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT COALESCE(SUM(CASE WHEN type='deposit' THEN amount ELSE 0 END), 0) as deposits, COALESCE(SUM(CASE WHEN type='withdrawal' THEN amount ELSE 0 END), 0) as withdrawals FROM savings_transactions");
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_deposits = $totals['deposits'];
    $total_withdrawals = $totals['withdrawals'];
} catch (PDOException $e) {
    $chart_error = 'Could not load savings data: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($chart_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($chart_error); ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-pie text-primary me-2"></i><?php echo __('savings_charts'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php"><?php echo __('savings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('savings_charts'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-arrow-down"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_deposits, 2); ?></h3>
                <p><?php echo __('total_deposits'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-arrow-up"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_withdrawals, 2); ?></h3>
                <p><?php echo __('total_withdrawals'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-balance-scale"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_deposits - $total_withdrawals, 2); ?></h3>
                <p><?php echo __('net_savings'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i><?php echo __('deposits_vs_withdrawals'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="savingsChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-doughnut me-2"></i><?php echo __('distribution'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="distributionChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyData = <?php echo json_encode($monthly_data); ?>;
    const labels = monthlyData.map(d => d.month);
    const deposits = monthlyData.map(d => parseFloat(d.deposits));
    const withdrawals = monthlyData.map(d => parseFloat(d.withdrawals));
    
    new Chart(document.getElementById('savingsChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '<?php echo __('deposits'); ?>',
                    data: deposits,
                    backgroundColor: 'rgba(40, 167, 69, 0.8)',
                    borderColor: 'rgb(40, 167, 69)',
                    borderWidth: 1
                },
                {
                    label: '<?php echo __('withdrawals'); ?>',
                    data: withdrawals,
                    backgroundColor: 'rgba(255, 193, 7, 0.8)',
                    borderColor: 'rgb(255, 193, 7)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    
    new Chart(document.getElementById('distributionChart'), {
        type: 'doughnut',
        data: {
            labels: ['<?php echo __('deposits'); ?>', '<?php echo __('withdrawals'); ?>'],
            datasets: [{
                data: [<?php echo $total_deposits; ?>, <?php echo $total_withdrawals; ?>],
                backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(255, 193, 7, 0.8)'],
                borderWidth: 0
            }]
        },
        options: { responsive: true }
    });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
