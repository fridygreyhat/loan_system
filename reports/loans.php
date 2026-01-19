<?php
$base_path = '../';
$page_title = 'Loan Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$loans = [];
$total_principal = 0;
$total_interest = 0;
$total_loans = 0;

try {
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, lp.name as product_name
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        ORDER BY l.id DESC
    ");
    $loans = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_loans = count($loans);
    $total_principal = array_sum(array_column($loans, 'principal_amount'));
    $total_interest = array_sum(array_column($loans, 'loan_interest'));
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-file-invoice-dollar me-2"></i><?php echo __('loan_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('loans'); ?></li>
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
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_loans); ?></h3>
                <p><?php echo __('total_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_principal, 2); ?></h3>
                <p><?php echo __('total_principal'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_interest, 2); ?></h3>
                <p><?php echo __('total_interest'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('all_loans'); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('interest'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('release_date'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_loans_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($loans as $loan): ?>
                    <tr>
                        <td><strong>#<?php echo $loan['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td>TZS <?php echo number_format($loan['principal_amount'] ?? 0, 2); ?></td>
                        <td><?php echo $loan['loan_interest'] ?? 0; ?>%</td>
                        <td>
                            <?php 
                            $status = $loan['status'] ?? 'pending';
                            $badge_class = 'secondary';
                            if ($status === 'approved') $badge_class = 'success';
                            elseif ($status === 'pending') $badge_class = 'warning';
                            elseif ($status === 'denied') $badge_class = 'danger';
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo __($status); ?></span>
                        </td>
                        <td><?php echo $loan['release_date'] ? date('M d, Y', strtotime($loan['release_date'])) : '-'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
