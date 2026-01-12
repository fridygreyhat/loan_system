<?php
$base_path = '../';
$page_title = 'Investors';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$investors = [];
$total_investments = 0;
try {
    $stmt = $con->query("
        SELECT i.*, 
            COALESCE(SUM(ia.investment_amount), 0) as total_invested,
            COALESCE(SUM(ia.interest_earned), 0) as total_interest
        FROM investors i
        LEFT JOIN investor_accounts ia ON i.id = ia.investor_id
        GROUP BY i.id
        ORDER BY i.created_at DESC
    ");
    $investors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT COALESCE(SUM(investment_amount), 0) as total FROM investor_accounts");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_investments = $result['total'];
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('investors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('investors'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_investor.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add_investor'); ?>
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card investor-card">
            <div class="stat-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_investments, 2); ?></h3>
                <p><?php echo __('total_investments'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($investors); ?></h3>
                <p><?php echo __('view_investors'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($investors, 'total_interest')), 2); ?></h3>
                <p><?php echo __('interest'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i><?php echo __('view_investors'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i> <?php echo __('export'); ?></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('investor_name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('investment_amount'); ?></th>
                        <th><?php echo __('interest'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($investors)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_investors_found'); ?>. <a href="add_investor.php"><?php echo __('add_one_now'); ?></a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($investors as $investor): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($investor['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($investor['phone'] ?? '-'); ?></td>
                        <td><strong>TZS <?php echo number_format($investor['total_invested'], 2); ?></strong></td>
                        <td class="text-success">TZS <?php echo number_format($investor['total_interest'], 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $investor['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo __($investor['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_investor.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_investment.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-success" title="<?php echo __('add'); ?>">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="edit_investor.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>">
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
