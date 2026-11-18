<?php
$base_path = '../';
$page_title = 'Edit Investor';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$investor = null;

try {
    $stmt = $con->prepare("SELECT * FROM investors WHERE id = ?");
    $stmt->execute([$id]);
    $investor = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load investor: ' . $e->getMessage();
}

if ($investor && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim((string)($_POST['first_name'] ?? ''));
    $last_name = trim((string)($_POST['last_name'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $phone = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $id_number = trim((string)($_POST['id_number'] ?? ''));
    $status = ($_POST['status'] ?? '') === 'inactive' ? 'inactive' : 'active';

    $investor = array_merge($investor, compact('first_name', 'last_name', 'email', 'phone', 'address', 'id_number', 'status'));

    if ($first_name === '' || $last_name === '') {
        $error = 'First name and last name are required.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $stmt = $con->prepare("UPDATE investors SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, id_number = ?, status = ? WHERE id = ?");
            $stmt->execute([$first_name, $last_name, $email, $phone, $address, $id_number, $status, $id]);
            header('Location: view_investor.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            $error = 'Could not update investor: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-edit text-primary me-2"></i>Edit Investor</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <li class="breadcrumb-item active">Edit Investor</li>
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
                <h5><i class="fas fa-user me-2"></i><?php echo __('investor_details'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('first_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars((string)$investor['first_name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('last_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars((string)$investor['last_name']); ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars((string)($investor['email'] ?? '')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars((string)($investor['phone'] ?? '')); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><?php echo __('address'); ?></label>
                        <textarea name="address" class="form-control" rows="2"><?php echo htmlspecialchars((string)($investor['address'] ?? '')); ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('id_number'); ?></label>
                            <input type="text" name="id_number" class="form-control" value="<?php echo htmlspecialchars((string)($investor['id_number'] ?? '')); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('status'); ?></label>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo ($investor['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo ($investor['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
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
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
