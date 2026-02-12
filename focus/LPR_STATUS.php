<?php
// 2023.12.11 차단기상태 자동업데이트 프로세스
// 차단단말장비 --> 주기적인 상태전송(기본 10분) --> 상태 업데이트
//include "../../../include/dbconn.php";
// 192.168.83.88 접속방식 수동세팅
$conn = mysqli_connect('127.0.0.1', 'userWooboWeb', 'wooboWeb!@', 'warning');
header('Content-Type: application/json; charset=UTF-8');

$req_json = file_get_contents('php://input');
$req_json = preg_replace('/[\x00-\x1F\x7F]/u', '', $req_json);

/*
	$req_json = '
	{
		"ip":"192.168.10.6", 
		"serial":"0000-2066",
		"date":"2023-12-11 16:28:00", 
		"cmd":"1"
	}';
	*/

if (strlen($req_json) < 1) {
    echo json_encode(['resultCode' => '400', 'resultMessage' => 'parameter is NULL!']);
    exit();
}

$arrv = json_decode($req_json, true); //배열로 변환

$arrVal = [];

$arrVal[0] = 'False'; // process Flag
$arrVal[1] = $arrv['ip']; // ip
$arrVal[2] = substr($arrv['serial'], -4); // 시리얼코드 중 오른쪽 4자리만 사용
$arrVal[3] = date('Y-m-d H:i:s', strtotime($arrv['date'])); // 단말장비 시간
$arrVal[4] = $arrv['cmd']; // 상태 >> 1:올림(고정), 2:올림(자동), 3:내림

if (strlen($arrVal[4]) > 0) {
    $arrVal[0] = 'True';
} // 이후 처리

// Debug log ---------------------------------------------------
$fp = fopen('log/GATE_' . date('ym', strtotime('Now')) . '.txt', 'a');
fwrite($fp, date('Y-m-d H:i:s', strtotime('Now')) . ' ');
fwrite($fp, ' / serial:' . $arrVal[2] . ' / ip:' . $arrVal[1] . ' / cmd:' . $arrVal[4] . ' / gateTime:' . $arrVal[3] . " \r\n");
fclose($fp);
//-----------------------------------------

// Database Insert
if ($arrVal[0] == 'True') {
    // WB_Equip 차단기 등록정보 조회
    $sql8 = "select CD_DIST_OBSV from wb_Equip where CD_DIST_OBSV = '{$arrVal[2]}'";
    $res8 = mysqli_query($conn, $sql8);
    $Rcount = mysqli_num_rows($res8);

    if ($Rcount < 1) {
        //등록된 내용이 없으면
        echo json_encode(['result_code' => '400', 'resultMessage' => 'WB_Equip.CD_DIST_OBSV value no match!']);
        exit();
    } else {
        // WB_Equip 상태수신 업데이트
        $sql = "UPDATE wb_equip SET LastDate = '{$arrVal[3]}', LastStatus = 'OK' 
			WHERE CD_DIST_OBSV = '{$arrVal[2]}'";
        mysqli_query($conn, $sql);

        // wb_gatestatus 업데이트
        switch ($arrVal[4]) {
            case '1':
                $cmdVal = 'open(fix)';
                break;
            case '2':
                $cmdVal = 'open(auto)';
                break;
            case '3':
                $cmdVal = 'close';
                break;
            default:
                $cmdVal = 'open(?)';
        }

        //WB_gatestatus // 차단기 상태 업데이트
        $sql31 = "select Gate, Regdate from wb_gatestatus where CD_DIST_OBSV = '{$arrVal[2]}' ";
        $res31 = mysqli_query($conn, $sql31);
        $Scount = mysqli_num_rows($res31);

        if ($Scount < 1) {
            //중복없으면
            //신규 추가
            $sql3 =
                "insert into wb_gatestatus (CD_DIST_OBSV, RegDate, Gate) 
							values ('" .
                $arrVal[2] .
                "' ,'" .
                $arrVal[3] .
                "', '" .
                $cmdVal .
                "')";
            $res = mysqli_query($conn, $sql3);
        } else {
            //기존 자료 업데이트
            $sql4 = "update wb_gatestatus set RegDate='" . $arrVal[3] . "', Gate='" . $cmdVal . "' Where CD_DIST_OBSV='" . $arrVal[2] . "'";
            $res = mysqli_query($conn, $sql4);
        }

        echo json_encode(['result_code' => '200']);
    }
} else {
    echo json_encode(['result_code' => '400', 'resultMessage' => 'request CMD value is NULL!']);
    exit();
}
/**/
?>
