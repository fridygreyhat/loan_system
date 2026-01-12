<?php
$base_path = '../';
$page_title = 'Staff Members';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';

$staff = [];
try {
    $stmt = $con->query("SELECT s.*, srt.name as role_name FROM staff s LEFT JOIN staff_role_types srt ON s.role_id = srt.id ORDER BY s.fname, s.lname");
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Staff Members</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Staff</li>
            </ol>
        </nav>
    </div>
    <a href="add_staff.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Staff
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($staff); ?></h3>
                <p>Total Staff</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users me-2"></i>All Staff Members</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            <a href="roles.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-shield-alt me-1"></i>Manage Roles</a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staff)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No staff members found. <a href="add_staff.php">Add one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($staff as $member): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars(($member['fname'] ?? '') . ' ' . ($member['lname'] ?? '')); ?></strong></td>
                        <td><?php echo htmlspecialchars($member['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($member['phone_no'] ?? '-'); ?></td>
                        <td><span class="badge bg-primary"><?php echo htmlspecialchars($member['role_name'] ?? 'Staff'); ?></span></td>
                        <td>
                            <span class="badge bg-<?php echo ($member['status'] ?? 'active') === 'active' ? 'success' : 'secondary'; ?>">
                                <?php echo ucfirst($member['status'] ?? 'active'); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_staff_details.php?id=<?php echo $member['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_staff.php?id=<?php echo $member['id']; ?>" class="btn btn-outline-secondary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
