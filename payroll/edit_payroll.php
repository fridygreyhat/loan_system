<?php
$base_path = '../';
$page_title = 'Edit Payroll';
$current_page = 'payroll';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$id = (int)($_GET['id'] ?? 0);
$error = '';
$payroll = null;
$staff_list = [];

$payment_methods = ['bank_transfer' => __('bank_transfer'), 'cash' => __('cash'), 'mobile_money' => __('mobile_money')];
// Earnings/deductions stored on the payroll row; net_pay is derived from these.
$amount_fields = ['basic_salary', 'allowances', 'overtime', 'bonus', 'tax', 'social_security', 'deductions'];

try {
    $stmt = $con->prepare("SELECT * FROM payroll WHERE id = ?");
    $stmt->execute([$id]);
    $payroll = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
} catch (PDOException $e) {
    $error = 'Could not load payroll record: ' . $e->getMessage();
}

if ($payroll && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = (int)($_POST['staff_id'] ?? 0);
    $pay_period = trim((string)($_POST['pay_period'] ?? ''));
    $pay_date = trim((string)($_POST['pay_date'] ?? ''));
    $payment_method = (string)($_POST['payment_method'] ?? '');
    $notes = trim((string)($_POST['notes'] ?? ''));
    $status = (string)($_POST['status'] ?? '');

    $posted = compact('staff_id', 'pay_period', 'pay_date', 'payment_method', 'notes', 'status');
    $amounts = [];
    $bad_amount = null;
    foreach ($amount_fields as $f) {
        $raw = trim((string)($_POST[$f] ?? ''));
        $posted[$f] = $raw;
        if ($raw === '') {
            $amounts[$f] = 0.0;
        } elseif (!is_numeric($raw) || (float)$raw < 0) {
            $bad_amount = $bad_amount ?? $f;
        } else {
            $amounts[$f] = (float)$raw;
        }
    }
    $payroll = array_merge($payroll, $posted);

    $date_obj = DateTime::createFromFormat('Y-m-d', $pay_date);

    if ($staff_id <= 0) {
        $error = 'Please select a staff member.';
    } elseif (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $pay_period)) {
        $error = 'Please enter a valid pay period (YYYY-MM).';
    } elseif (!$date_obj || $date_obj->format('Y-m-d') !== $pay_date) {
        $error = 'Please enter a valid pay date.';
    } elseif (!array_key_exists($payment_method, $payment_methods)) {
        $error = 'Please select a valid payment method.';
    } elseif (!in_array($status, ['pending', 'paid'], true)) {
        $error = 'Please select a valid status.';
    } elseif ($bad_amount !== null) {
        $error = 'Amounts must be numbers greater than or equal to zero (' . str_replace('_', ' ', $bad_amount) . ').';
    } elseif ($amounts['basic_salary'] <= 0) {
        $error = 'Basic salary must be greater than zero.';
    } else {
        $net_pay = round(
            $amounts['basic_salary'] + $amounts['allowances'] + $amounts['overtime'] + $amounts['bonus']
            - $amounts['tax'] - $amounts['social_security'] - $amounts['deductions'],
            2
        );
        if ($net_pay < 0) {
            $error = 'Total deductions exceed gross earnings.';
        } else {
            try {
                $stmt = $con->prepare("SELECT id FROM staff WHERE id = ?");
                $stmt->execute([$staff_id]);
                if (!$stmt->fetchColumn()) {
                    $error = 'Selected staff member does not exist.';
                } else {
                    $stmt = $con->prepare("
                        UPDATE payroll SET
                            staff_id = ?, pay_period = ?, pay_date = ?,
                            basic_salary = ?, allowances = ?, overtime = ?, bonus = ?,
                            tax = ?, social_security = ?, deductions = ?, net_pay = ?,
                            payment_method = ?, notes = ?, status = ?
                        WHERE id = ?
                    ");
                    $stmt->execute([
                        $staff_id, $pay_period, $pay_date,
                        $amounts['basic_salary'], $amounts['allowances'], $amounts['overtime'], $amounts['bonus'],
                        $amounts['tax'], $amounts['social_security'], $amounts['deductions'], $net_pay,
                        $payment_method, $notes, $status, $id,
                    ]);
                    header('Location: view_payroll.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = 'Could not update payroll record: ' . $e->getMessage();
            }
        }
    }
}

if ($payroll) {
    try {
        $stmt = $con->prepare("SELECT id, TRIM(CONCAT(fname, ' ', COALESCE(lname, ''))) AS full_name FROM staff WHERE status = 'active' OR id = ? ORDER BY fname, lname");
        $stmt->execute([(int)$payroll['staff_id']]);
        $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Could not load staff list: ' . $e->getMessage();
    }

    // Legacy rows may store the method as a label (e.g. the column default "Bank Transfer").
    $current_method = strtolower(str_replace(' ', '_', (string)($payroll['payment_method'] ?? '')));
}

include $base_path . 'includes/header.php';
?>

<div class="content-header">
    <h1><i class="fas fa-edit"></i> Edit Payroll</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="view_payroll.php"><?php echo __('payroll'); ?></a></li>
            <li class="breadcrumb-item active">Edit Payroll</li>
        </ol>
    </nav>
</div>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!$payroll): ?>
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>Payroll record not found.
    <a href="view_payroll.php" class="alert-link">Back to payroll</a>
</div>
<?php else: ?>
<form method="POST" id="payrollForm">
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('payroll_details'); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo __('staff_member'); ?> *</label>
                        <select name="staff_id" class="form-select" required>
                            <option value=""><?php echo __('select_staff'); ?></option>
                            <?php foreach ($staff_list as $staff): ?>
                            <option value="<?php echo (int)$staff['id']; ?>" <?php echo (int)$staff['id'] === (int)$payroll['staff_id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($staff['full_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label"><?php echo __('pay_period'); ?> *</label>
                        <input type="month" name="pay_period" class="form-control" value="<?php echo htmlspecialchars((string)$payroll['pay_period']); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Pay Date *</label>
                        <input type="date" name="pay_date" class="form-control" value="<?php echo htmlspecialchars((string)$payroll['pay_date']); ?>" required>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="mb-3"><?php echo __('earnings'); ?></h6>
                <div class="row">
                    <?php foreach (['basic_salary' => __('basic_salary') . ' *', 'allowances' => __('allowances'), 'overtime' => 'Overtime', 'bonus' => 'Bonus'] as $field => $label): ?>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><?php echo htmlspecialchars($label); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">TZS</span>
                            <input type="number" name="<?php echo $field; ?>" class="form-control js-earning" step="0.01" min="0" value="<?php echo htmlspecialchars((string)$payroll[$field]); ?>" <?php echo $field === 'basic_salary' ? 'required' : ''; ?>>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4">
                <h6 class="mb-3"><?php echo __('deductions'); ?></h6>
                <div class="row">
                    <?php foreach (['tax' => __('tax') . ' (PAYE)', 'social_security' => __('nssf'), 'deductions' => 'Loan & Other Deductions'] as $field => $label): ?>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo htmlspecialchars($label); ?></label>
                        <div class="input-group">
                            <span class="input-group-text">TZS</span>
                            <input type="number" name="<?php echo $field; ?>" class="form-control js-deduction" step="0.01" min="0" value="<?php echo htmlspecialchars((string)$payroll[$field]); ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <hr class="my-4">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo __('payment_method'); ?></label>
                        <select name="payment_method" class="form-select">
                            <?php foreach ($payment_methods as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $current_method === $value ? 'selected' : ''; ?>><?php echo htmlspecialchars($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo __('status'); ?></label>
                        <select name="status" class="form-select">
                            <option value="pending" <?php echo $payroll['status'] === 'pending' ? 'selected' : ''; ?>><?php echo __('pending'); ?></option>
                            <option value="paid" <?php echo $payroll['status'] === 'paid' ? 'selected' : ''; ?>><?php echo __('paid'); ?></option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="1"><?php echo htmlspecialchars((string)($payroll['notes'] ?? '')); ?></textarea>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?php echo __('save_changes'); ?>
                    </button>
                    <a href="view_payslip.php?id=<?php echo (int)$payroll['id']; ?>" class="btn btn-outline-primary">
                        <i class="fas fa-file-invoice"></i> <?php echo __('view_payslip'); ?>
                    </a>
                    <a href="view_payroll.php" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> <?php echo __('cancel'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo __('payroll_summary'); ?></h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo __('gross_earnings'); ?>:</span>
                    <strong id="grossEarnings">TZS 0.00</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span><?php echo __('total_deductions'); ?>:</span>
                    <strong id="totalDeductions" class="text-danger">TZS 0.00</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="h5"><?php echo __('net_pay'); ?>:</span>
                    <strong id="netPay" class="h5 text-success">TZS 0.00</strong>
                </div>
                <small class="text-muted">Net pay is recalculated on save.</small>
            </div>
        </div>
    </div>
</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('payrollForm');
    const fmt = n => 'TZS ' + n.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    const sum = sel => Array.from(form.querySelectorAll(sel)).reduce((t, el) => t + (parseFloat(el.value) || 0), 0);
    function recalc() {
        const gross = sum('.js-earning');
        const ded = sum('.js-deduction');
        document.getElementById('grossEarnings').textContent = fmt(gross);
        document.getElementById('totalDeductions').textContent = fmt(ded);
        document.getElementById('netPay').textContent = fmt(gross - ded);
    }
    form.querySelectorAll('input[type="number"]').forEach(el => el.addEventListener('input', recalc));
    recalc();
});
</script>
<?php endif; ?>

<?php include $base_path . 'includes/footer.php'; ?>
