<?php
$base_path = '../';
$page_title = 'Edit Expense';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$expense = null;
$vendors = [];
$expense_types = [];

try {
    $stmt = $con->prepare("SELECT * FROM expenses WHERE id = ?");
    $stmt->execute([$id]);
    $expense = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load expense: ' . $e->getMessage();
}

if ($expense && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (($_POST['action'] ?? '') === 'delete') {
        try {
            $stmt = $con->prepare("DELETE FROM expenses WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: view_expenses.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Could not delete expense: ' . $e->getMessage();
        }
    } else {
        $vendor_id = (int)($_POST['vendor_id'] ?? 0);
        $expense_type_id = (int)($_POST['expense_type_id'] ?? 0);
        $amount_raw = trim((string)($_POST['amount'] ?? ''));
        $expense_date = trim((string)($_POST['expense_date'] ?? ''));
        $description = trim((string)($_POST['description'] ?? ''));

        $date_obj = DateTime::createFromFormat('Y-m-d', $expense_date);

        // Keep posted values so the form re-renders with them on error
        $expense = array_merge($expense, [
            'vendor_id' => $vendor_id ?: null,
            'expense_type_id' => $expense_type_id ?: null,
            'amount' => $amount_raw,
            'expense_date' => $expense_date,
            'description' => $description,
        ]);

        if (!is_numeric($amount_raw) || (float)$amount_raw <= 0) {
            $error = 'Please enter a valid amount greater than zero.';
        } elseif (!$date_obj || $date_obj->format('Y-m-d') !== $expense_date) {
            $error = 'Please enter a valid expense date.';
        } else {
            try {
                $stmt = $con->prepare("UPDATE expenses SET vendor_id = ?, expense_type_id = ?, amount = ?, expense_date = ?, description = ? WHERE id = ?");
                $stmt->execute([$vendor_id ?: null, $expense_type_id ?: null, $amount_raw, $expense_date, $description, $id]);
                header('Location: view_expenses.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Could not update expense: ' . $e->getMessage();
            }
        }
    }
}

if ($expense) {
    try {
        $stmt = $con->prepare("SELECT id, name FROM vendors WHERE status = 'active' OR id = ? ORDER BY name");
        $stmt->execute([(int)($expense['vendor_id'] ?? 0)]);
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $con->query("SELECT id, name FROM expense_types ORDER BY name");
        $expense_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Could not load vendors / expense types: ' . $e->getMessage();
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i>Edit Expense</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_expenses.php"><?php echo __('view_expenses'); ?></a></li>
                <li class="breadcrumb-item active">Edit Expense</li>
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

<?php if (!$expense): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Expense not found.
    <a href="view_expenses.php" class="alert-link">Back to expenses</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar me-2"></i>Expense Details</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="update">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="<?php echo htmlspecialchars((string)$expense['expense_date']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('amount'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0.01" value="<?php echo htmlspecialchars((string)$expense['amount']); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vendor</label>
                            <select name="vendor_id" class="form-select">
                                <option value="">-- Select Vendor --</option>
                                <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo (int)$vendor['id']; ?>" <?php echo (int)$vendor['id'] === (int)($expense['vendor_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($vendor['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Expense Type</label>
                            <select name="expense_type_id" class="form-select">
                                <option value="">-- Select Type --</option>
                                <?php foreach ($expense_types as $type): ?>
                                <option value="<?php echo (int)$type['id']; ?>" <?php echo (int)$type['id'] === (int)($expense['expense_type_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars((string)($expense['description'] ?? '')); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
                        </button>
                        <a href="view_expenses.php" class="btn btn-outline-secondary">
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
                <h5><i class="fas fa-trash me-2"></i>Delete Expense</h5>
            </div>
            <div class="card-body">
                <p class="text-muted small">This permanently removes the expense record.</p>
                <form method="POST" onsubmit="return confirm('Delete this expense? This cannot be undone.');">
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
