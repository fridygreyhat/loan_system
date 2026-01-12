<?php
$base_path = '../';
$page_title = 'Settings';
$current_page = 'settings';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1>Settings</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <a href="sms_settings.php" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-sms fa-3x text-primary mb-3"></i>
                    <h5>SMS Settings</h5>
                    <p class="text-muted mb-0">Configure SMS providers and templates</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="templates.php" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-file-word fa-3x text-primary mb-3"></i>
                    <h5>Agreement Templates</h5>
                    <p class="text-muted mb-0">Manage loan agreement templates</p>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_loan_products.php" class="text-decoration-none">
            <div class="card h-100">
                <div class="card-body text-center py-4">
                    <i class="fas fa-box fa-3x text-primary mb-3"></i>
                    <h5>Loan Products</h5>
                    <p class="text-muted mb-0">Configure loan products and terms</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php include $base_path . 'includes/footer.php'; ?>
