<?php
$base_path = '../';
$page_title = 'Repayments Charts';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$monthly_data = [];
try {
    $stmt = $con->query("
        SELECT 
            TO_CHAR(collection_date, 'Mon YYYY') as month,
            EXTRACT(MONTH FROM collection_date) as month_num,
            EXTRACT(YEAR FROM collection_date) as year,
            SUM(amount) as total
        FROM repayments
        WHERE collection_date >= CURRENT_DATE - INTERVAL '12 months'
        GROUP BY TO_CHAR(collection_date, 'Mon YYYY'), EXTRACT(MONTH FROM collection_date), EXTRACT(YEAR FROM collection_date)
        ORDER BY year, month_num
    ");
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$payment_methods = [];
try {
    $stmt = $con->query("
        SELECT 
            COALESCE(payment_method, 'Cash') as method,
            SUM(amount) as total
        FROM repayments
        GROUP BY payment_method
    ");
    $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-chart-bar text-primary me-2"></i><?php echo __('repayments_charts'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_repayments.php"><?php echo __('repayments'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('repayments_charts'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-line me-2"></i><?php echo __('monthly_collections'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="300"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-chart-pie me-2"></i><?php echo __('payment_methods'); ?></h5>
            </div>
            <div class="card-body">
                <canvas id="methodsChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const monthlyLabels = <?php echo json_encode(array_column($monthly_data, 'month')); ?>;
    const monthlyValues = <?php echo json_encode(array_map(function($r) { return floatval($r['total']); }, $monthly_data)); ?>;
    
    new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: '<?php echo __('collections'); ?>',
                data: monthlyValues,
                backgroundColor: 'rgba(40, 167, 69, 0.8)',
                borderColor: 'rgb(40, 167, 69)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
    
    const methodLabels = <?php echo json_encode(array_column($payment_methods, 'method')); ?>;
    const methodValues = <?php echo json_encode(array_map(function($r) { return floatval($r['total']); }, $payment_methods)); ?>;
    
    new Chart(document.getElementById('methodsChart'), {
        type: 'doughnut',
        data: {
            labels: methodLabels,
            datasets: [{
                data: methodValues,
                backgroundColor: ['#2c7be5', '#28a745', '#ffc107', '#dc3545', '#6c757d']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
