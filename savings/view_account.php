<?php
$base_path = '../';
$page_title = 'Account Details';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';

$account_id = $_GET['id'] ?? 0;
$account = null;
$transactions = [];

try {
    $stmt = $con->prepare("SELECT sa.*, b.fname, b.lname, sp.name as product_name, sp.interest_rate FROM savings_accounts sa LEFT JOIN borrower b ON sa.borrower_id = b.id LEFT JOIN savings_products sp ON sa.product_id = sp.id WHERE sa.id = ?");
    $stmt->execute([$account_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($account) {
        $stmt = $con->prepare("SELECT * FROM savings_transactions WHERE account_id = ? ORDER BY created_at DESC LIMIT 50");
        $stmt->execute([$account_id]);
        $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
}

if (!$account) {
    header("Location: view_accounts.php");
    exit;
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Account Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="view_accounts.php">Savings Accounts</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($account['account_number']); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <a href="deposit.php?id=<?php echo $account_id; ?>" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Deposit
        </a>
        <a href="withdraw.php?id=<?php echo $account_id; ?>" class="btn btn-warning">
            <i class="fas fa-minus me-2"></i>Withdraw
        </a>
    </div>
</div>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success">
    <?php echo $_GET['success'] === 'deposit' ? 'Deposit successful!' : 'Withdrawal successful!'; ?>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card savings-card">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($account['balance'], 2); ?></h3>
                <p>Current Balance</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($account['interest_earned'], 2); ?></h3>
                <p>Interest Earned</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($transactions); ?></h3>
                <p>Transactions</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Account Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Account Number:</strong><br><?php echo htmlspecialchars($account['account_number']); ?></p>
                <p><strong>Account Holder:</strong><br><?php echo htmlspecialchars($account['fname'] . ' ' . $account['lname']); ?></p>
                <p><strong>Product:</strong><br><?php echo htmlspecialchars($account['product_name'] ?? 'Standard Savings'); ?></p>
                <p><strong>Interest Rate:</strong><br><?php echo $account['interest_rate'] ?? 0; ?>% per year</p>
                <p><strong>Status:</strong><br>
                    <span class="badge badge-<?php echo $account['status'] === 'active' ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($account['status']); ?>
                    </span>
                </p>
                <p><strong>Opened:</strong><br><?php echo date('M d, Y', strtotime($account['opened_at'])); ?></p>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i>Transaction History</h5>
                <button class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-download me-1"></i>Export
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Amount</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($transactions)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No transactions yet</td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($transactions as $tx): ?>
                            <tr>
                                <td><?php echo date('M d, Y H:i', strtotime($tx['created_at'])); ?></td>
                                <td>
                                    <?php if ($tx['type'] === 'deposit'): ?>
                                    <span class="badge bg-success">Deposit</span>
                                    <?php elseif ($tx['type'] === 'withdrawal'): ?>
                                    <span class="badge bg-warning text-dark">Withdrawal</span>
                                    <?php else: ?>
                                    <span class="badge bg-info"><?php echo ucfirst($tx['type']); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($tx['description'] ?? '-'); ?></td>
                                <td class="<?php echo $tx['type'] === 'deposit' ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo $tx['type'] === 'deposit' ? '+' : '-'; ?>TZS <?php echo number_format($tx['amount'], 2); ?>
                                </td>
                                <td>TZS <?php echo number_format($tx['balance_after'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
