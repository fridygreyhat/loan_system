<?php
$base_path = '../';
$page_title = 'Pro-Rata Collections Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$collections = [];
$selected_month = $_GET['month'] ?? date('Y-m');

try {
    $stmt = $con->prepare("
        SELECT 
            lr.*,
            l.principal_amount,
            l.loan_interest,
            b.fname,
            b.lname
        FROM loan_repayment lr
        LEFT JOIN loans l ON lr.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE TO_CHAR(lr.paid_date, 'YYYY-MM') = ?
        ORDER BY lr.paid_date DESC
    ");
    $stmt->execute([$selected_month]);
    $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_principal_collected = array_sum(array_column($collections, 'principal'));
$total_interest_collected = array_sum(array_column($collections, 'interest'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-pie me-2"></i><?php echo __('pro_rata_collections_monthly'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('pro_rata_collections'); ?></li>
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
                <label class="form-label"><?php echo __('month'); ?></label>
                <input type="month" name="month" class="form-control" value="<?php echo $selected_month; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_principal_collected, 2); ?></h3>
                <p><?php echo __('principal_collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_interest_collected, 2); ?></h3>
                <p><?php echo __('interest_collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collections); ?></h3>
                <p><?php echo __('total_payments'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('collections'); ?> - <?php echo date('F Y', strtotime($selected_month . '-01')); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('interest'); ?></th>
                        <th><?php echo __('total'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collections)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_collections_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collections as $item): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($item['paid_date'])); ?></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td>#<?php echo $item['loan_id']; ?></td>
                        <td>TZS <?php echo number_format($item['principal'] ?? 0, 2); ?></td>
                        <td>TZS <?php echo number_format($item['interest'] ?? 0, 2); ?></td>
                        <td class="text-success"><strong>TZS <?php echo number_format($item['amount'] ?? 0, 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
