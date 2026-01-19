# LoanDisk - Modern Loan Management System

## Overview
LoanDisk is a comprehensive web-based loan management system built with PHP and PostgreSQL. It provides modern features for managing loans, borrowers, staff, repayments, savings accounts, investor accounts, and various administrative functions matching the latest LoanDisk platform.

## Project Structure
```
/
├── admin/           - Admin settings, loan products, SMS/Email settings, templates
├── auth/            - Authentication handling
├── borrowers/       - Borrower management pages
├── collateral/      - Collateral register
├── collection_sheets/ - Collection sheet management
├── commons/         - Shared operations
├── css/             - Stylesheets (loandisk-modern.css)
├── expenses/        - Expense tracking
├── helpers/         - PHP helper classes (DB connection, utilities)
├── home/            - Dashboard and welcome pages
├── includes/        - Shared templates (header.php, footer.php)
├── accounting/      - Accounting module (Cash Flow, P&L, Balance Sheet, etc.)
├── investors/       - Investor account management
├── loans/           - Loan management pages
├── other_income/    - Other income tracking
├── payroll/         - Payroll management
├── plugins/         - Frontend plugins (jQuery, Bootstrap, DataTables, CKEditor)
├── repayments/      - Repayment tracking
├── reports/         - Reporting pages
├── savings/         - Savings account management (ENHANCED)
├── SQLDB/           - Database schema files
├── staff/           - Staff management and roles
└── uploads/         - User uploaded files
```

## Technology Stack
- **Backend:** PHP 8.2
- **Database:** PostgreSQL (via PDO)
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **UI Framework:** Bootstrap 5.3, Custom Modern CSS
- **Charts:** Chart.js 4.x
- **Icons:** Font Awesome 6.x
- **Fonts:** Inter (Google Fonts)

## Key Features (Matching Modern LoanDisk)

### 1. Modern Dashboard with Charts
- Visual analytics showing loan releases and collections
- Real-time statistics (borrowers, loans, savings, investments)
- Quick action buttons for common tasks
- Activity feed showing recent events

### 2. Savings Accounts
- Create savings accounts for borrowers
- Deposit and withdrawal transactions
- Automatic interest calculation
- Downloadable statements

### 3. Investor Accounts
- Manage investor profiles
- Track investments and returns
- Interest calculation and payments
- Investment history tracking

### 4. Enhanced Staff Roles & Permissions
- Granular role-based access control
- Pre-defined roles: Administrator, Branch Manager, Operations Manager, Cashier, Teller, Collector, Loan Officer
- Module-level permissions (borrowers, loans, savings, reports, etc.)
- Custom role creation

### 5. Automated SMS/Email
- Multiple SMS provider support (Twilio, Africa's Talking, Nexmo, Termii)
- Customizable SMS templates with placeholders
- Auto-send for payment reminders, confirmations, overdue notices
- Email notification system

### 6. Auto-fill Loan Agreements
- Upload DOCX templates
- Placeholder system for dynamic content
- Generate filled agreements on demand
- Download as DOCX or PDF

### 7. Complete Accounting Module
- **Cash Flow Report** - Accumulated and monthly views with inflows/outflows
- **Profit & Loss Statement** - Revenue vs expenses breakdown
- **Balance Sheet** - Assets, liabilities, and equity overview
- **Trial Balance** - Account balances with debit/credit columns
- **General Ledger** - Transaction journal entries
- **Chart of Accounts** - Account code management with types (Asset, Liability, Equity, Revenue, Expense)
- **Manual Journal** - Create and post journal entries

## Database
PostgreSQL database with comprehensive schema including:
- Core tables: borrower, loans, loan_product, staff, etc.
- New tables: savings_accounts, savings_transactions, investors, investor_accounts
- Support tables: sms_templates, email_templates, agreement_templates
- Role system: staff_role_types, permissions, role_permissions

## Running the Application
```bash
php -S 0.0.0.0:5000
```

## Entry Points
- `/login.php` - Modern login page
- `/home/dashboard.php` - Main dashboard with charts
- `/admin/index.php` - Admin panel
- `/savings/view_accounts.php` - Savings management
- `/investors/view_investors.php` - Investor management
- `/staff/roles.php` - Role & permission management
- `/admin/sms_settings.php` - SMS configuration
- `/admin/templates.php` - Agreement templates
- `/accounting/cash_flow.php` - Cash Flow Report
- `/accounting/profit_loss.php` - Profit & Loss Statement
- `/accounting/balance_sheet.php` - Balance Sheet
- `/accounting/trial_balance.php` - Trial Balance
- `/accounting/general_ledger.php` - General Ledger
- `/accounting/chart_of_accounts.php` - Chart of Accounts
- `/accounting/manual_journal.php` - Manual Journal

## Recent Changes (January 2026)
- Complete UI modernization with Bootstrap 5 and custom CSS
- Added Chart.js dashboard with loan performance analytics
- Implemented savings account management
- Added investor account functionality
- Enhanced staff roles with granular permissions
- Integrated SMS/Email notification system with templates
- Added DOCX agreement template system
- Migrated database from MySQL to PostgreSQL
- Created comprehensive PostgreSQL schema with all new tables
- **Full Kiswahili (Swahili) Translation System** - Complete bilingual support

## Bilingual Support (English/Kiswahili)
The system supports full bilingual interface:
- **Translation file:** `helpers/lang.php` - Contains all translations
- **Language selector:** Globe icon in header and login page
- **Supported languages:** English (en), Kiswahili (sw)
- **How to switch:** Click globe icon > Select language

### Translated Pages:
- Login page (login.php)
- Dashboard (home/dashboard.php)
- Borrowers (borrowers/view_borrowers.php)
- Loans (loans/view_loans.php)
- Repayments (repayments/view_repayments.php)
- Savings (savings/view_accounts.php)
- Investors (investors/view_investors.php)
- Reports (reports/collections.php)
- Staff (staff/view_staff.php)
- Collateral (collateral/collateral_register.php)
- Collection Sheets (collection_sheets/daily.php)
- Settings (admin/loan_products.php)
- **Accounting Module** - All 7 pages fully translated
- All navigation menus

### Adding New Translations:
1. Add translation key to `$translations` array in `helpers/lang.php`
2. Use `<?php echo __('key_name'); ?>` in PHP files

## User Preferences
- Modern, clean UI matching loandisk.com design
- Blue color scheme with professional styling
- Responsive sidebar navigation
- Card-based layout with shadow effects
- Bilingual support (English/Kiswahili) for Tanzanian market
