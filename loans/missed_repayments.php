<?php
$base_path = '../';
$page_title = 'Missed Repayments';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$missed = [];
$total_missed_amount = 0;
try {
    // Loans that are active/overdue but have no payments or last payment was > 30 days ago
    $stmt = $con->query("
        SELECT l.*, b.fname, b.lname, b.mobile_no, lp.name as product_name,
               COALESCE(
                   (SELECT SUM(lr.amount) FROM loan_repayment lr WHERE lr.loan_id = l.id),
                   0
               ) as paid_amount,
               (SELECT MAX(lr.paid_date) FROM loan_repayment lr WHERE lr.loan_id = l.id) as last_payment_date
        FROM loans l
        LEFT JOIN borrower b ON l.borrower_id = b.id
        LEFT JOIN loan_product lp ON l.loan_product_id = lp.id
        WHERE l.status NOT IN ('paid','closed','written_off')
        AND (
            NOT EXISTS (SELECT 1 FROM loan_repayment lr WHERE lr.loan_id = l.id)
            OR (SELECT MAX(lr.paid_date) FROM loan_repayment lr WHERE lr.loan_id = l.id) < CURRENT_DATE - INTERVAL '30 days'
        )
        ORDER BY l.release_date ASC
    ");
    $missed = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($missed as $m) {
        $total_missed_amount += ($m['principal_amt'] ?? 0) - ($m['paid_amount'] ?? 0);
    }
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('missed_repayments'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('missed_repayments'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo count($missed); ?></h3>
                <p><?php echo __('missed_repayments'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon danger">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <div class="stat-info">
                <h3>TZS <?php echo number_format($total_missed_amount, 2); ?></h3>
                <p><?php echo __('total_outstanding'); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon warning">
                <i class="fas fa-percentage"></i>
            </div>
            <div class="stat-info">
                <?php
                try {
                    $totalLoans = $con->query("SELECT COUNT(*) FROM loans WHERE status NOT IN ('paid','closed','written_off')")->fetchColumn();
                    $pct = $totalLoans > 0 ? round((count($missed) / $totalLoans) * 100, 1) : 0;
                } catch (Exception $e) { $pct = 0; }
                ?>
                <h3><?php echo $pct; ?>%</h3>
                <p><?php echo __('of_active_loans'); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fas fa-exclamation-triangle me-2 text-danger"></i><?php echo __('missed_repayments'); ?></h5>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width:220px;">
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="missedTable">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?php echo __('loan_no'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('product'); ?></th>
                        <th><?php echo __('principal'); ?></th>
                        <th><?php echo __('outstanding'); ?></th>
                        <th><?php echo __('last_payment'); ?></th>
                        <th><?php echo __('days_overdue'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($missed)): ?>
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0"><?php echo __('no_missed_repayments'); ?></p>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $i = 1; foreach ($missed as $loan):
                        $outstanding = ($loan['principal_amt'] ?? 0) - ($loan['paid_amount'] ?? 0);
                        $lastPayment = $loan['last_payment_date'];
                        $daysOverdue = '-';
                        $overdueBadge = 'bg-secondary';
                        if ($lastPayment) {
                            $diff = (new DateTime())->diff(new DateTime($lastPayment));
                            $daysOverdue = $diff->days;
                            $overdueBadge = $daysOverdue > 90 ? 'bg-danger' : ($daysOverdue > 30 ? 'bg-warning text-dark' : 'bg-info');
                        } else {
                            $releaseDate = $loan['release_date'];
                            if ($releaseDate) {
                                try {
                                    $diff = (new DateTime())->diff(new DateTime($releaseDate));
                                    $daysOverdue = $diff->days;
                                    $overdueBadge = 'bg-danger';
                                } catch (Exception $e) {}
                            }
                        }
                    ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="fw-semibold text-primary"><?php echo htmlspecialchars($loan['loan_no'] ?? 'L-' . $loan['id']); ?></a></td>
                        <td><?php echo htmlspecialchars($loan['fname'] . ' ' . $loan['lname']); ?></td>
                        <td><?php echo htmlspecialchars($loan['mobile_no'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td>
                        <td class="fw-semibold">TZS <?php echo number_format($loan['principal_amt'] ?? 0, 2); ?></td>
                        <td class="text-danger fw-semibold">TZS <?php echo number_format($outstanding, 2); ?></td>
                        <td><?php echo $lastPayment ? date('d M Y', strtotime($lastPayment)) : '<span class="text-danger">Never</span>'; ?></td>
                        <td><span class="badge <?php echo $overdueBadge; ?>"><?php echo $daysOverdue !== '-' ? $daysOverdue . ' days' : '-'; ?></span></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="view_loan_details.php?id=<?php echo $loan['id']; ?>" class="btn btn-outline-primary btn-sm" title="View"><i class="fas fa-eye"></i></a>
                                <a href="../repayments/add_repayment.php?loan_id=<?php echo $loan['id']; ?>" class="btn btn-outline-success btn-sm" title="Record Payment"><i class="fas fa-plus"></i></a>
                                <a href="javascript:sendSmsAlert(<?php echo $loan['id']; ?>, '<?php echo htmlspecialchars($loan['mobile_no'] ?? ''); ?>')" class="btn btn-outline-warning btn-sm" title="Send SMS"><i class="fas fa-sms"></i></a>
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

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#missedTable tbody tr');
    rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
    });
});
function sendSmsAlert(loanId, phone) {
    if (!phone) { alert('No phone number for this borrower'); return; }
    if (confirm('Send SMS reminder to ' + phone + '?')) {
        fetch('../commons/send_reminder_sms.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({loan_id: loanId, phone: phone})
        }).then(r => r.json()).then(d => {
            alert(d.success ? 'SMS sent successfully!' : 'Failed: ' + (d.error || 'Unknown error'));
        });
    }
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>
