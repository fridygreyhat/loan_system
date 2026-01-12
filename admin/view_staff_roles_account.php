<?php
$base_path = '../';
$page_title = 'Staff Roles';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$roles = [
    ['id' => 1, 'name' => 'Administrator', 'description' => 'Full system access', 'users' => 1],
    ['id' => 2, 'name' => 'Branch Manager', 'description' => 'Branch level access', 'users' => 0],
    ['id' => 3, 'name' => 'Loan Officer', 'description' => 'Loan management access', 'users' => 0],
    ['id' => 4, 'name' => 'Teller', 'description' => 'Transaction access', 'users' => 0],
    ['id' => 5, 'name' => 'Collector', 'description' => 'Collection access', 'users' => 0],
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Staff Roles</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Staff Roles</li>
            </ol>
        </nav>
    </div>
    <a href="add_staff_role_account.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Role</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-tag me-2"></i>All Roles</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Description</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $index => $role): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo $role['name']; ?></strong></td>
                        <td><?php echo $role['description']; ?></td>
                        <td><span class="badge bg-primary"><?php echo $role['users']; ?></span></td>
                        <td>
                            <a href="edit_staff_role_permissions_account.html?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-key"></i> Permissions</a>
                            <a href="add_staff_role_account_edit.html?id=<?php echo $role['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
