<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$code = $_POST['num'];
$type = $_POST['type'];
$result = [];

if ($code == '') {
    exit();
}

$user = explode(',', $code);

if ($type != '2') {
    // 입/출차 일경우
    //입출차 내역 삭제
    for ($i = 0; $i < count($user); $i++) {
        $cRes = mysqli_query($conn, "SELECT CarNum FROM wb_parkcarhist WHERE idx = '{$user[$i]}'");
        $cRow = mysqli_fetch_assoc($cRes);
        if ($i == 0) {
            $result['num'] = $cRow['CarNum'];
        } else {
            $result['num'] = $result['num'] . ',' . $cRow['CarNum'];
        }

        $sql = "delete from wb_ParkCarHist where idx = '" . $user[$i] . "'";
        $res = mysqli_query($conn, $sql);
    }
}
// 현재 주차 일경우
else {
    // 현재 주차차량 내역 삭제
    for ($i = 0; $i < count($user); $i++) {
        $cRes = mysqli_query($conn, "SELECT CarNum FROM wb_parkcarnow WHERE idx = '{$user[$i]}'");
        $cRow = mysqli_fetch_assoc($cRes);
        if ($i == 0) {
            $result['num'] = $cRow['CarNum'];
        } else {
            $result['num'] = $result['num'] . ',' . $cRow['CarNum'];
        }

        $sql = "delete from wb_ParkCarNow where idx = '" . $user[$i] . "'";
        $res = mysqli_query($conn, $sql);
    }
}

echo json_encode($result);
?>
