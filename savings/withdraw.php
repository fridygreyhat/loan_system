<?php
$base_path = '../';
$page_title = 'Withdraw';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';

$account_id = $_GET['id'] ?? 0;
$account = null;
$message = '';

try {
    $stmt = $con->prepare("SELECT sa.*, b.fname, b.lname FROM savings_accounts sa LEFT JOIN borrower b ON sa.borrower_id = b.id WHERE sa.id = ?");
    $stmt->execute([$account_id]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if (!$account) {
    header("Location: view_accounts.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount'] ?? 0);
    $description = $_POST['description'] ?? 'Withdrawal';
    $reference = $_POST['reference'] ?? '';
    
    if ($amount > 0 && $amount <= $account['balance']) {
        try {
            $new_balance = $account['balance'] - $amount;
            
            $stmt = $con->prepare("UPDATE savings_accounts SET balance = ? WHERE id = ?");
            $stmt->execute([$new_balance, $account_id]);
            
            $stmt = $con->prepare("INSERT INTO savings_transactions (account_id, type, amount, balance_after, description, reference, created_by) VALUES (?, 'withdrawal', ?, ?, ?, ?, 1)");
            $stmt->execute([$account_id, $amount, $new_balance, $description, $reference]);
            
            header("Location: view_account.php?id=$account_id&success=withdraw");
            exit;
        } catch (PDOException $e) {
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else if ($amount > $account['balance']) {
        $message = '<div class="alert alert-danger">Insufficient balance. Available: $' . number_format($account['balance'], 2) . '</div>';
    } else {
        $message = '<div class="alert alert-warning">Please enter a valid amount.</div>';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Make Withdrawal</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_accounts.php">Savings Accounts</a></li>
            <li class="breadcrumb-item active">Withdraw</li>
        </ol>
    </nav>
</div>

<?php echo $message; ?>

<div class="row">
    <div class="col-lg-6">
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Account Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Account Number:</strong> <?php echo htmlspecialchars($account['account_number']); ?></p>
                <p><strong>Account Holder:</strong> <?php echo htmlspecialchars($account['fname'] . ' ' . $account['lname']); ?></p>
                <p><strong>Available Balance:</strong> <span class="text-success fs-4">TZS <?php echo number_format($account['balance'], 2); ?></span></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-minus-circle me-2"></i>Withdraw Funds</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="amount" class="form-control form-control-lg" step="0.01" min="0.01" max="<?php echo $account['balance']; ?>" required autofocus>
                        </div>
                        <small class="text-muted">Maximum: TZS <?php echo number_format($account['balance'], 2); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="Withdrawal">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference" class="form-control">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-minus me-2"></i>Make Withdrawal
                        </button>
                        <a href="view_accounts.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
