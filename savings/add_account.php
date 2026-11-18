<?php
$base_path = '../';
$page_title = 'Add Savings Account';
$current_page = 'savings';

require_once $base_path . 'helpers/connect.php';

$message = '';
$borrowers = [];
$products = [];

try {
    $stmt = $con->query("SELECT id, fname, lname FROM borrower ORDER BY fname, lname");
    $borrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT * FROM savings_products WHERE active = TRUE ORDER BY name");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $borrower_id = $_POST['borrower_id'] ?? '';
    $product_id = $_POST['product_id'] ?? null;
    $initial_deposit = floatval($_POST['initial_deposit'] ?? 0);
    
    if ($borrower_id) {
        try {
            $con->beginTransaction();
            $account_number = 'SAV' . date('Ymd') . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            
            $stmt = $con->prepare("INSERT INTO savings_accounts (borrower_id, product_id, account_number, balance, status) VALUES (?, ?, ?, ?, 'active') RETURNING id");
            $stmt->execute([$borrower_id, $product_id ?: null, $account_number, $initial_deposit]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $account_id = $result['id'];
            
            if ($initial_deposit > 0) {
                $stmt = $con->prepare("INSERT INTO savings_transactions (account_id, type, amount, balance_after, description, created_by) VALUES (?, 'deposit', ?, ?, 'Initial deposit', ?)");
                $stmt->execute([$account_id, $initial_deposit, $initial_deposit, $_SESSION['user_id'] ?? null]);
            }
            $con->commit();

            header("Location: view_accounts.php?success=1");
            exit;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $message = '<div class="alert alert-danger">Error creating account: ' . htmlspecialchars($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-warning">Please select a borrower.</div>';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Add Savings Account</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_accounts.php">Savings Accounts</a></li>
            <li class="breadcrumb-item active">Add Account</li>
        </ol>
    </nav>
</div>

<?php echo $message; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle me-2"></i>New Savings Account</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Account Holder (Borrower) <span class="text-danger">*</span></label>
                        <select name="borrower_id" class="form-select" required>
                            <option value="">Select a borrower...</option>
                            <?php foreach ($borrowers as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['fname'] . ' ' . $b['lname']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Savings Product</label>
                        <select name="product_id" class="form-select">
                            <option value="">Standard Savings</option>
                            <?php foreach ($products as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?> (<?php echo $p['interest_rate']; ?>% interest)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Initial Deposit</label>
                        <div class="input-group">
                            <span class="input-group-text">TZS </span>
                            <input type="number" name="initial_deposit" class="form-control" step="0.01" min="0" value="0">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Create Account
                        </button>
                        <a href="view_accounts.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
