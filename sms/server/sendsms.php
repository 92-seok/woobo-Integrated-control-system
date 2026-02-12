<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$code = $_POST['code'];
$content = $_POST['content'];
$title = $_POST['title'];
$now = date('YmdHi');

$explode = explode(',', $code);
$eCount = count($explode);
$resultMessage = '';

$selment_sql = 'select * from wb_smsment';
if ($mentsel_res = mysqli_query($conn, $selment_sql)) {
    $count = mysqli_num_rows($mentsel_res);
    if ($count >= 1) {
        $updment_sql = "update wb_smsment set Title = '" . $title . "', Content = '" . $content . "'";
        if ($mentup_res = mysqli_query($conn, $updment_sql)) {
            $resultMessage = '문자 내용만 수정 되었습니다.';
        } else {
            $resultAlert = ['code' => '01', 'message' => mysqli_error($conn)];
            echo json_encode($resultAlert);
        }
    } else {
        $instment_sql = "insert into wb_smsment (Title,Content) VALUES ('" . $title . "','" . $content . "')";
        if ($mentinst_res = mysqli_query($conn, $instment_sql)) {
            $resultMessage = '문자 내용만 등록 되었습니다.';
        } else {
            $resultAlert = ['code' => '01', 'message' => mysqli_error($conn)];
            echo json_encode($resultAlert);
        }
    }

    if ($code != '') {
        $list_sql = "insert into wb_smslist (GCode, SMSTitle, SMSContent, SMSDate) values ('" . $code . "', '" . $title . "', '" . $content . "', now())";
        if ($list_res = mysqli_query($conn, $list_sql)) {
            $id = mysqli_insert_id($conn);
            $messageCount = mb_strwidth($content, 'UTF-8');

            for ($i = 0; $i < $eCount; $i++) {
                $addr_sql = "select * from wb_smsuser where GCode = ('" . $explode[$i] . "')";
                $addr_res = mysqli_query($conn, $addr_sql);
                $addr_row = mysqli_fetch_assoc($addr_res);
                $phone = $addr_row['Phone'];

                $sendmsg_sql = "insert into wb_sendmessage ( SCode, PhoneNum, SendMessage, SendStatus, RegDate) values ('" . $id . "' ,'" . $phone . "', '" . $content . "', 'start', DATE_FORMAT(NOW(),'%Y-%m-%d %T'))";
                $sendmsg_res = mysqli_query($conn, $sendmsg_sql);
            }
            $resultAlert = ['code' => '00', 'message' => $eCount . '명에게 발송등록을 하였습니다.'];
        } else {
            $resultAlert = ['code' => '01', 'message' => '데이터 처리를 실패 하였습니다.'];
        }
    } else {
        $resultAlert = ['code' => '00', 'message' => '선택된 수신자가 없어 ' . $resultMessage];
    }
} else {
    $resultAlert = ['code' => '01', 'message' => '데이터 처리를 실패 하였습니다.'];
}

echo json_encode($resultAlert);

?>
