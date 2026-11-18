<?php
$base_path = '../';
$page_title = 'Collector Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$collectors = [];
$total_collected = 0;

try {
    $stmt = $con->query("
        SELECT s.*, 
               COUNT(DISTINCT l.id) as loans_assigned,
               COALESCE(SUM(lr.amount), 0) as total_collected
        FROM staff s
        JOIN staff_role_types srt ON s.role_id = srt.id AND srt.code = 'collector'
        LEFT JOIN loans l ON l.collector_id = s.id
        LEFT JOIN loan_repayment lr ON lr.loan_id = l.id
        GROUP BY s.id
        ORDER BY total_collected DESC
    ");
    $collectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_collected = array_sum(array_column($collectors, 'total_collected'));
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-tie me-2"></i><?php echo __('collector_report_staff'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('collector'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collectors); ?></h3>
                <p><?php echo __('total_collectors'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_collected, 2); ?></h3>
                <p><?php echo __('total_collected'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('collector_performance'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('collector_name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('loans_assigned'); ?></th>
                        <th><?php echo __('total_collected'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collectors)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_collectors_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collectors as $collector): ?>
                    <tr>
                        <td><?php echo $collector['id']; ?></td>
                        <td><?php echo htmlspecialchars(($collector['fname'] ?? '') . ' ' . ($collector['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($collector['email'] ?? '-'); ?></td>
                        <td><?php echo $collector['loans_assigned']; ?></td>
                        <td class="text-success"><strong>TZS <?php echo number_format($collector['total_collected'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
