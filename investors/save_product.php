<?php
/**
 * POST handler for the "Add Investor Product" modal in products.php.
 *
 * Fields: name, interest_rate, interest_frequency, min_balance, description.
 * Also accepts (for when products.php wires up its edit/delete buttons):
 *   id      -> update that product instead of inserting
 *   status  -> active | inactive
 *   action=delete with id -> delete that product
 */
$base_path = '../';

require_once $base_path . 'helpers/connect.php';

function product_redirect(?string $error = null): void
{
    header('Location: products.php' . ($error !== null ? '?error=' . urlencode($error) : '?success=1'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    product_redirect();
}

$id = (int)($_POST['id'] ?? 0);

if (($_POST['action'] ?? '') === 'delete') {
    if ($id <= 0) {
        product_redirect('Missing product id.');
    }
    try {
        $stmt = $con->prepare("DELETE FROM investor_products WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        product_redirect('Could not delete product: ' . $e->getMessage());
    }
    product_redirect();
}

$name = trim((string)($_POST['name'] ?? ''));
$interest_rate = trim((string)($_POST['interest_rate'] ?? ''));
$interest_frequency = (string)($_POST['interest_frequency'] ?? 'monthly');
$min_balance = trim((string)($_POST['min_balance'] ?? ''));
$description = trim((string)($_POST['description'] ?? ''));
$status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

if ($min_balance === '') {
    $min_balance = '0';
}

if ($name === '') {
    product_redirect('Product name is required.');
}
if (!is_numeric($interest_rate) || (float)$interest_rate < 0) {
    product_redirect('Interest rate must be a number greater than or equal to zero.');
}
if (!is_numeric($min_balance) || (float)$min_balance < 0) {
    product_redirect('Minimum balance must be a number greater than or equal to zero.');
}
if (!in_array($interest_frequency, ['monthly', 'quarterly', 'semi_annually', 'annually'], true)) {
    product_redirect('Invalid interest frequency.');
}

try {
    if ($id > 0) {
        $stmt = $con->prepare("
            UPDATE investor_products
            SET name = ?, interest_rate = ?, interest_frequency = ?, min_balance = ?, description = ?, status = ?
            WHERE id = ?
        ");
        $stmt->execute([$name, (float)$interest_rate, $interest_frequency, (float)$min_balance, $description, $status, $id]);
        if ($stmt->rowCount() === 0) {
            product_redirect('Product not found.');
        }
    } else {
        $stmt = $con->prepare("
            INSERT INTO investor_products (name, interest_rate, interest_frequency, min_balance, description, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, (float)$interest_rate, $interest_frequency, (float)$min_balance, $description, $status]);
    }
} catch (PDOException $e) {
    product_redirect('Could not save product: ' . $e->getMessage());
}

product_redirect();
