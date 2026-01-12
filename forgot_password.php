<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Loan_system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/loandisk-modern.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="logo">
            <h2><i class="fas fa-coins me-2"></i>Loan_system</h2>
            <p>Reset your password</p>
        </div>
        
        <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>Password reset link sent to your email.
        </div>
        <?php endif; ?>
        
        <form action="auth/forgot_password_process.php" method="POST">
            <div class="form-group mb-4">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <small class="text-muted">We'll send you a link to reset your password.</small>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
            </button>
        </form>
        
        <hr class="my-4">
        
        <p class="text-center text-muted mb-0">
            Remember your password? <a href="login.php">Sign in</a>
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
