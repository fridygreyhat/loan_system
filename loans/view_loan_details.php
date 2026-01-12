

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



            
            <header class="main-header">

            <!-- Logo -->
            <a href="../home/welcome.html" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <!-- logo for regular state and mobile devices -->
                 <span class="logo-lg"><b>Uwezo</b> Admin</span>
            </a>

            <!-- Header Navbar -->
            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                
                <div class="navbar-header">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        Left Menu
                    </a>
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                        <i class="fa fa-bars">Top Menu</i>
                    </button>
                </div>
                <!-- Navbar Right Menu -->
                <div class="collapse navbar-collapse pull-right" id="navbar-collapse">
    
                    <!-- Top Menu -->
                    <ul class="nav navbar-nav">
                            <li>     
                                <a href="../admin/index.html"><i class="fa fa-ban"></i> <span>Admin</span></a>
                            </li>  
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-link"></i> <span>Settings</span> <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">  
                                    <li>          
                                        <a href="../billing/billing.html"><i class="fa fa-circle-o"></i> Billing</a>
                                   </li>  
                                    <li>          
                                        <a href="../home/change_password.html"><i class="fa fa-circle-o"></i> Change Password</a>
                                   </li>  
                                    <li>          
                                        <a href="../index.html"><i class="fa fa-circle-o"></i> Logout</a>
                                   </li>  
                                </ul>
                            </li>  
                            <li>
                                <a href="support_login.html" target="_blank"><i class="fa fa-support"></i> <span>Help</span></a>
                            </li>  
                        </ul>                 
                </div>
                
            </nav>
            </header>





            <!-- Left side column. contains the logo and sidebar -->
            <aside class="main-sidebar">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
        
                    <!-- Sidebar user panel (optional) -->
                    <div class="user-panel">
                        <div class="pull-left image">
                            <img src="../uploads/staff_images/thumbnails/user2-160x160.jpg" class="img-circle" alt="User Image">
                        </div>
                        <div class="pull-left info">
                            <h5>Japhet Ty Aiko</h5>
                            
                            
                        </div>
                    </div>
                    <!-- Sidebar Menu -->
                    <ul class="sidebar-menu">
                            <li>     
                                <a href="../admin/change_branch.html"><i class="fa fa-eye"></i> <span>View Another Branch</span></a>
                            </li>
                            <li style="border-top: 1px solid #000;"><a href="../home/home_branch.html"><i class="fa fa-circle-o text-aqua"></i> <span>Branch #1</span></a></li>
                            <li>     
                                <a href="../home/home_branch.html"><i class="fa fa-home"></i> <span>Home Branch</span></a>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-user"></i> <span>Borrowers</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../borrowers/view_borrowers_branch.html"><i class="fa fa-circle-o"></i> View Borrowers</a>
                                   </li>
                                    <li>
                                        <a href="../borrowers/add_borrower.html"><i class="fa fa-circle-o"></i> Add Borrower</a>
                                   </li>
                                    <li>
                                        <a href="../borrowers/groups/view_borrowers_groups_branch.html"><i class="fa fa-circle-o"></i> View Borrower Groups</a>
                                   </li>
                                    <li>
                                        <a href="../borrowers/groups/add_borrowers_group.html"><i class="fa fa-circle-o"></i> Add Borrowers Group</a>
                                   </li>
                                    <li>
                                        <a href="../borrowers/send_sms_to_all_borrowers.html"><i class="fa fa-circle-o"></i> Send SMS to All Borrowers</a>
                                   </li>
                                    <li>
                                        <a href="../borrowers/send_email_to_all_borrowers.html"><i class="fa fa-circle-o"></i> Send Email to All Borrowers</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-balance-scale"></i> <span>Loans</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../loans/view_loans_branch.html"><i class="fa fa-circle-o"></i> View All Loans</a>
                                   </li>
                                    <li>
                                        <a href="../loans/missed_repayments.html"><i class="fa fa-circle-o"></i> Missed Repayments</a>
                                   </li>
                                    <li>
                                        <a href="../loans/due_loans.html"><i class="fa fa-circle-o"></i> Due Loans</a>
                                   </li>
                                    <li>
                                        <a href="../loans/view_loans_past_maturity_date_branch.html"><i class="fa fa-circle-o"></i> Past Maturity Date</a>
                                   </li>
                                    <li>
                                        <a href="../loans/view_loans_late_1month_branch.html"><i class="fa fa-circle-o"></i> 1 Month Late Loans</a>
                                   </li>
                                    <li>
                                        <a href="../loans/view_loans_late_3months_branch.html"><i class="fa fa-circle-o"></i> 3 Months Late Loans</a>
                                   </li>
                                    <li>
                                        <a href="../loans/loan_calculator.html"><i class="fa fa-circle-o"></i> Loan Calculator</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-dollar"></i> <span>Repayments</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../repayments/view_repayments_branch.html"><i class="fa fa-circle-o"></i> View Repayments</a>
                                   </li>
                                    <li>
                                        <a href="../repayments/add_bulk_repayments.html"><i class="fa fa-circle-o"></i> Add Bulk Repayments</a>
                                   </li>
                                </ul>
                            </li>
                            <li>     
                                <a href="../collateral/collateral_register.html"><i class="fa fa-list"></i> <span>Collateral Register</span></a>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-file-text-o"></i> <span>Collection Sheets</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../collection_sheets/view_daily_collection.html"><i class="fa fa-circle-o"></i> Daily Collection Sheet</a>
                                   </li>
                                    <li>
                                        <a href="../collection_sheets/view_missed_repayments_sheet.html"><i class="fa fa-circle-o"></i> Missed Repayment Sheet</a>
                                   </li>
                                    <li>
                                        <a href="../collection_sheets/view_past_maturity_date.html"><i class="fa fa-circle-o"></i> Past Maturity Date Loans</a>
                                   </li>
                                    <li>
                                        <a href="../collection_sheets/send_sms.html"><i class="fa fa-circle-o"></i> Send SMS</a>
                                   </li>
                                    <li>
                                        <a href="../collection_sheets/send_email.html"><i class="fa fa-circle-o"></i> Send Email</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-bank"></i> <span>Savings</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../savings/view_savings_branch.html"><i class="fa fa-circle-o"></i> View Savings Accounts</a>
                                   </li>
                                    <li>
                                        <a href="../savings/view_savings_transactions_branch.html"><i class="fa fa-circle-o"></i> View Savings Transactions</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-paypal"></i> <span>Payroll</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../payroll/view_payroll_branch.html"><i class="fa fa-circle-o"></i> View Payroll</a>
                                   </li>
                                    <li>
                                        <a href="../payroll/select_staff.html"><i class="fa fa-circle-o"></i> Add Payroll</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-share"></i> <span>Expenses</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../expenses/view_expenses_branch.html"><i class="fa fa-circle-o"></i> View Expenses</a>
                                   </li>
                                    <li>
                                        <a href="../expenses/add_expense.html"><i class="fa fa-circle-o"></i> Add Expense</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-plus"></i> <span>Other Income</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../other_income/view_other_income_branch.html"><i class="fa fa-circle-o"></i> View Other Income</a>
                                   </li>
                                    <li>
                                        <a href="../other_income/add_other_income.html"><i class="fa fa-circle-o"></i> Add Other Income</a>
                                   </li>
                                </ul>
                            </li>
                            <li class="treeview">     
                                <a href="#"><i class="fa fa-industry"></i> <span>Reports</span> <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">
                                    <li>
                                        <a href="../reports/view_collections_report_branch.html"><i class="fa fa-circle-o"></i> Collections Report</a>
                                   </li>
                                    <li>
                                        <a href="../reports/collector_report.html"><i class="fa fa-circle-o"></i> Collector Report (Staff)</a>
                                   </li>
                                    <li>
                                        <a href="../reports/view_cash_flow_branch.html"><i class="fa fa-circle-o"></i> Cash flow</a>
                                   </li>
                                    <li>
                                        <a href="../reports/view_cash_flow_projection_branch.html"><i class="fa fa-circle-o"></i> Cash Flow Projection</a>
                                   </li>
                                    <li>
                                        <a href="../reports/view_profit_loss_branch.html"><i class="fa fa-circle-o"></i> Profit / Loss</a>
                                   </li>
                                    <li>
                                        <a href="../reports/mfrs_ratios.html"><i class="fa fa-circle-o"></i> MFRS Ratios</a>
                                   </li>
                                    <li>
                                        <a href="../reports/portfolio_at_risk.html"><i class="fa fa-circle-o"></i> Portfolio At Risk (PAR)</a>
                                   </li>
                                    <li>
                                        <a href="../reports/all_entries.html"><i class="fa fa-circle-o"></i> All Entries</a>
                                   </li>
                                </ul>
                            </li>
                        </ul> 
                </section>
            </aside>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header"><h1>Rev.  Damus Kumi - View Loan #1000002</h1>
                </section>

                <!-- Main content -->
                <section class="content">
        <div class="box box-widget">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="user-block">
                            <img class="img-circle" src="../uploads/borrower_images/thumbnails/user8-128x128.jpg" alt="user image">
                            <span class="username">
                                Rev.  Damus Kumi
                            </span>
                            <span class="description" style="font-size:13px; color:#000000">
                    <a href="../../borrowers/groups/view_group_details.html">MAZAS</a><br>4556666<br>
                    <a href="../../borrowers/add_borrower_edit.html">Edit</a><br>
                                goods, Owner<br>Male, 19 years
                            </span>
                        </div><!-- /.user-block --><div class="btn-group-horizontal"><a type="button" class="btn bg-olive margin" href="view_loans_borrower.html">View All Loans</a><a type="button" class="btn bg-blue margin" href="../savings/view_savings_borrower.html">View Savings</a></div>         
                    </div><!-- /.col -->
                    <div class="col-sm-4">
                        <ul class="list-unstyled">
                        <li><b>Address:</b> fsaaa\</li>
                        <li><b>City:</b> dfgfhjlk</li>
                        <li><b>Province:</b> aasss</li>
                        <li><b>Zipcode:</b> 42</li>
                        
                        <a data-toggle="collapse" data-parent="#accordion" href="#viewFiles">
                            View Borrower Files
                        </a>
                        <div id="viewFiles" class="panel-collapse collapse">
                            <div class="box-body">
                                <ul  class="no-margin" style="font-size:12px; padding-left:10px">
                    <li><a href="https://x.loandisk.com/uploads/index.php?file_id=467" target="_blank">‪+27 73 819 7980‬ 20151202_234238.jpg</a></li></ul>
                            </div>
                        </div>
                        </ul>
                    </div>
                    <div class="col-sm-4">
                        <ul class="list-unstyled">
                        <li><b>Landline:</b> 45655</li>
                        <li><b>Email:</b> <a onClick="javascript:window.open('mailto:sdd@mmmm.com', 'mail');event.preventDefault()" href="mailto:sdd@mmmm.com">sdd@mmmm.com</a><div class="btn-group-horizontal">
    <a type="button" class="btn-xs bg-red" href="../borrowers/send_email_to_borrowers.html">Send Email</a></div></li>
                        <li><b>Mobile:</b> 4588888<div class="btn-group-horizontal">
<a type="button" class="btn-xs bg-red" href="../borrowers/send_sms_to_borrower.html">Send SMS</a></div></li>
                        
                        </ul>
                    </div>
                </div><!-- /.row -->
            </div> 
        </div>
            <!-- Main content -->
            <div class="box box-warning">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered table-condensed  table-hover">
                        <tr style="background-color: #FFF8F2">
                            
                            <th>Loan #</th>
                            <th><b>Released</b></th>
                            <th><b>Maturity</b></th>
                            <th><b>Repayment</b></th>
                            <th><b>Principal &#85;&#83;&#104;</b></th>
                            <th>Interest</th>
                            <th>Fees</th>
                            <th>Penalty</th>
                            <th><b>Due</b></th>
                            <th><b>Paid</b></th>
                            <th><b>Balance</b></th>
                            
                            
                            <th><b>Status</b></th>
                            
                            
                        </tr>
                        <tr>
                            <td>
                                1000002
                            </td>
                            <td>
                                04/01/2017
                            </td>
                            <td>
                                06/01/2017
                            </td>
                            <td>
                                Daily
                            </td> 
                            <td>
                                777.00<br>@1% Per Day
                            </td>
                            <td>
                                7.77
                            </td>
                            <td>
                                1.00
                            </td>
                            <td>
                                0
                            </td>                                 
                            <td><s>785.77</s><br>6,233.00
                                <br><small><a href="https://x.loandisk.com/loans/override_loan_total_due.php?loan_id=5226">Override</a></small>
                            </td>
                            <td>
                                0
                            </td>
                            <td>
                                <b>6,233.00</b>
                            </td>
                            <td>
                                <span class="label label-info">
                                    Processing
                                </span>
                            </td>
                        </tr>
                    </table>
                
                </div> 
            </div>
<div class="box no-border">
    <div class="loan_tabs">
        <div data-pws-tab="tab_repayments" data-pws-tab-name="Repayments">
            <div class="tab_content"><div class="btn-group-horizontal"><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/repayments/add_repayment.php?loan_id=5226&tab=tab_repayments">Add Repayment</a><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/loans/view_loan_details.php?loan_id=5226&repayment_show_principal_interest=1">Show Principal &amp; Interest</a></div>Search found <b>0 results</b><p style="text-align:center">No Repayments Made</p>
                </div>
            </div>
            <div data-pws-tab="tab_terms" data-pws-tab-name="Loan Terms">
                <div class="tab_content">
                <div class="row">
                    <div class="col-sm-3">
                        
            <div class="input-group-btn">
                <button type="button" class="btn btn-info dropdown-toggle margin" data-toggle="dropdown">Loan Statement
                    <span class="fa fa-caret-down"></span></button>
                <ul class="dropdown-menu" role="menu">
                
                            <li><a  href="https://x.loandisk.com/loans/view_loan_statement.php?loan_id=5226" target="_blank">Print Statement</a></li>
                
                            <li><a  href="https://x.loandisk.com/pdf_generator/www/generate_loan_statment.php?loan_id=5226" target="_blank">Download in PDF</a></li>
                
                            <li><a  href="https://x.loandisk.com/loans/download_loan_statement_csv.php?loan_id=5226" target="_blank">Download in CSV</a></li>
                
                            <li><a  href="https://x.loandisk.com/admin/format_borrower_reports.php" target="_blank">Format Report</a></li>
                        </ul>
                    </div>
                    </div>
                    <div class="col-sm-9 pull-right">
                        <div class="btn-group-horizontal"><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/loans/add_loan.php?edit_loan=1&loan_id=5226&tab=tab_terms">Edit Loan</a><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/loans/delete_loan.php?loan_id=5226" onClick="javascript:return confirm('Are you sure you want to Delete Loan?')">Delete Loan</a></div>
                    </div>
                </div>
                
            <div class="box-body no-padding">
                <table class="table table-condensed">
                    <tr>
                        <td><b>Loan Status</b></td> 
                        <td>Processing</td>
                    </tr>  
                    <tr>
                    
                        <td width="200"><b>Loan Application ID</b></td>                      
                        <td>1000002</td>
                  
                    </tr>
                    <tr>
                        <td><b>Loan Product</b></td>
                        <td>Business Loan
                        </td>             
                    </tr>
                    <tr><td colspan="2"> </td></tr>
                    <tr>
                        <td colspan="2" class="bg-navy disabled color-palette">
                            Loan Terms
                        </td>
                    </tr>
                    <tr>
                        <td><b>Disbursed By</b></td>                      
                        <td>Cash</td>
                    </tr>   
                    <tr>
                    
                        <td><b>Principal Amount</b></td>                      
                        <td>&#85;&#83;&#104;777.00</td>
                  
                    </tr>                
                    <tr>
                    
                        <td><b>Loan Release Date</b></td>                      
                        <td>04/01/2017</td>
                  
                    </tr>  
                    <tr>
                        <td><b>Loan Interest Method</b></td>                      
                        <td> Flat Rate
                        </td>             
                    </tr>
                    <tr>
                        <td><b>Loan Interest</b></td>                      
                        <td>1% Per Day (Nominal APR: 360%)
                        </td>             
                    </tr>
                    <tr>
                        <td><b>Loan Duration</b></td>                      
                        <td>1 Days
                        </td>
                    </tr>
                    <tr>
                        <td><b>Repayment Cycle</b></td>
                        <td>Daily
                        </td>
                    </tr>
                    
                    <tr>
                        <td><b>Number of Repayments</b></td>                      
                        <td>2
                        </td>
                    </tr>
                    <tr>
                        <td><b>Decimal Places</b></td>                      
                        <td>Round Off to 2 Decimal Places
                        </td>             
                    </tr>
                    <tr>
                        <td><b>Interest Start Date</b></td>
                        <td>04/01/2017</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="bg-navy disabled color-palette">Loan  Fees</td>
                    </tr><tr><td colspan="2"> </td></tr>
                    <tr>
                        <td><b>Pining</b></td>                      
                        <td>&#85;&#83;&#104;7.77 (1%) <b>(Deductable)</b></td>   
                    </tr><tr><td colspan="2"> </td></tr>
                    <tr>
                        <td><b>Bavuu</b></td>                      
                        <td>&#85;&#83;&#104;1.00</td>   
                    </tr><tr><td colspan="2"> </td></tr>
                    <tr>
                        <td><b>Housing</b></td>                      
                        <td>&#85;&#83;&#104;1.00 <b>(Deductable)</b></td>   
                    </tr>
                    <tr>
                        <td><b>Schedule</b></td>                      
                        <td>Charge Fees on the Released Date
                        </td>             
                    </tr>
                    <tr><td colspan="2"> </td></tr>
                    <tr>
                        <td colspan="2" class="bg-navy disabled color-palette">
                            System Generated Penalties
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="bg-blue disabled color-palette">Late Repayment Penalty
                        </td>
                    </tr>
                    <tr>
                        <td class="text-red" colspan="2"><b>Late Repayment Penalty is Enabled</b>
                        </td>
                    </tr>                         
                    <tr>
                        <td><b>Penalty Fixed Amount</b></td>
                        <td>
                            &#85;&#83;&#104;200.00
                        </td>
                    </tr>
                
                    <tr>
                        <td><b>Grace Period</b></td>                      
                        <td>
                        8 day(s)
                        </td>
                    </tr>
                
                    <tr>
                        <td style="width:340px"><b>Is Penalty on Late Repayments <u>Recurring</u>?</b></td>
                        <td>
                        Every 10 day(s)
                        </td>
                    </tr>
            </table> 
            <br><br>
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <td colspan="2" class="bg-blue disabled color-palette">After Maturity Date Penalty
                        </td>
                    </tr>
                    <tr>
                        <td class="text-red" colspan="2"><b>After Maturity Date Penalty is Enabled</b>
                        </td>
                    </tr>
  
                    <tr>
                        <td><b>Calculate Penalty on</b></td>
                        <td>Overdue Principal Amount
                        </td>
                    </tr>                         
                    <tr>
                        <td><b>Penalty Interest</b></td>
                        <td>
                            1.00%
                        </td>
                    </tr>
                
                    <tr>
                        <td><b>Grace Period</b></td>
                        <td>
                        7 day(s)
                        </td>
                    </tr>
             
                    <tr>
                        <td style="width:340px"><b>Is Penalty After Maturity Date <u>Recurring</u>?</b></td>
                        <td>
                        Every 13 day(s)
                        </td>
                    </tr>
                    <tr><td colspan="2"> </td></tr>
                    <tr>
                        <td colspan="2" class="bg-navy disabled color-palette">
                            Description
                        </td>
                    </tr>
                    <tr> 
                        <td colspan="2">
                            shgkjkgfdvscaZZXCVBNM/LKJHMGNBFV
                        </td>
                    </tr>  
                </tbody>  
            </table>
        </div>
                </div>
            </div>
            <div data-pws-tab="tab_schedule" data-pws-tab-name="Loan Schedule">
                <div class="tab_content">
                <div class="row">
                    <div class="col-sm-3">
                        
            <div class="input-group-btn">
                <button type="button" class="btn btn-info dropdown-toggle margin" data-toggle="dropdown">Loan Schedule
                    <span class="fa fa-caret-down"></span></button>
                <ul class="dropdown-menu" role="menu">
                
                            <li><a  href="https://x.loandisk.com/loans/view_loan_repayments_schedule.php?loan_id=5226" target="_blank">Print Schedule</a></li>
                
                            <li><a  href="https://x.loandisk.com/pdf_generator/www/generate_loan_schedule.php?loan_id=5226" target="_blank">Download in PDF</a></li>
                
                            <li><a  href="https://x.loandisk.com/loans/download_loan_schedule_csv.php?loan_id=5226" target="_blank">Download in CSV</a></li>
                
                            <li><a  href="https://x.loandisk.com/admin/format_borrower_reports.php" target="_blank">Format Report</a></li>
                        </ul>
                    </div>
                    </div>
                    <div class="col-sm-9 pull-right">
                        <div class="btn-group-horizontal"><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/loans/view_loan_repayments_schedule.php?loan_id=5226" target="_blank">Print Schedule</a><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/collection_sheets/edit_collection_sheet.php?loan_id=5226&tab=tab_schedule">Edit Schedule</a></div>
                    </div>
                </div><p>Search found <b>3 results</b></p>
                <div class="box box-success">
                    <div class="box-body table-responsive no-padding">
                        <table class="table table-bordered table-condensed table-hover">
                            <tr style="background-color: #F2F8FF">
                                <th style="width: 10px">
                                  <b>#</b>
                                </th>
                                <th>
                                    <b>Date</b>
                                </th>
                                <th>
                                    <b>Description</b>
                                </th>
                                <th style="text-align:right;">
                                    <b>Principal</b>
                                </th>
                                <th style="text-align:right;">
                                    <b>Interest</b>
                                </th>
                                <th style="text-align:right;">
                                    <b>Fees</b>
                                </th>
                                <th style="text-align:right;">
                                    <b>Penalty</b>
                                </th>
                                <th style="text-align:right;">
                                    <b>Due</b>
                                </th>
                                <th style="text-align:right;">
                                    Total Due
                                </th>
                                <th style="text-align:right;">
                                    Principal Balance Owed
                                </th>
                            </tr>
                            <tr>
                                <td>
                                    1
                                </td>
                                <td>
                                    04/01/2017
                                </td>
                                <td>
                                    Fees (Non Deductable)
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right">
                                    1.00
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right; font-weight:bold">
                                    1.00
                                </td>
                                <td style="text-align:right;">
                                    1.00
                                </td>
                                <td style="text-align:right;">
                                    777.00
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    2
                                </td>
                                <td>
                                    05/01/2017
                                </td>
                                <td>
                                    Repayment
                                </td>
                                <td style="text-align:right">
                                    388.50
                                </td>
                                <td style="text-align:right">
                                    3.88
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right; font-weight:bold">
                                    392.38
                                </td>
                                <td style="text-align:right;">
                                    393.38
                                </td>
                                <td style="text-align:right;">
                                    388.50
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    3
                                </td>
                                <td>
                                    06/01/2017
                                </td>
                                <td>
                                    Repayment
                                </td>
                                <td style="text-align:right">
                                    388.50
                                </td>
                                <td style="text-align:right">
                                    3.89
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right">
                                    0
                                </td>
                                <td style="text-align:right; font-weight:bold">
                                    392.39
                                </td>
                                <td style="text-align:right;">
                                    785.77
                                </td>
                                <td style="text-align:right;">
                                    0
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="font-weight:bold">Total Due</td>
                                <td style="text-align:right;">
                                    777.00
                                </td>
                                <td style="text-align:right;">
                                    7.77
                                </td>
                                <td style="text-align:right;">
                                    1.00
                                </td>
                                <td style="text-align:right;">
                                    0
                                </td>
                                <td style="text-align:right; font-weight:bold">
                                    785.77
                                </td>
                            </tr>
                        </table>         
                               
                </div>
            </div>  
                </div>
            </div>
            
            
            <div data-pws-tab="tab_pending_dues" data-pws-tab-name="Pending Dues">
                <div class="tab_content">
                    <p>Here you can see the pending due amounts for all-time and until today.</p>
        <div class="box box-success">
            <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-condensed table-hover">
                    <tr style="background-color: #F2F8FF">
                        <th width="200">
                            <b>Based on Loan Terms:</b>
                        </th>
                        <th style="text-align:right;">
                            <b>Principal</b>
                        </th>
                        <th style="text-align:right;">
                            <b>Interest</b>
                        </th>
                        <th style="text-align:right;">
                            <b>Fees</b>
                        </th>
                        <th style="text-align:right;">
                            <b>Penalty</b>
                        </th>
                        <th style="text-align:right;">
                            <b>Total</b>
                        </th>
                    </tr>
                    <tr>
                        <td class="text-bold bg-red">
                            Total Due
                        </td>
                        <td style="text-align:right">
                            777.00
                        </td>
                        <td style="text-align:right">
                            7.77
                        </td>
                        <td style="text-align:right">
                            1.00
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            6,233.00
                        </td>
                    </tr>
                    <tr>
                        <td class="text-bold bg-green">
                            Total Paid
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td class="text-bold bg-gray">
                            Balance
                        </td>
                        <td style="text-align:right">
                            777.00
                        </td>
                        <td style="text-align:right">
                            7.77
                        </td>
                        <td style="text-align:right">
                            1.00
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            6,233.00
                        </td>
                    </tr>
                    <tr><td colspan="6"><br><br><b>Based on Loan Schedule:</b></td></tr>
                    <tr>
                        <td class="text-bold bg-red">
                            Due till 
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td class="text-bold bg-green">
                            Paid till 
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            0
                        </td>
                    </tr>
                    <tr>
                        <td class="text-bold bg-gray">
                            Balance  till 
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right">
                            0
                        </td>
                        <td style="text-align:right; font-weight:bold">
                            0
                        </td>
                    </tr>
                </table>
            </div>
        </div>
                </div>
            </div>
            <div data-pws-tab="tab_collateral" data-pws-tab-name="Loan Collateral">
                <div class="tab_content"><div class="btn-group-horizontal"><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/collateral/add_collateral.php?loan_id=5226&tab=tab_collateral">Add Collateral</a></div><p><b>No Collateral Found</b></p>
                </div>
            </div>
            <div data-pws-tab="tab_files" data-pws-tab-name="Loan Files">
                <div class="tab_content">
                    <p>To add new loan files or remove existing files, pls click the <b>Loan Terms</b> tab and then <b>Edit Loan</b>.</p>
            <div class="box box-success">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered table-condensed table-hover">
                        <tr style="background-color: #F2F8FF">
                            <th>
                              <b>Files</b>
                            </th>
                       </tr>
                       <tr>
                            <td class="margin"><ul class="margin">
                    <li><a href="https://x.loandisk.com/uploads/index.php?file_id=468" target="_blank">‪+250 722 212 160‬ 20151204_221727.jpg</a></li></ul></td>
                        </tr>
                    </table> 
                                       
                </div>
            </div>  
                </div>
            </div>
            <div data-pws-tab="tab_comments" data-pws-tab-name="Loan Comments">
                <div class="tab_content"><div class="btn-group-horizontal"><a type="button" class="btn bg-gray margin" href="https://x.loandisk.com/loans/add_comments.php?loan_id=5226&tab=tab_comments">Add Comments</a></div><br>
                <div class="box-footer box-comments">
                        <div class="box-comment">
                            <!-- User image -->
                            <img src="https://x.loandisk.com/uploads/staff_images/thumbnails/586b8bc6e5c18.PNG" class="img-circle" alt="User Image">
                            <div class="comment-text">
                                <span class="username">
                                    Japhet Ty Aiko
                                 
                                    <span class="text-muted pull-right">
                                        03/01/2017 6:52pm
                                    </span>
                                </span><!-- /.username -->
                            DJHDVHHGDSHGJHDS
                                <span class="text-muted pull-right"><div class="btn-group-horizontal"><a type="button" class="btn bg-white btn-xs text-bold" href="https://x.loandisk.com/loans/add_comments.php?edit_comments=1&comments_id=172&tab=tab_comments">Edit</a><a type="button" class="btn bg-white btn-xs text-bold" href="https://x.loandisk.com/loans/view_loan_details.php?delete_comments=delete&comments_id=172&tab=tab_comments" onClick="javascript:return confirm('Are you sure you want to Delete?')">Delete</a></div>
                                </span>
                            </div>
                            <!-- /.comment-text -->
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>

                    </section>
                </div><!-- /.content-wrapper -->
            </div><!-- ./wrapper -->

            <!-- REQUIRED JS SCRIPTS -->
        
            <!-- Bootstrap 3.3.5 -->
            <script src="https://x.loandisk.com/bootstrap/js/bootstrap.min.js"></script>
            <!-- AdminLTE App -->
            <script src="https://x.loandisk.com/dist/js/app.min.js"></script>
            
    </body>
</html>