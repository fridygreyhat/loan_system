<?php
$base_path = '../';
$page_title = 'Savings Report';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$accounts = [];
$total_balance = 0;
$total_interest = 0;
$active_accounts = 0;

try {
    $stmt = $con->query("
        SELECT sa.*, b.fname, b.lname, sp.name as product_name
        FROM savings_accounts sa
        LEFT JOIN borrower b ON sa.borrower_id = b.id
        LEFT JOIN savings_products sp ON sa.product_id = sp.id
        ORDER BY sa.balance DESC
    ");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_balance = array_sum(array_column($accounts, 'balance'));
    $total_interest = array_sum(array_column($accounts, 'interest_earned'));
    $active_accounts = count(array_filter($accounts, fn($a) => $a['status'] === 'active'));
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-alt text-primary me-2"></i><?php echo __('savings_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php"><?php echo __('savings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('savings_report'); ?></li>
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
    <div class="col-md-3">
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_balance, 2); ?></h3>
                <p><?php echo __('total_savings'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($accounts); ?></h3>
                <p><?php echo __('total_accounts'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $active_accounts; ?></h3>
                <p><?php echo __('active_accounts'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_interest, 2); ?></h3>
                <p><?php echo __('total_interest'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-table me-2"></i><?php echo __('account_summary'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
            <select class="form-select form-select-sm" style="width: 150px;">
                <option value=""><?php echo __('all_status'); ?></option>
                <option value="active"><?php echo __('active'); ?></option>
                <option value="inactive"><?php echo __('inactive'); ?></option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('account_number'); ?></th>
                        <th><?php echo __('account_holder'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('balance'); ?></th>
                        <th><?php echo __('interest_earned'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('opened_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($account['account_number']); ?></strong></td>
                        <td><?php echo htmlspecialchars($account['fname'] . ' ' . $account['lname']); ?></td>
                        <td><?php echo htmlspecialchars($account['product_name'] ?? 'Standard'); ?></td>
                        <td><strong>TZS <?php echo number_format($account['balance'], 2); ?></strong></td>
                        <td class="text-success">TZS <?php echo number_format($account['interest_earned'] ?? 0, 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $account['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo __($account['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($account['opened_at'] ?? 'now')); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end"><?php echo __('totals'); ?>:</th>
                        <th>TZS <?php echo number_format($total_balance, 2); ?></th>
                        <th>TZS <?php echo number_format($total_interest, 2); ?></th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
