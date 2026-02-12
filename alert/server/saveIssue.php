<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$listDao = new WB_ISULIST_DAO();
$groupDao = new WB_ISUALERTGROUP_DAO();

$listVo = new WB_ISULIST_VO();
$groupVo = new WB_ISUALERTGROUP_VO();

$type = $_POST['type'];
$num = $_POST['num'];
$level = $_POST['level'];
$groupVo = $groupDao->SELECT_SINGLE("GCode = {$num}");
$result = [];

if ($type == 'insert') {
    $listVo->GCode = $num;
    $listVo->IsuKind = $level;
    $listVo->IsuSrtAuto = 'manual';
    $listVo->IsuSrtDate = date('Y-m-d H:i:s');
    $listVo->Occur = 'manual';
    for ($i = 1; $i <= 4; $i++) {
        if ($level == "level{$i}") {
            $listVo->Equip = $groupVo->{"Equip{$i}"};
        }
    }
    $listVo->SMS = $groupVo->{"SMS{$num}"};
    $listVo->IStatus = 'm-start';
    $listVo->Send = 'N';
    $listVo->HAOK = 'E';

    $listDao->INSERT($listVo);

    $result['code'] = '00';
    $result['action'] = 'Alert Start[Manual]';
    $result['name'] = $groupVo->GName;
    $result['before'] = '';
    $result['after'] = $level;
} elseif ($type == 'update') {
    $listVo = $listDao->SELECT_SINGLE("IsuCode = {$num}");

    $result['code'] = '00';
    $result['action'] = 'Alert Start[Auto->Menual]';
    $result['name'] = $groupVo->GName;
    $result['before'] = "{$listVo->IsuKind}-대기";
    $result['after'] = "{$listVo->IsuKind}-발령";

    $listVo->IsuCode = $num;
    $listVo->IStatus = 'start';
    $listVo->Send = 'N';

    $listDao->UPDATE($listVo);
} elseif ($type == 'end') {
    $listVo = $listDao->SELECT_SINGLE("IsuCode = {$num}");
    $groupVo = $groupDao->SELECT_SINGLE("GCode = {$listVo->GCode}");

    $result['code'] = '00';
    $result['action'] = 'Alert End[Menual]';
    $result['name'] = $groupVo->GName;
    $result['before'] = "{$listVo->IsuKind}-발령";
    $result['after'] = "{$listVo->IsuKind}-종료";

    $listVo->IsuCode = $num;
    $listVo->IsuEndAuto = 'manual';
    $listVo->IsuEndDate = date('Y-m-d H:i:s');
    $listVo->IStatus = 'end';
    $listVo->Send = 'N';

    $listDao->UPDATE($listVo);
}

echo json_encode($result);
?>
