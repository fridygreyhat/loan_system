<?php
$base_path = '../';
$page_title = 'Edit Vendor';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$vendor = null;

try {
    $stmt = $con->prepare("SELECT * FROM vendors WHERE id = ?");
    $stmt->execute([$id]);
    $vendor = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load vendor: ' . $e->getMessage();
}

if ($vendor && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim((string)($_POST['name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $contact_person = trim((string)($_POST['contact_person'] ?? ''));
    $notes = trim((string)($_POST['notes'] ?? ''));
    $status = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    $vendor = array_merge($vendor, compact('name', 'email', 'phone', 'address', 'contact_person', 'notes', 'status'));

    if ($name === '') {
        $error = 'Vendor name is required.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $con->prepare("UPDATE vendors SET name = ?, email = ?, phone = ?, address = ?, contact_person = ?, notes = ?, status = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $address, $contact_person, $notes, $status, $id]);
            header('Location: view_vendors.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Could not update vendor: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i>Edit Vendor</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_vendors.php"><?php echo __('view_vendors'); ?></a></li>
                <li class="breadcrumb-item active">Edit Vendor</li>
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

<?php if (!$vendor): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Vendor not found.
    <a href="view_vendors.php" class="alert-link">Back to vendors</a>
</div>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-store me-2"></i>Vendor Information</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vendor Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars((string)$vendor['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Person</label>
                            <input type="text" name="contact_person" class="form-control" value="<?php echo htmlspecialchars((string)($vendor['contact_person'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars((string)($vendor['email'] ?? '')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars((string)($vendor['phone'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('address'); ?></label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars((string)($vendor['address'] ?? '')); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars((string)($vendor['notes'] ?? '')); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('status'); ?></label>
                        <select name="status" class="form-select">
                            <option value="active" <?php echo $vendor['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $vendor['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
                        </button>
                        <a href="view_vendors.php" class="btn btn-outline-secondary">
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
