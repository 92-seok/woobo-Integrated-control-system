<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

if ($_SESSION['Auth'] > 1) {
    echo "<script>alert('접근 권한이 없습니다.')</script>";
    echo "<script>window.location.replace('/main.php')</script>";
}
?>
<!doctype html>
<html>
<head>
<title><?= $_SESSION['title'] ?></title>

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

<link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumsquare.css" />
<link rel="stylesheet" type="text/css" href="/css/include.css" />
<link rel="stylesheet" type="text/css" href="/css/frame.css" />
<link rel="stylesheet" type="text/css" href="/css/summernote-lite.min.css"/>
<link rel="shortcut icon" href="/image/favicon.ico">	<!-- ico 파일 -->
<style>
	.cs_datatable th 
	{
		background-color:#383838;
	}

	.cs_datatable td
	{
		text-align:center;

	}
	.cs_btn
	{
		background-color:#383838;
	}
	.cs_btn:hover
	{
		background-color:#2B2B2B;
	}
	.cs_useToggle
	{
		width: 30px;
		height: 15px;
		position: relative;

		border: 1px solid #cfcfcf;
		border-radius: 40px;
	}
	.cs_useToggle.on
	{
		background-color: blue;
	}
	.cs_useToggle.off
	{
		background-color: gray;
	}

	.cs_toggleBtn
	{
		height: 11px;
		width: 11px;
		cursor:pointer;
		position: absolute;
		top:2px;

		background-color:#fff;
		border-radius: 13px;
	}
	.cs_toggleBtn.on
	{
		right: 1px;
	}
	.cs_toggleBtn.off
	{
		left: 1px;
	}
</style>
</head>
<body>
	<script> 
	let pType = "admin";
	let sessionIdx = "<?= $_SESSION['userIdx'] ?>";
	</script>

	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

	<div class="cs_frame_box" id="id_frame_box"></div>
   
	<script src="/js/include.js"></script>
	<script src="/js/userAuth.js"></script>
</body>
</html>