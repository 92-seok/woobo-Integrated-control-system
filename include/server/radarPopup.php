<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

echo "<div class='cs_radarPart'>";

$satDao = new KMA_SATELLITE_DAO();
$satVo = $satDao->SELECT_SAT() ?? [];
$satimg = '';
if (!empty($satVo)) {
    $satimg = $satVo[0]->filename ?? '';
}

if (!empty($satimg)) {
    echo "<div class='cs_pLargeTitle' value='satimg' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 위성영상</div>";
    echo "<div class='cs_radarImage' id='satimg' style='background-image:url(/files/radar/{$satimg});'></div>";
}

$satDao = new KMA_SATELLITE_DAO();
$satVo = $satDao->SELECT_RDR() ?? [];
$rdrimg = '';
if (!empty($satVo)) {
    $rdrimg = $satVo[0]->filename ?? '';
}

if (!empty($rdrimg)) {
    echo "<div class='cs_pLargeTitle' value='rdrimg' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 레이더영상</div>";
    echo "<div class='cs_radarImage' id='rdrimg' style='background-image:url(/files/radar/{$rdrimg});'></div>";
}

echo '</div>';
