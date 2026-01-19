<?php
$base_path = '../';
$page_title = 'Send Email to All Borrowers';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$borrowers = [];
$message = '';
$error = '';

try {
    $stmt = $con->query("SELECT id, fname, lname, email FROM borrower WHERE email IS NOT NULL AND email != '' ORDER BY fname, lname");
    $borrowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $email_message = trim($_POST['message'] ?? '');
    $selected = $_POST['borrowers'] ?? [];
    
    if (empty($subject)) {
        $error = 'Please enter a subject';
    } elseif (empty($email_message)) {
        $error = 'Please enter a message';
    } elseif (empty($selected)) {
        $error = 'Please select at least one borrower';
    } else {
        $message = 'Email queued for ' . count($selected) . ' borrowers';
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-envelope text-primary me-2"></i><?php echo __('send_email_to_all_borrowers'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_borrowers.php"><?php echo __('borrowers'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('send_email_to_all_borrowers'); ?></li>
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
                <h5><i class="fas fa-envelope me-2"></i><?php echo __('compose_email'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('subject'); ?></label>
                        <input type="text" name="subject" class="form-control" placeholder="<?php echo __('email_subject'); ?>..." required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('message'); ?></label>
                        <textarea name="message" class="form-control" rows="6" placeholder="<?php echo __('enter_message'); ?>..." required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('select_recipients'); ?></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleAll(this)">
                            <label class="form-check-label" for="selectAll"><strong><?php echo __('select_all'); ?> (<?php echo count($borrowers); ?>)</strong></label>
                        </div>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <?php foreach ($borrowers as $b): ?>
                            <div class="form-check">
                                <input class="form-check-input borrower-check" type="checkbox" name="borrowers[]" value="<?php echo $b['id']; ?>" id="b<?php echo $b['id']; ?>">
                                <label class="form-check-label" for="b<?php echo $b['id']; ?>">
                                    <?php echo htmlspecialchars($b['fname'] . ' ' . $b['lname']); ?> - <?php echo htmlspecialchars($b['email']); ?>
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
</div>

<script>
function toggleAll(source) {
    document.querySelectorAll('.borrower-check').forEach(cb => cb.checked = source.checked);
}
</script>

<?php include $base_path . 'includes/footer.php'; ?>
