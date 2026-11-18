<?php
$base_path = '../';
$page_title = 'Savings Fee Report';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$fees = [];
$total_fees = 0;

try {
    $stmt = $con->query("
        SELECT sf.*, sa.account_number, b.fname, b.lname
        FROM savings_fees sf
        LEFT JOIN savings_accounts sa ON sf.account_id = sa.id
        LEFT JOIN borrower b ON sa.borrower_id = b.id
        ORDER BY sf.fee_date DESC
    ");
    $fees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_fees = array_sum(array_column($fees, 'amount'));
} catch (PDOException $e) {
    $fees = [];
    $load_error = 'Could not load data: ' . $e->getMessage();
    $total_fees = array_sum(array_column($fees, 'amount'));
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-receipt text-primary me-2"></i><?php echo __('savings_fee_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php"><?php echo __('savings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('savings_fee_report'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="fas fa-print me-2"></i><?php echo __('print'); ?>
        </button>
        <button class="btn btn-primary">
            <i class="fas fa-download me-2"></i><?php echo __('export'); ?>
        </button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_fees, 2); ?></h3>
                <p><?php echo __('total_fees_collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($fees); ?></h3>
                <p><?php echo __('total_transactions'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calculator"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo count($fees) > 0 ? number_format($total_fees / count($fees), 2) : '0.00'; ?></h3>
                <p><?php echo __('average_fee'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-table me-2"></i><?php echo __('fee_transactions'); ?></h5>
        <div class="d-flex gap-2">
            <input type="date" class="form-control form-control-sm" style="width: 150px;">
            <input type="date" class="form-control form-control-sm" style="width: 150px;">
            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-filter"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('account_number'); ?></th>
                        <th><?php echo __('account_holder'); ?></th>
                        <th><?php echo __('fee_type'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fees)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($fees as $fee): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($fee['fee_date'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($fee['account_number']); ?></strong></td>
                        <td><?php echo htmlspecialchars($fee['fname'] . ' ' . $fee['lname']); ?></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($fee['fee_type']); ?></span></td>
                        <td><strong>TZS <?php echo number_format($fee['amount'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end"><?php echo __('total'); ?>:</th>
                        <th>TZS <?php echo number_format($total_fees, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
