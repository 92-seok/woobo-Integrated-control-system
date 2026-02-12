<?php
/**
 * 20211019 처리보완 v0.3 Hong
 * 입차시 >>
 * 		[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
 *		[입출차내역] 입차기록 (입차기록은 시간만 차이나면 계속 입력해서 기록보존)
 *		[현재주차내역] 내역조회(차번) --> 추가 또는 업데이트 처리(차번 유일하게)
 *		[입차카운트] 입차카운트 ++
 *
 *  출차시 >>
 * 		[조회] 내역조회(최근1분, 차번) --> 내역 중복 없을시
 *		[입출차내역] 출차기록 (출차기록은 시간만 차이나면 계속 입력해서 기록보존)
 *		[현재주차내역] 현재주차 삭제 (차번으로 일괄 삭제)
 *		[출차카운트] 출차카운트 ++
 *
 *  2023.07.03 강원도청 연계를 위해 Car In/Out 데이터 추가
 */

header('Content-Type: application/json; charset=UTF-8');

$req_json = file_get_contents('php://input');
$req_json = preg_replace('/[\x00-\x1F\x7F]/u', '', $req_json);

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';

try {
    $res = [];

    $path = $_SERVER['DOCUMENT_ROOT'] . '/lprLog';
    $fileName = date('Y-m') . '.txt';
    $log = new writeLog($path, $fileName);
    $log->write(date('Y-m-d H:i:s') . '] ', false);

    if (strlen($req_json) < 1) {
        throw new Exception('JSON 형식이 잘못되었습니다.', 400);
    }

    $arrv = json_decode($req_json, true); //배열로 변환

    $arrVal = [];
    $ip = $arrv['dev']['ip'];
    $serial = substr($arrv['dev']['serial'], -4); // 시리얼코드 중 오른쪽 4자리만 사용
    $date = date('Y-m-d H:i:s');
    $prevDate = date('Y-m-d H:i:s', strtotime('-1 minute')); //비교 기준시간

    $sGcode = substr($serial, 0, 1); // 시리얼코드 1번째자리,  주차장그룹코드 (0~9)
    $sType = substr($serial, 1, 1); // 시리얼코드 2번째자리,  0:입차, 1:출차
    $sDateTime = date('Y-m-d H:i:s', strtotime($date)); // YYYY-MM-DD hh:mm:ss type
    $sDate = date('Ymd', strtotime($date)); // YYYYMMDD type date
    $sHour = ((int) date('H', strtotime($date))) + 1; // Hour

    $carNum = $arrv['lpr'][0]['proc']['carnum'];
    $path = $arrv['lpr'][0]['img']['path'];
    $bin = $arrv['lpr'][0]['img']['bin'];

    //차량번호 수신시 이후 처리
    if (strlen($carNum) < 1) {
        throw new Exception('JSON 형식이 잘못되었습니다.', 400);
    }
    if (strpos($sGcode, ';') !== false) {
        throw new Exception('Serial Code가 잘못되었습니다', 400);
    }

    // Log 기록
    $log->write("{$serial}/{$carNum}");

    // 공통 DAO 선언
    $equip_dao = new WB_EQUIP_DAO();
    $hist_dao = new WB_PARKCARHIST_DAO();
    $now_dao = new WB_PARKCARNOW_DAO();
    $img_dao = new WB_PARKCARIMG_DAO();
    $group_dao = new WB_PARKGATEGROUP_DAO();

    // 공통 VO 선언
    $group_vo = $group_dao->SINGLE("ParkJoinGate LIKE '%{$serial}%'");

    $car_vo = new WB_PARKCAR_VO();
    $car_vo->GateDate = $date;
    $car_vo->GateSerial = $serial;
    $car_vo->CarNum = $carNum;

    $img_vo = new WB_PARKCARIMG_VO();
    $img_vo->CarNum_Img = $bin;
    $img_vo->CarNum_Imgname = $path;

    $cnt_vo = new WB_PARKCARCNT_VO();
    if ($group_vo !== false) {
        $cnt_vo->ParkGroupCode = $group_vo->ParkGroupCode;
    }
    $cnt_vo->RegDate = $sDate;

    $equip_vo = $equip_dao->SINGLE("CD_DIST_OBSV = '{$serial}'");

    if ($sType == 0) {
        // 입차
        // DAO 호출
        $carIn_dao = new WB_PARKCARINCNT_DAO();

        // WB_ParkCarHist (입차내역 조회-차번, 기간 조건으로)
        $param = (object) ['carNum' => $carNum];

        $duplicateChk = $hist_dao->SEL_PRE($param, "GateDate >= '{$prevDate}' AND CarNum = :carNum AND MID(GateSerial, 2, 1) = '0'");

        // 중복 검사
        // TODO: 1분 이내로 재출입시 쓰레기값으로 버렸지만 실제로 1분안에 가능 할 수도 있기에 변경 필요, 중복일 경우 throw 해야하지만 차단기 업체측과 협의가 안됨
        if (count($duplicateChk) < 1) {
            $hist_dao->insertCarInfo($car_vo);
            $idx = $hist_dao->INSERTID();

            $img_vo->idx = $idx;
            $img_dao->insertCarImg($img_vo);

            $equip_dao->Update("LastDate = '{$date}', LastStatus = 'ok'", "CD_DIST_OBSV = '{$serial}'");

            if ($carNum != '0000000000' && $group_vo !== false) {
                $car_vo->idx = $idx;
                $now_dao->insertCarInfo($car_vo);

                $rowChk = $carIn_dao->SINGLE("ParkGroupCode = {$cnt_vo->ParkGroupCode} AND RegDate = '{$cnt_vo->RegDate}'");

                if ($rowChk !== false && $rowChk->{"MR{$sHour}"} === null) {
                    $carIn_dao->carCountUpColNull($cnt_vo, "MR{$sHour}");
                } elseif ($rowChk === false || $rowChk->{"MR{$sHour}"} !== null) {
                    $carIn_dao->carCountUp($cnt_vo, "MR{$sHour}");
                }

                // 강원도청 연계 위한 장비 번호로 Counting
                if ($equip_vo !== false && $equip_vo->DetCode != null) {
                    $cnt_vo->ParkGroupCode = $equip_vo->DetCode;
                    $rowChk = $carIn_dao->SINGLE("ParkGroupCode = {$cnt_vo->ParkGroupCode} AND RegDate = '{$cnt_vo->RegDate}'");

                    if ($rowChk !== false && $rowChk->{"MR{$sHour}"} === null) {
                        $carIn_dao->carCountUpColNull($cnt_vo, "MR{$sHour}");
                    } elseif ($rowChk === false || $rowChk->{"MR{$sHour}"} !== null) {
                        $carIn_dao->carCountUp($cnt_vo, "MR{$sHour}");
                    }
                }
            }
        }
    } elseif ($sType == 1) {
        // 출차
        // DAO 호출
        $carOut_dao = new WB_PARKCAROUTCNT_DAO();

        // WB_ParkCarHist (입차내역 조회-차번, 기간 조건으로)
        $param = (object) ['carNum' => $carNum];

        $duplicate_check = $hist_dao->SEL_PRE($param, "GateDate >= '{$prevDate}' AND CarNum = :carNum AND MID(GateSerial, 2, 1) = '1'");

        // 중복 검사
        // TODO: 1분 이내로 했지만 실제로 1분안에 가능 할 수도 있기에 변경 필요, 중복일 경우 throw 해야하지만 차단기 업체측과 협의가 안됨
        if (count($duplicate_check) < 1) {
            $hist_dao->insertCarInfo($car_vo);
            $idx = $hist_dao->INSERTID();

            $img_vo->idx = $idx;
            $img_dao->insertCarImg($img_vo);

            $equip_dao->Update("LastDate = '{$date}', LastStatus = 'ok'", "CD_DIST_OBSV = '{$serial}'");

            if ($carNum != '0000000000' && $group_vo !== false) {
                $now_dao->outCarDelete($car_vo->CarNum);
                $now_dao->delete3DaysInfo();

                $rowChk = $carOut_dao->SINGLE("ParkGroupCode = {$cnt_vo->ParkGroupCode} AND RegDate = '{$cnt_vo->RegDate}'");

                if ($rowChk !== false && $rowChk->{"MR{$sHour}"} === null) {
                    $carOut_dao->carCountDownColNull($cnt_vo, "MR{$sHour}");
                } elseif ($rowChk === false || $rowChk->{"MR{$sHour}"} !== null) {
                    $carOut_dao->carCountDown($cnt_vo, "MR{$sHour}");
                }

                // 강원도청 연계 위한 장비 번호로 Counting
                if ($equip_vo !== false && $equip_vo->DetCode != null) {
                    $cnt_vo->ParkGroupCode = $equip_vo->DetCode;
                    $rowChk = $carOut_dao->SINGLE("ParkGroupCode = {$cnt_vo->ParkGroupCode} AND RegDate = '{$cnt_vo->RegDate}'");

                    if ($rowChk !== false && $rowChk->{"MR{$sHour}"} === null) {
                        $carOut_dao->carCountDownColNull($cnt_vo, "MR{$sHour}");
                    } elseif ($rowChk === false || $rowChk->{"MR{$sHour}"} !== null) {
                        $carOut_dao->carCountDown($cnt_vo, "MR{$sHour}");
                    }
                }
            }
        }
    }

    throw new Exception('', 200);
} catch (Exception $ex) {
    $res['result_code'] = $ex->getCode();
    $res['result_message'] = $ex->getMessage();

    if ($ex->getCode() === 400) {
        $log->write(' Request json value Null');
    }
} finally {
    echo json_encode($res);
}

?>
