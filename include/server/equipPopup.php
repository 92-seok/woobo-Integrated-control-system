<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
$equipDao = new WB_EQUIP_DAO();
$equipVo = new WB_EQUIP_VO();

$asdao = new WB_ASRECEIVED_DAO();
$asvo = new WB_ASRECEIVED_VO();
$today = new DateTime();

/* 강우 */
$equipVo = $equipDao->SELECT("GB_OBSV = '01' AND USE_YN IN ('1', '2')");
if (isset($equipVo[0]->{key($equipVo[0])})) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='rainPopup' stat='close'>>&nbsp&nbsp 강우</div>";
    echo "<div class='material-symbols-outlined build' id='id_refresh' title='강우 장비 전체 점검' style='position:absolute;top:0px;right:20px;display:none;' value='01'>build</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable rainPopup' style='display:none;'>";
    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>점검</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equipVo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if ($asvo->{key($asvo)}) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>점검요망</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>점검요망</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined handyman' id='id_check' title='{$v->NM_DIST_OBSV} 장비 점검' value='{$v->CD_DIST_OBSV},{$v->GB_OBSV}'>handyman</span></td>";
        echo "<td><span class='material-symbols-outlined support_agent' id='id_as' title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";

        echo '</tr>';
    }
    echo '</table>';
}
/* 강우 */

/* 방송 */
$equipVo = $equipDao->SELECT("GB_OBSV = '17' AND USE_YN IN ('1', '2')");
if (isset($equipVo[0]->{key($equipVo[0])})) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='broadPopup' stat='close'>>&nbsp&nbsp 방송</div>";
    echo "<div class='material-symbols-outlined build' id='id_refresh' title='방송 장비 전체 점검' style='position:absolute;top:0px;right:20px;display:none;' value='17'>build</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable broadPopup' style='display:none;'>";
    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>점검</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equipVo as $v) {
        echo "<tr align='center'>";

        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

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
                    echo "<td><span style='color:red'>점검요망</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>점검요망</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined handyman' id='id_check' title='{$v->NM_DIST_OBSV} 장비 점검' value='{$v->CD_DIST_OBSV},{$v->GB_OBSV}'>handyman</span></td>";
        echo "<td><span class='material-symbols-outlined support_agent' id='id_as' title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";

        echo '</tr>';
    }
    echo '</table>';
}
/* 방송 */

/* 전광판 */
$equipVo = $equipDao->SELECT("GB_OBSV = '18' AND USE_YN IN ('1', '2')");
if (isset($equipVo[0]->{key($equipVo[0])})) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='displayPopup' stat='close'>>&nbsp&nbsp 전광판</div>";
    echo "<div class='material-symbols-outlined build' id='id_refresh' title='전광판 장비 전체 점검' style='position:absolute;top:0px;right:20px;display:none;' value='18'>build</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable displayPopup' style='display:none;'>";
    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>점검</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equipVo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        if (strtolower($v->LastStatus) == 'ok') {
            echo "<td><span style='color:blue'>정상</span></td>";
        } else {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if ($asvo->{key($asvo)}) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>점검요망</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>점검요망</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined handyman' id='id_check' title='{$v->NM_DIST_OBSV} 장비 점검' value='{$v->CD_DIST_OBSV},{$v->GB_OBSV}'>handyman</span></td>";
        echo "<td><span class='material-symbols-outlined support_agent' id='id_as' title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";

        echo '</tr>';
    }
    echo '</table>';
}
/* 전광판 */

/* 차단기 */
$equipVo = $equipDao->SELECT("GB_OBSV = '20' AND USE_YN IN ('1', '2')");
if (isset($equipVo[0]->{key($equipVo[0])})) {
    echo "<div style='position:relative;'>";
    echo "<div class='cs_pLargeTitle' value='gatePopup' stat='close'>>&nbsp&nbsp 차단기</div>";
    echo "<div class='material-symbols-outlined build' id='id_refresh' title='차단기 장비 전체 점검' style='position:absolute;top:0px;right:20px;display:none;' value='20'>build</div>";
    echo '</div>';

    echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable gatePopup' style='display:none;'>";
    echo "<tr align='center'>";
    echo '<th>장비명</th>';
    echo "<th width='20%'>상태</th>";
    echo "<th width='20%'>점검</th>";
    echo "<th width='20%'>A/S</th>";
    echo '</tr>';

    foreach ($equipVo as $v) {
        echo "<tr align='center'>";
        echo "<td><b>{$v->NM_DIST_OBSV}</b></td>";

        if (strtolower($v->LastStatus) != 'ok') {
            $asvo = $asdao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'", 'RegDate DESC');
            if ($asvo->{key($asvo)}) {
                $regDate = new DateTime($asvo->RegDate);
                $interval = $today->diff($regDate);

                if ($interval->days <= 6) {
                    echo "<td><span style='color:red'>AS접수됨</span></td>";
                } else {
                    echo "<td><span style='color:red'>점검요망</span></td>";
                }
            } else {
                echo "<td><span style='color:red'>점검요망</span></td>";
            }
        } else {
            $gate_dao = new WB_GATESTATUS_DAO();
            $gate_vo = new WB_GATESTATUS_VO();

            $gate_vo = $gate_dao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'");

            if ($gate_vo->{key($gate_vo)}) {
                $gate_vo->Gate = strtoupper($gate_vo->Gate);
                echo "<td><span style='color:blue'>{$gate_vo->Gate}</span></td>";
            } else {
                echo "<td><span style='color:blue'>정상</span></td>";
            }
        }

        echo "<td><span class='material-symbols-outlined handyman' id='id_check' title='{$v->NM_DIST_OBSV} 장비 점검' value='{$v->CD_DIST_OBSV},{$v->GB_OBSV}'>handyman</span></td>";
        echo "<td><span class='material-symbols-outlined support_agent' id='id_as' title='{$v->NM_DIST_OBSV} 장비 A/S 접수' value='{$v->CD_DIST_OBSV}'>support_agent</span></td>";

        echo '</tr>';
    }
    echo '</table>';
}

/* 차단기 */
?>
<div style="height:200px;"></div>