<?php
session_start();
if (!isset($_SESSION['userIdx'])) {
    echo "<script>window.location.replace('../login/login.php')</script>";
} else {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
    $dao = new WB_USER_DAO();
    $vo = $dao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
    $auth = $vo->Auth;
}

if ($auth != 'root') {
    echo "<script>alert('접근 권한이 없습니다.')</script>";
    echo "<script>window.location.replace('/main.php')</script>";
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['chk'])) {
    $chk = $_GET['chk'];
} else {
    $chk = 'all';
}
if (isset($_GET['move'])) {
    $move = $_GET['move'];
} else {
    $move = '0';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/adminRegist/server/pageName.php';

$logDao = new WB_LOG_DAO();
$logVo = new WB_LOG_VO();

if ($chk == 'all') {
    if ($move == '1') {
        $where = '1';
    } else {
        $where = "EventType != 'Move'";
    }
} else {
    if ($move == '1') {
        $where = "pType = '{$chk}'";
    } else {
        $where = "pType = '{$chk}' AND EventType != 'Move'";
    }
}

$logVo = $logDao->SELECT($where);

if (isset($logVo[0]->{key($logVo[0])})) {
    $countRec = count($logVo);
} else {
    $countRec = 0;
}

$list = 25;
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

$count = ($page - 1) * $list;
$listCnt = $countRec - $count;
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
<link rel="shortcut icon" href="/image/favicon.ico">	<!-- ico 파일 -->
<style>
	table th
	{
		background-color:#f9d9ca;
	}

	#select
	{
		width: 100px;
		height: 30px;
		font-size: 16px;
		margin-bottom: 25px;
	}
</style>
</head>
<body>
	<script> 
	let pType = "log"; 
	</script>

    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/menu.php'; ?>
    <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/top_sub.php'; ?>
	<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/popup.php'; ?>

    <div class="cs_frame_box" id="id_frame_box">
		<div class="cs_frame">
			<div class="cs_date" style="float:right;">
				<select name="type" id="id_pTypeSelect">
					<option value="all" <?php if ($chk == 'all') {
         echo 'selected';
     } ?>>전체</option>
					<?php
     $typeArr = [];
     $res = $logDao->SELECT_QUERY('SELECT DISTINCT pType FROM wb_log');
     foreach ($res as $v) {
         array_push($typeArr, $v['pType']);
     }

     if (in_array('data', $typeArr)) {
         echo "<option value='data' " . ($chk == 'data' ? 'selected' : '') . '>데이터</option>';
     }
     if (in_array('broad', $typeArr)) {
         echo "<option value='broad' " . ($chk == 'broad' ? 'selected' : '') . '>방송</option>';
     }
     if (in_array('display', $typeArr)) {
         echo "<option value='display' " . ($chk == 'display' ? 'selected' : '') . '>전광판</option>';
     }
     if (in_array('gate', $typeArr)) {
         echo "<option value='gate' " . ($chk == 'gate' ? 'selected' : '') . '>차단기</option>';
     }
     if (in_array('alert', $typeArr)) {
         echo "<option value='alert' " . ($chk == 'alert' ? 'selected' : '') . '>임계치</option>';
     }
     if (in_array('admin', $typeArr)) {
         echo "<option value='admin' " . ($chk == 'admin' ? 'selected' : '') . '>계정</option>';
     }
     ?>
				</select>
				<input type="checkbox" id="id_moveSelect" name="move" <?php if ($move == '1') {
        echo 'checked';
    } ?>>페이지 이동 포함
			</div>

			<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:10px;">
				<tr align="center"> 
					<th width="3%">no</th>
					<th>RegDate</th>
					<th>IP(ID)</th>
					<th>Page</th>
					<th>Event</th>
				</tr>
				<?php
    $logVo = $logDao->SELECT($where, 'idx DESC', "{$count},{$list}");
    if (isset($logVo[0]->{key($logVo[0])})) {
        foreach ($logVo as $v) {
            echo "<tr class='cs_trList' style='cursor:pointer' data-idx='{$v->idx}'>";
            echo "<td>{$v->idx}</td>";
            echo "<td>{$v->RegDate}</td>";
            echo "<td>{$v->ip}({$v->userID})</td>";
            echo '<td>';
            switch ($v->pType) {
                case 'data':
                    echo '[데이터]';
                    break;
                case 'broad':
                    echo '[방송]';
                    break;
                case 'display':
                    echo '[전광판]';
                    break;
                case 'gate':
                    echo '[차단기]';
                    break;
                case 'alert':
                    echo '[임계치]';
                    break;
                case 'admin':
                    echo '[계정]';
                    break;
                case 'equip':
                    echo '[장비]';
                    break;
            }
            echo pageName($v->Page);
            echo '</td>';
            echo "<td>{$v->EventType}</td>";
            echo '</tr>';
        }
    }
    ?>
			</table>

			<div class="cs_page">
				<?php
    if ($page != 1) {
        echo "<div class='cs_pages' id='id_page' data-idx='" . ($page - 1) . "'>이전</div>";
    }
    for ($p = $s_page; $p <= $e_page; $p++) {
        $act = '';
        if ($p == $page) {
            $act = 'active';
        }
        echo "<div class='cs_pages {$act}' id='id_page' data-idx='{$p}'>" . $p . '</div>';
    }
    if ($page != $pageNum) {
        echo "<div class='cs_pages' id='id_page' data-idx='" . ($page + 1) . "'>다음</div>";
    }
    ?>
				</div>
			</div>
		</div>
	</div>
	
  	<!-- <script src="/js/jquery-1.9.1.js"></script> -->
   	<script src="/js/include.js"></script>
	<script>
		window.onload = () =>
		{
			let page = "<?= $page ?>";
			let chk = "<?= $chk ?>";
			let move = "<?= $move ?>";
			let listE = document.querySelectorAll(".cs_pages");
			listE.forEach((el) => 
			{
				el.addEventListener("click", (e) =>
				{
					page = e.target.attributes["data-idx"].value;
					window.location.href = `logFrame.php?page=${page}&chk=${chk}&move=${move}`;
				})
			})

			listE = document.querySelectorAll(".cs_trList");
			listE.forEach((el) => 
			{
				el.addEventListener("click", (e) => 
				{
					let idx = el.attributes["data-idx"].value;
					window.location.href = `logDetail.php?page=${page}&chk=${chk}&move=${move}&idx=${idx}`;
				})
			})

			listE = document.querySelector("#id_moveSelect");
			listE.addEventListener("click", (e) => 
			{
				if( e.target.checked ) move = "1";
				else move = "0";

				window.location.href = `logFrame.php?chk=${chk}&move=${move}`;
			})

			listE = document.querySelector("#id_pTypeSelect");
			listE.addEventListener("change", () => 
			{
				chk = listE.value;
				window.location.href = `logFrame.php?chk=${chk}`;
			})
		};
	</script>
</body>
</html>
