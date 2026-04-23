<?php
$base_path = '../';
$page_title = 'Settings';
$current_page = 'settings';

require_once $base_path . 'helpers/connect.php';
require_once $base_path . 'helpers/lang.php';

include $base_path . 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-cog me-2"></i><?php echo __('settings'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('settings'); ?></li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <a href="general_settings.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-sliders-h fa-3x text-primary"></i></div>
                <h5 class="mb-1"><?php echo __('general_settings'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('company_info_currency_timezone'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="loan_products.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-box fa-3x text-success"></i></div>
                <h5 class="mb-1"><?php echo __('loan_products'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_loan_products'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_loan_fees.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-percentage fa-3x text-warning"></i></div>
                <h5 class="mb-1"><?php echo __('loan_fees'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_loan_fees_charges'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_loan_repayment_methods.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-calendar-alt fa-3x text-info"></i></div>
                <h5 class="mb-1"><?php echo __('repayment_methods'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_repayment_cycles'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_collateral_types.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-shield-alt fa-3x text-danger"></i></div>
                <h5 class="mb-1"><?php echo __('collateral_types'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_collateral_types'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="sms_settings.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-sms fa-3x text-primary"></i></div>
                <h5 class="mb-1"><?php echo __('sms_settings'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('configure_sms_provider'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="email_settings.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-envelope fa-3x text-success"></i></div>
                <h5 class="mb-1"><?php echo __('email_settings'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('configure_smtp_email'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="templates.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-file-word fa-3x text-info"></i></div>
                <h5 class="mb-1"><?php echo __('agreement_templates'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('upload_loan_agreement_templates'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="branches.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-building fa-3x text-warning"></i></div>
                <h5 class="mb-1"><?php echo __('branches'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_branches'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_loan_disbursed_by.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-hand-holding-usd fa-3x text-danger"></i></div>
                <h5 class="mb-1"><?php echo __('disbursement_methods'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_disbursement_methods'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="view_collectors.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-user-tie fa-3x text-primary"></i></div>
                <h5 class="mb-1"><?php echo __('collectors'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('manage_loan_collectors'); ?></p>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="profile.php" class="card text-decoration-none hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3"><i class="fas fa-user-circle fa-3x text-secondary"></i></div>
                <h5 class="mb-1"><?php echo __('my_profile'); ?></h5>
                <p class="text-muted mb-0 small"><?php echo __('update_your_profile'); ?></p>
            </div>
        </a>
    </div>
</div>

<style>
.hover-card { transition: transform 0.2s, box-shadow 0.2s; }
.hover-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; }
</style>

<?php include $base_path . 'includes/footer.php'; ?>
