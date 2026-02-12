<?php
// 2021.08.25 JSON Parsing Change
// 2023.12.08 Focus 신규제휴작업 Hong
// 차량 입/출차 이벤트 처리
//include "../../../include/dbconn.php";
// 192.168.83.88 접속방식 수동세팅
$conn = mysqli_connect('127.0.0.1', 'userWooboWeb', 'wooboWeb!@', 'warning');
header('Content-Type: application/json; charset=UTF-8');

$req_json = file_get_contents('php://input');
$req_json = preg_replace('/[\x00-\x1F\x7F]/u', '', $req_json);

/*
	$req_json = '
	{
		"serial":"0000-1003", 
		"date":"2023-12-11 16:28:00", 
		"carNum":"75보2928", 
		"carBin":"/9j/4QAYRXhpZgAASUkqAAgAAA..."
	}';
	*/

if (strlen($req_json) < 1) {
    echo json_encode(['resultCode' => '400', 'resultMessage' => 'parameter not enought!']);
    exit();
}

$arrv = json_decode($req_json, true); //배열로 변환
//print_r($arrv);		//debug print

$arrVal = [];

$arrVal[0] = 'False'; // process Flag
$arrVal[1] = 'Null'; // ip
$arrVal[2] = substr($arrv['serial'], -4); // 시리얼코드 중 오른쪽 4자리만 사용
//$date_default_timezone_set('Asia/Seoul');
//$arrVal[3] = $arrv["dev"]["issueDate"];
//$arrVal[3] = date("Y-m-d H:i:s", time());	// 시스템시간 (현장특이사항 고려해서 변겨함) 2021.09.13
//$arrVal[3] = strtotime("+0 hours");		// 유닉스시간 +7H  PHP.ini config >> Asia/Seoul 로 수정필요함
$arrVal[3] = strtotime($arrv['date']);
$arrVal[4] = $arrv['carNum'];
$arrVal[5] = $arrv['carNum']; // carNum imgname --> carNUm 으로 변경해서 사용
$arrVal[6] = $arrv['carBin']; // carNum img (binary)

if (strlen($arrVal[4]) > 0) {
    $arrVal[0] = 'True';
} //차량번호 수신시 이후 처리

/*
	// Debug log ---------------------------------------------------
	$fp = fopen("log/LPR_".date("ym",strtotime("Now")).".txt", "a");
	fwrite($fp, date("Y-m-d H:i:s", strtotime("Now")));
	fwrite($fp, " / serial:".$arrVal[2]." / "."carNum:".$arrVal[4]." / ".$arrVal[6]." \r\n");
	fclose($fp);
	// ------------------------------------------------
	*/

// Database Insert
if ($arrVal[0] == 'True') {
    // serial Code define
    // 0000-0000-0000-0000-0000-ABCD  A:ParkGroupCode (0~9) , B:IN/OUT (0:IN, 1:OUT) , CD:index, WB_Equip.CD_dist_OBSV매칭 (00~99)

    $sGcode = substr($arrVal[2], 0, 1); // 시리얼코드 1번째자리,  주차장그룹코드 (0~9)
    $sType = substr($arrVal[2], 1, 1); // 시리얼코드 2번째자리,  0:입차, 1:출차
    $sDateTime = date('Y-m-d H:i:s', $arrVal[3]); // YYYY-MM-DD hh:mm:ss type
    $sDate = date('Ymd', $arrVal[3]); // YYYYMMDD type date
    $sHour = (int) date('H', $arrVal[3]); // Hour

    // log
    $fp = fopen('log/LPR_' . date('ym', strtotime('Now')) . '.txt', 'a');
    fwrite($fp, date('Y-m-d H:i:s', strtotime('Now')));
    fwrite($fp, ' / serial:' . $arrVal[2] . ' / ' . 'carNum:' . $arrVal[4] . "\r\n");
    fclose($fp);

    /* debug line
		echo "ip:".$arrVal[1]."\n";
		echo "serial:".$arrVal[2]."\n";
		echo "issueDate:".$arrVal[3]."\n"; 
		echo "Datetime:".$sDateTime." , Date:".$sDate." , MR_Hour:".$sHour."\n"; 
		echo "carnum:".$arrVal[4]."\n";
		echo "path:".$arrVal[5]."\n";
		echo "bin:".$arrVal[6]."\n";
		*/

    /* 20211019 처리보완 v0.3 Hong
		입차시 >> 
			[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
				[입출차내역] 입차기록 (입차기록은 시간만 차이나면 계속 입력해서 기록보존)
				[현재주차내역] 내역조회(차번) --> 추가 또는 업데이트 처리(차번 유일하게) 
				[입차카운트] 입차카운트 ++

		출차시 >>
			[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
				[입출차내역] 출차기록 (출차기록은 시간만 차이나면 계속 입력해서 기록보존)
				[현재주차내역] 현재주차 삭제 (차번으로 일괄 삭제)
				[출차카운트] 출차카운트 ++ 
		*/

    $Prevdate = date('Y-m-d H:i:s', strtotime('-1 minute')); //비교 기준시간

    if ($sType == 0) {
        // 시리얼코드 2번째 자리가 0이면 입차 - - - -
        //WB_ParkCarHist (입차내역 조회-차번, 기간 조건으로)
        $sql8 = "select carNum, gatedate from wb_ParkCarHist where GateDate >= '" . $Prevdate . "' and CarNum = '" . $arrVal[4] . "' and mid(GateSerial,2,1) = '0'";
        $res8 = mysqli_query($conn, $sql8);
        $Rcount = mysqli_num_rows($res8);
        //fwrite($fp, "입차내역 조회(8) ".$sql8." --> 조회 결과 : ".$Rcount."\r\n");

        if ($Rcount < 1) {
            //중복없으면
            //WB_ParkCarHist (차량출입내역 추가-입출차 공통)
            $sql2 =
                "insert into wb_ParkCarHIST (GateDate, GateSerial, CarNum) 
							values ('" .
                $sDateTime .
                "', '" .
                $arrVal[2] .
                "', '" .
                $arrVal[4] .
                "')";
            $res = mysqli_query($conn, $sql2);

            $idx = mysqli_insert_id($conn);
            $sql22 = "insert into wb_ParkCarImg (idx, CarNum_img, CarNum_imgname) values ({$idx}, '{$arrVal[6]}', '{$arrVal[5]}')";
            $res22 = mysqli_query($conn, $sql22);

            //WB_ParkcarNow (현재주차차량 중복 확인, 차번조건)
            $sql31 = "select carNum, gatedate from wb_ParkCarNow where CarNum = '" . $arrVal[4] . "'";
            $res31 = mysqli_query($conn, $sql31);
            $Scount = mysqli_num_rows($res31);
            //fwrite($fp, "입차) 현재주차차량 중복 확인(31) ".$sql31." .. 조회 결과 : ".$Scount."\n");

            if ($Scount < 1) {
                //중복없으면
                //WB_ParkCarNow (현재 주차중차량 추가)
                $sql3 =
                    "insert into wb_ParkCarNow (idx, GateDate, GateSerial, CarNum) 
								values (" .
                    $idx .
                    " ,'" .
                    $sDateTime .
                    "', '" .
                    $arrVal[2] .
                    "', '" .
                    $arrVal[4] .
                    "')";
                $res = mysqli_query($conn, $sql3);
                //fwrite($fp, "입차)현재주차차량 중복 없음 > 현재 주차중차량 추가(3) ".$sql3."\n");
            } else {
                //WB_ParkCarNow (기존 주차차량 업데이트)
                $sql4 = "update wb_ParkCarNow set GateDate='" . $sDateTime . "', GateSerial='" . $arrVal[2] . "' Where CarNum='" . $arrVal[4] . "'";
                $res = mysqli_query($conn, $sql4);
                // fwrite($fp, "입차)현재주차차량 중복 있음 > 기존 주차차량 업데이트(4) ".$sql4_t."\n");
            }

            //WB_ParkCarInCnt (입차카운트 누적-기존내역 조회)
            $sql4 = "select RegDate from wb_ParkCarInCnt where RegDate = '" . $sDate . "' and ParkGroupCode = '" . $sGcode . "'";
            $res4 = mysqli_query($conn, $sql4);
            $Incount = mysqli_num_rows($res4);
            // fwrite($fp, "입차카운트 누적(4) ".$sql4." .. 조회 결과 : ".$Incount."\n");

            if ($Incount > 0) {
                $sql5 = 'update wb_ParkCarInCnt set MR' . $sHour . '=MR' . $sHour . "+1, DaySum=DaySum+1 where ParkGroupCode='" . $sGcode . "' and Regdate='" . $sDate . "'";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "입차카운트 중복 있음 > Incount > 0 (5) ".$sql5."\n");
            } else {
                $sql5 =
                    "insert into wb_ParkCarInCnt (ParkGroupCode, Regdate, MR0, MR1, MR2, MR3, MR4, MR5, MR6, MR7, MR8, MR9, MR10, MR11, MR12, MR13, MR14, MR15, MR16, MR17, MR18, MR19, MR20, MR21, MR22, MR23, daysum) values ('" .
                    $sGcode .
                    "', '" .
                    $sDate .
                    "', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 )";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "입차카운트 중복 없음 (insert) > Incount > 0 (5) ".$sql5."\n");

                $sql5 = 'update wb_ParkCarInCnt set MR' . $sHour . '=MR' . $sHour . "+1, DaySum=DaySum+1 where ParkGroupCode='" . $sGcode . "' and Regdate='" . $sDate . "'";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "입차카운트 중복 없음 (update) > Incount > 0 (5) ".$sql5."\n");
            }
        }
    } elseif ($sType == 1) {
        // 시리얼코드 2번째 자리가 1이면 출차 - - - -
        //WB_ParkCarHist (출차내역 조회-차번, 기간 조건으로)
        $sql8 = "select carNum, gatedate from wb_ParkCarHist where GateDate >= '" . $Prevdate . "' and CarNum = '" . $arrVal[4] . "' and mid(GateSerial,2,1) = '1'";
        $res8 = mysqli_query($conn, $sql8);
        $Rcount = mysqli_num_rows($res8);
        //fwrite($fp, "출차내역 조회(8) ".$sql8." .. 조회 결과 : ".$Rcount." / ");

        if ($Rcount < 1) {
            //중복없으면
            //WB_ParkCarHist (차량출입내역 추가-입출차 공통)
            $sql2 =
                "insert into wb_ParkCarHIST (GateDate, GateSerial, CarNum) 
							values ('" .
                $sDateTime .
                "', '" .
                $arrVal[2] .
                "', '" .
                $arrVal[4] .
                "')";
            $res = mysqli_query($conn, $sql2);

            $idx = mysqli_insert_id($conn);
            $sql22 = "insert into wb_ParkCarImg (idx, CarNum_img, CarNum_imgname) values ({$idx}, '{$arrVal[6]}', '{$arrVal[5]}')";
            $res22 = mysqli_query($conn, $sql22);

            //WB_ParkCarNow (현재 주차중차량 삭제 - 차량번호 일치하면 일괄삭제)
            $sql23 = "delete from wb_ParkCarNow where CarNum = '" . $arrVal[4] . "' ";
            $res = mysqli_query($conn, $sql23);
            // fwrite($fp, "출차) 현재주차중차량 삭제(21) ".$sql22."\n");

            //WB_ParkCarOutCnt (출차카운트 누적)
            $sql4 = "select RegDate from wb_ParkCarOutCnt where RegDate = '" . $sDate . "' and ParkGroupCode = '" . $sGcode . "'";
            $res4 = mysqli_query($conn, $sql4);
            $Incount = mysqli_num_rows($res4);
            // fwrite($fp, "출차카운트누적(2) ".$sql4." .. 조회 결과 : ".$Incount."\n");

            if ($Incount > 0) {
                $sql5 = 'update wb_ParkCarOutCnt set MR' . $sHour . '=MR' . $sHour . "+1, DaySum=DaySum+1 where ParkGroupCode='" . $sGcode . "' and Regdate='" . $sDate . "'";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "출차카운트 중복 있음 > Incount > 0 (5) ".$sql5."\n");
            } else {
                $sql5 =
                    "insert into wb_ParkCarOutCnt (ParkGroupCode, Regdate, MR0, MR1, MR2, MR3, MR4, MR5, MR6, MR7, MR8, MR9, MR10, MR11, MR12, MR13, MR14, MR15, MR16, MR17, MR18, MR19, MR20, MR21, MR22, MR23, daysum) values ('" .
                    $sGcode .
                    "', '" .
                    $sDate .
                    "', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 )";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "출차카운트 중복 없음 (insert) > Incount > 0 (5) ".$sql5."\n");

                $sql5 =
                    'update wb_ParkCarOutCnt set MR' .
                    $sHour .
                    '=MR' .
                    $sHour .
                    "+1, DaySum=DaySum+1
									where ParkGroupCode='" .
                    $sGcode .
                    "' and Regdate='" .
                    $sDate .
                    "'";
                $res = mysqli_query($conn, $sql5);
                // fwrite($fp, "출차카운트 중복 없음 (update) > Incount > 0 (5) ".$sql5."\n");
            }
        }
    }
    echo json_encode(['result_code' => '200']);
    //fclose($fp);
} else {
    echo " Request Value is Null \n";
    echo json_encode(['result_code' => '400']);
    //fclose($fp);
    exit();
}

?>
