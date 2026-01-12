<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'helpers/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo get_current_lang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo __('sign_in'); ?> - Loan_system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="css/loandisk-modern.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="login-card">
        <div class="logo">
            <h2><i class="fas fa-coins me-2"></i>Loan_system</h2>
            <p><?php echo __('loan_management_system'); ?></p>
        </div>
        
        <div class="d-flex justify-content-end mb-3">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-globe me-1"></i> <?php echo __('language'); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item <?php echo get_current_lang() === 'en' ? 'active' : ''; ?>" href="?lang=en">English</a></li>
                    <li><a class="dropdown-item <?php echo get_current_lang() === 'sw' ? 'active' : ''; ?>" href="?lang=sw">Kiswahili</a></li>
                </ul>
            </div>
        </div>
        
        <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo __('invalid_credentials'); ?>
        </div>
        <?php endif; ?>
        
        <form action="auth/login_process.php" method="POST">
            <div class="form-group mb-3">
                <label class="form-label"><?php echo __('email_address'); ?></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="<?php echo __('enter_your_email'); ?>" required>
                </div>
            </div>
            
            <div class="form-group mb-3">
                <label class="form-label"><?php echo __('password'); ?></label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="<?php echo __('enter_your_password'); ?>" required>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember"><?php echo __('remember_me'); ?></label>
                </div>
                <a href="forgot_password.php" class="text-decoration-none"><?php echo __('forgot_password'); ?></a>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt me-2"></i><?php echo __('sign_in'); ?>
            </button>
        </form>
        
        <hr class="my-4">
        
        <p class="text-center text-muted mb-0">
            <?php echo __('dont_have_account'); ?> <a href="register.php"><?php echo __('sign_up'); ?></a>
        </p>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
