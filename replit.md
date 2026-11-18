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

### 8. Calendar View
- View due amounts, disbursed amounts, and payments in calendar format
- Monthly navigation with previous/next buttons
- Color-coded entries (red = due, blue = disbursed, green = collected)
- Today indicator

### 9. Charts & Analytics
- Loan Releases vs Collections line chart
- Loan Status pie/doughnut chart
- Monthly Disbursements bar chart
- Monthly Collections bar chart
- Outstanding Portfolio area chart
- Recovery Rate bar chart with color-coded performance

### 10. Asset Management
- Add and manage company assets
- Track purchase price, current value, and depreciation
- Asset types: Equipment, Furniture, Vehicle, Property
- Asset register with search and filter

### 11. E-Signature
- Send documents for electronic signature
- Track document status (Pending, Signed, Expired)
- Document templates (Loan Agreement, Collateral Agreement, Guarantor Form)
- Resend capability for pending documents

### 12. Complete Loans Menu (Matching LoanDisk)
- View All Loans
- Add Loan
- Due Loans
- Missed Repayments
- Loans in Arrears
- No Repayments
- Past Maturity Date
- Principal Outstanding
- 1 Month Late Loans
- 3 Months Late Loans
- Loan Calculator
- Guarantors
- Loan Comments
- Approve Loans

## Database
PostgreSQL database with comprehensive schema including:
- Core tables: borrower, loans, loan_product, staff, etc.
- New tables: savings_accounts, savings_transactions, investors, investor_accounts
- Support tables: sms_templates, email_templates, agreement_templates
- Role system: staff_role_types, permissions, role_permissions

## Setting Up the Database
Load the schema files in this order (all are safe to re-run):
```bash
psql "$DATABASE_URL" -f SQLDB/loandisk_postgresql.sql
psql "$DATABASE_URL" -f SQLDB/schema_updates.sql
psql "$DATABASE_URL" -f SQLDB/schema_reconcile.sql   # brings the schema in line with the code
```
`schema_reconcile.sql` is idempotent, so also run it on an existing database after pulling changes.

Create (or reset) the super administrator. The password is read from the
environment or prompted for, so it is never stored in the repo:
```bash
SUPER_ADMIN_PASSWORD='...' php SQLDB/create_super_admin.php admin@example.com
```
Without `DATABASE_URL` the app connects to `postgres@localhost:5432/loandisk`.

## Running the Application
```bash
php -S 0.0.0.0:5000 router.php
```
`router.php` stops the built-in server from serving internal folders (`SQLDB/`,
`helpers/`, `.git/`, …) or executing anything under `uploads/`. On macOS port
5000 is usually taken by AirPlay Receiver; use another port locally.

## Security Notes
- Every page requires login (`helpers/auth.php`, loaded via `connect.php`,
  `lang.php` and `DbAcess.php`). New self-registrations stay `pending` until an
  administrator activates them in Staff.
- All POST requests need a CSRF token. It is injected automatically into every
  `<form method="post">`; JavaScript should send the `csrf-token` meta value as
  the `X-CSRF-Token` header.
- File uploads must go through `safe_upload()` in `helpers/upload.php`.
- SMS is only sent when it is switched on in Admin > SMS Settings.

## Loan Lifecycle
`pending` → (approve) `approved` → `paid` / `defaulted`; or `denied`.
Approving (or creating an already-approved loan, or editing one) generates the
instalment schedule in `loan_schedule` (`helpers/loan_utils.php`, same maths as
the Loan Calculator). Collection sheets, the calendar, reminders and cash-flow
projections are driven by that schedule. A loan is marked `paid` once
repayments cover the scheduled total.

## Entry Points
- `/login.php` - Modern login page
- `/home/dashboard.php` - Main dashboard with charts
- `/admin/index.php` - Admin panel
- `/admin/general_settings.php` - General Settings (currency, date format, logo)
- `/admin/branches.php` - Branch Management
- `/admin/email_settings.php` - Email/SMTP Configuration
- `/savings/view_accounts.php` - Savings management
- `/investors/view_investors.php` - Investor management
- `/investors/products.php` - Investor Products
- `/payroll/view_payroll.php` - Payroll Management
- `/payroll/add_payroll.php` - Add Staff Payroll
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
- `/reports/cash_flow_projections.php` - Cash Flow Projections
- `/calendar/view_calendar.php` - Calendar View
- `/charts/view_charts.php` - Charts & Analytics
- `/assets/view_assets.php` - Asset Management
- `/e_signature/view_documents.php` - E-Signature

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
- **NEW: Payroll Module** - Staff salary management with payslips
- **NEW: Branches Management** - Multi-branch support with capital tracking
- **NEW: General Settings** - Company info, currency, date format, timezone
- **NEW: Email Settings** - SMTP configuration and auto email triggers
- **NEW: Investor Products** - Product types for investor accounts
- **NEW: Cash Flow Projections** - Financial forecasting with Chart.js

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
- **Calendar** - Full translation
- **Charts & Analytics** - Full translation
- **Asset Management** - Full translation
- **E-Signature** - Full translation
- **Payroll** - Full translation
- **Branches** - Full translation
- **General Settings** - Full translation
- **Email Settings** - Full translation
- **Investor Products** - Full translation
- **Cash Flow Projections** - Full translation
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
