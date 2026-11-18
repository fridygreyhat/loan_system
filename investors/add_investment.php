<?php
$base_path = '../';
$page_title = 'Add Investment';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$investor = null;
$accounts = [];
$products = [];

try {
    $stmt = $con->prepare("SELECT * FROM investors WHERE id = ?");
    $stmt->execute([$id]);
    $investor = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load investor: ' . $e->getMessage();
}

$form = [
    'account_id' => '',
    'amount' => '',
    'interest_rate' => '0',
    'maturity_date' => '',
    'description' => '',
    'reference' => '',
];

if ($investor && $_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($form as $k => $_) {
        $form[$k] = trim((string)($_POST[$k] ?? ''));
    }
    if ($form['interest_rate'] === '') {
        $form['interest_rate'] = '0';
    }
    $is_new = $form['account_id'] === 'new';
    $account_id = (int)$form['account_id'];
    $maturity_ok = true;
    if ($is_new && $form['maturity_date'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $form['maturity_date']);
        $maturity_ok = $d && $d->format('Y-m-d') === $form['maturity_date'];
    }

    if (!$is_new && $account_id <= 0) {
        $error = 'Please choose an account or open a new one.';
    } elseif (!is_numeric($form['amount']) || (float)$form['amount'] <= 0) {
        $error = 'Please enter an amount greater than zero.';
    } elseif ($is_new && (!is_numeric($form['interest_rate']) || (float)$form['interest_rate'] < 0)) {
        $error = 'Interest rate must be a number greater than or equal to zero.';
    } elseif (!$maturity_ok) {
        $error = 'Please enter a valid maturity date.';
    } else {
        $amount = round((float)$form['amount'], 2);
        $description = $form['description'] !== '' ? $form['description'] : ($is_new ? 'Initial investment' : 'Additional investment');
        $reference = $form['reference'] !== '' ? $form['reference'] : null;
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

        try {
            $con->beginTransaction();

            if ($is_new) {
                // Generate an account number that is not already taken (column is UNIQUE).
                $check = $con->prepare("SELECT 1 FROM investor_accounts WHERE account_number = ?");
                do {
                    $account_number = 'INV' . date('Ymd') . str_pad((string)random_int(0, 9999), 4, '0', STR_PAD_LEFT);
                    $check->execute([$account_number]);
                } while ($check->fetchColumn());

                $stmt = $con->prepare("
                    INSERT INTO investor_accounts (investor_id, account_number, investment_amount, interest_rate, maturity_date, status)
                    VALUES (?, ?, ?, ?, ?, 'active') RETURNING id, investment_amount
                ");
                $stmt->execute([$id, $account_number, $amount, (float)$form['interest_rate'], $form['maturity_date'] ?: null]);
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $account_id = (int)$row['id'];
                $balance_after = $row['investment_amount'];
            } else {
                // Lock the account row so concurrent deposits compute balance_after correctly.
                $stmt = $con->prepare("SELECT id FROM investor_accounts WHERE id = ? AND investor_id = ? FOR UPDATE");
                $stmt->execute([$account_id, $id]);
                if (!$stmt->fetchColumn()) {
                    throw new RuntimeException('The selected account does not belong to this investor.');
                }
                $stmt = $con->prepare("UPDATE investor_accounts SET investment_amount = COALESCE(investment_amount, 0) + ? WHERE id = ? RETURNING investment_amount");
                $stmt->execute([$amount, $account_id]);
                $balance_after = $stmt->fetchColumn();
            }

            $stmt = $con->prepare("
                INSERT INTO investor_transactions (account_id, type, amount, balance_after, description, reference, created_by)
                VALUES (?, 'investment', ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$account_id, $amount, $balance_after, $description, $reference, $user_id]);

            $con->commit();
            header('Location: view_investor.php?id=' . $id);
            exit;
        } catch (PDOException | RuntimeException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $error = 'Could not record investment: ' . $e->getMessage();
        }
    }
}

if ($investor) {
    try {
        $stmt = $con->prepare("SELECT id, account_number, investment_amount, interest_rate, status FROM investor_accounts WHERE investor_id = ? ORDER BY created_at, id");
        $stmt->execute([$id]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $con->query("SELECT id, name, interest_rate FROM investor_products WHERE status = 'active' ORDER BY name");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = $error ?: 'Could not load accounts: ' . $e->getMessage();
    }
    if ($form['account_id'] === '') {
        $form['account_id'] = $accounts ? (string)$accounts[0]['id'] : 'new';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle text-success me-2"></i>Add Investment</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <?php if ($investor): ?>
                <li class="breadcrumb-item"><a href="view_investor.php?id=<?php echo (int)$investor['id']; ?>"><?php echo htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active">Add Investment</li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$investor): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Investor not found.
    <a href="view_investors.php" class="alert-link">Back to investors</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-hand-holding-usd me-2"></i>Investment for <?php echo htmlspecialchars($investor['first_name'] . ' ' . $investor['last_name']); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Account <span class="text-danger">*</span></label>
                            <select name="account_id" id="accountSelect" class="form-select" required>
                                <?php foreach ($accounts as $acc): ?>
                                <option value="<?php echo (int)$acc['id']; ?>" <?php echo $form['account_id'] === (string)$acc['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($acc['account_number']); ?> (TZS <?php echo number_format((float)$acc['investment_amount'], 2); ?>, <?php echo htmlspecialchars((string)$acc['status']); ?>)
                                </option>
                                <?php endforeach; ?>
                                <option value="new" <?php echo $form['account_id'] === 'new' ? 'selected' : ''; ?>>+ Open a new account</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('amount'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="<?php echo htmlspecialchars($form['amount']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div id="newAccountFields" class="border rounded p-3 mb-3 bg-light">
                        <h6 class="text-uppercase text-muted mb-3">New Account</h6>
                        <div class="row">
                            <?php if ($products): ?>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Product (fills rate)</label>
                                <select id="productSelect" class="form-select">
                                    <option value="">-- None --</option>
                                    <?php foreach ($products as $p): ?>
                                    <option value="<?php echo htmlspecialchars((string)$p['interest_rate']); ?>"><?php echo htmlspecialchars($p['name']); ?> (<?php echo number_format((float)$p['interest_rate'], 2); ?>%)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php endif; ?>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><?php echo __('interest_rate'); ?> (%)</label>
                                <input type="number" name="interest_rate" id="interestRate" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars($form['interest_rate']); ?>">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label"><?php echo __('maturity_date'); ?></label>
                                <input type="date" name="maturity_date" class="form-control" value="<?php echo htmlspecialchars($form['maturity_date']); ?>">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('description'); ?></label>
                            <input type="text" name="description" class="form-control" value="<?php echo htmlspecialchars($form['description']); ?>" placeholder="e.g. Additional investment">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('reference'); ?></label>
                            <input type="text" name="reference" class="form-control" value="<?php echo htmlspecialchars($form['reference']); ?>" placeholder="Receipt / bank ref">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-2"></i><?php echo __('save'); ?>
                        </button>
                        <a href="view_investor.php?id=<?php echo (int)$investor['id']; ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i><?php echo __('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sel = document.getElementById('accountSelect');
    const box = document.getElementById('newAccountFields');
    const product = document.getElementById('productSelect');
    function toggle() { box.style.display = sel.value === 'new' ? '' : 'none'; }
    sel.addEventListener('change', toggle);
    toggle();
    if (product) {
        product.addEventListener('change', function () {
            if (this.value !== '') document.getElementById('interestRate').value = this.value;
        });
    }
});
</script>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
