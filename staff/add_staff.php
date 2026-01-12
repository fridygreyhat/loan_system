<?php
$base_path = '../';
$page_title = 'Add Staff';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';

$staff = null;
$edit_id = $_GET['id'] ?? null;

if ($edit_id) {
    try {
        $stmt = $con->prepare("SELECT * FROM staff WHERE id = ?");
        $stmt->execute([$edit_id]);
        $staff = $stmt->fetch(PDO::FETCH_ASSOC);
        $page_title = 'Edit Staff';
    } catch (PDOException $e) {
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fname = $_POST['fname'] ?? '';
    $lname = $_POST['lname'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $role_name = $_POST['role_name'] ?? 'Staff';
    $status = $_POST['status'] ?? 'active';
    
    try {
        if ($edit_id && $staff) {
            $stmt = $con->prepare("UPDATE staff SET fname = ?, lname = ?, email = ?, phone = ?, role_name = ?, status = ? WHERE id = ?");
            $stmt->execute([$fname, $lname, $email, $phone, $role_name, $status, $edit_id]);
        } else {
            $stmt = $con->prepare("INSERT INTO staff (fname, lname, email, phone, role_name, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$fname, $lname, $email, $phone, $role_name, $status]);
        }
        header('Location: view_staff.php');
        exit;
    } catch (PDOException $e) {
        $error = "Error saving staff member.";
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
        <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="fname" class="form-control" required value="<?php echo htmlspecialchars($staff['fname'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="lname" class="form-control" required value="<?php echo htmlspecialchars($staff['lname'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" required value="<?php echo htmlspecialchars($staff['email'] ?? ''); ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($staff['phone'] ?? ''); ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role *</label>
                    <select name="role_name" class="form-select" required>
                        <option value="Administrator" <?php echo ($staff['role_name'] ?? '') === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                        <option value="Branch Manager" <?php echo ($staff['role_name'] ?? '') === 'Branch Manager' ? 'selected' : ''; ?>>Branch Manager</option>
                        <option value="Loan Officer" <?php echo ($staff['role_name'] ?? '') === 'Loan Officer' ? 'selected' : ''; ?>>Loan Officer</option>
                        <option value="Teller" <?php echo ($staff['role_name'] ?? '') === 'Teller' ? 'selected' : ''; ?>>Teller</option>
                        <option value="Collector" <?php echo ($staff['role_name'] ?? '') === 'Collector' ? 'selected' : ''; ?>>Collector</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" <?php echo ($staff['status'] ?? 'active') === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo ($staff['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Staff</button>
                <a href="view_staff.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
