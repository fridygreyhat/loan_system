<?php
$base_path = '../';
$page_title = 'Collateral Register';
$current_page = 'collateral';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$collaterals = [];
try {
    $stmt = $con->query("
        SELECT c.*, b.fname, b.lname, l.id as loan_id
        FROM collateral c
        LEFT JOIN borrower b ON c.borrower_id = b.id
        LEFT JOIN loans l ON c.loan_id = l.id
        ORDER BY c.id DESC
    ");
    $collaterals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('collateral_register'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('collateral_register'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_collateral.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add'); ?> <?php echo __('collateral'); ?>
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collaterals); ?></h3>
                <p><?php echo __('collateral'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('collateral'); ?></h5>
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
                        <th><?php echo __('name'); ?></th>
                        <th><?php echo __('collateral_type'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('collateral_value'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collaterals)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_collateral_found'); ?>. <a href="add_collateral.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collaterals as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name'] ?? $item['item_name'] ?? '-'); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['type'] ?? $item['collateral_type'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td><?php echo $item['loan_id'] ? '#' . $item['loan_id'] : '-'; ?></td>
                        <td>TZS <?php echo number_format($item['value'] ?? $item['collateral_value'] ?? 0, 2); ?></td>
                        <td>
                            <?php 
                            $status = $item['status'] ?? 'active';
                            $badge_class = $status === 'active' ? 'success' : 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo __($status); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_collateral.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_collateral.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>">
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
