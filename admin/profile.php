<?php
$base_path = '../';
$page_title = 'My Profile';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$user = [
    'id' => 1,
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@loandisk.com',
    'phone' => '+255 712 345 678',
    'role' => 'Administrator',
    'created_at' => '2024-01-15'
];

include $base_path . 'includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h1><i class="fas fa-user-circle me-2"></i><?php echo __('my_profile'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('my_profile'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="avatar-placeholder" style="width: 120px; height: 120px; background: linear-gradient(135deg, #1a73e8, #0d47a1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; color: white; font-size: 48px;">
                        <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                    </div>
                </div>
                <h4><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($user['role']); ?></p>
                <span class="badge bg-success"><?php echo __('active'); ?></span>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i><?php echo __('account_info'); ?></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted"><?php echo __('member_since'); ?></small>
                    <p class="mb-0"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
                </div>
                <div class="mb-3">
                    <small class="text-muted"><?php echo __('last_login'); ?></small>
                    <p class="mb-0"><?php echo date('F j, Y g:i A'); ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit me-2"></i><?php echo __('edit_profile'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('first_name'); ?></label>
                            <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('last_name'); ?></label>
                            <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?php echo __('save_changes'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5><i class="fas fa-lock me-2"></i><?php echo __('change_password'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label"><?php echo __('current_password'); ?></label>
                            <input type="password" class="form-control" name="current_password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('new_password'); ?></label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?php echo __('confirm_password'); ?></label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i><?php echo __('update_password'); ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
