<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_POST['num'];
$parm = $_POST['parm'];

$sql = "UPDATE wb_brdlistdetail SET RegDate = now(),  BrdStatus = 'ing', RetDate = null WHERE CD_DIST_OBSV = '{$num}' and BCode = '{$parm}'";
$res = mysqli_query($conn, $sql);
$sql = "UPDATE wb_brdsend SET RegDate = now(),  BStatus = 'start' WHERE CD_DIST_OBSV = '{$num}' and Parm4 = '{$parm}'";
$res = mysqli_query($conn, $sql);
$retSql = "SELECT Parm3 FROM wb_brdsend WHERE Parm4 = '{$parm}'";
$retRes = mysqli_query($conn, $retSql);
$retRow = mysqli_fetch_assoc($retRes);

$result = ['content' => $retRow['Parm3']];
echo json_encode($result);
?>
