
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Uwezo Admin</title>
        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../dist/css/AdminLTE.css">
        <link rel="stylesheet" href="../dist/css/skins/skin-blue.min.css">

        <link rel="stylesheet" href="../plugins/datatables/dataTables.bootstrap.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.2.1/css/select.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/scroller/1.4.2/css/scroller.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">

        
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <link rel="stylesheet" href="../css/style.css">
        <script src="../plugins/jQuery/jQuery-2.1.4.min.js"></script>
        <link type="text/css" rel="stylesheet" href="../tab_assets/jquery.pwstabs.css">
        <script src="../tab_assets/jquery.pwstabs.min.js"></script>
        <script>
            jQuery(document).ready(function($){
               $('.loan_tabs').pwstabs({
                   effect: 'none',
                   defaultTab: 1,
                   containerWidth: '100%', 
                   responsive: false,
                   theme: 'pws_theme_grey'
               })   
            });
        </script>       
        <link rel="stylesheet" type="text/css" href="../css/jquery.datepick.css"> 
        <script type="text/javascript" src="../include/js/jquery.plugin.js"></script> 
        <script type="text/javascript" src="../include/js/jquery.datepick.js"></script>
        <script type="text/javascript" src="../include/js/numbers.decimals.validation.js"></script>    
    </head>

    <body class="hold-transition skin-blue sidebar-mini" onload="doOnLoad();">    
        <div class="wrapper">
            <!-- Main Header -->

             <?php include '../main_menu.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header"><h1>Admin</h1>
                </section>

                <!-- Main content -->
                <section class="content">
    <div class="box box-info">
           <!-- Horizontal Form -->
        <div class="box-header with-border">
        
            <div class="row margin">
                <div class="col-sm-4">
                    <strong>Settings</strong><br>
                    <a href="account_settings.php">Account Settings</a><br>
                    <a href="format_borrower_reports.php">Format Borrower Reports</a><br>                    
                    
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <strong>Manage Staff</strong><br>
                    <a href="../staff/view_staff_account.php">Staff</a><br>
                    <a href="view_staff_roles_account.php">Staff Roles and Permissions</a><br>                    
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <strong>Loans</strong><br>
                    <a href="view_loan_products.php">Loan Products</a><br>
                    <a href="view_loan_disbursed_by.php">Loan Disbursed By</a><br>
                    <a href="view_loan_penalty_settings.php">Loan Penalty Settings</a><br>
                    <a href="view_loan_fees.php">Loan Fees</a><br>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <hr>
              <div class="row margin">
                <div class="col-sm-4">
                    <strong>Manage Branches</strong><br>
                    <a href="view_branches.php">Branches</a><br>
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <strong>Savings</strong><br>
                    <a href="view_savings_products.php">Savings Products</a><br>
                </div>
                <!-- /.col -->
                <div class="col-sm-4">
                    <strong>Expenses</strong><br>
                    <a href="view_expense_types.php">Expense Types</a><br>                    
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->
              <hr>
              <div class="row margin">
                <div class="col-sm-4">
                    <strong>Collateral</strong><br>
                    <a href="view_collateral_types.php">Collateral Types</a><br>
                </div>
                <div class="col-sm-4">
                    <strong>Payroll</strong><br>
                    <a href="view_payroll_templates.php">Payroll Templates</a><br>
                </div>
                <div class="col-sm-4">
                    <strong>Bulk Upload</strong><br>
                    <a href="bulk_upload_csv.php">Upload Borrowers from CSV file</a><br>
                </div>
            </div>
            <!-- /.row -->
            <hr>
            <div class="row margin">
                <div class="col-sm-4">
                    <strong>Other Income</strong><br>
                    <a href="view_other_income_types.php">Other Income Types</a><br>
              </div>
                <div class="col-sm-4">
                    <strong>Borrower Fields</strong><br>
                    <a href="view_borrower_fields.php">Custom Fields</a><br>
              </div>
              <div class="col-sm-4">
                    <strong>Repayments</strong><br>
                        <a href="view_loan_repayment_methods.php">Loan Repayment Methods</a><br>
                        <a href="view_collectors.php">Manage Collectors</a><br>
              </div>
           </div>
           <hr>
           <div class="row margin">
                <div class="col-sm-4">
                    <strong>SMS Settings</strong><br>
                        <a href="sms/sms_credits.php">SMS Credits</a><br>
                        <a href="sms/sender_id.php">Sender ID</a><br>
                        <a href="sms/view_sms_templates.php">SMS Templates</a><br>
                        <a href="sms/auto_send_sms_settings.php">Auto Send SMS</a><br>
                        <a href="sms/collection_sheets_sms_template.php">Collection Sheets - SMS Template</a><br>
              </div>
              <div class="col-sm-4">
                    <strong>Email Settings</strong><br>
                        <a href="email/view_staff_email_accounts.php">Email Accounts</a><br>
                        <a href="email/view_email_templates.php">Email Templates</a><br>
                        <a href="email/auto_send_email_settings.php">Auto Send Emails</a><br>
                        <a href="email/collection_sheets_email_template.php">Collection Sheets - Email Template</a><br>
						<a href="email/email_logs.php">Email Logs</a><br>

              </div>
            </div>
        </div>
    </div>

                    </section>
                </div><!-- /.content-wrapper -->
            </div><!-- ./wrapper -->

            <!-- REQUIRED JS SCRIPTS -->
        
            <!-- Bootstrap 3.3.5 -->
            <script src="../bootstrap/js/bootstrap.min.js"></script>
            <!-- AdminLTE App -->
            <script src="../dist/js/app.min.js"></script>
            
    </body>
</html>