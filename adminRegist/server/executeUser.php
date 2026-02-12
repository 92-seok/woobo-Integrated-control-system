<?php
header('Content-Type: application/json');

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_USER_DAO();
$vo = new WB_USER_VO();
$bVo = new WB_USER_VO();

$saveType = base64_decode($_POST['saveType']);
$vo->idx = base64_decode($_POST['idx']);

if ($saveType != 'delete') {
    $password = base64_decode($_POST['pw']);
    $vo->Auth = base64_decode($_POST['auth']);
    $vo->uName = urldecode(base64_decode($_POST['name']));
    $vo->uPhone = base64_decode($_POST['phone']);
    $vo->ip = base64_decode($_POST['ip']);
    $vo->ipUse = base64_decode($_POST['ipUse']);
    $vo->RegDate = date('Y-m-d H:i:s');

    if ($saveType == 'insert') {
        $bVo->uId = urldecode(base64_decode($_POST['id']));
        $bVo = $dao->SELECT_SINGLE("uId = '{$bVo->uId}'");
        if ($bVo->{key($bVo)}) {
            $res['code'] = '400';
            $res['msg'] = '이미 등록된 아이디 입니다.';
        } else {
            $vo->idx = null;
            $vo->uId = urldecode(base64_decode($_POST['id']));
            $vo->uPwd = strtoupper(hash('sha512', $password));

            $dao->INSERT($vo);

            $res['code'] = '200';
            $res['action'] = 'User Insert';
            $res['name'] = $vo->uName;
            $res['before'] = '';
            $res['after'] = "아이디:{$vo->uId}</br>번호:{$vo->uPhone}</br>등급:{$vo->Auth}</br>IP({$vo->ipUse}):{$vo->ip}";
        }
    } else {
        $bVo = $dao->SELECT_SINGLE("idx = {$vo->idx}");

        $vo->uId = $bVo->uId;
        if ($password == '') {
            $vo->uPwd = $bVo->uPwd;
        } else {
            $vo->uPwd = strtoupper(hash('sha512', $password));
        }
        $dao->UPDATE($vo);

        $res['code'] = '200';
        $res['action'] = 'User Update';
        $res['name'] = $vo->uName;
        $res['before'] = '';
        $res['after'] = '';

        if ($bVo->uName != $vo->uName) {
            $res['before'] .= "</br>이름:{$bVo->uName}";
            $res['after'] .= "</br>이름{$vo->uName}";
        }
        if ($bVo->uPhone != $vo->uPhone) {
            $res['before'] .= "</br>번호:{$bVo->uPhone}";
            $res['after'] .= "</br>번호:{$vo->uPhone}";
        }
        if ($bVo->Auth != $vo->Auth) {
            $res['before'] .= "</br>등급:{$bVo->Auth}";
            $res['after'] .= "</br>등급:{$vo->Auth}";
        }
        if ($bVo->ip != $vo->ip) {
            $res['before'] .= "</br>IP({$bVo->ipUse}):{$bVo->ip}";
            $res['after'] .= "</br>IP({$vo->ipUse}):{$vo->ip}";
        }
    }
} else {
    $bVo = $dao->SELECT_SINGLE("idx = '{$vo->idx}'");

    $dao->DELETE($vo);

    $res['code'] = '200';
    $res['action'] = 'User Delete';
    $res['name'] = $bVo->uName;
    $res['before'] = '';
    $res['after'] = '';
}

echo json_encode($res);
?>
