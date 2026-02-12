<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$type = $_POST['type'];
$ment = $_POST['ment'];
$result = [];

$vo = new WB_BRDMENT_VO();

if ($ment == 'type') {
    if ($type == 'tts') {
        $dao = new WB_BRDMENT_DAO();
        $sql = "BUse = 'ON'";
    } elseif ($type == 'alert') {
        $dao = new WB_BRDALERT_DAO();
        $sql = '1';
    }

    $vo = $dao->SELECT($sql);
    foreach ($vo as $v) {
        $result[$v->AltCode] = $v->Title;
    }
} else {
    if ($type == 'tts') {
        $dao = new WB_BRDMENT_DAO();
        $sql = "BUse = 'ON' and AltCode = '{$ment}' ";
    } elseif ($type == 'alert') {
        $dao = new WB_BRDALERT_DAO();
        $sql = "AltCode = '{$ment}'";
    }

    $vo = $dao->SELECT($sql);
    foreach ($vo as $v) {
        $result[$v->AltCode] = $v->Content;
    }
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>
