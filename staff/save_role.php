<?php
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($name) || empty($code)) {
        header("Location: roles.php?error=Name and code are required");
        exit;
    }
    
    $code = strtolower(preg_replace('/[^a-z0-9_]/', '_', $code));
    
    try {
        $stmt = $con->prepare("INSERT INTO staff_role_types (name, code, description, is_system) VALUES (?, ?, ?, FALSE)");
        $stmt->execute([$name, $code, $description]);
        
        header("Location: roles.php?success=1");
        exit;
    } catch (PDOException $e) {
        header("Location: roles.php?error=" . urlencode($e->getMessage()));
        exit;
    }
} else {
    header("Location: roles.php");
    exit;
}
