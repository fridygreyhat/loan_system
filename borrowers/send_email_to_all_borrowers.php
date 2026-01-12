<?php
$base_path = '../';
$page_title = 'Send Email to All Borrowers';
$current_page = 'borrowers';

require_once $base_path . 'helpers/connect.php';

$borrower_count = 0;
try {
    $stmt = $con->query("SELECT COUNT(*) as count FROM borrower WHERE email IS NOT NULL AND email != ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $borrower_count = $result['count'] ?? 0;
} catch (PDOException $e) {
}

$success = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($subject) || empty($message)) {
        $error = "Subject and message are required.";
    } else {
        $success = true;
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Send Email to All Borrowers</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="view_borrowers.php">Borrowers</a></li>
            <li class="breadcrumb-item active">Send Email</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-envelope me-2"></i>Compose Email</h5>
            </div>
            <div class="card-body">
                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>Email queued for sending to all borrowers.
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Subject *</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Enter email subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message *</label>
                        <textarea name="message" class="form-control" rows="10" required placeholder="Enter your message here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Available Placeholders</label>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-secondary">{first_name}</span>
                            <span class="badge bg-secondary">{last_name}</span>
                            <span class="badge bg-secondary">{email}</span>
                            <span class="badge bg-secondary">{phone}</span>
                            <span class="badge bg-secondary">{loan_balance}</span>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send Email</button>
                        <a href="view_borrowers.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i>Email Info</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <i class="fas fa-users fa-3x text-primary mb-2"></i>
                    <h3><?php echo $borrower_count; ?></h3>
                    <p class="text-muted">Borrowers with email addresses</p>
                </div>
                <hr>
                <p class="small text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Emails will be sent to all borrowers who have an email address on file.
                </p>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
