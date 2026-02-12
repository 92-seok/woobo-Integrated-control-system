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

$selectDate = $year . $month . $day;
$showDate = $year . '-' . $month . '-' . $day;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = new WB_EQUIP_VO();
$equip_vo = $equip_dao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");

$dao = new WB_DATA1HOUR_DAO($dType);
$vo = new WB_DATA1HOUR_VO();
?>
<div class="cs_frame">
    <div class="cs_selectBox">
        <div style="font-size: 16px;margin-bottom: 5px;"><?= $year ?>년<?= $month ?>월<?= $day ?>일</div>
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
                <input type="hidden" name="addr" id="id_addr" value="Day.php">
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
            if ($dType == 'dplace') {
                // echo "<th colspan='2' width='150'>지역명</th>";
                echo "<th width='100'>지역명</th>";
                echo "<th width='50'>채널</th>";
            } else {
                echo "<th width='100'>지역명</th>";
            }

            if ($dType == 'flood') {
                echo '<th>타입</th>';
            }

            for ($i = 0; $i < 24; $i++) {
                echo "<th>{$i}</th>";
            }

            if ($dType == 'rain') {
                echo "<th width='50'>최고</th>";
                echo "<th width='60'>계</th>";
            } elseif ($dType == 'water') {
                echo "<th width='50'>최대</th>";
                echo "<th width='50'>최소</th>";
            } elseif ($dType == 'dplace' || $dType == 'snow') {
                echo "<th width='50'>최고</th>";
            }
            echo '</tr>';

            $graphData = [];
            $r = 1;

            foreach ($equip_vo as $i => $evo) {
                echo "<tr name='dataTr{$i}'>";

                /* 지역명 & Sub_OBSV || 침수 Row 갯수 */
                if ($dType == 'dplace') {
                    $row = $evo->SubOBCount;
                } elseif ($dType == 'flood') {
                    $row = '4';
                } else {
                    $row = '1';
                }

                echo "<td name='titleTd{$i}' rowspan='{$row}' style='font-weight:bold; background-color:#f2f2f2; border-right:2px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";

                /* 변위, 침수는 한 쿼리에 두줄 이상 사용하기에 따로 구현 */
                if ($dType != 'dplace' && $dType != 'flood') {
                    // 강우, 수위, 함수비, 적설, 경사
                    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");
                    $max = 0;

                    $data = array_fill(0, 25, '');
                    if ($vo != null) {
                        $data = $vo->MR_array();
                    } else {
                        $vo = new WB_DATA1HOUR_VO();
                    }

                    echo "<input type='hidden' id='test' name='unitData' value='" . json_encode($vo) . "' />";

                    for ($i = 1; $i <= 24; $i++) {
                        echo "<td name='dataTd'>";
                        if ($vo->{"MR{$i}"} !== null) {
                            if ($dType == 'snow') {
                                // 적설 Cm
                                echo number_format($vo->{"MR{$i}"} / 10, 1);
                            } elseif ($dType == 'water') {
                                // 수위
                                echo number_format($vo->{"MR{$i}"} / 1000, 2);
                            } elseif ($dType == 'tilt') {
                                echo number_format($vo->{"MR{$i}"} / 1, 2);
                            } elseif ($dType == 'rain') {
                                echo number_format($vo->{"MR{$i}"}, 1);
                                if ($max < $vo->{"MR{$i}"}) {
                                    $max = $vo->{"MR{$i}"};
                                } // rain1hour Table에는 DayMax Column이 없어서 따로 구함
                            } else {
                                echo number_format($vo->{"MR{$i}"}, 1);
                            }
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                    }

                    $graphData[$r] = $data;
                    $graphData[$r++][0] = $evo->NM_DIST_OBSV;

                    // DB는 mm 수집 & (Rain : mm , Water : M , Snow : Cm) 소수점 한자리까지만 표출 (Min, Max, Sum)
                    switch ($dType) {
                        case 'snow':
                            $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax / 10, 1) : '-';
                            break;

                        case 'water':
                            $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax / 1000, 2) : '-';
                            $strMin = is_numeric($vo->DayMin) ? number_format($vo->DayMin / 1000, 2) : '-';
                            break;

                        case 'tilt':
                            $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax / 1, 2) : '-';
                            $strMin = is_numeric($vo->DayMin) ? number_format($vo->DayMin / 1, 2) : '-';
                            break;

                        case 'rain':
                            $strMax = is_numeric($max) ? number_format($max, 1) : '-';
                            $strSum = is_numeric($vo->DaySum) ? number_format($vo->DaySum, 1) : '-';
                            break;

                        default:
                            $strMax = is_numeric($max) ? number_format($max, 1) : '-';
                            $strSum = is_numeric($vo->DaySum) ? number_format($vo->DaySum, 1) : '-';
                            break;
                    }

                    if ($dType == 'rain') {
                        echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                        echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
                    } elseif ($dType == 'snow') {
                        echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                    } elseif ($dType == 'water') {
                        echo "<td name='dayMax' style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";
                        echo "<td name='dayMin' style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
                    }
                } elseif ($dType == 'dplace') {
                    // 변위
                    if (is_null($evo->SubOBCount)) {
                        echo "<td width='20' style='background-color:#f2f2f2;'>NULL</td>";
                        for ($j = 1; $j <= 24; $j++) {
                            echo '<td>-</td>';
                        }
                        echo "<td style='background:#FAE4D6; font-weight:bold'>-</td>";
                        echo '</tr>';
                    } else {
                        // $i = Sub_OBSV로 $i++ 될때마다 쿼리로 데이터값 받아와 mm로 표출
                        for ($i = 1; $i <= $evo->SubOBCount; $i++) {
                            if ($i != 1) {
                                echo '<tr>';
                            }

                            $subobsv = $i;
                            echo "<td width='20' style='background-color:#f2f2f2;'>{$subobsv}</td>";
                            $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'", $subobsv);

                            $data = array_fill(0, 25, '');
                            if ($vo != null) {
                                $data = $vo->MR_array();
                            } else {
                                $vo = new WB_DATA1HOUR_VO();
                            }

                            for ($j = 1; $j <= 24; $j++) {
                                echo '<td>';
                                // if( $vo->{"MR{$j}"} !== null )
                                if ($data !== null) {
                                    echo number_format($data[$j], 1);
                                } else {
                                    echo '-';
                                }
                                echo '</td>';
                            }

                            $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax, 1) : '-';
                            echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

                            if ($i != $evo->SubOBCount) {
                                echo '</tr>';
                            }
                        }
                        $graphData[$r] = $data;
                        $graphData[$r++][0] = $evo->NM_DIST_OBSV;
                    }
                } elseif ($dType == 'flood') {
                    // 침수
                    // 침수수위 $data 배열에 담기
                    $dao = new WB_DATA1HOUR_DAO('water');
                    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

                    $data = array_fill(0, 25, '');
                    if ($vo != null) {
                        $data = $vo->MR_array();
                    } else {
                        $vo = new WB_DATA1HOUR_VO();
                    }

                    // 침수상태 $flood 배열에 담기
                    $dao = new WB_DATA1HOUR_DAO('flood');
                    $floodvo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

                    $flooddata = array_fill(0, 25, '');
                    if ($floodvo != null) {
                        $flooddata = $floodvo->MR_array();
                    } else {
                        $floodvo = new WB_DATA1HOUR_VO();
                    }

                    // 침수수위 표출
                    echo "<td style='background-color:#f2f2f2;'>수위</td>";
                    for ($i = 1; $i <= 24; $i++) {
                        echo '<td>';
                        if ($data !== null) {
                            echo number_format($data[$i] / 10, 1);
                        } else {
                            echo '-';
                        }
                        echo '</td>';
                    }
                    $graphData[$r] = $data;
                    $graphData[$r++][0] = $evo->NM_DIST_OBSV;
                    echo '</tr>';

                    // 침수상태 표출
                    for ($j = 0; $j <= 2; $j++) {
                        echo '<tr>';
                        echo "<td style='background-color:#f2f2f2;'>침수" . ($j + 1) . '</td>';
                        for ($i = 1; $i <= 24; $i++) {
                            echo '<td>';

                            if ($flooddata === null) {
                                echo '-';
                            } else {
                                if ($flooddata[$j] === '0') {
                                    echo 'X';
                                } elseif ($flooddata[$j] === '1') {
                                    echo 'O';
                                } else {
                                    echo '-';
                                }
                            }

                            echo '</td>';
                        }
                        if ($j != 3) {
                            echo '</tr>';
                        }
                    }
                    $graphFData[$r] = $flooddata;
                    $graphFData[$r++][0] = $evo->NM_DIST_OBSV;
                }

                echo '</tr>';
            }
            ?>
    </table>
</div> <?php
//frame
?>
<script>
$(document).ready(function(e) {
    let colorArr = new Array(<?= count($graphData) ?>); // 배열의 길이를 설정
    let hour = [];
    let label, data;
    let graphData = <?= json_encode($graphData) ?>;
    let dType = "<?= $dType ?>";
    let dataset = [];
    let datasets = [];
    let bColor = [];
    let count = Object.keys(graphData).length;
    let color = [
        'rgba(0, 128, 255, 1)', 'rgba(255, 0, 102, 1)', 'rgba(102, 255, 0, 1)', 'rgba(255, 153, 0, 1)',
        'rgba(0, 255, 204, 1)', 'rgba(153, 0, 255, 1)', 'rgba(0, 102, 204, 1)', 'rgba(204, 0, 102, 1)',
        'rgba(255, 255, 0, 1)', 'rgba(0, 204, 153, 1)', 'rgba(255, 51, 0, 1)', 'rgba(102, 51, 153, 1)',
        'rgba(0, 153, 255, 1)', 'rgba(255, 102, 0, 1)', 'rgba(153, 204, 0, 1)', 'rgba(204, 102, 0, 1)',
        'rgba(51, 204, 255, 1)', 'rgba(255, 0, 204, 1)', 'rgba(204, 0, 0, 1)', 'rgba(51, 153, 102, 1)',
        'rgba(153, 0, 0, 1)', 'rgba(0, 51, 102, 1)', 'rgba(204, 51, 255, 1)', 'rgba(102, 255, 255, 1)',
        'rgba(255, 204, 204, 1)', 'rgba(204, 255, 204, 1)', 'rgba(204, 204, 255, 1)',
        'rgba(255, 255, 204, 1)',
        'rgba(255, 204, 255, 1)', 'rgba(204, 255, 255, 1)', 'rgba(255, 255, 255, 1)',
        'rgba(192, 192, 192, 1)',
        'rgba(128, 0, 128, 1)', 'rgba(0, 128, 128, 1)', 'rgba(128, 128, 0, 1)', 'rgba(0, 0, 128, 1)',
        'rgba(255, 128, 0, 1)', 'rgba(0, 255, 128, 1)', 'rgba(128, 0, 0, 1)', 'rgba(0, 128, 0, 1)',
        'rgba(0, 0, 0, 1)', 'rgba(60, 179, 113, 1)', 'rgba(106, 90, 205, 1)', 'rgba(70, 130, 180, 1)',
        'rgba(255, 105, 180, 1)', 'rgba(219, 112, 147, 1)', 'rgba(176, 224, 230, 1)',
        'rgba(244, 164, 96, 1)',
        'rgba(0, 76, 153, 1)', 'rgba(255, 204, 0, 1)', 'rgba(255, 77, 77, 1)', 'rgba(0, 230, 64, 1)',
        'rgba(204, 153, 255, 1)', 'rgba(0, 255, 128, 1)', 'rgba(255, 128, 128, 1)',
        'rgba(128, 255, 128, 1)',
        'rgba(128, 128, 255, 1)', 'rgba(255, 255, 128, 1)', 'rgba(255, 179, 255, 1)',
        'rgba(128, 255, 255, 1)',
        'rgba(192, 128, 255, 1)', 'rgba(0, 0, 160, 1)', 'rgba(0, 100, 0, 1)', 'rgba(100, 0, 0, 1)',
        'rgba(150, 75, 0, 1)', 'rgba(255, 215, 0, 1)', 'rgba(233, 150, 122, 1)', 'rgba(127, 255, 212, 1)',
        'rgba(220, 20, 60, 1)', 'rgba(65, 105, 225, 1)', 'rgba(85, 107, 47, 1)', 'rgba(210, 105, 30, 1)',
        'rgba(100, 149, 237, 1)', 'rgba(0, 191, 255, 1)', 'rgba(255, 160, 122, 1)',
        'rgba(144, 238, 144, 1)',
        'rgba(255, 99, 71, 1)', 'rgba(154, 205, 50, 1)', 'rgba(72, 209, 204, 1)', 'rgba(186, 85, 211, 1)',
        'rgba(255, 20, 147, 1)', 'rgba(135, 206, 235, 1)', 'rgba(32, 178, 170, 1)',
        'rgba(240, 128, 128, 1)',
        'rgba(218, 165, 32, 1)', 'rgba(255, 228, 181, 1)', 'rgba(173, 216, 230, 1)',
        'rgba(255, 182, 193, 1)',
        'rgba(221, 160, 221, 1)', 'rgba(250, 128, 114, 1)', 'rgba(244, 164, 96, 1)',
        'rgba(210, 180, 140, 1)',
        'rgba(176, 196, 222, 1)', 'rgba(152, 251, 152, 1)', 'rgba(255, 228, 225, 1)',
        'rgba(189, 183, 107, 1)',
        'rgba(112, 128, 144, 1)', 'rgba(72, 61, 139, 1)', 'rgba(46, 139, 87, 1)', 'rgba(0, 139, 139, 1)',
        'rgba(0, 0, 205, 1)', 'rgba(255, 69, 0, 1)', 'rgba(124, 252, 0, 1)', 'rgba(173, 255, 47, 1)',
        'rgba(199, 21, 133, 1)', 'rgba(255, 218, 185, 1)', 'rgba(176, 224, 230, 1)',
        'rgba(255, 239, 213, 1)',
        'rgba(250, 250, 210, 1)', 'rgba(0, 250, 154, 1)', 'rgba(219, 112, 147, 1)', 'rgba(139, 0, 139, 1)',
        'rgba(255, 228, 196, 1)', 'rgba(255, 160, 122, 1)', 'rgba(100, 149, 237, 1)',
        'rgba(244, 164, 96, 1)',
        'rgba(233, 150, 122, 1)', 'rgba(60, 179, 113, 1)', 'rgba(255, 222, 173, 1)',
        'rgba(255, 105, 180, 1)',
        'rgba(138, 43, 226, 1)', 'rgba(102, 205, 170, 1)', 'rgba(147, 112, 219, 1)', 'rgba(255, 215, 0, 1)',
        'rgba(123, 104, 238, 1)', 'rgba(72, 209, 204, 1)', 'rgba(240, 230, 140, 1)',
        'rgba(173, 255, 47, 1)',
        'rgba(255, 250, 250, 1)', 'rgba(105, 105, 105, 1)', 'rgba(190, 190, 190, 1)',
        'rgba(119, 136, 153, 1)',
        'rgba(176, 196, 222, 1)', 'rgba(95, 158, 160, 1)', 'rgba(255, 248, 220, 1)',
        'rgba(255, 235, 205, 1)',
        'rgba(240, 248, 255, 1)', 'rgba(255, 250, 205, 1)', 'rgba(124, 252, 0, 1)', 'rgba(127, 255, 0, 1)',
        'rgba(102, 205, 170, 1)', 'rgba(255, 127, 80, 1)', 'rgba(255, 69, 0, 1)', 'rgba(139, 0, 0, 1)'
    ];

    let sumGraph, maxGraph = 0;

    function sumNumber(graphData) {
        let sum = 0;

        for (let item in graphData) {
            let num = Number(item);

            if (!isNaN(num)) {
                sum += num;

            }
        }

        return sum;
    }

    function maxNumber(graphData) {
        let overallMax = 0;
        for (const key in graphData) {
            if (graphData.hasOwnProperty(key)) {
                // 배열의 두 번째 요소부터 시작 (첫 번째는 문자열)
                const values = graphData[key].slice(1);
                // 숫자로 변환한 값 중에서 가장 큰 값을 찾음
                const maxInArray = Math.max(...values.map(value => Number(value)).filter(num => !isNaN(num)));
                // 전체 최대값 업데이트
                overallMax = Math.max(overallMax, maxInArray);
            }
        }

        return overallMax;
    }

    console.log(`dType: ${dType}`);
    console.log(`graphData: ${graphData}`);

    switch (dType) {
        case "rain":
            if (sumNumber(graphData) === 0) {
                sumGraph = 1;
                maxGraph = 1;
            } else {
                sumGraph = sumNumber(graphData);
                maxGraph = maxNumber(graphData);
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

    function getRandomColor() {
        let r = Math.floor(Math.random() * 256);
        let g = Math.floor(Math.random() * 256);
        let b = Math.floor(Math.random() * 256);
        return `rgba(${r}, ${g}, ${b}, 1)`;
    }


    if (colorArr.length < 24) {
        for (let i = 0; i < colorArr.length; i++) {
            bColor[i] = color[i];
        }
    } else {
        for (let i = 0; i < colorArr.length; i++) {
            bColor[i] = getRandomColor();
        }
    }

    let colCount = 0;
    for (let r in graphData) {
        colCount += 1;
        label = graphData[r][0];
        let arrData = [];
        color = bColor[(colCount - 1)];

        for (let i = 1; i <= 24; i++) {
            if (graphData[r][i] !== "") {
                switch (dType) {
                    case "snow":
                    case "flood":
                        data = (graphData[r][i] / 10).toFixed(1);
                        break;
                    case "water":
                        data = (graphData[r][i] / 1000).toFixed(1);
                        break;
                    default:
                        data = (graphData[r][i]);
                        break;
                }
                console.log(typeof(graphData[r][i]));
                arrData.push(data);
                //arrData.push(color);
            } else {
                // 라인 형태의 그래프이기때문에 중간 값이 비면 자연스러운 연결을 위해 앞과 뒤의 값을 이용해 연결 (처음 값과 마지막 값은 상관 없으므로 예외처리)
                if (i == 1) {
                    data = "";
                } else if (i == 24) {
                    data += data + ',';
                } else {
                    // 강우 데이터는 막대 그래프 표시로 예외 처리
                    if (dType == "rain") {
                        data += ',0';
                    } else {
                        let pre_idx = 0;
                        let post_idx = 0;
                        let pre_data = 0;
                        let post_data = 0;

                        // 앞쪽에 유효한 값 찾기
                        for (let j = i; j >= 1; j--) {
                            if (graphData[r][j] !== "") {
                                pre_idx = j;

                                switch (dType) {
                                    case "snow":
                                    case "flood":
                                        pre_data = (graphData[r][j] / 10);
                                        break;
                                    case "water":
                                        pre_data = (graphData[r][j] / 1000);
                                        break;
                                    default:
                                        pre_data = (graphData[r][j]);
                                        break;
                                }
                            }
                        }
                        // 뒷쪽에 유효한 값 찾기
                        for (let j = i; j <= 24; j++) {
                            if (graphData[r][j] !== "") {
                                post_idx = j;

                                switch (dType) {
                                    case "snow":
                                    case "flood":
                                        post_data = (graphData[r][j] / 10);
                                        break;
                                    case "water":
                                        post_data = (graphData[r][j] / 1000);
                                        break;
                                    default:
                                        post_data = (graphData[r][j]);
                                        break;
                                }
                            }
                        }

                        // 앞이나 뒷쪽 값이 없다면 예외 처리
                        if (pre_idx == 0 || post_idx == 0) data = data + ',';
                        else {
                            // 앞, 뒤 값이 있다면 몇번 건너뛰었는지 계산
                            let k = (i - pre_idx) / (post_idx - pre_idx);
                            // 건너뛴 갯수와 값에 비례하여 데이터 계산
                            data = data + "," + (pre_data + (post_data - pre_data) * k);
                        }
                    }
                }
            }
        }
        dataset = {
            "label": label,
            "backgroundColor": bColor[(colCount - 1)],
            "borderColor": bColor[(colCount - 1)],
            "fill": false,
            "data": arrData
        };
        datasets.push(dataset);
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
            datasets: datasets
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
    console.log(dataType);
    switch (dataType) {
        case 'rain':
            // console.log('switch rain');
            // chartList.options.scales.yAxes[0].ticks['max'] = sumGraph + 5;
            chartList.options.scales.yAxes[0].ticks['max'] = maxGraph + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'water':
            // console.log('switch water');
            chartList.options.scales.yAxes[0].ticks['max'] = maxGraph + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        case 'dplace':
            // console.log('switch dplace');
            break;
        case 'soil':
            // console.log('switch soil');
            chartList.options.scales.yAxes[0].ticks['max'] = 100;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
        default:
            // console.log('switch default');
            chartList.options.scales.yAxes[0].ticks['max'] = maxGraph + 5;
            chartList.options.scales.yAxes[0].ticks['min'] = 0;
            break;
    }

    console.log('start');
    var myChart = new Chart(ctx, chartList);

    $("#id_sDate").datepicker({
        dateFormat: "yy-mm-dd",
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        showMonthAfterYear: true
    });

    $("select[name='units']").change(function() {
        let i = 0;
        let td, maxTd, minTd = "";
        let val, max, min, unitData = "";

        $('td[name^="dataTd"]').remove();
        $('td[name^="dayMax"]').remove();
        $('td[name^="dayMin"]').remove();

        for (i; i < $("input[name='unitData']").length; i++) {
            unitData = JSON.parse($("input[name='unitData']").eq(i).val());
            $("td[name='titleTd" + i + "']").after("<td name='dataTd0' style='display:none'></td>");
            for (let j = 1; j <= 24; j++) {
                if (unitData != "" || unitData != 'undefined') {
                    if ($("select[name='units']").val() == '1') {
                        if (unitData["MR" + j] != null) {
                            val = (Math.round(unitData["MR" + j] / 100) / 10).toFixed(1);
                            max = (Math.round(unitData["DayMax"] / 100) / 10).toFixed(1);
                            min = (Math.round(unitData["DayMin"] / 100) / 10).toFixed(1);
                        } else {
                            val = "-";
                        }
                    } else if ($("select[name='units']").val() == '2') {
                        if (unitData["MR" + j] != null) {
                            val = (Math.round(unitData["MR" + j]) / 10).toFixed(1);
                            max = (Math.round(unitData["DayMax"]) / 10).toFixed(1);
                            min = (Math.round(unitData["DayMin"]) / 10).toFixed(1);
                        } else {
                            val = "-";
                        }
                    } else if ($("select[name='units']").val() == '3') {
                        if (unitData["MR" + j] != null) {
                            val = (Math.round(unitData["MR" + j])).toFixed(0);
                            max = (Math.round(unitData["DayMax"])).toFixed(0);
                            min = (Math.round(unitData["DayMin"])).toFixed(0);
                        } else {
                            val = "-";
                        }
                    }
                    td = "<td name='dataTd" + j + "' style='color:#4900FF'>" + val + "</td>";
                    $("td[name='titleTd" + i + "']").siblings("td[name='dataTd" + (j - 1) + "']").after(
                        td);

                }
            }
            if (unitData["DayMax"] == 0) {
                max = "0.0";
            } else if (unitData["DayMax"] == null) {
                max = "-";
            }
            if (unitData["DayMin"] == 0) {
                min = "0.0";
            } else if (unitData["DayMin"] == null) {
                min = "-";
            }
            maxTd = "<td name='dayMax' style='background:#E7E9C9; font-weight:bold'>" + max + "</td>";
            minTd = "<td name='dayMin' style='background:#D8E5F8; font-weight:bold'>" + min + "</td>";

            $("td[name='titleTd" + i + "']").siblings("td[name='dataTd24']").after(maxTd);
            $("td[name='titleTd" + i + "']").siblings("td[name='dayMax']").after(minTd);
        }
    })
});
</script>