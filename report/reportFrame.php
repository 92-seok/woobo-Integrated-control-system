<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php'; ?>

<!doctype html>
<html>

<head>
    <title><?= $_SESSION['title'] ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumsquare.css" />
    <link rel="stylesheet" type="text/css" href="/css/include.css" />
    <link rel="stylesheet" type="text/css" href="/css/frame.css" />
    <link rel="stylesheet" type="text/css" href="/css/jquery-ui.css" />
    <link rel="shortcut icon" href="/image/favicon.ico"> <!-- ico 파일 -->
</head>

<body>
    <script>
    let pType = "report";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box">
        <div class="cs_frame">
            <div class="cs_report">
                <div class="cs_header">
                    <img class="cs_logo" src="/image/slogan.png">
                    <div>통합관제시스템 현황</div>
                    <div style="font-size:18px;">[재난안전대책본부]</div>
                </div>

                <div class="cs_container" style="flex-direction:column;">
                    <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
                        <!--                    <label class="calendar" for="id_sDate" style="">달 력</label>-->
                        <!--                    <input type="text" name="sDate" class="sDate_css" id="id_sDate" value="--><?php
//=date('Y-m-d')
?>
                        <!--" readonly>-->
                        <!--                    <div class="cs_search" id="id_search">검색</div>-->
                    </form>
                    <?php
                    $sql = "SELECT DISTINCT GB_OBSV FROM wb_equip WHERE USE_YN = '1' ORDER BY GB_OBSV ASC";
                    $res = mysqli_query($conn, $sql);
                    $gb_obsv = [];
                    while ($row = mysqli_fetch_assoc($res)) {
                        array_push($gb_obsv, $row['GB_OBSV']);
                    }

                    // 수위
                    if (in_array('02', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 수위
                        <div style="float:right; font-size:10px;">단위 : M</div>
                    </div>

                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#5fbaef;">
                            <th>지역명</th>
                            <th>현재</th>
                        </tr>
                        <?php
                        $water_sql = "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.*
									from wb_equip as a left join wb_water1hour as b
									on a.CD_DIST_OBSV = b.CD_DIST_OBSV
									where GB_OBSV = '02' and USE_YN = '1' and regDate = CURDATE() order by a.CD_DIST_OBSV asc";
                        $water_res = mysqli_query($conn, $water_sql);
                        while ($water_row = mysqli_fetch_assoc($water_res)) { ?>
                        <tr>
                            <td><?= $water_row['NM_DIST_OBSV'] ?></td>
                            <td><?= number_format($water_row['DayMax'] / 10 / 100, 3) ?></td>
                        </tr>
                        <?php }

                        // waterWhile
                        ?>
                    </table>
                    <?php }

                    // 강우
                    if (in_array('01', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 강우
                        <div style="float:right; font-size:10px;">단위 : mm</div>
                    </div>

                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#738bd5;">
                            <th>지역명</th>
                            <th><?= date('m월 d일', strtotime('-2days')) ?></th>
                            <th><?= date('m월 d일', strtotime('-1days')) ?></th>
                            <th><?= date('m월 d일', time()) ?></th>
                            <th>합계</th>
                        </tr>
                        <?php
                        $rain_sql =
                            "select  a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.today, c.yesterday, d.yyesterday from wb_equip as a left join
									(select CD_DIST_OBSV, DaySum as today
									from wb_rain1hour where RegDate = '" .
                            date('Ymd', time()) .
                            "')as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV left join
									(select CD_DIST_OBSV, DaySum as yesterday 
									from wb_rain1hour where RegDate = '" .
                            date('Ymd', strtotime('-1days')) .
                            "')as c on a.CD_DIST_OBSV = c.CD_DIST_OBSV left join
									(select CD_DIST_OBSV, DaySum as yyesterday
									from wb_rain1hour where RegDate = '" .
                            date('Ymd', strtotime('-2days')) .
                            "')as d on a.CD_DIST_OBSV = d.CD_DIST_OBSV
									where GB_OBSV = '01' and USE_YN = '1' order by CD_DIST_OBSV asc";
                        $rain_res = mysqli_query($conn, $rain_sql);
                        while ($rain_row = mysqli_fetch_assoc($rain_res)) { ?>
                        <tr>
                            <td><?= $rain_row['NM_DIST_OBSV'] ?></td>
                            <td>
                                <?php if ($rain_row['yyesterday'] == '') {
                                    echo '-';
                                } else {
                                    echo number_format($rain_row['yyesterday'], 1);
                                } ?>
                            </td>
                            <td>
                                <?php if ($rain_row['yesterday'] == '') {
                                    echo '-';
                                } else {
                                    echo number_format($rain_row['yesterday'], 1);
                                } ?>
                            </td>
                            <td>
                                <?php if ($rain_row['today'] == '') {
                                    echo '-';
                                } else {
                                    echo number_format($rain_row['today'], 1);
                                } ?>
                            </td>
                            <td><?= number_format($rain_row['yyesterday'] + $rain_row['yesterday'] + $rain_row['today'], 1) ?>
                            </td>
                        </tr>
                        <?php }

                        // rainWhile
                        ?>
                    </table>
                    <?php }

                    // 변위
                    if (in_array('03', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 변위
                        <div style="float:right; font-size:10px;">단위 : mm</div>
                    </div>

                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">

                        <colgroup>
                            <col style="width:30%;">
                            <col style="width:10%;">
                            <col style="width:30%;">
                            <col style="width:30%;">
                        </colgroup>
                        <tr style="background-color:#e49479;">
                            <th colspan="2">지역명</th>
                            <th>현재</th>
                            <th>누적</th>
                        </tr>
                        <?php
                        $dplace_sql =
                            "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.DayMax, b.SUB_OBSV
                                        , SUM(
                                            IFNULL(MR1, 0) + IFNULL(MR2, 0) + IFNULL(MR3, 0) + 
                                            IFNULL(MR4, 0) + IFNULL(MR5, 0) + IFNULL(MR6, 0) + 
                                            IFNULL(MR7, 0) + IFNULL(MR8, 0) + IFNULL(MR9, 0) + 
                                            IFNULL(MR10, 0) + IFNULL(MR11, 0) + IFNULL(MR12, 0) + 
                                            IFNULL(MR13, 0) + IFNULL(MR14, 0) + IFNULL(MR15, 0) + 
                                            IFNULL(MR16, 0) + IFNULL(MR17, 0) + IFNULL(MR18, 0) + 
                                            IFNULL(MR19, 0) + IFNULL(MR20, 0) + IFNULL(MR21, 0) + 
                                            IFNULL(MR22, 0) + IFNULL(MR23, 0) + IFNULL(MR24, 0)
                                        ) as DaySum
                                        from wb_equip as a left join wb_dplace1hour as b
                                        on a.CD_DIST_OBSV = b.CD_DIST_OBSV
                                        where GB_OBSV = '03' and USE_YN = '1'
                                        and RegDate = '" .
                            date('Ymd', time()) .
                            "'
                                        group by a.CD_DIST_OBSV
                                        order by a.CD_DIST_OBSV asc, b.SUB_OBSV";
                        $dplace_res = mysqli_query($conn, $dplace_sql);
                        while ($dplace_row = mysqli_fetch_assoc($dplace_res)) { ?>
                        <tr>
                            <td><?= $dplace_row['NM_DIST_OBSV'] ?></td>
                            <td width="10%"><?= $dplace_row['SUB_OBSV'] ?></td>
                            <td><?= number_format($dplace_row['DayMax'], 1) ?></td>
                            <td><?= number_format($dplace_row['DaySum'], 1) ?></td>
                        </tr>
                        <?php }

                        //dplaceWhile
                        ?>
                    </table>
                    <?php }

                    // 함수비
                    if (in_array('04', $gb_obsv)) {
                        $soil_sql =
                            'SELECT NM_DIST_OBSV, ErrorChk, MR' .
                            date('G', time()) .
                            " as now FROM wb_soil1hour as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE GB_OBSV = '04' and USE_YN = '1' and RegDate = '" .
                            date('Ymd', time()) .
                            "'";
                        $soil_res = mysqli_query($conn, $soil_sql);
                        $soil_cnt = mysqli_num_rows($soil_res);

                        if ($soil_cnt > 0) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 함수비율
                        <div style="float:right; font-size:10px;">단위 : %</div>
                    </div>

                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#e49479;">
                            <th>지역명</th>
                            <th>현재</th>
                        </tr>
                        <?php while ($soil_row = mysqli_fetch_assoc($soil_res)) { ?>
                        <tr>
                            <td><?= $soil_row['NM_DIST_OBSV'] ?></td>
                            <td><?= number_format($soil_row['now'], 1) ?></td>
                        </tr>
                        <?php }
                            //soilWhile
                            ?>
                    </table>
                    <?php }
                    }

                    // 경사
                    if (in_array('08', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 경사
                        <div style="float:right; font-size:10px;">단위 : °</div>
                    </div>

                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#e49479;">
                            <th>지역명</th>
                            <th>현재</th>
                        </tr>
                        <?php
                        $tilt_sql =
                            'SELECT NM_DIST_OBSV, ErrorChk, MR' .
                            date('G', time()) .
                            " as now FROM wb_tilt1hour as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE GB_OBSV = '08' and USE_YN = '1' and RegDate = '" .
                            date('Ymd', time()) .
                            "'";
                        $tilt_res = mysqli_query($conn, $tilt_sql);
                        while ($tilt_row = mysqli_fetch_assoc($tilt_res)) { ?>
                        <tr>
                            <td><?= $tilt_row['NM_DIST_OBSV'] ?></td>
                            <td><?= number_format($tilt_row['now'], 1) ?></td>
                        </tr>
                        <?php }

                        //tiltWhile
                        ?>
                    </table>
                    <?php }

                    // 적설
                    if (in_array('06', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 적설
                        <div style="float:right; font-size:10px;">단위 : Cm</div>
                    </div>
                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#46b7b6;">
                            <th>지역명</th>
                            <th>현재</th>
                            <th>누적</th>
                        </tr>
                        <?php
                        $snow_sql =
                            "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.DayMax
                                        , SUM(
                                            IFNULL(MR1, 0) + IFNULL(MR2, 0) + IFNULL(MR3, 0) + 
                                            IFNULL(MR4, 0) + IFNULL(MR5, 0) + IFNULL(MR6, 0) + 
                                            IFNULL(MR7, 0) + IFNULL(MR8, 0) + IFNULL(MR9, 0) + 
                                            IFNULL(MR10, 0) + IFNULL(MR11, 0) + IFNULL(MR12, 0) + 
                                            IFNULL(MR13, 0) + IFNULL(MR14, 0) + IFNULL(MR15, 0) + 
                                            IFNULL(MR16, 0) + IFNULL(MR17, 0) + IFNULL(MR18, 0) + 
                                            IFNULL(MR19, 0) + IFNULL(MR20, 0) + IFNULL(MR21, 0) + 
                                            IFNULL(MR22, 0) + IFNULL(MR23, 0) + IFNULL(MR24, 0)
                                        ) as DaySum
                                        from wb_equip as a left join wb_snow1hour as b
                                        on a.CD_DIST_OBSV = b.CD_DIST_OBSV
                                        where GB_OBSV = '06' and USE_YN = '1'
                                        and RegDate = '" .
                            date('Ymd', time()) .
                            "'
                                        group by a.CD_DIST_OBSV
                                        order by a.CD_DIST_OBSV asc";
                        $snow_res = mysqli_query($conn, $snow_sql);
                        while ($snow_row = mysqli_fetch_assoc($snow_res)) { ?>
                        <tr>
                            <td><?= $snow_row['NM_DIST_OBSV'] ?></td>
                            <td><?= number_format($snow_row['DayMax'], 1) ?></td>
                            <td><?= number_format($snow_row['DaySum'], 1) ?></td>
                        </tr>
                        <?php }

                        //snowWhile
                        ?>
                    </table>
                    <?php }
                    // 침수
                    if (in_array('21', $gb_obsv)) { ?>
                    <div class="cs_kind" style="width:100%"> ◈ 침수
                        <div style="float:right; font-size:10px;">단위 : Cm</div>
                    </div>
                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#ff8184;">
                            <th>지역명</th>
                            <th>현재</th>
                            <th>누적</th>
                        </tr>
                        <?php
                        $flood_sql = "SELECT a.CD_DIST_OBSV, 
                                            a.NM_DIST_OBSV,
                                        COALESCE(b.MR1,b.MR2,b.MR3,b.MR4,b.MR5,b.MR6,b.MR7,b.MR8,b.MR9,b.MR10,b.MR11,b.MR12,b.MR13,b.MR14,b.MR15,b.MR16,b.MR17,b.MR18,b.MR19,b.MR20,b.MR21,b.MR22,b.MR23,b.MR24) AS now,
                                        COALESCE(c.MR1,c.MR2,c.MR3,c.MR4,c.MR5,c.MR6,c.MR7,c.MR8,c.MR9,c.MR10,c.MR11,c.MR12,c.MR13,c.MR14,c.MR15,c.MR16,c.MR17,c.MR18,c.MR19,c.MR20,c.MR21,c.MR22,c.MR23,c.MR24) AS status
                                        FROM wb_equip AS a
                                        LEFT JOIN wb_flood1hour AS c
                                        ON a.CD_DIST_OBSV = c.CD_DIST_OBSV
                                        LEFT JOIN wb_water1hour AS b
                                        ON a.CD_DIST_OBSV = b.CD_DIST_OBSV
                                        WHERE 1=1 AND a.USE_YN = '1' AND a.GB_OBSV='21'
                                        GROUP BY a.CD_DIST_OBSV, a.NM_DIST_OBSV
                                        ORDER BY a.CD_DIST_OBSV ASC";
                        $flood_res = mysqli_query($conn, $flood_sql);
                        while ($flood_row = mysqli_fetch_assoc($flood_res)) { ?>
                        <tr>
                            <td><?= $flood_row['NM_DIST_OBSV'] ?></td>
                            <td>
                                <?php
                                if ($flood_row['status'][0] == '0') {
                                    echo 'X';
                                } else {
                                    echo 'O';
                                }
                                if ($flood_row['status'][1] == '0') {
                                    echo 'X';
                                } else {
                                    echo 'O';
                                }
                                if ($flood_row['status'][2] == '0') {
                                    echo 'X';
                                } else {
                                    echo 'O';
                                }
                                ?>
                            </td>
                            <td><?= number_format($flood_row['now'], 1) ?></td>
                        </tr>
                        <?php }

                        //floodwWhile
                        ?>
                    </table>
                    <?php }
                    ?>

                    <div class="cs_kind" style="width:100%"> ◈ 경보현황 </div>
                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr style="background-color:#5b237c;">
                            <th>지구명</th>
                            <th>경보발령단계</th>
                            <th>발령시간</th>
                            <th>종료시간</th>
                            <th>발생사유</th>
                            <th>상태</th>
                        </tr>
                        <?php
                        $sql = "select a.GCode, a.GName, b.*
								from wb_isualertgroup as a left join wb_isulist as b
								on a.GCode = b.GCode
								order by b.IsuSrtDate desc limit 0,5";
                        $res = mysqli_query($conn, $sql);
                        while ($row = mysqli_fetch_assoc($res)) {
                            $explode = explode(',', $row['Occur']); ?>
                        <tr>
                            <td><?= $row['GName'] ?></td>
                            <td>
                                <?php if ($row['IsuKind'] == 'level1') {
                                    echo '레벨1';
                                } elseif ($row['IsuKind'] == 'level2') {
                                    echo '레벨2';
                                } elseif ($row['IsuKind'] == 'level3') {
                                    echo '레벨3';
                                } elseif ($row['IsuKind'] == 'level4') {
                                    echo '레벨4';
                                } else {
                                    echo '-';
                                } ?>
                            </td>
                            <td><?php if ($row['IsuSrtDate'] == null) {
                                echo '-';
                            } else {
                                echo $row['IsuSrtDate'];
                            } ?></td>
                            <td><?php if ($row['IsuEndDate'] == null) {
                                echo '-';
                            } else {
                                echo $row['IsuEndDate'];
                            } ?></td>
                            <td>
                                <?php for ($i = 0; $i < count($explode); $i++) {
                                    if ($explode[$i] == '02') {
                                        if ($explode[$i] == $explode[0]) {
                                            echo '수위';
                                        } else {
                                            echo ', 수위';
                                        }
                                    } elseif ($explode[$i] == '03') {
                                        if ($explode[$i] == $explode[0]) {
                                            echo '변위';
                                        } else {
                                            echo ', 변위';
                                        }
                                    } elseif ($explode[$i] == '01') {
                                        if ($explode[$i] == $explode[0]) {
                                            echo '강우';
                                        } else {
                                            echo ', 강우';
                                        }
                                    } elseif ($explode[$i] == 'manual') {
                                        if ($explode[$i] == $explode[0]) {
                                            echo '수동제어';
                                        } else {
                                            echo ', 수동제어';
                                        }
                                    } else {
                                        echo '-';
                                    }
                                } ?>
                            </td>
                            <td>
                                <?php if ($row['IStatus'] == 'm-start') {
                                    echo '수동 시작';
                                } elseif ($row['IStatus'] == 'start') {
                                    echo '시작';
                                } elseif ($row['IStatus'] == 'ing') {
                                    echo '발령 중';
                                } elseif ($row['IStatus'] == 'end') {
                                    echo '종료';
                                } else {
                                    echo '대기 중';
                                } ?>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table>

                    <div class="cs_container" style="justify-content:center; margin-top:20px;">
                        <div class="cs_btn" id="id_printBtn">엑셀다운</div>
                        <div class="cs_btn" id="id_refreshBtn">새로고침</div>
                    </div>
                </div> <?php
//container
?>
            </div> <?php
//report
?>
        </div> <?php
//frame
?>
    </div> <?php
//frame_box
?>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/printThis.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/Chart.min.js"></script>
    <script>
    $(document).ready(function(e) {
        $("#id_sDate").datepicker({
            dateFormat: "yy-mm-dd",
            dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
            monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
            showMonthAfterYear: true
        });

        $(document).on("click", "#id_refreshBtn", function() {
            window.location.replace("reportFrame.php");
        });

        $(document).on("click", "#id_printBtn", function() {
            getlog("report", "root/reportFrame.php", "Report Excel Down");
            window.location.href = "reportExcel.php";
            // $(".cs_frame").printThis({
            // 		debug:false,
            // 		importCSS:true,
            // 		printContainer:true,
            // 		loadCSS: "../css/frame.css",
            // 		loadCSS: "../css/include.css",
            // 		pageTitle:"",
            // 		removelnline:false
            // 	});
        });
    });
    </script>
</body>

</html>