<?php

// 세션 확인 및 보안 검증
session_start();
if (!isset($_SESSION['userIdx'])) {
    http_response_code(401);
    die('Unauthorized access');
}

// 에러 로깅
// ini_set("display_errors", 1);
// error_reporting(E_ALL);

// POST 및 파일 업로드 확인
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    die('File upload error');
}

// 파일 크기 제한 (50MB)
$maxFileSize = 50 * 1024 * 1024;
if ($_FILES['file']['size'] > $maxFileSize) {
    http_response_code(413);
    die('File too large. Maximum size: 50MB');
}

// post 받은 데이터 검증
$cdDistObsv = isset($_POST['cdDistObsv']) ? $_POST['cdDistObsv'] : '';
$videoName = $_FILES['file']['name'];
$c16FileNameVideo = isset($_POST['c16FileNameVideo']) ? $_POST['c16FileNameVideo'] : '';

// cdDistObsv 보안 검증 (영숫자만 허용)
if (!preg_match('/^[a-zA-Z0-9]+$/', $cdDistObsv)) {
    http_response_code(400);
    die('Invalid cdDistObsv parameter');
}

// 임시로 저장되는 정보
$tempFile = $_FILES['file']['tmp_name'];

// 실제 파일 MIME 타입 검증
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$realMimeType = finfo_file($finfo, $tempFile);
finfo_close($finfo);

// 허용된 비디오 MIME 타입
$allowedMimeTypes = [
    'video/mp4',
    'video/avi', 
    'video/quicktime', // .mov
    'video/x-msvideo', // .avi
    'video/x-ms-wmv'   // .wmv
];

if (!in_array($realMimeType, $allowedMimeTypes)) {
    http_response_code(400);
    die('Invalid file type. Only video files are allowed.');
}

// 확장자 검증 (pathinfo 사용으로 보안 강화)
$fileExt = strtolower(pathinfo($videoName, PATHINFO_EXTENSION));
$allowedExtensions = ['mp4', 'avi', 'mov', 'wmv'];

if (!in_array($fileExt, $allowedExtensions)) {
    http_response_code(400);
    die('Invalid file extension. Allowed: ' . implode(', ', $allowedExtensions));
}

/********************* 전광판 *********************/
// 보안 강화: 업로드 디렉토리 절대 경로 설정
$baseUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/displayImage/';

// 디렉토리가 없으면 생성 (보안 권한으로)
if (!file_exists($baseUploadDir)) {
    mkdir($baseUploadDir, 0755, true);
}

// 임시 파일 옮길 디렉토리 및 파일명
if ($c16FileNameVideo == '') {
    // c16이 아니면 - 안전한 파일명 생성
    $safeFileName = $cdDistObsv . '_video_' . date('YmdHis', time()) . '_' . uniqid() . '.' . $fileExt;
    $resFile = $baseUploadDir . $safeFileName;
} else {
    // c16이면 - 파일명 보안 검증
    $c16FileNameVideo = basename($c16FileNameVideo); // 경로 순회 방지
    
    // 파일명에서 위험한 문자 제거
    $c16FileNameVideo = preg_replace('/[^a-zA-Z0-9._-]/', '', $c16FileNameVideo);
    
    // 확장자 강제 적용
    $c16FileNameVideo = pathinfo($c16FileNameVideo, PATHINFO_FILENAME) . '.' . $fileExt;
    
    // MC 디렉토리 경로 (웹루트 내부로 제한)
    $mcUploadDir = $_SERVER['DOCUMENT_ROOT'] . '/MC/';
    if (!file_exists($mcUploadDir)) {
        mkdir($mcUploadDir, 0755, true);
    }
    
    $resFile = $mcUploadDir . $c16FileNameVideo;
}

// 경로 순회 공격 최종 방어
$realBasePath = realpath($baseUploadDir);
$realMcPath = realpath($_SERVER['DOCUMENT_ROOT'] . '/MC/');
$realTargetPath = realpath(dirname($resFile));

// displayImage 또는 MC 디렉토리 외부 업로드 차단
if ($realTargetPath !== $realBasePath && $realTargetPath !== $realMcPath) {
    http_response_code(403);
    die('Invalid upload path');
}

// 임시 저장된 파일을 우리가 저장할 디렉토리 및 파일명으로 옮김
if (move_uploaded_file($tempFile, $resFile)) {
    // 업로드된 파일 권한 설정 (실행 권한 제거)
    chmod($resFile, 0644);
    
    // 성공 로그 기록
    error_log("File uploaded successfully: " . basename($resFile) . " by user " . $_SESSION['userIdx']);
    
    // 성공 응답
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'File uploaded successfully',
        'filename' => basename($resFile)
    ]);
} else {
    // 실패 로그 기록
    error_log("File upload failed for user " . $_SESSION['userIdx'] . ": " . $videoName);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'File upload failed'
    ]);
}
/********************* 전광판 *********************/

?>
