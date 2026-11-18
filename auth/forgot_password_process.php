<?php
require_once '../helpers/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if (empty($email)) {
        header("Location: ../forgot_password.php?error=1");
        exit;
    }
    
    try {
        $stmt = $con->prepare("SELECT id FROM staff WHERE LOWER(email) = LOWER(?)");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $con->prepare("UPDATE staff SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
        }
        
        header("Location: ../forgot_password.php?success=1");
        exit;
    } catch (PDOException $e) {
        header("Location: ../forgot_password.php?success=1");
        exit;
    }
} else {
    header("Location: ../forgot_password.php");
    exit;
}
