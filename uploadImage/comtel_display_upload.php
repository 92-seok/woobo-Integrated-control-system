<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// 업로드 디렉토리 설정
// $targetDir = '../displayImage';
$targetDir = __DIR__ . '/../displayImage/';

// 디렉토리가 없으면 생성
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// 업로드된 파일 정보 가져오기
$fileName = basename($_FILES['imageFile']['name']);
$targetFilePath = $targetDir . $fileName;
$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

// 업로드 성공 여부
$uploadOk = 1;

// 파일이 실제로 이미지인지 확인
$check = getimagesize($_FILES['imageFile']['tmp_name']);
if ($check === false) {
    echo '파일이 이미지가 아닙니다.';
    $uploadOk = 0;
}

// 파일 크기 제한 (예: 5MB)
if ($_FILES['imageFile']['size'] > 5000000) {
    echo '파일이 너무 큽니다.';
    $uploadOk = 0;
}

// 특정 파일 형식만 허용
$allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array(strtolower($fileType), $allowedTypes)) {
    echo 'JPG, JPEG, PNG, GIF 파일만 업로드 가능합니다.';
    $uploadOk = 0;
}

// 최종 업로드 처리
if ($uploadOk == 1) {
    if (move_uploaded_file($_FILES['imageFile']['tmp_name'], $targetFilePath)) {
        echo '파일 ' . htmlspecialchars($fileName) . '이(가) 성공적으로 업로드되었습니다.';
    } else {
        echo '파일 업로드 중 오류가 발생했습니다.';
    }
}
