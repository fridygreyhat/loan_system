<?php
$base_path = '../';
$page_title = 'Edit Customer';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$customer = null;

try {
    $stmt = $con->prepare("SELECT * FROM other_income_customers WHERE id = ?");
    $stmt->execute([$id]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load customer: ' . $e->getMessage();
}

if ($customer && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $contact_person = trim((string)($_POST['contact_person'] ?? ''));
    $notes = trim((string)($_POST['notes'] ?? ''));
    $status = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    $customer = array_merge($customer, compact('name', 'email', 'phone', 'address', 'contact_person', 'notes', 'status'));

    if ($name === '') {
        $error = 'Customer name is required.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $con->prepare("UPDATE other_income_customers SET name = ?, email = ?, phone = ?, address = ?, contact_person = ?, notes = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $address, $contact_person, $notes, $status, $id]);
            header('Location: view_customers.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Could not update customer: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i>Edit Customer</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_customers.php"><?php echo __('view_customers'); ?></a></li>
                <li class="breadcrumb-item active">Edit Customer</li>
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

<?php if (!$customer): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Customer not found.
    <a href="view_customers.php" class="alert-link">Back to customers</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user me-2"></i>Customer Information</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars((string)$customer['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars((string)($customer['contact_person'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars((string)($customer['email'] ?? '')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars((string)($customer['phone'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('address'); ?></label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars((string)($customer['address'] ?? '')); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars((string)($customer['notes'] ?? '')); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('status'); ?></label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $customer['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $customer['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
                        </button>
                        <a href="view_customers.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i><?php echo __('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
