<?php
$base_path = '../';
$page_title = 'View Borrowers';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

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
        <h1><?php echo __('borrowers'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('borrowers'); ?></li>
            </ol>
        </nav>
    </div>
    <a href="add_borrower.php" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i><?php echo __('add_borrower'); ?>
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
                <p><?php echo __('total_borrowers'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('all_borrowers'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;" id="searchInput">
            <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-download"></i></button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('gender'); ?></th>
                        <th><?php echo __('address'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_borrowers_found'); ?>. <a href="add_borrower.php"><?php echo __('add_one_now'); ?></a>.
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
                                <a href="view_borrower.php?id=<?php echo $borrower['id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="add_borrower_edit.php?id=<?php echo $borrower['id']; ?>" class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>">
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
