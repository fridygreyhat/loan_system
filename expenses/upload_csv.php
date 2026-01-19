<?php
$base_path = '../';
$page_title = 'Upload Expenses CSV';
$current_page = 'expenses';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'csv') {
            $message = __('csv_uploaded_successfully');
        } else {
            $error = __('invalid_file_type');
        }
    } else {
        $error = __('upload_failed');
    }
}

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <div>
        <h1><i class="fas fa-file-csv text-primary me-2"></i><?php echo __('upload_expenses_csv'); ?></h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
                <li class="breadcrumb-item"><a href="view_expenses.php"><?php echo __('expenses'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('upload_csv'); ?></li>
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
                <h5><i class="fas fa-upload me-2"></i><?php echo __('upload_csv'); ?></h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-4">
                        <label class="form-label"><?php echo __('select_csv_file'); ?></label>
                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                        <small class="text-muted"><?php echo __('csv_format_help'); ?></small>
                    </div>
                    
                    <div class="border rounded p-4 bg-light mb-4">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i><?php echo __('csv_format'); ?></h6>
                        <p class="mb-2"><?php echo __('csv_columns_required'); ?>:</p>
                        <code>date, vendor, expense_type, description, amount</code>
                        <hr>
                        <p class="mb-0 small text-muted">
                            <?php echo __('example'); ?>: 2025-01-15, Office Supplies Co., Office Supplies, Paper and pens, 50000
                        </p>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i><?php echo __('upload_and_process'); ?>
                        </button>
                        <a href="view_expenses.php" class="btn btn-outline-secondary">
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
                <h5><i class="fas fa-download me-2"></i><?php echo __('download_template'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted"><?php echo __('download_sample_csv'); ?></p>
                <a href="#" class="btn btn-outline-primary w-100">
                    <i class="fas fa-file-download me-2"></i><?php echo __('download_template'); ?>
                </a>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="fas fa-history me-2"></i><?php echo __('recent_uploads'); ?></h5>
            </div>
            <div class="card-body">
                <p class="text-muted text-center py-3">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                    <?php echo __('no_recent_uploads'); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
