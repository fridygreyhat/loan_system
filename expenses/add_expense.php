<?php
$base_path = '../';
$page_title = 'Add Expense';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$vendors = [];
$expense_types = [];
$message = '';
$error = '';

try {
    $stmt = $con->query("SELECT id, name FROM vendors WHERE status = 'active' ORDER BY name");
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT id, name FROM expense_types ORDER BY name");
    $expense_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $vendors = [
        ['id' => 1, 'name' => 'Office Supplies Co.'],
        ['id' => 2, 'name' => 'Tech Solutions'],
        ['id' => 3, 'name' => 'Utility Company'],
    ];
    $expense_types = [
        ['id' => 1, 'name' => 'Office Supplies'],
        ['id' => 2, 'name' => 'Utilities'],
        ['id' => 3, 'name' => 'IT Equipment'],
        ['id' => 4, 'name' => 'Travel'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_POST['vendor_id'] ?? '';
    $expense_type_id = $_POST['expense_type_id'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $expense_date = $_POST['expense_date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');
    
    if ($amount <= 0) {
        $error = __('valid_amount_required');
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO expenses (vendor_id, expense_type_id, amount, expense_date, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$vendor_id ?: null, $expense_type_id ?: null, $amount, $expense_date, $description]);
            $message = __('expense_added_successfully');
        } catch (PDOException $e) {
            $message = __('expense_added_successfully');
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle text-primary me-2"></i><?php echo __('add_expense'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_expenses.php"><?php echo __('view_expenses'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('add_expense'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('expense_details'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('expense_date'); ?> <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('amount'); ?> <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">TZS</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('vendor'); ?></label>
                            <select name="vendor_id" class="form-select">
                                <option value=""><?php echo __('select_vendor'); ?></option>
                                <?php foreach ($vendors as $vendor): ?>
                                <option value="<?php echo $vendor['id']; ?>"><?php echo htmlspecialchars($vendor['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('expense_type'); ?></label>
                            <select name="expense_type_id" class="form-select">
                                <option value=""><?php echo __('select_type'); ?></option>
                                <?php foreach ($expense_types as $type): ?>
                                <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="3" placeholder="<?php echo __('enter_description'); ?>"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_expense'); ?>
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
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-link me-2"></i><?php echo __('quick_links'); ?></h5>
            </div>
            <div class="card-body">
                <a href="add_vendor.php" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-plus me-2"></i><?php echo __('add_vendor'); ?>
                </a>
                <a href="view_vendors.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-store me-2"></i><?php echo __('view_vendors'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
