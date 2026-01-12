

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
                <section class="content-header"><h1>Loan Calculator</h1>
                </section>

                <!-- Main content -->
                <section class="content">
    <div class="box box-info">
        <form action = "/loans/loan_calculator.php" class="form-horizontal" method="post"  enctype="multipart/form-data" name="form">
            <input type="hidden" name="back_url" value="/loans/loan_calculator.php">
            <input type="hidden" name="loan_calculator" value="1">
            <script type="text/javascript" src="../include/js/add_edit_loan_new.js"></script>
            <div class="box-body">
                <p>You can use this page to calculate the loan value in case of customer inquiries. To add a loan into the system, visit <b>Borrowers</b>(left menu) &rarr; <b>View Borrowers</b> &rarr; <b>Loans</b> &rarr; <b>Add Loan</b></p>
                <p class="bg-navy disabled color-palette">Loan Terms:</p>
                <div class="form-group">
                    
                    <label for="inputLoanPrincipalAmount" class="col-sm-3 control-label">Principal Amount</label>                      
                    <div class="col-sm-5">
                        <input type="text" name="loan_principal_amount" class="form-control" id="inputLoanPrincipalAmount" placeholder="Principal Amount" required value="" onkeypress="return isDecimalKey(this,event)">
                    </div>
                </div>
                
        <script>
        $(function() {
            $('#inputLoanReleasedDate').datepick({ 
            defaultDate: '06/01/2017', showTrigger: '#calImg',
            yearRange: 'c-20:c+20', showTrigger: '#calImg',
            dateFormat: 'dd/mm/yyyy',
            
            });
        });
        
        </script>
                <div class="form-group">
                    <label for="inputLoanReleasedDate" class="col-sm-3 control-label">Loan Release Date</label>
                    <div class="col-sm-5">
                        <input type="text" name="loan_released_date" class="form-control" id="inputLoanReleasedDate" placeholder="dd/mm/yyyy" value="" required >
                    </div>
                </div>
                <hr>
                <p class="text-red"><b>Interest:</b></p>
                <div class="form-group">
                    <label for="inputLoanInterestMethod" class="col-sm-3 control-label">Interest Method</label>                      
                    <div class="col-sm-4">
                        <select class="form-control" name="loan_interest_method" id="inputLoanInterestMethod" required onChange="enableDisableFirstRepaymentAmount();">
                            <option value="flat_rate" > Flat Rate</option>
                            <option value="reducing_rate_equal_installments" >Reducing Balance - Equal Installments</option>
                            <option value="reducing_rate_equal_principal" >Reducing Balance - Equal Principal</option>
                            <option value="interest_only" >Interest-Only</option>
                            <option value="compound_interest" >Compound Interest</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputLoanInterest" class="col-sm-3 control-label">Loan Interest %</label>                      
                    <div class="col-sm-2">
                        <input type="text" name="loan_interest" class="form-control" id="inputLoanInterest" placeholder="%" value="" required  onkeypress="return isInterestKey(this,event)">
                    </div>
                    <div class="col-sm-2">
                        <select class="form-control" name="loan_interest_period" id="inputInterestPeriod" onChange="check();">
                            <option value="Day"  >Per Day</option>
                            <option value="Week"  >Per Week</option>
                            <option value="Month"  >Per Month</option>
                            <option value="Year"  >Per Year</option>
                        </select>
                    </div>             
                </div>
                <hr>
                <p class="text-red"><b>Duration:</b></p>
                <div class="form-group">
                    
                    <label for="inputLoanDuration" class="col-sm-3 control-label">Loan Duration</label>                      
                    <div class="col-sm-2">
                        <select class="form-control" name="loan_duration" id="inputLoanDuration"  onChange="setNumofRep();">
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                            <option value="6" >6</option>
                            <option value="7" >7</option>
                            <option value="8" >8</option>
                            <option value="9" >9</option>
                            <option value="10" >10</option>
                            <option value="11" >11</option>
                            <option value="12" >12</option>
                            <option value="13" >13</option>
                            <option value="14" >14</option>
                            <option value="15" >15</option>
                            <option value="16" >16</option>
                            <option value="17" >17</option>
                            <option value="18" >18</option>
                            <option value="19" >19</option>
                            <option value="20" >20</option>
                            <option value="21" >21</option>
                            <option value="22" >22</option>
                            <option value="23" >23</option>
                            <option value="24" >24</option>
                            <option value="25" >25</option>
                            <option value="26" >26</option>
                            <option value="27" >27</option>
                            <option value="28" >28</option>
                            <option value="29" >29</option>
                            <option value="30" >30</option>
                            <option value="31" >31</option>
                            <option value="32" >32</option>
                            <option value="33" >33</option>
                            <option value="34" >34</option>
                            <option value="35" >35</option>
                            <option value="36" >36</option>
                            <option value="37" >37</option>
                            <option value="38" >38</option>
                            <option value="39" >39</option>
                            <option value="40" >40</option>
                            <option value="41" >41</option>
                            <option value="42" >42</option>
                            <option value="43" >43</option>
                            <option value="44" >44</option>
                            <option value="45" >45</option>
                            <option value="46" >46</option>
                            <option value="47" >47</option>
                            <option value="48" >48</option>
                            <option value="49" >49</option>
                            <option value="50" >50</option>
                            <option value="51" >51</option>
                            <option value="52" >52</option>
                            <option value="53" >53</option>
                            <option value="54" >54</option>
                            <option value="55" >55</option>
                            <option value="56" >56</option>
                            <option value="57" >57</option>
                            <option value="58" >58</option>
                            <option value="59" >59</option>
                            <option value="60" >60</option>
                            <option value="61" >61</option>
                            <option value="62" >62</option>
                            <option value="63" >63</option>
                            <option value="64" >64</option>
                            <option value="65" >65</option>
                            <option value="66" >66</option>
                            <option value="67" >67</option>
                            <option value="68" >68</option>
                            <option value="69" >69</option>
                            <option value="70" >70</option>
                            <option value="71" >71</option>
                            <option value="72" >72</option>
                            <option value="73" >73</option>
                            <option value="74" >74</option>
                            <option value="75" >75</option>
                            <option value="76" >76</option>
                            <option value="77" >77</option>
                            <option value="78" >78</option>
                            <option value="79" >79</option>
                            <option value="80" >80</option>
                            <option value="81" >81</option>
                            <option value="82" >82</option>
                            <option value="83" >83</option>
                            <option value="84" >84</option>
                            <option value="85" >85</option>
                            <option value="86" >86</option>
                            <option value="87" >87</option>
                            <option value="88" >88</option>
                            <option value="89" >89</option>
                            <option value="90" >90</option>
                            <option value="91" >91</option>
                            <option value="92" >92</option>
                            <option value="93" >93</option>
                            <option value="94" >94</option>
                            <option value="95" >95</option>
                            <option value="96" >96</option>
                            <option value="97" >97</option>
                            <option value="98" >98</option>
                            <option value="99" >99</option>
                            <option value="100" >100</option>
                            <option value="101" >101</option>
                            <option value="102" >102</option>
                            <option value="103" >103</option>
                            <option value="104" >104</option>
                            <option value="105" >105</option>
                            <option value="106" >106</option>
                            <option value="107" >107</option>
                            <option value="108" >108</option>
                            <option value="109" >109</option>
                            <option value="110" >110</option>
                            <option value="111" >111</option>
                            <option value="112" >112</option>
                            <option value="113" >113</option>
                            <option value="114" >114</option>
                            <option value="115" >115</option>
                            <option value="116" >116</option>
                            <option value="117" >117</option>
                            <option value="118" >118</option>
                            <option value="119" >119</option>
                            <option value="120" >120</option>
                            <option value="121" >121</option>
                            <option value="122" >122</option>
                            <option value="123" >123</option>
                            <option value="124" >124</option>
                            <option value="125" >125</option>
                            <option value="126" >126</option>
                            <option value="127" >127</option>
                            <option value="128" >128</option>
                            <option value="129" >129</option>
                            <option value="130" >130</option>
                            <option value="131" >131</option>
                            <option value="132" >132</option>
                            <option value="133" >133</option>
                            <option value="134" >134</option>
                            <option value="135" >135</option>
                            <option value="136" >136</option>
                            <option value="137" >137</option>
                            <option value="138" >138</option>
                            <option value="139" >139</option>
                            <option value="140" >140</option>
                            <option value="141" >141</option>
                            <option value="142" >142</option>
                            <option value="143" >143</option>
                            <option value="144" >144</option>
                            <option value="145" >145</option>
                            <option value="146" >146</option>
                            <option value="147" >147</option>
                            <option value="148" >148</option>
                            <option value="149" >149</option>
                            <option value="150" >150</option>
                            <option value="151" >151</option>
                            <option value="152" >152</option>
                            <option value="153" >153</option>
                            <option value="154" >154</option>
                            <option value="155" >155</option>
                            <option value="156" >156</option>
                            <option value="157" >157</option>
                            <option value="158" >158</option>
                            <option value="159" >159</option>
                            <option value="160" >160</option>
                            <option value="161" >161</option>
                            <option value="162" >162</option>
                            <option value="163" >163</option>
                            <option value="164" >164</option>
                            <option value="165" >165</option>
                            <option value="166" >166</option>
                            <option value="167" >167</option>
                            <option value="168" >168</option>
                            <option value="169" >169</option>
                            <option value="170" >170</option>
                            <option value="171" >171</option>
                            <option value="172" >172</option>
                            <option value="173" >173</option>
                            <option value="174" >174</option>
                            <option value="175" >175</option>
                            <option value="176" >176</option>
                            <option value="177" >177</option>
                            <option value="178" >178</option>
                            <option value="179" >179</option>
                            <option value="180" >180</option>
                            <option value="181" >181</option>
                            <option value="182" >182</option>
                            <option value="183" >183</option>
                            <option value="184" >184</option>
                            <option value="185" >185</option>
                            <option value="186" >186</option>
                            <option value="187" >187</option>
                            <option value="188" >188</option>
                            <option value="189" >189</option>
                            <option value="190" >190</option>
                            <option value="191" >191</option>
                            <option value="192" >192</option>
                            <option value="193" >193</option>
                            <option value="194" >194</option>
                            <option value="195" >195</option>
                            <option value="196" >196</option>
                            <option value="197" >197</option>
                            <option value="198" >198</option>
                            <option value="199" >199</option>
                            <option value="200" >200</option>
                            <option value="201" >201</option>
                            <option value="202" >202</option>
                            <option value="203" >203</option>
                            <option value="204" >204</option>
                            <option value="205" >205</option>
                            <option value="206" >206</option>
                            <option value="207" >207</option>
                            <option value="208" >208</option>
                            <option value="209" >209</option>
                            <option value="210" >210</option>
                            <option value="211" >211</option>
                            <option value="212" >212</option>
                            <option value="213" >213</option>
                            <option value="214" >214</option>
                            <option value="215" >215</option>
                            <option value="216" >216</option>
                            <option value="217" >217</option>
                            <option value="218" >218</option>
                            <option value="219" >219</option>
                            <option value="220" >220</option>
                            <option value="221" >221</option>
                            <option value="222" >222</option>
                            <option value="223" >223</option>
                            <option value="224" >224</option>
                            <option value="225" >225</option>
                            <option value="226" >226</option>
                            <option value="227" >227</option>
                            <option value="228" >228</option>
                            <option value="229" >229</option>
                            <option value="230" >230</option>
                            <option value="231" >231</option>
                            <option value="232" >232</option>
                            <option value="233" >233</option>
                            <option value="234" >234</option>
                            <option value="235" >235</option>
                            <option value="236" >236</option>
                            <option value="237" >237</option>
                            <option value="238" >238</option>
                            <option value="239" >239</option>
                            <option value="240" >240</option>
                            <option value="241" >241</option>
                            <option value="242" >242</option>
                            <option value="243" >243</option>
                            <option value="244" >244</option>
                            <option value="245" >245</option>
                            <option value="246" >246</option>
                            <option value="247" >247</option>
                            <option value="248" >248</option>
                            <option value="249" >249</option>
                            <option value="250" >250</option>
                            <option value="251" >251</option>
                            <option value="252" >252</option>
                            <option value="253" >253</option>
                            <option value="254" >254</option>
                            <option value="255" >255</option>
                            <option value="256" >256</option>
                            <option value="257" >257</option>
                            <option value="258" >258</option>
                            <option value="259" >259</option>
                            <option value="260" >260</option>
                            <option value="261" >261</option>
                            <option value="262" >262</option>
                            <option value="263" >263</option>
                            <option value="264" >264</option>
                            <option value="265" >265</option>
                            <option value="266" >266</option>
                            <option value="267" >267</option>
                            <option value="268" >268</option>
                            <option value="269" >269</option>
                            <option value="270" >270</option>
                            <option value="271" >271</option>
                            <option value="272" >272</option>
                            <option value="273" >273</option>
                            <option value="274" >274</option>
                            <option value="275" >275</option>
                            <option value="276" >276</option>
                            <option value="277" >277</option>
                            <option value="278" >278</option>
                            <option value="279" >279</option>
                            <option value="280" >280</option>
                            <option value="281" >281</option>
                            <option value="282" >282</option>
                            <option value="283" >283</option>
                            <option value="284" >284</option>
                            <option value="285" >285</option>
                            <option value="286" >286</option>
                            <option value="287" >287</option>
                            <option value="288" >288</option>
                            <option value="289" >289</option>
                            <option value="290" >290</option>
                            <option value="291" >291</option>
                            <option value="292" >292</option>
                            <option value="293" >293</option>
                            <option value="294" >294</option>
                            <option value="295" >295</option>
                            <option value="296" >296</option>
                            <option value="297" >297</option>
                            <option value="298" >298</option>
                            <option value="299" >299</option>
                            <option value="300" >300</option>
                            <option value="301" >301</option>
                            <option value="302" >302</option>
                            <option value="303" >303</option>
                            <option value="304" >304</option>
                            <option value="305" >305</option>
                            <option value="306" >306</option>
                            <option value="307" >307</option>
                            <option value="308" >308</option>
                            <option value="309" >309</option>
                            <option value="310" >310</option>
                            <option value="311" >311</option>
                            <option value="312" >312</option>
                            <option value="313" >313</option>
                            <option value="314" >314</option>
                            <option value="315" >315</option>
                            <option value="316" >316</option>
                            <option value="317" >317</option>
                            <option value="318" >318</option>
                            <option value="319" >319</option>
                            <option value="320" >320</option>
                            <option value="321" >321</option>
                            <option value="322" >322</option>
                            <option value="323" >323</option>
                            <option value="324" >324</option>
                            <option value="325" >325</option>
                            <option value="326" >326</option>
                            <option value="327" >327</option>
                            <option value="328" >328</option>
                            <option value="329" >329</option>
                            <option value="330" >330</option>
                            <option value="331" >331</option>
                            <option value="332" >332</option>
                            <option value="333" >333</option>
                            <option value="334" >334</option>
                            <option value="335" >335</option>
                            <option value="336" >336</option>
                            <option value="337" >337</option>
                            <option value="338" >338</option>
                            <option value="339" >339</option>
                            <option value="340" >340</option>
                            <option value="341" >341</option>
                            <option value="342" >342</option>
                            <option value="343" >343</option>
                            <option value="344" >344</option>
                            <option value="345" >345</option>
                            <option value="346" >346</option>
                            <option value="347" >347</option>
                            <option value="348" >348</option>
                            <option value="349" >349</option>
                            <option value="350" >350</option>
                            <option value="351" >351</option>
                            <option value="352" >352</option>
                            <option value="353" >353</option>
                            <option value="354" >354</option>
                            <option value="355" >355</option>
                            <option value="356" >356</option>
                            <option value="357" >357</option>
                            <option value="358" >358</option>
                            <option value="359" >359</option>
                            <option value="360" >360</option>
                            <option value="361" >361</option>
                            <option value="362" >362</option>
                            <option value="363" >363</option>
                            <option value="364" >364</option>
                            <option value="365" >365</option>
                        </select>
                        
                     </div>
                     <div class="col-sm-2">
                        <select class="form-control" name="loan_duration_period" id="inputLoanDurationPeriod" required onChange="setNumofRep();">
                            <option value=""></option>
                            <option value="Days"  >Days</option>
                            <option value="Weeks"  >Weeks</option>
                            <option value="Months"  >Months</option>
                            <option value="Years"  >Years</option>
                        </select>
                    </div>
                </div>
                <hr>
                <p class="text-red"><b>Repayments:</b></p>
                <div class="form-group">
                    <label for="inputLoanPaymentSchemeId"  class="col-sm-3 control-label">Repayment Cycle</label>
                    <div class="col-sm-2">
                        <select class="form-control" name="loan_payment_scheme_id" id="inputLoanPaymentSchemeId" required onChange=" disableNumRepayments(); setNumofRep();">
                            <option value=""></option>
                            <option value="6"  >Daily</option>
                            <option value="4"  >Weekly</option>
                            <option value="9"  >Biweekly</option>
                            <option value="3"  >Monthly</option>
                            <option value="12"  >Bimonthly</option>
                            <option value="13"  >Quarterly</option>
                            <option value="14"  >Semi-Annual</option>
                            <option value="11"  >Yearly</option>
                            <option value="10"  >Lump-Sum</option>
                        </select>
                    </div>
                </div>
                
                
                <div class="form-group">
                    <label for="inputLoanNumOfRepayments" class="col-sm-3 control-label">Number of Repayments</label>                      
                    <div class="col-sm-2">
                        <select class="form-control" name="loan_num_of_repayments" id="inputLoanNumOfRepayments" required onChange="removeNumRepaymentsMessage()">
                            <option value="1" >1</option>
                            <option value="2" >2</option>
                            <option value="3" >3</option>
                            <option value="4" >4</option>
                            <option value="5" >5</option>
                            <option value="6" >6</option>
                            <option value="7" >7</option>
                            <option value="8" >8</option>
                            <option value="9" >9</option>
                            <option value="10" >10</option>
                            <option value="11" >11</option>
                            <option value="12" >12</option>
                            <option value="13" >13</option>
                            <option value="14" >14</option>
                            <option value="15" >15</option>
                            <option value="16" >16</option>
                            <option value="17" >17</option>
                            <option value="18" >18</option>
                            <option value="19" >19</option>
                            <option value="20" >20</option>
                            <option value="21" >21</option>
                            <option value="22" >22</option>
                            <option value="23" >23</option>
                            <option value="24" >24</option>
                            <option value="25" >25</option>
                            <option value="26" >26</option>
                            <option value="27" >27</option>
                            <option value="28" >28</option>
                            <option value="29" >29</option>
                            <option value="30" >30</option>
                            <option value="31" >31</option>
                            <option value="32" >32</option>
                            <option value="33" >33</option>
                            <option value="34" >34</option>
                            <option value="35" >35</option>
                            <option value="36" >36</option>
                            <option value="37" >37</option>
                            <option value="38" >38</option>
                            <option value="39" >39</option>
                            <option value="40" >40</option>
                            <option value="41" >41</option>
                            <option value="42" >42</option>
                            <option value="43" >43</option>
                            <option value="44" >44</option>
                            <option value="45" >45</option>
                            <option value="46" >46</option>
                            <option value="47" >47</option>
                            <option value="48" >48</option>
                            <option value="49" >49</option>
                            <option value="50" >50</option>
                            <option value="51" >51</option>
                            <option value="52" >52</option>
                            <option value="53" >53</option>
                            <option value="54" >54</option>
                            <option value="55" >55</option>
                            <option value="56" >56</option>
                            <option value="57" >57</option>
                            <option value="58" >58</option>
                            <option value="59" >59</option>
                            <option value="60" >60</option>
                            <option value="61" >61</option>
                            <option value="62" >62</option>
                            <option value="63" >63</option>
                            <option value="64" >64</option>
                            <option value="65" >65</option>
                            <option value="66" >66</option>
                            <option value="67" >67</option>
                            <option value="68" >68</option>
                            <option value="69" >69</option>
                            <option value="70" >70</option>
                            <option value="71" >71</option>
                            <option value="72" >72</option>
                            <option value="73" >73</option>
                            <option value="74" >74</option>
                            <option value="75" >75</option>
                            <option value="76" >76</option>
                            <option value="77" >77</option>
                            <option value="78" >78</option>
                            <option value="79" >79</option>
                            <option value="80" >80</option>
                            <option value="81" >81</option>
                            <option value="82" >82</option>
                            <option value="83" >83</option>
                            <option value="84" >84</option>
                            <option value="85" >85</option>
                            <option value="86" >86</option>
                            <option value="87" >87</option>
                            <option value="88" >88</option>
                            <option value="89" >89</option>
                            <option value="90" >90</option>
                            <option value="91" >91</option>
                            <option value="92" >92</option>
                            <option value="93" >93</option>
                            <option value="94" >94</option>
                            <option value="95" >95</option>
                            <option value="96" >96</option>
                            <option value="97" >97</option>
                            <option value="98" >98</option>
                            <option value="99" >99</option>
                            <option value="100" >100</option>
                            <option value="101" >101</option>
                            <option value="102" >102</option>
                            <option value="103" >103</option>
                            <option value="104" >104</option>
                            <option value="105" >105</option>
                            <option value="106" >106</option>
                            <option value="107" >107</option>
                            <option value="108" >108</option>
                            <option value="109" >109</option>
                            <option value="110" >110</option>
                            <option value="111" >111</option>
                            <option value="112" >112</option>
                            <option value="113" >113</option>
                            <option value="114" >114</option>
                            <option value="115" >115</option>
                            <option value="116" >116</option>
                            <option value="117" >117</option>
                            <option value="118" >118</option>
                            <option value="119" >119</option>
                            <option value="120" >120</option>
                            <option value="121" >121</option>
                            <option value="122" >122</option>
                            <option value="123" >123</option>
                            <option value="124" >124</option>
                            <option value="125" >125</option>
                            <option value="126" >126</option>
                            <option value="127" >127</option>
                            <option value="128" >128</option>
                            <option value="129" >129</option>
                            <option value="130" >130</option>
                            <option value="131" >131</option>
                            <option value="132" >132</option>
                            <option value="133" >133</option>
                            <option value="134" >134</option>
                            <option value="135" >135</option>
                            <option value="136" >136</option>
                            <option value="137" >137</option>
                            <option value="138" >138</option>
                            <option value="139" >139</option>
                            <option value="140" >140</option>
                            <option value="141" >141</option>
                            <option value="142" >142</option>
                            <option value="143" >143</option>
                            <option value="144" >144</option>
                            <option value="145" >145</option>
                            <option value="146" >146</option>
                            <option value="147" >147</option>
                            <option value="148" >148</option>
                            <option value="149" >149</option>
                            <option value="150" >150</option>
                            <option value="151" >151</option>
                            <option value="152" >152</option>
                            <option value="153" >153</option>
                            <option value="154" >154</option>
                            <option value="155" >155</option>
                            <option value="156" >156</option>
                            <option value="157" >157</option>
                            <option value="158" >158</option>
                            <option value="159" >159</option>
                            <option value="160" >160</option>
                            <option value="161" >161</option>
                            <option value="162" >162</option>
                            <option value="163" >163</option>
                            <option value="164" >164</option>
                            <option value="165" >165</option>
                            <option value="166" >166</option>
                            <option value="167" >167</option>
                            <option value="168" >168</option>
                            <option value="169" >169</option>
                            <option value="170" >170</option>
                            <option value="171" >171</option>
                            <option value="172" >172</option>
                            <option value="173" >173</option>
                            <option value="174" >174</option>
                            <option value="175" >175</option>
                            <option value="176" >176</option>
                            <option value="177" >177</option>
                            <option value="178" >178</option>
                            <option value="179" >179</option>
                            <option value="180" >180</option>
                            <option value="181" >181</option>
                            <option value="182" >182</option>
                            <option value="183" >183</option>
                            <option value="184" >184</option>
                            <option value="185" >185</option>
                            <option value="186" >186</option>
                            <option value="187" >187</option>
                            <option value="188" >188</option>
                            <option value="189" >189</option>
                            <option value="190" >190</option>
                            <option value="191" >191</option>
                            <option value="192" >192</option>
                            <option value="193" >193</option>
                            <option value="194" >194</option>
                            <option value="195" >195</option>
                            <option value="196" >196</option>
                            <option value="197" >197</option>
                            <option value="198" >198</option>
                            <option value="199" >199</option>
                            <option value="200" >200</option>
                            <option value="201" >201</option>
                            <option value="202" >202</option>
                            <option value="203" >203</option>
                            <option value="204" >204</option>
                            <option value="205" >205</option>
                            <option value="206" >206</option>
                            <option value="207" >207</option>
                            <option value="208" >208</option>
                            <option value="209" >209</option>
                            <option value="210" >210</option>
                            <option value="211" >211</option>
                            <option value="212" >212</option>
                            <option value="213" >213</option>
                            <option value="214" >214</option>
                            <option value="215" >215</option>
                            <option value="216" >216</option>
                            <option value="217" >217</option>
                            <option value="218" >218</option>
                            <option value="219" >219</option>
                            <option value="220" >220</option>
                            <option value="221" >221</option>
                            <option value="222" >222</option>
                            <option value="223" >223</option>
                            <option value="224" >224</option>
                            <option value="225" >225</option>
                            <option value="226" >226</option>
                            <option value="227" >227</option>
                            <option value="228" >228</option>
                            <option value="229" >229</option>
                            <option value="230" >230</option>
                            <option value="231" >231</option>
                            <option value="232" >232</option>
                            <option value="233" >233</option>
                            <option value="234" >234</option>
                            <option value="235" >235</option>
                            <option value="236" >236</option>
                            <option value="237" >237</option>
                            <option value="238" >238</option>
                            <option value="239" >239</option>
                            <option value="240" >240</option>
                            <option value="241" >241</option>
                            <option value="242" >242</option>
                            <option value="243" >243</option>
                            <option value="244" >244</option>
                            <option value="245" >245</option>
                            <option value="246" >246</option>
                            <option value="247" >247</option>
                            <option value="248" >248</option>
                            <option value="249" >249</option>
                            <option value="250" >250</option>
                            <option value="251" >251</option>
                            <option value="252" >252</option>
                            <option value="253" >253</option>
                            <option value="254" >254</option>
                            <option value="255" >255</option>
                            <option value="256" >256</option>
                            <option value="257" >257</option>
                            <option value="258" >258</option>
                            <option value="259" >259</option>
                            <option value="260" >260</option>
                            <option value="261" >261</option>
                            <option value="262" >262</option>
                            <option value="263" >263</option>
                            <option value="264" >264</option>
                            <option value="265" >265</option>
                            <option value="266" >266</option>
                            <option value="267" >267</option>
                            <option value="268" >268</option>
                            <option value="269" >269</option>
                            <option value="270" >270</option>
                            <option value="271" >271</option>
                            <option value="272" >272</option>
                            <option value="273" >273</option>
                            <option value="274" >274</option>
                            <option value="275" >275</option>
                            <option value="276" >276</option>
                            <option value="277" >277</option>
                            <option value="278" >278</option>
                            <option value="279" >279</option>
                            <option value="280" >280</option>
                            <option value="281" >281</option>
                            <option value="282" >282</option>
                            <option value="283" >283</option>
                            <option value="284" >284</option>
                            <option value="285" >285</option>
                            <option value="286" >286</option>
                            <option value="287" >287</option>
                            <option value="288" >288</option>
                            <option value="289" >289</option>
                            <option value="290" >290</option>
                            <option value="291" >291</option>
                            <option value="292" >292</option>
                            <option value="293" >293</option>
                            <option value="294" >294</option>
                            <option value="295" >295</option>
                            <option value="296" >296</option>
                            <option value="297" >297</option>
                            <option value="298" >298</option>
                            <option value="299" >299</option>
                            <option value="300" >300</option>
                            <option value="301" >301</option>
                            <option value="302" >302</option>
                            <option value="303" >303</option>
                            <option value="304" >304</option>
                            <option value="305" >305</option>
                            <option value="306" >306</option>
                            <option value="307" >307</option>
                            <option value="308" >308</option>
                            <option value="309" >309</option>
                            <option value="310" >310</option>
                            <option value="311" >311</option>
                            <option value="312" >312</option>
                            <option value="313" >313</option>
                            <option value="314" >314</option>
                            <option value="315" >315</option>
                            <option value="316" >316</option>
                            <option value="317" >317</option>
                            <option value="318" >318</option>
                            <option value="319" >319</option>
                            <option value="320" >320</option>
                            <option value="321" >321</option>
                            <option value="322" >322</option>
                            <option value="323" >323</option>
                            <option value="324" >324</option>
                            <option value="325" >325</option>
                            <option value="326" >326</option>
                            <option value="327" >327</option>
                            <option value="328" >328</option>
                            <option value="329" >329</option>
                            <option value="330" >330</option>
                            <option value="331" >331</option>
                            <option value="332" >332</option>
                            <option value="333" >333</option>
                            <option value="334" >334</option>
                            <option value="335" >335</option>
                            <option value="336" >336</option>
                            <option value="337" >337</option>
                            <option value="338" >338</option>
                            <option value="339" >339</option>
                            <option value="340" >340</option>
                            <option value="341" >341</option>
                            <option value="342" >342</option>
                            <option value="343" >343</option>
                            <option value="344" >344</option>
                            <option value="345" >345</option>
                            <option value="346" >346</option>
                            <option value="347" >347</option>
                            <option value="348" >348</option>
                            <option value="349" >349</option>
                            <option value="350" >350</option>
                            <option value="351" >351</option>
                            <option value="352" >352</option>
                            <option value="353" >353</option>
                            <option value="354" >354</option>
                            <option value="355" >355</option>
                            <option value="356" >356</option>
                            <option value="357" >357</option>
                            <option value="358" >358</option>
                            <option value="359" >359</option>
                            <option value="360" >360</option>
                            <option value="361" >361</option>
                            <option value="362" >362</option>
                            <option value="363" >363</option>
                            <option value="364" >364</option>
                            <option value="365" >365</option>
                            <option value="366" >366</option>
                            <option value="367" >367</option>
                            <option value="368" >368</option>
                            <option value="369" >369</option>
                            <option value="370" >370</option>
                            <option value="371" >371</option>
                            <option value="372" >372</option>
                            <option value="373" >373</option>
                            <option value="374" >374</option>
                            <option value="375" >375</option>
                            <option value="376" >376</option>
                            <option value="377" >377</option>
                            <option value="378" >378</option>
                            <option value="379" >379</option>
                            <option value="380" >380</option>
                            <option value="381" >381</option>
                            <option value="382" >382</option>
                            <option value="383" >383</option>
                            <option value="384" >384</option>
                            <option value="385" >385</option>
                            <option value="386" >386</option>
                            <option value="387" >387</option>
                            <option value="388" >388</option>
                            <option value="389" >389</option>
                            <option value="390" >390</option>
                            <option value="391" >391</option>
                            <option value="392" >392</option>
                            <option value="393" >393</option>
                            <option value="394" >394</option>
                            <option value="395" >395</option>
                            <option value="396" >396</option>
                            <option value="397" >397</option>
                            <option value="398" >398</option>
                            <option value="399" >399</option>
                            <option value="400" >400</option>
                            <option value="401" >401</option>
                            <option value="402" >402</option>
                            <option value="403" >403</option>
                            <option value="404" >404</option>
                            <option value="405" >405</option>
                            <option value="406" >406</option>
                            <option value="407" >407</option>
                            <option value="408" >408</option>
                            <option value="409" >409</option>
                            <option value="410" >410</option>
                            <option value="411" >411</option>
                            <option value="412" >412</option>
                            <option value="413" >413</option>
                            <option value="414" >414</option>
                            <option value="415" >415</option>
                            <option value="416" >416</option>
                            <option value="417" >417</option>
                            <option value="418" >418</option>
                            <option value="419" >419</option>
                            <option value="420" >420</option>
                            <option value="421" >421</option>
                            <option value="422" >422</option>
                            <option value="423" >423</option>
                            <option value="424" >424</option>
                            <option value="425" >425</option>
                            <option value="426" >426</option>
                            <option value="427" >427</option>
                            <option value="428" >428</option>
                            <option value="429" >429</option>
                            <option value="430" >430</option>
                            <option value="431" >431</option>
                            <option value="432" >432</option>
                            <option value="433" >433</option>
                            <option value="434" >434</option>
                            <option value="435" >435</option>
                            <option value="436" >436</option>
                            <option value="437" >437</option>
                            <option value="438" >438</option>
                            <option value="439" >439</option>
                            <option value="440" >440</option>
                            <option value="441" >441</option>
                            <option value="442" >442</option>
                            <option value="443" >443</option>
                            <option value="444" >444</option>
                            <option value="445" >445</option>
                            <option value="446" >446</option>
                            <option value="447" >447</option>
                            <option value="448" >448</option>
                            <option value="449" >449</option>
                            <option value="450" >450</option>
                            <option value="451" >451</option>
                            <option value="452" >452</option>
                            <option value="453" >453</option>
                            <option value="454" >454</option>
                            <option value="455" >455</option>
                            <option value="456" >456</option>
                            <option value="457" >457</option>
                            <option value="458" >458</option>
                            <option value="459" >459</option>
                            <option value="460" >460</option>
                            <option value="461" >461</option>
                            <option value="462" >462</option>
                            <option value="463" >463</option>
                            <option value="464" >464</option>
                            <option value="465" >465</option>
                            <option value="466" >466</option>
                            <option value="467" >467</option>
                            <option value="468" >468</option>
                            <option value="469" >469</option>
                            <option value="470" >470</option>
                            <option value="471" >471</option>
                            <option value="472" >472</option>
                            <option value="473" >473</option>
                            <option value="474" >474</option>
                            <option value="475" >475</option>
                            <option value="476" >476</option>
                            <option value="477" >477</option>
                            <option value="478" >478</option>
                            <option value="479" >479</option>
                            <option value="480" >480</option>
                            <option value="481" >481</option>
                            <option value="482" >482</option>
                            <option value="483" >483</option>
                            <option value="484" >484</option>
                            <option value="485" >485</option>
                            <option value="486" >486</option>
                            <option value="487" >487</option>
                            <option value="488" >488</option>
                            <option value="489" >489</option>
                            <option value="490" >490</option>
                            <option value="491" >491</option>
                            <option value="492" >492</option>
                            <option value="493" >493</option>
                            <option value="494" >494</option>
                            <option value="495" >495</option>
                            <option value="496" >496</option>
                            <option value="497" >497</option>
                            <option value="498" >498</option>
                            <option value="499" >499</option>
                            <option value="500" >500</option>
                        </select>
                    </div>
                    
                    <div class="col-sm-2" id="inputLoanNumOfRepaymentsChanged">
                    </div>
                </div>
                
        <script>
        $(function() {
            $('#inputLoanInterestStartDate').datepick({ 
            defaultDate: '06/01/2017', showTrigger: '#calImg',
            yearRange: 'c-20:c+20', showTrigger: '#calImg',
            dateFormat: 'dd/mm/yyyy',
            
            });
        });
        
        </script>
                <script>
                $(document).ready(function () {
                  $(".slidingDiv").hide();
                
                  $('.show_hide').click(function (e) {
                    $(".slidingDiv").slideToggle("fast");
                    var val = $(this).text() == "Hide" ? "Show" : "Hide";
                    $(this).hide().text(val).fadeIn("fast");
                    e.preventDefault();
                  });
                });
                </script>
                <hr>
                <p><b>Advance Settings: <a href="#" class="show_hide">Show</a></b></p>
                <div class="slidingDiv" style="display: none;">
                    <p>These are optional fields</p>
                    <div class="form-group">
                        <label for="inputLoanDecimalPlaces" class="col-sm-3 control-label">Decimal Places</label>                      
                        <div class="col-sm-4">
                            <select class="form-control" name="loan_decimal_places" id="inputLoanDecimalPlaces">
                                <option value=""></option>
                                <option value="round_off_to_two_decimal" >Round Off to 2 Decimal Places</option>
                                <option value="round_off_to_integer" >Round Off to Integer</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputLoanInterestStartDate" class="col-sm-3 control-label">Interest Start Date</label>
                        <div class="col-sm-5">
                            <input type="text" name="loan_interest_start_date" class="form-control" id="inputLoanInterestStartDate" placeholder="dd/mm/yyyy" value="">
                        </div>
                    </div>
                    
        <script>
        $(function() {
            $('#inputLoanFirstRepaymentDate').datepick({ 
            defaultDate: '06/01/2017', showTrigger: '#calImg',
            yearRange: 'c-20:c+20', showTrigger: '#calImg',
            dateFormat: 'dd/mm/yyyy',
            
            });
        });
        
        </script>
                    <div class="form-group">
                        <label for="inputLoanFirstRepaymentDate" class="col-sm-3 control-label">First Repayment Date</label>
                        <div class="col-sm-5">
                            <input type="text" name="loan_first_repayment_date" class="form-control" id="inputLoanFirstRepaymentDate" placeholder="dd/mm/yyyy" value="">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        
                        <label for="inputFirstRepaymentAmount" class="col-sm-3 control-label">First Repayment Amount</label>                      
                        <div class="col-sm-5">
                            <input type="text" name="first_repayment_amount" class="form-control" id="inputFirstRepaymentAmount" placeholder="First Repayment Amount" value=""  onkeypress="return isDecimalKey(this,event)">
                        </div>
                    </div>
                </div>
                <hr>
                <p class="bg-navy disabled color-palette">Loan Fees (optional):</p>
                <div class="form-group">
                    <label for="inputLoanFee_144" class="col-sm-3 control-label">Pining %</label>                      
                    <div class="col-sm-3">
                        <input type="text" name="loan_fee_id_144" class="form-control" id="inputLoanFee_144" placeholder="Percentage" value=""  onkeypress="return  isInterestKey(this,event)">
                    </div>
                    <div class="col-sm-2">
                        <label>(Deductable)
                        </label>
                    </div>
               </div>
                <div class="form-group">
                    <label for="inputLoanFee_145" class="col-sm-3 control-label">Bavuu</label>                      
                    <div class="col-sm-3">
                        <input type="text" name="loan_fee_id_145" class="form-control" id="inputLoanFee_145" placeholder="Fixed Amount" value=""  onkeypress="return  isDecimalKey(this,event)">
                    </div>
                    <div class="col-sm-2">
                        <label>
                        </label>
                    </div>
               </div>
                <div class="form-group">
                    <label for="inputLoanFee_146" class="col-sm-3 control-label">Housing</label>                      
                    <div class="col-sm-3">
                        <input type="text" name="loan_fee_id_146" class="form-control" id="inputLoanFee_146" placeholder="Fixed Amount" value=""  onkeypress="return  isDecimalKey(this,event)">
                    </div>
                    <div class="col-sm-2">
                        <label>(Deductable)
                        </label>
                    </div>
               </div>
                <div class="form-group">
                    <label for="inputLoanFeesSchedule" class="col-sm-3 control-label">How should Fees be charged in Loan Schedule?</label>                      
                    <div class="col-sm-5">
                        <select class="form-control" name="loan_fees_schedule" id="inputLoanFeesSchedule" required>
                            <option value="dont_include" >Don't include in the loan schedule</option>
                            <option value="distribute_fees_evenly" >Distribute Fees Evenly Among All Repayments</option>
                            <option value="charge_fees_on_released_date" >Charge Fees on the Released Date</option>
                            <option value="charge_fees_on_first_repayment" >Charge Fees on the First Repayment</option>
                            <option value="charge_fees_on_last_repayment" >Charge Fees on the Last Repayment</option>
                        </select>
                    </div>
                </div>
                <div class="box-footer">
    <button type="button" class="btn btn-default"  onClick="parent.location='loan_calculator.html'">Reset</button>
                    <button type="submit" class="btn btn-info pull-right">Calculate</button>
                </div><!-- /.box-footer -->
            </div>                
        </form>
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