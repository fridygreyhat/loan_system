<?php
$base_path = '../';
$page_title = 'Loan Statement';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

// view_loan_details.php links with ?loan_id=; ?id= is accepted too.
$loan_id = (int)($_GET['id'] ?? $_GET['loan_id'] ?? 0);

$loan = null;
$repayments = [];
$error = '';

if ($loan_id > 0) {
    try {
        $stmt = $con->prepare("
            SELECT l.*, b.fname, b.lname, b.mobile_no, b.email, b.address, b.city, b.country, b.unique_no,
                   lp.name AS product_name
            FROM loans l
            LEFT JOIN borrower b ON b.id = l.borrower_id
            LEFT JOIN loan_product lp ON lp.id = l.loan_product_id
            WHERE l.id = ?
        ");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($loan) {
            $stmt = $con->prepare("SELECT * FROM loan_repayment WHERE loan_id = ? ORDER BY paid_date ASC, id ASC");
            $stmt->execute([$loan_id]);
            $repayments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        $error = 'Could not load loan statement: ' . $e->getMessage();
    }
}

$principal_amount = (float)($loan['principal_amount'] ?? 0);
$totals = ['amount' => 0.0, 'principal' => 0.0, 'interest' => 0.0, 'fees' => 0.0, 'penalty' => 0.0];
foreach ($repayments as $r) {
    foreach ($totals as $k => $_) {
        $totals[$k] += (float)($r[$k] ?? 0);
    }
}
$closing_balance = $principal_amount - $totals['amount'];

function stmt_money($v): string
{
    return 'TZS ' . number_format((float)$v, 2);
}

function stmt_date($v): string
{
    return $v ? date('d M Y', strtotime($v)) : '-';
}

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
        <h1><i class="fas fa-file-alt text-primary me-2"></i><?php echo __('loan_statement'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <?php if ($loan): ?>
                <li class="breadcrumb-item"><a href="view_loan_details.php?id=<?php echo $loan_id; ?>"><?php echo __('loan_details'); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?php echo __('loan_statement'); ?></li>
            </ol>
        </nav>
    </div>
    <?php if ($loan): ?>
    <div class="d-flex gap-2">
        <a href="view_loan_details.php?id=<?php echo $loan_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i><?php echo __('back'); ?></a>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fas fa-print me-2"></i><?php echo __('print'); ?></button>
    </div>
    <?php endif; ?>
</div>

<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php elseif (!$loan): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Loan not found. <a href="view_loans.php">Back to loans</a></div>
<?php else: ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="mb-1"><?php echo __('loan_statement'); ?></h4>
                <div class="text-muted">Loan <?php echo htmlspecialchars($loan['loan_no'] ?: ('L-' . $loan_id)); ?></div>
            </div>
            <div class="text-end text-muted small">Generated: <?php echo date('d M Y H:i'); ?></div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <h6 class="text-uppercase text-muted small"><?php echo __('borrower'); ?></h6>
                <div class="fw-semibold"><?php echo htmlspecialchars(trim(($loan['fname'] ?? '') . ' ' . ($loan['lname'] ?? '')) ?: '-'); ?></div>
                <?php if (!empty($loan['unique_no'])): ?><div>ID: <?php echo htmlspecialchars($loan['unique_no']); ?></div><?php endif; ?>
                <div><?php echo __('phone'); ?>: <?php echo htmlspecialchars($loan['mobile_no'] ?? '-'); ?></div>
                <?php if (!empty($loan['email'])): ?><div><?php echo __('email'); ?>: <?php echo htmlspecialchars($loan['email']); ?></div><?php endif; ?>
                <div><?php echo htmlspecialchars(trim(($loan['address'] ?? '') . ' ' . ($loan['city'] ?? '') . ' ' . ($loan['country'] ?? ''))); ?></div>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless mb-0">
                    <tr><td class="text-muted">Loan Product</td><td class="text-end"><?php echo htmlspecialchars($loan['product_name'] ?? '-'); ?></td></tr>
                    <tr><td class="text-muted">Principal Amount</td><td class="text-end fw-semibold"><?php echo stmt_money($principal_amount); ?></td></tr>
                    <tr><td class="text-muted"><?php echo __('interest_rate'); ?></td><td class="text-end"><?php echo htmlspecialchars((string)($loan['loan_interest'] ?? '0')); ?>% <?php echo htmlspecialchars($loan['loan_interest_pd'] ?? ''); ?></td></tr>
                    <tr><td class="text-muted">Duration</td><td class="text-end"><?php echo htmlspecialchars(trim(($loan['loan_duration'] ?? '-') . ' ' . ($loan['loan_duration_pd'] ?? ''))); ?></td></tr>
                    <tr><td class="text-muted"><?php echo __('release_date'); ?></td><td class="text-end"><?php echo stmt_date($loan['release_date']); ?></td></tr>
                    <tr><td class="text-muted"><?php echo __('maturity_date'); ?></td><td class="text-end"><?php echo stmt_date($loan['maturity_date']); ?></td></tr>
                    <tr><td class="text-muted"><?php echo __('status'); ?></td><td class="text-end"><?php echo htmlspecialchars(ucfirst($loan['status'] ?? '-')); ?></td></tr>
                </table>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?php echo __('date'); ?></th>
                        <th><?php echo __('receipt_no'); ?></th>
                        <th><?php echo __('payment_method'); ?></th>
                        <th class="text-end"><?php echo __('amount'); ?></th>
                        <th class="text-end"><?php echo __('principal'); ?></th>
                        <th class="text-end"><?php echo __('interest'); ?></th>
                        <th class="text-end"><?php echo __('fees'); ?></th>
                        <th class="text-end">Penalty</th>
                        <th class="text-end"><?php echo __('balance'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="table-light">
                        <td></td>
                        <td><?php echo stmt_date($loan['release_date']); ?></td>
                        <td colspan="7">Loan disbursed (opening balance)</td>
                        <td class="text-end fw-semibold"><?php echo stmt_money($principal_amount); ?></td>
                    </tr>
                    <?php if (empty($repayments)): ?>
                    <tr><td colspan="10" class="text-center text-muted py-3"><?php echo __('no_repayments_made'); ?></td></tr>
                    <?php endif; ?>
                    <?php $balance = $principal_amount; $i = 1; foreach ($repayments as $r): $balance -= (float)$r['amount']; ?>
                    <tr>
                        <td><?php echo $i++; ?></td>
                        <td><?php echo stmt_date($r['paid_date']); ?></td>
                        <td><?php echo htmlspecialchars($r['receipt_no'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($r['payment_method'] ?? '-'); ?></td>
                        <td class="text-end"><?php echo number_format((float)$r['amount'], 2); ?></td>
                        <td class="text-end"><?php echo number_format((float)$r['principal'], 2); ?></td>
                        <td class="text-end"><?php echo number_format((float)$r['interest'], 2); ?></td>
                        <td class="text-end"><?php echo number_format((float)$r['fees'], 2); ?></td>
                        <td class="text-end"><?php echo number_format((float)$r['penalty'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($balance, 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light fw-semibold">
                    <tr>
                        <td colspan="4"><?php echo __('total'); ?></td>
                        <td class="text-end"><?php echo stmt_money($totals['amount']); ?></td>
                        <td class="text-end"><?php echo number_format($totals['principal'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['interest'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['fees'], 2); ?></td>
                        <td class="text-end"><?php echo number_format($totals['penalty'], 2); ?></td>
                        <td class="text-end"><?php echo stmt_money($closing_balance); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row justify-content-end">
            <div class="col-md-5">
                <table class="table table-sm mb-0">
                    <tr><td>Principal Amount</td><td class="text-end"><?php echo stmt_money($principal_amount); ?></td></tr>
                    <tr><td><?php echo __('total_paid'); ?></td><td class="text-end"><?php echo stmt_money($totals['amount']); ?></td></tr>
                    <tr class="fw-bold"><td><?php echo __('outstanding'); ?></td><td class="text-end"><?php echo stmt_money(max(0, $closing_balance)); ?></td></tr>
                </table>
                <p class="text-muted small mt-2 mb-0">Balance = principal amount minus total amount paid.</p>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
