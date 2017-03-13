<?php
$borrowerID = (int) $_GET['borrower'];
$borrower = [];
include '../helpers/DbAcess.php';
include '../helpers/AppUtil.php';
$db = new DbAcess();
if (is_numeric($borrowerID)) {

    $table = 'borrower';
    $borrower = $db->select($table, [], ['id' => $borrowerID]);
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

            <?php include '../main_menu.php'; ?>

            <!-- Content Wrapper. Contains page content -->
            <div class="content-wrapper">
                <!-- Content Header (Page header) -->
                <section class="content-header"><h1><?= isset($borrower['fname']) ? $borrower['title'] . ' ' . $borrower['fname'] . ' ' . $borrower['lname'] : "" ?>- View All Loans</h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div class="box box-widget">
                        <div class="box-header with-border">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="user-block"> <!-- ../load_image.php?id=<?= $borrower['id'] . '&item=borrower' ?> -->
                                        <img class="img-circle" src="data:<?= $borrower['field1'] ?>;base64,<?= base64_encode($borrower['photo']) ?>" alt="user image">
                                        <span class="username">
                                            <?= $borrower['title'] . ' ' . $borrower['fname'] . ' ' . $borrower['lname'] ?>
                                        </span>
                                        <span class="description" style="font-size:13px; color:#000000">
                                            <a href="../borrowers/groups/view_group_details.php">MAZAS</a><br>4556666<br>
                                            <a href="../borrowers/add_borrower_edit.php">Edit</a><br>
                                            <?= $borrower['business_name'] ?><br><?= $borrower['gender'] . ', ' ?>
                                            <?php
                                            echo date_diff(date_create($borrower['dob']), date_create('today'))->y;
                                            ?>
                                        </span>
                                    </div><!-- /.user-block --><div class="btn-group-horizontal"><a type="button" class="btn bg-olive margin" href="add_loan.php?borrower=<?= $borrower['id'] ?>">Add Loan</a></div>         
                                </div><!-- /.col -->
                                <div class="col-sm-4">
                                    <ul class="list-unstyled">
                                        <li><b>Address:</b> <?= $borrower['address'] ?></li>
                                        <li><b>City:</b> <?= $borrower['city'] ?></li>
                                        <li><b>Province:</b> <?= $borrower['province_state'] ?></li>
                                        <li><b>Zipcode:</b> <?= $borrower['zipcode'] ?></li>

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
                                        <li><b>Landline:</b> <?= $borrower['landline'] ?></li>
                                        <li><b>Email:</b> <a onClick="javascript:window.open('mailto:<?= $borrower['email'] ?>', 'mail');
                                                event.preventDefault()" href="mailto::<?= $borrower['email'] ?>">:<?= $borrower['email'] ?></a>
                                            <div class="btn-group-horizontal"><a type="button" class="btn-xs bg-red" href="https://x.loandisk.com/borrowers/send_email_to_borrower.php?borrower_id=10419">Send Email</a></div></li>
                                        <li><b>Mobile:</b> :<?= $borrower['mobile_no'] ?><div class="btn-group-horizontal"><a type="button" class="btn-xs bg-red" href="https://x.loandisk.com/borrowers/send_sms_to_borrower.php?borrower_id=10419">Send SMS</a></div></li>

                                    </ul>
                                </div>
                            </div><!-- /.row -->
                        </div> 
                    </div>
                    <!-- Main content -->
                    <div class="box box-warning">
                        <div class="box-body table-responsive no-padding">
                            <table class="table table-bordered table-condensed  table-hover" id="table_allLoans">
                                <thead>
                                    <tr style="background-color: #FFF8F2">

                                        <th>Loan #</th>
                                        <th><b>Released</b></th>
                                        <th><b>Maturity</b></th>
                                        <th><b>Repayment</b></th>
                                        <th><b>Principal &#85;&#83;&#104;</b></th>

                                        <th>Fees</th>
                                        <th>Penalty</th>
                                        <th><b>Due</b></th>
                                        <th><b>Paid</b></th>
                                        <th><b>Balance</b></th>


                                        <th><b>Status</b></th>

                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //get Loans for the Borrower.
                                    $where = [
                                        "borrower_id" => $borrowerID,
                                        "status" => "!closed"
                                    ];
                                    $loans = $db->select("loans", [], $where);
                                    foreach ($loans as $loan) {
                                        $loanFees=$db->select("loan_applied_charges", [],["loan_id"=>$loan['id']]);
                                        ?>
                                        <tr>
                                            <td>
                                                <?= $loan['loan_no'] ?>
                                            </td>
                                            <td>
                                                <?= $loan['release_date'] ?>
                                            </td>
                                            <td>
                                               <?= $loan['release_date'] ?>
                                            </td>
                                            <td>
                                                <?= $loan['repayment_cycle'] ?>
                                            </td> 
                                            <td>
                                                777.00<br>@1% Per Day
                                            </td>
                                            <td>
                                                <?php
                                                $sumFess=0;
                                                foreach ($loanFees as $fee) {
                                                    $sumFess+=$fee['amount'];
                                                }
                                                echo $sumFess;
                                                ?>                                              
                                            </td>
                                            <td>
                                                0
                                            </td>                                 
                                            <td><s>785.77</s><br>6,233.00
                                    </td>
                                    <td>
                                        0
                                    </td>
                                    <td>
                                        <b>6,233.00</b>
                                    </td>
                                    <td>
                                        <span class="label label-info">
                                             <?= $loan['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_loan_details.php?loan_id= <?= $loan['id'] ?>" type="button" class="btn-xs bg-olive margin">View/Modify</a>
                                    </td>
                                    </tr>
                                    <?php
                                }
                                ?>


                                </tbody>

                            </table>

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