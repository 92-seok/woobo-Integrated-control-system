<?php
// 2021.08.25 JSON Parsing Change
include '../../../include/dbconn.php';
header('Content-Type: application/json; charset=UTF-8');

//if (!in_array('application/json', explode(';', $_SERVER['CONTENT_TYPE']) ) )
//{
//	echo json_encode(array('result_code'=>'400'));
//	exit;
//}

$req_json = file_get_contents('php://input');
$req_json = preg_replace('/[\x00-\x1F\x7F]/u', '', $req_json);

// $req_json = '{
//     "unit":{
//         "fullCode":"0082-0000-0001-0001-0008-0001"
//     },
//     "errCode":"0801",
//     "status":"2",
//     "sendTime":"1622606807"
// }' Keep Alive

// $req_json = '{
// 	"fullCode":"0082-0000-0001-0001-0008-0001",
// 	"carno":"12가1234",
// 	"action":"OPEN",
// 	"status":"SUCCESS"
// }' Bar Action

if (strlen($req_json) < 1) {
    echo json_encode(['result_code' => '400', 'result' => 'Request json value Null']);
    exit();
}

$fp = fopen('log/log_' . date('ym', strtotime('Now')) . '.txt', 'a');
fwrite($fp, date('Y-m-d H:i:s', strtotime('Now')));

$arrv = json_decode($req_json, true); //배열로 변환
$status = $arrv['status'];

fwrite($fp, $req_json . "\r\n");
fclose($fp);
// Database Insert
if (strtolower($status) != 'false') {
    // Keep Alive
    if (strtolower($status) == 'success') {
        // Bar Action
        $gateSerial = substr($arrv['full_code'], -4);
        $sendTime = date('Y-m-d H:i:s', time());
        $action = strtolower($arrv['action']);
        $result = [];
        if ($action == 'open' || $action == 'close') {
            $actionSql = "UPDATE wb_gatestatus SET RegDate = now(), Gate = '{$action}' WHERE CD_DIST_OBSV = '{$gateSerial}'";
            mysqli_query($conn, $actionSql);
            //$result["action:"] = $actionSql;
            $resultSql = "UPDATE wb_gatecontrol SET RetDate = now(), GStatus = 'end', Gate='{$action}' WHERE CD_DIST_OBSV = '{$gateSerial}' and RetDate is null order by GCtrCode DESC limit 1";
            mysqli_query($conn, $resultSql);
            //$result["result:"] = $resultSql;
            $statusSql = "UPDATE wb_equip SET LastDate = now(), LastStatus = 'ok' WHERE CD_DIST_OBSV = '{$gateSerial}'";
            mysqli_query($conn, $statusSql);
            //$result["status:"] = $statusSql;
        }

        //echo json_encode($result);
        echo json_encode(['result_code' => '200']);
    }
} else {
    echo " Request Value is Null \n";
    echo json_encode(['result_code' => '400']);
    exit();
}

?>
