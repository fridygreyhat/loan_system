-- =====================================================================
-- Loan Store: schema reconciliation
--
-- Brings a database built from loandisk_postgresql.sql + schema_updates.sql
-- in line with what the application code actually uses. Every statement is
-- idempotent, so it is safe to run on a fresh database or re-run on an
-- existing one.
--
-- Load order:
--   1. loandisk_postgresql.sql
--   2. schema_updates.sql
--   3. schema_reconcile.sql   (this file)
-- =====================================================================

-- ---------------------------------------------------------------------
-- staff: authentication + contact columns
-- ---------------------------------------------------------------------
ALTER TABLE staff ADD COLUMN IF NOT EXISTS password VARCHAR(255);
ALTER TABLE staff ADD COLUMN IF NOT EXISTS status VARCHAR(20) NOT NULL DEFAULT 'active';
ALTER TABLE staff ADD COLUMN IF NOT EXISTS reset_token VARCHAR(100);
ALTER TABLE staff ADD COLUMN IF NOT EXISTS reset_expires TIMESTAMP;
ALTER TABLE staff ADD COLUMN IF NOT EXISTS is_super_admin BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE staff ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE staff ALTER COLUMN branch_id SET DEFAULT 1;
ALTER TABLE staff ALTER COLUMN role_id SET DEFAULT 7;
CREATE UNIQUE INDEX IF NOT EXISTS staff_email_unique ON staff (LOWER(email));

-- branches: lending limits and capital shown/edited in admin/branches.php
ALTER TABLE branches ADD COLUMN IF NOT EXISTS capital NUMERIC(15,2) NOT NULL DEFAULT 0;
ALTER TABLE branches ADD COLUMN IF NOT EXISTS min_loan_amount NUMERIC(15,2) NOT NULL DEFAULT 0;
ALTER TABLE branches ADD COLUMN IF NOT EXISTS max_loan_amount NUMERIC(15,2) NOT NULL DEFAULT 0;
ALTER TABLE branches ADD COLUMN IF NOT EXISTS min_interest_rate NUMERIC(8,2) NOT NULL DEFAULT 0;
ALTER TABLE branches ADD COLUMN IF NOT EXISTS max_interest_rate NUMERIC(8,2) NOT NULL DEFAULT 0;

-- email_settings: encryption + automatic email triggers used by admin/email_settings.php
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS encryption VARCHAR(10) NOT NULL DEFAULT 'tls';
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_loan_approved BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_payment_received BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_payment_reminder BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_overdue_notice BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_loan_statement BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE email_settings ADD COLUMN IF NOT EXISTS email_birthday BOOLEAN NOT NULL DEFAULT FALSE;

-- communication_logs: link SMS/email to the loan they were about
ALTER TABLE communication_logs ADD COLUMN IF NOT EXISTS loan_id INTEGER;

-- ---------------------------------------------------------------------
-- borrower: group membership
-- ---------------------------------------------------------------------
ALTER TABLE borrower ADD COLUMN IF NOT EXISTS group_id INTEGER;

-- ---------------------------------------------------------------------
-- loans: money as NUMERIC, real dates, assignment columns
-- ---------------------------------------------------------------------
DO $$
BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.columns
               WHERE table_name = 'loans' AND column_name = 'principal_amt') THEN
        ALTER TABLE loans RENAME COLUMN principal_amt TO principal_amount;
    END IF;
END $$;
ALTER TABLE loans ALTER COLUMN principal_amount TYPE NUMERIC(15,2);
ALTER TABLE loans ALTER COLUMN loan_interest TYPE NUMERIC(8,2);

DO $$
BEGIN
    IF (SELECT data_type FROM information_schema.columns
        WHERE table_name = 'loans' AND column_name = 'release_date') <> 'date' THEN
        ALTER TABLE loans ALTER COLUMN release_date DROP DEFAULT;
        ALTER TABLE loans ALTER COLUMN release_date TYPE DATE
            USING NULLIF(TRIM(release_date), '')::DATE;
    END IF;
END $$;

ALTER TABLE loans ADD COLUMN IF NOT EXISTS maturity_date DATE;
ALTER TABLE loans ADD COLUMN IF NOT EXISTS collector_id INTEGER;
ALTER TABLE loans ADD COLUMN IF NOT EXISTS loan_officer_id INTEGER;

-- Normalise legacy status values to: pending, approved, denied, paid, defaulted
UPDATE loans SET status = 'pending'  WHERE status IN ('pending_approval', 'processing') OR status IS NULL;
UPDATE loans SET status = 'approved' WHERE status IN ('active', 'open');

-- Back-fill maturity dates from release date + duration
UPDATE loans SET maturity_date = release_date + CASE
        WHEN LOWER(loan_duration_pd) LIKE 'day%'   THEN make_interval(days   => COALESCE(loan_duration, 0))
        WHEN LOWER(loan_duration_pd) LIKE 'week%'  THEN make_interval(weeks  => COALESCE(loan_duration, 0))
        WHEN LOWER(loan_duration_pd) LIKE 'year%'  THEN make_interval(years  => COALESCE(loan_duration, 0))
        ELSE make_interval(months => COALESCE(loan_duration, 0))
    END
WHERE maturity_date IS NULL AND release_date IS NOT NULL;

-- loan_charges: charge_mtd is 'fixed' (charge_amount in TZS) or
-- 'percentage' (charge_amount is a % of principal).
ALTER TABLE loan_charges ALTER COLUMN charge_amount TYPE NUMERIC(15,2);
ALTER TABLE loan_charges ALTER COLUMN charge_mtd SET DEFAULT 'fixed';
ALTER TABLE loan_charges ALTER COLUMN creation_user SET DEFAULT 0;
ALTER TABLE loan_applied_charges ALTER COLUMN amount TYPE NUMERIC(15,2);
ALTER TABLE loan_applied_charges ALTER COLUMN status SET DEFAULT 'applied';
ALTER TABLE loan_applied_charges ALTER COLUMN creation_user SET DEFAULT 0;

-- Loan product limits: allow fractional rates (2.5%) and amounts with cents.
-- The view depends on these columns, so drop it, alter, and recreate it below.
DROP VIEW IF EXISTS loan_product_fulldetails;
ALTER TABLE loan_product_interest ALTER COLUMN mini_interest TYPE NUMERIC(8,2);
ALTER TABLE loan_product_interest ALTER COLUMN default_interest TYPE NUMERIC(8,2);
ALTER TABLE loan_product_interest ALTER COLUMN max_interest TYPE NUMERIC(8,2);
ALTER TABLE principal_amount ALTER COLUMN mini TYPE NUMERIC(15,2);
ALTER TABLE principal_amount ALTER COLUMN default_amount TYPE NUMERIC(15,2);
ALTER TABLE principal_amount ALTER COLUMN max TYPE NUMERIC(15,2);
CREATE OR REPLACE VIEW loan_product_fulldetails AS
SELECT 
    lp.id,
    lp.name,
    lp.decimal_places,
    lp.repayment_order,
    lp.creation_user,
    lp.creation_date,
    lp.disbursement_mtds,
    ld.min_duration,
    ld.min_duration_rate AS mini_duration_pd,
    ld.default_duration,
    ld.default_duration_rate AS default_duration_pd,
    ld.max_duration,
    ld.max_duration_rate AS max_duration_pd,
    lpi.method AS interest_mtd,
    lpi.mini_interest,
    lpi.mini_interest_rate AS mini_interest_pd,
    lpi.default_interest,
    lpi.default_interest_date AS default_interest_pd,
    lpi.max_interest,
    lpi.max_interest_rate AS max_interest_pd,
    lr.repayment_cycle,
    lr.mini_no_repayments,
    lr.default_no_repayments,
    lr.max_no_repayments,
    pa.mini AS minimum_principal,
    pa.default_amount AS default_principal_amount,
    pa.max AS max_principal_amount
FROM loan_product lp
LEFT JOIN loan_duration ld ON lp.id = ld.loan_product_id
LEFT JOIN loan_product_interest lpi ON lp.id = lpi.loan_product_id
LEFT JOIN loan_repayments lr ON lp.id = lr.loan_product_id
LEFT JOIN principal_amount pa ON lp.id = pa.loan_product_id;

-- ---------------------------------------------------------------------
-- loan_repayment: actual payments received against a loan
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS loan_repayment (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER NOT NULL REFERENCES loans(id) ON DELETE CASCADE,
    amount NUMERIC(15,2) NOT NULL DEFAULT 0,
    principal NUMERIC(15,2) NOT NULL DEFAULT 0,
    interest NUMERIC(15,2) NOT NULL DEFAULT 0,
    fees NUMERIC(15,2) NOT NULL DEFAULT 0,
    penalty NUMERIC(15,2) NOT NULL DEFAULT 0,
    paid_date DATE NOT NULL DEFAULT CURRENT_DATE,
    due_date DATE,
    payment_method VARCHAR(50) DEFAULT 'Cash',
    receipt_no VARCHAR(100),
    notes TEXT,
    status VARCHAR(20),               -- NULL/pending = awaiting approval
    created_by INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS loan_repayment_loan_idx ON loan_repayment (loan_id);
CREATE INDEX IF NOT EXISTS loan_repayment_paid_date_idx ON loan_repayment (paid_date);

-- loan_schedule: expected instalments
CREATE TABLE IF NOT EXISTS loan_schedule (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER NOT NULL REFERENCES loans(id) ON DELETE CASCADE,
    installment_no INTEGER NOT NULL DEFAULT 1,
    due_date DATE NOT NULL,
    principal_due NUMERIC(15,2) NOT NULL DEFAULT 0,
    interest_due NUMERIC(15,2) NOT NULL DEFAULT 0,
    amount_due NUMERIC(15,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS loan_schedule_loan_idx ON loan_schedule (loan_id);
CREATE INDEX IF NOT EXISTS loan_schedule_due_idx ON loan_schedule (due_date);

CREATE TABLE IF NOT EXISTS loan_guarantors (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER REFERENCES loans(id) ON DELETE CASCADE,
    name VARCHAR(150) NOT NULL,
    phone VARCHAR(50),
    email VARCHAR(100),
    address VARCHAR(255),
    id_number VARCHAR(50),
    relationship VARCHAR(50),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS loan_comments (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER REFERENCES loans(id) ON DELETE CASCADE,
    staff_id INTEGER,
    comment TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Collateral
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS collateral (
    id SERIAL PRIMARY KEY,
    borrower_id INTEGER,
    loan_id INTEGER,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(100),
    value NUMERIC(15,2) NOT NULL DEFAULT 0,
    serial_number VARCHAR(100),
    description TEXT,
    status VARCHAR(30) NOT NULL DEFAULT 'held',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Borrower groups
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS borrower_groups (
    id SERIAL PRIMARY KEY,
    group_name VARCHAR(150) NOT NULL,
    leader_name VARCHAR(150),
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Expenses
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS vendors (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(50),
    address VARCHAR(255),
    contact_person VARCHAR(150),
    notes TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS expense_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);
INSERT INTO expense_types (name) VALUES
    ('Rent'), ('Utilities'), ('Salaries'), ('Office Supplies'), ('Transport'),
    ('Communication'), ('Marketing'), ('Bank Charges'), ('Repairs & Maintenance'), ('Other')
ON CONFLICT (name) DO NOTHING;

CREATE TABLE IF NOT EXISTS expenses (
    id SERIAL PRIMARY KEY,
    vendor_id INTEGER REFERENCES vendors(id) ON DELETE SET NULL,
    expense_type_id INTEGER REFERENCES expense_types(id) ON DELETE SET NULL,
    amount NUMERIC(15,2) NOT NULL DEFAULT 0,
    expense_date DATE NOT NULL DEFAULT CURRENT_DATE,
    description TEXT,
    created_by INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Other income
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS other_income_customers (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(50),
    address VARCHAR(255),
    contact_person VARCHAR(150),
    notes TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS other_income_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
);
INSERT INTO other_income_types (name) VALUES
    ('Registration Fees'), ('Processing Fees'), ('Penalty Income'),
    ('Consultancy'), ('Rental Income'), ('Other')
ON CONFLICT (name) DO NOTHING;

CREATE TABLE IF NOT EXISTS other_income (
    id SERIAL PRIMARY KEY,
    customer_id INTEGER REFERENCES other_income_customers(id) ON DELETE SET NULL,
    income_type_id INTEGER REFERENCES other_income_types(id) ON DELETE SET NULL,
    amount NUMERIC(15,2) NOT NULL DEFAULT 0,
    income_date DATE NOT NULL DEFAULT CURRENT_DATE,
    description TEXT,
    created_by INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Payroll
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS payroll (
    id SERIAL PRIMARY KEY,
    staff_id INTEGER NOT NULL REFERENCES staff(id) ON DELETE CASCADE,
    pay_period VARCHAR(7) NOT NULL,          -- YYYY-MM
    pay_date DATE NOT NULL DEFAULT CURRENT_DATE,
    basic_salary NUMERIC(15,2) NOT NULL DEFAULT 0,
    allowances NUMERIC(15,2) NOT NULL DEFAULT 0,
    overtime NUMERIC(15,2) NOT NULL DEFAULT 0,
    bonus NUMERIC(15,2) NOT NULL DEFAULT 0,
    tax NUMERIC(15,2) NOT NULL DEFAULT 0,
    social_security NUMERIC(15,2) NOT NULL DEFAULT 0,
    deductions NUMERIC(15,2) NOT NULL DEFAULT 0,
    net_pay NUMERIC(15,2) NOT NULL DEFAULT 0,
    payment_method VARCHAR(50) DEFAULT 'Bank Transfer',
    notes TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Savings: cash safe + fees
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cash_safe (
    id SERIAL PRIMARY KEY,
    type VARCHAR(10) NOT NULL CHECK (type IN ('in', 'out')),
    amount NUMERIC(15,2) NOT NULL DEFAULT 0,
    description TEXT,
    created_by INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS savings_fees (
    id SERIAL PRIMARY KEY,
    account_id INTEGER REFERENCES savings_accounts(id) ON DELETE CASCADE,
    fee_type VARCHAR(100) NOT NULL,
    amount NUMERIC(15,2) NOT NULL DEFAULT 0,
    fee_date DATE NOT NULL DEFAULT CURRENT_DATE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Investors
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS investor_products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    interest_rate NUMERIC(8,2) NOT NULL DEFAULT 0,
    interest_frequency VARCHAR(30) NOT NULL DEFAULT 'monthly',
    min_balance NUMERIC(15,2) NOT NULL DEFAULT 0,
    description TEXT,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Company assets
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS assets (
    id SERIAL PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    type VARCHAR(50),
    purchase_date DATE,
    purchase_price NUMERIC(15,2) NOT NULL DEFAULT 0,
    current_value NUMERIC(15,2) NOT NULL DEFAULT 0,
    depreciation NUMERIC(15,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- E-signature
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS e_signature_documents (
    id SERIAL PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    borrower_id INTEGER,
    borrower VARCHAR(200),
    loan_id INTEGER,
    file_path VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    sent_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    signed_date TIMESTAMP,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ---------------------------------------------------------------------
-- Accounting
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS chart_of_accounts (
    id SERIAL PRIMARY KEY,
    account_code VARCHAR(20) NOT NULL UNIQUE,
    account_name VARCHAR(150) NOT NULL,
    account_type VARCHAR(20) NOT NULL,       -- Asset, Liability, Equity, Revenue, Expense
    parent_id INTEGER REFERENCES chart_of_accounts(id),
    status VARCHAR(20) NOT NULL DEFAULT 'active'
);
INSERT INTO chart_of_accounts (account_code, account_name, account_type) VALUES
    ('1000', 'Cash & Bank', 'Asset'),
    ('1100', 'Loans Receivable', 'Asset'),
    ('1200', 'Accounts Receivable', 'Asset'),
    ('1300', 'Prepaid Expenses', 'Asset'),
    ('1500', 'Property & Equipment', 'Asset'),
    ('2000', 'Accounts Payable', 'Liability'),
    ('2100', 'Savings Deposits', 'Liability'),
    ('2200', 'Investor Funds', 'Liability'),
    ('3000', 'Owner''s Equity', 'Equity'),
    ('3100', 'Retained Earnings', 'Equity'),
    ('4000', 'Interest Income', 'Revenue'),
    ('4100', 'Fee Income', 'Revenue'),
    ('4200', 'Other Income', 'Revenue'),
    ('5000', 'Salaries & Wages', 'Expense'),
    ('5100', 'Rent', 'Expense'),
    ('5200', 'Utilities', 'Expense'),
    ('5300', 'Office Expenses', 'Expense'),
    ('5400', 'Loan Loss Provision', 'Expense')
ON CONFLICT (account_code) DO NOTHING;

CREATE TABLE IF NOT EXISTS journal_entries (
    id SERIAL PRIMARY KEY,
    entry_date DATE NOT NULL DEFAULT CURRENT_DATE,
    account_id INTEGER REFERENCES chart_of_accounts(id),
    description TEXT,
    reference VARCHAR(100),
    debit NUMERIC(15,2) NOT NULL DEFAULT 0,
    credit NUMERIC(15,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'posted',
    created_by INTEGER,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS journal_entries_date_idx ON journal_entries (entry_date);

-- ---------------------------------------------------------------------
-- System settings (single row)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS system_settings (
    id SERIAL PRIMARY KEY,
    company_name VARCHAR(150) NOT NULL DEFAULT 'Loan Store',
    company_email VARCHAR(100),
    company_phone VARCHAR(50),
    company_address TEXT,
    logo VARCHAR(255),
    currency VARCHAR(10) NOT NULL DEFAULT 'TZS',
    date_format VARCHAR(20) NOT NULL DEFAULT 'd/m/Y',
    number_format VARCHAR(20) NOT NULL DEFAULT '1,234.56',
    timezone VARCHAR(50) NOT NULL DEFAULT 'Africa/Dar_es_Salaam',
    fiscal_year_start VARCHAR(2) NOT NULL DEFAULT '1',        -- month number: 1, 4, 7 or 10
    loan_number_format VARCHAR(50) NOT NULL DEFAULT 'LN-{YEAR}-{NUMBER}',
    borrower_number_format VARCHAR(50) NOT NULL DEFAULT 'BR-{NUMBER}',
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO system_settings (company_name)
SELECT 'Loan Store' WHERE NOT EXISTS (SELECT 1 FROM system_settings);
ALTER TABLE system_settings ALTER COLUMN fiscal_year_start SET DEFAULT '1';
UPDATE system_settings SET fiscal_year_start = LTRIM(fiscal_year_start, '0') WHERE fiscal_year_start LIKE '0%';
