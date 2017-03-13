<?php
$borrowerID = (int) $_GET['borrower'];
$borrower = [];
include '../helpers/DbAcess.php';
include '../helpers/AppUtil.php';
$db = new DbAcess(); //$cursor = new Cursor;
if (is_numeric($borrowerID)) {
    $table = 'borrower';
    $borrower = $db->select($table, [], ['id' => $borrowerID]);
}
$allloanPdcts = $db->select("loan_product_fulldetails");
$loanCharges = $db->select("loan_charges");

if (isset($_POST['loan_product_id'])) {

    $loan_product_id = $_POST['loan_product_id'];
    $borrower_ID = $_POST['borrower_id'];
    $loan_application_id = $_POST['loan_application_id'];
    $loan_disbursed_by_mtd = $_POST["loan_disbursed_by_id"];
    $loan_principal_amount = $_POST['loan_principal_amount'];
    $loan_released_date = $_POST['loan_released_date'];
    $loan_interest_method = $_POST['loan_interest_method'];
    $loan_interest = $_POST['loan_interest'];
    $loan_interest_pd = $_POST['loan_interest_period'];
    $loan_duration = $_POST['loan_duration'];
    $loan_duration_pd = $_POST['loan_duration_period'];
    $repayment_cycle = $_POST['loan_payment_scheme_id'];
    $loan_num_of_repayments = $_POST['loan_num_of_repayments'];

    $loan_fee = $_POST['loan_fee']; //Array of Ids of each Charge
    //loan_files
    $loan_description = $_POST['loan_description'];
    $loan_status = $_POST['loan_status_id'];

    ///
    $data = [
        "loan_product_id" => $loan_product_id,
        "borrower_id" => $borrower_ID,
        "loan_no" => $loan_application_id,
        "disbursement_mtd" => $loan_disbursed_by_mtd,
        "principal_amt" => $loan_principal_amount,
        "release_date" => $loan_released_date,
        "interest_mtd" => $loan_interest_method,
        "loan_interest" => $loan_interest,
        "loan_interest_pd" => $loan_interest_pd,
        "loan_duration" => $loan_duration,
        "loan_duration_pd" => $loan_duration_pd,
        "repayment_cycle" => $repayment_cycle,
        "no_repayment_cycle" => $loan_num_of_repayments,
        "description" => $loan_description,
        "creation_user" => AppUtil::userId(),
        "status" => $loan_status
    ];
    $insertId = $db->insert('loans', $data);
   // echo '$insertId == ' . $insertId;
    //save Loan Charges.
    foreach ($loan_fee as $fee) {
        $feeDetails = $db->select("loan_charges", [], ["id" => $fee]);
        //save Charge Agaist Loan
        $data2 = [
            "loan_id" => $insertId,
            "loan_charge_id" => $fee,
            "amount" => $feeDetails["charge_amount"],
            "creation_user" => AppUtil::userId()
        ];
        $saveAppliedCharge = $db->insert("loan_applied_charges", $data2);
        //echo ' $saveAppliedCharge = ' . $saveAppliedCharge;
    }
    //save Files
    //loan_files
    $loan_files = $_FILES["loan_files"];

    if (!empty($loan_files)) {
        // print_r($allOtherFiles);
        $namesArray = $loan_files['name'];
        $tempNameArray = $loan_files['tmp_name'];
        $typeArray = $loan_files['type'];
        $ii = 0;
        foreach ($tempNameArray as $value) {
            $saveFileH = AppUtil::saveFile($tempNameArray[$ii], $namesArray[$ii]);
            $tableHer = 'loan_files';

            $datahere = [
                'loan_id' => $insertId,
                'name' => $namesArray[$ii],
                'creation_user' => 0,
                'extension' => $typeArray[$ii],
                'path' => $saveFileH,
                "creation_user" => AppUtil::userId()
            ];
            $insertIdHere = $db->insert($tableHer, $datahere);
           // echo $insertIdHere . '`******1</br>**';

            $ii++;
        }
    }
    //Redirect to Borrower for details
    //view_loans_borrower.php?borrower=13
    header("location:view_loans_borrower.php?borrower=".$borrower_ID);
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
                <section class="content-header"><h1>Add Loan<small><a href="" target="_blank">Help</a></small></h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box box-info">  
                        <form action = "" class="form-horizontal" method="post"  enctype="multipart/form-data" name="form" id="form">
                            <input type="hidden" name="back_url" value="">
                            <input type="hidden" name="borrower_id" value="<?= $borrower['id'] ?>">
                            <input type="hidden" name="add_loan" value="1">
                            <script type="text/javascript" src="../include/js/add_edit_loan_new.js"></script>
                            <div class="box-body">
                                <div class="form-group">

                                    <label for="inputLoanBorrowerName" class="col-sm-3 control-label">Borrower</label>                      
                                    <div class="col-sm-7" style="margin-top:8px;">
                                        <?= $borrower['title'] . ' ' . $borrower['fname'] . ' ' . $borrower['lname'] ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputLoanType"  class="col-sm-3 control-label">Loan Product</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" name="loan_product_id" id="inputLoanType">
                                            <?php
                                            foreach ($allloanPdcts as $loan) {
                                                ?>
                                                <option value="<?= $loan['id'] ?>" ><?= $loan['name'] ?></option>

                                                <?php
                                            }
                                            ?>
                                            <!---     <option value="2384" onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2384&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Business Loan</option>
                                                 <option value="2772" selected onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2772&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Farming</option>
                                                 <option value="2386" onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2386&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Overseas Worker Loan</option>
                                                 <option value="2387" onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2387&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Pensioner Loan</option>
                                                 <option value="2383" onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2383&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Personal Loan</option>
                                                 <option value="2385" onclick="javascript:location.href = 'https://x.loandisk.com/loans/add_loan.php?borrower_id=10419&loan_product_id=2385&back_url=https://x.loandisk.com/loans/view_loans_borrower.php?borrower_id=10419'">Student Loan</option>
                                                 --->
                                        </select>
                                    </div> 

                                    <div class="col-sm-4">
                                        <div class="pull-left" style="margin-top:5px">
                                            <a href="../admin/view_loan_products.php" target="_blank">Add/Edit Loan Products</a>
                                        </div>
                                    </div>            
                                </div>
                                <div class="form-group">

                                    <label for="inputLoanApplicationId" class="col-sm-3 control-label">Loan no. #</label>                      
                                    <div class="col-sm-4">
                                        <input type="text" name="loan_application_id" class="form-control" id="inputLoanApplicationId" required value="10003">
                                    </div>
                                </div>
                                <hr>
                                <p class="bg-navy disabled color-palette">Loan Terms (required fields):</b></p>
                                <p class="text-red"><b>Principal:</b></p>
                                <div class="form-group">
                                    <label for="inputDisbursedById"  class="col-sm-3 control-label">Disbursed By</label>
                                    <div class="col-sm-3">
                                        <select class="form-control" name="loan_disbursed_by_id" id="inputDisbursedById" required>

                                        </select>
                                    </div>  
                                    <div class="col-sm-4">
                                        <div class="pull-left" style="margin-top:5px">
                                            <a href="../admin/view_loan_disbursed_by.php" target="_blank">Add/Edit Disbursed By</a>
                                        </div>
                                    </div>           
                                </div>
                                <div class="form-group">

                                    <label for="inputLoanPrincipalAmount" class="col-sm-3 control-label">Principal Amount</label>                      
                                    <div class="col-sm-4">
                                        <input type="number" name="loan_principal_amount" class="form-control" id="inputLoanPrincipalAmount" placeholder="Principal Amount" required value="8000" onkeypress="return isDecimalKey(this, event)">

                                    </div>
                                </div>

                                <script>
                                    $(function () {
                                        $('#inputLoanReleasedDate').datepick({
                                            defaultDate: '12/01/2017', showTrigger: '#calImg',
                                            yearRange: 'c-20:c+20', showTrigger: '#calImg',
                                                    dateFormat: 'dd/mm/yyyy',
                                        });
                                    });

                                </script>
                                <div class="form-group">
                                    <label for="inputLoanReleasedDate" class="col-sm-3 control-label">Loan Release Date</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="loan_released_date" class="form-control" id="inputLoanReleasedDate" placeholder="dd/mm/yyyy" value="" required>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-red"><b>Interest:</b></p>
                                <div class="form-group">
                                    <label for="inputLoanInterestMethod" class="col-sm-3 control-label">Interest Method</label>                      
                                    <div class="col-sm-4">
                                        <select class="form-control" name="loan_interest_method" id="inputLoanInterestMethod" required onChange="enableDisableFirstRepaymentAmount();">
                                            <option value="flat_rate" > Flat Rate</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputLoanInterest" class="col-sm-3 control-label">Loan Interest %</label>                      
                                    <div class="col-sm-2">    
                                        <input type="number" name="loan_interest" class="form-control" id="inputLoanInterest" placeholder="%" value="3" required  onkeypress="return isInterestKey(this, event)">
                                        <!--Min 2% - 4% Max -->
                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="loan_interest_period" id="inputInterestPeriod" onChange="check();">
                                            <option value="Day"  >Per Day</option>
                                        </select>
                                    </div>             
                                </div>
                                <hr>
                                <p class="text-red"><b>Duration:</b></p>
                                <div class="form-group">

                                    <label for="inputLoanDuration" class="col-sm-3 control-label">Loan Duration</label>                      
                                    <div class="col-sm-2">
                                        <select class="form-control" name="loan_duration" id="inputLoanDuration"  onChange="setNumofRep();">
                                            <option value="1" selected>1</option>
                                            <option value="2" >2</option>



                                        </select>

                                    </div>
                                    <div class="col-sm-2">
                                        <select class="form-control" name="loan_duration_period" id="inputLoanDurationPeriod" required onChange="setNumofRep();">
                                            <option value=""></option>
                                            <option value="Days"  >Days</option>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <p class="text-red"><b>Repayments:</b></p>
                                <div class="form-group">
                                    <label for="inputLoanPaymentSchemeId"  class="col-sm-3 control-label">Repayment Cycle</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="loan_payment_scheme_id" id="inputLoanPaymentSchemeId" required onChange=" disableNumRepayments();
                                                setNumofRep();">
                                            <option value=""></option>
                                            <option value="6" >Daily</option>
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label for="inputLoanNumOfRepayments" class="col-sm-3 control-label">Number of Repayments</label>                      
                                    <div class="col-sm-2">
                                        <select class="form-control" name="loan_num_of_repayments" id="inputLoanNumOfRepayments" required onChange="removeNumRepaymentsMessage()">

                                        </select>

                                    </div>

                                    <div class="col-sm-2" id="inputLoanNumOfRepaymentsChanged">
                                    </div>
                                </div>

                                <script>
                                    $(function () {
                                        $('#inputLoanInterestStartDate').datepick({
                                            defaultDate: '12/01/2017', showTrigger: '#calImg',
                                            yearRange: 'c-20:c+20', showTrigger: '#calImg',
                                                    dateFormat: 'dd/mm/yyyy',
                                        });
                                    });

                                </script>
                                <script>
                                    $(document).ready(function () {
                                        //
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
                                                <option value="round_off_to_two_decimal" >Round Off to 2 Decimal Places</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputLoanInterestStartDate" class="col-sm-3 control-label">Interest Start Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="loan_interest_start_date" class="form-control" id="inputLoanInterestStartDate" placeholder="dd/mm/yyyy" value="">
                                        </div>
                                    </div>

                                    <script>
                                        $(function () {
                                            $('#inputLoanFirstRepaymentDate').datepick({
                                                defaultDate: '12/01/2017', showTrigger: '#calImg',
                                                yearRange: 'c-20:c+20', showTrigger: '#calImg',
                                                        dateFormat: 'dd/mm/yyyy',
                                            });
                                        });

                                    </script>
                                    <div class="form-group">
                                        <label for="inputLoanFirstRepaymentDate" class="col-sm-3 control-label">First Repayment Date</label>
                                        <div class="col-sm-4">
                                            <input type="text" name="loan_first_repayment_date" class="form-control" id="inputLoanFirstRepaymentDate" placeholder="dd/mm/yyyy" value="">
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        <label for="inputFirstRepaymentAmount" class="col-sm-3 control-label">First Repayment Amount</label>                      
                                        <div class="col-sm-4">
                                            <input type="text" name="first_repayment_amount" class="form-control" id="inputFirstRepaymentAmount" placeholder="First Repayment Amount" value=""  onkeypress="return isDecimalKey(this, event)">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <p class="bg-navy disabled color-palette">Loan Fees (optional):</p>
                                <?php
                                foreach ($loanCharges as $charge) {
                                    ?>

                                    <div class="form-group">
                                        <label for="inputLoanFee_<?= $charge['id'] ?>" class="col-sm-3 control-label"><?= $charge['name'] ?></label>                      
                                        <div class="col-sm-3">
                                            <input type="text" disabled="disabled" name="loan_fee_charge[]" class="form-control" id="inputLoanFee_<?= $charge['id'] ?>" placeholder="Percentage" value="<?= $charge['charge_amount'] ?>"  onkeypress="return  isInterestKey(this, event)">

                                        </div>
                                        <div class="col-sm-2">
                                            <input type="checkbox" id="checkCharges" name="loan_fee[]"  value="<?= $charge['id'] ?>"/> Is Applicable?
                                            <label>
                                            </label>
                                        </div>
                                    </div>

                                    <?php
                                }
                                ?>

                                <!--  <div class="form-group">
                                      <label for="inputLoanFeesSchedule" class="col-sm-3 control-label">How should Fees be charged in Loan Schedule?</label>                      
                                      <div class="col-sm-4">
                                          <select class="form-control" name="loan_fees_schedule" id="inputLoanFeesSchedule" required>
                                              <option value="charge_fees_on_first_repayment" >Charge Fees on the First Repayment</option>
                                          </select>
                                      </div>
                                  </div>      -->          

                                <hr>
                                <p class="bg-navy disabled color-palette">Other (optional):</p>

                                <div class="form-group">
                                    <label for="inputLoanDescription" class="col-sm-3 control-label">Description</label>                      
                                    <div class="col-sm-9">
                                        <textarea name="loan_description" class="form-control" id="inputLoanDescription" rows="3"></textarea>
                                    </div>
                                </div>                                 
                                <div class="form-group">
                                    <label for="loan_files" class="col-sm-3 control-label">Loan Files<br>(doc, pdf, image)</label>
                                    <div class="col-sm-9">    
                                        <input type="file" id="data_files" name="loan_files[]" multiple>
                                    </div>
                                    <div class="col-sm-9">
                                        You can select up to 30 files. Please click <b>Browse</b> button and then hold <b>Ctrl</b> button on your keyboard to select multiple files.
                                    </div>               
                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            if (window.FormData) {
                                                $('#data_files').bind({
                                                    change: function ()
                                                    {
                                                        var input = document.getElementById('data_files');
                                                        files = input.files;

                                                        if (files.length > 30) {
                                                            alert('You can only upload max of 30 files');
                                                            $('#data_files').val("");
                                                            return false;
                                                        }
                                                        else {
                                                            var regExp = new RegExp('(application/pdf|application/acrobat|applications/vnd.pdf|text/pdf|text/x-pdf|application/msword|application/x-msword|application/vnd.openxmlformats-officedocument.wordprocessingml.document|image/png|image/jpeg|image/gif)', 'i');
                                                            for (var i = 0; i < files.length; i++)
                                                            {
                                                                var file = files[i];
                                                                var matcher = regExp.test(file.type);
                                                                var filesize = file.size;
                                                                if (!matcher)
                                                                {
                                                                    alert('You can only upload doc, pdf, or image files');
                                                                    $('#data_files').val("");
                                                                    return false;
                                                                }
                                                                if (filesize > 30000000)
                                                                {
                                                                    alert('File must not be more than 30mb');
                                                                    $('#data_files').val("");
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                                $('#photo_file').bind({
                                                    change: function ()
                                                    {
                                                        var input = document.getElementById('photo_file');
                                                        files = input.files;

                                                        if (files.length > 1) {
                                                            alert('You can only upload max of 1 file');
                                                            $('#photo_file').val("");
                                                            return false;
                                                        }
                                                        else {
                                                            var regExp = new RegExp('(image/png|image/jpeg|image/gif)', 'i');
                                                            for (var i = 0; i < files.length; i++)
                                                            {
                                                                var file = files[i];
                                                                var matcher = regExp.test(file.type);
                                                                var filesize = file.size;
                                                                if (!matcher)
                                                                {
                                                                    alert('You can only upload png, jpeg, or gif files');
                                                                    $('#photo_file').val("");
                                                                    return false;
                                                                }
                                                                if (filesize > 30000000)
                                                                {
                                                                    alert('File must not be more than 30mb');
                                                                    $('#photo_file').val("");
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                                $('#csv_file').bind({
                                                    change: function ()
                                                    {
                                                        var input = document.getElementById('csv_file');
                                                        files = input.files;

                                                        if (files.length > 1) {
                                                            alert('You can only upload max of 1 file');
                                                            $('#photo_file').val("");
                                                            return false;
                                                        }
                                                        else {
                                                            var regExp = new RegExp('(text/csv|text/plain|application/csv|application/x-csv|text/comma-separated-values|application/excel|application/ms-excel|application/vnd.ms-excel|application/vnd.msexcel|text/anytext|application/octet-stream|application/txt)', 'i');
                                                            for (var i = 0; i < files.length; i++)
                                                            {
                                                                var file = files[i];
                                                                var matcher = regExp.test(file.type);
                                                                var filesize = file.size;
                                                                if (!matcher)
                                                                {
                                                                    alert('You can only upload csv file');
                                                                    $('#csv_file').val("");
                                                                    return false;
                                                                }

                                                                if (filesize > 30000000)
                                                                {
                                                                    alert('File must not be more than 30mb');
                                                                    $('#csv_file').val("");
                                                                    return false;
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            }
                                        });
                                    </script>

                                </div>
                                <div class="form-group">
                                    <label for="inputStatusId" class="col-sm-3 control-label">Loan Status</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" name="loan_status_id" id="inputStatusId">
                                            <option value="Processing">Processing</option>
                                            <option value="Open" selected>Open</option>
                                            <option value="Defaulted">Defaulted</option>
                                            <option value="Denied">Denied</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="box-footer">
                                    <button type="button" class="btn btn-default"  onClick="parent.location = 'view_loans_borrower.html'">Back</button>
                                    <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait">Submit</button>

                                    <script type="text/javascript">
                                        $('#form').submit(function () {
                                            $(this).find('button[type=submit]').prop('disabled', true);
                                            $('.btn').prop('disabled', true);
                                            $(this).find('button[type=submit]').button('loading');
                                            return true;
                                        });
                                    </script>
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
        <script type="text/javascript">
                                        var allloanPdcts = <?= AppUtil::json_encodeWithCheck($allloanPdcts) ?>;

                                        $(document).ready(function () {
                                            //add Selection listener to change Loan Product....
                                            loanSettings(allloanPdcts[0].id);

                                            $("#inputLoanType").on("change", function (e) {
                                                e.preventDefault();
                                                var newValue = $(this).find("option:selected");
                                                ///console.log(newValue);
                                                //console.log("New Value *** " + newValue.val() + "*** " + newValue.text());
                                                //console.log(allloanPdcts);
                                                //load Default Settings for that Loan.
                                                loanSettings(newValue.val());


                                            });
                                        });

                                        function loanSettings(loanProductId) {
                                            //disbursement_mtds
                                            console.log(loanProductId + " Loading *** ");
                                            for (var i = 0; i < allloanPdcts.length; i++) {

                                                var currentLoanpdt = allloanPdcts[i];
                                                // console.log(loanProductId+" Loading *** "+i+" Check"+ new Number(currentLoanpdt.id) +" @"+ new Number(loanProductId)+" Result "+(currentLoanpdt.id == loanProductId));
                                                if (currentLoanpdt.id == loanProductId) {
                                                    //
                                                    var allDisbursementMtds = currentLoanpdt.disbursement_mtds;
                                                    console.log(allDisbursementMtds + " &*&& ");
                                                    var html = "";
                                                    for (var r = 0; r < allDisbursementMtds.length; r++) {
                                                        html += "<option value='" + allDisbursementMtds[r] + "' >" + allDisbursementMtds[r] + "</option>";
                                                    }
                                                    $("#inputDisbursedById").html(html);
                                                    // console.log("Showing " + html);

                                                    //Show Principal Amount
                                                    $("#inputLoanPrincipalAmount").val(currentLoanpdt.default_principal_amount);
                                                    $("#inputLoanPrincipalAmount").attr("max", currentLoanpdt.max_principal_amount);
                                                    $("#inputLoanPrincipalAmount").attr("min", currentLoanpdt.minimum_principal);
                                                    // Min 7521 - 10000 Max

                                                    $("#inputLoanInterestMethod").html("<option value='" + currentLoanpdt.interest_mtd + "'>" + currentLoanpdt.interest_mtd + "</option>");

                                                    $("#inputLoanInterest").val(currentLoanpdt.default_interest);
                                                    $("#inputLoanInterest").attr("max", currentLoanpdt.max_interest);
                                                    $("#inputLoanInterest").attr("min", currentLoanpdt.mini_interest);

                                                    $("inputInterestPeriod").html("<option value='" + currentLoanpdt.default_interest_pd + "'>Per " + currentLoanpdt.default_interest_pd + "</option>");
                                                    var newHtml = "";
                                                    var miniDuratin = currentLoanpdt.min_duration;
                                                    var maxDuratin = currentLoanpdt.max_duration;
                                                    for (var mm = miniDuratin; mm < (maxDuratin + 1); mm++) {
                                                        newHtml += "<option value='" + mm + "'>" + mm + "</option>";
                                                    }
                                                    $("#inputLoanDuration").html(newHtml);
                                                    $("#inputLoanDurationPeriod").html("<option value='" + currentLoanpdt.default_duration_pd + "'>Per " + currentLoanpdt.default_duration_pd + "</option>");
                                                    var cycleHtml = "";
                                                    // console.log(currentLoanpdt.repayment_cycle);
                                                    for (var k = 0; k < currentLoanpdt.repayment_cycle.length; k++) {
                                                        cycleHtml += "<option value='" + currentLoanpdt.repayment_cycle[k] + "'>" + currentLoanpdt.repayment_cycle[k] + "</option>";
                                                    }
                                                    //  console.log(cycleHtml+" ****");
                                                    $("#inputLoanPaymentSchemeId").html(cycleHtml);

                                                    var installMentsHtml = "";
                                                    var miniInstall = currentLoanpdt.mini_no_repayments;
                                                    var maxInstall = currentLoanpdt.max_no_repayments;
                                                    var defaultInstall = currentLoanpdt.default_no_repayments;
                                                    for (mm = miniInstall; mm < (maxInstall + 1); mm++) {
                                                        var sel = mm == defaultInstall ? "selected='selected'" : "";
                                                        installMentsHtml += "<option value='" + mm + "' " + sel + ">" + mm + "</option>";
                                                    }

                                                    $("#inputLoanNumOfRepayments").html(installMentsHtml);

                                                    break;
                                                }
                                            }
                                        }
        </script>

    </body>
</html>