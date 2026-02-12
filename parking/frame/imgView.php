<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$checkHnsLpr = isset($_GET['checkhnslpr']) ? $_GET['checkhnslpr'] : null;

if ($checkHnsLpr == 1) {
    $carNum = isset($_GET['carnum']) ? $_GET['carnum'] : null;
    $cdDistObsv = isset($_GET['cd_dist_obsv']) ? $_GET['cd_dist_obsv'] : null;
    $eventDateTime = isset($_GET['eventdatetime']) ? $_GET['eventdatetime'] : null;

    if ($carNum != '') {
        $carSql = "SELECT ImageString, CarNumber FROM hns_lprdata WHERE CarNumber = '{$carNum}' AND CD_DIST_OBSV = '{$cdDistObsv}'
					AND EventDateTime = '{$eventDateTime}'";

        $carRes = mysqli_query($conn, $carSql);
        $carRow = mysqli_fetch_assoc($carRes);
        if ($carRow) {
            echo "<div><img alt='{$carRow['CarNumber']}' src='data:image/jpeg;base64,{$carRow['ImageString']}' width='375'/></div>";
            echo "<div style='font-size:20px;text-align:center;color:#fff;background-color:#5e60cd;height:100px;line-height:40px;'>{$carNum}</div>";
        }
    }
} else {
    $caridx = isset($_GET['caridx']) ? $_GET['caridx'] : null;
    $carnum = isset($_GET['carnum']) ? $_GET['carnum'] : null;

    if ($caridx != '') {
        $carSql = "SELECT CarNum_Img, CarNum_Imgname FROM wb_parkcarimg WHERE idx = {$caridx}";
        $carRes = mysqli_query($conn, $carSql);
        $carRow = mysqli_fetch_assoc($carRes);
        if ($carRow > 0) {
            echo "<div><img alt='{$carRow['CarNum_Imgname']}' src='data:image/jpeg;base64,{$carRow['CarNum_Img']}' width='375'/></div>";
            echo "<div style='font-size:20px;text-align:center;color:#fff;background-color:#5e60cd;height:100px;line-height:40px;'>{$carnum}</div>";
        }
    }
}
