<?php
$base_path = '../';
$page_title = 'Edit Loan';
$current_page = 'loans';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';
require_once $base_path . 'helpers/loan_utils.php';

// Links pass either ?id= (view_loans.php) or ?loan_id= (view_loan_details.php).
$loan_id = (int)($_GET['id'] ?? $_GET['loan_id'] ?? 0);

$loan = null;
$error = '';
$products = [];
$borrowers = [];
$collectors = [];
$officers = [];

$interest_methods = [
    'flat_rate' => __('flat_rate'),
    'reducing_balance' => __('reducing_balance'),
    'interest_only' => 'Interest Only',
    'compound_interest' => 'Compound Interest',
];
$interest_periods = ['Day' => __('per_day'), 'Week' => __('per_week'), 'Month' => __('per_month'), 'Year' => __('per_year')];
$duration_units = ['Days' => __('days'), 'Weeks' => __('weeks'), 'Months' => __('months'), 'Years' => __('years')];
$repayment_cycles = ['Daily' => __('daily'), 'Weekly' => __('weekly'), 'Bi-Weekly' => 'Bi-Weekly', 'Monthly' => __('monthly'), 'Quarterly' => 'Quarterly', 'Yearly' => __('yearly')];

if ($loan_id > 0) {
    try {
        $stmt = $con->prepare("SELECT * FROM loans WHERE id = ?");
        $stmt->execute([$loan_id]);
        $loan = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        $error = 'Could not load loan: ' . $e->getMessage();
    }
}

// Values shown in the form: POSTed values win over stored ones so a failed save keeps the user's input.
$form = $loan ?? [];

if ($loan && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $form = [
        'loan_product_id'    => (int)($_POST['loan_product_id'] ?? 0),
        'borrower_id'        => (int)($_POST['borrower_id'] ?? 0),
        'loan_no'            => trim($_POST['loan_no'] ?? ''),
        'principal_amount'   => trim($_POST['principal_amount'] ?? ''),
        'release_date'       => trim($_POST['release_date'] ?? ''),
        'interest_mtd'       => $_POST['interest_mtd'] ?? '',
        'loan_interest'      => trim($_POST['loan_interest'] ?? ''),
        'loan_interest_pd'   => $_POST['loan_interest_pd'] ?? '',
        'loan_duration'      => (int)($_POST['loan_duration'] ?? 0),
        'loan_duration_pd'   => $_POST['loan_duration_pd'] ?? '',
        'repayment_cycle'    => $_POST['repayment_cycle'] ?? '',
        'no_repayment_cycle' => (int)($_POST['no_repayment_cycle'] ?? 0),
        'description'        => trim($_POST['description'] ?? ''),
        'status'             => $_POST['status'] ?? '',
        'collector_id'       => (int)($_POST['collector_id'] ?? 0),
        'loan_officer_id'    => (int)($_POST['loan_officer_id'] ?? 0),
    ];

    $releaseDt = DateTime::createFromFormat('!Y-m-d', $form['release_date']);
    $allowed_statuses = LOAN_STATUSES;
    if (!empty($loan['status'])) {
        $allowed_statuses[] = $loan['status']; // keep a legacy status if it is left unchanged
    }

    if ($form['loan_product_id'] <= 0 || $form['borrower_id'] <= 0) {
        $error = 'Please select a loan product and a borrower.';
    } elseif (!is_numeric($form['principal_amount']) || (float)$form['principal_amount'] <= 0) {
        $error = 'Principal amount must be a number greater than zero.';
    } elseif (!$releaseDt || $releaseDt->format('Y-m-d') !== $form['release_date']) {
        $error = 'Please enter a valid release date.';
    } elseif ($form['loan_interest'] !== '' && (!is_numeric($form['loan_interest']) || (float)$form['loan_interest'] < 0)) {
        $error = 'Interest must be a non-negative number.';
    } elseif ($form['loan_duration'] < 1) {
        $error = 'Loan duration must be at least 1.';
    } elseif (!isset($duration_units[$form['loan_duration_pd']])) {
        $error = 'Please select a valid duration unit.';
    } elseif (!in_array($form['status'], $allowed_statuses, true)) {
        $error = 'Please select a valid status.';
    } else {
        try {
            $chk = $con->prepare("SELECT (SELECT COUNT(*) FROM loan_product WHERE id = ?) AS p, (SELECT COUNT(*) FROM borrower WHERE id = ?) AS b");
            $chk->execute([$form['loan_product_id'], $form['borrower_id']]);
            $exists = $chk->fetch(PDO::FETCH_ASSOC);
            if (!$exists['p']) {
                $error = 'Selected loan product does not exist.';
            } elseif (!$exists['b']) {
                $error = 'Selected borrower does not exist.';
            } else {
                $maturity = compute_maturity_date($form['release_date'], $form['loan_duration'], $form['loan_duration_pd']);
                $con->beginTransaction();
                $stmt = $con->prepare("
                    UPDATE loans SET
                        loan_product_id = ?, borrower_id = ?, loan_no = ?, principal_amount = ?, release_date = ?,
                        interest_mtd = ?, loan_interest = ?, loan_interest_pd = ?, loan_duration = ?, loan_duration_pd = ?,
                        repayment_cycle = ?, no_repayment_cycle = ?, description = ?, status = ?,
                        collector_id = ?, loan_officer_id = ?, maturity_date = ?,
                        last_modified_by = ?, last_modified_date = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([
                    $form['loan_product_id'],
                    $form['borrower_id'],
                    $form['loan_no'] !== '' ? $form['loan_no'] : null,
                    $form['principal_amount'],
                    $form['release_date'],
                    $form['interest_mtd'] !== '' ? $form['interest_mtd'] : null,
                    $form['loan_interest'] !== '' ? $form['loan_interest'] : 0,
                    $form['loan_interest_pd'] !== '' ? $form['loan_interest_pd'] : null,
                    $form['loan_duration'],
                    $form['loan_duration_pd'],
                    $form['repayment_cycle'] !== '' ? $form['repayment_cycle'] : null,
                    $form['no_repayment_cycle'] > 0 ? $form['no_repayment_cycle'] : null,
                    $form['description'] !== '' ? $form['description'] : null,
                    $form['status'],
                    $form['collector_id'] > 0 ? $form['collector_id'] : null,
                    $form['loan_officer_id'] > 0 ? $form['loan_officer_id'] : null,
                    $maturity,
                    (int)($_SESSION['user_id'] ?? 0) ?: null,
                    $loan_id,
                ]);
                // Terms may have changed, so rebuild the instalment schedule.
                regenerate_loan_schedule($con, $loan_id);
                $con->commit();
                header('Location: view_loan_details.php?id=' . $loan_id);
                exit;
            }
        } catch (PDOException $e) {
            if ($con->inTransaction()) {
                $con->rollBack();
            }
            $error = 'Could not save loan: ' . $e->getMessage();
        }
    }
}

if ($loan) {
    try {
        $stmt = $con->prepare("SELECT id, name FROM loan_product WHERE active_flg = 1 OR id = ? ORDER BY name");
        $stmt->execute([(int)$loan['loan_product_id']]);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $borrowers = $con->query("SELECT id, fname, lname, mobile_no FROM borrower ORDER BY fname, lname")->fetchAll(PDO::FETCH_ASSOC);

        $staffStmt = $con->prepare("
            SELECT s.id, s.fname, s.lname
            FROM staff s
            LEFT JOIN staff_role_types r ON r.id = s.role_id
            WHERE r.code = ? OR s.id = ?
            ORDER BY s.fname, s.lname
        ");
        $staffStmt->execute(['collector', (int)($loan['collector_id'] ?? 0)]);
        $collectors = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
        $staffStmt->execute(['loan_officer', (int)($loan['loan_officer_id'] ?? 0)]);
        $officers = $staffStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Could not load form options: ' . $e->getMessage();
    }
}

function edit_loan_options(array $options, $selected): string
{
    $html = '';
    $selected = (string)$selected;
    if ($selected !== '' && !array_key_exists($selected, $options)) {
        $options = [$selected => $selected] + $options; // preserve a legacy value
    }
    foreach ($options as $value => $label) {
        $html .= '<option value="' . htmlspecialchars((string)$value) . '"' . ((string)$value === $selected ? ' selected' : '') . '>'
            . htmlspecialchars((string)$label) . '</option>';
    }
    return $html;
}

$status_options = [];
foreach (LOAN_STATUSES as $s) {
    $status_options[$s] = __($s);
}

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-edit text-primary me-2"></i><?php echo __('edit_loan'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_loans.php"><?php echo __('loans'); ?></a></li>
                <?php if ($loan): ?>
                <li class="breadcrumb-item"><a href="view_loan_details.php?id=<?php echo $loan_id; ?>"><?php echo __('loan_details'); ?></a></li>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?php echo __('edit_loan'); ?></li>
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

<?php if (!$loan): ?>
<?php if (!$error): ?>
<div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>Loan not found. <a href="view_loans.php">Back to loans</a></div>
<?php endif; ?>
<?php else: ?>
<form method="POST" action="edit_loan.php?id=<?php echo $loan_id; ?>">
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5><i class="fas fa-file-invoice-dollar me-2"></i>Loan <?php echo htmlspecialchars($loan['loan_no'] ?: ('L-' . $loan_id)); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo __('borrower'); ?> <span class="text-danger">*</span></label>
                        <select name="borrower_id" class="form-select" required>
                            <option value="">-- <?php echo __('select_borrower'); ?> --</option>
                            <?php foreach ($borrowers as $b): ?>
                            <option value="<?php echo (int)$b['id']; ?>" <?php echo (int)$b['id'] === (int)($form['borrower_id'] ?? 0) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($b['fname'] . ' ' . $b['lname'] . ($b['mobile_no'] ? ' (' . $b['mobile_no'] . ')' : '')); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Loan Product <span class="text-danger">*</span></label>
                        <select name="loan_product_id" class="form-select" required>
                            <option value="">-- <?php echo __('select_product'); ?> --</option>
                            <?php foreach ($products as $p): ?>
                            <option value="<?php echo (int)$p['id']; ?>" <?php echo (int)$p['id'] === (int)($form['loan_product_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo __('loan_number'); ?></label>
                        <input type="text" name="loan_no" class="form-control" value="<?php echo htmlspecialchars($form['loan_no'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo __('status'); ?> <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <?php echo edit_loan_options($status_options, $form['status'] ?? ''); ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Principal Amount (TZS) <span class="text-danger">*</span></label>
                        <input type="number" name="principal_amount" class="form-control" step="0.01" min="0.01" required value="<?php echo htmlspecialchars((string)($form['principal_amount'] ?? '')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo __('release_date'); ?> <span class="text-danger">*</span></label>
                        <input type="date" name="release_date" class="form-control" required value="<?php echo htmlspecialchars((string)($form['release_date'] ?? '')); ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo __('interest_method'); ?></label>
                        <select name="interest_mtd" class="form-select">
                            <?php echo edit_loan_options($interest_methods, $form['interest_mtd'] ?? 'flat_rate'); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo __('interest_rate'); ?> (%)</label>
                        <input type="number" name="loan_interest" class="form-control" step="0.01" min="0" value="<?php echo htmlspecialchars((string)($form['loan_interest'] ?? '')); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Interest Period</label>
                        <select name="loan_interest_pd" class="form-select">
                            <?php echo edit_loan_options($interest_periods, $form['loan_interest_pd'] ?? 'Month'); ?>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Duration <span class="text-danger">*</span></label>
                        <input type="number" name="loan_duration" class="form-control" min="1" required value="<?php echo htmlspecialchars((string)($form['loan_duration'] ?? '')); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Duration Unit</label>
                        <select name="loan_duration_pd" class="form-select">
                            <?php echo edit_loan_options($duration_units, $form['loan_duration_pd'] ?? 'Months'); ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?php echo __('repayment_cycle'); ?></label>
                        <select name="repayment_cycle" class="form-select">
                            <?php echo edit_loan_options($repayment_cycles, $form['repayment_cycle'] ?? 'Monthly'); ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?php echo __('no_of_repayments'); ?></label>
                        <input type="number" name="no_repayment_cycle" class="form-control" min="1" value="<?php echo htmlspecialchars((string)($form['no_repayment_cycle'] ?? '')); ?>">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label"><?php echo __('description'); ?></label>
                    <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars((string)($form['description'] ?? '')); ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header"><h5><i class="fas fa-user-tie me-2"></i>Assigned Staff</h5></div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><?php echo __('collector'); ?></label>
                    <select name="collector_id" class="form-select">
                        <option value="0">-- None --</option>
                        <?php foreach ($collectors as $s): ?>
                        <option value="<?php echo (int)$s['id']; ?>" <?php echo (int)$s['id'] === (int)($form['collector_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars(trim($s['fname'] . ' ' . $s['lname'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loan Officer</label>
                    <select name="loan_officer_id" class="form-select">
                        <option value="0">-- None --</option>
                        <?php foreach ($officers as $s): ?>
                        <option value="<?php echo (int)$s['id']; ?>" <?php echo (int)$s['id'] === (int)($form['loan_officer_id'] ?? 0) ? 'selected' : ''; ?>><?php echo htmlspecialchars(trim($s['fname'] . ' ' . $s['lname'])); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle me-1"></i>The maturity date is recalculated from the release date and duration when you save.
                    Current: <strong><?php echo htmlspecialchars($loan['maturity_date'] ?? '-'); ?></strong>
                </p>
            </div>
        </div>
        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?></button>
            <a href="view_loan_details.php?id=<?php echo $loan_id; ?>" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i><?php echo __('cancel'); ?></a>
        </div>
    </div>
</div>
</form>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
