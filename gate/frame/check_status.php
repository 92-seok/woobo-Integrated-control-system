<?php
// 필요한 DAO(Data Access Object) 파일 포함
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

// 데이터베이스 DAO 객체 생성
$statusDao = new WB_GATESTATUS_DAO();

// WB_GATESTATUS 테이블에서 모든 레코드를 선택
$statusList = $statusDao->SELECT();

$statuses = [];
if ($statusList) {
    // 각 레코드의 CD_DIST_OBSV와 Gate 값을 배열에 저장
    foreach ($statusList as $statusVo) {
        $statuses[$statusVo->CD_DIST_OBSV] = $statusVo->Gate;
    }
}

// 결과를 JSON 형식으로 응답
header('Content-Type: application/json');
echo json_encode($statuses);
?>
