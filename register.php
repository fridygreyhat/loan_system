<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Loan_system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/loandisk-modern.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card" style="max-width: 500px;">
        <div class="logo">
            <h2><i class="fas fa-coins me-2"></i>Loan_system</h2>
            <p>Create your account</p>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>
        
        <form action="auth/register_process.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" placeholder="First name" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Last name" required>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Create a password" required minlength="6">
                </div>
            </div>
            
            <div class="form-group mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password" required minlength="6">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus me-2"></i>Create Account
            </button>
        </form>
        
        <hr class="my-4">
        
        <p class="text-center text-muted mb-0">
            Already have an account? <a href="login.php">Sign in</a>
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
