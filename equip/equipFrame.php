<?php
session_start();

if (!isset($_SESSION['userIdx'])) {
    echo '<script>';
    echo "alert('세션이 만료되었습니다.')";
    echo "window.location.replace('../login/login.php')";
    echo '</script>';
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
    $dao = new WB_USER_DAO();
    $vo = $dao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
    $auth = $vo->Auth;
}

if ($_SESSION['Auth'] > 0) {
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
    <link rel="stylesheet" type="text/css" href="/css/equip.css" />
    <link rel="shortcut icon" href="/image/favicon.ico"> <!-- ico 파일 -->
    <style>
    .cs_datatable th {
        color: #000;
        background-color: #f9d9ca;
    }

    .cs_subTitle {
        text-align: left;
        padding-left: 10px;
    }

    .cs_select {
        width: 95%;
    }

    .cs_btnGroup {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
    }

    .cs_btn {
        color: #000;
        background-color: #f9d9ca;
        /* width:24%; */
        width: 150px;
    }

    .tBtn {
        font-size: 12px;
        text-align: center;
        font-weight: bold;
        cursor: pointer;
        color: #000;
        background-color: #f9d9ca;
    }

    .cs_btn:hover,
    .tBtn:hover {
        background-color: #D18063;
    }
    </style>
</head>

<body>
    <script>
    let pType = "equip";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box"></div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/equip.js"></script>
</body>

</html>