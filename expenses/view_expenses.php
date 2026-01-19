<?php
$base_path = '../';
$page_title = 'View Expenses';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$expenses = [];
$total_expenses = 0;

try {
    $stmt = $con->query("
        SELECT e.*, v.name as vendor_name, et.name as expense_type
        FROM expenses e
        LEFT JOIN vendors v ON e.vendor_id = v.id
        LEFT JOIN expense_types et ON e.expense_type_id = et.id
        ORDER BY e.expense_date DESC
    ");
    $expenses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_expenses = array_sum(array_column($expenses, 'amount'));
} catch (PDOException $e) {
    $expenses = [
        ['id' => 1, 'expense_date' => date('Y-m-d'), 'vendor_name' => 'Office Supplies Co.', 'expense_type' => 'Office Supplies', 'description' => 'Stationery', 'amount' => 150000],
        ['id' => 2, 'expense_date' => date('Y-m-d', strtotime('-1 day')), 'vendor_name' => 'Tech Solutions', 'expense_type' => 'IT Equipment', 'description' => 'Computer repair', 'amount' => 350000],
        ['id' => 3, 'expense_date' => date('Y-m-d', strtotime('-2 days')), 'vendor_name' => 'Utility Company', 'expense_type' => 'Utilities', 'description' => 'Electricity bill', 'amount' => 200000],
    ];
    $total_expenses = array_sum(array_column($expenses, 'amount'));
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-invoice-dollar text-primary me-2"></i><?php echo __('view_expenses'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('expenses'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="upload_csv.php" class="btn btn-outline-primary">
            <i class="fas fa-upload me-2"></i><?php echo __('upload_csv'); ?>
        </a>
        <a href="add_expense.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo __('add_expense'); ?>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_expenses, 2); ?></h3>
                <p><?php echo __('total_expenses'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($expenses); ?></h3>
                <p><?php echo __('total_transactions'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo date('M Y'); ?></h3>
                <p><?php echo __('current_period'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('expenses_list'); ?></h5>
        <div class="d-flex gap-2">
            <input type="date" class="form-control form-control-sm" style="width: 140px;">
            <input type="date" class="form-control form-control-sm" style="width: 140px;">
            <button class="btn btn-sm btn-outline-primary"><i class="fas fa-filter"></i></button>
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('vendor'); ?></th>
                        <th><?php echo __('expense_type'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_expenses_found'); ?>. <a href="add_expense.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($expenses as $expense): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($expense['expense_date'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($expense['vendor_name'] ?? '-'); ?></strong></td>
                        <td><span class="badge bg-info"><?php echo htmlspecialchars($expense['expense_type'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($expense['description'] ?? '-'); ?></td>
                        <td><strong class="text-danger">TZS <?php echo number_format($expense['amount'], 2); ?></strong></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_expense.php?id=<?php echo $expense['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
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
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end"><?php echo __('total'); ?>:</th>
                        <th colspan="2">TZS <?php echo number_format($total_expenses, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
