<?php
$base_path = '../../';
$page_title = 'Borrower Groups';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';

$groups = [];
try {
    $stmt = $con->query("
        SELECT bg.*, COUNT(b.id) as member_count
        FROM borrower_groups bg
        LEFT JOIN borrower b ON b.group_id = bg.id
        GROUP BY bg.id
        ORDER BY bg.group_name ASC
    ");
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
    <a href="add_borrowers_group.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Group</a>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-users me-2"></i>All Groups</h5>
        <input type="text" class="form-control w-25" placeholder="Search groups..." id="searchInput">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Group Name</th>
                        <th>Members</th>
                        <th>Leader</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($groups)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-users fa-3x mb-3 d-block"></i>
                            No groups created yet.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($groups as $index => $group): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($group['group_name'] ?? ''); ?></strong></td>
                        <td><span class="badge bg-primary"><?php echo $group['member_count']; ?></span></td>
                        <td><?php echo htmlspecialchars($group['leader_name'] ?? '-'); ?></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="view_group_details.php?id=<?php echo $group['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>
                            <a href="add_borrowers_group.php?edit=<?php echo $group['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
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
