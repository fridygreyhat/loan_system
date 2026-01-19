<?php
$base_path = '../';
$page_title = 'Loan Products Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$products = [];

try {
    $stmt = $con->query("
        SELECT lp.*, 
               COUNT(l.id) as loan_count,
               COALESCE(SUM(l.principal_amount), 0) as total_disbursed,
               COALESCE(SUM(CASE WHEN l.status = 'approved' THEN l.principal_amount END), 0) as active_amount
        FROM loan_product lp
        LEFT JOIN loans l ON l.loan_product_id = lp.id
        GROUP BY lp.id
        ORDER BY total_disbursed DESC
    ");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$total_disbursed = array_sum(array_column($products, 'total_disbursed'));
$total_loans = array_sum(array_column($products, 'loan_count'));

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-box me-2"></i><?php echo __('loan_products_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loan_products'); ?></li>
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
            <div class="stat-icon info">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_loans; ?></h3>
                <p><?php echo __('total_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_disbursed, 2); ?></h3>
                <p><?php echo __('total_disbursed'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('products_performance'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('product_name'); ?></th>
                        <th><?php echo __('interest_rate'); ?></th>
                        <th><?php echo __('loans'); ?></th>
                        <th><?php echo __('total_disbursed'); ?></th>
                        <th><?php echo __('active_amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_products_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($product['name'] ?? '-'); ?></strong></td>
                        <td><?php echo $product['interest_rate'] ?? 0; ?>%</td>
                        <td><?php echo $product['loan_count']; ?></td>
                        <td>TZS <?php echo number_format($product['total_disbursed'], 2); ?></td>
                        <td class="text-success"><strong>TZS <?php echo number_format($product['active_amount'], 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
