<?php
$base_path = '../';
$page_title = 'Savings Accounts';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';

$accounts = [];
try {
    $stmt = $con->query("
        SELECT sa.*, b.fname, b.lname, sp.name as product_name, sp.interest_rate
        FROM savings_accounts sa
        LEFT JOIN borrower b ON sa.borrower_id = b.id
        LEFT JOIN savings_products sp ON sa.product_id = sp.id
        ORDER BY sa.opened_at DESC
    ");
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Savings Accounts</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Savings Accounts</li>
            </ol>
        </nav>
    </div>
    <a href="add_account.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Account
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-piggy-bank"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($accounts, 'balance')), 2); ?></h3>
                <p>Total Savings</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($accounts); ?></h3>
                <p>Active Accounts</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($accounts, 'interest_earned')), 2); ?></h3>
                <p>Interest Earned</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Savings Accounts</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-filter"></i></button>
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Account #</th>
                        <th>Account Holder</th>
                        <th>Product</th>
                        <th>Balance</th>
                        <th>Interest Rate</th>
                        <th>Interest Earned</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No savings accounts found. <a href="add_account.php">Create one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($accounts as $account): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($account['account_number']); ?></strong></td>
                        <td><?php echo htmlspecialchars($account['fname'] . ' ' . $account['lname']); ?></td>
                        <td><?php echo htmlspecialchars($account['product_name'] ?? 'Standard'); ?></td>
                        <td><strong>TZS <?php echo number_format($account['balance'], 2); ?></strong></td>
                        <td><?php echo $account['interest_rate'] ?? 0; ?>%</td>
                        <td class="text-success">TZS <?php echo number_format($account['interest_earned'], 2); ?></td>
                        <td>
                            <span class="badge badge-<?php echo $account['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($account['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_account.php?id=<?php echo $account['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="deposit.php?id=<?php echo $account['id']; ?>" class="btn btn-outline-success" title="Deposit">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="withdraw.php?id=<?php echo $account['id']; ?>" class="btn btn-outline-warning" title="Withdraw">
                                    <i class="fas fa-minus"></i>
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
