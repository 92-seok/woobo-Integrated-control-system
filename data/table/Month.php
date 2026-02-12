<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date('Y', time());
}
if (isset($_GET['month'])) {
    $month = $_GET['month'];
} else {
    $month = date('m', time());
}
if (isset($_GET['dType'])) {
    $dType = $_GET['dType'];
} else {
    $dType = $_SESSION['firstData'];
}

$selectDate = $year . $month;
$showDate = $year . '-' . $month;

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
    $flooddao = new WB_DATA1HOUR_DAO('flood');
    $floodArray = [];

    foreach ($equipvo as $key => $val) {
        try {
            $result = $flooddao->SELECT_FLOOD_MONTH("left(RegDate,6) like '{$selectDate}' and CD_DIST_OBSV IN ({$val->CD_DIST_OBSV})");
            if ($result !== false) {
                $floodvo[$key] = $result;
            }
        } catch (Exception $e) {
            echo '에러: ' . $e->getMessage();
        }
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
?>
<div class="cs_frame">
    <div class="cs_selectBox">
        <div style="font-size: 16px;margin-bottom: 5px;"><?= $year ?>년<?= $month ?>월</div>
        <?php if ($dType == 'water') {
            echo "<div class='cs_unit'>단위 : ";
            echo "<select name='units'>";
            echo "<option value='1'>m</option>";
            echo "<option value='2'>cm</option>";
            echo "<option value='3'>mm</option>";
            echo '</select>';
            echo '</div>';
        } elseif ($dType == 'soil') {
            echo "<div class='cs_unit'>(단위 : %)</div>";
        } elseif ($dType == 'tilt') {
            echo "<div class='cs_unit'>(단위 : °)</div>";
        } elseif ($dType == 'snow' || $dType == 'flood') {
            echo "<div class='cs_unit'>(단위 : cm)</div>";
        } else {
            echo "<div class='cs_unit'>(단위 : mm)</div>";
        } ?>
        <div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                <input type="hidden" name="addr" id="id_addr" value="Month.php">
                <input type="hidden" name="dType" value="<?= $dType ?>">
                <label class="calendar" for="id_sDate" style="background-color: <?= $border_color ?>;">달 력</label>
                <input type="text" name="sDate" class="sDate_css" id="id_sDate" value="<?= $showDate ?>" readonly>
                <div class="cs_search" id="id_search">검색</div>
            </form>
            <div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div> <?php
//selectBox
?>
    <div class="gClose" id="id_gBtn"></div>
    <canvas id="id_myChart" width="400" height="150" style="padding-top: 50px;"></canvas>

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows"
        style="border:1px solid <?= $border_color ?>;">
        <tr style="position:sticky;top:0px; background-color: <?= $border_color ?>;">
            <?php
            if ($dType == 'dplace' || $dType == 'water') {
                // echo "<th colspan='2' width='150px'>지역명</th>";
                echo "<th width='100px'>지역명</th>";
                echo "<th width='50px'>채널</th>";
            } else {
                echo "<th width='100px'>지역명</th>";
            }

            for ($i = 1; $i <= 31; $i++) {
                echo "<th>{$i}</th>";
            }

            if ($dType == 'rain') {
                echo "<th width='50'>최고</th>";
                echo "<th width='60'>계</th>";
            } elseif ($dType == 'snow') {
                echo "<th width='50'>최고</th>";
            }
            echo '</tr>';

            $r = 0;
            $graphData = [];
            $maxVal = 0;
            $sumVal = 0;
            $equipvo = $equipdao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
            foreach ($equipvo as $idx => $evo) {
                // 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
                if ($dType != 'dplace') {
                    /**
                     * @param $data     : 데이터 담는 배열 (수위는 최대값)
                     * @param $dataMin  : 수위용 최소 데이터 담는 배열
                     * @param $max      : 최대치 계산
                     * @param $min      : 최소치 계산
                     * @param $sum      : 합계 계산
                     */
                    $vo = $dao->SELECT_MONTH("IFNULL(DATE_FORMAT(RegDate, '%Y%m'), LEFT(RegDate, 6)) = '{$selectDate}' AND CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
                    $data = array_fill(0, 32, '');
                    $dataMin = array_fill(0, 32, '');
                    $max = 0;
                    $min = 0;
                    $sum = 0;

                    echo "<input type='hidden' id='test' name='unitData' value='" . json_encode($vo) . "' />";

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

                    $numeric = array_map(function ($item) {
                        $num = is_numeric($item) ? (float) $item : 0;
                        return $num;
                    }, $data);

                    if ($maxVal <= max($numeric)) {
                        $numericMax = max($numeric);
                        $maxVal = $numericMax == 0 ? 1 : $numericMax;
                    }

                    if ($sumVal <= array_sum($numeric)) {
                        $nummericSum = array_sum($numeric);
                        $sumVal = $nummericSum == 0 ? 1 : $nummericSum;
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
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
                                $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max, 1) . '<font>' : '-';
                                $strSum = $sum != 0 ? "<font color='#4900FF'>" . number_format($sum, 1) . '<font>' : '-';
                                break;

                            case 'snow':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 10, 1) . '</font>' : '-';
                                $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max / 10, 1) . '<font>' : '-';
                                break;

                            case 'water':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 1000, 2) . '</font>' : '-';
                                $strMin[$i] = $dataMin[$i] !== '' ? "<font color='#4900FF'>" . number_format($dataMin[$i] / 1000, 2) . '</font>' : '-';
                                break;

                            case 'tilt':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 2) . '</font>' : '-';
                                $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max, 1) . '<font>' : '-';
                                break;

                            default:
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '</font>' : '-';
                                $strMax = $max != 0 ? "<font color='#4900FF'>" . number_format($max, 1) . '<font>' : '-';
                        }
                    }

                    // 수위는 최대/최소로 표출하기에 따로 표출
                    if ($dType == 'water') {
                        echo "<tr name='maxArea{$idx}'>";
                        echo "<td rowspan='2' style='width:150px; font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;' rowspan={$rowspan}>{$evo->NM_DIST_OBSV}</td>";
                        echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최대</td>";
                        for ($i = 1; $i <= 31; $i++) {
                            echo "<td name='maxTd{$i}'>{$strArr[$i]}</td>";
                        }
                        echo '</tr>';

                        echo "<tr name='minArea{$idx}'>";
                        echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>최소</td>";
                        for ($i = 1; $i <= 31; $i++) {
                            echo "<td name='minTd{$i}'>{$strMin[$i]}</td>";
                        }
                        echo '</tr>';
                    } else {
                        echo '<tr>';

                        echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;' rowspan={$rowspan}>{$evo->NM_DIST_OBSV}</td>";
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
                    }

                    if ($dType == 'flood') {
                        echo '<tr>';
                        for ($j = 0; $j < 31; $j++) {
                            //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                            echo "<td name='floodData'>";
                            echo $floodMark[$idx][$j];
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                    $graphData[$r] = $data;
                    $graphData[$r++][0] = $evo->NM_DIST_OBSV;
                } else {
                    echo '<tr>';
                    echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";

                    for ($e = 1; $e <= $evo->SubOBCount; $e++) {
                        $vo = $dao->SELECT_MONTH("ifnull(date_format(RegDate,'%Y%m'),left(RegDate,6)) = '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
                        $data = array_fill(0, 32, '');
                        foreach ($vo as $v) {
                            $data[$v->idx] = (float) $v->DayMax;
                        }

                        for ($i = 1; $i <= 31; $i++) {
                            $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
                        }

                        if ($e != 1) {
                            echo '<tr>';
                        }
                        echo "<td style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$e}</td>";
                        for ($i = 1; $i <= 31; $i++) {
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
</div> <?php
//frame
?>


<style>
.ui-datepicker-calendar {
    display: none;
}
</style>
<script>
$(document).ready(function(e) {
    function formatMonth(month) {
        const parseMonth = parseInt(month, 10) + 1;
        return parseMonth.toString().padStart(2, '0');
    }
    $("#id_sDate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm",
        monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        showMonthAfterYear: true,
        onClose: function() {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            if (month && year) {
                $(this).val(year + '-' + (formatMonth(month)));
            }
        }
    });

    $("select[name='units']").change(function() {
        let unitData = "";
        let jsonData = [];
        let dayMax, dayMin = "";

        for (let j = 0; j <= "<?= $idx ?>"; j++) {
            jsonData = JSON.parse($("input[name='unitData']").eq(j).val());
            unitData = jsonData.reverse();
            for (let i = 1; i <= 31; i++) {

                if (unitData[(i - 1)] === undefined || unitData[(i - 1)]['DayMax'] === null) {
                    dayMax = '-';
                } else {
                    dayMax = unitData[(i - 1)]['DayMax'];
                }
                if (unitData[(i - 1)] === undefined || unitData[(i - 1)]['DayMin'] === null) {
                    dayMin = '-';
                } else {
                    dayMin = unitData[(i - 1)]['DayMin'];
                }

                $("tr[name='maxArea" + j + "'] td[name='maxTd" + (i) + "']").empty();
                $("tr[name='maxArea" + j + "'] td[name='minTd" + (i) + "']").empty();

                if (dayMax != '-') {
                    if ($("select[name='units']").val() == '1') {
                        $("tr[name='maxArea" + j + "'] td[name='maxTd" + (i) + "']").text((Math.round(
                            dayMax / 100) / 10).toFixed(1)).css('color', '#4900FF');
                    } else if ($("select[name='units']").val() == '2') {
                        $("tr[name='maxArea" + j + "'] td[name='maxTd" + (i) + "']").text((Math.round(
                            dayMax / 10)).toFixed(1)).css('color', '#4900FF');
                    } else if ($("select[name='units']").val() == '3') {
                        $("tr[name='maxArea" + j + "'] td[name='maxTd" + (i) + "']").text((Math.round(
                            dayMax)).toFixed(0)).css('color', '#4900FF');
                    }
                } else {
                    $("tr[name='maxArea" + j + "'] td[name='maxTd" + (i) + "']").text('-');
                }

                if (dayMin != '-') {
                    if ($("select[name='units']").val() == '1') {
                        $("tr[name='minArea" + j + "'] td[name='minTd" + (i) + "']").text((Math.round(
                            dayMin / 100) / 10).toFixed(1)).css('color', '#4900FF');
                    } else if ($("select[name='units']").val() == '2') {
                        $("tr[name='minArea" + j + "'] td[name='minTd" + (i) + "']").text((Math.round(
                            dayMin / 10)).toFixed(1)).css('color', '#4900FF');
                    } else if ($("select[name='units']").val() == '3') {
                        $("tr[name='minArea" + j + "'] td[name='minTd" + (i) + "']").text((Math.round(
                            dayMin)).toFixed(0)).css('color', '#4900FF');
                    }
                } else {
                    $("tr[name='minArea" + j + "'] td[name='minTd" + (i) + "']").text('-');
                }
            }
        }
    });



    let hour = [];
    for (let i = 1; i <= 31; i++) { // 0부터 9까지의 숫자 추가
        hour.push(i);
    }

    <?php
    $color = [
        'rgba(0, 128, 255, 1)',
        'rgba(255, 0, 102, 1)',
        'rgba(102, 255, 0, 1)',
        'rgba(255, 153, 0, 1)',
        'rgba(0, 255, 204, 1)',
        'rgba(153, 0, 255, 1)',
        'rgba(0, 102, 204, 1)',
        'rgba(204, 0, 102, 1)',
        'rgba(255, 255, 0, 1)',
        'rgba(0, 204, 153, 1)',
        'rgba(255, 51, 0, 1)',
        'rgba(102, 51, 153, 1)',
        'rgba(0, 153, 255, 1)',
        'rgba(255, 102, 0, 1)',
        'rgba(153, 204, 0, 1)',
        'rgba(204, 102, 0, 1)',
        'rgba(51, 204, 255, 1)',
        'rgba(255, 0, 204, 1)',
        'rgba(204, 0, 0, 1)',
        'rgba(51, 153, 102, 1)',
        'rgba(153, 0, 0, 1)',
        'rgba(0, 51, 102, 1)',
        'rgba(204, 51, 255, 1)',
        'rgba(102, 255, 255, 1)',
        'rgba(255, 204, 204, 1)',
        'rgba(204, 255, 204, 1)',
        'rgba(204, 204, 255, 1)',
        'rgba(255, 255, 204, 1)',
        'rgba(255, 204, 255, 1)',
        'rgba(204, 255, 255, 1)',
        'rgba(255, 255, 255, 1)',
        'rgba(192, 192, 192, 1)',
        'rgba(128, 0, 128, 1)',
        'rgba(0, 128, 128, 1)',
        'rgba(128, 128, 0, 1)',
        'rgba(0, 0, 128, 1)',
        'rgba(255, 128, 0, 1)',
        'rgba(0, 255, 128, 1)',
        'rgba(128, 0, 0, 1)',
        'rgba(0, 128, 0, 1)',
        'rgba(0, 0, 0, 1)',
        'rgba(60, 179, 113, 1)',
        'rgba(106, 90, 205, 1)',
        'rgba(70, 130, 180, 1)',
        'rgba(255, 105, 180, 1)',
        'rgba(219, 112, 147, 1)',
        'rgba(176, 224, 230, 1)',
        'rgba(244, 164, 96, 1)',
        'rgba(0, 76, 153, 1)',
        'rgba(255, 204, 0, 1)',
        'rgba(255, 77, 77, 1)',
        'rgba(0, 230, 64, 1)',
        'rgba(204, 153, 255, 1)',
        'rgba(0, 255, 128, 1)',
        'rgba(255, 128, 128, 1)',
        'rgba(128, 255, 128, 1)',
        'rgba(128, 128, 255, 1)',
        'rgba(255, 255, 128, 1)',
        'rgba(255, 179, 255, 1)',
        'rgba(128, 255, 255, 1)',
        'rgba(192, 128, 255, 1)',
        'rgba(0, 0, 160, 1)',
        'rgba(0, 100, 0, 1)',
        'rgba(100, 0, 0, 1)',
        'rgba(150, 75, 0, 1)',
        'rgba(255, 215, 0, 1)',
        'rgba(233, 150, 122, 1)',
        'rgba(127, 255, 212, 1)',
        'rgba(220, 20, 60, 1)',
        'rgba(65, 105, 225, 1)',
        'rgba(85, 107, 47, 1)',
        'rgba(210, 105, 30, 1)',
        'rgba(100, 149, 237, 1)',
        'rgba(0, 191, 255, 1)',
        'rgba(255, 160, 122, 1)',
        'rgba(144, 238, 144, 1)',
        'rgba(255, 99, 71, 1)',
        'rgba(154, 205, 50, 1)',
        'rgba(72, 209, 204, 1)',
        'rgba(186, 85, 211, 1)',
        'rgba(255, 20, 147, 1)',
        'rgba(135, 206, 235, 1)',
        'rgba(32, 178, 170, 1)',
        'rgba(240, 128, 128, 1)',
        'rgba(218, 165, 32, 1)',
        'rgba(255, 228, 181, 1)',
        'rgba(173, 216, 230, 1)',
        'rgba(255, 182, 193, 1)',
        'rgba(221, 160, 221, 1)',
        'rgba(250, 128, 114, 1)',
        'rgba(244, 164, 96, 1)',
        'rgba(210, 180, 140, 1)',
        'rgba(176, 196, 222, 1)',
        'rgba(152, 251, 152, 1)',
        'rgba(255, 228, 225, 1)',
        'rgba(189, 183, 107, 1)',
        'rgba(112, 128, 144, 1)',
        'rgba(72, 61, 139, 1)',
        'rgba(46, 139, 87, 1)',
        'rgba(0, 139, 139, 1)',
        'rgba(0, 0, 205, 1)',
        'rgba(255, 69, 0, 1)',
        'rgba(124, 252, 0, 1)',
        'rgba(173, 255, 47, 1)',
        'rgba(199, 21, 133, 1)',
        'rgba(255, 218, 185, 1)',
        'rgba(176, 224, 230, 1)',
        'rgba(255, 239, 213, 1)',
        'rgba(250, 250, 210, 1)',
        'rgba(0, 250, 154, 1)',
        'rgba(219, 112, 147, 1)',
        'rgba(139, 0, 139, 1)',
        'rgba(255, 228, 196, 1)',
        'rgba(255, 160, 122, 1)',
        'rgba(100, 149, 237, 1)',
        'rgba(244, 164, 96, 1)',
        'rgba(233, 150, 122, 1)',
        'rgba(60, 179, 113, 1)',
        'rgba(255, 222, 173, 1)',
        'rgba(255, 105, 180, 1)',
        'rgba(138, 43, 226, 1)',
        'rgba(102, 205, 170, 1)',
        'rgba(147, 112, 219, 1)',
        'rgba(255, 215, 0, 1)',
        'rgba(123, 104, 238, 1)',
        'rgba(72, 209, 204, 1)',
        'rgba(240, 230, 140, 1)',
        'rgba(173, 255, 47, 1)',
        'rgba(255, 250, 250, 1)',
        'rgba(105, 105, 105, 1)',
        'rgba(190, 190, 190, 1)',
        'rgba(119, 136, 153, 1)',
        'rgba(176, 196, 222, 1)',
        'rgba(95, 158, 160, 1)',
        'rgba(255, 248, 220, 1)',
        'rgba(255, 235, 205, 1)',
        'rgba(240, 248, 255, 1)',
        'rgba(255, 250, 205, 1)',
        'rgba(124, 252, 0, 1)',
        'rgba(127, 255, 0, 1)',
        'rgba(102, 205, 170, 1)',
        'rgba(255, 127, 80, 1)',
        'rgba(255, 69, 0, 1)',
        'rgba(139, 0, 0, 1)'
    ];

    function getRandomColor() {
        $r = rand(0, 255);
        $g = rand(0, 255);
        $b = rand(0, 255);

        return "rgba($r,$g,$b,1)";
    }

    for ($r = 0; $r < count($graphData); $r++) {
        if (count($graphData) > 24) {
            $color = array_fill(0, count($graphData), getRandomColor());
        }
        $label = "{$graphData[$r][0]}";
        $data = '';
        $bColor = "'{$color[$r]}'";

        for ($i = 0; $i < 59; $i++) {
            $bColor = "{$bColor},'{$color[$r]}'";
        }

        // Chart.min.js 차트 표현식 중 데이터 입력 방식 [0,0,0,0,...,n]
        for ($i = 1; $i <= 31; $i++) {
            if ($graphData[$r][$i] !== '') {
                // 데이터가 있다면 데이터 입력
                if ($i == 1) {
                    if ($dType == 'snow') {
                        $data = number_format($graphData[$r][$i] / 10, 1) . '';
                    } elseif ($dType == 'water') {
                        $data = number_format($graphData[$r][$i] / 1000, 1) . '';
                    } else {
                        $data = $graphData[$r][$i] . '';
                    }
                } else {
                    if ($dType == 'snow') {
                        $data = $data . ',' . number_format($graphData[$r][$i] / 10, 1);
                    } elseif ($dType == 'water') {
                        $data = $data . ',' . number_format($graphData[$r][$i] / 1000, 1);
                    } else {
                        $data = $data . ',' . $graphData[$r][$i];
                    }
                }
            } else {
                // 라인 형태의 그래프이기때문에 중간 값이 비면 자연스러운 연결을 위해 앞과 뒤의 값을 이용해 연결 (처음 값과 마지막 값은 상관 없으므로 예외처리)
                if ($i == 1) {
                    $data = '';
                } elseif ($i == 31) {
                    $data = "{$data},";
                } else {
                    // 강우 데이터는 막대 그래프 표시로 예외 처리
                    if ($dType == 'rain') {
                        $data = "{$data},0";
                    } else {
                        $pre_idx = 0;
                        $post_idx = 0;
                        $pre_data = 0;
                        $post_data = 0;

                        // 앞쪽에 유효한 값 찾기
                        for ($j = $i; $j >= 1; $j--) {
                            if ($graphData[$r][$j] !== '') {
                                $pre_idx = $j;

                                if ($dType == 'snow') {
                                    $pre_data = $graphData[$r][$j] / 10;
                                } elseif ($dType == 'water') {
                                    $pre_data = $graphData[$r][$j] / 1000;
                                } else {
                                    $pre_data = $graphData[$r][$j];
                                }

                                break;
                            }
                        }

                        // 뒷쪽에 유효한 값 찾기
                        for ($j = $i; $j <= 31; $j++) {
                            if ($graphData[$r][$j] !== '') {
                                $post_idx = $j;

                                if ($dType == 'snow') {
                                    $post_data = $graphData[$r][$j] / 10;
                                } elseif ($dType == 'water') {
                                    $post_data = $graphData[$r][$j] / 1000;
                                } else {
                                    $post_data = $graphData[$r][$j];
                                }

                                break;
                            }
                        }

                        // 앞이나 뒷쪽 값이 없다면 예외 처리
                        if ($pre_idx == 0 || $post_idx == 0) {
                            $data = "{$data},";
                        } else {
                            // 앞, 뒤 값이 있다면 몇번 건너뛰었는지 계산
                            $j = ($i - $pre_idx) / ($post_idx - $pre_idx);
                            // 건너뛴 갯수와 값에 비례하여 데이터 계산
                            $data = "{$data}," . ($pre_data + ($post_data - $pre_data) * $j);
                        }
                    }
                }
            }
        }

        echo "let {$dType}{$r} = { label: '{$label}', backgroundColor: [{$bColor}], borderColor: ['{$color[$r]}'], fill: false, data : [{$data}]};";
        if ($r == 0) {
            $dataset = "{$dType}{$r}";
        } else {
            $dataset = "{$dataset},{$dType}{$r}";
        }
    }
    ?>
    var ctx = document.getElementById('id_myChart').getContext('2d');
    let chartList = {
        type: <?php if ($dType == 'rain') {
            echo "'bar'";
        } else {
            echo "'line'";
        } ?>,
        data: {
            labels: hour,
            datasets: [
                <?= $dataset ?>
            ]
        },
        options: {
            reponsive: true,
            title: {
                display: false
            },
            tooltips: {
                enabled: true
            },
            hover: {
                mode: 'nearest',
                intersect: true
            },
            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        // labelString: '<?= date('Y-m', strtotime($selectDate)) ?> 데이터 통계'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: false
                    },
                    gridLines: {
                        drawBorder: false
                    },
                    ticks: {
                        display: true
                    }
                }]
            }
        }
    };

    let dataType = '<?= $dType ?>';
    const sumVal = <?= $sumVal ?>;
    const maxVal = <?= $maxVal ?>;
    console.log(`dType: ${dataType} | sumVal: ${sumVal} | maxVal: ${maxVal}`);
    switch (dataType) {
        case 'rain':
            chartList.options.scales.yAxes[0].ticks['max'] = sumVal + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'water':
            chartList.options.scales.yAxes[0].ticks['max'] = (maxVal / 1000) + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'dplace':
            break;
        case 'soil':
            chartList.options.scales.yAxes[0].ticks['max'] = 100;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        default:
            // console.log('switch default');
            chartList.options.scales.yAxes[0].ticks['max'] = maxVal + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
    }

    var myChart = new Chart(ctx, chartList);
});
</script>