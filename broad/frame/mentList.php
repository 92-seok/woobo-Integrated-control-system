<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$allRecSql = "select * from wb_brdment where BUse = 'ON'";
$allRecRes = mysqli_query($conn, $allRecSql);
$countRec = mysqli_num_rows($allRecRes);

$url = $_SERVER['PHP_SELF'] . '?page=';

$list = 10;
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
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:20px; box-shadow:0px 5px 3px 3px #ebebeb;">
        <tr align="center">
            <th width="3%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
            <th width="3%">no</th>
            <th width="20%">제목</th>
            <th>내용</th>
        </tr>
        <?php
        $s_point = ($page - 1) * $list;
        $sql = "select * from wb_brdment where BUse = 'ON' order by AltCode desc limit " . $s_point . ',' . $list;
        $res = mysqli_query($conn, $sql);

        $count = ($page - 1) * $list;
        $listCnt = $countRec - $count;

        while ($row = mysqli_fetch_assoc($res)) { ?>
        <tr align="center">
            <td style="text-align: center;"><input type="checkbox" name="brChk" class="cs_brdChk" value="<?= $row['AltCode'] ?>"></td>
            <td><?= $listCnt-- ?></td>
            <td id="id_mntList" value="<?= $row['AltCode'] ?>" style ="cursor:pointer;"><?= $row['Title'] ?></td>
            <td id="id_mntList" value="<?= $row['AltCode'] ?>" style ="cursor:pointer;"><?= nl2br($row['Content']) ?></td>
        </tr>
        <?php }

// end whild
?>
    </table>

    <div class="cs_page">
        <?php
        if ($page != 1) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='3'>이전</div>";
        }
        for ($p = $s_page; $p <= $e_page; $p++) {
            $act = '';
            if ($p == $page) {
                $act = 'active';
            }
            echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='3'>" . $p . '</div>';
        }
        if ($page != $pageNum) {
            echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='3'>다음</div>";
        }
        ?>
    </div>

    <div class="cs_btnBox" style="width:100%; text-align: center;display:flex;justify-content: flex-end;">
        <div class="cs_btn" id="id_addmntBtn">멘트 추가</div>
        <div class="cs_btn" id="id_mntBtn" data-type="mdelete">멘트 삭제</div>
    </div>
    <div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
            - 자주 사용하는 방송멘트를 추가합니다.<br/>
            - 추가한 멘트는 ‘방송하기’ - ‘방송종류’ - ‘TTS방송’클릭시, 확인할 수 있습니다.
        </div>
    </div>
</div>