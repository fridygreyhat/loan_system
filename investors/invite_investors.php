<?php
$base_path = '../';
$page_title = 'Invite Investors';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emails = trim($_POST['emails'] ?? '');
    $invitation_message = trim($_POST['invitation_message'] ?? '');
    
    if (empty($emails)) {
        $error = __('please_enter_emails');
    } else {
        $email_list = array_filter(array_map('trim', explode("\n", $emails)));
        $valid_emails = array_filter($email_list, fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL));
        
        if (count($valid_emails) > 0) {
            $message = __('invitations_sent') . ' ' . count($valid_emails) . ' ' . __('investors');
        } else {
            $error = __('no_valid_emails');
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-plus text-primary me-2"></i><?php echo __('invite_investors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('invite_investors'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-envelope-open-text me-2"></i><?php echo __('send_invitations'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('email_addresses'); ?></label>
                        <textarea name="emails" class="form-control" rows="6" placeholder="<?php echo __('enter_one_email_per_line'); ?>" required></textarea>
                        <small class="text-muted"><?php echo __('enter_one_email_per_line'); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('invitation_message'); ?> <span class="text-muted">(<?php echo __('optional'); ?>)</span></label>
                        <textarea name="invitation_message" class="form-control" rows="4" placeholder="<?php echo __('optional_personal_message'); ?>"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i><?php echo __('send_invitations'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i><?php echo __('about_invitations'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted"><?php echo __('invitation_description'); ?></p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('invitation_benefit_1'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('invitation_benefit_2'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('invitation_benefit_3'); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i><?php echo __('recent_invitations'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted text-center py-3">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    <?php echo __('no_recent_invitations'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
