<?php
$base_path = '../';
$page_title = 'Add Customer';
$current_page = 'other_income';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_person = trim($_POST['contact_person'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    if (empty($name)) {
        $error = __('customer_name_required');
    } else {
        try {
            $stmt = $con->prepare("INSERT INTO other_income_customers (name, email, phone, address, contact_person, notes, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->execute([$name, $email, $phone, $address, $contact_person, $notes]);
            $message = __('customer_added_successfully');
        } catch (PDOException $e) {
            $error = 'Could not save: ' . $e->getMessage();
        }
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-user-plus text-primary me-2"></i><?php echo __('add_customer'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_customers.php"><?php echo __('view_customers'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('add_customer'); ?></li>
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
                <h5><i class="fas fa-user me-2"></i><?php echo __('customer_information'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('customer_name'); ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="<?php echo __('enter_customer_name'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('contact_person'); ?></label>
                            <input type="text" name="contact_person" class="form-control" placeholder="<?php echo __('enter_contact_person'); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('email'); ?></label>
                            <input type="email" name="email" class="form-control" placeholder="customer@example.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('phone'); ?></label>
                            <input type="tel" name="phone" class="form-control" placeholder="+255 xxx xxx xxx">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('address'); ?></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="<?php echo __('enter_address'); ?>"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('notes'); ?></label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="<?php echo __('additional_notes'); ?>"></textarea>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?php echo __('save_customer'); ?>
                        </button>
                        <a href="view_customers.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i><?php echo __('cancel'); ?>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle me-2"></i><?php echo __('tips'); ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('add_customer_tip_1'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('add_customer_tip_2'); ?></li>
                    <li class="mb-2"><i class="fas fa-check text-success me-2"></i><?php echo __('add_customer_tip_3'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
