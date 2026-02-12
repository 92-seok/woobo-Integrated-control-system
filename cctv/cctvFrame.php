<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
$KAKAO_API = getenv('KAKAO_API') ?: false;
?>
<!doctype html>
<html lang="KO">
<head>
<title><?= $_SESSION['title'] ?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumsquare.css"/>
<link rel="stylesheet" type="text/css" href="/css/include.css"/>
<link rel="stylesheet" type="text/css" href="/css/frame.css"/>
<link rel="stylesheet" href="/css/cctv.css" type="text/css">
<link rel="shortcut icon" href="/image/favicon.ico">    <!-- ico 파일 -->

<script src="/js/jsmpg.js"></script>
<style>
table,
th,
td {
    border: 1px solid #bcbcbc;
}

.map_list {
    margin-left: 10px;
}

canvas {
    width: 100%;
}
</style>
</head>
<body>
    <script>
    let pType = "cctv";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="modal-overlay"></div>
    <div class="cs_frame_box" id="id_frame_box">
        <div class="cs_frame">
            <div class="cs_cctv">
                <div class="cs_cctv_map" id="cctv_map"></div>
                <div class="cs_cctv_info">
                    <p> ◈ Tip
                        <br>&nbsp;&nbsp;&nbsp;- 현재 설치되어있는 CCTV 위치가 마커로 표시됩니다.
                        <br>&nbsp;&nbsp;&nbsp;- 지도 밖에 위치한 CCTV 마커를 클릭하면 해당 위치로 이동합니다.
                        <br>&nbsp;&nbsp;&nbsp;- 지도 내의 마커를 클릭하여 해당 위치의 CCTV 영상을 확인합니다.
                    </p>
                </div>
                <div class="map_list">
                </div>
            </div>
        </div>
    </div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/printThis.js"></script>
    <script src="/js/cctv.js"></script>
    <script src="/js/cctv-modal.js"></script>
    <!-- Map 호출 -->
    <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=<?= $KAKAO_API ?>&libraries=services"></script>
    <script src="/js/cctv-map.js"></script>
</body>
</html>
