<?php
$base_path = '../';
$page_title = 'Borrowers Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$borrowers = [];
$total_borrowers = 0;
$active_borrowers = 0;
$new_this_month = 0;

try {
    $stmt = $con->query("SELECT * FROM borrower ORDER BY id DESC");
    $borrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_borrowers = count($borrowers);
    
    $stmt = $con->query("SELECT COUNT(*) FROM borrower b WHERE EXISTS (SELECT 1 FROM loans l WHERE l.borrower_id = b.id AND l.status = 'approved')");
    $active_borrowers = $stmt->fetchColumn() ?: 0;
    
    $stmt = $con->query("SELECT COUNT(*) FROM borrower WHERE created_at >= DATE_TRUNC('month', CURRENT_DATE)");
    $new_this_month = $stmt->fetchColumn() ?: 0;
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-users me-2"></i><?php echo __('borrowers_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('borrowers'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_borrowers); ?></h3>
                <p><?php echo __('total_borrowers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($active_borrowers); ?></h3>
                <p><?php echo __('active_borrowers'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($new_this_month); ?></h3>
                <p><?php echo __('new_this_month'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('all_borrowers'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('name'); ?></th>
                        <th><?php echo __('email'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('address'); ?></th>
                        <th><?php echo __('status'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($borrowers)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_borrowers_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($borrowers as $borrower): ?>
                    <tr>
                        <td><?php echo $borrower['id']; ?></td>
                        <td><?php echo htmlspecialchars(($borrower['fname'] ?? '') . ' ' . ($borrower['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($borrower['email'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($borrower['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($borrower['address'] ?? '-'); ?></td>
                        <td><span class="badge bg-success"><?php echo __('active'); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
