<?php

$id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? intval($_GET['id']) : 0;
$item = $_GET["item"] ?? "";
if ($item == 'borrower') {
    include './helpers/DbAcess.php';
    $db = new DbAcess();
    $table = 'borrower';
    $result = $db->select($table, ['photo', 'field1'], ['id' => $id]);
    //print_r($result);
    header('Content-Type: '.$result['field1']);
    echo $result['photo'];
}
//$image = getImageFromDatabase($id); // your code to fetch the image

//header('Content-Type: image/jpeg');
//echo $image;
?>