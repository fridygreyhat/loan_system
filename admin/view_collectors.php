<?php
$base_path = '../';
$page_title = 'Collectors';
$current_page = 'admin';

require_once $base_path . 'helpers/connect.php';

$collectors = [];
try {
    $stmt = $con->query("
        SELECT s.*, COUNT(l.id) as assigned_loans
        FROM staff s
        LEFT JOIN loans l ON l.collector_id = s.id
        WHERE s.role = 'Collector' OR s.role_name = 'Collector'
        GROUP BY s.id
        ORDER BY s.fname ASC
    ");
    $collectors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Collectors</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="index.php">Admin</a></li>
                <li class="breadcrumb-item active">Collectors</li>
            </ol>
        </nav>
    </div>
    <a href="add_collector.html" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Add Collector</a>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-user-tie me-2"></i>All Collectors</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Assigned Loans</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collectors)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-user-tie fa-3x mb-3 d-block"></i>
                            No collectors found. Add staff with "Collector" role.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collectors as $index => $collector): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars(($collector['fname'] ?? '') . ' ' . ($collector['lname'] ?? '')); ?></strong></td>
                        <td><?php echo htmlspecialchars($collector['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($collector['email'] ?? '-'); ?></td>
                        <td><span class="badge bg-primary"><?php echo $collector['assigned_loans'] ?? 0; ?></span></td>
                        <td><span class="badge bg-success">Active</span></td>
                        <td>
                            <a href="add_collector_edit.html?id=<?php echo $collector['id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
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
