<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$data = json_decode(file_get_contents('php://input'), true);

$CD_DIST_OBSV = $data['CD_DIST_OBSV'];

$result = [];

/// TODO: FK 삭제 (wb_brdstatus, wb_disstatus, wb_gatestatus)

try {
    // $sql = "SELECT * FROM wb_equip WHERE CD_DIST_OBSV = '$CD_DIST_OBSV'";
    mysqli_query($conn, "DELETE FROM wb_raindis    WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_waterdis   WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_dplacedis  WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_snowdis    WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_brdstatus  WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_disstatus  WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");
    mysqli_query($conn, "DELETE FROM wb_gatestatus WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV)");

    $sql = "DELETE FROM wb_equip WHERE CD_DIST_OBSV IN ($CD_DIST_OBSV);";
    $res = mysqli_query($conn, $sql);

    // 쿼리 실행 후, 영향을 받은 행의 수 확인
    $count = mysqli_affected_rows($conn);
    if (0 < $count) {
        $result['code'] = '00';
        $result['msg'] = '삭제되었습니다.';
    } elseif ($count == 0) {
        $result['code'] = '00';
        $result['msg'] = '삭제된 장비가 없습니다.';
    } else {
        $result['code'] = '01';
        $result['msg'] = '삭제 실패했습니다.';
    }
} catch (Exception $e) {
    echo json_encode(['code' => '01', 'msg' => 'DB 연결 실패: ' . $e->getMessage()]);
    exit();
}

echo json_encode($result);
