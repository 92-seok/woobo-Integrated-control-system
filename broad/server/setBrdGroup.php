<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$type = $_POST['type'];
$content = $_POST['group_value'];
$num = $_POST['group_code'];

if ($type == 'select_broadForm') {
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
        if (strpos($eRow['ConnPhone'], '-')) {
            $phone_number = $eRow['ConnPhone'];
        } else {
            if (strlen($eRow['ConnPhone']) == 10) {
                $phone_number = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 3) . '-' . substr($eRow['ConnPhone'], 6, 4);
            } else {
                $phone_number = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 4) . '-' . substr($eRow['ConnPhone'], 7, 4);
            }
        }

        echo '<tr>';
        echo "<td><input type='checkbox' value='" . $eRow['CD_DIST_OBSV'] . "' name='equip' class='cs_brdChk'></td>";
        echo "<td style='text-align:left; padding-left: 10px;'>" . $eRow['NM_DIST_OBSV'] . '</td>';
        echo "<td>{$phone_number}</td>";
        if ($eRow['LastStatus'] == 'OK') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } elseif ($eRow['LastStatus'] == 'Fail') {
            echo "<td><span style='color:red'>점검요망</span></td>";
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
}
if ($type == 'select') {
    $sql = "select BEquip from wb_brdgroup where GCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($res);

    $arr = explode(',', $row['BEquip']);

    echo "<table border='0' cellpadding='0' cellspacing='0' class='cs_datatable' rules='rows'>";
    echo "<tr align='center' id='id_before' value='{$row['BEquip']}'>";
    echo "<th width='10%'><input type='checkbox' name='allCheck' id='id_allCheck'></th>";
    echo "<th width='35%'>장비명</th>";
    echo "<th width='35%'>전화번호</th>";
    echo "<th width='20%'>상태</th>";
    echo '</tr>';

    $eSql = "select CD_DIST_OBSV, NM_DIST_OBSV, ConnPhone, LastStatus from wb_equip where USE_YN = '1' and GB_OBSV = '17'";
    $eRes = mysqli_query($conn, $eSql);
    while ($eRow = mysqli_fetch_array($eRes)) {
        if (strpos($eRow['ConnPhone'], '-')) {
            $phone_number = $eRow['ConnPhone'];
        } else {
            if (strlen($eRow['ConnPhone']) == 10) {
                $phone_number = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 3) . '-' . substr($eRow['ConnPhone'], 6, 4);
            } else {
                $phone_number = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 4) . '-' . substr($eRow['ConnPhone'], 7, 4);
            }
        }

        $chk = '';
        if (in_array($eRow['CD_DIST_OBSV'], $arr)) {
            $chk = 'checked';
        }
        echo '<tr>';
        echo "<td><input type='checkbox' value='" . $eRow['CD_DIST_OBSV'] . "' name='equip' class='cs_brdChk' " . $chk . '></td>';
        echo "<td style='text-align:left; padding-left: 10px;'>" . $eRow['NM_DIST_OBSV'] . '</td>';
        echo "<td>{$phone_number}</td>";
        if ($eRow['LastStatus'] == 'OK') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } elseif ($eRow['LastStatus'] == 'Fail') {
            echo "<td><span style='color:red'>점검요망</span></td>";
        } else {
            echo '<td></td>';
        }
        echo '</tr>';
    }
    echo '</table>';
} elseif ($type == 'insert') {
    $sql = "insert into wb_brdgroup (GName) values ('" . $content . "')";
    $res = mysqli_query($conn, $sql);
} elseif ($type == 'delete') {
    $sql = "delete from wb_brdgroup where GCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
} elseif ($type == 'update') {
    $sql = "update wb_brdgroup set GName = '" . $content . "' where GCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
} elseif ($type == 'eupdate') {
    $sql = "update wb_brdgroup set BEquip = '" . $content . "' where GCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
}
?>
