<?php
$base_path = '../../';
$page_title = 'Borrower Groups';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';

$groups = [];
try {
    $stmt = $con->query("SELECT bg.*, bg.group_name AS name, COUNT(b.id) AS member_count FROM borrower_groups bg LEFT JOIN borrower b ON b.group_id = bg.id GROUP BY bg.id ORDER BY bg.group_name");
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Borrower Groups</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>borrowers/view_borrowers.php">Borrowers</a></li>
                <li class="breadcrumb-item active">Groups</li>
            </ol>
        </nav>
    </div>
    <a href="add_borrowers_group.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Group
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-users-cog me-2"></i>All Groups</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Group Name</th>
                        <th>Description</th>
                        <th>Members</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($groups)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No groups found. <a href="add_borrowers_group.php">Create one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($groups as $group): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($group['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($group['description'] ?? '-'); ?></td>
                        <td><?php echo $group['member_count'] ?? 0; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_group.php?id=<?php echo $group['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_borrowers_group.php?edit=<?php echo $group['id']; ?>" class="btn btn-outline-secondary" title="Edit">
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
