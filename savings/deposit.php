<?php
$base_path = '../';
$page_title = 'Deposit';
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
    $description = $_POST['description'] ?? 'Deposit';
    $reference = $_POST['reference'] ?? '';
    
    if ($amount > 0) {
        try {
            // Atomic: the database adds to the current balance, so concurrent postings can't overwrite each other.
            $con->beginTransaction();
            $stmt = $con->prepare("UPDATE savings_accounts SET balance = balance + ? WHERE id = ? RETURNING balance");
            $stmt->execute([$amount, $account_id]);
            $new_balance = $stmt->fetchColumn();

            $stmt = $con->prepare("INSERT INTO savings_transactions (account_id, type, amount, balance_after, description, reference, created_by) VALUES (?, 'deposit', ?, ?, ?, ?, ?)");
            $stmt->execute([$account_id, $amount, $new_balance, $description, $reference, $_SESSION['user_id'] ?? null]);
            $con->commit();

            header("Location: view_account.php?id=$account_id&success=deposit");
            exit;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $message = '<div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please enter a valid amount.</div>';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Make Deposit</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_accounts.php">Savings Accounts</a></li>
            <li class="breadcrumb-item active">Deposit</li>
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
                <p><strong>Current Balance:</strong> <span class="text-success fs-4">TZS <?php echo number_format($account['balance'], 2); ?></span></p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle me-2"></i>Deposit Funds</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">TZS </span>
                            <input type="number" name="amount" class="form-control form-control-lg" step="0.01" min="0.01" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <input type="text" name="description" class="form-control" value="Deposit" placeholder="e.g., Monthly savings">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Reference Number</label>
                        <input type="text" name="reference" class="form-control" placeholder="e.g., Receipt #">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Make Deposit
                        </button>
                        <a href="view_accounts.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
