-- PostgreSQL Schema for LoanDisk
-- Converted from MySQL

-- Table structure for table borrower
CREATE TABLE IF NOT EXISTS borrower (
  id SERIAL PRIMARY KEY,
  fname VARCHAR(20) NOT NULL,
  lname VARCHAR(20) NOT NULL,
  gender VARCHAR(20) NOT NULL,
  country VARCHAR(40) DEFAULT NULL,
  title VARCHAR(20) DEFAULT NULL,
  mobile_no VARCHAR(50) DEFAULT NULL,
  email VARCHAR(60) DEFAULT NULL,
  unique_no VARCHAR(50) DEFAULT NULL,
  dob DATE DEFAULT NULL,
  address VARCHAR(50) DEFAULT NULL,
  city VARCHAR(20) DEFAULT NULL,
  province_state VARCHAR(20) DEFAULT NULL,
  zipcode VARCHAR(20) DEFAULT NULL,
  landline VARCHAR(30) DEFAULT NULL,
  business_name VARCHAR(100) DEFAULT NULL,
  working_status VARCHAR(100) DEFAULT NULL,
  photo BYTEA,
  description VARCHAR(100) DEFAULT NULL,
  staff_id INTEGER DEFAULT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  branch_id INTEGER DEFAULT NULL,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL,
  field3 VARCHAR(100) DEFAULT NULL,
  field4 VARCHAR(100) DEFAULT NULL,
  field5 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table borrower_files
CREATE TABLE IF NOT EXISTS borrower_files (
  id SERIAL PRIMARY KEY,
  borrower_id INTEGER NOT NULL,
  name VARCHAR(200) DEFAULT NULL,
  description VARCHAR(200) DEFAULT NULL,
  content BYTEA,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_user INTEGER DEFAULT NULL,
  filepath TEXT
);

-- Table structure for table disbursement_methods
CREATE TABLE IF NOT EXISTS disbursement_methods (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(50) DEFAULT NULL,
  field1 VARCHAR(50) DEFAULT NULL,
  field2 VARCHAR(50) DEFAULT NULL,
  field3 VARCHAR(50) DEFAULT NULL,
  field4 VARCHAR(50) DEFAULT NULL
);

-- Table structure for table loans
CREATE TABLE IF NOT EXISTS loans (
  id SERIAL PRIMARY KEY,
  loan_product_id INTEGER NOT NULL,
  borrower_id INTEGER NOT NULL,
  loan_no VARCHAR(100) DEFAULT NULL,
  disbursement_mtd VARCHAR(100) DEFAULT NULL,
  principal_amt INTEGER DEFAULT NULL,
  release_date VARCHAR(100) DEFAULT NULL,
  interest_mtd VARCHAR(40) DEFAULT NULL,
  loan_interest INTEGER DEFAULT NULL,
  loan_interest_pd VARCHAR(30) DEFAULT NULL,
  loan_duration INTEGER DEFAULT NULL,
  loan_duration_pd VARCHAR(30) DEFAULT NULL,
  repayment_cycle VARCHAR(25) DEFAULT NULL,
  no_repayment_cycle INTEGER DEFAULT NULL,
  description VARCHAR(200) DEFAULT NULL,
  status VARCHAR(50) DEFAULT NULL,
  application_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_user INTEGER DEFAULT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_modified_by INTEGER DEFAULT NULL,
  last_modified_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL,
  field3 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table loan_applied_charges
CREATE TABLE IF NOT EXISTS loan_applied_charges (
  id SERIAL PRIMARY KEY,
  loan_id INTEGER NOT NULL,
  loan_charge_id INTEGER NOT NULL,
  amount INTEGER NOT NULL,
  creation_user INTEGER NOT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  status VARCHAR(100) NOT NULL
);

-- Table structure for table loan_charges
CREATE TABLE IF NOT EXISTS loan_charges (
  id SERIAL PRIMARY KEY,
  name VARCHAR(50) NOT NULL,
  description VARCHAR(100) DEFAULT NULL,
  charge_mtd VARCHAR(100) DEFAULT NULL,
  charge_amount INTEGER DEFAULT NULL,
  charge_rate VARCHAR(100) DEFAULT NULL,
  creation_user INTEGER DEFAULT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_modified_by INTEGER DEFAULT NULL,
  field1 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table loan_duration
CREATE TABLE IF NOT EXISTS loan_duration (
  id SERIAL PRIMARY KEY,
  loan_product_id INTEGER NOT NULL,
  min_duration INTEGER NOT NULL,
  min_duration_rate VARCHAR(100) NOT NULL,
  default_duration INTEGER NOT NULL,
  default_duration_rate VARCHAR(100) NOT NULL,
  max_duration INTEGER NOT NULL,
  max_duration_rate VARCHAR(100) NOT NULL,
  creation_user INTEGER DEFAULT NULL,
  last_modified_by INTEGER DEFAULT NULL,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table loan_product
CREATE TABLE IF NOT EXISTS loan_product (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  decimal_places INTEGER NOT NULL,
  repayment_order VARCHAR(200) NOT NULL,
  disbursement_mtds VARCHAR(200) DEFAULT NULL,
  creation_user INTEGER NOT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  active_flg INTEGER NOT NULL DEFAULT 1,
  last_modified_by INTEGER DEFAULT NULL,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table loan_product_interest
CREATE TABLE IF NOT EXISTS loan_product_interest (
  id SERIAL PRIMARY KEY,
  loan_product_id INTEGER NOT NULL,
  method VARCHAR(100) NOT NULL,
  mini_interest INTEGER NOT NULL,
  mini_interest_rate VARCHAR(100) NOT NULL,
  default_interest INTEGER NOT NULL,
  default_interest_date VARCHAR(100) NOT NULL,
  max_interest INTEGER NOT NULL,
  max_interest_rate VARCHAR(100) NOT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_user INTEGER DEFAULT NULL,
  last_modified_by INTEGER DEFAULT NULL,
  field1 VARCHAR(100) DEFAULT NULL,
  feild2 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table loan_repayments
CREATE TABLE IF NOT EXISTS loan_repayments (
  id SERIAL PRIMARY KEY,
  loan_product_id INTEGER NOT NULL,
  repayment_cycle VARCHAR(50) NOT NULL,
  mini_no_repayments INTEGER NOT NULL,
  default_no_repayments INTEGER NOT NULL,
  max_no_repayments INTEGER NOT NULL,
  creation_user INTEGER NOT NULL,
  last_modified_by INTEGER DEFAULT NULL,
  field1 VARCHAR(50) DEFAULT NULL,
  field2 VARCHAR(50) DEFAULT NULL
);

-- Table structure for table principal_amount
CREATE TABLE IF NOT EXISTS principal_amount (
  id SERIAL PRIMARY KEY,
  loan_product_id INTEGER NOT NULL,
  mini INTEGER NOT NULL,
  default_amount INTEGER NOT NULL,
  max INTEGER NOT NULL,
  field1 INTEGER DEFAULT NULL
);

-- Table structure for table staff
CREATE TABLE IF NOT EXISTS staff (
  id SERIAL PRIMARY KEY,
  branch_id INTEGER NOT NULL,
  role_id INTEGER NOT NULL,
  fname VARCHAR(100) NOT NULL,
  lname VARCHAR(100) DEFAULT NULL,
  email VARCHAR(100) DEFAULT NULL,
  gender VARCHAR(20) DEFAULT NULL,
  country VARCHAR(100) DEFAULT NULL,
  phone_no VARCHAR(100) DEFAULT NULL,
  dob DATE DEFAULT NULL,
  address VARCHAR(50) DEFAULT NULL,
  city VARCHAR(50) DEFAULT NULL,
  province_state VARCHAR(50) DEFAULT NULL,
  zipcode VARCHAR(30) DEFAULT NULL,
  landline VARCHAR(30) DEFAULT NULL,
  skype VARCHAR(30) DEFAULT NULL,
  pic TEXT,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table staff_access
CREATE TABLE IF NOT EXISTS staff_access (
  id SERIAL PRIMARY KEY,
  role_id INTEGER NOT NULL,
  actions TEXT NOT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_user INTEGER NOT NULL,
  last_modified_by INTEGER NOT NULL,
  field1 VARCHAR(100) DEFAULT NULL,
  field2 VARCHAR(100) DEFAULT NULL
);

-- Table structure for table staff_roles
CREATE TABLE IF NOT EXISTS staff_roles (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description VARCHAR(100) NOT NULL,
  creation_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  creation_user INTEGER NOT NULL,
  field1 VARCHAR(100) DEFAULT NULL
);

-- View structure for loan_product_fulldetails (joins loan product with related tables).
-- Defined after all the tables it references.
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

-- Seed data (idempotent). The login account is created separately with
-- SQLDB/create_super_admin.php so no password is ever stored in this file.
INSERT INTO staff_roles (name, description, creation_user)
SELECT 'Admin', 'System Administrator', 1
WHERE NOT EXISTS (SELECT 1 FROM staff_roles WHERE name = 'Admin');
INSERT INTO disbursement_methods (name, description)
SELECT v.name, v.description FROM (VALUES ('Cash', 'Cash disbursement'), ('Bank Transfer', 'Bank transfer disbursement')) AS v(name, description)
WHERE NOT EXISTS (SELECT 1 FROM disbursement_methods d WHERE d.name = v.name);
