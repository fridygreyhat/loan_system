<?php
/**
 * Create or reset the super administrator account.
 *
 * Usage (command line only):
 *   php SQLDB/create_super_admin.php <email> [first_name] [last_name]
 *
 * The password is read from the SUPER_ADMIN_PASSWORD environment variable,
 * or prompted for interactively, so it never ends up in shell history or
 * in this repository.
 *
 * Uses the same database settings as the app (DATABASE_URL or the local default).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$email = $argv[1] ?? '';
$fname = $argv[2] ?? 'Super';
$lname = $argv[3] ?? 'Admin';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Usage: php SQLDB/create_super_admin.php <email> [first_name] [last_name]\n");
    exit(1);
}

$adminPassword = getenv('SUPER_ADMIN_PASSWORD');
if ($adminPassword === false || $adminPassword === '') {
    fwrite(STDOUT, "Password for $email: ");
    system('stty -echo 2>/dev/null');
    $adminPassword = rtrim((string) fgets(STDIN), "\r\n");
    system('stty echo 2>/dev/null');
    fwrite(STDOUT, "\n");
}

if (strlen($adminPassword) < 8) {
    fwrite(STDERR, "Password must be at least 8 characters.\n");
    exit(1);
}

// Hash before loading connect.php: it defines its own $password (the DB password).
$hash = password_hash($adminPassword, PASSWORD_DEFAULT);
unset($adminPassword);

require __DIR__ . '/../helpers/connect.php';

$roleId = $con->query("SELECT id FROM staff_role_types WHERE code = 'admin'")->fetchColumn() ?: 1;
$branchId = $con->query("SELECT id FROM branches ORDER BY id LIMIT 1")->fetchColumn() ?: 1;

$stmt = $con->prepare("SELECT id FROM staff WHERE LOWER(email) = LOWER(?)");
$stmt->execute([$email]);
$existingId = $stmt->fetchColumn();

if ($existingId) {
    $stmt = $con->prepare("UPDATE staff SET password = ?, role_id = ?, status = 'active', is_super_admin = TRUE WHERE id = ?");
    $stmt->execute([$hash, $roleId, $existingId]);
    echo "Updated existing account #$existingId ($email) as super admin.\n";
} else {
    $stmt = $con->prepare("INSERT INTO staff (branch_id, role_id, fname, lname, email, password, status, is_super_admin)
                           VALUES (?, ?, ?, ?, ?, ?, 'active', TRUE) RETURNING id");
    $stmt->execute([$branchId, $roleId, $fname, $lname, $email, $hash]);
    echo "Created super admin #" . $stmt->fetchColumn() . " ($email).\n";
}
