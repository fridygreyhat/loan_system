<?php
$base_path = '../';
$page_title = 'Daily Report';
$current_page = 'reports';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$selected_date = $_GET['date'] ?? date('Y-m-d');
$transactions = [];
$daily_disbursed = 0;
$daily_collected = 0;

try {
    $stmt = $con->prepare("
        SELECT l.id, 'disbursement' as type, l.principal_amount as amount, 
               b.fname, b.lname, l.release_date as trans_date
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE l.release_date = ? AND l.status = 'approved'
        UNION ALL
        SELECT lr.loan_id as id, 'collection' as type, lr.amount,
               b.fname, b.lname, lr.paid_date as trans_date
        FROM loan_repayment lr
        LEFT JOIN loans l ON lr.loan_id = l.id
        LEFT JOIN borrower b ON l.borrower_id = b.id
        WHERE lr.paid_date = ?
        ORDER BY trans_date DESC
    ");
    $stmt->execute([$selected_date, $selected_date]);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($transactions as $t) {
        if ($t['type'] === 'disbursement') {
            $daily_disbursed += $t['amount'];
        } else {
            $daily_collected += $t['amount'];
        }
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-calendar-day me-2"></i><?php echo __('daily_report'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('reports'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('daily'); ?></li>
            </ol>
        </nav>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
        <button class="btn btn-outline-secondary"><i class="fas fa-download me-2"></i><?php echo __('export'); ?></button>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><?php echo __('date'); ?></label>
                <input type="date" name="date" class="form-control" value="<?php echo $selected_date; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i><?php echo __('filter'); ?></button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-hand-holding-usd"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($daily_disbursed, 2); ?></h3>
                <p><?php echo __('disbursed'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon success">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($daily_collected, 2); ?></h3>
                <p><?php echo __('collected'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($transactions); ?></h3>
                <p><?php echo __('transactions'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-list me-2"></i><?php echo __('transactions'); ?> - <?php echo date('M d, Y', strtotime($selected_date)); ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('type'); ?></th>
                        <th><?php echo __('amount'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <?php echo __('no_transactions_found'); ?>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><strong>#<?php echo $t['id']; ?></strong></td>
                        <td><?php echo htmlspecialchars(($t['fname'] ?? '') . ' ' . ($t['lname'] ?? '')); ?></td>
                        <td>
                            <?php if ($t['type'] === 'disbursement'): ?>
                            <span class="badge bg-warning"><?php echo __('disbursement'); ?></span>
                            <?php else: ?>
                            <span class="badge bg-success"><?php echo __('collection'); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo $t['type'] === 'collection' ? 'text-success' : 'text-warning'; ?>">
                            <strong>TZS <?php echo number_format($t['amount'] ?? 0, 2); ?></strong>
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
