<?php
$base_path = '../';
$page_title = 'Repayment Receipt';
$current_page = 'repayments';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$repayment_id = (int)($_GET['id'] ?? 0);

$rep = null;
$total_paid = 0.0;
$error = '';

if ($repayment_id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT lr.*, l.loan_no, l.principal_amount, l.status AS loan_status, l.release_date, l.maturity_date,
                   b.id AS borrower_id, b.fname, b.lname, b.mobile_no, b.address, b.city,
                   lp.name AS product_name,
                   s.fname AS staff_fname, s.lname AS staff_lname
            FROM loan_repayment lr
            LEFT JOIN loans l ON l.id = lr.loan_id
            LEFT JOIN borrower b ON b.id = l.borrower_id
            LEFT JOIN loan_product lp ON lp.id = l.loan_product_id
            LEFT JOIN staff s ON s.id = lr.created_by
            WHERE lr.id = ?
        ");
        $stmt->execute([$repayment_id]);
        $rep = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($rep) {
            // Total paid on this loan up to and including this repayment.
            $stmt = $con->prepare("
                SELECT COALESCE(SUM(amount), 0) FROM loan_repayment
                WHERE loan_id = ? AND (paid_date < ? OR (paid_date = ? AND id <= ?))
            ");
            $stmt->execute([$rep['loan_id'], $rep['paid_date'], $rep['paid_date'], $repayment_id]);
            $total_paid = (float)$stmt->fetchColumn();
        }
    } catch (PDOException $e) {
        $error = 'Could not load repayment: ' . $e->getMessage();
    }
}

$balance_after = (float)($rep['principal_amount'] ?? 0) - $total_paid;

include $base_path . 'includes/header.php';
?>

<style>
@media print {
    .sidebar, .top-navbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>

<div class="page-header d-flex justify-content-between align-items-center no-print">
    <div>
        <h1><i class="fas fa-receipt text-primary me-2"></i>Repayment Receipt</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_repayments.php"><?php echo __('repayments'); ?></a></li>
                <li class="breadcrumb-item active">Receipt</li>
            </ol>
        </nav>
    </div>
    <?php if ($rep): ?>
    <div class="d-flex gap-2">
        <a href="edit_repayment.php?id=<?php echo $repayment_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-edit me-2"></i><?php echo __('edit'); ?></a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$rep): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Repayment not found. <a href="view_repayments.php">Back to repayments</a></div>
<?php else: ?>
<div class="row justify-content-center">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                        <h4 class="mb-0">Payment Receipt</h4>
                        <div class="text-muted"><?php echo __('receipt_no'); ?>: <strong><?php echo htmlspecialchars($rep['receipt_no'] ?: ('R-' . $repayment_id)); ?></strong></div>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small"><?php echo __('date'); ?></div>
                        <div class="fw-semibold"><?php echo date('d M Y', strtotime($rep['paid_date'])); ?></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-6">
                        <div class="text-muted small text-uppercase"><?php echo __('borrower'); ?></div>
                        <div class="fw-semibold">
                            <?php if (!empty($rep['borrower_id'])): ?>
                            <a href="../borrowers/view_borrower.php?id=<?php echo (int)$rep['borrower_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars(trim($rep['fname'] . ' ' . $rep['lname'])); ?></a>
                            <?php else: ?>-<?php endif; ?>
                        </div>
                        <div><?php echo htmlspecialchars($rep['mobile_no'] ?? ''); ?></div>
                        <div class="small text-muted"><?php echo htmlspecialchars(trim(($rep['address'] ?? '') . ' ' . ($rep['city'] ?? ''))); ?></div>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <div class="text-muted small text-uppercase">Loan</div>
                        <div class="fw-semibold">
                            <a href="../loans/view_loan_details.php?id=<?php echo (int)$rep['loan_id']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($rep['loan_no'] ?: ('L-' . $rep['loan_id'])); ?></a>
                        </div>
                        <div class="small"><?php echo htmlspecialchars($rep['product_name'] ?? ''); ?></div>
                        <div class="small text-muted">Principal: TZS <?php echo number_format((float)$rep['principal_amount'], 2); ?></div>
                    </div>
                </div>

                <table class="table table-sm mb-3">
                    <tbody>
                        <tr><td><?php echo __('principal'); ?></td><td class="text-end">TZS <?php echo number_format((float)$rep['principal'], 2); ?></td></tr>
                        <tr><td><?php echo __('interest'); ?></td><td class="text-end">TZS <?php echo number_format((float)$rep['interest'], 2); ?></td></tr>
                        <tr><td><?php echo __('fees'); ?></td><td class="text-end">TZS <?php echo number_format((float)$rep['fees'], 2); ?></td></tr>
                        <tr><td>Penalty</td><td class="text-end">TZS <?php echo number_format((float)$rep['penalty'], 2); ?></td></tr>
                    </tbody>
                    <tfoot>
                        <tr class="fw-bold fs-5"><td><?php echo __('amount'); ?></td><td class="text-end text-success">TZS <?php echo number_format((float)$rep['amount'], 2); ?></td></tr>
                    </tfoot>
                </table>

                <div class="row small mb-3">
                    <div class="col-6"><span class="text-muted"><?php echo __('payment_method'); ?>:</span> <?php echo htmlspecialchars($rep['payment_method'] ?? '-'); ?></div>
                    <div class="col-6 text-end"><span class="text-muted"><?php echo __('status'); ?>:</span> <?php echo htmlspecialchars(ucfirst($rep['status'] ?? '-')); ?></div>
                    <div class="col-6"><span class="text-muted"><?php echo __('total_paid'); ?>:</span> TZS <?php echo number_format($total_paid, 2); ?></div>
                    <div class="col-6 text-end"><span class="text-muted">Balance after payment:</span> TZS <?php echo number_format(max(0, $balance_after), 2); ?></div>
                </div>

                <?php if (!empty($rep['notes'])): ?>
                <div class="small mb-3"><span class="text-muted"><?php echo __('notes'); ?>:</span> <?php echo nl2br(htmlspecialchars($rep['notes'])); ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between small text-muted border-top pt-3">
                    <span>Received by: <?php echo htmlspecialchars(trim(($rep['staff_fname'] ?? '') . ' ' . ($rep['staff_lname'] ?? '')) ?: '-'); ?></span>
                    <span>Recorded: <?php echo date('d M Y H:i', strtotime($rep['created_at'])); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
