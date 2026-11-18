<?php
$base_path = '../';
$page_title = 'Edit Staff';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$statuses = ['active' => 'Active', 'pending' => 'Pending', 'inactive' => 'Inactive'];

$id = (int)($_GET['id'] ?? 0);
$current_user_id = (int)($_SESSION['user_id'] ?? 0);
$is_self = $id > 0 && $id === $current_user_id;

$error = '';
$member = null;
$roles = [];
$branches = [];

try {
    if ($id > 0) {
        $stmt = $con->prepare("SELECT id, fname, lname, email, phone_no, role_id, branch_id, status FROM staff WHERE id = ?");
        $stmt->execute([$id]);
        $member = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    if ($member) {
        $roles = $con->query("SELECT id, name FROM staff_role_types ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
        $branches = $con->query("SELECT id, name FROM branches ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log('edit_staff load: ' . $e->getMessage());
    $error = 'Could not load the staff member: database error.';
}

$form = $member ? [
    'fname' => $member['fname'] ?? '',
    'lname' => $member['lname'] ?? '',
    'email' => $member['email'] ?? '',
    'phone_no' => $member['phone_no'] ?? '',
    'role_id' => (int)$member['role_id'],
    'branch_id' => (int)$member['branch_id'],
    'status' => $member['status'],
] : null;

// Keep a legacy status value selectable so saving does not silently change it.
if ($member && !isset($statuses[$member['status']])) {
    $statuses[$member['status']] = ucfirst($member['status']);
}

if ($member && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'fname' => trim($_POST['fname'] ?? ''),
        'lname' => trim($_POST['lname'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'phone_no' => trim($_POST['phone_no'] ?? ''),
        'role_id' => (int)($_POST['role_id'] ?? 0),
        'branch_id' => (int)($_POST['branch_id'] ?? 0),
        'status' => (string)($_POST['status'] ?? ''),
    ];
    $password = (string)($_POST['password'] ?? '');
    $password_confirm = (string)($_POST['password_confirm'] ?? '');

    $role_ids = array_map('intval', array_column($roles, 'id'));
    $branch_ids = array_map('intval', array_column($branches, 'id'));

    if ($form['fname'] === '') {
        $error = 'First name is required.';
    } elseif ($form['email'] === '' || !filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'A valid email address is required.';
    } elseif (!in_array($form['role_id'], $role_ids, true)) {
        $error = 'Please select a valid role.';
    } elseif (!in_array($form['branch_id'], $branch_ids, true)) {
        $error = 'Please select a valid branch.';
    } elseif (!isset($statuses[$form['status']])) {
        $error = 'Please select a valid status.';
    } elseif ($is_self && $form['status'] !== 'active') {
        $error = 'You cannot deactivate your own account.';
    } elseif ($password !== '' && strlen($password) < 6) {
        $error = 'New password must be at least 6 characters.';
    } elseif ($password !== '' && $password !== $password_confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        try {
            $stmt = $con->prepare("SELECT id FROM staff WHERE LOWER(email) = LOWER(?) AND id <> ?");
            $stmt->execute([$form['email'], $id]);
            if ($stmt->fetchColumn()) {
                $error = 'Another staff member already uses that email address.';
            } else {
                $sql = "UPDATE staff SET fname = ?, lname = ?, email = ?, phone_no = ?, role_id = ?, branch_id = ?, status = ?";
                $params = [
                    $form['fname'],
                    $form['lname'] !== '' ? $form['lname'] : null,
                    $form['email'],
                    $form['phone_no'] !== '' ? $form['phone_no'] : null,
                    $form['role_id'],
                    $form['branch_id'],
                    $form['status'],
                ];
                if ($password !== '') {
                    $sql .= ", password = ?";
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }
                $sql .= " WHERE id = ?";
                $params[] = $id;
                $con->prepare($sql)->execute($params);

                if ($is_self) {
                    $_SESSION['user_name'] = trim($form['fname'] . ' ' . $form['lname']);
                    $_SESSION['user_email'] = $form['email'];
                    $_SESSION['role_id'] = $form['role_id'];
                }

                header('Location: view_staff_details.php?id=' . $id);
                exit;
            }
        } catch (PDOException $e) {
            error_log('edit_staff update: ' . $e->getMessage());
            $error = 'Could not save the staff member: database error.';
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="content-header">
    <h1><i class="fas fa-user-edit text-primary me-2"></i>Edit Staff Member</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="view_staff.php"><?php echo __('staff'); ?></a></li>
            <li class="breadcrumb-item active">Edit Staff</li>
        </ol>
    </nav>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$member): ?>
<?php if (!$error): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Staff member not found.
    <a href="view_staff.php" class="alert-link">Back to staff list</a>.
</div>
<?php endif; ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-id-card me-2"></i><?php echo __('staff_details'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="edit_staff.php?id=<?php echo $id; ?>" autocomplete="off">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="fname" class="form-control" required value="<?php echo htmlspecialchars($form['fname']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="lname" class="form-control" value="<?php echo htmlspecialchars($form['lname']); ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($form['email']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone_no" class="form-control" value="<?php echo htmlspecialchars($form['phone_no']); ?>" placeholder="+255 XXX XXX XXX">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select name="role_id" class="form-select" required>
                                <option value="">-- Select Role --</option>
                                <?php foreach ($roles as $r): ?>
                                <option value="<?php echo (int)$r['id']; ?>" <?php echo $form['role_id'] === (int)$r['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($r['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" class="form-select" required>
                                <option value="">-- Select Branch --</option>
                                <?php foreach ($branches as $b): ?>
                                <option value="<?php echo (int)$b['id']; ?>" <?php echo $form['branch_id'] === (int)$b['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($b['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <?php foreach ($statuses as $key => $label): ?>
                                <option value="<?php echo htmlspecialchars($key); ?>" <?php echo $form['status'] === $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($is_self): ?>
                            <small class="text-muted">You cannot deactivate your own account.</small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Change Password <small class="text-muted fw-normal">(leave blank to keep the current password)</small></h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="password" class="form-control" minlength="6" autocomplete="new-password">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="password_confirm" class="form-control" minlength="6" autocomplete="new-password">
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                        <a href="view_staff_details.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
