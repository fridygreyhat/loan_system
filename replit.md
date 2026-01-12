# LoanDisk - Loan Management System

## Overview
LoanDisk is a web-based loan management system built with PHP and PostgreSQL. It provides comprehensive features for managing loans, borrowers, staff, repayments, and various administrative functions.

## Project Structure
- `/admin` - Administrative pages (settings, staff management, loan products)
- `/borrowers` - Borrower management pages
- `/loans` - Loan management pages
- `/repayments` - Repayment tracking pages
- `/staff` - Staff management
- `/savings` - Savings products management
- `/expenses` - Expense tracking
- `/payroll` - Payroll management
- `/collateral` - Collateral register
- `/collection_sheets` - Collection sheets
- `/reports` - Reporting pages
- `/home` - Dashboard/welcome pages
- `/helpers` - PHP helper classes (database connection, utilities)
- `/plugins` - Frontend plugins (jQuery, Bootstrap, DataTables, CKEditor, etc.)
- `/bootstrap` - Bootstrap CSS/JS framework
- `/dist` - AdminLTE theme files
- `/SQLDB` - Database schema files

## Technology Stack
- **Backend:** PHP 8.2
- **Database:** PostgreSQL (via PDO)
- **Frontend:** HTML, CSS, JavaScript
- **UI Framework:** AdminLTE, Bootstrap 3.3.5
- **Plugins:** DataTables, CKEditor, jQuery, iCheck

## Database
The application uses PostgreSQL with PDO connection. Database configuration is handled via the `DATABASE_URL` environment variable.

Tables include:
- `borrower` - Borrower information
- `loans` - Loan records
- `loan_product` - Loan product definitions
- `loan_charges` - Loan fee definitions
- `loan_repayments` - Repayment configuration
- `staff` - Staff accounts
- `staff_roles` - Staff role definitions
- `staff_access` - Role permissions
- `disbursement_methods` - Payment methods

## Running the Application
The PHP development server runs on port 5000:
```
php -S 0.0.0.0:5000
```

## Entry Points
- `/index.html` - Login page
- `/home/welcome.html` - Dashboard (after login)
- `/admin/index.php` - Admin panel

## Recent Changes
- January 2026: Migrated from MySQL to PostgreSQL
- Updated database connection files to use PDO with PostgreSQL driver
- Created PostgreSQL-compatible schema in `SQLDB/loandisk_postgresql.sql`
