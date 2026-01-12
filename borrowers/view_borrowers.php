<?php
$base_path = '../';
$page_title = 'View Borrowers';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';

$borrowers = [];
try {
    $stmt = $con->query("SELECT * FROM borrower ORDER BY fname, lname");
    $borrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Borrowers</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Borrowers</li>
            </ol>
        </nav>
    </div>
    <a href="add_borrower.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Borrower
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($borrowers); ?></h3>
                <p>Total Borrowers</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Borrowers</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;" id="searchInput">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
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
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No borrowers found. <a href="add_borrower.php">Add one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($borrowers as $borrower): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($borrower['fname'] . ' ' . $borrower['lname']); ?></strong></td>
                        <td><?php echo htmlspecialchars($borrower['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($borrower['mobile'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($borrower['gender'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($borrower['address'] ?? '-'); ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_borrower.php?id=<?php echo $borrower['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_borrower_edit.php?id=<?php echo $borrower['id']; ?>" class="btn btn-outline-secondary" title="Edit">
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
