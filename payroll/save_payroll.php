<?php
/**
 * POST handler for the form in add_payroll.php.
 *
 * The form posts itemised earnings/deductions; the payroll table stores
 * aggregates, so they are mapped as:
 *   allowances      = housing_allowance + transport_allowance + other_allowances
 *   social_security = nssf
 *   deductions      = loan_deduction + other_deductions
 *   overtime, bonus = optional fields (not on the current form, default 0)
 * net_pay is always computed here, never taken from the request.
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_payroll.php');
    exit;
}

function payroll_back_with_error(string $message): void
{
    header('Location: add_payroll.php?error=' . urlencode($message));
    exit;
}

/** Returns the posted value as a float, or null if it is not a number >= 0. */
function payroll_amount(string $field): ?float
{
    $raw = trim((string)($_POST[$field] ?? ''));
    if ($raw === '') {
        return 0.0;
    }
    if (!is_numeric($raw) || (float)$raw < 0) {
        return null;
    }
    return (float)$raw;
}

$staff_id = (int)($_POST['staff_id'] ?? 0);
$pay_period = trim((string)($_POST['pay_period'] ?? ''));
$payment_method = (string)($_POST['payment_method'] ?? 'bank_transfer');
$notes = trim((string)($_POST['notes'] ?? ''));
$pay_date = trim((string)($_POST['pay_date'] ?? ''));

if ($staff_id <= 0) {
    payroll_back_with_error('Please select a staff member.');
}
if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $pay_period)) {
    payroll_back_with_error('Please enter a valid pay period (YYYY-MM).');
}
if (!in_array($payment_method, ['bank_transfer', 'cash', 'mobile_money'], true)) {
    payroll_back_with_error('Invalid payment method.');
}
if ($pay_date === '') {
    $pay_date = date('Y-m-d');
} else {
    $d = DateTime::createFromFormat('Y-m-d', $pay_date);
    if (!$d || $d->format('Y-m-d') !== $pay_date) {
        payroll_back_with_error('Invalid pay date.');
    }
}

$fields = [
    'basic_salary', 'housing_allowance', 'transport_allowance', 'other_allowances',
    'overtime', 'bonus', 'tax', 'nssf', 'loan_deduction', 'other_deductions',
];
$v = [];
foreach ($fields as $f) {
    $v[$f] = payroll_amount($f);
    if ($v[$f] === null) {
        payroll_back_with_error('Amounts must be numbers greater than or equal to zero (' . str_replace('_', ' ', $f) . ').');
    }
}
if ($v['basic_salary'] <= 0) {
    payroll_back_with_error('Basic salary must be greater than zero.');
}

$allowances = $v['housing_allowance'] + $v['transport_allowance'] + $v['other_allowances'];
$social_security = $v['nssf'];
$deductions = $v['loan_deduction'] + $v['other_deductions'];
$net_pay = round(
    $v['basic_salary'] + $allowances + $v['overtime'] + $v['bonus']
    - $v['tax'] - $social_security - $deductions,
    2
);

if ($net_pay < 0) {
    payroll_back_with_error('Total deductions exceed gross earnings.');
}

try {
    $stmt = $con->prepare("SELECT id FROM staff WHERE id = ?");
    $stmt->execute([$staff_id]);
    if (!$stmt->fetchColumn()) {
        payroll_back_with_error('Selected staff member does not exist.');
    }

    $stmt = $con->prepare("
        INSERT INTO payroll
            (staff_id, pay_period, pay_date, basic_salary, allowances, overtime, bonus,
             tax, social_security, deductions, net_pay, payment_method, notes, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([
        $staff_id, $pay_period, $pay_date, $v['basic_salary'], $allowances, $v['overtime'], $v['bonus'],
        $v['tax'], $social_security, $deductions, $net_pay, $payment_method, $notes,
    ]);
} catch (PDOException $e) {
    payroll_back_with_error('Could not save payroll: ' . $e->getMessage());
}

// view_payroll.php does not display a success flag, so redirect plainly.
header('Location: view_payroll.php');
exit;
