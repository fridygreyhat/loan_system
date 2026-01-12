<?php
$base_path = '../';
$page_title = 'Staff Roles & Permissions';
$current_page = 'staff';

require_once $base_path . 'helpers/connect.php';

$roles = [];
$permissions = [];
try {
    $stmt = $con->query("SELECT * FROM staff_role_types ORDER BY name");
    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT * FROM permissions ORDER BY module, name");
    $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

$grouped_permissions = [];
foreach ($permissions as $perm) {
    $grouped_permissions[$perm['module']][] = $perm;
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Staff Roles & Permissions</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="view_staff.php">Staff</a></li>
                <li class="breadcrumb-item active">Roles & Permissions</li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="fas fa-plus me-2"></i>Add Role
    </button>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-tag me-2"></i>Roles</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($roles as $index => $role): ?>
                    <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center role-item <?php echo $index === 0 ? 'active' : ''; ?>" data-role-id="<?php echo $role['id']; ?>">
                        <div>
                            <strong><?php echo htmlspecialchars($role['name']); ?></strong>
                            <small class="d-block text-muted"><?php echo htmlspecialchars($role['description'] ?? ''); ?></small>
                        </div>
                        <?php if ($role['is_system']): ?>
                        <span class="badge bg-secondary">System</span>
                        <?php endif; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Role Types</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><strong>Administrator:</strong> Full system access</li>
                    <li class="mb-2"><strong>Branch Manager:</strong> Manage branch operations</li>
                    <li class="mb-2"><strong>Operations Manager:</strong> Daily operations</li>
                    <li class="mb-2"><strong>Cashier:</strong> Handle cash transactions</li>
                    <li class="mb-2"><strong>Teller:</strong> Process deposits/withdrawals</li>
                    <li class="mb-2"><strong>Collector:</strong> Collect loan repayments</li>
                    <li><strong>Loan Officer:</strong> Process loan applications</li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-shield-alt me-2"></i>Permissions</h5>
                <button class="btn btn-sm btn-success" id="savePermissions">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
            <div class="card-body">
                <?php foreach ($grouped_permissions as $module => $perms): ?>
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted mb-3">
                        <i class="fas fa-folder me-2"></i><?php echo ucfirst($module); ?>
                    </h6>
                    <div class="row">
                        <?php foreach ($perms as $perm): ?>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input permission-check" type="checkbox" 
                                    id="perm_<?php echo $perm['id']; ?>" 
                                    data-permission-id="<?php echo $perm['id']; ?>">
                                <label class="form-check-label" for="perm_<?php echo $perm['id']; ?>">
                                    <?php echo htmlspecialchars($perm['name']); ?>
                                    <small class="d-block text-muted"><?php echo htmlspecialchars($perm['description'] ?? ''); ?></small>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_role.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Role Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g., senior_officer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.role-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelectorAll('.role-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
