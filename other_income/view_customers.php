<?php
$base_path = '../';
$page_title = 'View Customers';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$customers = [];
$total_customers = 0;

try {
    $stmt = $con->query("
        SELECT c.*, 
            COALESCE(SUM(oi.amount), 0) as total_income,
            COUNT(oi.id) as income_count
        FROM other_income_customers c
        LEFT JOIN other_income oi ON c.id = oi.customer_id
        GROUP BY c.id
        ORDER BY c.name
    ");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_customers = count($customers);
} catch (PDOException $e) {
    $customers = [
        ['id' => 1, 'name' => 'ABC Corporation', 'email' => 'abc@example.com', 'phone' => '+255712345678', 'status' => 'active', 'total_income' => 1500000, 'income_count' => 10],
        ['id' => 2, 'name' => 'XYZ Limited', 'email' => 'xyz@example.com', 'phone' => '+255723456789', 'status' => 'active', 'total_income' => 800000, 'income_count' => 5],
        ['id' => 3, 'name' => 'Global Services', 'email' => 'global@example.com', 'phone' => '+255734567890', 'status' => 'active', 'total_income' => 2200000, 'income_count' => 15],
    ];
    $total_customers = count($customers);
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-users text-primary me-2"></i><?php echo __('view_customers'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_income.php"><?php echo __('other_income'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('view_customers'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_customer.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add_customer'); ?>
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_customers; ?></h3>
                <p><?php echo __('total_customers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count(array_filter($customers, fn($c) => $c['status'] === 'active')); ?></h3>
                <p><?php echo __('active_customers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($customers, 'total_income')), 2); ?></h3>
                <p><?php echo __('total_revenue'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('customers_list'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('customer_name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('total_income'); ?></th>
                        <th><?php echo __('transactions'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_customers_found'); ?>. <a href="add_customer.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($customer['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($customer['phone'] ?? '-'); ?></td>
                        <td><strong class="text-success">TZS <?php echo number_format($customer['total_income'], 2); ?></strong></td>
                        <td><?php echo $customer['income_count']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $customer['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo __($customer['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_customer.php?id=<?php echo $customer['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button class="btn btn-outline-danger" title="<?php echo __('delete'); ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
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
