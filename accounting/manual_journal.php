<?php
$base_path = '../';
$page_title = 'Manual Journal';
$current_page = 'accounting';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

$accounts = [];
try {
    $accounts = $con->query("SELECT id, account_code, account_name FROM chart_of_accounts WHERE status = 'active' ORDER BY account_code")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Could not load chart of accounts: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entry_date = $_POST['entry_date'] ?? date('Y-m-d');
    $reference = trim($_POST['reference'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $line_accounts = $_POST['account_id'] ?? [];
    $line_debits = $_POST['debit'] ?? [];
    $line_credits = $_POST['credit'] ?? [];

    $lines = [];
    $total_debit = 0.0;
    $total_credit = 0.0;
    foreach ((array)$line_accounts as $i => $account_id) {
        $account_id = (int)$account_id;
        $debit = round((float)($line_debits[$i] ?? 0), 2);
        $credit = round((float)($line_credits[$i] ?? 0), 2);
        if (!$account_id || ($debit <= 0 && $credit <= 0)) {
            continue;
        }
        if ($debit > 0 && $credit > 0) {
            $error = 'Each line must have either a debit or a credit, not both.';
            break;
        }
        $lines[] = [$account_id, max($debit, 0), max($credit, 0)];
        $total_debit += max($debit, 0);
        $total_credit += max($credit, 0);
    }

    if (!$error) {
        if (!date_create($entry_date)) {
            $error = 'Please enter a valid date.';
        } elseif (count($lines) < 2) {
            $error = 'A journal entry needs at least two lines.';
        } elseif (abs($total_debit - $total_credit) > 0.004) {
            $error = sprintf('Debits (TZS %s) and credits (TZS %s) must balance.', number_format($total_debit, 2), number_format($total_credit, 2));
        }
    }

    if (!$error) {
        if ($reference === '') {
            $reference = 'JV-' . date('Ymd-His');
        }
        try {
            $con->beginTransaction();
            $stmt = $con->prepare("INSERT INTO journal_entries (entry_date, account_id, description, reference, debit, credit, status, created_by) VALUES (?, ?, ?, ?, ?, ?, 'posted', ?)");
            foreach ($lines as [$account_id, $debit, $credit]) {
                $stmt->execute([$entry_date, $account_id, $description, $reference, $debit, $credit, $_SESSION['user_id'] ?? null]);
            }
            $con->commit();
            $message = "Journal entry $reference posted.";
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $error = 'Error saving journal entry: ' . $e->getMessage();
        }
    }
}

$journals = [];
try {
    $stmt = $con->query("
        SELECT je.*, je.entry_date AS date, coa.account_code, coa.account_name
        FROM journal_entries je
        LEFT JOIN chart_of_accounts coa ON je.account_id = coa.id
        ORDER BY je.entry_date DESC, je.id DESC
        LIMIT 100
    ");
    $journals = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $error ?: 'Could not load journal entries: ' . $e->getMessage();
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><?php echo __('manual_journal'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="#"><?php echo __('accounting'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('manual_journal'); ?></li>
            </ol>
        </nav>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJournalModal">
        <i class="fas fa-plus me-2"></i><?php echo __('add_entry'); ?>
    </button>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h5><i class="fas fa-file-alt me-2"></i><?php echo __('journal_entry'); ?>s</h5>
        <input type="text" class="form-control form-control-sm" placeholder="<?php echo __('search'); ?>..." style="width: 200px;">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th><?php echo __('reference'); ?></th>
                        <th><?php echo __('journal_date'); ?></th>
                        <th><?php echo __('description'); ?></th>
                        <th class="text-end"><?php echo __('debit'); ?></th>
                        <th class="text-end"><?php echo __('credit'); ?></th>
                        <th><?php echo __('status'); ?></th>
                        <th><?php echo __('actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($journals)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No journal entries yet.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($journals as $journal): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($journal['reference'] ?: ('#' . $journal['id'])); ?></span></td>
                        <td><?php echo date('M d, Y', strtotime($journal['date'])); ?></td>
                        <td>
                            <?php echo htmlspecialchars($journal['description'] ?? ''); ?>
                            <small class="d-block text-muted"><?php echo htmlspecialchars(trim(($journal['account_code'] ?? '') . ' ' . ($journal['account_name'] ?? ''))); ?></small>
                        </td>
                        <td class="text-end">TZS <?php echo number_format($journal['debit'], 2); ?></td>
                        <td class="text-end">TZS <?php echo number_format($journal['credit'], 2); ?></td>
                        <td>
                            <span class="badge bg-<?php echo $journal['status'] === 'posted' ? 'success' : 'warning'; ?>">
                                <?php echo __($journal['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="<?php echo __('view'); ?>"><i class="fas fa-eye"></i></button>
                                <?php if ($journal['status'] !== 'posted'): ?>
                                <button class="btn btn-outline-success" title="Post"><i class="fas fa-check"></i></button>
                                <button class="btn btn-outline-secondary" title="<?php echo __('edit'); ?>"><i class="fas fa-edit"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="addJournalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo __('add_entry'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="journalForm">
            <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('journal_date'); ?></label>
                            <input type="date" name="entry_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('reference'); ?></label>
                            <input type="text" name="reference" class="form-control" placeholder="Auto-generated" maxlength="100">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('description'); ?></label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <h6><?php echo __('journal_entry'); ?> Lines</h6>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th><?php echo __('account_name'); ?></th>
                                <th><?php echo __('debit'); ?></th>
                                <th><?php echo __('credit'); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="journalLines">
                            <?php for ($i = 0; $i < 2; $i++): ?>
                            <tr class="journal-line">
                                <td>
                                    <select name="account_id[]" class="form-select form-select-sm" required>
                                        <option value="">-- Select account --</option>
                                        <?php foreach ($accounts as $acc): ?>
                                        <option value="<?php echo (int)$acc['id']; ?>"><?php echo htmlspecialchars($acc['account_code'] . ' - ' . $acc['account_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="debit[]" step="0.01" min="0" class="form-control form-control-sm line-debit" placeholder="0.00"></td>
                                <td><input type="number" name="credit[]" step="0.01" min="0" class="form-control form-control-sm line-credit" placeholder="0.00"></td>
                                <td><button type="button" class="btn btn-sm btn-outline-danger remove-line"><i class="fas fa-times"></i></button></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-semibold">
                                <td class="text-end">Totals</td>
                                <td id="totalDebit">0.00</td>
                                <td id="totalCredit">0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="addLine"><i class="fas fa-plus me-2"></i>Add Line</button>
                    <div id="balanceWarning" class="text-danger small mt-2 d-none">Debits and credits must balance.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo __('cancel'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo __('save'); ?></button>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    const tbody = document.getElementById('journalLines');
    const fmt = n => n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    function totals() {
        let d = 0, c = 0;
        tbody.querySelectorAll('.line-debit').forEach(i => d += parseFloat(i.value) || 0);
        tbody.querySelectorAll('.line-credit').forEach(i => c += parseFloat(i.value) || 0);
        document.getElementById('totalDebit').textContent = fmt(d);
        document.getElementById('totalCredit').textContent = fmt(c);
        return Math.abs(d - c) < 0.005 && d > 0;
    }
    tbody.addEventListener('input', totals);
    tbody.addEventListener('click', e => {
        const btn = e.target.closest('.remove-line');
        if (btn && tbody.querySelectorAll('.journal-line').length > 2) {
            btn.closest('tr').remove();
            totals();
        }
    });
    document.getElementById('addLine').addEventListener('click', () => {
        const row = tbody.querySelector('.journal-line').cloneNode(true);
        row.querySelectorAll('input').forEach(i => i.value = '');
        row.querySelector('select').selectedIndex = 0;
        tbody.appendChild(row);
    });
    document.getElementById('journalForm').addEventListener('submit', e => {
        const ok = totals();
        document.getElementById('balanceWarning').classList.toggle('d-none', ok);
        if (!ok) e.preventDefault();
    });
})();
</script>

<?php include $base_path . 'includes/footer.php'; ?>
