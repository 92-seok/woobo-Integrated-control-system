<!doctype html>
<html>

<head>
    <title>GATE STATUS Event List</title>

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
    <?php $conn = mysqli_connect('127.0.0.1', 'userWooboWeb', 'wooboWeb!@', 'warning');
// 2021.10.26 CarNumber Search 추가
?>

    <div class="cs_frame">
        <!-- 차량 입출차 내역 -->
        <font size="20px">GATE STATUS UPDATE LIST</font><br>
        <font style="color:#000000;font-size:14px;">* 부여 백제교 차단기 serial은 입차: 2066 , 출차:2167 이며, 최종 업데이트시간으로 표출 됩니다.
        </font>
        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
            <tr>
                <th width="3%"><input type="checkbox" id="id_allCheck" name="allcheck"></th>
                <th width="7%">no</th>
                <th>게이트번호(GATE)</th>
                <th>게이트 상태</th>
                <th>업데이트 시간</th>
            </tr>
            <?php
            $sql = "select CD_DIST_OBSV, GATE, dtmUpdate from wb_gatestatus where CD_DIST_OBSV='2066' or CD_DIST_OBSV='2167' order by CD_DIST_OBSV desc";
            $res = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td style="text-align: center;"><input type="checkbox" class="cs_gateChk" value="0"></td>
                <td> </td>
                <td><?= $row['CD_DIST_OBSV'] ?></td>
                <td><?= $row['GATE'] ?></td>
                <td><?= $row['dtmUpdate'] ?></td>
            </tr>
            <?php }
            ?>
        </table>

    </div>

</body>

</html>