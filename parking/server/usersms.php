<?php
include '../../include/dbconn.php';

$type = $_POST['type'];
$result = [];

if ($type == 'insert') {
    $arrCN = $_POST['num']; //차량번호 ,
    $content = $_POST['content'];
    $checkHnsLpr = $_POST['checkHnsLpr'];
    $CarPhone = '';
    $SendStatus = 'T10';
    $SendType = 'usersend';
    $CarNum = explode(',', $arrCN);
    $RegDate = date('Y-m-d H:i:s', time());
    // T10.변환요청 > T11.변환실패 > T19.변환완료 > T20.발송접수 > T21.발송실패 > T29.발송완료

    if ($checkHnsLpr == 1) {
        for ($i = 0; $i < count($CarNum); $i++) {
            $mentSql = 'SELECT * FROM wb_parksmsment';
            $mentRes = mysqli_query($conn, $mentSql);
            $mentRow = mysqli_fetch_assoc($mentRes);

            if ($mentRow['Content'] != $content) {
                mysqli_query($conn, "UPDATE wb_parksmsment SET Content = '{$content}' WHERE 1");
            }

            $pContent = str_replace('@CarNumber', $CarNum[$i], $content);
            $sql =
                "insert into wb_ParkSMSList (CarNum, CarPhone, SMSContent, RegDate, EndDate, SendStatus, SendType) 
					values ('" .
                $CarNum[$i] .
                "', '', '" .
                $pContent .
                "', '" .
                $RegDate .
                "', '', '" .
                $SendStatus .
                "', '" .
                $SendType .
                "' )";
            $res = mysqli_query($conn, $sql);
        }
    } else {
        for ($i = 0; $i < count($CarNum); $i++) {
            $scenSql = "SELECT * FROM wb_parkcarhist WHERE idx = {$CarNum[$i]}";
            $scenRes = mysqli_query($conn, $scenSql);
            $row = mysqli_fetch_assoc($scenRes);

            $mentSql = 'SELECT * FROM wb_parksmsment';
            $mentRes = mysqli_query($conn, $mentSql);
            $mentRow = mysqli_fetch_assoc($mentRes);

            if ($mentRow['Content'] != $content) {
                mysqli_query($conn, "UPDATE wb_parksmsment SET Content = '{$content}' WHERE 1");
            }

            if ($i == 0) {
                $result['num'] = $row['CarNum'];
            } else {
                $result['num'] = $result['num'] . ',' . $row['CarNum'];
            }

            $pContent = str_replace('@CarNumber', $row['CarNum'], $content);
            $sql =
                "insert into wb_ParkSMSList (CarNum, CarPhone, SMSContent, RegDate, EndDate, SendStatus, SendType) 
					values ('" .
                $row['CarNum'] .
                "', '', '" .
                $pContent .
                "', '" .
                $RegDate .
                "', '', '" .
                $SendStatus .
                "', '" .
                $SendType .
                "' )";
            $res = mysqli_query($conn, $sql);
            //echo $sql."<br>";
        }
    }
} elseif ($type == 'resend') {
    /*
		$num = $_POST['num'];
		$car = $_POST['car'];
		
		$sql = "select a.*, b.SMSTitle, b.SMSContent
				from wb_parksmslistdetail as a left join
				wb_parksmslist as b
				on a.SMSCode = b.SMSCode
				where a.SMSCode = '".$num."' and a.CarNum = '".$car."'";
		$res = mysqli_query($conn, $sql);
		$row = mysqli_fetch_assoc($res);
				
		if(isset($row['SMSContent'])) {$content = $row['SMSContent'];} else {$content = '';}
		if(isset($row['SMSTitle'])) {$title = $row['SMSTitle'];} else {$title = '';}
		if(isset($row['CarNum'])) {$CarNum = $row['CarNum'];} else {$CarNum = '';}
				
		$reSql = "insert into wb_parksmslist(CarNum, SMSTitle, SMSContent, SMSDate) values ('".$CarNum."', '".$title."', '".$content."', '".date("Y-m-d H:i:s")."')";
		$reRes = mysqli_query($conn, $reSql);
		
		$id = mysqli_insert_id($conn);
		$messageCount = mb_strwidth( $content , "UTF-8");
		
		$phone_sql = "select * from wb_parkcar where CarNum = '".$car."'";
		$phone_res = mysqli_query($conn, $phone_sql);
		$phone_row = mysqli_fetch_assoc($phone_res);
		if(isset($phone_row['phone'])) {$phone = $phone_row['phone'];} else {$phone = '';}
		
		$reDetail_sql = "insert into wb_parksmslistdetail (SMSCode, CarNum, phone, SMSStatus, ErrLog, SMSDate) values ('".$id."' ,'".$CarNum."', '".$phone."', 'ing', '', '".date("Y-m-d H:i:s")."')";	
		$reDetail_res = mysqli_query($conn, $reDetail_sql);
		
		$sendmsg_sql = "insert into wb_sendmessage (SCode, PhoneNum, SendMessage, SendStatus, RegDate) values ('".$id."' ,'".$phone."', '".$content."', 'ing', '".date("Y-m-d H:i:s")."')";				
		$sendmsg_res = mysqli_query($conn, $sendmsg_sql);
		*/
}

echo json_encode($result);
