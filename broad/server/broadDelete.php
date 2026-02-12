<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_POST['num'];

// 방송 데이터 삭제
$sql = "delete from wb_brdlist where BCode IN ($num)";
$res = mysqli_query($conn, $sql);

// 방송 세부 데이터 삭제
$sql = "delete from wb_brdlistdetail where BCode IN ($num)";
$res = mysqli_query($conn, $sql);

$resultArray = ['code' => '00'];

echo json_encode($resultArray);

?>
