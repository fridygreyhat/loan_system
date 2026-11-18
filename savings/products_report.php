<?php
$base_path = '../';
$page_title = 'Savings Products Report';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$products = [];
$total_balance = 0;

try {
    $stmt = $con->query("
        SELECT sp.*, 
            COUNT(sa.id) as account_count,
            COALESCE(SUM(sa.balance), 0) as total_balance,
            COALESCE(SUM(sa.interest_earned), 0) as total_interest
        FROM savings_products sp
        LEFT JOIN savings_accounts sa ON sp.id = sa.product_id
        GROUP BY sp.id
        ORDER BY total_balance DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_balance = array_sum(array_column($products, 'total_balance'));
} catch (PDOException $e) {
    $products = [];
    $load_error = 'Could not load data: ' . $e->getMessage();
    $total_balance = array_sum(array_column($products, 'total_balance'));
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-box text-primary me-2"></i><?php echo __('savings_products_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php"><?php echo __('savings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('savings_products_report'); ?></li>
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
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($products); ?></h3>
                <p><?php echo __('total_products'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo array_sum(array_column($products, 'account_count')); ?></h3>
                <p><?php echo __('total_accounts'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_balance, 2); ?></h3>
                <p><?php echo __('total_savings'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-table me-2"></i><?php echo __('products_breakdown'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('product_name'); ?></th>
                        <th><?php echo __('interest_rate'); ?></th>
                        <th><?php echo __('accounts'); ?></th>
                        <th><?php echo __('total_balance'); ?></th>
                        <th><?php echo __('total_interest'); ?></th>
                        <th><?php echo __('percentage'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <?php $percentage = $total_balance > 0 ? ($product['total_balance'] / $total_balance) * 100 : 0; ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo $product['interest_rate']; ?>%</td>
                        <td><?php echo $product['account_count']; ?></td>
                        <td><strong>TZS <?php echo number_format($product['total_balance'], 2); ?></strong></td>
                        <td class="text-success">TZS <?php echo number_format($product['total_interest'], 2); ?></td>
                        <td>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-primary" style="width: <?php echo $percentage; ?>%">
                                    <?php echo number_format($percentage, 1); ?>%
                                </div>
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
