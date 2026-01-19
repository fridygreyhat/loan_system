<?php
$base_path = '../';
$page_title = 'View Vendors';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$vendors = [];
$total_vendors = 0;

try {
    $stmt = $con->query("
        SELECT v.*, 
            COALESCE(SUM(e.amount), 0) as total_expenses,
            COUNT(e.id) as expense_count
        FROM vendors v
        LEFT JOIN expenses e ON v.id = e.vendor_id
        GROUP BY v.id
        ORDER BY v.name
    ");
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_vendors = count($vendors);
} catch (PDOException $e) {
    $vendors = [
        ['id' => 1, 'name' => 'Office Supplies Co.', 'email' => 'supplies@example.com', 'phone' => '+255712345678', 'status' => 'active', 'total_expenses' => 500000, 'expense_count' => 15],
        ['id' => 2, 'name' => 'Tech Solutions Ltd', 'email' => 'tech@example.com', 'phone' => '+255723456789', 'status' => 'active', 'total_expenses' => 1200000, 'expense_count' => 8],
        ['id' => 3, 'name' => 'Cleaning Services', 'email' => 'clean@example.com', 'phone' => '+255734567890', 'status' => 'active', 'total_expenses' => 350000, 'expense_count' => 12],
    ];
    $total_vendors = count($vendors);
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-store text-primary me-2"></i><?php echo __('view_vendors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_expenses.php"><?php echo __('expenses'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('view_vendors'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_vendor.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add_vendor'); ?>
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $total_vendors; ?></h3>
                <p><?php echo __('total_vendors'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count(array_filter($vendors, fn($v) => $v['status'] === 'active')); ?></h3>
                <p><?php echo __('active_vendors'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-money-bill"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($vendors, 'total_expenses')), 2); ?></h3>
                <p><?php echo __('total_spent'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('vendors_list'); ?></h5>
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
                        <th><?php echo __('vendor_name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('total_expenses'); ?></th>
                        <th><?php echo __('transactions'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendors)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_vendors_found'); ?>. <a href="add_vendor.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($vendors as $vendor): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($vendor['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($vendor['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($vendor['phone'] ?? '-'); ?></td>
                        <td><strong>TZS <?php echo number_format($vendor['total_expenses'], 2); ?></strong></td>
                        <td><?php echo $vendor['expense_count']; ?></td>
                        <td>
                            <span class="badge badge-<?php echo $vendor['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo __($vendor['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_vendor.php?id=<?php echo $vendor['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
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
