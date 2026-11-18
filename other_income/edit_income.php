<?php
$base_path = '../';
$page_title = 'Edit Income';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$income = null;
$customers = [];
$income_types = [];

try {
    $stmt = $con->prepare("SELECT * FROM other_income WHERE id = ?");
    $stmt->execute([$id]);
    $income = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load income: ' . $e->getMessage();
}

if ($income && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        try {
            $stmt = $con->prepare("DELETE FROM other_income WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: view_income.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Could not delete income: ' . $e->getMessage();
        }
    } else {
        $customer_id = (int)($_POST['customer_id'] ?? 0);
        $income_type_id = (int)($_POST['income_type_id'] ?? 0);
        $amount_raw = trim((string)($_POST['amount'] ?? ''));
        $income_date = trim((string)($_POST['income_date'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));

        $date_obj = DateTime::createFromFormat('Y-m-d', $income_date);

        // Keep posted values so the form re-renders with them on error
        $income = array_merge($income, [
            'customer_id' => $customer_id ?: null,
            'income_type_id' => $income_type_id ?: null,
            'amount' => $amount_raw,
            'income_date' => $income_date,
            'description' => $description,
        ]);

        if (!is_numeric($amount_raw) || (float)$amount_raw <= 0) {
            $error = 'Please enter a valid amount greater than zero.';
        } elseif (!$date_obj || $date_obj->format('Y-m-d') !== $income_date) {
            $error = 'Please enter a valid income date.';
        } else {
            try {
                $stmt = $con->prepare("UPDATE other_income SET customer_id = ?, income_type_id = ?, amount = ?, income_date = ?, description = ? WHERE id = ?");
                $stmt->execute([$customer_id ?: null, $income_type_id ?: null, $amount_raw, $income_date, $description, $id]);
                header('Location: view_income.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Could not update income: ' . $e->getMessage();
            }
        }
    }
}

if ($income) {
    try {
        $stmt = $con->prepare("SELECT id, name FROM other_income_customers WHERE status = 'active' OR id = ? ORDER BY name");
        $stmt->execute([(int)($income['customer_id'] ?? 0)]);
        $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $con->query("SELECT id, name FROM other_income_types ORDER BY name");
        $income_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Could not load customers / income types: ' . $e->getMessage();
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i>Edit Income</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_income.php"><?php echo __('view_other_income'); ?></a></li>
                <li class="breadcrumb-item active">Edit Income</li>
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

<?php if (!$income): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Income record not found.
    <a href="view_income.php" class="alert-link">Back to other income</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-hand-holding-usd me-2"></i>Income Details</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Income Date <span class="text-danger">*</span></label>
                            <input type="date" name="income_date" class="form-control" value="<?php echo htmlspecialchars((string)$income['income_date']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('amount'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="<?php echo htmlspecialchars((string)$income['amount']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-select">
                                <option value="">-- Select Customer --</option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo (int)$customer['id']; ?>" <?php echo (int)$customer['id'] === (int)($income['customer_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($customer['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Income Type</label>
                            <select name="income_type_id" class="form-select">
                                <option value="">-- Select Type --</option>
                                <?php foreach ($income_types as $type): ?>
                                <option value="<?php echo (int)$type['id']; ?>" <?php echo (int)$type['id'] === (int)($income['income_type_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars((string)($income['description'] ?? '')); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
                        </button>
                        <a href="view_income.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i><?php echo __('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-danger">
            <div class="card-header">
                <h5><i class="fas fa-trash me-2"></i>Delete Income</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">This permanently removes the income record.</p>
                <form method="POST" onsubmit="return confirm('Delete this income record? This cannot be undone.');">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-trash me-2"></i><?php echo __('delete'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
