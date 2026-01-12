<?php
//loan_product_fulldetails
session_start();

$loanPdct = FALSE;
include '../helpers/AppUtil.php';
include '../helpers/DbAcess.php';
$userId = AppUtil::userId();
$db = new DbAcess();

$messasge=NULL;

if (!empty($_POST) && isset($_POST['loan_product_name'])) {

    $loan_product_id = $_POST['loan_product_id'];
    $loan_product_name = $_POST['loan_product_name'];
    $disbursedMtds = $_POST['loan_disbursed_by_id'];
    $min_loan_principal_amount = $_POST['min_loan_principal_amount'];
    $default_loan_principal_amount = $_POST["default_loan_principal_amount"];
    $max_loan_principal_amount = $_POST['max_loan_principal_amount'];
    $loan_interest_method = $_POST['loan_interest_method'];
    $min_loan_interest = $_POST['min_loan_interest'];
    $min_loan_interest_period = $_POST['min_loan_interest_period'];
    $default_loan_interest = $_POST['default_loan_interest'];
    $default_loan_interest_period = $_POST['default_loan_interest_period'];
    $max_loan_interest = $_POST['max_loan_interest'];
    $max_loan_interest_period = $_POST['max_loan_interest_period'];
    $min_loan_duration = $_POST['min_loan_duration'];
    $min_loan_duration_period = $_POST['min_loan_duration_period'];
    $default_loan_duration = $_POST['default_loan_duration'];
    $max_loan_duration = $_POST['max_loan_duration'];
    $max_loan_duration_period = $_POST['max_loan_duration_period'];
    $loan_payment_scheme_id = $_POST['loan_payment_scheme_id'];

    $min_loan_num_of_repayments = $_POST['min_loan_num_of_repayments'];
    $default_loan_num_of_repayments = $_POST['default_loan_num_of_repayments'];
    $max_loan_num_of_repayments = $_POST['max_loan_num_of_repayments'];

    $loan_decimal_places = $_POST['loan_decimal_places'];
    $repayment_order = $_POST['repayment_order'];

    $set1 = [
        "name" => $loan_product_name,
        "decimal_places" => $loan_decimal_places,
        "repayment_order" => json_encode($repayment_order),
        "disbursement_mtds" => json_encode($disbursedMtds),
        "last_modified_by" => $userId
    ];

    $where = ["id" => $loan_product_id];
    $update = $db->update("loan_product", $set1, $where);
   /// echo '</br>%%% *** .' . $update;

    if ($update) {
        //save Principal Settings
        $set2 = [
            "mini" => $min_loan_principal_amount,
            "default_amount" => $default_loan_principal_amount,
            "max" => $max_loan_principal_amount
        ];
        $where2 = ["loan_product_id" => $loan_product_id];
        $principalUpdate = $db->update("principal_amount", $set2, $where2);
       // echo '</br>%%% *** Principal.' . $principalUpdate;
        //Save Interest           
        $set3 = [
            "method" => $loan_interest_method,
            "mini_interest " => $min_loan_interest,
            "mini_interest_rate" => $min_loan_interest_period,
            "default_interest" => $default_loan_interest,
            "default_interest_date" => $default_loan_interest_period,
            "max_interest" => $max_loan_interest,
            "max_interest_rate" => $max_loan_interest_period,
            "last_modified_by" => $userId
        ];
        $interest = $db->update("loan_product_interest", $set3, $where2);
       // echo '</br>%%% *** Interest.' . $interest;
        //save Duration       
        $setDur = [
            "min_duration" => $min_loan_duration,
            "min_duration_rate " => $min_loan_duration_period,
            "default_duration" => $default_loan_duration,
            "default_duration_rate" => $max_loan_duration_period,
            "max_duration" => $max_loan_duration,
            "max_duration_rate" => $max_loan_duration_period,
            "last_modified_by" => $userId
        ];
        $durn = $db->update("loan_duration", $setDur, $where2);
       // echo '</br>%%% *** Duration.' . $durn;
        //save Repayments
        $setRepay = [
            "repayment_cycle" => json_encode($loan_payment_scheme_id),
            "mini_no_repayments" => $min_loan_num_of_repayments,
            "default_no_repayments" => $default_loan_num_of_repayments,
            "max_no_repayments" => $max_loan_num_of_repayments,
            "last_modified_by" => $userId
        ];
        $durn1 = $db->update("loan_repayments", $setRepay, $where2);
       // echo '</br>%%% *** Repayments.' . $durn1;
        //header("location:view_loan_products.php");
        $messasge="Loan Product Updated Successfully";
    }
}

if (isset($_GET['id'])) {

    $loanPdct = $db->select("loan_product_fulldetails", [], ['id' => $_GET['id']]);
}
?>
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
            jQuery(document).ready(function ($) {
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
                <section class="content-header"><h1>Edit Loan Product<small><a href="http://support.loandisk.com/support/solutions/articles/17000013634-add-loan-product" target="_blank">Help</a></small></h1>
                </section>

                <?php
                if ($loanPdct) {
                    //print_r($db);
                    //print_r($loanPdct);
                    ?>
                    <!-- Main content -->
                    <section class="content">


                        <div class="box box-info">
                            <?php  if($messasge!=NULL){ ?>
                            <div class="col-md-12">
                                <div class="col-md-offset-3">
                                    <h2 class="text text-success">Updated </h2>
                                </div>
                            </div>
                            <?php } ?>
                            <form action="" class="form-horizontal" method="post" enctype="multipart/form-data"  onSubmit="return selectAll()">
                                <input type="hidden" name="back_url" value="view_loan_products.php">

                                <input type="hidden" name="edit_loan_product_submitted" value="1">
                                <input type="hidden" name="loan_product_id" value="<?= $loanPdct['id'] ?>">          
                                <div class="box-body">
                                    <div class="form-group">

                                        <label for="inputName" class="col-sm-3 control-label">Loan Product Name</label>                      
                                        <div class="col-sm-5">
                                            <input type="text" name="loan_product_name" class="form-control" id="inputName" placeholder="Loan Product Name" value="<?= $loanPdct['name'] ?>" required>
                                        </div>
                                    </div>

                                    <div class="slidingDiv_" style="display: block;">

                                        <hr>
                                        <p class="text-red"><b>Principal Amount:</b></p>

                                        <div class="form-group">
                                            <label for="inputDisbursedById"  class="col-sm-3 control-label">Disbursed By</label>
                                            <div class="col-sm-3">
                                                <?php
                                                $mtds = $db->select("disbursement_methods");
                                                $loanDisMtds = json_decode($loanPdct['disbursement_mtds']);
                                                foreach ($mtds as $mtd) {
                                                    $checked = in_array($mtd['name'], $loanDisMtds) ? "checked='checked'" : "";
                                                    ?>
                                                    <input class="inputDisbursedById" type="checkbox" name="loan_disbursed_by_id[]" value="<?= $mtd['name'] ?>" <?= $checked ?>> <?= $mtd['name'] ?><br>
                                                    <?php
                                                }
                                                ?>

                                            </div>
                                        </div>

                                        <div class="form-group">

                                            <label for="inputMinLoanPrincipalAmount" class="col-sm-3 control-label">Minimum Principal Amount</label>                      
                                            <div class="col-sm-5">
                                                <input type="text" name="min_loan_principal_amount" class="form-control" id="inputMinLoanPrincipalAmount" placeholder="Minimum Amount" value="<?= $loanPdct['minimum_principal'] ?>" onkeypress="return isDecimalKey(this, event)">
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label for="inputDefaultLoanPrincipalAmount" class="col-sm-3 control-label">Default Principal Amount</label>                      
                                            <div class="col-sm-5">
                                                <input type="text" name="default_loan_principal_amount" class="form-control" id="inputDefaultLoanPrincipalAmount" placeholder="Default Amount" value="<?= $loanPdct['default_principal_amount'] ?>" onkeypress="return isDecimalKey(this, event)">
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label for="inputMaxLoanPrincipalAmount" class="col-sm-3 control-label">Maximum Principal Amount</label>                      
                                            <div class="col-sm-5">
                                                <input type="text" name="max_loan_principal_amount" class="form-control" id="inputMaxLoanPrincipalAmount" placeholder="Maximum Amount" value="<?= $loanPdct['max_principal_amount'] ?>" onkeypress="return isDecimalKey(this, event)">
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="text-red"><b>Interest:</b></p>
                                        <div class="form-group">
                                            <label for="inputLoanInterestMethod" class="col-sm-3 control-label">Interest Method</label>                      
                                            <div class="col-sm-5">
                                                <select class="form-control" name="loan_interest_method" id="inputLoanInterestMethod">

                                                    <option value="Flat Rate" <?= $loanPdct['interest_mtd'] == 'Flat Rate' ? "selected='selected'" : "" ?>> Flat Rate</option>
                                                    <option value="Reducing Balance - Equal Installments"  <?= $loanPdct['interest_mtd'] == 'Reducing Balance - Equal Installments' ? "selected='selected'" : "" ?>>Reducing Balance - Equal Installments</option>
                                                    <option value="Reducing Balance - Equal Principal" <?= $loanPdct['interest_mtd'] == 'Reducing Balance - Equal Principal' ? "selected='selected'" : "" ?> >Reducing Balance - Equal Principal</option>
                                                    <option value="Interest-Only" <?= $loanPdct['interest_mtd'] == 'Interest-Only' ? "selected='selected'" : "" ?>>Interest-Only</option>
                                                    <option value="Compound Interest" <?= $loanPdct['interest_mtd'] == 'Compound Interest' ? "selected='selected'" : "" ?>>Compound Interest</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputMinLoanInterest" class="col-sm-3 control-label">Minimum Loan Interest %</label>                      
                                            <div class="col-sm-2">
                                                <input type="text" name="min_loan_interest" class="form-control" id="inputMinLoanInterest" placeholder="%" value="<?= $loanPdct['mini_interest'] ?>"  onkeypress="return isInterestKey(this, event)">
                                            </div>
                                            <div class="col-sm-3">
                                                <script>
                                                    function check() {
                                                        var selectedIndex = document.getElementById("inputMinInterestPeriod").selectedIndex;
                                                        var inputMaxInterestPeriod = document.getElementById("inputMaxInterestPeriod");
                                                        var inputDefaultInterestPeriod = document.getElementById("inputDefaultInterestPeriod");
                                                        inputMaxInterestPeriod.options[selectedIndex].selected = true;
                                                        inputDefaultInterestPeriod.options[selectedIndex].selected = true;

                                                        var inputMinLoanDurationPeriod = document.getElementById("inputMinLoanDurationPeriod");
                                                        var inputMaxLoanDurationPeriod = document.getElementById("inputMaxLoanDurationPeriod");
                                                        var inputDefaultLoanDurationPeriod = document.getElementById("inputDefaultLoanDurationPeriod");

                                                        var min_loan_interest_period_value = document.getElementById("inputMinInterestPeriod").value;
                                                        var loan_duration_period_value = "";

                                                        if (min_loan_interest_period_value == "Day")
                                                            loan_duration_period_value = "Days";

                                                        else if (min_loan_interest_period_value == "Week")
                                                            loan_duration_period_value = "Weeks";

                                                        else if (min_loan_interest_period_value == "Month")
                                                            loan_duration_period_value = "Months";

                                                        else if (min_loan_interest_period_value == "Year")
                                                            loan_duration_period_value = "Years";

                                                        selectItemByValue(inputMinLoanDurationPeriod, loan_duration_period_value);
                                                        selectItemByValue(inputMaxLoanDurationPeriod, loan_duration_period_value);
                                                        selectItemByValue(inputDefaultLoanDurationPeriod, loan_duration_period_value);
                                                    }

                                                    function setMaxLoanDurationPeriod()
                                                    {

                                                        var selectedIndex = document.getElementById("inputMinLoanDurationPeriod").selectedIndex;
                                                        var inputMaxLoanDurationPeriod = document.getElementById("inputMaxLoanDurationPeriod");
                                                        var inputDefaultLoanDurationPeriod = document.getElementById("inputDefaultLoanDurationPeriod");
                                                        inputMaxLoanDurationPeriod.options[selectedIndex].selected = true;
                                                        inputDefaultLoanDurationPeriod.options[selectedIndex].selected = true;
                                                    }
                                                    function selectItemByValue(elmnt, value) {

                                                        for (var i = 0; i < elmnt.options.length; i++)
                                                        {
                                                            if (elmnt.options[i].value == value)
                                                                elmnt.selectedIndex = i;
                                                        }
                                                    }
                                                </script> 
                                                <select class="form-control" name="min_loan_interest_period" id="inputMinInterestPeriod"  onChange="check();">

                                                    <option value="Day" <?= $loanPdct['mini_interest_pd'] == 'Day' ? "selected='selected'" : "" ?>>Per Day</option>
                                                    <option value="Week"  <?= $loanPdct['mini_interest_pd'] == 'Week' ? "selected='selected'" : "" ?>>Per Week</option>
                                                    <option value="Month"  <?= $loanPdct['mini_interest_pd'] == 'Month' ? "selected='selected'" : "" ?>>Per Month</option>
                                                    <option value="Year"  <?= $loanPdct['mini_interest_pd'] == 'Year' ? "selected='selected'" : "" ?>>Per Year</option>
                                                </select>
                                            </div>             
                                        </div>
                                        <div class="form-group">
                                            <label for="inputDefaultLoanInterest" class="col-sm-3 control-label">Default Loan Interest %</label>                      
                                            <div class="col-sm-2">
                                                <input type="text" name="default_loan_interest" class="form-control" id="inputDefaultLoanInterest" placeholder="%" value="<?= $loanPdct['default_interest'] ?>"  onkeypress="return isInterestKey(this, event)">
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="default_loan_interest_period" id="inputDefaultInterestPeriod"  onChange="check();" readonly>
                                                    <option value="Day" <?= $loanPdct['default_interest_pd'] == 'Day' ? "selected='selected'" : "" ?> >Per Day</option>
                                                    <option value="Week" <?= $loanPdct['default_interest_pd'] == 'Week' ? "selected='selected'" : "" ?> >Per Week</option>
                                                    <option value="Month" <?= $loanPdct['default_interest_pd'] == 'Month' ? "selected='selected'" : "" ?> >Per Month</option>
                                                    <option value="Year" <?= $loanPdct['default_interest_pd'] == 'Year' ? "selected='selected'" : "" ?> >Per Year</option>
                                                </select>
                                            </div>             
                                        </div>
                                        <div class="form-group">
                                            <label for="inputMaxLoanInterest" class="col-sm-3 control-label">Maximum Loan Interest %</label>                      
                                            <div class="col-sm-2">
                                                <input type="text" name="max_loan_interest" class="form-control" id="inputMaxLoanInterest" placeholder="%" value="<?= $loanPdct['max_interest'] ?>"  onkeypress="return isInterestKey(this, event)">
                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="max_loan_interest_period" id="inputMaxInterestPeriod"  onChange="check();" readonly>
                                                    <option value=""></option>
                                                    <option value="Day" <?= $loanPdct['max_interest_pd'] == 'Day' ? "selected='selected'" : "" ?> >Per Day</option>
                                                    <option value="Week"  <?= $loanPdct['max_interest_pd'] == 'Week' ? "selected='selected'" : "" ?>>Per Week</option>
                                                    <option value="Month" <?= $loanPdct['max_interest_pd'] == 'Month' ? "selected='selected'" : "" ?> >Per Month</option>
                                                    <option value="Year" <?= $loanPdct['max_interest_pd'] == 'Year' ? "selected='selected'" : "" ?> >Per Year</option>
                                                </select>
                                            </div>             
                                        </div>
                                        <hr>
                                        <p class="text-red"><b>Duration:</b></p>
                                        <div class="form-group">

                                            <label for="inputMinLoanDuration" class="col-sm-3 control-label">Minimum Loan Duration</label>   

                                            <div class="col-sm-2">
                                                <select class="form-control" name="min_loan_duration" id="inputMinLoanDuration">
                                                    <option value="">Any</option>
                                                    <?php
                                                    for ($i = 1; $i < 201; $i++) {
                                                        $selected = $loanPdct['min_duration'] == $i ? "selected='selected'" : "";
                                                        ?>

                                                        <option value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
                                                    <?php } ?>
                                                </select>

                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="min_loan_duration_period" id="inputMinLoanDurationPeriod"  onChange="setMaxLoanDurationPeriod()">
                                                    <option value=""></option>
                                                    <option value="Days"<?= $loanPdct['mini_duration_pd'] == "Days" ? "selected='selected'" : "" ?>  >Days</option>
                                                    <option value="Weeks" <?= $loanPdct['mini_duration_pd'] == "Weeks" ? "selected='selected'" : "" ?> >Weeks</option>
                                                    <option value="Months"  <?= $loanPdct['mini_duration_pd'] == "Months" ? "selected='selected'" : "" ?> >Months</option>
                                                    <option value="Years" <?= $loanPdct['mini_duration_pd'] == "Years" ? "selected='selected'" : "" ?>  >Years</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label for="inputDefaultLoanDuration" class="col-sm-3 control-label">Default Loan Duration</label>                      
                                            <div class="col-sm-2">
                                                <select class="form-control" name="default_loan_duration" id="inputDefaultLoanDuration">
                                                    <option value="">Any</option>
                                                    <?php
                                                    for ($i = 1; $i < 201; $i++) {
                                                        $selected = $loanPdct['default_duration'] == $i ? "selected='selected'" : "";
                                                        ?>

                                                        <option value="<?= $i ?>" <?= $selected ?>><?= $i ?></option>
                                                    <?php } ?>
                                                </select>

                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="default_loan_duration_period" id="inputDefaultLoanDurationPeriod"  onChange="setMaxLoanDurationPeriod()" readonly>
                                                    <option value=""></option>
                                                    <option value="Days" <?= $loanPdct['default_duration_pd'] == "Days" ? "selected='selected'" : "" ?> >Days</option>
                                                    <option value="Weeks" <?= $loanPdct['default_duration_pd'] == "Weeks" ? "selected='selected'" : "" ?>  >Weeks</option>
                                                    <option value="Months" <?= $loanPdct['default_duration_pd'] == "Months" ? "selected='selected'" : "" ?>>Months</option>
                                                    <option value="Years" <?= $loanPdct['default_duration_pd'] == "Years" ? "selected='selected'" : "" ?>  >Years</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">

                                            <label for="inputMaxLoanDuration" class="col-sm-3 control-label">Maximum Loan Duration</label>                      
                                            <div class="col-sm-2">
                                                <select class="form-control" name="max_loan_duration" id="inputMaxLoanDuration" onChange="setMaxLoanDurationPeriod()">
                                                    <option value="">Any</option>
                                                    <?php
                                                    for ($ii = 1; $ii < 201; $ii++) {
                                                        $selected = $loanPdct['max_duration'] == $ii ? "selected='selected'" : "";
                                                        ?>

                                                        <option value="<?= $ii ?>" <?= $selected ?>><?= $ii ?></option>
                                                    <?php } ?>


                                                </select>

                                            </div>
                                            <div class="col-sm-3">
                                                <select class="form-control" name="max_loan_duration_period" id="inputMaxLoanDurationPeriod" readonly>
                                                    <option value=""></option>
                                                    <option value="Days" <?= $loanPdct['max_duration_pd'] == "Days" ? "selected='selected'" : "" ?> >Days</option>
                                                    <option value="Weeks" <?= $loanPdct['max_duration_pd'] == "Weeks" ? "selected='selected'" : "" ?> >Weeks</option>
                                                    <option value="Months"<?= $loanPdct['max_duration_pd'] == "Months" ? "selected='selected'" : "" ?>  >Months</option>
                                                    <option value="Years" <?= $loanPdct['max_duration_pd'] == "Years" ? "selected='selected'" : "" ?>  >Years</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="text-red"><b>Repayments:</b></p>
                                        <div class="form-group">
                                            <label for="inputLoanPaymentSchemeId"  class="col-sm-3 control-label">Repayment Cycle</label>
                                            <div class="col-sm-3">
                                                <?php
                                                $cycles = ["Daily", "Weekly", "Biweekly", "Monthly", "Bimonthly", "Quarterly", "Semi-Annual", "Yearly", "Lump-Sum"];
                                                $dbCycles = json_decode($loanPdct['repayment_cycle']);
                                                //print_r($dbCycles);
                                                foreach ($cycles as $valu) {
                                                    $checkedH = in_array($valu, $dbCycles) ? "checked='checked'" : "";
                                                    ?>
                                                    <input class="classLoanPaymentSchemeId" type="checkbox"  name="loan_payment_scheme_id[]" value="<?= $valu ?>" <?= $checkedH ?>><?= $valu ?><br>
                                                <?php }
                                                ?>


                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="inputMinLoanNumOfRepayments" class="col-sm-3 control-label">Minimum Number of Repayments</label>                      
                                            <div class="col-sm-2">
                                                <select class="form-control" name="min_loan_num_of_repayments" id="inputMinLoanNumOfRepayments">
                                                    <option value="">Any</option>
                                                    <?php
                                                    for ($y = 1; $y < 2001; $y++) {
                                                        $sel = $loanPdct['mini_no_repayments'] == $y ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?= $y ?>" <?= $sel ?>><?= $y ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputDefaultLoanNumOfRepayments" class="col-sm-3 control-label">Default Number of Repayments</label>                      
                                            <div class="col-sm-2">
                                                <select class="form-control" name="default_loan_num_of_repayments" id="inputDefaultLoanNumOfRepayments">
                                                    <option value="">Any</option>

                                                    <?php
                                                    for ($y = 1; $y < 2001; $y++) {
                                                        $sel = $loanPdct['default_no_repayments'] == $y ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?= $y ?>" <?= $sel ?>><?= $y ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputMaxLoanNumOfRepayments" class="col-sm-3 control-label">Maximum Number of Repayments</label>                      
                                            <div class="col-sm-2">
                                                <select class="form-control" name="max_loan_num_of_repayments" id="inputMaxLoanNumOfRepayments">
                                                    <option value="">Any</option>
                                                    <?php
                                                    for ($y = 1; $y < 2001; $y++) {
                                                        $sel = $loanPdct['max_no_repayments'] == $y ? "selected='selected'" : "";
                                                        ?>
                                                        <option value="<?= $y ?>" <?= $sel ?>><?= $y ?></option>
                                                    <?php } ?>

                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <p class="text-red"><b>Loan Due and Loan Schedule Amounts:</b></p>
                                        <p>If Loan Due amount and/or Schedule amounts are in decimals for example TZS 100.33333, the system will convert them based on below option.<p>
                                        <div class="form-group">
                                            <label for="inputLoanDecimalPlaces" class="col-sm-3 control-label">Decimal Places</label>                      
                                            <div class="col-sm-5">
                                                <select class="form-control" name="loan_decimal_places" id="inputLoanDecimalPlaces">
                                                    <option value=""></option>
                                                    <option value="2" <?= $loanPdct['decimal_places'] == 2 ? "selected='selected'" : "" ?>>Round Off to 2 Decimal Places</option>
                                                    <option value="0" <?= $loanPdct['decimal_places'] == 0 ? "selected='selected'" : "" ?>>Round Off to Integer</option>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                        <script>
                                            function up() {
                                                var selectedOpts = $('#to option:selected');
                                                if (selectedOpts.length == 0) {

                                                    alert("Select a column");

                                                    e.preventDefault();

                                                }
                                                var selected = $("#to").find(":selected");
                                                var before = selected.prev();
                                                if (before.length > 0)
                                                    selected.detach().insertBefore(before);
                                            }

                                            function down() {
                                                var selectedOpts = $('#to option:selected');
                                                if (selectedOpts.length == 0) {

                                                    alert("Select a column");

                                                    e.preventDefault();

                                                }
                                                var selected = $("#to").find(":selected");
                                                var next = selected.next();
                                                if (next.length > 0)
                                                    selected.detach().insertAfter(next);
                                            }
                                            function  selectAll() {
                                                var listbox = document.getElementById('to');
                                                for (var count = 0; count < listbox.options.length; count++) {
                                                    listbox.options[count].selected = true;
                                                }
                                            }
                                        </script>
                                        <p class="text-red"><b>Repayment Order:</b></p>
                                        <p>The order in which repayments are allocated. For example let's say you receive payment of TZS 100 and order is <b>Fees</b>,  <b>Principal</b>, <b>Interest</b>, <b>Penalty</b>. Based on the loan schedule, the system will allocate the amount to <b>Fees</b> first and remaining amount to <b>Principal</b> and then <b>Interest</b> and then <b>Penalty</b>.<p>
                                        <div class="form-group">
                                            <label for="inputLoanRepaymentOrder" class="col-sm-3 control-label">Repayment Order</label>       
                                            <div class="col-xs-6">
                                                <select multiple id="to" size="7" name="repayment_order[]">
                                                    <?php
                                                    foreach (json_decode($loanPdct['repayment_order']) as $value) {
                                                        ?>
                                                        <option value="<?= $value ?>" ><?= $value ?></option>
                                                    <?php }
                                                    ?>

                                                </select>
                                                <input type="button" value="Up" onclick="up()">
                                                <input type="button" value="Down" onclick="down()">
                                            </div>
                                        </div>
                                        <hr>

                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="button" class="btn btn-default"  onClick="parent.location = 'view_loan_products.php'">Back</button>
                                    <button type="submit" class="btn btn-info pull-right">Update</button>
                                </div><!-- /.box-footer -->
                            </form>
                        </div>

                    </section>

                <?php } ?>
            </div><!-- /.content-wrapper -->
        </div><!-- ./wrapper -->

        <!-- REQUIRED JS SCRIPTS -->

        <!-- Bootstrap 3.3.5 -->
        <script src="../bootstrap/js/bootstrap.min.js"></script>
        <!-- AdminLTE App -->
        <script src="../dist/js/app.min.js"></script>

    </body>
</html>