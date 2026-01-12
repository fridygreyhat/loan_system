<?php
$base_path = '../';
$page_title = 'Loan Products';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';

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
        <h1>Loan Products</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="settings.php">Settings</a></li>
                <li class="breadcrumb-item active">Loan Products</li>
            </ol>
        </nav>
    </div>
    <a href="add_loan_product.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Product
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-box me-2"></i>All Loan Products</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Interest Rate</th>
                        <th>Interest Method</th>
                        <th>Min Amount</th>
                        <th>Max Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No loan products found. <a href="add_loan_product.php">Add one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($product['name']); ?></strong></td>
                        <td><?php echo $product['interest_rate'] ?? 0; ?>%</td>
                        <td><?php echo htmlspecialchars($product['interest_method'] ?? 'Flat'); ?></td>
                        <td>$<?php echo number_format($product['min_amount'] ?? 0, 2); ?></td>
                        <td>$<?php echo number_format($product['max_amount'] ?? 0, 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($product['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($product['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="add_loan_product_edit.php?id=<?php echo $product['id']; ?>" class="btn btn-outline-primary" title="Edit">
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
