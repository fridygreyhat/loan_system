

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
                            <!-- Trial -->
                            <span class="text-red text-bold">Trial ending in 14 days</span>
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
                <section class="content-header"><h1>Missed Repayments</h1>
                </section>

                <!-- Main content -->
                <section class="content"><p>Loans that have overdue repayments.</p>
        <div class="box box-success">

            <form action="" class="form-horizontal" method="get"  enctype="multipart/form-data">
                <input type="hidden" name="search_overdue_loans" value="1">
                <div class="box-body">
                    <div class="row">
                        <label for="inputOverdueDays"  class="col-sm-2 control-label">Overdue by</label>
                        <div class="col-xs-3">
                            <select class="form-control" name="overdue_days" id="inputOverdueDays">
                                <option value="1" selected>1 day</option>
                                <option value="2">2 days</option>
                                <option value="3">3 days</option>
                                <option value="4">4 days</option>
                                <option value="5">5 days</option>
                                <option value="6">6 days</option>
                                <option value="7">7 days</option>
                                <option value="8">8 days</option>
                                <option value="9">9 days</option>
                                <option value="10">10 days</option>
                                <option value="11">11 days</option>
                                <option value="12">12 days</option>
                                <option value="13">13 days</option>
                                <option value="14">14 days</option>
                                <option value="15">15 days</option>
                                <option value="16">16 days</option>
                                <option value="17">17 days</option>
                                <option value="18">18 days</option>
                                <option value="19">19 days</option>
                                <option value="20">20 days</option>
                                <option value="21">21 days</option>
                                <option value="22">22 days</option>
                                <option value="23">23 days</option>
                                <option value="24">24 days</option>
                                <option value="25">25 days</option>
                                <option value="26">26 days</option>
                                <option value="27">27 days</option>
                                <option value="28">28 days</option>
                                <option value="29">29 days</option>
                                <option value="30">30 days</option>
                                <option value="31">31 days</option>
                                <option value="32">32 days</option>
                                <option value="33">33 days</option>
                                <option value="34">34 days</option>
                                <option value="35">35 days</option>
                                <option value="36">36 days</option>
                                <option value="37">37 days</option>
                                <option value="38">38 days</option>
                                <option value="39">39 days</option>
                                <option value="40">40 days</option>
                                <option value="41">41 days</option>
                                <option value="42">42 days</option>
                                <option value="43">43 days</option>
                                <option value="44">44 days</option>
                                <option value="45">45 days</option>
                                <option value="46">46 days</option>
                                <option value="47">47 days</option>
                                <option value="48">48 days</option>
                                <option value="49">49 days</option>
                                <option value="50">50 days</option>
                                <option value="51">51 days</option>
                                <option value="52">52 days</option>
                                <option value="53">53 days</option>
                                <option value="54">54 days</option>
                                <option value="55">55 days</option>
                                <option value="56">56 days</option>
                                <option value="57">57 days</option>
                                <option value="58">58 days</option>
                                <option value="59">59 days</option>
                                <option value="60">60 days</option>
                                <option value="61">61 days</option>
                                <option value="62">62 days</option>
                                <option value="63">63 days</option>
                                <option value="64">64 days</option>
                                <option value="65">65 days</option>
                                <option value="66">66 days</option>
                                <option value="67">67 days</option>
                                <option value="68">68 days</option>
                                <option value="69">69 days</option>
                                <option value="70">70 days</option>
                                <option value="71">71 days</option>
                                <option value="72">72 days</option>
                                <option value="73">73 days</option>
                                <option value="74">74 days</option>
                                <option value="75">75 days</option>
                                <option value="76">76 days</option>
                                <option value="77">77 days</option>
                                <option value="78">78 days</option>
                                <option value="79">79 days</option>
                                <option value="80">80 days</option>
                                <option value="81">81 days</option>
                                <option value="82">82 days</option>
                                <option value="83">83 days</option>
                                <option value="84">84 days</option>
                                <option value="85">85 days</option>
                                <option value="86">86 days</option>
                                <option value="87">87 days</option>
                                <option value="88">88 days</option>
                                <option value="89">89 days</option>
                                <option value="90">90 days</option>
                                <option value="91">91 days</option>
                                <option value="92">92 days</option>
                                <option value="93">93 days</option>
                                <option value="94">94 days</option>
                                <option value="95">95 days</option>
                                <option value="96">96 days</option>
                                <option value="97">97 days</option>
                                <option value="98">98 days</option>
                                <option value="99">99 days</option>
                                <option value="100">100 days</option>
                                <option value="101">101 days</option>
                                <option value="102">102 days</option>
                                <option value="103">103 days</option>
                                <option value="104">104 days</option>
                                <option value="105">105 days</option>
                                <option value="106">106 days</option>
                                <option value="107">107 days</option>
                                <option value="108">108 days</option>
                                <option value="109">109 days</option>
                                <option value="110">110 days</option>
                                <option value="111">111 days</option>
                                <option value="112">112 days</option>
                                <option value="113">113 days</option>
                                <option value="114">114 days</option>
                                <option value="115">115 days</option>
                                <option value="116">116 days</option>
                                <option value="117">117 days</option>
                                <option value="118">118 days</option>
                                <option value="119">119 days</option>
                                <option value="120">120 days</option>
                                <option value="121">121 days</option>
                                <option value="122">122 days</option>
                                <option value="123">123 days</option>
                                <option value="124">124 days</option>
                                <option value="125">125 days</option>
                                <option value="126">126 days</option>
                                <option value="127">127 days</option>
                                <option value="128">128 days</option>
                                <option value="129">129 days</option>
                                <option value="130">130 days</option>
                                <option value="131">131 days</option>
                                <option value="132">132 days</option>
                                <option value="133">133 days</option>
                                <option value="134">134 days</option>
                                <option value="135">135 days</option>
                                <option value="136">136 days</option>
                                <option value="137">137 days</option>
                                <option value="138">138 days</option>
                                <option value="139">139 days</option>
                                <option value="140">140 days</option>
                                <option value="141">141 days</option>
                                <option value="142">142 days</option>
                                <option value="143">143 days</option>
                                <option value="144">144 days</option>
                                <option value="145">145 days</option>
                                <option value="146">146 days</option>
                                <option value="147">147 days</option>
                                <option value="148">148 days</option>
                                <option value="149">149 days</option>
                                <option value="150">150 days</option>
                                <option value="151">151 days</option>
                                <option value="152">152 days</option>
                                <option value="153">153 days</option>
                                <option value="154">154 days</option>
                                <option value="155">155 days</option>
                                <option value="156">156 days</option>
                                <option value="157">157 days</option>
                                <option value="158">158 days</option>
                                <option value="159">159 days</option>
                                <option value="160">160 days</option>
                                <option value="161">161 days</option>
                                <option value="162">162 days</option>
                                <option value="163">163 days</option>
                                <option value="164">164 days</option>
                                <option value="165">165 days</option>
                                <option value="166">166 days</option>
                                <option value="167">167 days</option>
                                <option value="168">168 days</option>
                                <option value="169">169 days</option>
                                <option value="170">170 days</option>
                                <option value="171">171 days</option>
                                <option value="172">172 days</option>
                                <option value="173">173 days</option>
                                <option value="174">174 days</option>
                                <option value="175">175 days</option>
                                <option value="176">176 days</option>
                                <option value="177">177 days</option>
                                <option value="178">178 days</option>
                                <option value="179">179 days</option>
                                <option value="180">180 days</option>
                                <option value="181">181 days</option>
                                <option value="182">182 days</option>
                                <option value="183">183 days</option>
                                <option value="184">184 days</option>
                                <option value="185">185 days</option>
                                <option value="186">186 days</option>
                                <option value="187">187 days</option>
                                <option value="188">188 days</option>
                                <option value="189">189 days</option>
                                <option value="190">190 days</option>
                                <option value="191">191 days</option>
                                <option value="192">192 days</option>
                                <option value="193">193 days</option>
                                <option value="194">194 days</option>
                                <option value="195">195 days</option>
                                <option value="196">196 days</option>
                                <option value="197">197 days</option>
                                <option value="198">198 days</option>
                                <option value="199">199 days</option>
                                <option value="200">200 days</option>
                                <option value="201">201 days</option>
                                <option value="202">202 days</option>
                                <option value="203">203 days</option>
                                <option value="204">204 days</option>
                                <option value="205">205 days</option>
                                <option value="206">206 days</option>
                                <option value="207">207 days</option>
                                <option value="208">208 days</option>
                                <option value="209">209 days</option>
                                <option value="210">210 days</option>
                                <option value="211">211 days</option>
                                <option value="212">212 days</option>
                                <option value="213">213 days</option>
                                <option value="214">214 days</option>
                                <option value="215">215 days</option>
                                <option value="216">216 days</option>
                                <option value="217">217 days</option>
                                <option value="218">218 days</option>
                                <option value="219">219 days</option>
                                <option value="220">220 days</option>
                                <option value="221">221 days</option>
                                <option value="222">222 days</option>
                                <option value="223">223 days</option>
                                <option value="224">224 days</option>
                                <option value="225">225 days</option>
                                <option value="226">226 days</option>
                                <option value="227">227 days</option>
                                <option value="228">228 days</option>
                                <option value="229">229 days</option>
                                <option value="230">230 days</option>
                                <option value="231">231 days</option>
                                <option value="232">232 days</option>
                                <option value="233">233 days</option>
                                <option value="234">234 days</option>
                                <option value="235">235 days</option>
                                <option value="236">236 days</option>
                                <option value="237">237 days</option>
                                <option value="238">238 days</option>
                                <option value="239">239 days</option>
                                <option value="240">240 days</option>
                                <option value="241">241 days</option>
                                <option value="242">242 days</option>
                                <option value="243">243 days</option>
                                <option value="244">244 days</option>
                                <option value="245">245 days</option>
                                <option value="246">246 days</option>
                                <option value="247">247 days</option>
                                <option value="248">248 days</option>
                                <option value="249">249 days</option>
                                <option value="250">250 days</option>
                                <option value="251">251 days</option>
                                <option value="252">252 days</option>
                                <option value="253">253 days</option>
                                <option value="254">254 days</option>
                                <option value="255">255 days</option>
                                <option value="256">256 days</option>
                                <option value="257">257 days</option>
                                <option value="258">258 days</option>
                                <option value="259">259 days</option>
                                <option value="260">260 days</option>
                                <option value="261">261 days</option>
                                <option value="262">262 days</option>
                                <option value="263">263 days</option>
                                <option value="264">264 days</option>
                                <option value="265">265 days</option>
                                <option value="266">266 days</option>
                                <option value="267">267 days</option>
                                <option value="268">268 days</option>
                                <option value="269">269 days</option>
                                <option value="270">270 days</option>
                                <option value="271">271 days</option>
                                <option value="272">272 days</option>
                                <option value="273">273 days</option>
                                <option value="274">274 days</option>
                                <option value="275">275 days</option>
                                <option value="276">276 days</option>
                                <option value="277">277 days</option>
                                <option value="278">278 days</option>
                                <option value="279">279 days</option>
                                <option value="280">280 days</option>
                                <option value="281">281 days</option>
                                <option value="282">282 days</option>
                                <option value="283">283 days</option>
                                <option value="284">284 days</option>
                                <option value="285">285 days</option>
                                <option value="286">286 days</option>
                                <option value="287">287 days</option>
                                <option value="288">288 days</option>
                                <option value="289">289 days</option>
                                <option value="290">290 days</option>
                                <option value="291">291 days</option>
                                <option value="292">292 days</option>
                                <option value="293">293 days</option>
                                <option value="294">294 days</option>
                                <option value="295">295 days</option>
                                <option value="296">296 days</option>
                                <option value="297">297 days</option>
                                <option value="298">298 days</option>
                                <option value="299">299 days</option>
                                <option value="300">300 days</option>
                                <option value="301">301 days</option>
                                <option value="302">302 days</option>
                                <option value="303">303 days</option>
                                <option value="304">304 days</option>
                                <option value="305">305 days</option>
                                <option value="306">306 days</option>
                                <option value="307">307 days</option>
                                <option value="308">308 days</option>
                                <option value="309">309 days</option>
                                <option value="310">310 days</option>
                                <option value="311">311 days</option>
                                <option value="312">312 days</option>
                                <option value="313">313 days</option>
                                <option value="314">314 days</option>
                                <option value="315">315 days</option>
                                <option value="316">316 days</option>
                                <option value="317">317 days</option>
                                <option value="318">318 days</option>
                                <option value="319">319 days</option>
                                <option value="320">320 days</option>
                                <option value="321">321 days</option>
                                <option value="322">322 days</option>
                                <option value="323">323 days</option>
                                <option value="324">324 days</option>
                                <option value="325">325 days</option>
                                <option value="326">326 days</option>
                                <option value="327">327 days</option>
                                <option value="328">328 days</option>
                                <option value="329">329 days</option>
                                <option value="330">330 days</option>
                                <option value="331">331 days</option>
                                <option value="332">332 days</option>
                                <option value="333">333 days</option>
                                <option value="334">334 days</option>
                                <option value="335">335 days</option>
                                <option value="336">336 days</option>
                                <option value="337">337 days</option>
                                <option value="338">338 days</option>
                                <option value="339">339 days</option>
                                <option value="340">340 days</option>
                                <option value="341">341 days</option>
                                <option value="342">342 days</option>
                                <option value="343">343 days</option>
                                <option value="344">344 days</option>
                                <option value="345">345 days</option>
                                <option value="346">346 days</option>
                                <option value="347">347 days</option>
                                <option value="348">348 days</option>
                                <option value="349">349 days</option>
                                <option value="350">350 days</option>
                                <option value="351">351 days</option>
                                <option value="352">352 days</option>
                                <option value="353">353 days</option>
                                <option value="354">354 days</option>
                                <option value="355">355 days</option>
                                <option value="356">356 days</option>
                                <option value="357">357 days</option>
                                <option value="358">358 days</option>
                                <option value="359">359 days</option>
                                <option value="360">360 days</option>
                            </select>
                    </div>
                   
                    <div class="col-xs-1">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">Search!</button>
                        </span>

                        <span class="input-group-btn">
                          <button type="button" class="btn bg-purple  btn-flat" onClick="parent.location='missed_repayments.html'">Reset!</button>
                        </span>
                    </div>
                  </div>
                </div><!-- /.box-body -->
            </form>
          </div><!-- /.box -->There are no loans with missed repayments.

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