<?php
/**
 * Streams an agreement template file (agreement_templates.file_path) as a download.
 * Linked from admin/templates.php as download_template.php?id=N.
 *
 * Only files that resolve (via realpath) to a location inside the app's
 * uploads/ directory are served.
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

function template_download_fail(int $code, string $message): void
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    echo $message;
    exit;
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    template_download_fail(404, 'Template not found.');
}

try {
    $stmt = $con->prepare("SELECT name, file_path FROM agreement_templates WHERE id = ?");
    $stmt->execute([$id]);
    $template = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('download_template: ' . $e->getMessage());
    template_download_fail(500, 'Could not load template: database error.');
}

if (!$template || trim((string)$template['file_path']) === '') {
    template_download_fail(404, 'Template not found.');
}

$app_root    = realpath(__DIR__ . '/..');
$uploads_dir = realpath($app_root . '/uploads');
if ($app_root === false || $uploads_dir === false) {
    template_download_fail(404, 'Template file not found.');
}

$stored = str_replace('\\', '/', trim((string)$template['file_path']));
if (strpos($stored, "\0") !== false) {
    template_download_fail(403, 'Access denied.');
}

// upload_template.php stores paths relative to admin/ (e.g. "../uploads/templates/x.docx");
// also accept paths relative to the app root (e.g. "uploads/templates/x.docx").
$candidates = [];
if ($stored[0] === '/') {
    $candidates[] = $stored;
} else {
    $candidates[] = __DIR__ . '/' . $stored;
    $candidates[] = $app_root . '/' . $stored;
}

$resolved     = false;
$found_any    = false;
foreach ($candidates as $candidate) {
    $real = realpath($candidate);
    if ($real === false || !is_file($real)) {
        continue;
    }
    $found_any = true;
    if (strpos($real, $uploads_dir . DIRECTORY_SEPARATOR) === 0) {
        $resolved = $real;
        break;
    }
}

if ($resolved === false) {
    if ($found_any) {
        template_download_fail(403, 'Access denied.');
    }
    template_download_fail(404, 'Template file not found.');
}

if (!is_readable($resolved)) {
    template_download_fail(404, 'Template file not found.');
}

$ext = strtolower(pathinfo($resolved, PATHINFO_EXTENSION));
$types = [
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'doc'  => 'application/msword',
    'pdf'  => 'application/pdf',
    'odt'  => 'application/vnd.oasis.opendocument.text',
    'rtf'  => 'application/rtf',
    'txt'  => 'text/plain',
];
$content_type = $types[$ext] ?? 'application/octet-stream';

// Build a safe download filename from the template name.
$base_name = preg_replace('/[^A-Za-z0-9._-]+/', '_', (string)$template['name']);
$base_name = trim($base_name, '._-');
if ($base_name === '') {
    $base_name = 'template_' . $id;
}
$download_name = $base_name . ($ext !== '' ? '.' . $ext : '');

while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $download_name . '"; filename*=UTF-8\'\'' . rawurlencode($download_name));
header('Content-Length: ' . filesize($resolved));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');

readfile($resolved);
exit;
