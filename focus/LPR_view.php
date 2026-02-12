<!doctype html>
<html>

<head>
    <title>LPR IN/OUT Event List</title>

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
    let pType = "gate";
    </script>
    <?php $conn = mysqli_connect('127.0.0.1', 'userWooboWeb', 'wooboWeb!@', 'warning');
// 2021.10.26 CarNumber Search 추가
?>

    <div class="cs_frame">
        <!-- 차량 입출차 내역 -->
        <font size="20px">LPR IN/OUT EVENT LIST</font><br>
        <font style="color:#000000;font-size:14px;">*부여 백제교 차단기 serial은 입차:2066 , 출차:2167 이며, 본 목록은 최근 50개만 출력합니다.
        </font>

        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
            <tr>
                <th width="3%"><input type="checkbox" id="id_allCheck" name="allcheck"></th>
                <th width="7%">no</th>
                <th>게이트번호(LPR)</th>
                <th>구분</th>
                <th>차량번호</th>
                <th>시간</th>
            </tr>
            <?php
            $sql = 'select idx, GateDate, GateSerial, CarNum, gatedate,mid(gateSerial,2,1) AS ty from wb_ParkCarHist order by GateDate desc limit 50';
            $res = mysqli_query($conn, $sql);
            while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td style="text-align: center;"><input type="checkbox" class="cs_gateChk" value="<?= $row['idx'] ?>">
                </td>
                <td><?= $row['idx'] ?></td>
                <td class="cs_imgLink" data-url="imgView.php?carnum=<?= $row['CarNum'] ?>&caridx=<?= $row['idx'] ?>"
                    style="cursor:pointer;"><?= $row['GateSerial'] ?></td>
                <td>
                    <?php if ($row['ty'] == '0') {
                        echo '입차';
                    } elseif ($row['ty'] == '1') {
                        echo '출차';
                    } ?>
                </td>
                <!--<td class="cs_imgLink" data-url="imgView.php?carnum=<?= $row['CarNum'] ?>&caridx=<?= $row['idx'] ?>" style="cursor:pointer;"><?= $row['CarNum'] ?></td>-->
                <td class="cs_imgLink" style="cursor:pointer;"><a
                        href="imgView.php?carnum=<?= $row['CarNum'] ?>&caridx=<?= $row['idx'] ?>"><?= $row['CarNum'] ?></a>
                </td>
                <td><?= $row['GateDate'] ?></td>
            </tr>
            <?php }
            ?>
        </table>

    </div>

    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <script src="/js/include.js"></script>
    <script src="/js/gate.js"></script>

</body>

</html>