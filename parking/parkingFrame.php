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
    <link rel="shortcut icon" href="/image/favicon.ico"> <!-- ico 파일 -->

    <style>
    .cs_datatable tr.active {
        background-color: #5e60cd;
        color: #fff;
    }

    .cs_datatable th {
        background-color: #5e60cd;
    }

    .cs_btn {
        background-color: #5e60cd;
    }

    .cs_btn:hover {
        background-color: #4F509B;
    }

    .cs_imgBox {
        width: 375px;
        height: 315px;
        position: absolute;
        overflow: hidden;
        border: none;
        margin: 0px;
        padding: 0px;
    }
    </style>
</head>

<body>
    <script>
    let pType = "parking";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box"></div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/gate.js"></script>
    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
</body>

</html>