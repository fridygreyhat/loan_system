<!DOCTYPE html>
<html lang="en">
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
                    <h6><?php echo isset($current_user) ? $current_user : 'Admin User'; ?></h6>
                    <span><?php echo isset($user_role) ? $user_role : 'Administrator'; ?></span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="<?php echo $base_path ?? ''; ?>home/dashboard.php" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Dashboard</a></li>
                    
                    <li class="has-submenu <?php echo in_array($current_page ?? '', ['borrowers', 'add-borrower']) ? 'open' : ''; ?>">
                        <a href="#"><i class="fas fa-users"></i> Borrowers</a>
                        <ul class="submenu <?php echo in_array($current_page ?? '', ['borrowers', 'add-borrower']) ? 'show' : ''; ?>">
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/view_borrowers.php">View Borrowers</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/add_borrower.php">Add Borrower</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>borrowers/groups/view_groups.php">Borrower Groups</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu <?php echo in_array($current_page ?? '', ['loans', 'add-loan']) ? 'open' : ''; ?>">
                        <a href="#"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                        <ul class="submenu <?php echo in_array($current_page ?? '', ['loans', 'add-loan']) ? 'show' : ''; ?>">
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/view_loans.php">View All Loans</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/add_loan.php">Add Loan</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/due_loans.php">Due Loans</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/missed_repayments.php">Missed Repayments</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>loans/loan_calculator.php">Loan Calculator</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-money-bill-wave"></i> Repayments</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/view_repayments.php">View Repayments</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>repayments/add_bulk_repayments.php">Bulk Repayments</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-piggy-bank"></i> Savings</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/view_accounts.php">View Accounts</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/add_account.php">Add Account</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>savings/transactions.php">Transactions</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-chart-bar"></i> Investors</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/view_investors.php">View Investors</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/add_investor.php">Add Investor</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>investors/investments.php">Investments</a></li>
                        </ul>
                    </li>
                    
                    <li><a href="<?php echo $base_path ?? ''; ?>collateral/collateral_register.php"><i class="fas fa-shield-alt"></i> Collateral</a></li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-file-invoice"></i> Collection Sheets</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>collection_sheets/daily.php">Daily Collection</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>collection_sheets/missed.php">Missed Repayments</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-chart-pie"></i> Reports</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/collections.php">Collections Report</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/cash_flow.php">Cash Flow</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/profit_loss.php">Profit / Loss</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>reports/portfolio_risk.php">Portfolio at Risk</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-user-tie"></i> Staff</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/view_staff.php">View Staff</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/add_staff.php">Add Staff</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>staff/roles.php">Roles & Permissions</a></li>
                        </ul>
                    </li>
                    
                    <li class="has-submenu">
                        <a href="#"><i class="fas fa-cog"></i> Settings</a>
                        <ul class="submenu">
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/loan_products.php">Loan Products</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/branches.php">Branches</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/sms_settings.php">SMS Settings</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/email_settings.php">Email Settings</a></li>
                            <li><a href="<?php echo $base_path ?? ''; ?>admin/templates.php">Agreement Templates</a></li>
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
                    <input type="text" placeholder="Search borrowers, loans..." class="form-control">
                </div>
                <div class="navbar-actions">
                    <div class="nav-icon" title="Notifications">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="nav-icon" title="Messages">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="dropdown">
                        <div class="nav-icon dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>admin/profile.php"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>admin/settings.php"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo $base_path ?? ''; ?>logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            
            <div class="content-area">
