<?php
$base_path = '../';
$page_title = 'General Settings';
$current_page = 'settings';
include '../includes/header.php';
require_once '../helpers/connect.php';

$db = $con;

$settings = [];
try {
    $stmt = $db->query("SELECT * FROM system_settings LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
} catch (PDOException $e) {
    $settings = [];
}
?>

<div class="content-header">
    <h1><i class="fas fa-cogs"></i> <?php echo __('general_settings'); ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $base_path; ?>home/dashboard.php"><?php echo __('dashboard'); ?></a></li>
            <li class="breadcrumb-item"><a href="#"><?php echo __('settings'); ?></a></li>
            <li class="breadcrumb-item active"><?php echo __('general_settings'); ?></li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('company_information'); ?></h5>
            </div>
            <div class="card-body">
                <form action="save_general_settings.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('company_name'); ?> *</label>
                            <input type="text" name="company_name" class="form-control" value="<?php echo htmlspecialchars($settings['company_name'] ?? 'Loan System'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('company_email'); ?></label>
                            <input type="email" name="company_email" class="form-control" value="<?php echo htmlspecialchars($settings['company_email'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('company_phone'); ?></label>
                            <input type="tel" name="company_phone" class="form-control" value="<?php echo htmlspecialchars($settings['company_phone'] ?? ''); ?>" placeholder="+255 XXX XXX XXX">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('company_address'); ?></label>
                            <input type="text" name="company_address" class="form-control" value="<?php echo htmlspecialchars($settings['company_address'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?php echo __('company_logo'); ?></label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                        <?php if (!empty($settings['logo'])): ?>
                        <div class="mt-2">
                            <img src="<?php echo $base_path . $settings['logo']; ?>" alt="Logo" style="max-height: 60px;">
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><?php echo __('regional_settings'); ?></h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('currency'); ?></label>
                            <select name="currency" class="form-select">
                                <option value="TZS" <?php echo ($settings['currency'] ?? 'TZS') === 'TZS' ? 'selected' : ''; ?>>TZS - Tanzanian Shilling</option>
                                <option value="KES" <?php echo ($settings['currency'] ?? '') === 'KES' ? 'selected' : ''; ?>>KES - Kenyan Shilling</option>
                                <option value="UGX" <?php echo ($settings['currency'] ?? '') === 'UGX' ? 'selected' : ''; ?>>UGX - Ugandan Shilling</option>
                                <option value="USD" <?php echo ($settings['currency'] ?? '') === 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                <option value="EUR" <?php echo ($settings['currency'] ?? '') === 'EUR' ? 'selected' : ''; ?>>EUR - Euro</option>
                                <option value="GBP" <?php echo ($settings['currency'] ?? '') === 'GBP' ? 'selected' : ''; ?>>GBP - British Pound</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('date_format'); ?></label>
                            <select name="date_format" class="form-select">
                                <option value="d/m/Y" <?php echo ($settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : ''; ?>>DD/MM/YYYY (<?php echo date('d/m/Y'); ?>)</option>
                                <option value="m/d/Y" <?php echo ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : ''; ?>>MM/DD/YYYY (<?php echo date('m/d/Y'); ?>)</option>
                                <option value="Y-m-d" <?php echo ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : ''; ?>>YYYY-MM-DD (<?php echo date('Y-m-d'); ?>)</option>
                                <option value="d M Y" <?php echo ($settings['date_format'] ?? '') === 'd M Y' ? 'selected' : ''; ?>>DD Mon YYYY (<?php echo date('d M Y'); ?>)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label"><?php echo __('timezone'); ?></label>
                            <select name="timezone" class="form-select">
                                <option value="Africa/Dar_es_Salaam" <?php echo ($settings['timezone'] ?? 'Africa/Dar_es_Salaam') === 'Africa/Dar_es_Salaam' ? 'selected' : ''; ?>>Africa/Dar_es_Salaam (EAT)</option>
                                <option value="Africa/Nairobi" <?php echo ($settings['timezone'] ?? '') === 'Africa/Nairobi' ? 'selected' : ''; ?>>Africa/Nairobi (EAT)</option>
                                <option value="Africa/Kampala" <?php echo ($settings['timezone'] ?? '') === 'Africa/Kampala' ? 'selected' : ''; ?>>Africa/Kampala (EAT)</option>
                                <option value="Africa/Lagos" <?php echo ($settings['timezone'] ?? '') === 'Africa/Lagos' ? 'selected' : ''; ?>>Africa/Lagos (WAT)</option>
                                <option value="UTC" <?php echo ($settings['timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('number_format'); ?></label>
                            <select name="number_format" class="form-select">
                                <option value="1,234.56" <?php echo ($settings['number_format'] ?? '1,234.56') === '1,234.56' ? 'selected' : ''; ?>>1,234.56 (<?php echo __('comma_thousand_dot_decimal'); ?>)</option>
                                <option value="1.234,56" <?php echo ($settings['number_format'] ?? '') === '1.234,56' ? 'selected' : ''; ?>>1.234,56 (<?php echo __('dot_thousand_comma_decimal'); ?>)</option>
                                <option value="1 234.56" <?php echo ($settings['number_format'] ?? '') === '1 234.56' ? 'selected' : ''; ?>>1 234.56 (<?php echo __('space_thousand_dot_decimal'); ?>)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('fiscal_year_start'); ?></label>
                            <select name="fiscal_year_start" class="form-select">
                                <option value="1" <?php echo ($settings['fiscal_year_start'] ?? '1') === '1' ? 'selected' : ''; ?>>January</option>
                                <option value="4" <?php echo ($settings['fiscal_year_start'] ?? '') === '4' ? 'selected' : ''; ?>>April</option>
                                <option value="7" <?php echo ($settings['fiscal_year_start'] ?? '') === '7' ? 'selected' : ''; ?>>July</option>
                                <option value="10" <?php echo ($settings['fiscal_year_start'] ?? '') === '10' ? 'selected' : ''; ?>>October</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><?php echo __('unique_number_formats'); ?></h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('borrower_unique_number'); ?></label>
                            <input type="text" name="borrower_number_format" class="form-control" value="<?php echo htmlspecialchars($settings['borrower_number_format'] ?? 'BRW-{NNNN}'); ?>" placeholder="BRW-{NNNN}">
                            <small class="text-muted"><?php echo __('use_nnnn_placeholder'); ?></small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><?php echo __('loan_unique_number'); ?></label>
                            <input type="text" name="loan_number_format" class="form-control" value="<?php echo htmlspecialchars($settings['loan_number_format'] ?? 'LN-{YYYY}-{NNNN}'); ?>" placeholder="LN-{YYYY}-{NNNN}">
                            <small class="text-muted"><?php echo __('use_yyyy_nnnn_placeholder'); ?></small>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo __('save_settings'); ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> <?php echo __('settings_info'); ?></h5>
            </div>
            <div class="card-body">
                <p class="small"><?php echo __('settings_affect_display'); ?></p>
                <ul class="small">
                    <li><?php echo __('currency_affects_reports'); ?></li>
                    <li><?php echo __('date_format_affects_display'); ?></li>
                    <li><?php echo __('timezone_affects_timestamps'); ?></li>
                    <li><?php echo __('logo_appears_statements'); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0"><?php echo __('system_info'); ?></h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><?php echo __('php_version'); ?></td>
                        <td><strong><?php echo phpversion(); ?></strong></td>
                    </tr>
                    <tr>
                        <td><?php echo __('database'); ?></td>
                        <td><strong>PostgreSQL</strong></td>
                    </tr>
                    <tr>
                        <td><?php echo __('server_time'); ?></td>
                        <td><strong><?php echo date('Y-m-d H:i:s'); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
