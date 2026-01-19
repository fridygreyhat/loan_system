<?php
$base_path = '../';
$page_title = 'Send SMS to All Investors';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$investors = [];
$message = '';
$error = '';

try {
    $stmt = $con->query("SELECT id, first_name, last_name, phone FROM investors WHERE phone IS NOT NULL AND phone != '' ORDER BY first_name, last_name");
    $investors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $investors = [
        ['id' => 1, 'first_name' => 'James', 'last_name' => 'Wilson', 'phone' => '+255712345678'],
        ['id' => 2, 'first_name' => 'Sarah', 'last_name' => 'Brown', 'phone' => '+255723456789'],
        ['id' => 3, 'first_name' => 'Michael', 'last_name' => 'Davis', 'phone' => '+255734567890'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sms_message = trim($_POST['message'] ?? '');
    $selected = $_POST['investors'] ?? [];
    
    if (empty($sms_message)) {
        $error = __('please_enter_message');
    } elseif (empty($selected)) {
        $error = __('please_select_recipients');
    } else {
        $message = __('sms_queued') . ' ' . count($selected) . ' ' . __('investors');
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-sms text-primary me-2"></i><?php echo __('send_sms_to_all_investors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('send_sms_to_all_investors'); ?></li>
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
                <h5><i class="fas fa-envelope me-2"></i><?php echo __('compose_message'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('message'); ?></label>
                        <textarea name="message" class="form-control" rows="5" placeholder="<?php echo __('enter_message'); ?>..." required></textarea>
                        <small class="text-muted"><?php echo __('max_characters'); ?>: 160</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('select_recipients'); ?></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            <label class="form-check-label" for="selectAll"><strong><?php echo __('select_all'); ?> (<?php echo count($investors); ?>)</strong></label>
                        </div>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($investors as $inv): ?>
                            <div class="form-check">
                                <input class="form-check-input investor-check" type="checkbox" name="investors[]" value="<?php echo $inv['id']; ?>" id="inv<?php echo $inv['id']; ?>">
                                <label class="form-check-label" for="inv<?php echo $inv['id']; ?>">
                                    <?php echo htmlspecialchars($inv['first_name'] . ' ' . $inv['last_name']); ?> - <?php echo htmlspecialchars($inv['phone']); ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i><?php echo __('send'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i><?php echo __('sms_info'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted"><?php echo __('sms_will_be_sent'); ?></p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('instant_delivery'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('delivery_tracking'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('personalized_messages'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAll(source) {
    document.querySelectorAll('.investor-check').forEach(cb => cb.checked = source.checked);
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>
