<?php
$base_path = '../';
$page_title = 'Guarantors';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$guarantors = [];
try {
    $stmt = $con->query("
        SELECT g.*, l.id as loan_id, l.principal_amount,
               b.fname as borrower_fname, b.lname as borrower_lname
        FROM loan_guarantors g
        LEFT JOIN loans l ON g.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        ORDER BY g.id DESC
    ");
    $guarantors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-shield text-info me-2"></i><?php echo __('guarantors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('guarantors'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon info">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($guarantors); ?></h3>
                <p><?php echo __('guarantors'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('guarantors'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><?php echo __('name'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('relationship'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('loan'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($guarantors)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_data_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($guarantors as $g): ?>
                    <tr>
                        <td><?php echo $g['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($g['name'] ?? '-'); ?></strong></td>
                        <td><?php echo htmlspecialchars($g['phone'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($g['relationship'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars(($g['borrower_fname'] ?? '') . ' ' . ($g['borrower_lname'] ?? '')); ?></td>
                        <td>
                            <a href="view_loan_details.php?id=<?php echo $g['loan_id']; ?>">
                                #<?php echo $g['loan_id']; ?> - TZS <?php echo number_format($g['principal_amount'] ?? 0, 0); ?>
                            </a>
                        </td>
                        <td>
                            <a href="view_loan_details.php?id=<?php echo $g['loan_id']; ?>" class="btn btn-sm btn-outline-primary">
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
