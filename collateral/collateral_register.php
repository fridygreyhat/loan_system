<?php
$base_path = '../';
$page_title = 'Collateral Register';
$current_page = 'collateral';

require_once $base_path . 'helpers/connect.php';

$collaterals = [];
try {
    $stmt = $con->query("
        SELECT c.*, b.fname, b.lname, l.id as loan_id
        FROM collateral c
        LEFT JOIN borrower b ON c.borrower_id = b.id
        LEFT JOIN loans l ON c.loan_id = l.id
        ORDER BY c.id DESC
    ");
    $collaterals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1>Collateral Register</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Collateral Register</li>
            </ol>
        </nav>
    </div>
    <a href="add_collateral.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Add Collateral
    </a>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collaterals); ?></h3>
                <p>Total Collateral Items</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i>All Collateral</h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="Search..." style="width: 200px;">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Borrower</th>
                        <th>Loan #</th>
                        <th>Value</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collaterals)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            No collateral items found. <a href="add_collateral.php">Add one now</a>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collaterals as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name'] ?? $item['item_name'] ?? '-'); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['type'] ?? $item['collateral_type'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></td>
                        <td><?php echo $item['loan_id'] ? '#' . $item['loan_id'] : '-'; ?></td>
                        <td>$<?php echo number_format($item['value'] ?? $item['collateral_value'] ?? 0, 2); ?></td>
                        <td>
                            <?php 
                            $status = $item['status'] ?? 'active';
                            $badge_class = $status === 'active' ? 'success' : 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_collateral.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-primary" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit_collateral.php?id=<?php echo $item['id']; ?>" class="btn btn-outline-secondary" title="Edit">
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
