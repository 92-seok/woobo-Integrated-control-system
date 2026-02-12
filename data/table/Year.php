<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['year'])) {
    $year = $_GET['year'];
} else {
    $year = date('Y', time());
}
if (isset($_GET['dType'])) {
    $dType = $_GET['dType'];
} else {
    $dType = $_SESSION['firstData'];
}

$selectDate = $year;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equipdao = new WB_EQUIP_DAO();
$equipvo = $equipdao->SELECT("GB_OBSV IN ('{$area_code}') AND USE_YN='1'");

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
?>
<div class="cs_frame">
    <div class="cs_selectBox">
        <div style="font-size: 16px;margin-bottom: 5px;"><?= $year ?>년</div>
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
                <input type="hidden" name="addr" id="id_addr" value="Year.php">
                <input type="hidden" name="dType" value="<?= $dType ?>">
                <label class="calendar" for="id_sDate" style="background-color: <?= $border_color ?>;">달 력</label>
                <input type="text" name="sDate" class="sDate_css" id="id_sDate" value="<?= $selectDate ?>" readonly>

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
            if ($dType == 'dplace') {
                // echo "<th colspan='2' width='150px'>지역명</th>";
                echo "<th width='100px'>지역명</th>";
                echo "<th width='50px'>채널</th>";
            } else {
                echo "<th width='100px'>지역명</th>";
            }

            for ($i = 1; $i <= 12; $i++) {
                echo '<th>' . $i . '</th>';
            }

            echo '</tr>';
            $r = 0;
            $graphData = [];
            $maxVal = 0;
            $sumVal = 0;
            foreach ($equipvo as $idx => $evo) {
                // 변위 데이터는 SubOBCount에 따라 쿼리를 따로 줘야해서 별도로 빼줌
                if ($dType != 'dplace') {
                    if ($dType == 'flood') {
                        $vo = $dao->SELECT_WATER_YEAR("left(RegDate,4) like '{$selectDate}%' and CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
                    } else {
                        $vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}%' and CD_DIST_OBSV = {$evo->CD_DIST_OBSV}");
                    }
                    $data = array_fill(0, 13, '');
                    if ($vo) {
                        foreach ($vo as $v) {
                            $data[$v->idx] = (float) $v->Data;
                        }
                    }

                    for ($i = 1; $i <= 12; $i++) {
                        switch ($dType) {
                            case 'rain':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
                                break;

                            case 'snow':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 10, 1) . '</font>' : '-';
                                break;

                            case 'water':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 1000, 2) . '</font>' : '-';
                                break;

                            case 'flood':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 10, 1) . '</font>' : '-';
                                break;

                            case 'tilt':
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i] / 1, 2) . '</font>' : '-';
                                break;

                            default:
                                $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '</font>' : '-';
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

                    echo '<tr>';
                    echo "<td name='titleTd{$idx}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;' rowspan='{$rowspan}'>{$evo->NM_DIST_OBSV}</td>";
                    for ($i = 1; $i <= 12; $i++) {
                        echo "<td name='dataTd'>{$strArr[$i]}</td>";
                    }

                    echo '</tr>';
                    if ($dType == 'flood') {
                        echo '<tr>';
                        for ($j = 0; $j < 12; $j++) {
                            //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                            echo "<td name='floodData'>";
                            if (!empty($floodMark[$idx][$j])) {
                                echo $floodMark[$idx][$j];
                            } else {
                                echo '-';
                            }

                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                    echo "<input type='hidden' name='unitData' value='" . json_encode($data) . "' />";
                    $graphData[$r] = $data;
                    $graphData[$r++][0] = $evo->NM_DIST_OBSV;
                } else {
                    echo '<tr>';
                    echo "<td rowspan='{$evo->SubOBCount}' style='font-weight:bold; background-color:#f2f2f2; border-right:1px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";

                    for ($e = 1; $e <= $evo->SubOBCount; $e++) {
                        $vo = $dao->SELECT_YEAR("left(RegDate,4) like '{$selectDate}' and CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}'", $e);
                        $data = array_fill(0, 13, '');
                        if ($vo) {
                            foreach ($vo as $v) {
                                $data[$v->idx] = (float) $v->Data;
                            }
                        }

                        for ($i = 1; $i <= 12; $i++) {
                            $strArr[$i] = $data[$i] !== '' ? "<font color='#4900FF'>" . number_format($data[$i], 1) . '<font>' : '-';
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
</div> <?php
//frame
?>

<style>
.ui-datepicker-calendar {
    display: none;
}

.ui-datepicker-month {
    display: none;
}
</style>

<script>
// document.addEventListener('DOMContentLoaded', () => {
$(document).ready(function(e) {
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
    if (count($graphData) > 12) {
        $color = array_fill(0, count($graphData), getRandomColor());
    }
    $label = "{$graphData[$r][0]}";
    $data = '';
    $bColor = "'{$color[$r]}'";

    for ($i = 0; $i < 59; $i++) {
        $bColor = "{$bColor},'{$color[$r]}'";
    }

    // Chart.min.js 차트 표현식 중 데이터 입력 방식 [0,0,0,0,...,n]
    for ($i = 1; $i <= 12; $i++) {
        if ($graphData[$r][$i] !== '') {
            // 데이터가 있다면 데이터 입력
            if ($i == 1) {
                if ($dType == 'snow' || $dType == 'flood') {
                    $data = number_format($graphData[$r][$i] / 10, 1) . '';
                } elseif ($dType == 'water') {
                    $data = number_format($graphData[$r][$i] / 1000, 1) . '';
                } else {
                    $data = $graphData[$r][$i] . '';
                }
            } else {
                if ($dType == 'snow' || $dType == 'flood') {
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
            } elseif ($i == 12) {
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
                        if ($graphData[$r][$j] != '') {
                            $pre_idx = $j;

                            if ($dType == 'snow' || $dType == 'flood') {
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
                    for ($j = $i; $j <= 12; $j++) {
                        if ($graphData[$r][$j] != '') {
                            $post_idx = $j;

                            if ($dType == 'snow' || $dType == 'flood') {
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
            labels: Array.from({
                length: 12
            }, (_, i) => i + 1),
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
                        // labelString: '<?= date('Y', strtotime($selectDate)) ?> 데이터 통계'
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
    switch (dataType) {
        case 'rain':
            chartList.options.scales.yAxes[0].ticks['max'] = (<?= $sumVal ?>) + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'water':
            chartList.options.scales.yAxes[0].ticks['max'] = (<?= $maxVal ?> / 1000) + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'flood':
            chartList.options.scales.yAxes[0].ticks['max'] = (<?= $maxVal ?> / 10) + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'dplace':
            break;
        case 'soil':
            chartList.options.scales.yAxes[0].ticks['max'] = <?= $maxVal ?>;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
    }

    var myChart = new Chart(ctx, chartList);

    $("#id_sDate").datepicker({
        changeYear: true,
        dateFormat: "yy",
        onClose: function() {
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            if (year) {
                $(this).val(year);
            }
        }
    });

    $("select[name='units']").change(function() {
        let i = 0;
        let td = "";
        let unitData, val = "";

        $('td[name^="dataTd"]').remove();
        for (i; i < $("input[name='unitData']").length; i++) {
            unitData = JSON.parse($("input[name='unitData']").eq(i).val());
            $("td[name='titleTd" + i + "']").after("<td name='dataTd0' style='display:none'></td>");

            for (let j = 1; j <= 12; j++) {
                if (typeof unitData[j] === 'number') {
                    if ($("select[name='units']").val() == '1') {
                        val = (Math.round(unitData[j] / 100) / 10).toFixed(1);
                    } else if ($("select[name='units']").val() == '2') {
                        val = (Math.round(unitData[j]) / 10).toFixed(1);
                    } else if ($("select[name='units']").val() == '3') {
                        val = (Math.round(unitData[j])).toFixed(0);
                    }

                    td = "<td name='dataTd" + j + "' style='color:#4900FF'>" + val + "</td>";
                } else {
                    td = "<td name='dataTd" + j + "'>-</td>";
                }
                $("td[name='titleTd" + i + "']").siblings("td[name='dataTd" + (j - 1) + "']").after(td);
            }
        }
    })
});

// });
</script>