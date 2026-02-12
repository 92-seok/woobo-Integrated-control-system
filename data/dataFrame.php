<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['equip'])) {
    $equip = $_GET['equip'];
} else {
    $equip = '';
}
if (isset($_GET['dType'])) {
    $dType = $_GET['dType'];
} else {
    $dType = 'first';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

if ($dType == 'first') {
    $menuKindSql = "SELECT DISTINCT GB_OBSV AS GB FROM wb_equip WHERE USE_YN = '1' ORDER BY GB_OBSV";
    $menuKindRes = mysqli_query($conn, $menuKindSql);
    $i = 0;
    $menuKindArr = [];
    while ($menuKindRow = mysqli_fetch_assoc($menuKindRes)) {
        $menuKindArr[$i++] = $menuKindRow['GB'];
    }

    if ($menuKindArr[0] == '01') {
        $equip = 'rain';
    } elseif ($menuKindArr[0] == '02') {
        $equip = 'water';
    } elseif ($menuKindArr[0] == '03') {
        $equip = 'dplace';
    } elseif ($menuKindArr[0] == '04') {
        $equip = 'soil';
    } elseif ($menuKindArr[0] == '06') {
        $equip = 'snow';
    } elseif ($menuKindArr[0] == '08') {
        $equip = 'tilt';
    } elseif ($menuKindArr[0] == '21') {
        $equip = 'flood';
    } else {
        $equip = 'rain';
    }
    $dType = 'na';
}
?>
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

    <style>
    canvas {
        -moz-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;

        margin-bottom: 60px;
    }

    .cs_datatable th {
        border: none;
    }

    .cs_datatable td {
        border: none;
        border-bottom: 1px solid #cfcfcf;
    }

    select {
        margin-left: auto;
    }
    </style>
</head>

<body>
    <script>
    let pType = "data";
    let type = "<?= $dType ?>";
    let equip = "<?= $equip ?>";
    console.info("type:", type, " | equip:", equip, " | pType:", pType);
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box"></div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/jquery.battatech.excelexport.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/Chart.min.js"></script>
    <script src="/js/data.js"></script>

</body>

</html>