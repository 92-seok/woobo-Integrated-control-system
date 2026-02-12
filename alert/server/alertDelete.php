<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$numList = $_POST['equip'];
$type = $_POST['type'];
$table = '';
$num = explode(',', $numList);
$where = '';
$result = [];

if ($type == 'group') {
    $table = 'wb_isualertgroup';
    for ($i = 0; $i < count($num); $i++) {
        if ($i == 0) {
            $where = " GCode = '" . $num[$i] . "' ";
        } else {
            $where = $where . " or GCode = '" . $num[$i] . "' ";
        }
    }

    $result['action'] = 'Alert Group Delete';
    $result['name'] = '';
    $result['content'] = '';

    $bSql = "SELECT GName FROM wb_isualertgroup WHERE {$where}";
    $bRes = mysqli_query($conn, $bSql);
    while ($bRow = mysqli_fetch_assoc($bRes)) {
        $result['name'] = $result['name'] . ',' . $bRow['GName'];
    }

    if ($result['name'] != '') {
        $result['name'] = substr($result['name'], 1, strlen($result['name']) - 1);
    }
} elseif ($type == 'control') {
    $table = 'wb_isulist';
    for ($i = 0; $i < count($num); $i++) {
        if ($i == 0) {
            $where = " IsuCode = '" . $num[$i] . "' ";
        } else {
            $where = $where . " or IsuCode = '" . $num[$i] . "' ";
        }
    }

    $result['action'] = 'Alert List Delete';
    $result['name'] = '';
    $result['content'] = '';

    $bSql = "SELECT RIGHT(a.IsuKind, 1) as con, b.GName FROM wb_isulist AS a LEFT JOIN wb_isualertgroup AS b ON a.GCode = b.GCode WHERE {$where}";
    $bRes = mysqli_query($conn, $bSql);
    while ($bRow = mysqli_fetch_assoc($bRes)) {
        $result['name'] = $result['name'] . ',' . $bRow['GName'];
        $result['content'] = $result['content'] . ',' . $bRow['con'] . '단계';
    }

    if ($result['name'] != '') {
        $result['name'] = substr($result['name'], 1, strlen($result['name']) - 1);
    }
    if ($result['content'] != '') {
        $result['content'] = substr($result['content'], 1, strlen($result['content']) - 1);
    }
}

$sql = 'delete from ' . $table . ' where ' . $where;
$isuRes = mysqli_query($conn, $sql);

echo json_encode($result);
?>	
