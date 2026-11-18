<?php
/**
 * Login guard.
 *
 * Required from the shared entry points (connect.php, lang.php, DbAcess.php,
 * main_menu.php) so every page that reads data or renders the layout is
 * protected. Public pages are allow-listed below.
 */

if (PHP_SAPI === 'cli') {
    return;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('app_base_url')) {
    /** URL path of the application root, e.g. "/" or "/loan_system/". */
    function app_base_url(): string
    {
        $appRoot = realpath(__DIR__ . '/..');
        $docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: $appRoot;
        $rel = str_replace('\\', '/', substr($appRoot, strlen($docRoot)));
        return rtrim($rel, '/') . '/';
    }
}

if (!function_exists('is_logged_in')) {
    function is_logged_in(): bool
    {
        return !empty($_SESSION['user_id']);
    }
}

/*
 * CSRF protection for every POST in the app.
 *  - One random token per session.
 *  - An output filter adds it as a hidden field to every <form method="post">
 *    and as <meta name="csrf-token"> for JavaScript (send it as X-CSRF-Token).
 *  - Any POST without a matching token is rejected.
 */
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!function_exists('csrf_inject')) {
    function csrf_inject(string $html): string
    {
        foreach (headers_list() as $h) {
            if (stripos($h, 'content-type:') === 0 && stripos($h, 'text/html') === false) {
                return $html;   // downloads, JSON, images: leave untouched
            }
        }
        $token = htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES);
        $field = '<input type="hidden" name="_csrf" value="' . $token . '">';
        $html = preg_replace('/<form\b[^>]*\bmethod\s*=\s*["\']?post\b[^>]*>/i', '$0' . $field, $html);
        return preg_replace('/<\/head>/i', '<meta name="csrf-token" content="' . $token . '"></head>', $html, 1);
    }
    ob_start('csrf_inject');
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $sent = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
    if (!is_string($sent) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(419);
        exit('Your session has expired or the form is invalid. Please go back, reload the page and try again.');
    }
}

$publicScripts = [
    'index.php',
    'login.php',
    'register.php',
    'forgot_password.php',
    'auth/login_process.php',
    'auth/register_process.php',
    'auth/forgot_password_process.php',
];

$appRoot = realpath(__DIR__ . '/..');
$script = realpath($_SERVER['SCRIPT_FILENAME'] ?? '') ?: '';
$relScript = ltrim(str_replace('\\', '/', substr($script, strlen($appRoot))), '/');

if (!is_logged_in() && !in_array($relScript, $publicScripts, true)) {
    $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    if ($isAjax || ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
        http_response_code(401);
        exit('Authentication required');
    }
    header('Location: ' . app_base_url() . 'login.php');
    exit;
}
