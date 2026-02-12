<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
$KAKAO_API = getenv('KAKAO_API') ?? false;
?>
<?php
header('Content-Type: application/json'); // equip, title, tType, sDate, sTime, sMin, repeat, type, ment, content
$equip = $_POST['equip'];
$title = $_POST['title'];
$tType = $_POST['tType'];
$sDate = $_POST['sDate'];
$sTime = $_POST['sTime'];
$sMin = $_POST['sMin'];
$repeat = $_POST['repeat'];
$type = $_POST['type'];
$ment = $_POST['ment'];
$content = $_POST['content'];
$now = time();
if ($tType == 'general') {
    $bDate = date('Y-m-d H:i:s', $now);
    $bTime = 'now';
} elseif ($tType == 'reserve') {
    $bDate = $sDate . ' ' . $sTime . ':' . $sMin . ':00';
    $bTime = 'reserve';
}
// 1. 방송list 추가
$sql = 'insert into wb_brdlist (CD_DIST_OBSV, Title, BType, BrdType, AltMent, TTSContent, RevType, BrdDate, BRepeat, RegDate) ';
$sql .= " values ('{$equip}','{$title}','{$tType}','{$type}','{$ment}','{$content}','{$bTime}','{$bDate}','{$repeat}', now())";
$res = mysqli_query($conn, $sql);
if ($res === false) {
    $resultArray = ['code' => '01'];
    echo json_encode($resultArray);
}
$BCode = mysqli_insert_id($conn); // 직전에 저장된 방송 내역 (wb_brdlist.BCode AUTO_INCREMENT PK)
// 2. 방송 list 상세 추가 1..N
$equipList = explode(',', $equip);
foreach ($equipList as $CD_DIST_OBSV) {
    $subSql = 'insert into wb_brdlistdetail (BCode, CD_DIST_OBSV, BrdStatus, RegDate) ';
    $subSql .= " values ('{$BCode}', '{$CD_DIST_OBSV}', 'start', now())";
    $subRes = mysqli_query($conn, $subSql);
    if ($subRes === false) {
        // continue
    } else {
        // 3. 즉시 방송인경우 jhbsend에 등록
        if ($tType == 'general') {
            if ($type == 'tts') {
                $cmd = 'B010';
                $param3 = preg_replace('/\r\n|\r|\n/', ' ', $content); // TTS문구
            } elseif ($type == 'alert') {
                $cmd = 'B020';
                $param3 = $ment;
                // TTS문구
            }
            $param1 = '00000000'; // 그룹코드
            $param2 = $repeat; // 방송횟수
            $param4 = $BCode; // 리스트번호
            $bSql = 'insert into wb_brdsend (CD_DIST_OBSV, RCMD, Parm1, Parm2, Parm3, Parm4, RegDate, BStatus) ';
            $bSql .= " values ('{$CD_DIST_OBSV}', '{$cmd}', '{$param1}', '{$param2}', '{$param3}', '{$param4}', now(), 'start')";
            $bRes = mysqli_query($conn, $bSql);
            if ($bRes === false) {
                // continue
            } else {
                // ok
            }
        }
    }
}
$resultArray = ['code' => '00'];
echo json_encode($resultArray); ///////////////////////////////////////////////////////////////////////////////
/// 정선 지역 onpoom 온품 방송장비 연계 (php -> nodejs GISServer)
function sendAlert($id, $text, $geocode, $eventtime) {
    // $NODE_GIS_HOST = getenv("NODE_GIS_HOST") ?? "127.0.0.1";
    // $NODE_GIS_PORT = getenv("NODE_GIS_PORT") ?? 3030;
    $ip = '127.0.0.1';
    $port = '3030';
    $query_string = http_build_query(['id' => $id, 'text' => $text, 'geocode' => $geocode, 'eventtime' => $eventtime]);
    $url = "http://{$ip}:{$port}/sendalert?{$query_string}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
    $head = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($head === false) {
        return false;
    }
    if ($httpCode !== 200) {
        return false;
    }
    return true;
} /// CD_DIST_OBSV 에서 방송장비의 지역코드 DSCODE 를 얻은 후, Comma separated 에서 Space separated 로 바꿔서 nodejs로 보냄
/// GET '/sendalert'
// $params = array_fill(0, count($equipList), '?');
// $commaSeparatedParams = implode(",", $params);
// $prepare = "SELECT DSCODE FROM wb_equip WHERE CD_DIST_OBSV IN ($commaSeparatedParams)";
// $prepare = "SELECT DSCODE FROM wb_equip WHERE CD_DIST_OBSV IN (10016)";
// $prepare = "select DSCODE,  CD_DIST_OBSV, NM_DIST_OBSV, ConnPhone, LastStatus from wb_equip where USE_YN = '1' and CD_DIST_OBSV IN ('10016')";
// $prepare = "select DSCODE,  CD_DIST_OBSV, NM_DIST_OBSV, ConnPhone, LastStatus from wb_equip where USE_YN = '1' and CD_DIST_OBSV IN ($commaSeparatedParams)";
$prepare = "select DSCODE,  CD_DIST_OBSV, NM_DIST_OBSV, ConnPhone, LastStatus from wb_equip where USE_YN = '1' and CD_DIST_OBSV IN ($equip)"; // echo $prepare;
try {
    // $res = $conn->execute_query($prepare, $equipList);
    $result = mysqli_query($conn, $prepare);
    $rowCount = mysqli_num_rows($result); // $rows = $result->fetch_all(MYSQLI_ASSOC);
    // $row = mysqli_fetch_array( $result );
    $DSCODE_STRING_BUILDER = ''; // $DSCODE = "";
    while ($row = mysqli_fetch_array($result)) {
        $DSCODE = $row['DSCODE']; // $geocode = implode(" ", $DSCODE); // Space seperated values
        $DSCODE_STRING_BUILDER .= $DSCODE . ' '; // Space seperated values
    }
    $geocode = $DSCODE_STRING_BUILDER = rtrim($DSCODE_STRING_BUILDER); // $BCode = $BCode;
    $TTSContent = $content;
    $BrdDate = date('YmdHis', $now);
    $response = sendAlert($BCode, $TTSContent, rtrim($geocode), $BrdDate);
    if ($response === false) {
        // continue
    }
} catch (PDOException | Exception $e) {
    echo 'exception';
    echo $e;
} // ///////////////////////////////////////////////////////////////////////////////
// /// 정선 지역 onpoom 온품 방송장비 연계 (php -> nodejs GISServer)
// function sendAlert($id, $text, $geocode, $eventtime) {
// 	// $NODE_HOST = getenv("NODE_HOST") ?? "127.0.0.1";
// 	// $NODE_PORT = getenv("NODE_PORT") ?? 5555;
// 	$ip = '127.0.0.1';
// 	$port = '3030';
// 	$query_string = http_build_query([
// 		'id' => $id,
// 		'text' => $text,
// 		'geocode' => $geocode,
// 		'eventtime' => $eventtime
// 	]);
// 	$url = "http://{$ip}:{$port}/sendalert?{$query_string}";
// 	$ch = curl_init();
// 	curl_setopt($ch, CURLOPT_URL, $url);
// 	curl_setopt($ch, CURLOPT_HTTPGET, true);
// 	curl_setopt($ch, CURLOPT_HEADER, true);
// 	curl_setopt($ch, CURLOPT_NOBODY, true);
// 	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 	curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
// 	curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000);
// 	$head = curl_exec($ch);
// 	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// 	curl_close($ch);
// 	if ($head === false) {
// 		return false;
// 	}
// 	if ($httpCode !== 200) {
// 		return false;
// 	}
// 	return true;
// }
// /// CD_DIST_OBSV 에서 방송장비의 지역코드 DSCODE 를 얻은 후, Comma separated 에서 Space separated 로 바꿔서 nodejs로 보냄
// /// GET '/sendalert'
// $params = array_fill(0, count($equipList), '?');
// $commaSeparatedParams = implode(",", $params);
// $prepare = "SELECT DSCODE FROM wb_equip WHERE CD_DIST_OBSV IN ($commaSeparatedParams)";
// $result = $conn->execute_query($prepare, $equipList);
// $rows = $result->fetch_all(MYSQLI_ASSOC);
// if ($rows !== false) {
// 	$DSCODE = array_column($rows, "DSCODE");
// 	$geocode = implode(" ", $DSCODE); // Space seperated values
// 	$BCode = $insertID;
// 	$TTSContent = $content;
// 	$BrdDate = date("YmdHis", $now);
// 	$response = sendAlert($BCode, $TTSContent, rtrim($geocode), $BrdDate);
// 	if ($response === false) {
// 		// continue
// 	}
// }
/// 데이터 json 포맷 예시
/// 실제 사용되는 데이터는 nodejs 에서 가공하므로 php에서 만들 필요 없음
// $json = "{
// 	"alertsCount": 1,
// 	"alerts": [
// 		{
// 			"identifier": "6_12_{$now}_{$insertID}",
// 			"status": "Test",
// 			"msgType": "Alert",
// 			"source": "192.168.0.1",
// 			"category": "Safety",
// 			"event": "호우경보",
// 			"senderName": "산간계곡시스템",
// 			"headline": "20241023150000_시험_호우경보",
// 			"reportTime": "20241023150003",
// 			"eventTime": "20241023150003",
// 			"priority": "3",
// 			"geocode": "2300000000 2400000000 2500000000 4277000000",
// 			"Text40.ko-KR": "재해문자전광판 문안",
// 			"Text80.ko-KR": "재해문자전광판 문안",
// 			"Text180.ko-KR": "버스정보시스템 문안",
// 			"VoiceText.ko-KR": "포스트맨 테스트1",
// 			"resource": [
// 				{
// 					"type": "image",
// 					"url": "158.181.17.75"
// 				},
// 				{
// 					"type": "video",
// 					"url": "158.181.17.76"
// 				}
// 			]
// 		}
// 	]
// }";

