<?php
$base_path = '../';
$page_title = 'Delete Loan';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

// view_loan_details.php links with ?loan_id=; ?id= is accepted too.
$loan_id = (int)($_POST['loan_id'] ?? $_GET['id'] ?? $_GET['loan_id'] ?? 0);

$loan = null;
$counts = ['repayments' => 0, 'schedule' => 0, 'charges' => 0];
$error = '';

if (empty($_SESSION['delete_loan_token'])) {
    $_SESSION['delete_loan_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['delete_loan_token'];

if ($loan_id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT l.id, l.loan_no, l.principal_amount, l.status, l.release_date, b.fname, b.lname
            FROM loans l
            LEFT JOIN borrower b ON b.id = l.borrower_id
            WHERE l.id = ?
        ");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        $error = 'Could not load loan: ' . $e->getMessage();
    }
}

if ($loan && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($token, (string)($_POST['token'] ?? ''))) {
        $error = 'Your session has expired. Please confirm the deletion again.';
    } else {
        try {
            $con->beginTransaction();
            // loan_applied_charges has no FK to loans, so remove its rows explicitly.
            $con->prepare("DELETE FROM loan_applied_charges WHERE loan_id = ?")->execute([$loan_id]);
            // loan_repayment, loan_schedule, loan_guarantors and loan_comments cascade via FK.
            $con->prepare("DELETE FROM loans WHERE id = ?")->execute([$loan_id]);
            $con->commit();
            header('Location: view_loans.php');
            exit;
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $error = 'Could not delete loan: ' . $e->getMessage();
        }
    }
}

if ($loan) {
    try {
        $stmt = $con->prepare("
            SELECT (SELECT COUNT(*) FROM loan_repayment WHERE loan_id = ?) AS repayments,
                   (SELECT COUNT(*) FROM loan_schedule WHERE loan_id = ?) AS schedule,
                   (SELECT COUNT(*) FROM loan_applied_charges WHERE loan_id = ?) AS charges
        ");
        $stmt->execute([$loan_id, $loan_id, $loan_id]);
        $counts = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = $error ?: 'Could not load related records: ' . $e->getMessage();
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-trash text-danger me-2"></i><?php echo __('delete_loan'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('delete_loan'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if (!$loan): ?>
<?php if (!$error): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Loan not found. <a href="view_loans.php">Back to loans</a></div>
<?php endif; ?>
<?php else: ?>
<div class="row">
    <div class="col-lg-7">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Confirm deletion</h5>
            </div>
            <div class="card-body">
                <p>Are you sure you want to permanently delete this loan? This cannot be undone.</p>
                <table class="table table-sm mb-3">
                    <tr><th class="w-50"><?php echo __('loan_number'); ?></th><td><?php echo htmlspecialchars($loan['loan_no'] ?: ('L-' . $loan_id)); ?></td></tr>
                    <tr><th><?php echo __('borrower'); ?></th><td><?php echo htmlspecialchars(trim(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')) ?: '-'); ?></td></tr>
                    <tr><th>Principal Amount</th><td>TZS <?php echo number_format((float)$loan['principal_amount'], 2); ?></td></tr>
                    <tr><th><?php echo __('release_date'); ?></th><td><?php echo htmlspecialchars($loan['release_date'] ?? '-'); ?></td></tr>
                    <tr><th><?php echo __('status'); ?></th><td><?php echo htmlspecialchars(ucfirst($loan['status'] ?? '-')); ?></td></tr>
                </table>
                <div class="alert alert-warning small">
                    The following related records will also be deleted:
                    <?php echo (int)$counts['repayments']; ?> repayment(s),
                    <?php echo (int)$counts['schedule']; ?> schedule row(s),
                    <?php echo (int)$counts['charges']; ?> applied charge(s), plus any guarantors and comments.
                </div>
                <form method="POST" action="delete_loan.php?id=<?php echo $loan_id; ?>" class="d-flex gap-2">
                    <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i><?php echo __('delete_loan'); ?></button>
                    <a href="view_loan_details.php?id=<?php echo $loan_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i><?php echo __('cancel'); ?></a>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
