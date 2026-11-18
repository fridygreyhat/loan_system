<?php
/**
 * POST handler for the form in admin/email_settings.php.
 * Updates the single email_settings row (inserting it if missing).
 * A blank SMTP password keeps the stored one.
 *
 * Also stores the encryption choice and the automatic-email checkboxes.
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

function email_settings_redirect(array $params = []): void
{
    $qs = $params ? '?' . http_build_query($params) : '';
    header('Location: email_settings.php' . $qs);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    email_settings_redirect();
}

$smtp_host     = trim($_POST['smtp_host'] ?? '');
$smtp_port_raw = trim((string)($_POST['smtp_port'] ?? '587'));
$smtp_user     = trim($_POST['smtp_username'] ?? '');
$smtp_password = (string)($_POST['smtp_password'] ?? '');
$from_email    = trim($_POST['from_email'] ?? '');
$from_name     = trim($_POST['from_name'] ?? '');
$encryption    = in_array($_POST['encryption'] ?? 'tls', ['tls', 'ssl', 'none'], true) ? $_POST['encryption'] : 'tls';

// Checkbox => column. Bound as 'true'/'false' strings: PDO sends PHP false as ''.
$triggers = [];
foreach (['email_loan_approved', 'email_payment_received', 'email_payment_reminder',
          'email_overdue_notice', 'email_loan_statement', 'email_birthday'] as $col) {
    $triggers[$col] = isset($_POST[$col]) ? 'true' : 'false';
}

if ($smtp_host === '') {
    email_settings_redirect(['error' => 'SMTP host is required.']);
}
$smtp_port = filter_var($smtp_port_raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
if ($smtp_port === false) {
    email_settings_redirect(['error' => 'SMTP port must be a number between 1 and 65535.']);
}
if ($from_email !== '' && !filter_var($from_email, FILTER_VALIDATE_EMAIL)) {
    email_settings_redirect(['error' => 'From email is not a valid email address.']);
}

try {
    $con->beginTransaction();
    $id = $con->query("SELECT id FROM email_settings ORDER BY id LIMIT 1")->fetchColumn();
    if (!$id) {
        $id = $con->query("INSERT INTO email_settings DEFAULT VALUES RETURNING id")->fetchColumn();
    }

    $sets = ['smtp_host = ?', 'smtp_port = ?', 'smtp_user = ?', 'from_email = ?', 'from_name = ?', 'encryption = ?'];
    $params = [$smtp_host, $smtp_port, $smtp_user, $from_email, $from_name, $encryption];
    foreach ($triggers as $col => $value) {
        $sets[] = "$col = ?";
        $params[] = $value;
    }
    if ($smtp_password !== '') {        // blank keeps the stored password
        $sets[] = 'smtp_password = ?';
        $params[] = $smtp_password;
    }
    $params[] = (int)$id;
    $con->prepare('UPDATE email_settings SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
    $con->commit();
} catch (PDOException $e) {
    if ($con->inTransaction()) {
        $con->rollBack();
    }
    error_log('save_email_settings: ' . $e->getMessage());
    email_settings_redirect(['error' => 'Could not save email settings: database error.']);
}

email_settings_redirect(['success' => 1]);
