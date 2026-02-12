<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if ($_SESSION['Auth'] > 1) {
    echo "<script>alert('접근 권한이 없습니다.')</script>";
    echo "<script>window.location.replace('/main.php')</script>";
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
    <link rel="stylesheet" type="text/css" href="/css/summernote-lite.min.css" />
    <link rel="shortcut icon" href="/image/favicon.ico"> <!-- ico 파일 -->
    <style>
    .cs_datatable th {
        background-color: #5b237c;
    }

    .cs_btn {
        background-color: #5b237c;
        margin: 30px;
        min-height: 35px;
        align-content: center;
        border-radius: 10px;
    }

    .cs_btn:hover {
        background-color: #42185b;
    }

    .cs_blockBox {
        height: 100%;
        width: 100%;
        position: absolute;
        line-height: 3;
        font-size: 50px;
        text-align: center;
        color: #fff;
        background-color: rgba(0, 0, 0, 0.4);
        display: none;
    }
    </style>
</head>

<body>
    <script>
    let pType = "alert";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box"></div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/alert.js"></script>
    <script src="/js/html2canvas.js"></script>
</body>

</html>
