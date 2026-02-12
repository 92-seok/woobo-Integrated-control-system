<?php
session_start();
if (!isset($_SESSION['userIdx'])) {
    echo "<script>window.location.replace('../login/login.php')</script>";
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
    $dao = new WB_USER_DAO();
    $vo = $dao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
    $auth = $vo->Auth;
}

if ($auth != 'root') {
    echo "<script>alert('접근 권한이 없습니다.')</script>";
    echo "<script>window.location.replace('/main.php')</script>";
}

if (isset($_GET['idx'])) {
    $idx = $_GET['idx'];
} else {
    echo "<script>alert('잘못된 접근입니다.');</script>";
    echo '<script>window.history.back();</script>';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/adminRegist/server/pageName.php';

$dao = new WB_LOG_DAO();
$vo = new WB_LOG_VO();

$vo = $dao->SELECT_SINGLE("idx = {$idx}");
$vo->Page = pageName($vo->Page);
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
    .cs_datatable th {
        color: black;
        background-color: #f9d9ca;
        text-align: center;
        font-size: 18px;
    }

    .cs_datatable td {
        text-align: left;
        font-size: 16px;
        padding-left: 10px;
    }
    </style>
</head>

<body>
    <script>
    let pType = "log";
    </script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box">
        <div class="cs_frame">
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows"
                style="margin-top:10px;">
                <?php
                echo '<tr>';
                echo "<th width='13%'>No</th>";
                echo "<td>{$vo->idx}</td>";
                echo '</tr>';

                echo '<tr>';
                echo "<th width='13%'>날짜</th>";
                echo "<td>{$vo->RegDate}</td>";
                echo '</tr>';

                echo '<tr>';
                echo "<th width='13%'>IP</th>";
                echo "<td>{$vo->ip}</td>";
                echo '</tr>';

                echo '<tr>';
                echo "<th width='13%'>사용자</th>";
                echo "<td>{$vo->userID}</td>";
                echo '</tr>';

                echo '<tr>';
                echo "<th width='13%'>페이지</th>";
                echo '<td>';
                switch ($vo->pType) {
                    case 'data':
                        echo '[데이터]';
                        break;
                    case 'broad':
                        echo '[방송]';
                        break;
                    case 'display':
                        echo '[전광판]';
                        break;
                    case 'gate':
                        echo '[차단기]';
                        break;
                    case 'alert':
                        echo '[임계치]';
                        break;
                    case 'admin':
                        echo '[계정]';
                        break;
                }
                echo "({$vo->Page})";
                echo '</td>';
                echo '</tr>';

                echo '<tr>';
                echo "<th width='13%'>이벤트</th>";
                echo "<td>{$vo->EventType}</td>";
                echo '</tr>';

                echo "<tr height='150px'>";
                echo "<th width='13%'>대상</th>";
                echo '<td>';
                if ($vo->EventType == 'SMS Send') {
                    $data = explode(',', $vo->equip);
                    foreach ($data as $val) {
                        $subSql = "SELECT UName FROM wb_smsuser WHERE GCode = '{$val}'";
                        $subRow = $dao->SELECT_QUERY($subSql);
                        echo "{$subRow['UName']}  ";
                    }
                } else {
                    echo "{$vo->equip}";
                }
                echo '</td>';
                echo '</tr>';

                echo "<tr height='150px'>";
                echo "<th width='13%'>이벤트 발생 전</th>";
                echo '<td>';
                echo "{$vo->EventBefore}";
                echo '</td>';
                echo '</tr>';

                echo "<tr height='150px'>";
                echo "<th width='13%'>이벤트 발생 후</th>";
                echo '<td>';
                echo "{$vo->EventAfter}";
                echo '</td>';
                echo '</tr>';

                echo "<tr height='150px'>";
                echo "<th width='13%'>문구</th>";
                echo "<td>{$vo->EventContent}</td>";
                echo '</tr>';
                ?>
            </table>
        </div>
    </div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
</body>

</html>