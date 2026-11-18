<?php
$base_path = '../';
$page_title = 'View Other Income';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$income_entries = [];
$total_income = 0;

try {
    $stmt = $con->query("
        SELECT oi.*, c.name as customer_name, it.name as income_type
        FROM other_income oi
        LEFT JOIN other_income_customers c ON oi.customer_id = c.id
        LEFT JOIN other_income_types it ON oi.income_type_id = it.id
        ORDER BY oi.income_date DESC
    ");
    $income_entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_income = array_sum(array_column($income_entries, 'amount'));
} catch (PDOException $e) {
    $income_entries = [];
    $load_error = 'Could not load data: ' . $e->getMessage();
    $total_income = array_sum(array_column($income_entries, 'amount'));
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-hand-holding-usd text-primary me-2"></i><?php echo __('view_other_income'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('other_income'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="upload_csv.php" class="btn btn-outline-primary">
            <i class="fas fa-upload me-2"></i><?php echo __('upload_csv'); ?>
        </a>
        <a href="add_income.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?php echo __('add_other_income'); ?>
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_income, 2); ?></h3>
                <p><?php echo __('total_income'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($income_entries); ?></h3>
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
        <h5><i class="fas fa-list me-2"></i><?php echo __('income_list'); ?></h5>
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
                        <th><?php echo __('customer'); ?></th>
                        <th><?php echo __('income_type'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($income_entries)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_income_found'); ?>. <a href="add_income.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($income_entries as $income): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($income['income_date'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($income['customer_name'] ?? '-'); ?></strong></td>
                        <td><span class="badge bg-success"><?php echo htmlspecialchars($income['income_type'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($income['description'] ?? '-'); ?></td>
                        <td><strong class="text-success">TZS <?php echo number_format($income['amount'], 2); ?></strong></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="edit_income.php?id=<?php echo $income['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
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
                        <th colspan="2">TZS <?php echo number_format($total_income, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
