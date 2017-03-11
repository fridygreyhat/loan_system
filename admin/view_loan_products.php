<?php
session_start();
include '../helpers/DbAcess.php';
//include '../helpers/AppUtil.php';
$db = new DbAcess();
//select All Loan Products.....
$loanProducts = $db->select("loan_product");
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
                <section class="content-header"><h1>Loan Products<small><a href="" target="_blank">Help</a></small></h1>
                </section>

                <!-- Main content -->
                <section class="content"><small><a href="index.html">Back to Admin</a></small>
                    <div class="box box-info">
                        <div class="box-body table-responsive"><div class="btn-group-horizontal"><a type="button" class="btn bg-olive margin-bottom" href="add_loan_product.html">Add Loan Product</a></div>

                            <table class="table table-bordered">
                                <tr class="bg-gray">
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                <?php  
                                foreach ($loanProducts as $loan) {
                                    ?>
                              <tr>
                                    <td><?=$loan['name']?></td>
                                    <td><?=$loan['active_flg']==1?"Active":"Deactivated"?></td>
                                    <td>
                                        <div class="btn-group-vertical">
                                            <a type="button" class="btn btn-xs btn-default btn-block" href="add_loan_product_edit.php?id=<?=$loan['id']?>">Edit</a>
                                            <a type="button" class="btn btn-xs btn-default btn-block" href="loan_product_action_delete.html" onClick="javascript:return confirm('Are you sure you want to Delete?')">Delete</a>
                                        </div>
                                    </td>
                                </tr>   
                                    
                             <?php   }                               
                                ?>
                            
                            </table>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->

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