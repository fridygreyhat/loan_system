<?php
$base_path = '../';
$page_title = 'Daily Collection Sheet';
$current_page = 'collection_sheets';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$collections = [];
$today = date('Y-m-d');
try {
    $stmt = $con->prepare("
        WITH sched AS (
            SELECT ls.*, SUM(ls.amount_due) OVER (PARTITION BY ls.loan_id ORDER BY ls.installment_no) AS cum_due
            FROM loan_schedule ls
        ), paid AS (
            SELECT loan_id, SUM(amount) AS total_paid
            FROM loan_repayment
            WHERE COALESCE(status, '') <> 'rejected'
            GROUP BY loan_id
        )
        SELECT s.loan_id, s.installment_no, s.due_date,
               GREATEST(0, LEAST(s.amount_due, s.cum_due - COALESCE(p.total_paid, 0))) AS amount,
               CASE WHEN s.cum_due - COALESCE(p.total_paid, 0) <= 0 THEN 'paid' ELSE 'due' END AS status,
               l.principal_amount, b.fname, b.lname, b.mobile_no
        FROM sched s
        JOIN loans l ON l.id = s.loan_id AND l.status = 'approved'
        LEFT JOIN paid p ON p.loan_id = s.loan_id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE s.due_date = ?
        ORDER BY b.fname, b.lname
    ");
    $stmt->execute([$today]);
    $collections = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $load_error = 'Could not load collection sheet: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('daily_collection'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('daily_collection'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <input type="date" class="form-control" value="<?php echo $today; ?>" id="collectionDate">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-calendar-day"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($collections); ?></h3>
                <p><?php echo __('due_loans'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format(array_sum(array_column($collections, 'amount')), 2); ?></h3>
                <p><?php echo __('amount_expected'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('collections'); ?> - <?php echo date('F d, Y', strtotime($today)); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($collections)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                            <?php echo __('no_repayments_found'); ?>.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($collections as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars(($item['fname'] ?? '') . ' ' . ($item['lname'] ?? '')); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['mobile_no'] ?? '-'); ?></td>
                        <td>#<?php echo $item['loan_id']; ?></td>
                        <td>TZS <?php echo number_format($item['amount'] ?? 0, 2); ?></td>
                        <td>
                            <?php 
                            $status = $item['status'] ?? 'pending';
                            $badge_class = $status === 'paid' ? 'success' : ($status === 'pending' ? 'warning' : 'danger');
                            ?>
                            <span class="badge bg-<?php echo $badge_class; ?>"><?php echo __($status); ?></span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="<?php echo $base_path; ?>repayments/add_repayment.php?loan_id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-success" title="<?php echo __('record_payment'); ?>">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="<?php echo $base_path; ?>loans/view_loan_details.php?id=<?php echo $item['loan_id']; ?>" class="btn btn-outline-primary" title="<?php echo __('view'); ?>">
                                    <i class="fas fa-eye"></i>
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
