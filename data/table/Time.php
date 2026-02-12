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
if (isset($_GET['day'])) {
    $day = $_GET['day'];
} else {
    $day = date('d', time());
}
if (isset($_GET['dType'])) {
    $dType = $_GET['dType'];
} else {
    $dType = $_SESSION['firstData'];
}
if (isset($_GET['equip'])) {
    $equip = $_GET['equip'];
} else {
    $equip = '';
}

$tableName = $dType;
$selectDate = $year . $month . $day;
$nextDate = $selectDate + 1;
$showDate = $year . '-' . $month . '-' . $day;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = new WB_EQUIP_VO();
$data_vo = new WB_DATA1MIN_VO();

if (isset($_GET['area'])) {
    $area = $_GET['area'];
} else {
    $equip_vo = $equip_dao->SELECT_SINGLE("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
    $area = $equip_vo->CD_DIST_OBSV;
}

//수위 그래프는 침수용 수위 그래프도 표출해야함.
if ($dType == 'water') {
    $gbobsv = "'02'";
} else {
    $gbobsv = "'{$area_code}'";
}
?>

<div class="cs_frame">
    <div class="cs_selectBox">
        <?php
        echo "<div style='font-size: 16px;margin-bottom: 5px;'>{$year}년{$month}월{$day}일</div>";

        if ($dType == 'water') {
            echo "<div class='cs_unit'>(단위 : mm)</div>";
        } elseif ($dType == 'water') {
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
        } elseif ($dType == 'flood') {
            echo "<div class='cs_unit'>(단위 : cm)</div>";
        } else {
            echo "<div class='cs_unit'>(단위 : mm)</div>";
        }
        ?>
        <div class="cs_date">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                <input type="hidden" name="addr" id="id_addr" value="Time.php">

                <?php
                // 해당 장비 전체 불러오기 (Select Box)
                $equip_vo = $equip_dao->SELECT("GB_OBSV  = '{$area_code}' and USE_YN = '1'");

                echo "<select name='area' id='id_select'>";
                foreach ($equip_vo as $v) {
                    if ($area == '') {
                        $area = $v->CD_DIST_OBSV;
                        echo "<script>console.log('selected CD_DIST_OBSV:','" . $area . "')</script>";
                    } elseif ($area == $v->CD_DIST_OBSV) {
                        $areaName = $v->NM_DIST_OBSV;
                        $sel = 'selected';
                        echo "<script>console.log('selected NM_DIST_OBSV:','" . $areaName . "')</script>";
                    } else {
                        $sel = '';
                    }

                    echo "<option value='{$v->CD_DIST_OBSV}' {$sel}>{$v->NM_DIST_OBSV}</option>";
                }
                echo '</select>&nbsp;&nbsp;';

                // 변위: Sub OBSV 불러와서 Select Box 만들기
                if ($dType == 'dplace') {
                    if ($equip == '') {
                        $equip = 1;
                    }
                    $equip_vo = $equip_dao->SELECT_SINGLE("CD_DIST_OBSV = '{$area}'");

                    echo "<select name='equip' id='id_select'>";
                    for ($i = 1; $i <= $equip_vo->SubOBCount; $i++) {
                        echo "<option value='{$i}'";
                        if ($equip == $i) {
                            echo 'selected';
                        }
                        echo ">{$i}</option>";
                    }
                    echo '</select> 채널 ';
                    echo "<input type='hidden' name='dType' value='dplace'>";
                } else {
                    echo "<input type='hidden' name='dType' value='{$dType}'>";
                }
                ?>
                <label class="calendar" for="id_sDate" style="background-color: <?= $border_color ?>;">달 력</label>
                <input type="text" name="sDate" class="sDate_css" id="id_sDate" value="<?= $showDate ?>" readonly>
                <div class="cs_search" id="id_search">검색</div>
            </form>
            <div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div> <?php
//selectBox
?>
    <div class="gClose" id="id_gBtn" style="display: none"></div>
    <!--임시 none-->
    <canvas id="id_myChart" width="400" height="150" style="padding-top: 50px; display: none"></canvas>
    <!--임시 none-->

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows"
        style="border:1px solid <?= $border_color ?>;">
        <tr name="ReferTime" style="position:sticky;top:0px; background-color: <?= $border_color ?>;">
            <th width="100">시간</th>
            <?php for ($i = 0; $i < 24; $i++) {
                echo "<th>{$i}</th>";
            } ?>
        </tr>

        <?php
        // 침수 시간별 데이터 표출 시 침수위,상태 선택에 따라 표출해야 할 테이블이 다름
        if ($dType == 'flood') {
            //여기부터 작업~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            $flood_dao = new WB_DATA1MIN_DAO('flood');
            $flood_vo = $flood_dao->SELECT("RegDate LIKE '{$selectDate}%' AND CD_DIST_OBSV = '{$area}'", $equip);
            $data_dao = new WB_DATA1MIN_DAO('water');
            $floodArray = array_fill(0, 24, array_fill(0, 60, '222'));

            foreach ($flood_vo as $v) {
                $floodData = $v->MRMin_array();
                for ($i = 0; $i < count($floodData); $i++) {
                    $floodArray[$v->idx * 1][$i] = $floodData[$i];
                }
            }
        } else {
            $data_dao = new WB_DATA1MIN_DAO($tableName);
        }
        $data_vo = $data_dao->SELECT("RegDate LIKE '{$selectDate}%' AND CD_DIST_OBSV = '{$area}'", $equip);
        $array = array_fill(0, 24, array_fill(0, 60, ''));
        $chartData = [];

        // 검색 결과 array에 담기
        foreach ($data_vo as $k => $v) {
            $data = $v->MRMin_array();

            $sum = $v->Sum == '-' ? 0 : $v->Sum;

            if ($dType == 'rain' || $dType == 'flood') {
                $chartData[] = $sum;
            } else {
                $chartData[] = $sum / count($data); // 오류로 인한 임시 주석
            }
            for ($i = 0; $i < count($data); $i++) {
                $array[$v->idx * 1][$i] = $data[$i];
            }
        }

        echo "<input type='hidden' name='waterUnitData' value='" . htmlspecialchars(json_encode($array)) . "'>";

        // 표출
        for ($i = 0; $i < 60; $i++) {
            echo "<tr name='ReferMin'>";
            if ($dType == 'flood') {
                echo "<td style='font-weight:bold; background-color:#f2f2f2;' rowspan='2'>{$i}분</td>";
            } else {
                echo "<td style='font-weight:bold; background-color:#f2f2f2;'>{$i}분</td>";
            }

            for ($j = 0; $j < 24; $j++) {
                echo "<td name='waterData'>";
                // 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                if ($array[$j][$i] === '' || !is_numeric($array[$j][$i])) {
                    echo '-';
                } else {
                    //Water는 M표출, 그 외 Rain, dplace는 mm표출
                    if ($dType == 'water') {
                        //echo "<font color='#4900FF'>" . number_format($array[$j][$i] / 1000, 1) . "</font>";//m
                        echo "<font color='#4900FF'>" . number_format($array[$j][$i] / 1000, 2) . '</font>'; //m
                    } elseif ($dType == 'flood') {
                        echo "<font color='#4900FF'>" . number_format($array[$j][$i] / 10, 1) . '</font>'; // 침수 수위 데이터
                    } elseif ($dType == 'tilt') {
                        echo "<font color='#4900FF'>" . number_format($array[$j][$i], 2) . '</font>';
                    } else {
                        echo "<font color='#4900FF'>" . number_format($array[$j][$i], 1) . '</font>';
                    }
                }

                echo '</td>';
            }
            echo '</tr>';
            if ($dType == 'flood') {
                echo '<tr>';
                for ($j = 0; $j < 24; $j++) {
                    //기본 "222"로 초기화 하고 array에 담긴 값이 없다면 데이터가 없다는것이므로 '-' 표시 (0도 데이터)
                    echo "<td name='floodData'>";
                    if ($floodArray[$j][$i] === '' || $floodArray[$j][$i] === '222') {
                        echo '-';
                    } else {
                        if ($floodArray[$j][$i][0] === '0') {
                            echo 'X';
                        } elseif ($floodArray[$j][$i][0] === '1') {
                            echo 'O';
                        }

                        if ($floodArray[$j][$i][1] === '0') {
                            echo 'X';
                        } elseif ($floodArray[$j][$i][1] === '1') {
                            echo 'O';
                        }

                        if ($floodArray[$j][$i][2] === '0') {
                            echo 'X';
                        } elseif ($floodArray[$j][$i][2] === '1') {
                            echo 'O';
                        }
                    }
                    echo '</td>';
                }
                echo '</tr>';
            }
        }

//for
?>
    </table>
    <div style="height:150px;"></div>
</div> <?php
//frame
?>


<script src="/js/chartjs-plugin-annotation.js"></script>
<script src="/js/chartjs-plugin-annotation.min.js"></script>
<script>
$(document).ready(function(e) {
    let colorArr = new Array(<?= count($chartData) ?>); // 배열의 길이를 설정
    let hour = [];
    let bColor, borderColor;
    let dType = "<?= $dType ?>";
    let graphData = <?= json_encode($chartData) ?>;
    let dataset = [];
    let datasets = [];
    let count = Object.keys(graphData).length;
    let data = [];
    let color = [];
    let label = $("#id_select option:selected").text();
    let sumGraph, maxGraph = 0;

    function sumNumber(graphData) {
        let sum = 0;

        for (const item of graphData) {
            const num = Number(item);
            if (!isNaN(num)) {
                sum += num;
            }
        }

        return sum;
    }

    function maxNumber(graphData) {
        const numbers = graphData.map(item => {
            const num = Number(item);
            return isNaN(num) ? 0 : num;
        });

        return Math.max(...numbers);
    }

    switch (dType) {
        case "rain":
            if (sumNumber(graphData) === 0) {
                sumGraph = 1;
            } else {
                sumGraph = sumNumber(graphData);
            }
            break;
        case "water":
            if (maxNumber(graphData) === 0) {
                maxGraph = 1;
            } else {
                maxGraph = maxNumber(graphData) / 1000;
            }
            break;
        case "dplace":
            if (maxNumber(graphData) === 0) {
                maxGraph = 1;
            } else {
                maxGraph = maxNumber(graphData) / 10;
            }
            break;
        case "flood":
            break;
    }

    for (let i = 0; i < 24; i++) {
        hour.push(i);
    }

    for (let r = 0; r < count; r++) {
        data = graphData[r] === "-" ? 0 : graphData[r];
        bColor = ["rgba(255, 99, 132, 1)"];
        borderColor = bColor;

        switch (dType) {
            case "snow":
            case "flood":
                data = (data / 10).toFixed(1);
                break;
            case "water":
                data = (data / 1000).toFixed(1);
                break;
            default:
                data = (data).toFixed(1);
                break;
        }

        dataset.unshift(data);
        color.push(bColor);

        datasets = {
            "label": label,
            "backgroundColor": color,
            "borderColor": borderColor,
            "fill": false,
            "data": dataset
        };
    }

    var ctx = document.getElementById('id_myChart').getContext('2d');
    let chartList = {
        type: <?php if ($dType == 'rain') {
            echo "'bar'";
        } else {
            echo "'line'";
        } ?>,
        data: {
            labels: hour,
            datasets: [datasets]
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
                        // labelString: '<?= date('Y-m-d', strtotime($selectDate)) ?> 데이터 통계'
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
            chartList.options.scales.yAxes[0].ticks['max'] = sumGraph + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'water':
            chartList.options.scales.yAxes[0].ticks['max'] = maxGraph + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'dplace':
            break;
        case 'soil':
            chartList.options.scales.yAxes[0].ticks['max'] = 100;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
    }

    var myChart = new Chart(ctx, chartList);
    $("#id_sDate").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        monthNamesShort: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        showMonthAfterYear: true
    });

    $("select[name='units']").change(function() {
        let i, j, minTitle, unitData, val = 0;
        let decimalVal = 0;
        let arr = JSON.parse($("input[name='waterUnitData']").val());
        $('tr[name^="ReferMin"]').remove();
        $('tr[name="ReferTime"]').after(
            "<tr name='ReferMin0'><td name='min' style='font-weight:bold; background-color:#f2f2f2;'>0분</td></tr>"
        ); // 0분 td 생성 (1분부터 after()를 하기 위한 기준)

        for (i = 0; i < 60; i++) { //
            minTitle = "<tr name='ReferMin" + i + "'>" +
                "<td style='font-weight:bold; background-color:#f2f2f2;'>" + i + "분</td>" +
                "</tr>";
            $("tr[name='ReferMin" + (i - 1) + "']").after(minTitle); //반복문 밖에 0분을 생성했으므로 -1

            for (j = 0; j < 24; j++) {
                let arrVal = arr[j][i];
                let mathVal = parseFloat(arrVal);
                if (arrVal == "") { // 배열이 비었을 경우
                    unitData = "<td name='waterData'>-</td>";
                    $("tr[name='ReferMin" + (i) + "']").append(unitData);
                } else {
                    if ($("select[name='units']").val() == '1') { // m일 때
                        decimalVal = Math.round(mathVal / 100) / 10;
                        if (decimalVal === Math.floor(decimalVal)) decimalVal = decimalVal.toFixed(1);
                        val = decimalVal.toString();
                        unitData = "<td name='waterData' style='color:#4900FF'>" + val + "</td>";
                        $("tr[name='ReferMin" + (i) + "']").append(unitData);
                    } else if ($("select[name='units']").val() == '2') { // cm일 때
                        decimalVal = Math.round(mathVal) / 10;
                        if (decimalVal === Math.floor(decimalVal)) decimalVal = decimalVal.toFixed(1);
                        val = decimalVal.toString();
                        unitData = "<td name='waterData' style='color:#4900FF'>" + val + "</td>";
                        $("tr[name='ReferMin" + (i) + "']").append(unitData);
                    } else if ($("select[name='units']").val() == '3') { // mm일 때
                        decimalVal = Math.round(mathVal);
                        if (decimalVal === Math.floor(decimalVal)) decimalVal = decimalVal.toFixed(0);
                        val = decimalVal.toString();
                        unitData = "<td name='waterData' style='color:#4900FF'>" + val + "</td>";
                        $("tr[name='ReferMin" + (i) + "']").append(unitData);
                    }
                }
            }
        }
    })
});
</script>