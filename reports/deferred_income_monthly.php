<?php
$base_path = '../';
$page_title = 'Monthly Deferred Income Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$monthly_data = [];
$selected_year = $_GET['year'] ?? date('Y');

try {
    $stmt = $con->prepare("
        SELECT 
            EXTRACT(MONTH FROM l.release_date) as month,
            COUNT(l.id) as loan_count,
            SUM(l.principal_amount * l.loan_interest / 100) as total_interest
        FROM loans l
        WHERE l.status = 'approved' 
        AND EXTRACT(YEAR FROM l.release_date) = ?
        GROUP BY EXTRACT(MONTH FROM l.release_date)
        ORDER BY month
    ");
    $stmt->execute([$selected_year]);
    $monthly_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-calendar-alt me-2"></i><?php echo __('deferred_income_monthly'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('deferred_income_monthly'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('year'); ?></label>
                <select name="year" class="form-select">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?php echo $y; ?>" <?php echo $y == $selected_year ? 'selected' : ''; ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('monthly_deferred_income'); ?> - <?php echo $selected_year; ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('month'); ?></th>
                        <th><?php echo __('loans'); ?></th>
                        <th><?php echo __('deferred_income'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $monthly_map = [];
                    foreach ($monthly_data as $data) {
                        $monthly_map[(int)$data['month']] = $data;
                    }
                    
                    for ($m = 1; $m <= 12; $m++): 
                        $data = $monthly_map[$m] ?? ['loan_count' => 0, 'total_interest' => 0];
                    ?>
                    <tr>
                        <td><strong><?php echo $months[$m - 1]; ?></strong></td>
                        <td><?php echo $data['loan_count']; ?></td>
                        <td class="text-warning">TZS <?php echo number_format($data['total_interest'] ?? 0, 2); ?></td>
                    </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
