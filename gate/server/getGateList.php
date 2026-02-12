<?php
include '../../include/dbconn.php';

$parkCode = $_POST['gateCode'];
$gateCode = $_POST['code'];

$sql = "select * from wb_parkgroup where ParkCode = '" . $parkCode . "'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($res);

$parkGate = explode(',', $row['ParkGate']);

echo '<option value="" >전체</option>';

for ($i = 0; $i < count($parkGate); $i++) {
    $subSql = "select * from wb_equip where CD_DIST_OBSV = '" . $parkGate[$i] . "'";
    $subRes = mysqli_query($conn, $subSql);
    $subRow = mysqli_fetch_array($subRes);

    if ($subRow['CD_DIST_OBSV'] == $gateCode) {
        $selected = 'selected';
    } else {
        $selected = '';
    }

    echo '<option value="' . $subRow['CD_DIST_OBSV'] . '" ' . $selected . '>' . $subRow['NM_DIST_OBSV'] . '</option>';
}
?>
