<?php
$base_path = '../';
$page_title = 'Missed Repayments Sheet';
$current_page = 'collection_sheets';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$missed = [];
$today = date('Y-m-d');
try {
    $stmt = $con->prepare("
        SELECT lr.*, l.id as loan_id, l.principal_amount, b.fname, b.lname, b.mobile
        FROM loan_repayment lr
        LEFT JOIN loans l ON lr.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE DATE(lr.due_date) < ? AND (lr.status IS NULL OR lr.status != 'paid')
        ORDER BY lr.due_date DESC
    ");
    $stmt->execute([$today]);
    $missed = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_missed = array_sum(array_column($missed, 'amount'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('missed_repayments'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('collection_sheets'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('missed_repayments'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($missed); ?></h3>
                <p><?php echo __('missed_repayments'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_missed, 2); ?></h3>
                <p><?php echo __('total_amount'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('missed_repayments'); ?></h5>
        <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('due_date'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                        <th><?php echo __('days_late'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($missed)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_missed_repayments'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($missed as $item): ?>
                    <?php 
                    $due_date = strtotime($item['due_date'] ?? 'now');
                    $days_late = floor((time() - $due_date) / 86400);
                    ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['mobile'] ?? '-'); ?></td>
                        <td>#<?php echo $item['loan_id']; ?></td>
                        <td><?php echo date('M d, Y', $due_date); ?></td>
                        <td>TZS <?php echo number_format($item['amount'] ?? 0, 2); ?></td>
                        <td><span class="badge bg-danger"><?php echo $days_late; ?> <?php echo __('days'); ?></span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $base_path; ?>repayments/add_repayment.php?loan_id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-success" title="<?php echo __('record_payment'); ?>">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="<?php echo $base_path; ?>loans/view_loan_details.php?id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
