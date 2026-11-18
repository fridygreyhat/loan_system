<?php
/**
 * POST handler for the "Add Branch" modal in admin/branches.php.
 * Inserts a branch, or updates one when a positive "id" is posted.
 *
 * Also stores capital, min/max loan amount, min/max interest rate and the
 * active flag. "location" is stored in branches.address.
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

function branches_redirect(array $params = []): void
{
    $qs = $params ? '?' . http_build_query($params) : '';
    header('Location: branches.php' . $qs);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    branches_redirect();
}

$id       = (int)($_POST['id'] ?? 0);
$name     = trim($_POST['name'] ?? '');
$code     = trim($_POST['code'] ?? '');
$location = trim($_POST['location'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$is_active = ($_POST['status'] ?? 'active') === 'inactive' ? 'false' : 'true';   // PDO: no PHP bools
$numbers = [];
foreach (['capital', 'min_loan_amount', 'max_loan_amount', 'min_interest_rate', 'max_interest_rate'] as $f) {
    $v = trim((string)($_POST[$f] ?? '0'));
    if ($v === '') {
        $v = '0';
    }
    if (!is_numeric($v) || $v < 0) {
        branches_redirect(['error' => ucfirst(str_replace('_', ' ', $f)) . ' must be a number of 0 or more.']);
    }
    $numbers[$f] = (float)$v;
}
if ($numbers['max_loan_amount'] && $numbers['min_loan_amount'] > $numbers['max_loan_amount']) {
    branches_redirect(['error' => 'Minimum loan amount cannot be greater than the maximum.']);
}
if ($numbers['max_interest_rate'] && $numbers['min_interest_rate'] > $numbers['max_interest_rate']) {
    branches_redirect(['error' => 'Minimum interest rate cannot be greater than the maximum.']);
}

if ($name === '') {
    branches_redirect(['error' => 'Branch name is required.']);
}
if (mb_strlen($name) > 100) {
    branches_redirect(['error' => 'Branch name is too long (max 100 characters).']);
}
if (mb_strlen($code) > 20) {
    branches_redirect(['error' => 'Branch code is too long (max 20 characters).']);
}

try {
    if ($code !== '') {
        $stmt = $con->prepare("SELECT id FROM branches WHERE LOWER(code) = LOWER(?) AND id <> ?");
        $stmt->execute([$code, $id]);
        if ($stmt->fetchColumn()) {
            branches_redirect(['error' => 'Another branch already uses that branch code.']);
        }
    }

    if ($id > 0) {
        $stmt = $con->prepare("UPDATE branches SET name = ?, code = ?, address = ?, phone = ?, is_active = ?,
                                   capital = ?, min_loan_amount = ?, max_loan_amount = ?, min_interest_rate = ?, max_interest_rate = ?
                               WHERE id = ?");
        $stmt->execute(array_merge([$name, $code !== '' ? $code : null, $location !== '' ? $location : null, $phone !== '' ? $phone : null, $is_active],
                                   array_values($numbers), [$id]));
        if ($stmt->rowCount() === 0) {
            branches_redirect(['error' => 'Branch not found.']);
        }
    } else {
        $stmt = $con->prepare("INSERT INTO branches (name, code, address, phone, is_active, capital, min_loan_amount, max_loan_amount, min_interest_rate, max_interest_rate)
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(array_merge([$name, $code !== '' ? $code : null, $location !== '' ? $location : null, $phone !== '' ? $phone : null, $is_active],
                                   array_values($numbers)));
    }
} catch (PDOException $e) {
    error_log('save_branch: ' . $e->getMessage());
    branches_redirect(['error' => 'Could not save branch: database error.']);
}

branches_redirect(['success' => 1]);
