<?php
include('../phplibs/mysql_crud.php');

switch ($_GET['action']) {
			case 'addborrower':
				addborrower();
				break;
		





			default:
				# code...
				break;
		}




function addborrower()
{
$db = new Database();
$db->connect();
$fname=$_POST['borrower_firstname'];
$lname=$_POST['borrower_lastname'];
$gender=$_POST['borrower_gender'];
$country=$_POST['borrower_country'];
$title=$_POST['borrower_title'];
$mnumber=$_POST['borrower_mobile'];
$dob=$_POST['borrower_dob'];
$address=$_POST['borrower_address'];
$city=$_POST['borrower_city'];
$landline=$_POST['borrower_landline'];
$bname=$_POST['borrower_business_name'];
$wstatus=$_POST['borrower_working_status'];
$pimage=$_POST['user_picture'];
$description=$_POST['borrower_description'];
$attachment=$_POST['borrower_files'];
$staffacess=$_POST['borrower_access_ids'];


$db->insert('borrower',array('fname'=>$fname, 'lname'=>$lname, 'gender'=>$gender, 'country'=>$country, 'title'=>$title, 'mnumber'=>$mnumber, 'dob'=>$dob, 'address'=>$address, 'city'=>$city, 'landline'=>$landline, 'bname'=>$bname, 'wstatus'=>$wstatus, 'pimage'=>$pimage, 'description'=>$attachment, 'staffacess'=>$staffacess));  // Table name, column names and respective values
$res = $db->getResult(); 
//header('location:../borrowers/view_borrowers_branch.html');

print_r($res);

}






?>