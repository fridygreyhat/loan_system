<?php
$base_path = '../';
$page_title = 'Branches';
$current_page = 'settings';
include '../includes/header.php';
require_once '../helpers/connect.php';

$db = $con;

$branches = [];
try {
    $stmt = $db->query("SELECT * FROM branches ORDER BY name");
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $branches = [];
}

$total_branches = count($branches);
$active_branches = 0;
$total_capital = 0;

foreach ($branches as $branch) {
    if (($branch['status'] ?? 'active') === 'active') {
        $active_branches++;
    }
    $total_capital += $branch['capital'] ?? 0;
}
?>

<div class="content-header">
    <h1><i class="fas fa-building"></i> <?php echo __('branches'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="#"><?php echo __('settings'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('branches'); ?></li>
        </ol>
    </nav>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo number_format($total_branches); ?></h3>
                <p><?php echo __('total_branches'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <h3><?php echo number_format($active_branches); ?></h3>
                <p><?php echo __('active_branches'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-details">
                <h3>TZS <?php echo number_format($total_capital, 0); ?></h3>
                <p><?php echo __('total_capital'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><?php echo __('all_branches'); ?></h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBranchModal">
            <i class="fas fa-plus"></i> <?php echo __('add_branch'); ?>
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('branch_name'); ?></th>
                        <th><?php echo __('branch_code'); ?></th>
                        <th><?php echo __('location'); ?></th>
                        <th><?php echo __('capital'); ?></th>
                        <th><?php echo __('min_loan'); ?></th>
                        <th><?php echo __('max_loan'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($branches)): ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-building fa-3x mb-3 d-block"></i>
                            <?php echo __('no_branches_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($branches as $index => $branch): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($branch['name'] ?? ''); ?></strong></td>
                        <td><?php echo htmlspecialchars($branch['code'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($branch['location'] ?? ''); ?></td>
                        <td>TZS <?php echo number_format($branch['capital'] ?? 0, 0); ?></td>
                        <td>TZS <?php echo number_format($branch['min_loan_amount'] ?? 0, 0); ?></td>
                        <td>TZS <?php echo number_format($branch['max_loan_amount'] ?? 0, 0); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($branch['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($branch['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="<?php echo __('edit'); ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-info" title="<?php echo __('holidays'); ?>">
                                    <i class="fas fa-calendar"></i>
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

<div class="modal fade" id="addBranchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add_branch'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_branch.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('branch_name'); ?> *</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('branch_code'); ?></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g., BR001">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('location'); ?></label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" name="phone" class="form-control" placeholder="+255 XXX XXX XXX">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('capital'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="capital" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('min_loan_amount'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="min_loan_amount" class="form-control" value="50000">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('max_loan_amount'); ?></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="max_loan_amount" class="form-control" value="50000000">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('min_interest_rate'); ?> (%)</label>
                            <input type="number" name="min_interest_rate" class="form-control" value="0" step="0.1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('max_interest_rate'); ?> (%)</label>
                            <input type="number" name="max_interest_rate" class="form-control" value="100" step="0.1">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                    <button type="submit" class="btn btn-primary"><?php echo __('save_branch'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
