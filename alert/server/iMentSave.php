<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
$data = json_decode(file_get_contents('php://input'), true);

$dao = new WB_ISUMENT_DAO();
$vo = new WB_ISUMENT_VO();
$bVo = new WB_ISUMENT_VO();

$vo->MentCode = $data['MentCode'] ?? '1';
$vo->BrdMent1 = preg_replace('/\r\n|\r|\n/', '', $data['broad1']);
$vo->BrdMent2 = preg_replace('/\r\n|\r|\n/', '', $data['broad2']);
$vo->BrdMent3 = preg_replace('/\r\n|\r|\n/', '', $data['broad3']);
$vo->BrdMent4 = preg_replace('/\r\n|\r|\n/', '', $data['broad4']);
$vo->SMSMent1 = $data['SMS1'];
$vo->SMSMent2 = $data['SMS2'];
$vo->SMSMent3 = $data['SMS3'];
$vo->SMSMent4 = $data['SMS4'];

$bVo = (object) $dao->SELECT();
$result = [];
$result['MentCode'] = $data['MentCode'] ?? '1';
$result['action'] = 'Alert Ment Update';
$result['name'] = '';
$result['before'] = '';
$result['after'] = '';

for ($i = 1; $i <= 4; $i++) {
    if ($bVo->{"BrdMent{$i}"} != $vo->{"BrdMent{$i}"}) {
        $result['before'] = "{$result['before']}방송{$i}단계:{$bVo->{"BrdMent{$i}"}}</br>";
        $result['after'] = "{$result['after']}방송{$i}단계:{$vo->{"BrdMent{$i}"}}</br>";
    }

    if ($bVo->{"SMSMent{$i}"} != $vo->{"SMSMent{$i}"}) {
        $result['before'] = "{$result['before']}SMS{$i}단계:{$bVo->{"SMSMent{$i}"}}</br>";
        $result['after'] = "{$result['after']}SMS{$i}단계:{$vo->{"SMSMent{$i}"}}</br>";
    }

    $vo->{"DisMent{$i}"} = $bVo->{"DisMent{$i}"};
}

$dao->UPDATE($vo);

echo json_encode($result);
?>