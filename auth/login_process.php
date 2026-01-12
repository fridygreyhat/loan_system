<?php
session_start();
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        header("Location: ../login.php?error=1");
        exit;
    }
    
    try {
        $stmt = $con->prepare("SELECT id, fname, lname, email, password, role_id, status FROM staff WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] !== 'active') {
                header("Location: ../login.php?error=inactive");
                exit;
            }
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fname'] . ' ' . $user['lname'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role_id'] = $user['role_id'];
            
            if ($remember) {
                setcookie('remember_email', $email, time() + (86400 * 30), "/");
            }
            
            header("Location: ../home/dashboard.php");
            exit;
        } else {
            header("Location: ../login.php?error=1");
            exit;
        }
    } catch (PDOException $e) {
        header("Location: ../login.php?error=1");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
