<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$equip_dao = new WB_EQUIP_DAO();
$data_dao = new WB_DATADIS_DAO('rain'); // 데이터에 맞춰 클레스 변경

$equip_vo = new WB_EQUIP_VO();
$data_vo = new WB_DATADIS_VO();
$menu_vo = new WB_EQUIP_VO();

$asdao = new WB_ASRECEIVED_DAO();
$asvo = new WB_ASRECEIVED_VO();

$today = new DateTime();
$date = date('Ymd');

/* 강우 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '01' and USE_YN IN ('1', '2')");
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='rainPopup' stat='close'>>&nbsp&nbsp 강우</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='강우 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='01'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable rainPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->rain_now)) {
            $data_vo->rain_now = number_format($data_vo->rain_now, 1);

            echo "<td>{$data_vo->rain_now}</td>";
        } else {
            echo '<td>-</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}
/* 강우 */

/* 수위 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '02' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('water');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='waterPopup' stat='close'>>&nbsp&nbsp 수위</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='수위 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='02'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable waterPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        if ($v->ErrorChk > 0) {
            $status = 'normal';
        } else {
            $status = 'error';
        }

        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->water_now)) {
            $data_vo->water_now = number_format($data_vo->water_now / 1000, 1);

            echo "<td>{$data_vo->water_now}</td> ";
        } else {
            echo '<td>-</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}
/* 수위 */

/* 변위 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '03' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('dplace');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='dplacePopup' stat='close'>>&nbsp&nbsp 변위</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='변위 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='03'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable dplacePopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        for ($i = 1; $i <= $v->SubOBCount; $i++) {
            echo "<tr align='center'>";
            echo "<td><b>{$v->NM_DIST_OBSV}_{$i}</b></td>";

            $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'", $i);
            if (isset($data_vo->dplace_now)) {
                $data_vo->dplace_now = number_format($data_vo->dplace_now, 1);

                echo "<td>{$data_vo->dplace_now}</td>";
            } else {
                echo '<td>-</td>';
            }

            if (strtolower($v->LastStatus) == 'ok') {
                echo "<td><span style='color:blue'>정상</span></td>";
            } else {
                $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
                if (isset($asvo->RegDate)) {
                    $regDate = new DateTime($asvo->RegDate);
                    $interval = $today->diff($regDate);

                    if ($interval->days <= 6) {
                        echo "<td><span style='color:red'>AS접수됨</span></td>";
                    } else {
                        echo "<td><span style='color:red'>오류</span></td>";
                    }
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            }

            echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV},{$i}'>support_agent</span></td>";
            echo '</tr> ';
        }
    }
    echo '</table>';
}
/* 변위 */

/* 함수비율 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '04' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('soil');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='soilPopup' stat='close'>>&nbsp&nbsp 함수비</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='함수비 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='04'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable soilPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->soil_now)) {
            echo "<td>{$data_vo->soil_now}%</td>";
        } else {
            echo '<td>-</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}
/* 함수비율 */

/* 적설 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '06' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('snow');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='snowPopup' stat='close'>>&nbsp&nbsp 적설</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='적설 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='06'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable snowPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->snow_now)) {
            $data_vo->snow_now = number_format($data_vo->snow_now / 10, 1);

            echo "<td>{$data_vo->snow_now}</td>";
        } else {
            echo '<td>-</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}
/* 적설 */

/* 경사 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '08' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('tilt');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='tiltPopup' stat='close'>>&nbsp&nbsp 경사</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='경사 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='08'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable tiltPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>현재값</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->tilt_now)) {
            $data_vo->tilt_now = number_format($data_vo->tilt_now, 2);
            echo "<td>{$data_vo->tilt_now}°</td>";
        } else {
            echo '<td>-</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}
/* 경사 */

/* 침수 */
$equip_vo = $equip_dao->SELECT("GB_OBSV = '21' and USE_YN IN ('1', '2')");
$data_dao = new WB_DATADIS_DAO('flood');
if (isset($equip_vo[0]->CD_DIST_OBSV)) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='floodPopup' stat='close'>>&nbsp&nbsp 침수</div>";
    echo "<div class='material-symbols-outlined autorenew' id='id_refresh' title='침수 계측값 및 상태 새로고침' style='position:absolute;top:1px;right:23px;font-size:20px;display:none;' value='21'>autorenew</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable floodPopup' style='display:none;'>";

    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>침수상태<br/>(침수수위)</th>";
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equip_vo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        $data_vo = $data_dao->SELECT("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}' AND RegDate LIKE '{$date}%'");
        if (isset($data_vo->flood_flood) || isset($data_vo->flood_water)) {
            if ($data_vo->flood_flood != null && $data_vo->flood_water != null) {
                $flood = preg_split('//u', $data_vo->flood_flood, -1, PREG_SPLIT_NO_EMPTY);

                $floodData = ['X', 'X', 'X'];
                if ($flood[0] == '0') {
                    $floodData[0] = 'X';
                } else {
                    $floodData[0] = 'O';
                }

                if ($flood[1] == '0') {
                    $floodData[1] = 'X';
                } else {
                    $floodData[1] = 'O';
                }

                if ($flood[2] == '0') {
                    $floodData[2] = 'X';
                } else {
                    $floodData[2] = 'O';
                }

                $data_vo->flood_water = number_format($data_vo->flood_water / 1000, 1);
                $flood = implode('/', $floodData);

                echo "<td>{$flood}({$data_vo->flood_water})</td>";
            } elseif ($data_vo->flood_flood == null && $data_vo->flood_water != null) {
                $data_vo->flood_water = number_format($data_vo->flood_water / 1000, 1);

                echo "<td>-({$data_vo->flood_water})</td>";
            } elseif ($data_vo->flood_flood != null && $data_vo->flood_water == null) {
                $flood = preg_split('//u', $data_vo->flood_flood, -1, PREG_SPLIT_NO_EMPTY);

                $floodData = ['X', 'X', 'X'];
                if ($flood[0] == '0') {
                    $floodData[0] = 'X';
                } else {
                    $floodData[1] = 'O';
                }

                if ($flood[1] == '0') {
                    $floodData[1] = 'X';
                } else {
                    $floodData[1] = 'O';
                }

                if ($flood[2] == '0') {
                    $floodData[2] = 'X';
                } else {
                    $floodData[2] = 'O';
                }

                $flood = implode('/', $floodData);

                echo "<td>{$flood}(-)</td>";
            } else {
                echo '<td>-(-)</td>';
            }
        } else {
            echo '<td>-(-)</td>';
        }

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if (isset($asvo->RegDate)) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>오류</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>오류</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined support_agent' id='id_as'  title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";
        echo '</tr> ';
    }
    echo '</table>';
}

/* 침수 */
?>
<div style="height:200px;"></div>