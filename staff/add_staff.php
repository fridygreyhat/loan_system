<?php
$base_path = '../';
$page_title = 'Add Staff';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$error = '';
$staff = null;
$edit_id = (int)($_GET['id'] ?? 0);

$roles = $con->query("SELECT id, name FROM staff_role_types ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$branches = $con->query("SELECT id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

if ($edit_id) {
    $stmt = $con->prepare("SELECT * FROM staff WHERE id = ?");
    $stmt->execute([$edit_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    $page_title = 'Edit Staff';
    if (!$staff) {
        $error = 'Staff member not found.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!$edit_id || $staff)) {
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_no = trim($_POST['phone_no'] ?? '');
    $role_id = (int)($_POST['role_id'] ?? 0);
    $branch_id = (int)($_POST['branch_id'] ?? 0);
    $status = in_array($_POST['status'] ?? '', ['active', 'pending', 'inactive'], true) ? $_POST['status'] : 'active';
    $password = $_POST['password'] ?? '';

    // Keep the form filled in if we have to show an error.
    $staff = array_merge($staff ?? [], compact('fname', 'lname', 'email', 'phone_no', 'role_id', 'branch_id', 'status'));

    $dup = $con->prepare("SELECT 1 FROM staff WHERE LOWER(email) = LOWER(?) AND id <> ?");
    $dup->execute([$email, $edit_id]);

    if ($fname === '' || $lname === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !$role_id || !$branch_id) {
        $error = 'Please fill in all required fields with valid values.';
    } elseif ($dup->fetchColumn()) {
        $error = 'Another staff member already uses this email address.';
    } elseif (!$edit_id && strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($edit_id && $password !== '' && strlen($password) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($edit_id && $edit_id === (int)($_SESSION['user_id'] ?? 0) && $status !== 'active') {
        $error = 'You cannot deactivate your own account.';
    } else {
        try {
            if ($edit_id) {
                $stmt = $con->prepare("UPDATE staff SET fname = ?, lname = ?, email = ?, phone_no = ?, role_id = ?, branch_id = ?, status = ? WHERE id = ?");
                $stmt->execute([$fname, $lname, $email, $phone_no, $role_id, $branch_id, $status, $edit_id]);
                if ($password !== '') {
                    $con->prepare("UPDATE staff SET password = ? WHERE id = ?")->execute([password_hash($password, PASSWORD_DEFAULT), $edit_id]);
                }
            } else {
                $stmt = $con->prepare("INSERT INTO staff (fname, lname, email, phone_no, role_id, branch_id, status, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fname, $lname, $email, $phone_no, $role_id, $branch_id, $status, password_hash($password, PASSWORD_DEFAULT)]);
            }
            header('Location: view_staff.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error saving staff member: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1><?php echo $edit_id ? 'Edit' : 'Add'; ?> Staff Member</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_staff.php">Staff</a></li>
            <li class="breadcrumb-item active"><?php echo $edit_id ? 'Edit' : 'Add'; ?> Staff</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-plus me-2"></i>Staff Details</h5>
    </div>
    <div class="card-body">
        <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!$edit_id || $staff): ?>
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="fname" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($staff['fname'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="lname" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($staff['lname'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required maxlength="100" value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone_no" class="form-control" maxlength="100" value="<?php echo htmlspecialchars($staff['phone_no'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role_id" class="form-select" required>
                        <option value="">-- Select role --</option>
                        <?php foreach ($roles as $role): ?>
                        <option value="<?php echo (int)$role['id']; ?>" <?php echo (int)($staff['role_id'] ?? 0) === (int)$role['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($role['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Branch *</label>
                    <select name="branch_id" class="form-select" required>
                        <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo (int)$branch['id']; ?>" <?php echo (int)($staff['branch_id'] ?? 0) === (int)$branch['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($branch['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <?php foreach (['active' => 'Active', 'pending' => 'Pending', 'inactive' => 'Inactive'] as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo ($staff['status'] ?? 'active') === $value ? 'selected' : ''; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label"><?php echo $edit_id ? 'New Password (leave blank to keep current)' : 'Password *'; ?></label>
                    <input type="password" name="password" class="form-control" minlength="8" autocomplete="new-password" <?php echo $edit_id ? '' : 'required'; ?>>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Staff</button>
                <a href="view_staff.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
