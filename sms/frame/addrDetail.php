<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_SMSUSER_DAO();
$vo = new WB_SMSUSER_VO();

$vo->GCode = $_GET['num'];

if ($vo->GCode != -1) {
    $vo = $dao->SELECT_SINGLE("GCode='{$vo->GCode}'");
}
?>
<div class="cs_frame"> <!-- 연락처관리 (Detail) -->
	<form name="form" id="id_form" method="get" action="">
		<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all"> 
			<tr>	
				<th width="15%">부서명</th>
				<td width="35%" style="text-align:left; padding-left:10px;"><input type="text" name="departments" maxlength='30' value="<?= $vo->Division ?>" style="height:25px; width:70%;"></td>
				<th width="15%">별칭</th>
				<td width="35%" style="text-align:left; padding-left:10px;"><input type="text" name="name" maxlength='30' value="<?= $vo->UName ?>" style="height:25px; width:70%;"></td>
			</tr>
			<tr>
				<th width="15%">직책</th>
				<td width="35%" style="text-align:left; padding-left:10px;"><input type="text" name="position" maxlength='30' value="<?= $vo->UPosition ?>" style="height:25px; width:70%;"></td>
				<th width="15%">이동전화번호</th>
				<td width="35%" style="text-align:left; padding-left:10px;"><input type="text" name="phone" maxlength='30' value="<?= $vo->Phone ?>" style="height:25px; width:70%;"></td>
			</tr>
		</table>
	</form>
	<div class="cs_btnBox">
		<?php if ($vo->GCode != -1) {
      echo "<div class='cs_btn' id='id_addrBtn' data-num='{$vo->GCode}' data-type='update'>수 정</div>";
      echo "<div class='cs_btn' id='id_addrBtn' data-num='{$vo->GCode}' data-type='delete'>삭 제</div>";
  } else {
      echo "<div class='cs_btn' id='id_addrBtn' data-num='{$vo->GCode}' data-type='insert'>추 가</div>";
  } ?>
	</div>
</div>