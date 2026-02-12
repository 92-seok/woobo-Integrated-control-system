<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$type = $_POST['type'];
$result = [];

if ($type == 'insert') {
    $name = $_POST['name'];
    $addr1 = $_POST['addr1'];
    $addr2 = $_POST['addr2'];
    $code = $_POST['code'];

    $sql = "insert into wb_parkgategroup (ParkGroupName, ParkGroupAddr, ParkJoinGate, RegDate) values ('" . $name . "', '" . $addr1 . ' ' . $addr2 . "', '" . $code . "', '" . date('Ymd', time()) . "')";
    $res = mysqli_query($conn, $sql);

    $result['code'] = '00';
    $result['equip'] = $name;
    $result['before'] = '';
    $result['after'] = $code;
} elseif ($type == 'update') {
    $name = $_POST['name'];
    $addr1 = $_POST['addr1'];
    $addr2 = $_POST['addr2'];
    $code = $_POST['code'];
    $num = $_POST['num'];

    $bRes = mysqli_query($conn, "SELECT ParkGroupName, ParkGroupAddr, ParkJoinGate From wb_parkgategroup WHERE ParkGroupCode = '{$num}'");
    $bRow = mysqli_fetch_assoc($bRes);

    $sql = "update wb_parkgategroup set ParkGroupName = '" . $name . "', ParkGroupAddr = '" . $addr1 . "', ParkJoinGate = '" . $code . "' where ParkGroupCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);

    $result['code'] = '10';
    $result['equip'] = $name;
    $result['before'] = '';
    $result['after'] = '';

    if ($name != $bRow['ParkGroupName']) {
        $result['before'] = $result['before'] . ',' . $bRow['ParkGroupName'];
        $result['after'] = $result['after'] . ',' . $name;
    } elseif ($addr1 != $bRow['ParkGroupAddr']) {
        $result['before'] = $result['before'] . ',' . $bRow['ParkGroupAddr'];
        $result['after'] = $result['after'] . ',' . $addr1;
    } elseif ($code != $bRow['ParkJoinGate']) {
        $result['before'] = $result['before'] . ',' . $bRow['ParkJoinGate'];
        $result['after'] = $result['after'] . ',' . $code;
    }

    if ($result['before'] != '') {
        $result['before'] = substr($result['before'], 1, strlen($result['before']) - 1);
        $result['after'] = substr($result['after'], 1, strlen($result['after']) - 1);
    }
} elseif ($type == 'delete') {
    $num = $_POST['num'];

    $bRes = mysqli_query($conn, "SELECT ParkGroupName, ParkGroupAddr, ParkJoinGate From wb_parkgategroup WHERE ParkGroupCode = '{$num}'");
    $bRow = mysqli_fetch_assoc($bRes);

    $sql = "delete from wb_parkgategroup where ParkGroupCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);

    $result['code'] = '00';
    $result['equip'] = $name;
    $result['before'] = $code;
    $result['after'] = '';
}
echo json_encode($result);
?>
