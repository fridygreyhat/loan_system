<?php
session_start();
if (!empty($_POST) && isset($_POST['borrower_firstname'])) {
    include '../helpers/DbAcess.php';
    include '../helpers/AppUtil.php';
    $db = new DbAcess();

    $user_picture_name = $_FILES['user_picture']['name'];
    $user_picture_TempName = $_FILES['user_picture']['tmp_name'];
    $user_picture_size = $_FILES['user_picture']['size'];
    $user_picture_Type = $_FILES['user_picture']['type'];

    $fp = fopen($user_picture_TempName, 'r');
    $content = fread($fp, filesize($user_picture_TempName));
    $content = addslashes($content);
    fclose($fp);

    if (!get_magic_quotes_gpc()) {
        $user_picture_name = addslashes($user_picture_name);
    }
    
    //save the file into the folder
    $saveFile=  AppUtil::saveFile($user_picture_TempName, $user_picture_name);
    
    $table = 'borrower';
    $data = [
        'fname' => $_POST['borrower_firstname'], 'lname' => $_POST['borrower_lastname'],
        'gender' => $_POST['borrower_gender'], 'country' => $_POST['borrower_country'],
        'title' => $_POST['borrower_title'], 'mobile_no' => $_POST['borrower_mobile'],
        'email' => $_POST['borrower_email'], 'unique_no' => $_POST['borrower_unique_number'],
        'dob' => $_POST['borrower_dob'], 'address' => $_POST['borrower_address'],
        'city' => $_POST['borrower_city'], 'province_state' => $_POST['borrower_province'],
        'zipcode' => $_POST['borrower_zipcode'], 'landline' => $_POST['borrower_landline'],
        'business_name' => $_POST['borrower_business_name'],
        'working_status' => $_POST['borrower_working_status'], 'photo' => $content,
        'description' => $_POST['borrower_description'], 'staff_id' => 0,
        'creation_user' => 0, 'last_modified_by' => 0,'field1'=>$user_picture_Type,'field2'=>$saveFile
    ];
    $insertId = $db->insert($table, $data);
    if (is_int($insertId)) {
        //Inserted Successfully *****
        //get all the other files if available
        $allOtherFiles = $_FILES['borrower_files'];
        if (!empty($allOtherFiles)) {
            // print_r($allOtherFiles);
            $namesArray = $allOtherFiles['name'];
            $tempNameArray = $allOtherFiles['tmp_name'];
            $typeArray = $allOtherFiles['type'];
            $ii=0;
            foreach ($tempNameArray as $value) {
                $fph = fopen($value, 'r');
                $contenth = fread($fph, filesize($value));
                $contentLast = addslashes($contenth);
                fclose($fph);
                
                   $saveFileH=  AppUtil::saveFile($tempNameArray[$ii],$namesArray[$ii]);
                
                $tableHer='borrower_files';
                
                $datahere=[
                    'borrower_id'=>$insertId,
                    'name'=>$namesArray[$ii],
                    'content'=>$contentLast,
                    'creation_user'=>0,
                    'field1'=>$typeArray[$ii],
                    'filepath'=>$saveFileH
                ];
                $insertIdHere=$db->insert($tableHer, $datahere);
               // echo $insertIdHere.'`******1</br>';
                
                $ii++;
            }
        }
        //redirect to all Borrowers.
        header("location:view_borrowers_branch2.php");
        
    } else {
        echo '**** InsertFailed With ******</br>';
        print_r($insertId);
    }
    // print_r($insertId);
    // echo '**** Insert With ******';
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
                <section class="content-header"><h1>Add Borrower<small><a href="" target="_blank">Help</a></small></h1>
                </section>
                <!-- Main content -->
                <section class="content">
                    <div class="box box-info">
                        <form action ="" class="form-horizontal" method="post"   id="add_borrower_form" enctype="multipart/form-data">
                            <input type="hidden" name="back_url" value="">
                            <input type="hidden" name="add_borrower" value="1">        
                            <div class="box-body">
                                <p class="bg-navy disabled color-palette">Required Fields</p>  
                                <div class="form-group">
                                    <label for="inputBorrowerFirstName" class="col-sm-2 control-label">First Name</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_firstname" class="form-control" id="inputBorrowerFirstName" placeholder="Enter First Name Only" value="" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerLastName" class="col-sm-2 control-label">Middle / Last Name</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_lastname" class="form-control" id="inputBorrowerLastName" placeholder="Middle and Last Name" value="" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputBorrowerGender" class="col-sm-2 control-label">Gender</label>                      
                                    <div class="col-sm-2">
                                        <select class="form-control" name="borrower_gender" required>
                                            <option value="Male" />Male</option>
                                            <option  value="Female" />Female</option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">

                                    <label for="inputCountry" class="col-sm-2 control-label">Country</label>                      
                                    <div class="col-sm-4">
                                        <select class="form-control" name="borrower_country" id="inputCountry" required>
                                            <option value="" selected></option>
                                            <option value="AF" >Afghanistan</option>
                                            <option value="AX" >Aland Islands</option>
                                            <option value="AL" >Albania</option>
                                            <option value="DZ" >Algeria</option>
                                            <option value="AS" >American Samoa</option>
                                            <option value="AD" >Andorra</option>
                                            <option value="AO" >Angola</option>
                                            <option value="AI" >Anguilla</option>
                                            <option value="AQ" >Antarctica</option>
                                            <option value="AG" >Antigua and Barbuda</option>
                                            <option value="AR" >Argentina</option>
                                            <option value="AM" >Armenia</option>
                                            <option value="AW" >Aruba</option>
                                            <option value="AU" >Australia</option>
                                            <option value="AT" >Austria</option>
                                            <option value="AZ" >Azerbaijan</option>
                                            <option value="BS" >Bahamas</option>
                                            <option value="BH" >Bahrain</option>
                                            <option value="BD" >Bangladesh</option>
                                            <option value="BB" >Barbados</option>
                                            <option value="BY" >Belarus</option>
                                            <option value="BE" >Belgium</option>
                                            <option value="BZ" >Belize</option>
                                            <option value="BJ" >Benin</option>
                                            <option value="BM" >Bermuda</option>
                                            <option value="BT" >Bhutan</option>
                                            <option value="BO" >Bolivia</option>
                                            <option value="BQ" >Bonaire</option>
                                            <option value="BA" >Bosnia and Herzegovina</option>
                                            <option value="BW" >Botswana</option>
                                            <option value="BV" >Bouvet Island</option>
                                            <option value="BR" >Brazil</option>
                                            <option value="IO" >British Indian Ocean Territory</option>
                                            <option value="BN" >Brunei Darussalam</option>
                                            <option value="BG" >Bulgaria</option>
                                            <option value="BF" >Burkina Faso</option>
                                            <option value="BI" >Burundi</option>
                                            <option value="KH" >Cambodia</option>
                                            <option value="CM" >Cameroon</option>
                                            <option value="CA" >Canada</option>
                                            <option value="CV" >Cape Verde</option>
                                            <option value="KY" >Cayman Islands</option>
                                            <option value="CF" >Central African Republic</option>
                                            <option value="TD" >Chad</option>
                                            <option value="CL" >Chile</option>
                                            <option value="CN" >China</option>
                                            <option value="CX" >Christmas Island</option>
                                            <option value="CC" >Cocos (Keeling) Islands</option>
                                            <option value="CO" >Colombia</option>
                                            <option value="KM" >Comoros</option>
                                            <option value="CG" >Congo</option>
                                            <option value="CD" >Congo</option>
                                            <option value="CK" >Cook Islands</option>
                                            <option value="CR" >Costa Rica</option>
                                            <option value="CI" >Cote dIvoire</option>
                                            <option value="HR" >Croatia</option>
                                            <option value="CU" >Cuba</option>
                                            <option value="CY" >Cyprus</option>
                                            <option value="CZ" >Czech Republic</option>
                                            <option value="DK" >Denmark</option>
                                            <option value="DJ" >Djibouti</option>
                                            <option value="DM" >Dominica</option>
                                            <option value="DO" >Dominican Republic</option>
                                            <option value="EC" >Ecuador</option>
                                            <option value="EG" >Egypt</option>
                                            <option value="SV" >El Salvador</option>
                                            <option value="GQ" >Equatorial Guinea</option>
                                            <option value="ER" >Eritrea</option>
                                            <option value="EE" >Estonia</option>
                                            <option value="ET" >Ethiopia</option>
                                            <option value="FK" >Falkland Islands (Malvinas)</option>
                                            <option value="FO" >Faroe Islands</option>
                                            <option value="FJ" >Fiji</option>
                                            <option value="FI" >Finland</option>
                                            <option value="FR" >France</option>
                                            <option value="GF" >French Guiana</option>
                                            <option value="PF" >French Polynesia</option>
                                            <option value="TF" >French Southern Territories</option>
                                            <option value="GA" >Gabon</option>
                                            <option value="GM" >Gambia</option>
                                            <option value="GE" >Georgia</option>
                                            <option value="DE" >Germany</option>
                                            <option value="GH" >Ghana</option>
                                            <option value="GI" >Gibraltar</option>
                                            <option value="GR" >Greece</option>
                                            <option value="GL" >Greenland</option>
                                            <option value="GD" >Grenada</option>
                                            <option value="GP" >Guadeloupe</option>
                                            <option value="GU" >Guam</option>
                                            <option value="GT" >Guatemala</option>
                                            <option value="GG" >Guernsey</option>
                                            <option value="GN" >Guinea</option>
                                            <option value="GW" >Guinea-Bissau</option>
                                            <option value="GY" >Guyana</option>
                                            <option value="HT" >Haiti</option>
                                            <option value="HM" >Heard Island and McDonald Islands</option>
                                            <option value="VA" >Holy See (Vatican City State)</option>
                                            <option value="HN" >Honduras</option>
                                            <option value="HK" >Hong Kong</option>
                                            <option value="HU" >Hungary</option>
                                            <option value="IS" >Iceland</option>
                                            <option value="IN" >India</option>
                                            <option value="ID" >Indonesia</option>
                                            <option value="IR" >Iran, Islamic Republic of</option>
                                            <option value="IQ" >Iraq</option>
                                            <option value="IE" >Ireland</option>
                                            <option value="IM" >Isle of Man</option>
                                            <option value="IL" >Israel</option>
                                            <option value="IT" >Italy</option>
                                            <option value="JM" >Jamaica</option>
                                            <option value="JP" >Japan</option>
                                            <option value="JE" >Jersey</option>
                                            <option value="JO" >Jordan</option>
                                            <option value="KZ" >Kazakhstan</option>
                                            <option value="KE" >Kenya</option>
                                            <option value="KI" >Kiribati</option>
                                            <option value="KP" >Korea, Democratic People's Republic of</option>
                                            <option value="KR" >Korea, Republic of</option>
                                            <option value="KW" >Kuwait</option>
                                            <option value="KG" >Kyrgyzstan</option>
                                            <option value="LA" >Lao PDR</option>
                                            <option value="LV" >Latvia</option>
                                            <option value="LB" >Lebanon</option>
                                            <option value="LS" >Lesotho</option>
                                            <option value="LR" >Liberia</option>
                                            <option value="LY" >Libya</option>
                                            <option value="LI" >Liechtenstein</option>
                                            <option value="LT" >Lithuania</option>
                                            <option value="LU" >Luxembourg</option>
                                            <option value="MO" >Macao</option>
                                            <option value="MK" >Macedonia, Republic of</option>
                                            <option value="MG" >Madagascar</option>
                                            <option value="MW" >Malawi</option>
                                            <option value="MY" >Malaysia</option>
                                            <option value="MV" >Maldives</option>
                                            <option value="ML" >Mali</option>
                                            <option value="MT" >Malta</option>
                                            <option value="MH" >Marshall Islands</option>
                                            <option value="MQ" >Martinique</option>
                                            <option value="MR" >Mauritania</option>
                                            <option value="MU" >Mauritius</option>
                                            <option value="YT" >Mayotte</option>
                                            <option value="MX" >Mexico</option>
                                            <option value="FM" >Micronesia, Federated States of</option>
                                            <option value="MD" >Moldova, Republic of</option>
                                            <option value="MC" >Monaco</option>
                                            <option value="MN" >Mongolia</option>
                                            <option value="ME" >Montenegro</option>
                                            <option value="MS" >Montserrat</option>
                                            <option value="MA" >Morocco</option>
                                            <option value="MZ" >Mozambique</option>
                                            <option value="MM" >Myanmar</option>
                                            <option value="NA" >Namibia</option>
                                            <option value="NR" >Nauru</option>
                                            <option value="NP" >Nepal</option>
                                            <option value="NL" >Netherlands</option>
                                            <option value="NC" >New Caledonia</option>
                                            <option value="NZ" >New Zealand</option>
                                            <option value="NI" >Nicaragua</option>
                                            <option value="NE" >Niger</option>
                                            <option value="NG" >Nigeria</option>
                                            <option value="NU" >Niue</option>
                                            <option value="NF" >Norfolk Island</option>
                                            <option value="MP" >Northern Mariana Islands</option>
                                            <option value="NO" >Norway</option>
                                            <option value="OM" >Oman</option>
                                            <option value="PK" >Pakistan</option>
                                            <option value="PW" >Palau</option>
                                            <option value="PS" >Palestine</option>
                                            <option value="PA" >Panama</option>
                                            <option value="PG" >Papua New Guinea</option>
                                            <option value="PY" >Paraguay</option>
                                            <option value="PE" >Peru</option>
                                            <option value="PH" >Philippines</option>
                                            <option value="PN" >Pitcairn</option>
                                            <option value="PL" >Poland</option>
                                            <option value="PT" >Portugal</option>
                                            <option value="PR" >Puerto Rico</option>
                                            <option value="QA" >Qatar</option>
                                            <option value="RE" >Reunion</option>
                                            <option value="RO" >Romania</option>
                                            <option value="RU" >Russian Federation</option>
                                            <option value="RW" >Rwanda</option>
                                            <option value="BL" >Saint Barthelemy</option>
                                            <option value="SH" >Saint Helena, Ascension and Tristan da Cunha</option>
                                            <option value="KN" >Saint Kitts and Nevis</option>
                                            <option value="LC" >Saint Lucia</option>
                                            <option value="MF" >Saint Martin (French part)</option>
                                            <option value="PM" >Saint Pierre and Miquelon</option>
                                            <option value="VC" >Saint Vincent and the Grenadines</option>
                                            <option value="WS" >Samoa</option>
                                            <option value="SM" >San Marino</option>
                                            <option value="ST" >Sao Tome and Principe</option>
                                            <option value="SA" >Saudi Arabia</option>
                                            <option value="SN" >Senegal</option>
                                            <option value="RS" >Serbia</option>
                                            <option value="SC" >Seychelles</option>
                                            <option value="SL" >Sierra Leone</option>
                                            <option value="SG" >Singapore</option>
                                            <option value="SX" >Sint Maarten (Dutch part)</option>
                                            <option value="SK" >Slovakia</option>
                                            <option value="SI" >Slovenia</option>
                                            <option value="SB" >Solomon Islands</option>
                                            <option value="SO" >Somalia</option>
                                            <option value="ZA" >South Africa</option>
                                            <option value="GS" >South Georgia and the South Sandwich Islands</option>
                                            <option value="SS" >South Sudan</option>
                                            <option value="ES" >Spain</option>
                                            <option value="LK" >Sri Lanka</option>
                                            <option value="SD" >Sudan</option>
                                            <option value="SR" >Suriname</option>
                                            <option value="SJ" >Svalbard and Jan Mayen</option>
                                            <option value="SZ" >Swaziland</option>
                                            <option value="SE" >Sweden</option>
                                            <option value="CH" >Switzerland</option>
                                            <option value="SY" >Syrian Arab Republic</option>
                                            <option value="TW" >Taiwan</option>
                                            <option value="TJ" >Tajikistan</option>
                                            <option value="TZ" >Tanzania, United Republic of</option>
                                            <option value="TH" >Thailand</option>
                                            <option value="TL" >Timor-Leste</option>
                                            <option value="TG" >Togo</option>
                                            <option value="TK" >Tokelau</option>
                                            <option value="TO" >Tonga</option>
                                            <option value="TT" >Trinidad and Tobago</option>
                                            <option value="TN" >Tunisia</option>
                                            <option value="TR" >Turkey</option>
                                            <option value="TM" >Turkmenistan</option>
                                            <option value="TC" >Turks and Caicos Islands</option>
                                            <option value="TV" >Tuvalu</option>
                                            <option value="UG" selected>Uganda</option>
                                            <option value="UA" >Ukraine</option>
                                            <option value="AE" >United Arab Emirates</option>
                                            <option value="GB" >United Kingdom</option>
                                            <option value="US" >United States</option>
                                            <option value="UM" >United States Minor Outlying Islands</option>
                                            <option value="UY" >Uruguay</option>
                                            <option value="UZ" >Uzbekistan</option>
                                            <option value="VU" >Vanuatu</option>
                                            <option value="VE" >Venezuela, Bolivarian Republic of</option>
                                            <option value="VN" >Vietnam</option>
                                            <option value="VG" >Virgin Islands, British</option>
                                            <option value="VI" >Virgin Islands, U.S.</option>
                                            <option value="WF" >Wallis and Futuna</option>
                                            <option value="EH" >Western Sahara</option>
                                            <option value="YE" >Yemen</option>
                                            <option value="ZM" >Zambia</option>
                                            <option value="ZW" >Zimbabwe</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="bg-navy disabled color-palette">Optional Fields</p>
                                <div class="form-group">
                                    <label for="inputBorrowerTitle" class="col-sm-2 control-label">Title</label>                      
                                    <div class="col-sm-2">
                                        <select class="form-control" name="borrower_title" id="inputBorrowerTitle">
                                            <option value="0" ></option>
                                            <option value="Mr." >Mr. </option>
                                            <option value="Mrs." >Mrs. </option>
                                            <option value="Miss" >Miss </option>
                                            <option value="Ms." >Ms. </option>
                                            <option value="Dr." >Dr. </option>
                                            <option value="Prof." >Prof. </option>
                                            <option value="Rev." >Rev. </option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="inputBorrowerMobile" class="col-sm-2 control-label">Mobile</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_mobile" class="form-control" id="inputBorrowerMobile" placeholder="Numbers Only" value="" onkeypress="return isNumberKey(event)">
                                        <p><b><u>Do not</u> put country code, spaces, or characters</b> in mobile otherwise you won't be able to send SMS to this mobile.</b></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputBorrowerEmail" class="col-sm-2 control-label">Email</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_email" class="form-control" id="inputBorrowerEmail" placeholder="Email" value="">
                                    </div>
                                </div>
                                <div class="form-group">

                                    <label for="inputBorrowerUniqueNumber" class="col-sm-2 control-label">Unique Number</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_unique_number" class="form-control" id="inputBorrowerUniqueNumber" placeholder="Unique Number" value="">
                                        <p>You can enter unique number to identify the borrower such as Social Security Number, License #, Registration Id....</p>
                                    </div>
                                </div>

                                <script>
                                    $(function () {
                                        $('#inputBorrowerDob').datepick({
                                            defaultDate: '1997/05/27', showTrigger: '#calImg',
                                            yearRange: 'c-20:c+20', showTrigger: '#calImg',
                                                    dateFormat: 'yyyy/mm/dd',
                                        });
                                    });

                                </script>
                                <div class="form-group">
                                    <label for="inputBorrowerDob" class="col-sm-2 control-label">Date of Birth</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_dob" class="form-control" id="inputBorrowerDob" placeholder="dd/mm/yyyy" value="">
                                    </div>
                                </div>
                                <div class="form-group">

                                    <label for="inputBorrowerAddress" class="col-sm-2 control-label">Address</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_address" class="form-control" id="inputBorrowerAddress" placeholder="Address" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerCity" class="col-sm-2 control-label">City</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_city" class="form-control" id="inputBorrowerCity" placeholder="City" value="">
                                    </div>
                                </div>  

                                <div class="form-group">
                                    <label for="inputBorrowerProvince" class="col-sm-2 control-label">Province / State</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_province" class="form-control" id="inputBorrowerProvince" placeholder="Province or State" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerZipcode" class="col-sm-2 control-label">Zipcode</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_zipcode" class="form-control" id="inputBorrowerZipcode" placeholder="Zipcode" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerLandline" class="col-sm-2 control-label">Landline Phone</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_landline" class="form-control" id="inputBorrowerLandline" placeholder="Landline Phone" value="">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputBorrowerBusinessName" class="col-sm-2 control-label">Business Name</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="borrower_business_name" class="form-control" id="inputBorrowerBusinessName" placeholder="Business Name" value="">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerEORS" class="col-sm-2 control-label">Working Status</label>                      
                                    <div class="col-sm-2">
                                        <select class="form-control" name="borrower_working_status" id="inputBorrowerEORS">
                                            <option value="" ></option>
                                            <option value="Employee" >Employee</option>
                                            <option value="Owner" >Owner</option>
                                            <option value="Student" >Student</option>
                                            <option value="Overseas Worker" >Overseas Worker</option>
                                            <option value="Pensioner" >Pensioner</option>
                                            <option value="Unemployed" >Unemployed</option>
                                        </select>
                                    </div>
                                </div> 
                                <div class="form-group">

                                    <label for="user_picture" class="col-sm-2 control-label">Borrower Photo</label>
                                    <div class="col-sm-10">    
                                        <input type="file" id="photo_file" name="user_picture">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="inputBorrowerDescription" class="col-sm-2 control-label">Description</label>                      
                                    <div class="col-sm-10">
                                        <INPUT TYPE="text" name="borrower_description" class="form-control" id="inputBorrowerDescription" rows="3">
                                    </div>
                                </div>
                                <div class="form-group">

                                    <label for="borrower_files" class="col-sm-2 control-label">Borrower Files<br>(doc, pdf, image)</label>
                                    <div class="col-sm-10">    
                                        <input type="file" id="data_files" name="borrower_files[]" multiple>
                                    </div>
                                    <div class="col-sm-10">
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

                                <hr>
                                <div class="form-group">
                                    <label for="inputStaff" class="col-sm-2 control-label">Staff Access<br>(optional)</label>                      
                                    <div class="col-sm-10">
                                        <input type="checkbox" name="borrower_access_ids[]" value="655"> Japhet Ty Aiko<br>
                                        <input type="checkbox" name="borrower_access_ids[]" value="768"> Japhet Kamugisha<br>
                                        <p><small>You can assign borrower to the above staff. This borrower will appear in the <b>
                                                    <a href="../collection_sheets/view_daily_collection.html" target="_blank">Daily Collection Sheet</a></b> and the <b><a href="../collection_sheets/view_past_maturity_date.html" target="_blank">Past Maturity Date Loans Sheet</a></b> of the staff. This will allow you to download the daily collection sheet for each staff and the staff will know which borrower to chase for payment.</small></p>
                                    </div>
                                </div><hr>
                                <div class="form-group">
                                    <label for="inputBorrower121" class="col-sm-2 control-label">Cooking</label>                      
                                    <div class="col-sm-10">
                                        <textarea name="121" class="form-control" id="inputCustom121" rows="3"></textarea>            
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputBorrower120" class="col-sm-2 control-label">Meda</label>                      
                                    <div class="col-sm-10">
                                        <input type="text" name="120" class="form-control" id="inputCustom120" value="">            
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputBorrower122" class="col-sm-2 control-label">Shopping</label>                      
                                    <div class="col-sm-10">
                                        <script>
                                            $(function () {
                                                $('#inputCustom122').datepick({
                                                    defaultDate: '06/01/2017', showTrigger: '#calImg',
                                                    yearRange: 'c-20:c+20', showTrigger: '#calImg',
                                                            dateFormat: 'dd/mm/yyyy',
                                                });
                                            });

                                        </script>
                                        <input type="text" name="122" class="form-control" id="inputCustom122" value="">            
                                    </div>
                                </div>
                                <p style="text-align:center; font-weight:bold;">Want to add more fields? <a href="../admin/view_borrower_fields.html" target="_blank">Click here to add custom fields to the Add Borrower page</a>.</p>

                                <div class="box-footer">
                                    <button type="button" class="btn btn-default"  onClick="parent.location = 'add_borrower.html'">Back</button>
                                    <button type="submit" class="btn btn-info pull-right" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Please Wait">Submit</button>

                                    <script type="text/javascript">
                                        $('#add_borrower_form').submit(function () {
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

    </body>
</html>