<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_GET['num'];

if ($num >= 0) {
    $sql = "select * from wb_parkgategroup where ParkGroupCode = '" . $num . "'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);

    $name = $row['ParkGroupName'];
    $arr = $row['ParkGroupAddr'];
    $gate = explode(',', $row['ParkJoinGate']);
} else {
    $name = '';
    $arr = '';
    $gate = [];
}
?>
<div class="cs_frame">
	<!-- 주차장그룹 관리 (Detail) -->
	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
		<tr>
			<th width="250">주차장그룹 이름</th>
			<td style="text-align:left;padding-left:10px;"><input type="text" style="height:25px;" size="100"
					id="id_title" autocomplete="off" value="<?= $name ?>"></td>
		</tr>
		<tr>
			<th>주차장그룹 주소</th>
			<td style="text-align:left;padding-left:10px;">
				<input type="text" style="height:25px;" size="40" name="addr1" id="id_addr1" autocomplete="off"
					placeholder="주소" value="<?= $arr ?>">
				<input type="text" style="height:25px;" size="20" id="id_addr2" name="addr2" autocomplete="off"
					placeholder="상세주소">
				<input type="button" style="width:80px; height:25px" onclick="sample6_execDaumPostcode()" value="주소 찾기">
			</td>
		</tr>
		<tr>
			<th style="height:auto">주차장그룹 차단기</th>
			<td>
				<div>
					<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows"
						style="box-shadow:none;">
						<tr>
							<th style="width:95px"><input type="checkbox" id="id_allCheck"></th>
							<th>차단기명</th>
							<th>주소</th>
						</tr>
						<?php
      $gateSql = "select * from wb_equip where GB_OBSV = 'LP' and USE_YN = '1' order by CD_DIST_OBSV asc";
      $gateRes = mysqli_query($conn, $gateSql);
      while ($gateRow = mysqli_fetch_assoc($gateRes)) {
          if (in_array($gateRow['CD_DIST_OBSV'], $gate)) {
              $chk = 'checked';
          } else {
              $chk = '';
          } ?>
							<tr>
								<td style="padding:0px"><input type="checkbox" class="cs_gateChk"
										value="<?= $gateRow['CD_DIST_OBSV'] ?>" <?= $chk ?>></td>
								<td><?= $gateRow['NM_DIST_OBSV'] ?></td>
								<td><?= $gateRow['DTL_ADRES'] ?></td>
							</tr>
						<?php
      }
      ?>
					</table>
				</div>
			</td>
		</tr>
	</table>

	<div class="cs_btnBox">
		<?php if ($num < 0) { ?>
			<div class="cs_btn" id="id_addBtn" data-type="insert">등 록</div>
		<?php } else { ?>
			<div class="cs_btn" id="id_addBtn" data-type="update" data-num="<?= $num ?>">수 정</div>
			<div class="cs_btn" id="id_addBtn" data-type="delete" data-num="<?= $num ?>">삭 제</div>
		<?php } ?>
	</div>
</div>