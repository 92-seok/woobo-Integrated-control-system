<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

if (isset($_GET['sec'])) {
    $sec = $_GET['sec'];
} else {
    $sec = 30;
}

if (isset($_GET['val'])) {
    $val = $_GET['val'];
} else {
    echo '<script>';
    echo "alert('잘못된 접근 입니다.');";
    echo 'window.close();';
    echo '</script>';
}

$arr = explode(',', $val);

if ($arr[1] == '01') {
    $sequence = "100,{$arr[1]}";
} else {
    if ($arr[1] == '17') {
        $dao = new WB_BRDSEND_DAO();
        $vo = new WB_BRDSEND_VO();

        $vo->CD_DIST_OBSV = $arr[0];
        $vo->RCMD = 'S170';
        $vo->Parm1 = '00000000';
        $vo->Parm2 = '1';
        $vo->Parm3 = '점검중입니다.';
        $vo->Parm4 = '0';
        $vo->BStatus = 'start';
        $vo->RegDate = date('Y-m-d H:i:s');
    } elseif ($arr[1] == '18') {
        $dao = new WB_DISSEND_DAO();
        $vo = new WB_DISSEND_VO();

        $vo->CD_DIST_OBSV = $arr[0];
        $vo->RCMD = 'S010';
        $vo->RegDate = date('Y-m-d H:i:s');
        $vo->BStatus = 'start';
    } elseif ($arr[1] == '20') {
        $dao = new WB_GATECONTROL_DAO();
        $vo = new WB_GATECONTROL_VO();

        $vo->CD_DIST_OBSV = $arr[0];
        $vo->Gate = 'check';
        $vo->GStatus = 'start';
        $vo->RegDate = date('Y-m-d H:i:s');
    }
    $dao->INSERT($vo);
    $insertId = $dao->INSERTID();
    $sequence = "{$insertId},{$arr[1]}";
}
?>

<!doctype html>
<html>
<head>
	<title>장비 점검</title>
	<link rel="stylesheet" type="text/css" href="/css/include.css" />
	<style>
		#closeBtn
		{
			width: 60px;
			height: 30px;
			margin: 15px auto;
			text-align: center;
			background-color: #5fbaef;
			color: #fff;
			line-height: 1.8;
			cursor: pointer;
			display:none;
		}
		#closeBtn:hover
		{
			background-color: #738bd5;
		}
		#id_second
		{
			position:absolute;
			top:83px;
			left:187px;

		}
	</style>
</head>
<body style="font-size:16px;text-align:center;">
	<div class="cs_equipIcon">
		<div class='material-symbols-outlined settingA'>settings</div>
		<div class='material-symbols-outlined settingB'>settings</div>
		<span id="id_second">(<?= $sec ?>)</span>
		<div class='material-symbols-outlined mood' style="font-size:70px;display:none;">mood</div>
		<div class='material-symbols-outlined sentiment_very_dissatisfied' style="font-size:70px;display:none;">sentiment_very_dissatisfied</div>
	</div>
	<div id='ingMessage' style="font-weight:bold;">장비를 점검하고 있습니다.</div>
	<div id='closeMessage' style="margin-top:15px;">진행 중에 창을 종료하지말고<br/>잠시만 기다려주세요.</div>
	<div id='listMessage' style="margin-top:15px;display:none;"></div>
	<div id="closeBtn">닫기</div>
<script>
	var sec = <?= $sec ?>;
	var sequence = "<?= $sequence ?>";

	document.addEventListener("DOMContentLoaded", ()=>
	{
		checksetting();
		document.querySelector("#closeBtn").addEventListener("click", ()=>{ window.close(); });
	});

	function success()
	{
		document.querySelector("#ingMessage").innerText = "장비 점검 완료";
		document.querySelector("#closeMessage").innerText = "모든 장비가 정상입니다.";

		document.querySelector(".settingA").remove();
		document.querySelector(".settingB").remove();
		document.querySelector("#id_second").remove();
		document.querySelector(".mood").style.display = "block";
		document.querySelector("#closeBtn").style.display = "block";
	}

	function fail(name)
	{
		document.querySelector("#ingMessage").innerText = "장비 점검 완료";
		document.querySelector("#closeMessage").innerHTML = "오류발생!<br/>A/S 접수 바랍니다.";
		document.querySelector("#listMessage").innerHTML = `[오류 장비 목록]<br/>${name}`;

		resizeTo(window.outerWidth, 800);
		document.querySelector(".settingA").remove();
		document.querySelector(".settingB").remove();
		document.querySelector("#id_second").remove();
		document.querySelector("#listMessage").style.display = "block";
		document.querySelector(".sentiment_very_dissatisfied").style.display = "block";
		document.querySelector("#closeBtn").style.display = "block";
	}

	function checksetting()
	{
		let ajax = new XMLHttpRequest();
		
		let url = `/include/server/equipsettingcheck.php?type=equip&sequence=${sequence}&sec=${sec}`;
		url = "/cgi-bin/getLoggerStatus?ip=192.168.83.66&port=4096&model=EWRAIN_LAN";

		ajax.open('GET', url);
		ajax.setRequestHeader('content-type', 'application/json');
		ajax.responseType = 'json';
		ajax.send();

		ajax.onload = () => 
		{
			if( ajax.status === 200 )
			{
				let data = ajax.response;
				console.log("dsdss", data);
				sec -= 1;

				if( data.stat == "ing" ) 
				{
					setTimeout(checksetting, 0.7 * 1000);
					document.querySelector("#id_second").innerText = `( ${sec} )`;
				}
				else if( data.stat == "success" ) success();
				else if ( data.stat == "fail" ) fail(data.name);
				else if ( data.stat == "error" ) fail(data.name);
				else console.log(data);
			}
			else
			{
				console.log("error");
				fail("네트워크 상태 오류");
			}
		};
	}
</script>
</body>
</html>