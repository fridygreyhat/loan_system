<?php
/**
 * Router for PHP's built-in web server:
 *
 *     php -S 0.0.0.0:5000 router.php
 *
 * Blocks paths that must never be served or executed directly, then lets the
 * built-in server handle everything else as usual (return false).
 */

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/');

$blocked = '#^/(?:'
    . 'uploads/.*\.(?:php\d?|phtml|phar|phps)$'    // uploaded files are never executed
    . '|\.git(?:/|$)|\.claude(?:/|$)|\.replit$'
    . '|SQLDB/|helpers/|includes/|phplibs/|nbproject/'
    . '|replit\.md$|router\.php$'
    . ')#i';

if (preg_match($blocked, $path) || str_contains($path, '..')) {
    http_response_code(404);
    echo 'Not Found';
    return true;
}

return false;
