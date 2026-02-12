<?php
if (isset($_GET['sDate'])) {
    $year = substr($_GET['sDate'], 0, 4);
    $month = substr($_GET['sDate'], 5, 2);
} else {
    $year = date('Y', time());
    $month = date('m', time());
}
if (isset($_GET['sDate'])) {
    $dType = $_GET['dType'];
} else {
    $dType = 'rain';
}

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=' . $dType . 'Month_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

$selectDate = $year . $month;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equipdao = new WB_EQUIP_DAO();
$equipvo = new WB_EQUIP_VO();

$floodvo = [];
if (isset($_GET['area'])) {
    $area = $_GET['area'];
} else {
    $equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
}
if ($dType == 'flood') {
    $dao = new WB_DATA1HOUR_DAO('water');
    $flood_dao = new WB_DATA1HOUR_DAO('flood');
    $floodArray = [];

    foreach ($equipvo as $key => $val) {
        $floodvo[$key] = $flood_dao->SELECT_FLOOD_MONTH("left(RegDate,6) like '{$selectDate}' and CD_DIST_OBSV IN ({$val->CD_DIST_OBSV})");
    }

    foreach ($floodvo as $idx => $fv) {
        $floodMark[$idx] = array_fill(0, 31, '-');
        foreach (array_reverse($fv) as $k => $v) {
            if (isset($v->data)) {
                $mark1 = 'X';
                $mark2 = 'X';
                $mark3 = 'X';
                $floodArray[$idx][$k] = $v->data;
                if ($floodArray[$idx][$k] === null || $floodArray[$idx][$k] === '' || $floodArray[$idx][$k] === '222') {
                    $floodMark[$idx] = '-';
                } else {
                    if ($floodArray[$idx][$k][0] === '1') {
                        $mark1 = 'O';
                    }
                    if ($floodArray[$idx][$k][1] === '1') {
                        $mark2 = 'O';
                    }
                    if ($floodArray[$idx][$k][2] === '1') {
                        $mark3 = 'O';
                    }
                }
                $floodMark[$idx][$k] = $mark1 . $mark2 . $mark3;
            }
        }
    }
    $rowspan = 2;
} else {
    $dao = new WB_DATA1HOUR_DAO($dType);
    $rowspan = 1;
}
$vo = new WB_DATA1HOUR_VO();

echo "<table border='1'>";
echo '<tr>';
if ($dType == 'dplace' || $dType == 'water') {
    echo "<th colspan='2' width='200'>지역명</th>";
} else {
    echo "<th width='150'>지역명</th>";
}

for ($i = 1; $i <= 31; $i++) {
    echo "<th width=50>{$i}</th>";
}

if ($dType == 'rain') {
    echo "<th width='50'>최고</th>";
    echo "<th width='60'>계</th>";
} elseif ($dType == 'snow') {
    echo "<th width='50'>최고</th>";
}
echo '</tr>';

$equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
foreach ($equipvo as $evo) {
    // 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
    if ($dType != 'dplace') {
        /**
         * @param $data     : 데이터 담는 배열 (수위는 최대값)
         * @param $dataMin  : 수위용 최소 데이터 담는 배열
         * @param $max      : 최대치 계산
         * @param $min      : 최소치 계산
         * @param $sum      : 합계 계산
         */
        if ($dType == 'flood') {
            $vo = $dao->SELECT_MONTH("IFNULL(DATE_FORMAT(RegDate, '%Y%m'), LEFT(RegDate, 6)) = '{$selectDate}' AND CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
            $flood_vo = $flood_dao->SELECT_MONTH("IFNULL(DATE_FORMAT(RegDate, '%Y%m'), LEFT(RegDate, 6)) = '{$selectDate}' AND CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
        } else {
            $vo = $dao->SELECT_MONTH("IFNULL(DATE_FORMAT(RegDate, '%Y%m'), LEFT(RegDate, 6)) = '{$selectDate}' AND CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
        }
        $data = array_fill(0, 32, '');
        $dataMin = array_fill(0, 32, '');
        $max = 0;
        $min = 0;
        $sum = 0;
        foreach ($vo as $v) {
            if ($dType == 'rain') {
                $data[$v->idx] = (float) $v->DaySum;
                $sum += (float) $v->DaySum;
                if ($max < $v->DaySum) {
                    $max = $v->DaySum;
                }
            } elseif ($dType == 'water') {
                $data[$v->idx] = (float) $v->DayMax;
                $dataMin[$v->idx] = (float) $v->DayMin;
            } else {
                $data[$v->idx] = (float) $v->DayMax;

                if ($sum == 0) {
                    $min = $v->DayMax;
                    $max = $v->DayMax;
                } else {
                    if ($max < $v->DayMax) {
                        $max = $v->DayMax;
                    }
                    if ($min > $v->DayMin) {
                        $min = $v->DayMin;
                    }
                }
                $sum += $v->DayMax;
            }
        }

        /**
         * DataBase는 mm로 값이 들어오는 기준
         * 강우 mm
         * 적설 Cm
         * 수위 M
         * Default mm
         * 소수점 1번째 자리까지!
         */
        for ($i = 1; $i <= 31; $i++) {
            switch ($dType) {
                case 'rain':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
                    $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max, 1) . '<font>' : '-';
                    $strSum = $sum != 0 ? "<font color='#4900FF'>" . number_format($sum, 1) . '<font>' : '-';
                    break;

                case 'snow':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i] / 10, 1) . '</font>' : '-';
                    $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max / 10, 1) . '<font>' : '-';
                    break;

                case 'water':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i] / 1000, 1) . '</font>' : '-';
                    $strMin[$i] = $dataMin[$i] != '' ? "<font color='#4900FF'>" . number_format($dataMin[$i] / 1000, 1) . '</font>' : '-';
                    break;

                default:
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '</font>' : '-';
                    $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max, 1) . '<font>' : '-';
            }
        }

        // 수위는 최대/최소로 표출하기에 따로 표출
        if ($dType == 'water') {
            echo '<tr>';
            if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
                echo "<td rowspan='2' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
            } else {
                echo "<td rowspan='2' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
            }
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최대</td>";
            for ($i = 1; $i <= 31; $i++) {
                echo "<td>{$strArr[$i]}</td>";
            }
            echo '</tr>';

            echo '<tr>';
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최소</td>";
            for ($i = 1; $i <= 31; $i++) {
                echo "<td>{$strMin[$i]}</td>";
            }
            echo '</tr>';
        } else {
            echo '<tr>';
            if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
                echo "<td rowspan='{$rowspan}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
            } else {
                echo "<td rowspan='{$rowspan}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
            }
            for ($i = 1; $i <= 31; $i++) {
                echo "<td>{$strArr[$i]}</td>";
            }

            if ($dType != 'flood') {
                echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
            }
            if ($dType == 'rain') {
                echo "<td style='color:#a30003; font-weight:bold'>{$strSum}</td>";
            }

            echo '</tr>';
            if ($dType == 'flood') {
                echo '<tr>';
                for ($j = 0; $j < 31; $j++) {
                    //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                    echo "<td name='floodData' style='text-align: right'>";
                    echo $floodMark[$idx][$j];
                    echo '</td>';
                }
                echo '</tr>';
            }
        }
    } else {
        echo '<tr>';
        if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
            echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
        } else {
            echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
        }

        for ($e = 1; $e <= $evo->SubOBCount; $e++) {
            $vo = $dao->SELECT_MONTH("ifnull(date_format(RegDate,'%Y%m'),left(RegDate,6)) = '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
            $data = array_fill(0, 32, '');
            foreach ($vo as $v) {
                $data[$v->idx] = (float) $v->DayMax;
            }

            for ($i = 1; $i <= 31; $i++) {
                $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
            }

            if ($e != 1) {
                echo '<tr>';
            }
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
            for ($i = 1; $i <= 31; $i++) {
                echo "<td>{$strArr[$i]}</td>";
            }
            echo '</tr>';
        }
    }
}
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
