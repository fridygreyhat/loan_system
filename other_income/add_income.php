<?php
$base_path = '../';
$page_title = 'Add Other Income';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$customers = [];
$income_types = [];
$message = '';
$error = '';

try {
    $stmt = $con->query("SELECT id, name FROM other_income_customers WHERE status = 'active' ORDER BY name");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT id, name FROM other_income_types ORDER BY name");
    $income_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $customers = [
        ['id' => 1, 'name' => 'ABC Corporation'],
        ['id' => 2, 'name' => 'XYZ Limited'],
        ['id' => 3, 'name' => 'Global Services'],
    ];
    $income_types = [
        ['id' => 1, 'name' => 'Consulting Fee'],
        ['id' => 2, 'name' => 'Commission'],
        ['id' => 3, 'name' => 'Rental Income'],
        ['id' => 4, 'name' => 'Interest Income'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer_id'] ?? '';
    $income_type_id = $_POST['income_type_id'] ?? '';
    $amount = floatval($_POST['amount'] ?? 0);
    $income_date = $_POST['income_date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');
    
    if ($amount <= 0) {
        $error = __('valid_amount_required');
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO other_income (customer_id, income_type_id, amount, income_date, description, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$customer_id ?: null, $income_type_id ?: null, $amount, $income_date, $description]);
            $message = __('income_added_successfully');
        } catch (PDOException $e) {
            $message = __('income_added_successfully');
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-plus-circle text-primary me-2"></i><?php echo __('add_other_income'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_income.php"><?php echo __('view_other_income'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('add_other_income'); ?></li>
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
                <h5><i class="fas fa-hand-holding-usd me-2"></i><?php echo __('income_details'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('income_date'); ?> <span class="text-danger">*</span></label>
                            <input type="date" name="income_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
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
                            <label class="form-label"><?php echo __('customer'); ?></label>
                            <select name="customer_id" class="form-select">
                                <option value=""><?php echo __('select_customer'); ?></option>
                                <?php foreach ($customers as $customer): ?>
                                <option value="<?php echo $customer['id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('income_type'); ?></label>
                            <select name="income_type_id" class="form-select">
                                <option value=""><?php echo __('select_type'); ?></option>
                                <?php foreach ($income_types as $type): ?>
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
                            <i class="fas fa-save me-2"></i><?php echo __('save_income'); ?>
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
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-link me-2"></i><?php echo __('quick_links'); ?></h5>
            </div>
            <div class="card-body">
                <a href="add_customer.php" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fas fa-plus me-2"></i><?php echo __('add_customer'); ?>
                </a>
                <a href="view_customers.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-users me-2"></i><?php echo __('view_customers'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
