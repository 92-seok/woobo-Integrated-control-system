<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_SMSUSER_DAO();
$vo = new WB_SMSUSER_VO();

$saveType = $_POST['type'];
$result = [];

if (isset($_POST['num'])) {
    $vo->GCode = $_POST['num'];
}
if (isset($_POST['name'])) {
    $vo->UName = $_POST['name'];
}
if (isset($_POST['phone'])) {
    $vo->Phone = $_POST['phone'];
}
if (isset($_POST['position'])) {
    $vo->UPosition = $_POST['position'];
}
if (isset($_POST['departments'])) {
    $vo->Division = $_POST['departments'];
}

if ($saveType == 'insert') {
    //중복 체크
    $chkvo = new WB_SMSUSER_VO();
    $chkvo = $dao->SELECT_SINGLE("UName = '{$vo->UName}' and Phone = '{$vo->Phone}'");
    if (isset($chkvo->UName)) {
        $result['code'] = '01';
        $result['content'] = '이미 등록된 전화번호입니다.';
    } else {
        $vo->GCode = null;
        $dao->INSERT($vo);

        $result['code'] = '10';
        $result['name'] = $vo->UName;
        $result['before'] = '';
        $result['after'] = "이름: {$vo->UName}<br/>번호: {$vo->Phone}<br/>부서명: {$vo->Division}<br/>직책: {$vo->UPosition}";
    }
} elseif ($saveType == 'update') {
    $bvo = new WB_SMSUSER_VO();
    $bvo = $dao->SELECT_SINGLE("GCode = '{$vo->GCode}'");

    $result['code'] = '00';
    $result['name'] = $vo->UName;

    if ($bvo->UName != $vo->UName) {
        $result['before'] = "이름: {$bvo->UName}<br/>";
        $result['after'] = "이름: {$vo->UName}<br/>";
    }
    if ($bvo->Phone != $vo->Phone) {
        $result['before'] = "{$result['before']}번호: {$bvo->Phone}<br/>";
        $result['after'] = "{$result['after']}번호: {$vo->Phone}<br/>";
    }
    if ($bvo->Division != $vo->Division) {
        $result['before'] = "{$result['before']}부서명: {$bvo->Division}<br/>";
        $result['after'] = "{$result['after']}부서명: {$vo->Division}<br/>";
    }
    if ($bvo->UPosition != $vo->UPosition) {
        $result['before'] = "{$result['before']}직책: {$bvo->UPosition}";
        $result['after'] = "{$result['after']}직책: {$vo->UPosition}";
    }

    $dao->UPDATE($vo);
} elseif ($saveType == 'delete') {
    $dao->DELETE($vo);

    $result['code'] = '20';
    $result['name'] = $vo->UName;
    $result['before'] = "이름: {$vo->UName}<br/>번호: {$vo->Phone}<br/>부서명: {$vo->Division}<br/>직책: {$vo->UPosition}";
} elseif ($saveType == 'listDelete') {
    $num = $_POST['num'];
    $equipList = explode(',', $num);

    // Log 쌓기 위한 로직
    $listdao = new WB_SMSLIST_DAO();
    $listvo = $listdao->SELECT("SCode IN ({$num})");

    $result['code'] = '30';
    $result['name'] = '';
    $result['before'] = '';

    foreach ($listvo as $v) {
        $userList = explode(',', $v->GCode);

        $result['before'] .= '보낸사람: ';
        foreach ($userList as $user) {
            $vo = $dao->SELECT_SINGLE("GCode = '{$user}'");
            if (isset($vo->UName)) {
                $result['before'] .= "{$vo->UName} / ";
            } else {
                $result['before'] .= '알수 없음[삭제된 유저] / ';
            }
        }
        $result['before'] .= '<br/>';
        $result['before'] .= "보낸내용: {$v->SMSContent}";
        $result['before'] .= '<br/>';
    }

    // DB Data Delete 로직
    $listvo = new WB_SMSLIST_VO();
    foreach ($equipList as $n) {
        $listvo->SCode = $n;
        $listdao->DELETE($listvo);
    }
}

echo json_encode($result);
?>
