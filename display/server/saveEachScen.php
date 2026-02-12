<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
include './imageResize.php';

$type = $_POST['type'];
$areaCode = $_POST['areaCode'];
$num = $_POST['num'];
$width = $_POST['width'];
$height = $_POST['height'];

$disEffect = $_POST['disEffect'];
$disSpeed = $_POST['disSpeed'];
$endEffect = $_POST['endEffect'];
$endSpeed = $_POST['endSpeed'];
$relay = $_POST['relay'];
$disTime = $_POST['disTime'];
$startDate = $_POST['startDate'];
$startTime = $_POST['startTime'];
$endDate = $_POST['endDate'];
$endTime = $_POST['endTime'];
$summernote = $_POST['summernote'];
$imageTag = $_POST['imageTag'];

// 이미지 저장 처리
$imageName = $areaCode . '_text_' . date('YmdHis', time()) . '.png';
$imageThumb = $areaCode . '_thumb_' . date('YmdHis', time()) . '.png';

if ($imageTag != '') {
    $path = '../../displayImage/' . $imageName;

    // 썸네일용 파일 생성
    $img = str_replace('data:image/png;base64,', '', $imageTag);
    $img = str_replace(' ', '+', $img);
    $data = base64_decode($img);
    file_put_contents($path, $data);

    // 전송파일 생성
    if (file_exists($path)) {
        copy($path, '../../displayImage/' . $imageThumb);
    }

    $re_image = new Image('../../displayImage/' . $imageThumb);
    $re_image->resize();
    $re_image->save();
}

$imageFile = 'displayImage/' . $imageName;
$sendImage = 'displayImage/' . $imageThumb;

if ($type == 'insert') {
    $sql =
        "insert into wb_display ( CD_DIST_OBSV, saveType, DisEffect, DisSpeed, DisTime, EndEffect, EndSpeed, StrTime, EndTime, Relay, ViewImg, SendImg, HtmlData, DisType, Exp_YN, RegDate )
		 		values ('" .
        $areaCode .
        "', 'local', '" .
        $disEffect .
        "', '" .
        $disSpeed .
        "', '" .
        $disTime .
        "', '" .
        $endEffect .
        "', '" .
        $endSpeed .
        "', '" .
        $startDate .
        ' ' .
        $startTime .
        ':00:00' .
        "',
						'" .
        $endDate .
        ' ' .
        $endTime .
        ':00:00' .
        "', '" .
        $relay .
        "', '" .
        $imageFile .
        "', '" .
        $sendImage .
        "', '" .
        $summernote .
        "', 'ad', 'Y', now() )";

    $res = mysqli_query($conn, $sql);
} elseif ($type == 'update') {
    // 기존 이미지 삭제
    $imgSql = "select * from wb_display where DisCode = '" . $num . "'";
    $imgRes = mysqli_query($conn, $imgSql);
    $imgRow = mysqli_fetch_array($imgRes);

    safe_unlink('../../' . $imgRow['ViewImg']); // 표시 이미지 삭제
    safe_unlink('../../' . $imgRow['SendImg']); // 전송 이미지 삭제

    $sql =
        "update wb_display 
				set DisEffect = '" .
        $disEffect .
        "',
					DisSpeed = '" .
        $disSpeed .
        "',
					DisTime = '" .
        $disTime .
        "',
					EndEffect = '" .
        $endEffect .
        "',
					EndSpeed = '" .
        $endSpeed .
        "',
					StrTime = '" .
        $startDate .
        ' ' .
        $startTime .
        ':00:00' .
        "',
					EndTime = '" .
        $endDate .
        ' ' .
        $endTime .
        ':00:00' .
        "',
					Relay = '" .
        $relay .
        "',
					ViewImg = '" .
        $imageFile .
        "',
					SendImg = '" .
        $sendImage .
        "',
					HtmlData = '" .
        $summernote .
        "',
					Exp_YN = 'Y'
				where DisCode = '" .
        $num .
        "'";

    $res = mysqli_query($conn, $sql);
}

$sendSql =
    "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
				values ('" .
    $areaCode .
    "', 'D060', '', now(), 'start' )";
$sendRes = mysqli_query($conn, $sendSql);

echo "<script>window.location.replace('../displayFrame.php');</script>";