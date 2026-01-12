<?php
$base_path = '../';
$page_title = 'Investors';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';

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
        <h1>Investors</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Investors</li>
            </ol>
        </nav>
    </div>
    <a href="add_investor.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Investor
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
                <p>Total Investments</p>
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
                <p>Active Investors</p>
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
                <p>Interest Payable</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i>All Investors</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i> Export</button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Investor</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Total Invested</th>
                        <th>Interest Earned</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($investors)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No investors found. <a href="add_investor.php">Add one now</a>.
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
                                <?php echo ucfirst($investor['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_investor.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_investment.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-success" title="Add Investment">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="edit_investor.php?id=<?php echo $investor['id']; ?>" class="btn btn-outline-secondary" title="Edit">
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
