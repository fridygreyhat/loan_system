-- LoanDisk Modern Schema Updates
-- Enhanced tables for savings, investors, staff roles, SMS/Email, and templates

-- Enhanced staff roles with more permissions
CREATE TABLE IF NOT EXISTS staff_role_types (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO staff_role_types (name, code, description, is_system) VALUES
('Administrator', 'admin', 'Full system access', TRUE),
('Branch Manager', 'branch_manager', 'Manage branch operations', TRUE),
('Operations Manager', 'operations_manager', 'Manage daily operations', TRUE),
('Cashier', 'cashier', 'Handle cash transactions', TRUE),
('Teller', 'teller', 'Process deposits and withdrawals', TRUE),
('Collector', 'collector', 'Collect loan repayments', TRUE),
('Loan Officer', 'loan_officer', 'Process loan applications', TRUE)
ON CONFLICT (code) DO NOTHING;

-- Enhanced permissions table
CREATE TABLE IF NOT EXISTS permissions (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(100) UNIQUE NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT
);

INSERT INTO permissions (name, code, module, description) VALUES
('View Borrowers', 'borrowers.view', 'borrowers', 'Can view borrower list'),
('Add Borrowers', 'borrowers.add', 'borrowers', 'Can add new borrowers'),
('Edit Borrowers', 'borrowers.edit', 'borrowers', 'Can edit borrower details'),
('Delete Borrowers', 'borrowers.delete', 'borrowers', 'Can delete borrowers'),
('View Loans', 'loans.view', 'loans', 'Can view loan list'),
('Add Loans', 'loans.add', 'loans', 'Can create new loans'),
('Approve Loans', 'loans.approve', 'loans', 'Can approve loan applications'),
('Disburse Loans', 'loans.disburse', 'loans', 'Can disburse approved loans'),
('View Repayments', 'repayments.view', 'repayments', 'Can view repayments'),
('Add Repayments', 'repayments.add', 'repayments', 'Can record repayments'),
('View Savings', 'savings.view', 'savings', 'Can view savings accounts'),
('Manage Savings', 'savings.manage', 'savings', 'Can manage savings transactions'),
('View Investors', 'investors.view', 'investors', 'Can view investor accounts'),
('Manage Investors', 'investors.manage', 'investors', 'Can manage investor accounts'),
('View Reports', 'reports.view', 'reports', 'Can view reports'),
('Export Reports', 'reports.export', 'reports', 'Can export reports'),
('Send SMS', 'communications.sms', 'communications', 'Can send SMS messages'),
('Send Email', 'communications.email', 'communications', 'Can send emails'),
('View Settings', 'settings.view', 'settings', 'Can view system settings'),
('Manage Settings', 'settings.manage', 'settings', 'Can modify system settings'),
('Manage Staff', 'staff.manage', 'staff', 'Can manage staff accounts')
ON CONFLICT (code) DO NOTHING;

-- Role permissions mapping
CREATE TABLE IF NOT EXISTS role_permissions (
    id SERIAL PRIMARY KEY,
    role_id INTEGER NOT NULL,
    permission_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(role_id, permission_id)
);

-- Savings products
CREATE TABLE IF NOT EXISTS savings_products (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_period VARCHAR(20) DEFAULT 'yearly',
    min_balance DECIMAL(15,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    active BOOLEAN DEFAULT TRUE
);

-- Savings accounts
CREATE TABLE IF NOT EXISTS savings_accounts (
    id SERIAL PRIMARY KEY,
    borrower_id INTEGER NOT NULL,
    product_id INTEGER REFERENCES savings_products(id),
    account_number VARCHAR(50) UNIQUE NOT NULL,
    balance DECIMAL(15,2) DEFAULT 0,
    interest_earned DECIMAL(15,2) DEFAULT 0,
    status VARCHAR(20) DEFAULT 'active',
    opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closed_at TIMESTAMP
);

-- Savings transactions
CREATE TABLE IF NOT EXISTS savings_transactions (
    id SERIAL PRIMARY KEY,
    account_id INTEGER REFERENCES savings_accounts(id),
    type VARCHAR(20) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    description TEXT,
    reference VARCHAR(100),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Investors
CREATE TABLE IF NOT EXISTS investors (
    id SERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(50),
    address TEXT,
    id_number VARCHAR(50),
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Investor accounts
CREATE TABLE IF NOT EXISTS investor_accounts (
    id SERIAL PRIMARY KEY,
    investor_id INTEGER REFERENCES investors(id),
    account_number VARCHAR(50) UNIQUE NOT NULL,
    investment_amount DECIMAL(15,2) DEFAULT 0,
    interest_rate DECIMAL(5,2) DEFAULT 0,
    interest_earned DECIMAL(15,2) DEFAULT 0,
    maturity_date DATE,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Investor transactions
CREATE TABLE IF NOT EXISTS investor_transactions (
    id SERIAL PRIMARY KEY,
    account_id INTEGER REFERENCES investor_accounts(id),
    type VARCHAR(20) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    balance_after DECIMAL(15,2) NOT NULL,
    description TEXT,
    reference VARCHAR(100),
    created_by INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SMS settings
CREATE TABLE IF NOT EXISTS sms_settings (
    id SERIAL PRIMARY KEY,
    provider VARCHAR(50) NOT NULL,
    api_key VARCHAR(255),
    api_secret VARCHAR(255),
    sender_id VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SMS templates
CREATE TABLE IF NOT EXISTS sms_templates (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    placeholders TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO sms_templates (name, type, message, placeholders) VALUES
('Payment Reminder', 'reminder', 'Dear {borrower_name}, your loan payment of {amount} is due on {due_date}. Please pay on time.', 'borrower_name,amount,due_date'),
('Payment Confirmation', 'confirmation', 'Dear {borrower_name}, we received your payment of {amount}. New balance: {balance}. Thank you!', 'borrower_name,amount,balance'),
('Overdue Notice', 'overdue', 'Dear {borrower_name}, your loan payment of {amount} is overdue by {days} days. Please pay immediately.', 'borrower_name,amount,days'),
('Loan Approved', 'approval', 'Dear {borrower_name}, congratulations! Your loan of {amount} has been approved.', 'borrower_name,amount')
ON CONFLICT DO NOTHING;

-- Email settings
CREATE TABLE IF NOT EXISTS email_settings (
    id SERIAL PRIMARY KEY,
    smtp_host VARCHAR(255),
    smtp_port INTEGER DEFAULT 587,
    smtp_user VARCHAR(255),
    smtp_password VARCHAR(255),
    from_email VARCHAR(255),
    from_name VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Email templates
CREATE TABLE IF NOT EXISTS email_templates (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    placeholders TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Loan agreement templates (DOCX)
CREATE TABLE IF NOT EXISTS agreement_templates (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    placeholders TEXT,
    is_default BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Branches
CREATE TABLE IF NOT EXISTS branches (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE,
    address TEXT,
    phone VARCHAR(50),
    email VARCHAR(100),
    manager_id INTEGER,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO branches (name, code, address) VALUES
('Main Branch', 'HQ', 'Head Office')
ON CONFLICT (code) DO NOTHING;

-- SMS/Email logs
CREATE TABLE IF NOT EXISTS communication_logs (
    id SERIAL PRIMARY KEY,
    type VARCHAR(20) NOT NULL,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    error_message TEXT,
    sent_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Scheduled reminders
CREATE TABLE IF NOT EXISTS scheduled_reminders (
    id SERIAL PRIMARY KEY,
    loan_id INTEGER,
    type VARCHAR(20) NOT NULL,
    channel VARCHAR(20) NOT NULL,
    scheduled_for TIMESTAMP NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    sent_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
