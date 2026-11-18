<?php
$base_path = '../';
$page_title = 'Email Settings';
$current_page = 'settings';
require_once '../helpers/connect.php';
require_once '../helpers/lang.php';
include '../includes/header.php';

$db = $con;

$settings = [];
try {
    $stmt = $db->query("SELECT * FROM email_settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    $settings = [];
}
?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>Email settings saved.</div>
<?php elseif (!empty($_GET['error'])): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<div class="content-header">
    <h1><i class="fas fa-envelope-open-text"></i> <?php echo __('email_settings'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="#"><?php echo __('settings'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('email_settings'); ?></li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('smtp_configuration'); ?></h5>
            </div>
            <div class="card-body">
                <form action="save_email_settings.php" method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('smtp_host'); ?> *</label>
                            <input type="text" name="smtp_host" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_host'] ?? ''); ?>" placeholder="smtp.gmail.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('smtp_port'); ?> *</label>
                            <input type="number" name="smtp_port" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_port'] ?? '587'); ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('smtp_username'); ?> *</label>
                            <input type="email" name="smtp_username" class="form-control" value="<?php echo htmlspecialchars($settings['smtp_user'] ?? ''); ?>" placeholder="your-email@gmail.com" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('smtp_password'); ?></label>
                            <input type="password" name="smtp_password" class="form-control" value="" autocomplete="new-password" placeholder="<?php echo !empty($settings['smtp_password']) ? 'Saved - leave blank to keep' : ''; ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('from_email'); ?> *</label>
                            <input type="email" name="from_email" class="form-control" value="<?php echo htmlspecialchars($settings['from_email'] ?? ''); ?>" placeholder="noreply@yourcompany.co.tz" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('from_name'); ?> *</label>
                            <input type="text" name="from_name" class="form-control" value="<?php echo htmlspecialchars($settings['from_name'] ?? ''); ?>" placeholder="Your Company Name" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('encryption'); ?></label>
                            <select name="encryption" class="form-select">
                                <option value="tls" <?php echo ($settings['encryption'] ?? 'tls') === 'tls' ? 'selected' : ''; ?>>TLS</option>
                                <option value="ssl" <?php echo ($settings['encryption'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                <option value="none" <?php echo ($settings['encryption'] ?? '') === 'none' ? 'selected' : ''; ?>><?php echo __('none'); ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><?php echo __('auto_email_triggers'); ?></h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_loan_approved" class="form-check-input" id="loanApproved" <?php echo ($settings['email_loan_approved'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="loanApproved"><?php echo __('loan_approved_email'); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_payment_received" class="form-check-input" id="paymentReceived" <?php echo ($settings['email_payment_received'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="paymentReceived"><?php echo __('payment_received_email'); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_payment_reminder" class="form-check-input" id="paymentReminder" <?php echo ($settings['email_payment_reminder'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="paymentReminder"><?php echo __('payment_reminder_email'); ?></label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_overdue_notice" class="form-check-input" id="overdueNotice" <?php echo ($settings['email_overdue_notice'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="overdueNotice"><?php echo __('overdue_notice_email'); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_loan_statement" class="form-check-input" id="loanStatement" <?php echo ($settings['email_loan_statement'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="loanStatement"><?php echo __('monthly_loan_statement_email'); ?></label>
                            </div>
                            <div class="form-check mb-2">
                                <input type="checkbox" name="email_birthday" class="form-check-input" id="birthday" <?php echo ($settings['email_birthday'] ?? false) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="birthday"><?php echo __('birthday_email'); ?></label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo __('save_settings'); ?>
                        </button>
                        <button type="button" class="btn btn-outline-info" id="testEmail">
                            <i class="fas fa-paper-plane"></i> <?php echo __('send_test_email'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> <?php echo __('email_setup_guide'); ?></h5>
            </div>
            <div class="card-body">
                <h6><?php echo __('gmail_smtp'); ?></h6>
                <ul class="small">
                    <li>Host: smtp.gmail.com</li>
                    <li>Port: 587</li>
                    <li>Encryption: TLS</li>
                    <li><?php echo __('enable_2fa_app_password'); ?></li>
                </ul>
                
                <h6 class="mt-3"><?php echo __('outlook_smtp'); ?></h6>
                <ul class="small">
                    <li>Host: smtp.office365.com</li>
                    <li>Port: 587</li>
                    <li>Encryption: TLS</li>
                </ul>
                
                <h6 class="mt-3"><?php echo __('custom_smtp'); ?></h6>
                <p class="small text-muted"><?php echo __('contact_provider_smtp'); ?></p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('email_templates'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted small"><?php echo __('customize_email_templates_desc'); ?></p>
                <a href="email_templates.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit"></i> <?php echo __('manage_templates'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
