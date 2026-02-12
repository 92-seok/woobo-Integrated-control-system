<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php'; ?>
	<div class='cs_SubTitle' style="padding:10px;">
		<input type='checkbox' id='id_autoAlertChk' checked> 1분 간격 자동 점검 <font id='id_alertCount'>(59)</font>
	</div>

	<div class='cs_alertPart'>
		<div class='cs_pLargeTitle' value='chkAlert' stat='open' style=''>∨&nbsp&nbsp 자동점검현황(실시간)</div>
		<div class='cs_alertPopup' id='chkAlert' style='height:200px;margin-bottom:0px;'>
			<div id="id_alertContainer" style="margin-top:15px;"></div>
		</div>

		<div class='cs_pLargeTitle' value='chkResult' stat='open' style=''>∨&nbsp&nbsp 자동점검 특이사항</div>
		<div class='cs_alertPopup' id='chkResult'>
			<div id="id_resultContainer" style="font-size:13px;"></div>
		</div>
	</div>
