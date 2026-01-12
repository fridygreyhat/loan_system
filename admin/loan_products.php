<?php
$base_path = '../';
$page_title = 'Loan Products';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$products = [];
try {
    $stmt = $con->query("SELECT * FROM loan_product ORDER BY name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('loan_products'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="settings.php"><?php echo __('settings'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loan_products'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_loan_product.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add'); ?> <?php echo __('product'); ?>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-box me-2"></i><?php echo __('loan_products'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('name'); ?></th>
                        <th><?php echo __('interest_rate'); ?></th>
                        <th><?php echo __('interest'); ?></th>
                        <th><?php echo __('amount'); ?> (Min)</th>
                        <th><?php echo __('amount'); ?> (Max)</th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_loans_found'); ?>. <a href="add_loan_product.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo $product['interest_rate'] ?? 0; ?>%</td>
                        <td><?php echo htmlspecialchars($product['interest_method'] ?? 'Flat'); ?></td>
                        <td>TZS <?php echo number_format($product['min_amount'] ?? 0, 2); ?></td>
                        <td>TZS <?php echo number_format($product['max_amount'] ?? 0, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($product['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo __($product['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="add_loan_product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
                                    <i class="fas fa-edit"></i>
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
