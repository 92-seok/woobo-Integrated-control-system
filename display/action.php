<?php

$data = json_decode(file_get_contents('php://input'), true);
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_DISSEND_DAO();
$vo = new WB_DISSEND_VO();

if ($data['parm'] != '00') {
    $vo->RCMD = 'D090';
} else {
    $vo->RCMD = 'D060';
}

$vo->CD_DIST_OBSV = $data['Cd_dist_obsv'];
$vo->Parm1 = $data['parm'];
$vo->RegDate = date('Y-m-d H:i:s');
$vo->BStatus = 'start';

$dao->INSERT($vo);

echo json_encode(['SQL' => $dao->TEST_INSERT($vo)]);
?>
