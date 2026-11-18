<?php
$base_path = '../';
$page_title = 'Charts & Analytics';
$current_page = 'charts';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$monthly_releases = [];
$monthly_collections = [];
$loan_status = ['active' => 0, 'completed' => 0, 'pending' => 0, 'overdue' => 0];

try {
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $stmt = $con->prepare("SELECT COALESCE(SUM(principal_amount), 0) as total FROM loans WHERE status IN ('approved', 'paid', 'defaulted') AND TO_CHAR(release_date, 'YYYY-MM') = ?");
        $stmt->execute([$month]);
        $monthly_releases[] = ['month' => date('M Y', strtotime($month)), 'value' => $stmt->fetch(PDO::FETCH_ASSOC)['total']];
        
        $stmt = $con->prepare("SELECT COALESCE(SUM(amount), 0) as total FROM loan_repayment WHERE TO_CHAR(paid_date, 'YYYY-MM') = ?");
        $stmt->execute([$month]);
        $monthly_collections[] = ['month' => date('M Y', strtotime($month)), 'value' => $stmt->fetch(PDO::FETCH_ASSOC)['total']];
    }
    
    // Map stored statuses onto the chart's buckets.
    $stmt = $con->query("
        SELECT CASE
                 WHEN status = 'paid' THEN 'completed'
                 WHEN status = 'pending' THEN 'pending'
                 WHEN status = 'defaulted' OR (status = 'approved' AND maturity_date < CURRENT_DATE) THEN 'overdue'
                 WHEN status = 'approved' THEN 'active'
               END AS bucket,
               COUNT(*) AS count
        FROM loans
        GROUP BY 1
    ");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (isset($loan_status[$row['bucket']])) {
            $loan_status[$row['bucket']] = (int)$row['count'];
        }
    }
} catch (PDOException $e) {
    $chart_error = 'Could not load chart data: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($chart_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($chart_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-pie me-2"></i><?php echo __('charts_analytics'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('charts_analytics'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <select class="form-select" id="periodFilter">
            <option value="12"><?php echo __('last_12_months'); ?></option>
            <option value="6"><?php echo __('last_6_months'); ?></option>
            <option value="3"><?php echo __('last_3_months'); ?></option>
        </select>
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i><?php echo __('loan_releases_vs_collections'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="releasesCollectionsChart" height="300"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i><?php echo __('loan_status'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="loanStatusChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i><?php echo __('monthly_disbursements'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="disbursementsChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-bar me-2"></i><?php echo __('monthly_collections'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="collectionsChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-area me-2"></i><?php echo __('outstanding_portfolio'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="portfolioChart" height="250"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-percent me-2"></i><?php echo __('recovery_rate'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="recoveryChart" height="250"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthLabels = <?php echo json_encode(array_column($monthly_releases, 'month')); ?>;
    const releaseData = <?php echo json_encode(array_column($monthly_releases, 'value')); ?>;
    const collectionData = <?php echo json_encode(array_column($monthly_collections, 'value')); ?>;
    const statusData = <?php echo json_encode(array_values($loan_status)); ?>;
    
    new Chart(document.getElementById('releasesCollectionsChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '<?php echo __('disbursements'); ?>',
                data: releaseData,
                borderColor: '#2c7be5',
                backgroundColor: 'rgba(44, 123, 229, 0.1)',
                fill: true,
                tension: 0.4
            }, {
                label: '<?php echo __('collections'); ?>',
                data: collectionData,
                borderColor: '#00d97e',
                backgroundColor: 'rgba(0, 217, 126, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'top' } }
        }
    });
    
    new Chart(document.getElementById('loanStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['<?php echo __('active'); ?>', '<?php echo __('completed'); ?>', '<?php echo __('pending'); ?>', '<?php echo __('overdue'); ?>'],
            datasets: [{
                data: statusData,
                backgroundColor: ['#2c7be5', '#00d97e', '#f6c343', '#e63757']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });
    
    new Chart(document.getElementById('disbursementsChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '<?php echo __('disbursements'); ?>',
                data: releaseData,
                backgroundColor: '#2c7be5'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    new Chart(document.getElementById('collectionsChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '<?php echo __('collections'); ?>',
                data: collectionData,
                backgroundColor: '#00d97e'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    new Chart(document.getElementById('portfolioChart'), {
        type: 'line',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '<?php echo __('outstanding'); ?>',
                data: releaseData.map((r, i) => r - (collectionData[i] || 0)),
                borderColor: '#f6c343',
                backgroundColor: 'rgba(246, 195, 67, 0.2)',
                fill: true
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    
    new Chart(document.getElementById('recoveryChart'), {
        type: 'bar',
        data: {
            labels: monthLabels,
            datasets: [{
                label: '<?php echo __('recovery_rate'); ?> %',
                data: releaseData.map((r, i) => r > 0 ? Math.min(100, Math.round((collectionData[i] / r) * 100)) : 0),
                backgroundColor: releaseData.map((r, i) => {
                    const rate = r > 0 ? (collectionData[i] / r) * 100 : 0;
                    return rate >= 80 ? '#00d97e' : rate >= 50 ? '#f6c343' : '#e63757';
                })
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { max: 100 } } }
    });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
