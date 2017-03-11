

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
                <section class="content-header"><h1>View Borrowers<small><a href="" target="_blank">Help</a></small></h1>
                </section>

                <!-- Main content -->
                <section class="content">
     <div class="box-body">
        <div class="box box-success">
            <form action = "" class="form-horizontal" method="get"  enctype="multipart/form-data">
                <input type="hidden" name="search_borrowers" value="1">
                <div class="box-body">
                  <div class="row">
                    <div class="col-xs-6">
                      <input type="text" class="form-control" name="fullname" placeholder="Borrower Name or Unique Number" value="" required>
                    </div>
                   
                    <div class="col-xs-1">
                        <span class="input-group-btn">
                          <button type="submit" class="btn bg-olive btn-flat">Search!</button>
                        </span>

                        <span class="input-group-btn">
        <button type="button" class="btn bg-purple  btn-flat" onClick="parent.location='view_borrowers_branch.html'">Reset!</button>
                        </span>
                    </div>
                  </div>
                </div><!-- /.box-body -->
            </form>
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

                        <span class="input-group-btn">
                          <button type="button" class="btn bg-purple  btn-flat" onClick="parent.location='view_borrowers_branch.html'">Reset!</button>
                        </span>
                    </div>
                  </div>
                </div><!-- /.box-body -->
            </form>
          </div><!-- /.box -->
      </div>
            <div class="row margin-bottom">
                <div class="col-xs-6">
                    Search found <b>3 results</b>
                </div>
                <div class="col-xs-6">
                    <div class="pull-right">
                        <div class="btn-group-horizontal"><a type="button" class="btn btn-block btn-info btn-xs" href="filter_borrowers_branch_columns.html">Show/Hide Columns</a></div>
                    </div>
                </div>
            </div>                       
            <div class="box box-info">
                <div class="box-body table-responsive no-padding">
                    <table class="table table-bordered table-condensed table-hover">
                        <tr style="background-color: #F2F8FF">
                          <th>
                              <b>Full Name</b>
                          </th>
                          <th>
                              <b>Unique Number</b>
                          </th>
                          <th>
                              <b>Mobile</b>
                          </th>
                          <th>
                              <b>City</b>
                          </th>
                          <th>View </th>
                          <th>Loan Status</th>
                          <th>Borrower</th>
                       </tr>
                        <tr>
                                  <td>
                                      <b>Rev.  Damus Kumi<br><a href="groups/view_group_details.html">MAZAS</a></b>
                                  </td>
                                  <td>
                                      4556666
                                  </td>
                                  <td>
                                      4588888
                                  </td>
                                  <td>
                                      dfgfhjlk
                                  </td>
                              <td><div class="btn-group-horizontal">
<a type="button" class="btn-xs bg-olive" href="../loans/view_loans_borrower.html">Loans</a>
<a type="button" class="btn-xs bg-blue margin" href="../savings/view_savings_borrower.html">Savings</a></div>
                                </td>
                                <td>
                                    <b>Processing</b>
                                </td>
                                <td><div class="btn-group-horizontal">
 <a type="button" class="btn bg-white btn-xs text-bold" href="add_borrower_edit.html">Edit</a>
 <a type="button" class="btn bg-white btn-xs text-bold" href="delete_borrower.html" onClick="javascript:return confirm('Are you sure you want to Delete?')">Delete</a></div>
                                </td>
                            </tr>
                        <tr>
                                  <td>
                                      <b>Ms.  Juma mIKE<br><a href="groups/view_group_details3.html">Japehtsss</a></b>
                                  </td>
                                  <td>
                                      55999
                                  </td>
                                  <td>
                                      88899
                                  </td>
                                  <td>
                                      FGGBH
                                  </td>
                              <td><div class="btn-group-horizontal">
                                <a type="button" class="btn-xs bg-olive" href="#">Loans</a>
                                <a type="button" class="btn-xs bg-blue margin" href="#">Savings</a></div>
                                </td>
                                <td>
                                    <b></b>
                                </td>
                                <td><div class="btn-group-horizontal">
                                    <a type="button" class="btn bg-white btn-xs text-bold" href="">Edit</a>
                                    <a type="button" class="btn bg-white btn-xs text-bold" href="" onClick="javascript:return confirm('Are you sure you want to Delete?')">Delete</a></div>
                                </td>
                            </tr>
                        <tr>
                                  <td>
                                      <b>Dr.  Kulungi Moses<br></b>
                                  </td>
                                  <td>
                                      47586
                                  </td>
                                  <td>
                                      
                                  </td>
                                  <td>
                                      tghyjujki
                                  </td>
                              <td><div class="btn-group-horizontal"><a type="button" class="btn-xs bg-olive" href="">Loans</a><a type="button" class="btn-xs bg-blue margin" href="#">Savings</a></div>
                                </td>
                                <td>
                                    <b></b>
                                </td>
                                <td><div class="btn-group-horizontal"><a type="button" class="btn bg-white btn-xs text-bold" href="#">Edit</a><a type="button" class="btn bg-white btn-xs text-bold" href="#" onClick="javascript:return confirm('Are you sure you want to Delete?')">Delete</a></div>
                                </td>
                            </tr>
                    </table>
                    
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