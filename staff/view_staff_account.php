<?php
$base_path = '../';
$page_title = 'Staff Account Settings';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Staff Account Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_staff.php">Staff</a></li>
            <li class="breadcrumb-item active">Account Settings</li>
        </ol>
    </nav>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-cog me-2"></i>Account Settings</h5>
    </div>
    <div class="card-body">
        <form>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Current Password</label>
                    <input type="password" class="form-control" placeholder="Enter current password">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">New Password</label>
                    <input type="password" class="form-control" placeholder="Enter new password">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" class="form-control" placeholder="Confirm new password">
                </div>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Update Password</button>
        </form>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
