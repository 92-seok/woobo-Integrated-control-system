<?php
$conn = mysqli_connect('127.0.0.1', 'userWooboWeb', 'wooboWeb!@', 'warning');

// 2021.10.25 차량번호 미인식 체크위해 수정
$caridx = $_GET['caridx'];
$carnum = $_GET['carnum'];
if ($caridx != '') {
    $carSql = "SELECT CarNum_Img, CarNum_Imgname FROM wb_parkcarimg WHERE idx = {$caridx}";
    $carRes = mysqli_query($conn, $carSql);
    $carRow = mysqli_fetch_assoc($carRes);
    if ($carRow > 0) {
        echo "<div><img alt='{$carRow['CarNum_Imgname']}' src='data:image/jpeg;base64,{$carRow['CarNum_Img']}' width='375'/></div>";
        echo "<div style='font-size:20px;text-align:center;color:#fff;background-color:#5e60cd;height:100px;line-height:40px;'>{$carnum}</div>";
    }
}
?>
