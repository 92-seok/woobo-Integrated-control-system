<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
$MsgCode = $_POST['code'];
$result = [];
$bRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT PhoneNum, SendMessage FROM wb_sendmessage WHERE MsgCode = {$MsgCode}"));
$result['equip'] = $bRow['PhoneNum'];
$result['content'] = $bRow['SendMessage'];

$sql = "UPDATE wb_sendmessage SET SendStatus = 'start' , RegDate = now() WHERE MsgCode = {$MsgCode}";
mysqli_query($conn, $sql);

echo json_encode($result);
?>
