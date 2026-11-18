<?php
$base_path = '../';
$page_title = 'Send Email to All Investors';
$current_page = 'investors';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$investors = [];
$message = '';
$error = '';

try {
    $stmt = $con->query("SELECT id, first_name, last_name, email FROM investors WHERE email IS NOT NULL AND email != '' ORDER BY first_name, last_name");
    $investors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $investors = [];
    $load_error = 'Could not load data: ' . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $email_message = trim($_POST['message'] ?? '');
    $selected = $_POST['investors'] ?? [];
    
    if (empty($subject)) {
        $error = __('please_enter_subject');
    } elseif (empty($email_message)) {
        $error = __('please_enter_message');
    } elseif (empty($selected)) {
        $error = __('please_select_recipients');
    } else {
        $message = __('email_queued') . ' ' . count($selected) . ' ' . __('investors');
    }
}

include $base_path . 'includes/header.php';
?>
<?php if (!empty($load_error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i><?php echo htmlspecialchars($load_error); ?></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-envelope text-primary me-2"></i><?php echo __('send_email_to_all_investors'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_investors.php"><?php echo __('investors'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('send_email_to_all_investors'); ?></li>
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
                <h5><i class="fas fa-edit me-2"></i><?php echo __('compose_email'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('subject'); ?></label>
                        <input type="text" name="subject" class="form-control" placeholder="<?php echo __('email_subject'); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('message'); ?></label>
                        <textarea name="message" class="form-control" rows="8" placeholder="<?php echo __('enter_message'); ?>..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('select_recipients'); ?></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            <label class="form-check-label" for="selectAll"><strong><?php echo __('select_all'); ?> (<?php echo count($investors); ?>)</strong></label>
                        </div>
                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach ($investors as $inv): ?>
                            <div class="form-check">
                                <input class="form-check-input investor-check" type="checkbox" name="investors[]" value="<?php echo $inv['id']; ?>" id="inv<?php echo $inv['id']; ?>">
                                <label class="form-check-label" for="inv<?php echo $inv['id']; ?>">
                                    <?php echo htmlspecialchars($inv['first_name'] . ' ' . $inv['last_name']); ?> - <?php echo htmlspecialchars($inv['email']); ?>
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
                <h5><i class="fas fa-lightbulb me-2"></i><?php echo __('tips'); ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong><?php echo __('personalize'); ?></strong>
                        <p class="text-muted small mb-0"><?php echo __('use_investor_names'); ?></p>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong><?php echo __('clear_subject'); ?></strong>
                        <p class="text-muted small mb-0"><?php echo __('descriptive_subject'); ?></p>
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <strong><?php echo __('concise_message'); ?></strong>
                        <p class="text-muted small mb-0"><?php echo __('keep_it_brief'); ?></p>
                    </li>
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
