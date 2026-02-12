<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_POST['group_code'];

if ($num == 'all') {
    $sql = 'select BEquip from wb_brdgroup';
} else {
    $sql = "select BEquip from wb_brdgroup where GCode = '" . $num . "'";
}
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($res);

if ($num == 'all') {
    $where = '';
} elseif ($row['BEquip'] == '') {
    $where = " and CD_DIST_OBSV = ''";
} else {
    $where = ' and CD_DIST_OBSV = ' . str_replace(',', ' or CD_DIST_OBSV = ', $row['BEquip']);
}

echo '<div>◈ 장비 선택</div>';
echo "<table border='0' cellpadding='0' cellspacing='0' class='cs_datatable' rules='rows'>";
echo "<tr align='center'>";
echo "<th width='10%'><input type='checkbox' name='allCheck' id='id_allCheck'></th>";
echo "<th width='35%'>장비명</th>";
echo "<th width='35%'>전화번호</th>";
echo "<th width='20%'>상태</th>";
echo '</tr>';

$eSql = "select CD_DIST_OBSV, NM_DIST_OBSV, ConnPhone, LastStatus from wb_equip where USE_YN = '1' and GB_OBSV = '17'" . $where;
$eRes = mysqli_query($conn, $eSql);
while ($eRow = mysqli_fetch_array($eRes)) {
    echo '<tr>';
    echo "<td><input type='checkbox' value='" . $eRow['CD_DIST_OBSV'] . "' name='equip' class='cs_eChk'></td>";
    echo "<td style='text-align:left; padding-left: 10px;'>" . $eRow['NM_DIST_OBSV'] . '</td>';
    echo '<td>' . substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 4) . '-' . substr($eRow['ConnPhone'], 7, 4) . '</td>';
    echo '<td>';
    if (strtolower($eRow['LastStatus']) == 'ok') {
        echo "<span style='color:blue'>정상</span>";
    } elseif (strtolower($eRow['LastStatus']) == 'ing') {
        echo "<span style='color:blue'>점검중</span>";
    } elseif (strtolower($eRow['LastStatus']) == 'fail') {
        echo "<span style='color:red'>점검요망</span>";
    }
    echo '</td>';
    echo '</tr>';
}
echo '</table>';
?>
