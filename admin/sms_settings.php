<?php
$base_path = '../';
$page_title = 'SMS Settings';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';

$settings = null;
$templates = [];
try {
    $stmt = $con->query("SELECT * FROM sms_settings WHERE is_active = TRUE LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $con->query("SELECT * FROM sms_templates ORDER BY name");
    $templates = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>SMS Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="index.php">Settings</a></li>
            <li class="breadcrumb-item active">SMS Settings</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-cog me-2"></i>SMS Provider Configuration</h5>
            </div>
            <div class="card-body">
                <form action="save_sms_settings.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">SMS Provider</label>
                        <select name="provider" class="form-select">
                            <option value="messaging_service_tz" <?php echo ($settings['provider'] ?? '') === 'messaging_service_tz' ? 'selected' : ''; ?>>Messaging Service TZ</option>
                            <option value="twilio" <?php echo ($settings['provider'] ?? '') === 'twilio' ? 'selected' : ''; ?>>Twilio</option>
                            <option value="africas_talking" <?php echo ($settings['provider'] ?? '') === 'africas_talking' ? 'selected' : ''; ?>>Africa's Talking</option>
                            <option value="nexmo" <?php echo ($settings['provider'] ?? '') === 'nexmo' ? 'selected' : ''; ?>>Nexmo (Vonage)</option>
                            <option value="termii" <?php echo ($settings['provider'] ?? '') === 'termii' ? 'selected' : ''; ?>>Termii</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Key / Account SID</label>
                        <input type="text" name="api_key" class="form-control" value="<?php echo htmlspecialchars($settings['api_key'] ?? ''); ?>" placeholder="Enter your API key">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">API Secret / Auth Token</label>
                        <input type="password" name="api_secret" class="form-control" value="<?php echo $settings['api_secret'] ? '********' : ''; ?>" placeholder="Enter your API secret">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sender ID</label>
                        <input type="text" name="sender_id" class="form-control" value="<?php echo htmlspecialchars($settings['sender_id'] ?? ''); ?>" placeholder="e.g., LoanDisk">
                        <small class="text-muted">This will appear as the sender name for SMS</small>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" <?php echo ($settings['is_active'] ?? false) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="is_active">Enable SMS Notifications</label>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="testSMS()">
                        <i class="fas fa-paper-plane me-2"></i>Send Test SMS
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-robot me-2"></i>Auto-Send SMS</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="reminder_before">
                    <label class="form-check-label" for="reminder_before">
                        <strong>Payment Reminder</strong>
                        <small class="d-block text-muted">Send reminder 3 days before due date</small>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="payment_confirm">
                    <label class="form-check-label" for="payment_confirm">
                        <strong>Payment Confirmation</strong>
                        <small class="d-block text-muted">Send confirmation after payment received</small>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="overdue_notice">
                    <label class="form-check-label" for="overdue_notice">
                        <strong>Overdue Notice</strong>
                        <small class="d-block text-muted">Send notice when payment is overdue</small>
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="loan_approved">
                    <label class="form-check-label" for="loan_approved">
                        <strong>Loan Approved</strong>
                        <small class="d-block text-muted">Notify borrower when loan is approved</small>
                    </label>
                </div>
                <button class="btn btn-primary">Save Auto-Send Settings</button>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="fas fa-file-alt me-2"></i>SMS Templates</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTemplateModal">
                    <i class="fas fa-plus me-1"></i>Add Template
                </button>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($templates as $template): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1"><?php echo htmlspecialchars($template['name']); ?></h6>
                                <p class="mb-1 text-muted small"><?php echo htmlspecialchars($template['message']); ?></p>
                                <small class="text-primary">
                                    Placeholders: <?php echo htmlspecialchars($template['placeholders'] ?? 'None'); ?>
                                </small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></button>
                                <button class="btn btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Available Placeholders</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Use these placeholders in your SMS templates. They will be replaced with actual data when sending.</p>
                <div class="row">
                    <div class="col-6">
                        <code>{borrower_name}</code> - Full name<br>
                        <code>{first_name}</code> - First name<br>
                        <code>{loan_amount}</code> - Loan amount<br>
                        <code>{amount}</code> - Payment amount
                    </div>
                    <div class="col-6">
                        <code>{due_date}</code> - Due date<br>
                        <code>{balance}</code> - Outstanding balance<br>
                        <code>{days}</code> - Days overdue<br>
                        <code>{loan_id}</code> - Loan reference
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function testSMS() {
    const phone = prompt('Enter phone number to send test SMS:');
    if (phone) {
        alert('Test SMS would be sent to: ' + phone);
    }
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>
