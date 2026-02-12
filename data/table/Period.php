<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['year1'])) {
    $year1 = $_GET['year1'];
} else {
    $year1 = date('Y', strtotime('-7days'));
}
if (isset($_GET['month1'])) {
    $month1 = $_GET['month1'];
} else {
    $month1 = date('m', strtotime('-7days'));
}
if (isset($_GET['day1'])) {
    $day1 = $_GET['day1'];
} else {
    $day1 = date('d', strtotime('-7days'));
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
if (isset($_GET['floodType'])) {
    $floodType = $_GET['floodType'];
} else {
    $floodType = 'water';
}

$selectDate1 = $year1 . $month1 . $day1;
$selectDate2 = $year2 . $month2 . $day2;
$showDate1 = $year1 . '-' . $month1 . '-' . $day1;
$showDate2 = $year2 . '-' . $month2 . '-' . $day2;
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = new WB_EQUIP_VO();

$datadao = new WB_DATA1HOUR_DAO($dType);
$datavo = new WB_DATA1HOUR_VO();

if (isset($_GET['area'])) {
    $area = $_GET['area'];
} else {
    $equip_vo = $equip_dao->SELECT_SINGLE("GB_OBSV = '{$area_code}' AND USE_YN = '1'");
    $area = $equip_vo->CD_DIST_OBSV;
}
?>
<div class="cs_frame">
    <div class="cs_selectBox">
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
                <input type="hidden" name="addr" id="id_addr" value="Period.php">

                <?php
                // 해당 장비 전체 불러오기 (Select Box)
                $equip_vo = $equip_dao->SELECT("GB_OBSV  = '{$area_code}' and USE_YN = '1'");

                echo "<select name='area' id='id_select'>";
                foreach ($equip_vo as $v) {
                    if ($area == '') {
                        $area = $v->CD_DIST_OBSV;
                    }
                    if ($area == $v->CD_DIST_OBSV) {
                        $sel = 'selected';
                    } else {
                        $sel = '';
                    }

                    echo "<option value='{$v->CD_DIST_OBSV}' {$sel}>{$v->NM_DIST_OBSV}</option>";
                }
                echo '</select>&nbsp;&nbsp;';

                // 변위: Sub OBSV 불러와서 Select Box 만들기, 침수: 침수수위/침수상태 Select Box 만들기
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
                    echo '</select>&nbsp;&nbsp;';
                    echo "<input type='hidden' name='dType' value='dplace'>";
                } else {
                    echo "<input type='hidden' name='dType' value='{$dType}'>";
                }
                ?>
                <label class="calendar" for="id_sDate1" style="background-color: <?= $border_color ?>;">달 력</label>
                <input type="text" name="sDate1" class="sDate_css" id="id_sDate1" value="<?= $showDate1 ?>" readonly>
                <label class="calendar" for="id_sDate2" style="background-color: <?= $border_color ?>;">달 력</label>
                <input type="text" name="sDate2" class="sDate_css" id="id_sDate2" value="<?= $showDate2 ?>" readonly>
                <div class="cs_search" id="id_search">검색</div>
            </form>
            <div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div> <?php
//selectBox
?>

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows"
        style="border:1px solid <?= $border_color ?>;">
        <tr style="position:sticky;top:0px; background-color: <?= $border_color ?>;">
            <th width="110">날짜</th>
            <?php
            if ($dType == 'flood') {
                $rowspan = '2';
            } else {
                $rowspan = '1';
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

            // 침수 중 침수수위의 경우 Data가 Water1Hour Table에 있으니 DAO Type 변경!
            if ($dType == 'flood') {
                $datadao = new WB_DATA1HOUR_DAO('water');
                $flooddao = new WB_DATA1HOUR_DAO('flood');

                $floodvo = $flooddao->SELECT(
                    "RegDate BETWEEN '{$selectDate1}' AND '{$selectDate2}' AND CD_DIST_OBSV = '{$area}'",
                    $equip,
                );

                $floodArray = [];
                foreach ($floodvo as $idx => $fv) {
                    for ($i = 1; $i <= 24; $i++) {
                        $floodArray[$idx]['MR' . $i] = $fv->{"MR{$i}"};
                    }
                }
            }

            $datavo = $datadao->SELECT(
                "RegDate BETWEEN '{$selectDate1}' AND '{$selectDate2}' AND CD_DIST_OBSV = '{$area}'",
                $equip,
            );

            foreach ($datavo as $idx => $v) {
                $strMin = '';
                $strMax = '';
                $strSum = '';

                $max = '';
                $min = '';
                $sum = 0;
                if ($v->RegDate == null) {
                    echo "<tr name='referDate'>";
                    echo '</tr>';
                } else {
                    echo "<input type='hidden' name='unitData' value='" . json_encode($v) . "' />";
                    echo "<tr name='referDate{$idx}'>";
                    echo "<td name='date{$idx}' style='font-weight:bold; background-color:#f2f2f2'  rowspan='{$rowspan}'>" .
                        date('Y년 m월 d일', strtotime($v->RegDate)) .
                        '</td>';

                    for ($i = 1; $i <= 24; $i++) {
                        echo '<td>'; // 요~부터
                        if ($v->{"MR{$i}"} === '' || $v->{"MR{$i}"} === null) {
                            echo '-';
                        } else {
                            echo "<font color='#4900FF'>";
                            if ($dType == 'snow' || $dType == 'flood') {
                                echo number_format($v->{"MR{$i}"} / 10, 1);
                            } elseif ($dType == 'water') {
                                echo $v->{"MR{$i}"} === null ? '-' : number_format($v->{"MR{$i}"} / 1000, 2);
                            } elseif ($dType == 'dplace') {
                                echo number_format($v->{"MR{$i}"}, 1);
                            } elseif ($dType == 'tilt') {
                                echo number_format($v->{"MR{$i}"}, 2);
                            } else {
                                echo number_format($v->{"MR{$i}"}, 1);
                            }

                            if ($sum == 0) {
                                $max = $v->{"MR{$i}"};
                                $min = $v->{"MR{$i}"};
                            } else {
                                if ($max < $v->{"MR{$i}"}) {
                                    $max = $v->{"MR{$i}"};
                                }
                                if ($min > $v->{"MR{$i}"}) {
                                    $min = $v->{"MR{$i}"};
                                }
                            }
                            $sum += $v->{"MR{$i}"};
                            echo '</font>';
                        }
                        echo '</td>'; // 요~까지 한칸!
                    }

                    switch ($dType) {
                        case 'rain':
                            $strMax = is_numeric($max) ? number_format($max, 1) : $max;
                            echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

                            $strSum = is_numeric($sum) ? number_format($sum, 1) : $sum;
                            echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
                            break;

                        case 'snow':
                            $strMax = is_numeric($max) ? number_format($max / 10, 1) : $max;
                            echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                            break;

                        case 'water':
                            $strMax = is_numeric($max) ? number_format($max / 1000, 2) : $max;
                            echo "<td style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";

                            $strMin = is_numeric($min) ? number_format($min / 1000, 2) : number_format('0.0', 1); //$min;
                            echo "<td style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
                            break;

                        case 'dplace':
                            $strMax = is_numeric($max) ? number_format($max, 1) : $max;
                            echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                            break;

                        case 'flood':
                            break;

                        default:
                            $strMax = is_numeric($max) ? number_format($max, 1) : $max;
                            echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
                    }
                    echo '</tr>';
                }
                if ($dType == 'flood') {
                    for ($i = 1; $i <= 24; $i++) {
                        echo '<td>';
                        if ($floodArray[$idx]["MR{$i}"] === '' || $floodArray[$idx]["MR{$i}"] === null) {
                            echo '-';
                        } else {
                            if ($floodArray[$idx]["MR{$i}"][0] === '0') {
                                echo 'X';
                            } elseif ($floodArray[$idx]["MR{$i}"][0] === '1') {
                                echo 'O';
                            }

                            if ($floodArray[$idx]["MR{$i}"][1] === '0') {
                                echo 'X';
                            } elseif ($floodArray[$idx]["MR{$i}"][1] === '1') {
                                echo 'O';
                            }

                            if ($floodArray[$idx]["MR{$i}"][2] === '0') {
                                echo 'X';
                            } elseif ($floodArray[$idx]["MR{$i}"][2] === '1') {
                                echo 'O';
                            }
                        }
                        echo '</td>';
                    }
                }
            }
            ?>
    </table>
</div> <?php
//frame
?>
<script>
$(document).ready(function(e) {
    let startDate = $("#id_sDate1").datepicker({
        dateFormat: "yy-mm-dd",
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        onSelect: function(selectedDate) {
            // Set the minDate of the second datepicker
            var minDate = $(this).datepicker("getDate");
            $("#id_sDate2").datepicker("option", "minDate", minDate);
        }
    });

    $("#id_sDate2").datepicker({
        dateFormat: "yy-mm-dd",
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        onSelect: function(selectedDate) {
            // Set the maxDate of the first datepicker
            var maxDate = $(this).datepicker("getDate");
            $("#id_sDate1").datepicker("option", "maxDate", maxDate);
        }
    });

    $("select[name='units']").change(function() {
        let i, j = 0;
        let parseData, unitData, waterData, val, max, min = "";
        let tr, maxTd, minTd = "";
        let year, month, day = "";
        let factor = 1;
        let point = 0;

        $("tr[name^='referDate']").remove();

        for (i = 0; i < $("input[name='unitData']").size(); i++) {
            parseData = JSON.parse($("input[name='unitData']").eq(i).val());
            year = parseData.RegDate.substring(0, 4);
            month = parseData.RegDate.substring(4, 6);
            day = parseData.RegDate.substring(6, 8);

            tr = "<tr name='referDate" + (i) +
                "'><td style='font-weight:bold; background-color:#f2f2f2;'>" + year + "년 " + month +
                "월 " + day + "일</td></tr>";

            if (i === 0) {
                $(".cs_datatable tbody tr:first").after(tr);
            } else {
                $("tr[name='referDate" + (i - 1) + "']").after(tr);
            }

            for (j = 1; j <= 24; j++) {
                switch ($("select[name='units']").val()) {
                    case '1':
                        factor = 1000;
                        point = 1;
                        break;
                    case '2':
                        factor = 10;
                        point = 1;
                        break;
                    case '3':
                        factor = 1;
                        point = 0;
                        break;
                    default:
                        continue;
                }

                if (parseData["MR" + j] == null) {
                    val = "-";
                } else {
                    val = (Math.round(parseFloat(parseData['MR' + j])) / factor).toFixed(point);
                    max = (Math.round(parseFloat(parseData['DayMax'])) / factor).toFixed(point);
                    min = (Math.round(parseFloat(parseData['DayMin'])) / factor).toFixed(point);
                }

                unitData = "<td name='dataTd" + j + "' style='color:#4900FF'>" + val + "</td>";
                $("tr[name='referDate" + (i) + "']").append(unitData);
            }

            if (parseData["DayMax"] == 0) {
                max = "0.0";
            } else if (parseData["DayMax"] == null) {
                max = "-";
            }
            if (parseData["DayMin"] == 0) {
                min = "0.0";
            } else if (parseData["DayMin"] == null) {
                min = "-";
            }
            maxTd = "<td name='periodMax' style='background:#E7E9C9; font-weight:bold'>" + max +
                "</td>";
            minTd = "<td name='periodMin' style='background:#D8E5F8; font-weight:bold'>" + min +
                "</td>";

            $("tr[name='referDate" + (i) + "']").children("td[name='dataTd24']").after(maxTd);
            $("tr[name='referDate" + (i) + "']").children("td[name='periodMax']").after(minTd);
        }
    })
});
</script>