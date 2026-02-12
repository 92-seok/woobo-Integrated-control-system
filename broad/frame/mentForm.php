<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$type = $_GET['type'];

if ($type == 'insert') {
    $title = '';
    $content = '';
    $num = '';
} elseif ($type == 'update') {
    $num = $_GET['num'];

    $sql = "select * from wb_brdment where AltCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($res);
    $title = $row['Title'];
    $content = $row['Content'];
}
?>
<div class="cs_frame">
	<div>◈ 상세내용</div>
	<form action="" method="post" id="saveForm">
		<input type="hidden" name="num" id="id_num" value="<?= $num ?>">
		<input type="hidden" name="beforeContent" id="id_beforeContent" value=<?= $content ?>>
		<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:20px; box-shadow:0px 5px 3px 3px #ebebeb;">
			<tr>
				<th width="10%">제목</th>
				<td><input type="text" name="title" maxlength="50" value="<?= $title ?>" id="id_title" style="width:99%;"></td>
			</tr>
			<tr>
				<th>내용</th>
				<td>
					<textarea name="content" class="cs_content" id="id_content"><?= $content ?></textarea>
				</td>
			</tr>
		</table>
	</form>


    <div class="cs_btnBox">
		<?php if ($type == 'insert') { ?>
			<div class="cs_btn" id="id_mntBtn" data-type="insert">저 장</div>
		<?php } else { ?>
			<div class="cs_btn" id="id_mntBtn" data-type="update">수 정</div>
			<div class="cs_btn" id="id_mntBtn" data-type="delete">삭 제</div>
            <div class="cs_btn" id="" data-type="" onclick="history.back();">뒤로가기</div>
		<?php } ?>
	</div>
</div>