<?php
//die($_SERVER['QUERY_STRING']);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

// 2021.10.26 CarNumber Search 추가
if (isset($_GET['dType'])) {
    $qType = $_GET['dType'];
} else {
    $qType = 'before';
}
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['reqnum'])) {
    $reqnum = $_GET['reqnum'];
} else {
    $reqnum = '';
}

if (isset($_GET['parkcode'])) {
    $parkCode = $_GET['parkcode'];
} else {
    $parkCode = '';
}
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = '3';
}

if (isset($_GET['year1'])) {
    $year1 = $_GET['year1'];
} else {
    $year1 = date('Y', strtotime('-0days'));
}
if (isset($_GET['month1'])) {
    $month1 = $_GET['month1'];
} else {
    $month1 = date('m', strtotime('-0days'));
}
if (isset($_GET['day1'])) {
    $day1 = $_GET['day1'];
} else {
    $day1 = date('d', strtotime('-0days'));
}

if (isset($_GET['year2'])) {
    $year2 = $_GET['year2'];
} else {
    $year2 = date('Y', time());
}
if (isset($_GET['month2'])) {
    $month2 = $_GET['month2'];
} else {
    $month2 = date('m', time());
}
if (isset($_GET['day2'])) {
    $day2 = $_GET['day2'];
} else {
    $day2 = date('d', time());
}

$strNowDate = date('Y-m-d', strtotime('-3 day'));

// 아너스LPR 검색
$connModelSql = "SELECT ConnModel FROM wb_equip WHERE ConnModel = 'HNSLPR_LAN'";
$connModelRes = mysqli_query($conn, $connModelSql);

if (mysqli_num_rows($connModelRes) > 0) {
    $checkHnsLpr = 1;
} else {
    $checkHnsLpr = 0;
}
////////////////////////////////////////
// 아너스 LPR 이면
////////////////////////////////////////
if ($checkHnsLpr === 1) {
    $selectDate1 = $year1 . $month1 . $day1;
    $selectDate2 = $year2 . $month2 . $day2;

    $parkingSql = 'SELECT * FROM wb_parkgategroup';

    $url = 'frame/parkingCar.php?page=';
    $reqnumWhere = '1=1';
    $typeWhere = '1=1';

    // 주차장 그룹 분기
    if ($parkCode != '') {
        // CD_DIST_OBSV가 여러개인지 한개인지 확인
        $parkCodeArray = explode(',', $parkCode);
        // 쿼리문에 넣기 위해 스트링으로 변환
        $parkCodeString = "'" . implode("','", $parkCodeArray) . "'";
        $parkCodeWhere = "A.CD_DIST_OBSV IN ({$parkCodeString})";
    } else {
        $parkCodeWhere = '1=1';
    }

    if ($type != '3') {
        switch ($type) {
            case '0':
                $typeWhere = "DeviceCode = '01'";
                break;
            case '1':
                $typeWhere = "DeviceCode = '02'";
                break;
            case '2':
                $typeWhere = "DeviceCode = '01'";
                break;
            default:
                $typewhere = '1=1';
                break;
        }
    }

    if ($reqnum != '') {
        $reqnumWhere = "CarNumber LIKE '%" . $reqnum . "%'";
    }

    // 동작 부분 쿼리

    if ($qType == 'before' || $qType == 'normal') {
        $sql = "SELECT A.CarNumber, A.EventDateTime, A.DeviceCode, A.CD_DIST_OBSV, D.NM_DIST_OBSV
				FROM hns_lprdata AS A
				INNER JOIN wb_equip AS D ON A.CD_DIST_OBSV = D.CD_DIST_OBSV";
    } else {
        $sql = "SELECT A.CarNumber, A.EventDateTime, A.DeviceCode, A.CD_DIST_OBSV, D.NM_DIST_OBSV
			FROM hns_lprdata AS A
			INNER JOIN wb_equip AS D ON A.CD_DIST_OBSV = D.CD_DIST_OBSV
			WHERE A.EventDateTime BETWEEN '{$selectDate1}' AND '{$selectDate2}235959' AND {$parkCodeWhere} AND {$typeWhere} AND {$reqnumWhere}";
    }

    if ($type == '2') {
        $sql = "SELECT A.CarNumber, A.EventDateTime, A.DeviceCode, A.CD_DIST_OBSV, D.NM_DIST_OBSV FROM hns_lprdata AS A
	 INNER JOIN wb_equip AS D ON A.CD_DIST_OBSV = D.CD_DIST_OBSV
	 WHERE A.DeviceCode = '01' AND A.EventDateTime = (
SELECT MAX(B.EventDateTime) FROM hns_lprdata AS B WHERE B.CarNumber = A.CarNumber AND B.DeviceCode = '01' AND B.EventDateTime BETWEEN '{$selectDate1}' AND '{$selectDate2}235959'
AND {$parkCodeWhere} AND {$reqnumWhere}
)
AND NOT EXISTS (
SELECT 1 FROM hns_lprdata AS C WHERE C.CarNumber = A.CarNumber AND C.DeviceCode = '02' AND C.EventDateTime > A.EventDateTime)
AND A.EventDateTime >= DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 3 DAY), '%Y%m%d%h%i%s')";
    }
}
////////////////////////////////////////
// 다른 LPR 이면 (기존 소스)
////////////////////////////////////////
else {
    $selectDate1 = $year1 . '-' . $month1 . '-' . $day1;
    $selectDate2 = $year2 . '-' . $month2 . '-' . $day2;

    $parkingSql = 'SELECT * FROM wb_parkgategroup';

    $url = 'frame/parkingCar.php?page=';
    $reqnumWhere = '';
    $parkCodeWhere = '';
    $typeWhere = " mid(GateSerial,2,1) = '" . $type . "' and";
    $table = 'wb_parkcarhist';
    if ($reqnum != '') {
        $reqnumWhere = " and CarNum like '%" . $reqnum . "%'";
    }
    if ($parkCode != '') {
        $parkCodeWhere = " left(GateSerial,1) = '" . $parkCode . "' and";
    }
    if ($type == '2') {
        $table = 'wb_parkcarnow';
        $typeWhere = '';
    } elseif ($type == '3') {
        $typeWhere = " (mid(GateSerial,2,1) = '0' or mid(GateSerial,2,1) = '1') and";
    }

    if ($qType == 'before' || $qType == 'normal') {
        $sql = "select a.idx,a.CarNum,a.GateDate,a.GateSerial, mid(a.GateSerial,2,1) as type, b.ParkGroupName, c.NM_DIST_OBSV
            from wb_parkcarhist as a
            left join wb_parkgategroup as b on left(a.GateSerial,1) = b.ParkGroupCode
            LEFT join wb_equip AS c ON a.GateSerial = c.CD_DIST_OBSV";
    }
    // b.ParkGroupName from wb_parkcarhist as a left join wb_parkgategroup as b on left(a.GateSerial,1) = b.ParkGroupCode WHERE (GateDate between '".$selectDate1." 00:00:00' and '".$selectDate2." 23:59:59')";
    else {
        $sql =
            "SELECT a.idx,a.CarNum,a.GateDate,a.GateSerial, mid(a.GateSerial,2,1) as type, b.ParkGroupName, c.NM_DIST_OBSV
				FROM " .
            $table .
            " AS a left join wb_parkgategroup AS b on left(a.GateSerial,1) = b.ParkGroupCode
                   LEFT join wb_equip AS c ON a.GateSerial = c.CD_DIST_OBSV 
            WHERE " .
            $typeWhere .
            $parkCodeWhere .
            " (GateDate BETWEEN '" .
            $selectDate1 .
            " 00:00:00' AND '" .
            $selectDate2 .
            " 23:59:59')" .
            $reqnumWhere;
    }
}
$res = mysqli_query($conn, $sql);
$countRec = mysqli_num_rows($res);

$list = 25;
$block = 20;

$pageNum = ceil($countRec / $list); // 총 페이지
$blockNum = ceil($pageNum / $block); // 총 블록
$nowBlock = ceil($page / $block);

$s_page = $nowBlock * $block - ($block - 1);

if ($s_page <= 1) {
    $s_page = 1;
}
$e_page = $nowBlock * $block;
if ($pageNum <= $e_page) {
    $e_page = $pageNum;
}
?>
<div class="cs_frame">
    <!-- 차량 입출차 내역 -->
    <div class="cs_selectBox">
        <div class="cs_date">
            <form id="id_form" name="form" method="get" action="">
                <input type="hidden" name="arr" value="parkingCar.php">
                <input type="hidden" id="checkHnsLprValue" Value="<?= $checkHnsLpr ?>">
                <?php $parkingRes = mysqli_query($conn, $parkingSql); ?>
                <select name="parkcode">
                    <option value='' <?php if ($parkCode == '') {
                        echo 'selected';
                    } ?>>전체</option>
                    <?php while ($parkingRow = mysqli_fetch_assoc($parkingRes)) {
                        $parkCodeOptionValue = $checkHnsLpr === 1 ? $parkingRow['ParkJoinGate'] : $parkingRow['ParkGroupCode']; ?>
                    <option value="<?= $parkCodeOptionValue ?>" <?php if ($parkCode == $parkCodeOptionValue) {
    echo 'selected';
} ?>>
                        <?= $parkingRow['ParkGroupName'] ?></option>
                    <?php
                    } ?>
                </select>

                <select name="type">
                    <option value="0" <?php if ($type == '0') {
                        echo 'selected';
                    } ?>>입차</option>
                    <option value="1" <?php if ($type == '1') {
                        echo 'selected';
                    } ?>>출차</option>
                    <option value="3" <?php if ($type == '3') {
                        echo 'selected';
                    } ?>>입/출차</option>
                    <option value="2" <?php if ($type == '2') {
                        echo 'selected';
                    } ?>>현재 주차</option>
                </select>

                <select name="year1">
                    <?php for ($y = 2020; $y < date('Y', time()) + 1; $y++) {
                        if ($year1 == $y) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        } ?>
                    <option value="<?= $y ?>" <?= $selected ?>><?= $y ?></option>
                    <?php
                    } ?>
                </select> 년

                <select name="month1">
                    <?php for ($m = 1; $m < 13; $m++) {

                        if ($m < 10) {
                            $date = '0' . $m;
                        } else {
                            $date = $m;
                        }
                        if ($month1 == $date) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                    <option value="<?= $date ?>" <?= $selected ?>><?= $date ?></option>
                    <?php
                    } ?>
                </select> 월

                <select name="day1">
                    <?php for ($d = 1; $d < 32; $d++) {

                        if ($d < 10) {
                            $date = '0' . $d;
                        } else {
                            $date = $d;
                        }
                        if ($day1 == $date) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                    <option value="<?= $date ?>" <?= $selected ?>><?= $date ?></option>
                    <?php
                    } ?>
                </select> 일
                ~
                <select name="year2">
                    <?php for ($y = 2020; $y < date('Y', time()) + 1; $y++) {
                        if ($year2 == $y) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        } ?>
                    <option value="<?= $y ?>" <?= $selected ?>><?= $y ?></option>
                    <?php
                    } ?>
                </select> 년

                <select name="month2">
                    <?php for ($m = 1; $m < 13; $m++) {

                        if ($m < 10) {
                            $date = '0' . $m;
                        } else {
                            $date = $m;
                        }
                        if ($month2 == $date) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                    <option value="<?= $date ?>" <?= $selected ?>><?= $date ?></option>
                    <?php
                    } ?>
                </select> 월

                <select name="day2">
                    <?php for ($d = 1; $d < 32; $d++) {

                        if ($d < 10) {
                            $date = '0' . $d;
                        } else {
                            $date = $d;
                        }
                        if ($day2 == $date) {
                            $selected = 'selected';
                        } else {
                            $selected = '';
                        }
                        ?>
                    <option value="<?= $date ?>" <?= $selected ?>><?= $date ?></option>
                    <?php
                    } ?>
                </select> 일
                &nbsp;
                <input type="text" name="reqnum" maxlength="15" size="15" placeholder="차량번호 검색" value=<?= $reqnum ?>>
                &nbsp;
                <input type="hidden" name="mode" value="result">
                <div class="cs_search" id="id_search">검색</div>
            </form>
        </div>
    </div>

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
        <tr>
            <th width="3%"><input type="checkbox" id="id_allCheck" name="allcheck"></th>
            <th width="7%">no</th>
            <th>게이트번호</th>
            <th>구분</th>
            <th>차량번호</th>
            <th>시간</th>
        </tr>
        <?php
        $count = ($page - 1) * $list;
        $listCnt = $countRec - $count;

        if ($checkHnsLpr === 1) {
            $sql = $sql . ' order by A.EventDateTime desc limit ' . $count . ',' . $list;
        } else {
            $sql = $sql . ' order by GateDate desc limit ' . $count . ',' . $list;
        }

        $res = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($res)) {
            // $equipQuery = "SELECT * FROM wb_equip WHERE CD_DIST_OBSV ='{$row["GateSerial"]}'";
            // $equipRes = mysqli_query($conn, $equipQuery);
            // $equipRow = mysqli_fetch_assoc($equipRes);

            if ($checkHnsLpr === 1) {
                $rowCheckboxValue = $row['CarNumber'];
                $rowNm = $row['NM_DIST_OBSV'];
                $rowParkName = strstr($rowNm, '_', true);
            } else {
                $rowCheckboxValue = $row['idx'];
                // $rowParkName = $row['ParkGroupName'] . '(' . $row['GateSerial'] . ')';
                $rowParkGroupName = $row['ParkGroupName'];
                $rowEquipName = $row['NM_DIST_OBSV'];
                $rowGateSerial = $row['GateSerial'];
                $rowParkName = "{$rowParkGroupName}({$rowGateSerial}) | {$rowEquipName}";
                // $rowParkName = "{$rowParkGroupName}({$rowGateSerial})";
            } ?>
        <tr>
            <td style="text-align: center;"><input type="checkbox" class="cs_gateChk" value="<?= $rowCheckboxValue ?>">
            </td>
            <td><?= $listCnt-- ?></td>
            <td><?= $rowParkName ?></td>
            <td>
                <?php if ($checkHnsLpr === 1) {
                    if ($row['DeviceCode'] == '01') {
                        echo '입차';
                    } elseif ($row['DeviceCode'] == '02') {
                        echo '출차';
                    } elseif ($row['DeviceCode'] == '01') {
                        echo '현재 주차';
                    }
                } else {
                    if ($row['type'] == '0') {
                        echo '입차';
                    } elseif ($row['type'] == '1') {
                        echo '출차';
                    } elseif ($row['type'] == '2') {
                        echo '현재 주차';
                    }
                } ?>
            </td>
            <?php if ($checkHnsLpr === 1) { ?>
            <td class="cs_imgLink"
                data-url="frame/imgView.php?carnum=<?= $row['CarNumber'] ?>&cd_dist_obsv=<?= $row['CD_DIST_OBSV'] ?>&eventdatetime=<?= $row['EventDateTime'] ?>&checkhnslpr=<?= $checkHnsLpr ?>"
                style="cursor:pointer;">
                <?= $row['CarNumber'] ?>
            </td>
            <td>
                <?php
                $datetime = DateTime::createFromFormat('YmdHis', $row['EventDateTime']);
                echo $datetime->format('Y-m-d H:i:s');
                ?>
            </td>
            <?php } else { ?>
            <td class="cs_imgLink"
                data-url="frame/imgView.php?carnum=<?= $row['CarNum'] ?>&caridx=<?= $row['idx'] ?>&checkhnslpr=<?= $checkHnsLpr ?>"
                style="cursor:pointer;">
                <?= $row['CarNum'] ?>
            </td>
            <td><?= $row['GateDate'] ?></td>
            <?php } ?>
        </tr>
        <?php
        }
        ?>
    </table>

    <!-- Pageing block begin -->
    <div class="cs_page">
        <?php
        if ($page != 1) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='1'>이전</div>";
        }
        for ($p = $s_page; $p <= $e_page; $p++) {
            $act = '';
            if ($p == $page) {
                $act = 'active';
            }
            echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='1'>" . $p . '</div>';
        }
        if ($page != $pageNum) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='1'>다음</div>";
        }
        ?>
    </div>
    <!-- Pageing block end-->

    <div class="cs_btnBox" style="justify-content: flex-end;">
        <div class="cs_btn" id="id_msgBtn" data="<?= $type ?>">안내문자 발송</div>
        <div class="cs_btn" id="id_delBtn" data="<?= $type ?>">입출차내역 삭제</div>
    </div>
</div>