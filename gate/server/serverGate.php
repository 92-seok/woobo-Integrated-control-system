<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$saveType = $_POST['saveType'];
$result = [];

$equipDao = new WB_EQUIP_DAO();
$statDao = new WB_GATESTATUS_DAO();
$gateDao = new WB_GATECONTROL_DAO();

$statVo = new WB_GATESTATUS_VO();
$gateVo = new WB_GATECONTROL_VO();

if ($saveType == 'save') {
    $gateVo->CD_DIST_OBSV = $_POST['num'];
    $gateVo->Gate = $_POST['gate'];
    $gateVo->RegDate = date('Y-m-d H:i:s');
    $gateVo->GStatus = 'start';

    $gateDao->INSERT($gateVo);

    $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$gateVo->CD_DIST_OBSV}'");

    $result['code'] = '00';
    $result['equip'] = $equipVo->NM_DIST_OBSV;
} elseif ($saveType == 'insert') {
    $equipVo = new WB_EQUIP_VO();
    $equipVo->NM_DIST_OBSV = $_POST['equipName'];
    $equipVo->ConnIP = $_POST['equipIP'];
    $equipVo->ConnPort = $_POST['equipPort'];
    $equipVo->DTL_ADRES = $_POST['equipAddr'];
    $equipVo->LAT = $_POST['lat'];
    $equipVo->LON = $_POST['long'];
    $equipVo->GB_OBSV = '20';
    $equipVo->USE_YN = '1';
    $cd = str_replace('.', '', $equipVo->ConnIP);
    $cd = substr($cd, strlen($cd) - 3, 3);
    $equipVo->CD_DIST_OBSV = "0{$cd}";

    $equipDao->INSERT($equipVo);

    $statVo->CD_DIST_OBSV = $equipVo->CD_DIST_OBSV;
    $statDao->INSERT($statVo);
} elseif ($saveType == 'update') {
    $num = $_POST['num'];
    $equipName = $_POST['equipName'];
    $equipIP = $_POST['equipIP'];
    $equipPort = $_POST['equipPort'];
    $equipAddr = $_POST['equipAddr'];
    $lat = $_POST['lat'];
    $long = $_POST['long'];

    $sql =
        "update wb_equip set NM_DIST_OBSV = '" .
        $equipName .
        "', ConnModel = 'ABC', ConnIP = '" .
        $equipIP .
        "', ConnPort = '" .
        $equipPort .
        "', ErrorChk = '5', GB_OBSV = '20',
									USE_YN = '1', LON = '" .
        $long .
        "', LAT = '" .
        $lat .
        "', DTL_ADRES = '" .
        $equipAddr .
        "' where CD_DIST_OBSV = '" .
        $num .
        "'";
    $res = mysqli_query($conn, $sql);
} elseif ($saveType == 'delete') {
    $num = $_POST['num'];

    $sql = "update wb_equip set USE_YN = '0' where CD_DIST_OBSV = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
}

echo json_encode($result);
?>
