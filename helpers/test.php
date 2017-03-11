<?php
include './DbAcess.php';
echo 'Testing Connection ***';

$db=new DbAcess();
$table="borrower";

//$result=$db->select($table);
//print_r($result);
echo 'Result ****///';

echo date('Y-m-d h:i:s');
echo '*** '.time().'</br>';
echo $_SERVER['DOCUMENT_ROOT'].'</br>';
echo dirname(__DIR__);