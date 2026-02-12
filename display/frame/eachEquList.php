<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/const.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';

$dao = new WB_EQUIP_DAO();
$vo = $dao->SELECT("GB_OBSV = '18' AND USE_YN IN ('1', 'Y')");
?>

<div class="cs_frame">
<div class="cs_container"> 
<div class="cs_displaybox">

	<h4 style='margin-top:20px;'>◈ 전광판 목록</h4>
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
		<tr align="center"> 
			<th width="3%">no</th>
			<?php if ($_SESSION['Auth'] === 0): ?>
				<th class="cs_admin" width="5%">장비ID</th>
				<th class="cs_admin" width="5%">구분</th>
				<th class="cs_admin" width="15%">로거모델</th>
			<?php endif; ?>
			<th width="15%">장비명</th>
            <th>설치지역</th>
            <th width="15%">사이즈</th>
			<th width="15%">IP(Port)</th>
			<th width="15%">전원상태</th>
		</tr>
		<?php $count = 1; ?>
		<?php foreach ($vo as $v) { ?>
		<tr id='id_disList' onclick='eachScenDetail("<?= $v->CD_DIST_OBSV ?>")' style='cursor:pointer;'>
			<td><?= $count++ ?></td>
			<?php if ($_SESSION['Auth'] === 0): ?>
				<td class="cs_admin"><?= $v->CD_DIST_OBSV ?></td>
				<td class="cs_admin"><?= id2gb($v->GB_OBSV) ?></td>
				<td class="cs_admin"><?= $v->ConnModel ?></td>
			<?php endif; ?>
			<td><?= $v->NM_DIST_OBSV ?></td>
            <td><?= $v->DTL_ADRES ?></td>
            <td><?= $v->SizeX . ' x ' . $v->SizeY ?></td>
			<td><?= $v->ConnIP . ' : ' . $v->ConnPort ?></td>
			<td>
			<?php if (strtolower($v->LastStatus) == 'ok'): ?>
				<span style='color:blue'>정상</span>
			<?php else: ?>
				<span style='color:red'>점검요망</span>
			<?php endif; ?>
			</td>
		</tr>
		<?php } ?>
	</table>

</div>
</div>
</div>
