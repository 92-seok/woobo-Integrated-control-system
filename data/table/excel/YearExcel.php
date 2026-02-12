<?php
if (isset($_GET['sDate'])) {
    $year = substr($_GET['sDate'], 0, 4);
} else {
    $year = date('Y', time());
}
if (isset($_GET['dType'])) {
    $dType = $_GET['dType'];
} else {
    $dType = 'rain';
}

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=' . $dType . 'Year_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

$selectDate = $year;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equipdao = new WB_EQUIP_DAO();
$equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN='1'");
if ($dType == 'flood') {
    $dao = new WB_DATA1HOUR_DAO('water');
    $flooddao = new WB_DATA1HOUR_DAO('flood');

    foreach ($equipvo as $key => $val) {
        $floodvo[$key] = $flooddao->SELECT_FLOOD_YEAR("RegDate like '{$selectDate}%' and CD_DIST_OBSV IN ({$val->CD_DIST_OBSV})");
    }

    $floodArray = [];

    foreach ($floodvo as $idx => $fv) {
        $floodMark[$idx] = array_fill(0, 12, '-');
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
    $equipvo = $equipdao->SELECT("GB_OBSV IN ('{$area_code}') AND USE_YN='1'");
    $dao = new WB_DATA1HOUR_DAO($dType);
    $rowspan = 1;
}
$vo = new WB_DATA1HOUR_VO();

echo "<table border='1'>";
echo '<tr>';
if ($dType == 'dplace') {
    echo "<th colspan='2' width='200'>지역명</th>";
} else {
    echo "<th width='150'>지역명</th>";
}

for ($i = 1; $i <= 12; $i++) {
    echo "<th width='50'>" . $i . '</th>';
}

echo '</tr>';
$r = 0;
$graphData = [];
foreach ($equipvo as $evo) {
    // 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
    if ($dType != 'dplace') {
        $vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}' and CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
        $data = array_fill(0, 13, '');
        if ($vo) {
            foreach ($vo as $v) {
                $data[$v->idx] = (float) $v->Data;
            };
        }

        for ($i = 1; $i <= 12; $i++) {
            switch ($dType) {
                case 'rain':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
                    break;

                case 'snow':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i] / 10, 1) . '</font>' : '-';
                    break;

                case 'water':
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i] / 1000, 1) . '</font>' : '-';
                    break;

                default:
                    $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '</font>' : '-';
            }
        }

        echo '<tr>';
        if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;' rowspan='{$rowspan}'>{$evo->NM_DIST_OBSV}</td>";
        } else {
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;' rowspan='{$rowspan}'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
        }
        for ($i = 1; $i <= 12; $i++) {
            echo "<td>{$strArr[$i]}</td>";
        }
        echo '</tr>';

        if ($dType == 'flood') {
            echo '<tr>';
            for ($j = 0; $j < 12; $j++) {
                //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                echo "<td name='floodData' style='text-align: right'>";
                if (!empty($floodMark[$idx][$j])) {
                    echo $floodMark[$idx][$j];
                } else {
                    echo '-';
                }

                echo '</td>';
            }
            echo '</tr>';
        }

        $graphData[$r] = $data;
        $graphData[$r++][0] = $evo->NM_DIST_OBSV;
    } else {
        echo '<tr>';
        if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
            echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
        } else {
            echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
        }
        for ($e = 1; $e <= $evo->SubOBCount; $e++) {
            $vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
            $data = array_fill(0, 13, '');
            if ($vo) {
                foreach ($vo as $v) {
                    $data[$v->idx] = (float) $v->Data;
                };
            }

            for ($i = 1; $i <= 12; $i++) {
                $strArr[$i] = $data[$i] != '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
            }

            if ($e != 1) {
                echo '<tr>';
            }
            echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
            for ($i = 1; $i <= 12; $i++) {
                echo "<td>{$strArr[$i]}</td>";
            }
            echo '</tr>';

            $graphData[$r] = $data;
            $graphData[$r++][0] = "{$evo->NM_DIST_OBSV}_{$e}";
        }
    }
}
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
