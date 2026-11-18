<?php
/**
 * POST handler for the form in admin/general_settings.php.
 * Updates the single system_settings row (inserting it if missing) and
 * optionally stores an uploaded company logo under uploads/.
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

function general_settings_redirect(array $params = []): void
{
    $qs = $params ? '?' . http_build_query($params) : '';
    header('Location: general_settings.php' . $qs);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    general_settings_redirect();
}

$company_name    = trim($_POST['company_name'] ?? '');
$company_email   = trim($_POST['company_email'] ?? '');
$company_phone   = trim($_POST['company_phone'] ?? '');
$company_address = trim($_POST['company_address'] ?? '');

$allowed_currencies   = ['TZS', 'KES', 'UGX', 'USD', 'EUR', 'GBP'];
$allowed_date_formats = ['d/m/Y', 'm/d/Y', 'Y-m-d', 'd M Y'];
$allowed_number_fmts  = ['1,234.56', '1.234,56', '1 234.56'];
$allowed_fiscal       = ['1', '4', '7', '10'];

$currency = $_POST['currency'] ?? 'TZS';
if (!in_array($currency, $allowed_currencies, true)) {
    $currency = 'TZS';
}
$date_format = $_POST['date_format'] ?? 'd/m/Y';
if (!in_array($date_format, $allowed_date_formats, true)) {
    $date_format = 'd/m/Y';
}
$timezone = $_POST['timezone'] ?? 'Africa/Dar_es_Salaam';
if (!in_array($timezone, timezone_identifiers_list(), true)) {
    $timezone = 'Africa/Dar_es_Salaam';
}
$number_format = $_POST['number_format'] ?? '1,234.56';
if (!in_array($number_format, $allowed_number_fmts, true)) {
    $number_format = '1,234.56';
}
$fiscal_year_start = (string)($_POST['fiscal_year_start'] ?? '1');
if (!in_array($fiscal_year_start, $allowed_fiscal, true)) {
    $fiscal_year_start = '1';
}

$borrower_number_format = trim($_POST['borrower_number_format'] ?? '');
if ($borrower_number_format === '') {
    $borrower_number_format = 'BR-{NUMBER}';
}
$loan_number_format = trim($_POST['loan_number_format'] ?? '');
if ($loan_number_format === '') {
    $loan_number_format = 'LN-{YEAR}-{NUMBER}';
}

if ($company_name === '') {
    general_settings_redirect(['error' => 'Company name is required.']);
}
if ($company_email !== '' && !filter_var($company_email, FILTER_VALIDATE_EMAIL)) {
    general_settings_redirect(['error' => 'Company email is not a valid email address.']);
}

// ---- Optional logo upload ----
$new_logo_rel  = null;
$new_logo_abs  = null;
if (isset($_FILES['logo']) && is_array($_FILES['logo']) && ($_FILES['logo']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['logo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $msg = in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
            ? 'Logo must be 2MB or smaller.'
            : 'Logo upload failed. Please try again.';
        general_settings_redirect(['error' => $msg]);
    }
    if (!is_uploaded_file($file['tmp_name'])) {
        general_settings_redirect(['error' => 'Invalid logo upload.']);
    }
    if ($file['size'] <= 0 || $file['size'] > 2 * 1024 * 1024) {
        general_settings_redirect(['error' => 'Logo must be 2MB or smaller.']);
    }

    $mime_map = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime  = $finfo->file($file['tmp_name']);
    if (!isset($mime_map[$mime])) {
        general_settings_redirect(['error' => 'Logo must be a PNG, JPG, GIF or WEBP image.']);
    }

    $uploads_dir = realpath(__DIR__ . '/../uploads');
    if ($uploads_dir === false || !is_dir($uploads_dir) || !is_writable($uploads_dir)) {
        general_settings_redirect(['error' => 'Uploads directory is not writable.']);
    }

    $filename     = 'logo_' . bin2hex(random_bytes(8)) . '.' . $mime_map[$mime];
    $new_logo_abs = $uploads_dir . DIRECTORY_SEPARATOR . $filename;
    if (!move_uploaded_file($file['tmp_name'], $new_logo_abs)) {
        general_settings_redirect(['error' => 'Could not save the uploaded logo.']);
    }
    $new_logo_rel = 'uploads/' . $filename;
}

try {
    $existing = $con->query("SELECT id, logo FROM system_settings ORDER BY id LIMIT 1")->fetch(PDO::FETCH_ASSOC);

    $params = [
        $company_name,
        $company_email !== '' ? $company_email : null,
        $company_phone !== '' ? $company_phone : null,
        $company_address !== '' ? $company_address : null,
        $currency,
        $date_format,
        $number_format,
        $timezone,
        $fiscal_year_start,
        $loan_number_format,
        $borrower_number_format,
    ];

    if ($existing) {
        $sql = "UPDATE system_settings SET company_name = ?, company_email = ?, company_phone = ?, company_address = ?,
                    currency = ?, date_format = ?, number_format = ?, timezone = ?, fiscal_year_start = ?,
                    loan_number_format = ?, borrower_number_format = ?, updated_at = NOW()";
        if ($new_logo_rel !== null) {
            $sql .= ", logo = ?";
            $params[] = $new_logo_rel;
        }
        $sql .= " WHERE id = ?";
        $params[] = (int)$existing['id'];
        $con->prepare($sql)->execute($params);

        // Remove the previously generated logo file (only files this handler created).
        $old = (string)($existing['logo'] ?? '');
        if ($new_logo_rel !== null && $old !== $new_logo_rel
            && preg_match('#^uploads/logo_[a-f0-9]{16}\.(png|jpg|gif|webp)$#', $old)) {
            $old_abs = realpath(__DIR__ . '/../' . $old);
            if ($old_abs !== false && is_file($old_abs)) {
                @unlink($old_abs);
            }
        }
    } else {
        $params[] = $new_logo_rel;
        $stmt = $con->prepare("INSERT INTO system_settings (company_name, company_email, company_phone, company_address,
                    currency, date_format, number_format, timezone, fiscal_year_start,
                    loan_number_format, borrower_number_format, logo, updated_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute($params);
    }
} catch (PDOException $e) {
    if ($new_logo_abs !== null && is_file($new_logo_abs)) {
        @unlink($new_logo_abs);
    }
    error_log('save_general_settings: ' . $e->getMessage());
    general_settings_redirect(['error' => 'Could not save settings: database error.']);
}

general_settings_redirect(['success' => 1]);
