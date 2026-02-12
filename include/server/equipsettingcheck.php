<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$type = $_GET['type'];
$sec = $_GET['sec'];
$result = [];

if (isset($_GET['sequence'])) {
    $sequence = $_GET['sequence'];
    $seq = explode(',', $sequence);

    if ($type == 'equip') {
        if ($seq[1] == '01') {
        } elseif ($seq[1] == '17') {
            $dao = new WB_BRDSEND_DAO();
            $vo = new WB_BRDSEND_VO();

            $vo = $dao->SELECT_SINGLE("SendCode = '{$seq[0]}'");

            $stat = $vo->BStatus;
            $startTime = date('YmdHis', strtotime($vo->RegDate));
        } elseif ($seq[1] == '18') {
            $dao = new WB_DISSEND_DAO();
            $vo = new WB_DISSEND_VO();

            $vo = $dao->SELECT_SINGLE("SendCode = '{$seq[0]}'");
            $stat = $vo->BStatus;
            $startTime = date('YmdHis', strtotime($vo->RegDate));
        } elseif ($seq[1] == '20') {
            $dao = new WB_GATECONTROL_DAO();
            $vo = new WB_GATECONTROL_VO();

            $vo = $dao->SELECT_SINGLE("GCtrCode = '{$seq[0]}'");
            $stat = $vo->GStatus;
            $startTime = date('YmdHis', strtotime($vo->RegDate));
        }

        $startDate = new DateTime($startTime);
        $endDate = new DateTime();
        $interval = $startDate->diff($endDate);

        if ($stat == 'error' || $stat == 'fail' || $sec < 1 || $interval->s >= 30) {
            $nmdao = new WB_EQUIP_DAO();
            $nmvo = $nmdao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");
            $nmdao->FAIL_QUERY($vo->CD_DIST_OBSV);

            $dao->FAIL_QUERY($seq[0]);
            $result['stat'] = 'fail';
            $result['name'] = $nmvo->NM_DIST_OBSV;
        } elseif ($stat == 'end') {
            $result['stat'] = 'success';
        } else {
            $result['stat'] = 'ing';
        }
    } else {
        $sequence = $_GET['sequence'];
        $seq = explode(',', $sequence);
        $val = $_GET['val'];
        $arr = explode(',', $val);

        $count = count($arr) - 1;
        $okCnt = 0;
        $failCnt = 0;
        $failNM = '';
        $failEquipList = [];
        $failEquipCode = [];
        $e = 0;

        if ($arr[0] == '01') {
            $result['stat'] = 'success';
        } else {
            if ($arr[0] == '17') {
                $dao = new WB_BRDSEND_DAO();
                $vo = new WB_BRDSEND_VO();

                for ($i = 1; $i < count($arr); $i++) {
                    $vo = $dao->SELECT_SINGLE("SendCode = '{$arr[$i]}'");
                    if ($vo->BStatus == 'end') {
                        $okCnt += 1;
                    } elseif ($vo->BStatus == 'fail' || $vo->BStatus == 'error') {
                        $failCnt += 1;
                    }

                    if ($vo->BStatus != 'end') {
                        if ($e == 0) {
                            $failNM = "{$seq[$i]}";
                        } else {
                            $failNM = "{$failNM},{$seq[$i]}";
                        }

                        $failEquipList[$e] = $vo->CD_DIST_OBSV;
                        $failEquipCode[$e++] = $arr[$i];
                    }

                    if ($i == 1) {
                        $startTime = date('YmdHis', strtotime($vo->RegDate));
                    }
                }
            } elseif ($arr[0] == '18') {
                $dao = new WB_DISSEND_DAO();
                $vo = new WB_DISSEND_VO();

                for ($i = 1; $i < count($arr); $i++) {
                    $vo = $dao->SELECT_SINGLE("SendCode = '{$arr[$i]}'");
                    if ($vo->BStatus == 'end') {
                        $okCnt += 1;
                    } elseif ($vo->BStatus == 'fail' || $vo->BStatus == 'error') {
                        $failCnt += 1;
                    }

                    if ($vo->BStatus != 'end') {
                        if ($e == 0) {
                            $failNM = "{$seq[$i]}";
                        } else {
                            $failNM = "{$failNM},{$seq[$i]}";
                        }

                        $failEquipList[$e] = $vo->CD_DIST_OBSV;
                        $failEquipCode[$e++] = $arr[$i];
                    }

                    if ($i == 1) {
                        $startTime = date('YmdHis', strtotime($vo->RegDate));
                    }
                }
            } elseif ($arr[0] == '20') {
                $dao = new WB_GATECONTROL_DAO();
                $vo = new WB_GATECONTROL_VO();

                for ($i = 1; $i < count($arr); $i++) {
                    $vo = $dao->SELECT_SINGLE("GCtrCode = '{$arr[$i]}'");
                    if ($vo->GStatus == 'end') {
                        $okCnt += 1;
                    } elseif ($vo->GStatus == 'fail' || $vo->GStatus == 'error') {
                        $failCnt += 1;
                    }

                    if ($vo->GStatus != 'end') {
                        if ($e == 0) {
                            $failNM = "{$seq[$i]}";
                        } else {
                            $failNM = "{$failNM},{$seq[$i]}";
                        }

                        $failEquipList[$e] = $vo->CD_DIST_OBSV;
                        $failEquipCode[$e++] = $arr[$i];
                    }

                    if ($i == 1) {
                        $startTime = date('YmdHis', strtotime($vo->RegDate));
                    }
                }
            }

            $startDate = new DateTime($startTime);
            $endDate = new DateTime();
            $interval = $startDate->diff($endDate);

            if ($count == $okCnt) {
                $result['stat'] = 'success';
            } elseif ($count == $okCnt + $failCnt || $count == $failCnt || $interval->s >= 30) {
                for ($i = 0; $i < count($failEquipList); $i++) {
                    $equipdao = new WB_EQUIP_DAO();

                    $dao->FAIL_QUERY($failEquipCode[$i]);
                    $equipdao->FAIL_QUERY($failEquipList[$i]);
                }
                $result['stat'] = 'fail';
                $result['failNM'] = $failNM;
            } else {
                $result['stat'] = 'ing';
            }
        }
    }

    echo json_encode($result);
}
