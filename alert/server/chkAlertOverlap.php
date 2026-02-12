<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_ISUALERT_DAO();
$vo = new WB_ISUALERT_VO();

$equipType = $_POST['equipType'];
$type = $_POST['type'];

$vo->CD_DIST_OBSV = $_POST['equip'];
$vo->RainTime = $_POST['rainTime'];

if ($equipType == 'rain') {
    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}' AND RainTime = '{$vo->RainTime}'");
} else {
    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '$vo->CD_DIST_OBSV'");
}

if ($type == 'ins') {
    if (isset($vo->AltCode)) {
        $resultArray['code'] = '00';
    } else {
        $resultArray['code'] = '01';
    }
} else {
    $resultArray['code'] = '00';
}

echo json_encode($resultArray);
