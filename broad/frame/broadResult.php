<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$allRecSql = 'select * from wb_brdlist where 1';
$allRecRes = mysqli_query($conn, $allRecSql);
$countRec = mysqli_num_rows($allRecRes);

$url = 'frame/broadresult.php?page=';

$list = 20;
$block = 20;

$pageNum = ceil($countRec / $list); // 총 페이지
$blockNum = ceil($pageNum / $block); // 총 블록
$nowBlock = ceil($page / $block);

$s_page = $nowBlock * $block - ($block - 1);

if ($s_page <= 1) {
    $s_page = 1;
}
$e_page = $nowBlock * $block;
if ($pageNum <= $e_page) {
    $e_page = $pageNum;
}
?>

<div class="cs_frame">
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows"
        style="margin-top:10px; box-shadow:0px 5px 3px 3px #ebebeb;">
        <tr align="center">
            <th width="3%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
            <th width="3%">no</th>
            <th>제목</th>
            <th width="10%">방송종류</th>
            <th width="10%">방송일자</th>
            <th width="5%">처리건수</th>
            <th width="5%">대기</th>
            <th width="5%">성공</th>
            <th width="5%">실패</th>
        </tr>
        <?php
        $count = ($page - 1) * $list;
        $listCnt = $countRec - $count;

        $sql =
            "select *,
						(select count(*) from wb_brdlistdetail where BCode = A.BCode ) as sendCount,
						(select count(*) from wb_brdlistdetail where LOWER(BrdStatus) IN('start', 'ing') and BCode = A.BCode ) as standby,
						(select count(*) from wb_brdlistdetail where LOWER(BrdStatus) IN('end') and BCode = A.BCode ) as success,
						(select count(*) from wb_brdlistdetail where LOWER(BrdStatus) IN('error', 'fail') and BCode = A.BCode ) as fail
				from wb_brdlist as A
				where 1
				order by RegDate desc limit " .
            $count .
            ',' .
            $list;
        $res = mysqli_query($conn, $sql);

        while ($row = mysqli_fetch_assoc($res)) { ?>
        <tr align="center" style="cursor:pointer;">
            <td style="text-align: center;"><input type="checkbox" name="brChk" class="cs_brdChk"
                    value="<?= $row['BCode'] ?>"></td>
            <td><?= $listCnt-- ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>"
                style="text-align: left; padding-left:10px; corsur:pointer;"><?= $row['Title'] ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>">
                <?php if ($row['BType'] == 'general') {
                    echo '일반방송';
                } elseif ($row['BType'] == 'reserve') {
                    echo '예약방송';
                } elseif ($row['BType'] == 'provin') {
                    echo '도청방송';
                } elseif ($row['BType'] == 'kiot') {
                    echo 'KIOT방송';
                } elseif ($row['BType'] == 'level1') {
                    echo '1단계 경보';
                } elseif ($row['BType'] == 'level2') {
                    echo '2단계 경보';
                } elseif ($row['BType'] == 'level3') {
                    echo '3단계 경보';
                } elseif ($row['BType'] == 'level4') {
                    echo '4단계 경보';
                } ?>
            </td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>">
                <?= date('Y-m-d H:i', strtotime($row['BrdDate'])) ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>"><?= $row['sendCount'] ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>"><?= $row['standby'] ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>"><?= $row['success'] ?></td>
            <td id="id_brdList" data-num='<?= $page ?>' value="<?= $row['BCode'] ?>"><?= $row['fail'] ?></td>
        </tr>
        <?php }

// end whild
?>
    </table>

    <div class="cs_page">
        <?php
        if ($page != 1) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='1'>이전</div>";
        }
        for ($p = $s_page; $p <= $e_page; $p++) {
            $act = '';
            if ($p == $page) {
                $act = 'active';
            }
            echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='1'>" . $p . '</div>';
        }
        if ($page != $pageNum) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='1'>다음</div>";
        }
        ?>
    </div>

    <div style="float:right;">
        <div class="cs_btn" id="id_delBtn" data-num="<?= $page ?>" data="result">방송내역 삭제</div>
    </div>
</div>