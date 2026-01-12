<?php
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        header("Location: ../register.php?error=All fields are required");
        exit;
    }
    
    if ($password !== $confirm_password) {
        header("Location: ../register.php?error=Passwords do not match");
        exit;
    }
    
    if (strlen($password) < 6) {
        header("Location: ../register.php?error=Password must be at least 6 characters");
        exit;
    }
    
    try {
        $stmt = $con->prepare("SELECT id FROM staff WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            header("Location: ../register.php?error=Email already registered");
            exit;
        }
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $con->prepare("INSERT INTO staff (fname, lname, email, password, role_id, status) VALUES (?, ?, ?, ?, 7, 'active')");
        $stmt->execute([$first_name, $last_name, $email, $hashed_password]);
        
        header("Location: ../login.php?registered=1");
        exit;
    } catch (PDOException $e) {
        header("Location: ../register.php?error=Registration failed. Please try again.");
        exit;
    }
} else {
    header("Location: ../register.php");
    exit;
}
