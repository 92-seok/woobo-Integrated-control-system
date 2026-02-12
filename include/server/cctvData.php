<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbvo.php';

// CCTV 데이터 조회 (GB_OBSV가 'CC'이고 USE_YN이 '1'인 데이터)
$sql = "SELECT CD_DIST_OBSV, NM_DIST_OBSV, LAT, LON, ConnIP, ConnPort, EType FROM wb_equip WHERE GB_OBSV = 'CC' AND USE_YN = '1' ORDER BY CD_DIST_OBSV ASC";

$result = mysqli_query($conn, $sql);
$cctvData = [];
$skippedCount = 0;

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // LAT, LON이 null이거나 빈 값인 경우 건너뛰기
        if (empty($row['LAT']) || empty($row['LON']) || $row['LAT'] === null || $row['LON'] === null || !is_numeric($row['LAT']) || !is_numeric($row['LON'])) {
            $skippedCount++;
            continue;
        }

        // ConnIP가 null이거나 빈 값인 경우 기본값 설정
        $connIP = !empty($row['ConnIP']) ? $row['ConnIP'] : 'localhost';

        // ConnPort가 null이거나 빈 값인 경우 기본값 설정
        $connPort = !empty($row['ConnPort']) ? $row['ConnPort'] : '9001';

        $cctvData[] = [
            'cd_dist_obsv' => $row['CD_DIST_OBSV'],
            'nm_dist_obsv' => $row['NM_DIST_OBSV'],
            'lat' => $row['LAT'],
            'lon' => $row['LON'],
            'conn_ip' => $connIP,
            'conn_port' => $connPort,
            'e_type' => $row['EType'],
            'data' => $row['DATA'],
        ];
    }

    // 로그 정보 추가
    if ($skippedCount > 0) {
        error_log("CCTV 데이터 로드 중 {$skippedCount}개의 항목이 유효하지 않은 좌표로 인해 제외되었습니다.");
    }
}

// JSON 형태로 응답
header('Content-Type: application/json');
echo json_encode($cctvData);
