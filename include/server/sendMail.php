<?php
$data = json_decode(file_get_contents('php://input'), true);
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/mailer.php';

$udao = new WB_USER_DAO();
$rvo = new WB_USER_VO();
$rvo = $udao->SELECT_SINGLE("Auth = 'root'");

$dao = new WB_EQUIP_DAO();
$vo = new WB_EQUIP_VO();

$senddao = new WB_SENDMESSAGE_DAO();
$sendvo = new WB_SENDMESSAGE_VO();

$asListDao = new WB_ASRECEIVED_DAO();
$asListVo = new WB_ASRECEIVED_VO();

$fromdao = new WB_USER_DAO();
$fromvo = $fromdao->SELECT_SINGLE("uId = '{$data['uId']}'");

$type = $data['receivedType'];
if ($type == 'menual') {
    if ($data['from'] != $fromvo->uName) {
        $fromvo->uName = $data['from'];
        $fromdao->UPDATE($fromvo);
    }
    $from = $data['from'];
    unset($data['from']);
} else {
    $from = $fromvo->uName;
}

unset($data['uId']);
unset($data['receivedType']);

// Mail 보내기 확인
if (isset($data['mailChk'])) {
    $mailChk = true;
    unset($data['mailChk']);
} else {
    $mailChk = false;
}

if (isset($data['email'])) {
    $email = $data['email'];
    unset($data['email']);
} else {
    $email = $rvo->uName;
}

// 문자 보내기 확인
if (isset($data['phoneChk'])) {
    $phoneChk = true;
    unset($data['phoneChk']);
} else {
    $phoneChk = false;
}

if (isset($data['phoneNum'])) {
    $phoneNum = $data['phoneNum'];
    unset($data['phoneNum']);
} else {
    $phoneNum = $rvo->uPhone;
}

/* 메일 */
if ($mailChk) {
    // 메일 내용
    $content = "[{$from}] A/S 접수 내용<br/>";
    foreach ($data as $key => $val) {
        $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$key}'");
        $con = $val;

        $content = "{$content}장비명(장비번호):{$vo->NM_DIST_OBSV}({$vo->CD_DIST_OBSV}) / 마지막 통신일자:{$vo->LastDate} / A.S 내용:{$con}<br/>";
    }

    // 메일 발송
    $Title = "[{$from}]A/S접수_" . date('Y-m-d H:i');

    //sendMail($email, $Title, $content, $from);
    sendMailWithIDCServer($email, $Title, $content, $from, true);

    // Root 정보 변경 여부 확인
    if ($email != $rvo->uName) {
        $rvo->uName = $email;
    }
}
/* 메일 */

/* 문자 */
if ($phoneChk) {
    // 문자 내용
    $content = "[{$from}]";
    foreach ($data as $key => $val) {
        $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$key}'");
        $date = date('y.m.d.H', strtotime($vo->LastDate));
        $content = "{$content}{$vo->CD_DIST_OBSV}->{$date}/";
    }

    // 문자 발송
    $sendvo->PhoneNum = $phoneNum;
    $sendvo->SendMessage = $content;
    $sendvo->SendStatus = 'start';
    $sendvo->RegDate = date('Y-m-d H:i:s');

    $senddao->INSERT($sendvo);

    // Root 정보 변경 여부 확인
    if ($phoneNum != $rvo->uPhone) {
        $rvo->uPhone = $phoneNum;
    }
}
/* 문자 */

// A/S List INSERT
foreach ($data as $key => $val) {
    $asListVo->CD_DIST_OBSV = $key;
    $asListVo->RegDate = date('Y-m-d H:i:s');
    $asListVo->ReceivedType = $type;

    if ($mailChk) {
        $asListVo->MailCheck = 'on';
        $asListVo->EMail = $email;
    } else {
        $asListVo->MailCheck = 'off';
    }

    if ($phoneChk) {
        $asListVo->PhoneCheck = 'on';
        $asListVo->Phone = $phoneNum;
    } else {
        $asListVo->PhoneCheck = 'off';
    }

    if ($type == 'menual') {
        $asListVo->Content = $val;
    } else {
        $asListVo->Content = $type;
    }

    $asListDao->INSERT($asListVo);
}

// root계정 정보 업데이트
//$udao->UPDATE($rvo);
