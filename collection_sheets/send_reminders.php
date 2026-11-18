<?php
$base_path = '../';
$page_title = 'Send Reminders';
$current_page = 'collection_sheets';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';
require_once $base_path . 'helpers/sms.php';

const REMINDER_DAYS_AHEAD = 3;

$error = '';
$results = [];   // loan_id => ['ok' => bool, 'message' => string, ...]
$loans = [];

if (empty($_SESSION['send_reminders_token'])) {
    $_SESSION['send_reminders_token'] = bin2hex(random_bytes(16));
}
$token = $_SESSION['send_reminders_token'];

/**
 * Approved loans that need a reminder: an unpaid schedule instalment due within
 * REMINDER_DAYS_AHEAD days (or already overdue), or past maturity with a balance left.
 */
function reminder_candidates(PDO $con, array $onlyIds = []): array
{
    $sql = "
        WITH paid AS (
            SELECT loan_id, SUM(amount) AS total_paid FROM loan_repayment GROUP BY loan_id
        ), sched AS (
            SELECT loan_id, MIN(due_date) AS next_due, SUM(amount_due) AS amount_due, COUNT(*) AS instalments
            FROM loan_schedule
            WHERE due_date <= CURRENT_DATE + " . (int)REMINDER_DAYS_AHEAD . " AND status <> 'paid'
            GROUP BY loan_id
        )
        SELECT l.id, l.loan_no, l.principal_amount, l.maturity_date,
               b.id AS borrower_id, b.fname, b.lname, b.mobile_no,
               COALESCE(p.total_paid, 0) AS total_paid,
               l.principal_amount - COALESCE(p.total_paid, 0) AS outstanding,
               s.next_due, s.amount_due, s.instalments,
               (l.maturity_date < CURRENT_DATE AND l.principal_amount - COALESCE(p.total_paid, 0) > 0) AS past_maturity
        FROM loans l
        JOIN borrower b ON b.id = l.borrower_id
        LEFT JOIN paid p ON p.loan_id = l.id
        LEFT JOIN sched s ON s.loan_id = l.id
        WHERE l.status = 'approved'
          AND (s.loan_id IS NOT NULL
               OR (l.maturity_date < CURRENT_DATE AND l.principal_amount - COALESCE(p.total_paid, 0) > 0))
    ";
    $params = [];
    if ($onlyIds) {
        $sql .= " AND l.id IN (" . implode(',', array_fill(0, count($onlyIds), '?')) . ")";
        $params = $onlyIds;
    }
    $sql .= " ORDER BY COALESCE(s.next_due, l.maturity_date), l.id";
    $stmt = $con->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function reminder_message(array $loan): string
{
    $name = trim($loan['fname'] . ' ' . $loan['lname']);
    $ref = $loan['loan_no'] ?: ('L-' . $loan['id']);
    if (!empty($loan['next_due'])) {
        $due = date('d M Y', strtotime($loan['next_due']));
        $verb = $loan['next_due'] < date('Y-m-d') ? 'was due on' : 'is due on';
        return "Dear {$name}, your loan {$ref} repayment of TZS " . number_format((float)$loan['amount_due'], 2)
            . " {$verb} {$due}. Please make payment on time to avoid penalties. - Loan Store";
    }
    return "Dear {$name}, your loan {$ref} passed its maturity date on " . date('d M Y', strtotime($loan['maturity_date']))
        . " with TZS " . number_format((float)$loan['outstanding'], 2) . " outstanding. Please make payment as soon as possible. - Loan Store";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = array_values(array_unique(array_filter(array_map('intval', (array)($_POST['loan_ids'] ?? [])))));
    if (!hash_equals($token, (string)($_POST['token'] ?? ''))) {
        $error = 'Your session has expired. Please reload the page and try again.';
    } elseif (!$selected) {
        $error = 'Please select at least one loan.';
    } else {
        try {
            $targets = [];
            foreach (reminder_candidates($con, $selected) as $row) {
                $targets[(int)$row['id']] = $row;
            }
            $log = $con->prepare("
                INSERT INTO communication_logs (loan_id, type, recipient, subject, message, status, error_message, sent_at, created_at)
                VALUES (?, 'sms', ?, ?, ?, ?, ?, ?, NOW())
            ");

            foreach ($selected as $lid) {
                if (!isset($targets[$lid])) {
                    $results[$lid] = ['ok' => false, 'label' => 'Loan #' . $lid, 'message' => 'Loan no longer needs a reminder or does not exist.'];
                    continue;
                }
                $loan = $targets[$lid];
                $label = ($loan['loan_no'] ?: ('L-' . $lid)) . ' - ' . trim($loan['fname'] . ' ' . $loan['lname']);
                $phone = trim((string)$loan['mobile_no']);
                $text = reminder_message($loan);
                $subject = 'Repayment reminder - loan ' . ($loan['loan_no'] ?: ('L-' . $lid));

                if ($phone === '') {
                    $log->execute([$lid, '(none)', $subject, $text, 'failed', 'Borrower has no mobile number', null]);
                    $results[$lid] = ['ok' => false, 'label' => $label, 'message' => 'Borrower has no mobile number.'];
                    continue;
                }

                $res = sendSMS($phone, $text);
                $ok = !empty($res['success']);
                $err = $ok ? null : (string)($res['error'] ?? 'Unknown error');
                $log->execute([$lid, $phone, $subject, $text, $ok ? 'sent' : 'failed', $err, $ok ? date('Y-m-d H:i:s') : null]);
                $results[$lid] = [
                    'ok' => $ok,
                    'label' => $label,
                    'message' => $ok ? 'Sent to ' . $phone : 'Failed to send to ' . $phone . ': ' . $err,
                ];
            }
        } catch (PDOException $e) {
            $error = 'Database error while sending reminders: ' . $e->getMessage();
        }
    }
}

try {
    $loans = reminder_candidates($con);
} catch (PDOException $e) {
    $error = $error ?: 'Could not load loans: ' . $e->getMessage();
}

$sent = count(array_filter($results, fn($r) => $r['ok']));
$failed = count($results) - $sent;
$today = date('Y-m-d');

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-bell text-warning me-2"></i><?php echo __('send_reminders'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><?php echo __('collection_sheets'); ?></li>
                <li class="breadcrumb-item active"><?php echo __('send_reminders'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($results): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-paper-plane me-2"></i>Results: <?php echo $sent; ?> sent, <?php echo $failed; ?> failed</h5>
    </div>
    <ul class="list-group list-group-flush">
        <?php foreach ($results as $r): ?>
        <li class="list-group-item d-flex align-items-start gap-2">
            <?php if ($r['ok']): ?>
            <i class="fas fa-check-circle text-success mt-1"></i>
            <?php else: ?>
            <i class="fas fa-times-circle text-danger mt-1"></i>
            <?php endif; ?>
            <div>
                <strong><?php echo htmlspecialchars($r['label']); ?></strong><br>
                <span class="small <?php echo $r['ok'] ? 'text-success' : 'text-danger'; ?>"><?php echo htmlspecialchars($r['message']); ?></span>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Loans due within <?php echo REMINDER_DAYS_AHEAD; ?> days, overdue or past maturity (<?php echo count($loans); ?>)</h5>
        <button type="submit" class="btn btn-warning" <?php echo $loans ? '' : 'disabled'; ?>><i class="fas fa-sms me-2"></i>Send SMS reminders</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" class="form-check-input" id="selectAll" title="<?php echo __('select_all'); ?>"></th>
                        <th><?php echo __('loan_number'); ?></th>
                        <th><?php echo __('borrower'); ?></th>
                        <th><?php echo __('phone'); ?></th>
                        <th><?php echo __('due_date'); ?></th>
                        <th class="text-end">Amount Due</th>
                        <th class="text-end"><?php echo __('outstanding'); ?></th>
                        <th>Reason</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($loans)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4"><i class="fas fa-check-circle fa-2x d-block mb-2"></i>No approved loans currently need a reminder.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($loans as $l):
                        $lid = (int)$l['id'];
                        $hasPhone = trim((string)$l['mobile_no']) !== '';
                        if (!empty($l['next_due'])) {
                            $isOverdue = $l['next_due'] < $today;
                            $reason = $isOverdue ? 'Overdue instalment' : 'Instalment due soon';
                            if ((int)$l['instalments'] > 1) {
                                $reason .= ' (' . (int)$l['instalments'] . ' unpaid)';
                            }
                            $dueDate = $l['next_due'];
                            $amountDue = (float)$l['amount_due'];
                        } else {
                            $isOverdue = true;
                            $reason = 'Past maturity date';
                            $dueDate = $l['maturity_date'];
                            $amountDue = (float)$l['outstanding'];
                        }
                        $checked = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($results[$lid]) && !$results[$lid]['ok'];
                    ?>
                    <tr>
                        <td><input type="checkbox" class="form-check-input loan-check" name="loan_ids[]" value="<?php echo $lid; ?>" <?php echo $checked ? 'checked' : ''; ?>></td>
                        <td><a href="../loans/view_loan_details.php?id=<?php echo $lid; ?>"><?php echo htmlspecialchars($l['loan_no'] ?: ('L-' . $lid)); ?></a></td>
                        <td><a href="../borrowers/view_borrower.php?id=<?php echo (int)$l['borrower_id']; ?>"><?php echo htmlspecialchars(trim($l['fname'] . ' ' . $l['lname'])); ?></a></td>
                        <td><?php echo $hasPhone ? htmlspecialchars($l['mobile_no']) : '<span class="text-danger small">No phone</span>'; ?></td>
                        <td class="<?php echo $isOverdue ? 'text-danger fw-semibold' : ''; ?>"><?php echo $dueDate ? date('d M Y', strtotime($dueDate)) : '-'; ?></td>
                        <td class="text-end">TZS <?php echo number_format($amountDue, 2); ?></td>
                        <td class="text-end">TZS <?php echo number_format(max(0, (float)$l['outstanding']), 2); ?></td>
                        <td><span class="badge <?php echo $isOverdue ? 'bg-danger' : 'bg-warning text-dark'; ?>"><?php echo htmlspecialchars($reason); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</form>

<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.loan-check').forEach(cb => { cb.checked = this.checked; });
});
</script>

<?php include $base_path . 'includes/footer.php'; ?>
