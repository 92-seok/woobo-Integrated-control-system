<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_POST['num'];
$type = $_POST['type'];
$sendData = $_POST['sendData'];

$sql =
    "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
				values ('" .
    $num .
    "', '" .
    $type .
    "', '" .
    $sendData .
    "', now(), 'start' )";
$res = mysqli_query($conn, $sql);

$resultArray = ['code' => '00'];

echo json_encode($resultArray);

?>
