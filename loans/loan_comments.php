<?php
$base_path = '../';
$page_title = 'Loan Comments';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$comments = [];
try {
    $stmt = $con->query("
        SELECT c.*, l.id as loan_id, l.principal_amount,
               b.fname as borrower_fname, b.lname as borrower_lname,
               s.fname as staff_fname, s.lname as staff_lname
        FROM loan_comments c
        LEFT JOIN loans l ON c.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN staff s ON c.staff_id = s.id
        ORDER BY c.created_at DESC
    ");
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-comments text-primary me-2"></i><?php echo __('loan_comments'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loan_comments'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($comments); ?></h3>
                <p><?php echo __('loan_comments'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('loan_comments'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('loan'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('comment'); ?></th>
                        <th><?php echo __('staff'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($comments as $c): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i', strtotime($c['created_at'])); ?></td>
                        <td>
                            <a href="view_loan_details.php?id=<?php echo $c['loan_id']; ?>">
                                #<?php echo $c['loan_id']; ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars(($c['borrower_fname'] ?? '') . ' ' . ($c['borrower_lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars(substr($c['comment'] ?? '', 0, 100)); ?>...</td>
                        <td><?php echo htmlspecialchars(($c['staff_fname'] ?? '') . ' ' . ($c['staff_lname'] ?? '')); ?></td>
                        <td>
                            <a href="view_loan_details.php?id=<?php echo $c['loan_id']; ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i>
                            </a>
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
