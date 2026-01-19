<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once ($base_path ?? '') . 'helpers/lang.php';
?>
<!DOCTYPE html>
<html lang="<?php echo get_current_lang(); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Loan_system</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo $base_path ?? ''; ?>css/loandisk-modern.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo $base_path ?? ''; ?>home/dashboard.php" class="sidebar-logo">
                    <i class="fas fa-coins"></i>
                    <span>Loan_system</span>
                </a>
            </div>
            
            <div class="sidebar-user">
                <img src="<?php echo $base_path ?? ''; ?>uploads/staff_images/thumbnails/user2-160x160.jpg" alt="User" onerror="this.src='https://ui-avatars.com/api/?name=Admin&background=2c7be5&color=fff'">
                <div class="sidebar-user-info">
                    <h6><?php echo isset($current_user) ? $current_user : __('admin_user'); ?></h6>
                    <span><?php echo isset($user_role) ? $user_role : __('administrator'); ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?php echo $base_path ?? ''; ?>home/dashboard.php" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> <?php echo __('dashboard'); ?></a></li>
                    
                    <li class="has-submenu <?php echo in_array($current_page ?? '', ['borrowers', 'add-borrower']) ? 'open' : ''; ?>">
                        <a href="#"><i class="fas fa-users"></i> <?php echo __('borrowers'); ?></a>
                        <ul class="submenu <?php echo in_array($current_page ?? '', ['borrowers', 'add-borrower']) ? 'show' : ''; ?>">
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/view_borrowers.php"><?php echo __('view_borrowers'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/add_borrower.php"><?php echo __('add_borrower'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/groups/view_groups.php"><?php echo __('view_borrower_groups'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/groups/add_group.php"><?php echo __('add_borrowers_group'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/send_sms_all.php"><?php echo __('send_sms_to_all_borrowers'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/send_email_all.php"><?php echo __('send_email_to_all_borrowers'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/invite_borrowers.php"><?php echo __('invite_borrowers'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu <?php echo in_array($current_page ?? '', ['loans', 'add-loan']) ? 'open' : ''; ?>">
                        <a href="#"><i class="fas fa-hand-holding-usd"></i> <?php echo __('loans'); ?></a>
                        <ul class="submenu <?php echo in_array($current_page ?? '', ['loans', 'add-loan']) ? 'show' : ''; ?>">
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/view_loans.php"><?php echo __('view_all_loans'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/add_loan.php"><?php echo __('add_loan'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/due_loans.php"><?php echo __('due_loans'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/missed_repayments.php"><?php echo __('missed_repayments'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/loans_in_arrears.php"><?php echo __('loans_in_arrears'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/no_repayments.php"><?php echo __('no_repayments'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/past_maturity.php"><?php echo __('past_maturity_date'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/principal_outstanding.php"><?php echo __('principal_outstanding'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/one_month_late.php"><?php echo __('one_month_late_loans'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/three_months_late.php"><?php echo __('three_months_late_loans'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/loan_calculator.php"><?php echo __('loan_calculator'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/guarantors.php"><?php echo __('guarantors'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/loan_comments.php"><?php echo __('loan_comments'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/approve_loans.php"><?php echo __('approve_loans'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-money-bill-wave"></i> <?php echo __('repayments'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/view_repayments.php"><?php echo __('view_repayments'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/add_bulk_repayments.php"><?php echo __('add_bulk_repayments'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/import_csv.php"><?php echo __('add_repayments_csv'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/charts.php"><?php echo __('repayments_charts'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/approve_repayments.php"><?php echo __('approve_repayments'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-piggy-bank"></i> <?php echo __('savings'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/view_accounts.php"><?php echo __('view_accounts'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/add_account.php"><?php echo __('add_account'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/transactions.php"><?php echo __('transactions'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-chart-bar"></i> <?php echo __('investors'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/view_investors.php"><?php echo __('view_investors'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/add_investor.php"><?php echo __('add_investor'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/investments.php"><?php echo __('investments'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>collateral/collateral_register.php"><i class="fas fa-shield-alt"></i> <?php echo __('collateral'); ?></a></li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>calendar/view_calendar.php" class="<?php echo ($current_page ?? '') === 'calendar' ? 'active' : ''; ?>"><i class="fas fa-calendar-alt"></i> <?php echo __('calendar'); ?></a></li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>charts/view_charts.php" class="<?php echo ($current_page ?? '') === 'charts' ? 'active' : ''; ?>"><i class="fas fa-chart-pie"></i> <?php echo __('charts_analytics'); ?></a></li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>assets/view_assets.php" class="<?php echo ($current_page ?? '') === 'assets' ? 'active' : ''; ?>"><i class="fas fa-building"></i> <?php echo __('asset_management'); ?></a></li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>e_signature/view_documents.php" class="<?php echo ($current_page ?? '') === 'e_signature' ? 'active' : ''; ?>"><i class="fas fa-file-signature"></i> <?php echo __('e_signature'); ?></a></li>
                    
                    <li class="has-submenu <?php echo ($current_page ?? '') === 'accounting' ? 'open' : ''; ?>">
                        <a href="#"><i class="fas fa-calculator"></i> <?php echo __('accounting'); ?></a>
                        <ul class="submenu <?php echo ($current_page ?? '') === 'accounting' ? 'show' : ''; ?>">
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/cash_flow.php"><?php echo __('cash_flow'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/profit_loss.php"><?php echo __('profit_loss'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/balance_sheet.php"><?php echo __('balance_sheet'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/trial_balance.php"><?php echo __('trial_balance'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/general_ledger.php"><?php echo __('general_ledger'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/chart_of_accounts.php"><?php echo __('chart_of_accounts'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>accounting/manual_journal.php"><?php echo __('manual_journal'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-invoice"></i> <?php echo __('collection_sheets'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>collection_sheets/daily.php"><?php echo __('daily_collection'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>collection_sheets/missed.php"><?php echo __('missed_repayments'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-chart-pie"></i> <?php echo __('reports'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/borrowers.php"><?php echo __('borrowers_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/loans.php"><?php echo __('loan_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/arrears_aging.php"><?php echo __('loan_arrears_aging_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/collections.php"><?php echo __('collections_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/collector.php"><?php echo __('collector_report_staff'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/deferred_income.php"><?php echo __('deferred_income'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/deferred_income_monthly.php"><?php echo __('deferred_income_monthly'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/pro_rata_collections.php"><?php echo __('pro_rata_collections_monthly'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/disbursement.php"><?php echo __('disbursement_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/fees.php"><?php echo __('fees_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/loan_officer.php"><?php echo __('loan_officer_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/loan_products.php"><?php echo __('loan_products_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/mfrs_ratios.php"><?php echo __('mfrs_ratios'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/daily.php"><?php echo __('daily_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/monthly.php"><?php echo __('monthly_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/outstanding.php"><?php echo __('outstanding_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/portfolio_risk.php"><?php echo __('portfolio_at_risk_par'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/at_a_glance.php"><?php echo __('at_a_glance_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/fully_paid_loans.php"><?php echo __('fully_paid_loans_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/defaulted_loans.php"><?php echo __('defaulted_loans_report'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/all_entries.php"><?php echo __('all_entries'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-user-tie"></i> <?php echo __('staff'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/view_staff.php"><?php echo __('view_staff'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/add_staff.php"><?php echo __('add_staff'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/roles.php"><?php echo __('roles_permissions'); ?></a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-cog"></i> <?php echo __('settings'); ?></a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/loan_products.php"><?php echo __('loan_products'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/branches.php"><?php echo __('branches'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/sms_settings.php"><?php echo __('sms_settings'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/email_settings.php"><?php echo __('email_settings'); ?></a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/templates.php"><?php echo __('agreement_templates'); ?></a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <main class="main-content">
            <nav class="top-navbar">
                <button class="btn btn-link d-lg-none" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="search-box d-none d-md-block">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="<?php echo __('search_placeholder'); ?>" class="form-control">
                </div>
                <div class="navbar-actions">
                    <div class="dropdown me-2">
                        <div class="nav-icon dropdown-toggle" data-bs-toggle="dropdown" title="<?php echo __('language'); ?>">
                            <i class="fas fa-globe"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item <?php echo get_current_lang() === 'en' ? 'active' : ''; ?>" href="?lang=en"><i class="me-2">🇬🇧</i> <?php echo __('english'); ?></a></li>
                            <li><a class="dropdown-item <?php echo get_current_lang() === 'sw' ? 'active' : ''; ?>" href="?lang=sw"><i class="me-2">🇹🇿</i> <?php echo __('swahili'); ?></a></li>
                        </ul>
                    </div>
                    <div class="nav-icon" title="<?php echo __('notifications'); ?>">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="nav-icon" title="<?php echo __('messages'); ?>">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="dropdown">
                        <div class="nav-icon dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>admin/profile.php"><i class="fas fa-user me-2"></i> <?php echo __('profile'); ?></a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>admin/settings.php"><i class="fas fa-cog me-2"></i> <?php echo __('settings'); ?></a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> <?php echo __('logout'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <div class="content-area">
