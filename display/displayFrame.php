<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
// if( $_SESSION["Auth"] > 1 )
// {
// 	echo "<script>alert('접근 권한이 없습니다.')</script>";
// 	echo "<script>window.location.replace('/main.php')</script>";
// }
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
    <link rel="stylesheet" type="text/css" href="/css/summernote-lite.min.css" />
    <link rel="shortcut icon" href="/image/favicon.ico"> <!-- ico 파일 -->
    <style>
    .cs_datatable th {
        background-color: #5490d9;
    }

    .cs_datatable td {
        height: 25px;
    }

    .cs_btn {
        background-color: #5490d9;
    }

    .cs_btn:hover {
        background-color: #5c86b9;
    }

    .cs_datatable .cs_btn {
        float: none;
        margin: auto;
    }

    p {
        margin: 0px !important;
    }

    tbody select {
        width: 70%;
        text-align: left;
        margin-left: 0px;
    }
    </style>
</head>

<body>
    <script>
    let pType = "display";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box"></div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/display.js"></script>
    <script src="/js/jquery-ui.js"></script>
    <script src="/js/html2canvas.js"></script>

</body>

</html>