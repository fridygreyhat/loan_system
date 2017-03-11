<?php
$table = 'borrower';
include '../helpers/DbAcess.php';
$db = new DbAcess();
$allBorrowers = $db->select($table);
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
                <section class="content-header"><h1>View Borrowers<small><a href="" target="_blank">Help</a></small></h1>
                </section>

                <!-- Main content -->
                <section class="content">

                    <div class="box box-success">
                        <form action="" class="form-horizontal" method="get"  enctype="multipart/form-data">
                            <input type="hidden" name="search_borrowers" value="1">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <select class="form-control" name="borrower_type" id="inputStatusId">
                                            <option value="">All Borrowers</option>
                                            <option value="Open">Borrowers with open loan</option>  
                                            <option value="MissedRepayment">Missed Repayment</option>
                                            <option value="PastMaturity">Past Maturity</option>
                                            <option value="FullyPaid">Fully Paid loan</option>
                                            <option value="Default">Default loan</option>
                                            <option value="Fraud">Fraud</option>
                                            <option value="Notactive">No Open loans</option>
                                        </select>
                                    </div>

                                    <div class="col-xs-1">
                                        <span class="input-group-btn">
                                            <button type="submit" class="btn bg-olive btn-flat">Search!</button>
                                        </span>
                                    </div>
                                    <div class="col-xs-1">
                                        <button type="button" class="btn bg-purple  btn-flat" onClick="parent.location = 'view_borrowers_branch.php'">Reset!</button>
                                        </span>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->
                        </form>
                    </div><!-- /.box -->
                    <div class="row margin-bottom">
                        <div class="col-xs-6">
                            <div class="pull-left">
                                <div id="export_button">
                                    <small>(Select <b>All</b> in <b>Show entries</b> below to export all data)</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-6">
                            <div class="pull-right">
                                <div class="btn-group-horizontal"><a type="button" class="btn btn-block btn-default" href="https://x.loandisk.com/borrowers/filter_borrowers_branch_columns.php">Show/Hide Columns</a></div>
                            </div>
                        </div>
                    </div>              
                    <div class="box box-info">
                        <div class="box-body">
                            <table id="borrowers-list" class="table table-bordered table-condensed table-hover dataTable">
                                <thead>
                                    <tr style="background-color: #D1F9FF">
                                        <th>
                                            Full Name
                                        </th>
                                        <th>
                                            Unique#
                                        </th>
                                        <th>
                                            Mobile
                                        </th>
                                        <th>
                                            City
                                        </th>
                                        <th>Status</th>
                                        <th class="noExport">View</th>
                                        <th class="noExport">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allBorrowers as $value) {
                                        ?>
                                        <tr>
                                            <td><?= $value['fname'] . ' ' . $value['lname'] ?></td>
                                            <td><?= $value['unique_no'] ?></td>
                                            <td><?= $value['mobile_no'] ?></td>
                                            <td><?= $value['city'] ?></td>
                                            <td></td>
                                            <td>
                                                <div class="btn-group-horizontal">
                                                <a type="button" class="btn-xs bg-olive" href="../loans/view_loans_borrower.php?borrower=<?=$value['id']?>">Loans</a>
                                                <a type="button" class="btn-xs bg-blue margin" href="../savings/view_savings_borrower.php">Savings</a>
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <script src="../plugins/datatables/jquery.dataTables.min.js"></script>
                    <script src="../plugins/datatables/dataTables.bootstrap.min.js"></script>


                    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/scroller/1.4.2/js/dataTables.scroller.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js">
                    </script>
                    <script type="text/javascript" language="javascript" src="//cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js">
                    </script>


                    <script type="text/javascript" language="javascript">
                        $(document).ready(function() {
                        var dataTable = $('#borrowers-list').DataTable({
                        "autoWidth": true,
                                "drawCallback": function(settings) {
                                $("#borrowers-list").wrap("<div class='table-responsive'></div>");
                                },
                                "lengthMenu": [[20, 50, 100, 250, 500, - 1], [20, 50, 100, 250, 500, "All (Slow)"]],
                                "iDisplayLength": 50,
                                "processing": true,
                                "serverSide": true,
                                "responsive": true,
                                "language": {
                                "processing": "<img src='https://x.loandisk.com/images/ajax-loader.gif'> Processing, please wait.."
                                },
                                "columnDefs": [ {
                                "targets": [ - 1, - 2, - 3], // column or columns numbers
                                        "orderable": false, // set orderable for selected columns

                                }]
                                /*,
                                 "ajax":{
                                 url :"https://x.loandisk.com/borrowers/process_borrowers_branch.php?page_current=view_borrowers", // json datasource
                                 type: "post",  // method  , by default get
                                 error: function(){  // error handling
                                 $(".borrowers-list-error").html("");
                                 $("#borrowers-list").append('<tbody class="borrowers-list-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                 $("#borrowers-list-processing").css("display","none");
                                 
                                 }
                                 },*/
                                scrollY:        400,
                                scroller:       true
                        });
                                var buttons = new $.fn.dataTable.Buttons(dataTable, {
                                "buttons": [
                                {
                                extend: 'collection',
                                        text: 'Export Data',
                                        buttons: [
                                                'copy',
                                                'excel',
                                                'csv',
                                                'print'
                                        ]
                                }
                                ]
                                }).container().appendTo($('#export_button'));
                        });</script>

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