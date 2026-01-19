<?php
$base_path = '../';
$page_title = 'Deferred Income Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$deferred_items = [];
$total_deferred = 0;

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname,
               (l.principal_amount * l.loan_interest / 100) as total_interest,
               COALESCE((SELECT SUM(lr.interest) FROM loan_repayment lr WHERE lr.loan_id = l.id), 0) as earned_interest
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE l.status = 'approved'
        ORDER BY l.id DESC
    ");
    $deferred_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($deferred_items as &$item) {
        $item['deferred_amount'] = ($item['total_interest'] ?? 0) - ($item['earned_interest'] ?? 0);
        $total_deferred += max(0, $item['deferred_amount']);
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-hourglass-half me-2"></i><?php echo __('deferred_income'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('deferred_income'); ?></li>
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
            <div class="stat-icon warning">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_deferred, 2); ?></h3>
                <p><?php echo __('total_deferred_income'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($deferred_items); ?></h3>
                <p><?php echo __('active_loans'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('deferred_income_details'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('total_interest'); ?></th>
                        <th><?php echo __('earned_interest'); ?></th>
                        <th><?php echo __('deferred_income'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($deferred_items)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($deferred_items as $item): ?>
                    <tr>
                        <td><strong>#<?php echo $item['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td>TZS <?php echo number_format($item['total_interest'] ?? 0, 2); ?></td>
                        <td>TZS <?php echo number_format($item['earned_interest'] ?? 0, 2); ?></td>
                        <td class="text-warning"><strong>TZS <?php echo number_format(max(0, $item['deferred_amount']), 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
