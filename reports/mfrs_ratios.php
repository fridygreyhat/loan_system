<?php
$base_path = '../';
$page_title = 'MFRS Ratios';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$ratios = [];

try {
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) as total_portfolio FROM loans WHERE status = 'approved'");
    $total_portfolio = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) as par_30 FROM loans WHERE status = 'approved' AND maturity_date < CURRENT_DATE - INTERVAL '30 days'");
    $par_30 = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(lr.amount), 0) FROM loan_repayment lr");
    $total_collections = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COALESCE(SUM(principal_amount), 0) FROM loans WHERE status = 'approved' AND release_date >= DATE_TRUNC('year', CURRENT_DATE)");
    $disbursed_ytd = $stmt->fetchColumn() ?: 0;
    
    $ratios = [
        'par_30' => $total_portfolio > 0 ? ($par_30 / $total_portfolio) * 100 : 0,
        'collection_rate' => $total_portfolio > 0 ? ($total_collections / $total_portfolio) * 100 : 0,
        'total_portfolio' => $total_portfolio,
        'total_collections' => $total_collections,
        'disbursed_ytd' => $disbursed_ytd
    ];
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-chart-line me-2"></i><?php echo __('mfrs_ratios'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('mfrs_ratios'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-chart-pie"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($ratios['total_portfolio'], 2); ?></h3>
                <p><?php echo __('total_portfolio'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon <?php echo ($ratios['par_30'] ?? 0) > 10 ? 'danger' : 'success'; ?>">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($ratios['par_30'] ?? 0, 2); ?>%</h3>
                <p><?php echo __('par_30'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($ratios['collection_rate'] ?? 0, 2); ?>%</h3>
                <p><?php echo __('collection_rate'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($ratios['disbursed_ytd'], 2); ?></h3>
                <p><?php echo __('disbursed_ytd'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-table me-2"></i><?php echo __('key_ratios'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('ratio'); ?></th>
                        <th><?php echo __('value'); ?></th>
                        <th><?php echo __('benchmark'); ?></th>
                        <th><?php echo __('status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong><?php echo __('portfolio_at_risk_30'); ?></strong></td>
                        <td><?php echo number_format($ratios['par_30'] ?? 0, 2); ?>%</td>
                        <td>&lt; 5%</td>
                        <td>
                            <?php if (($ratios['par_30'] ?? 0) <= 5): ?>
                            <span class="badge bg-success"><?php echo __('good'); ?></span>
                            <?php elseif (($ratios['par_30'] ?? 0) <= 10): ?>
                            <span class="badge bg-warning"><?php echo __('moderate'); ?></span>
                            <?php else: ?>
                            <span class="badge bg-danger"><?php echo __('high_risk'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong><?php echo __('collection_rate'); ?></strong></td>
                        <td><?php echo number_format($ratios['collection_rate'] ?? 0, 2); ?>%</td>
                        <td>&gt; 95%</td>
                        <td>
                            <?php if (($ratios['collection_rate'] ?? 0) >= 95): ?>
                            <span class="badge bg-success"><?php echo __('excellent'); ?></span>
                            <?php elseif (($ratios['collection_rate'] ?? 0) >= 85): ?>
                            <span class="badge bg-warning"><?php echo __('good'); ?></span>
                            <?php else: ?>
                            <span class="badge bg-danger"><?php echo __('needs_improvement'); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
