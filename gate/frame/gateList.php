<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

	if(isset($_GET['parkcode'])) {$parkcode = $_GET['parkcode'];} else {$parkcode = '';}

	if(isset($_GET['year1'])) {$year1 = $_GET['year1'];} else {$year1 = date("Y",strtotime("-7days"));}
	if(isset($_GET['month1'])) {$month1 = $_GET['month1'];} else {$month1 = date("m",strtotime("-7days"));}
	if(isset($_GET['day1'])) {$day1 = $_GET['day1'];} else {$day1 = date("d",strtotime("-7days"));}

	if(isset($_GET['year2'])) {$year2 = $_GET['year2'];} else {$year2 = date("Y",time());}
	if(isset($_GET['month2'])) {$month2 = $_GET['month2'];} else {$month2 = date("m",time());}
	if(isset($_GET['day2'])) {$day2 = $_GET['day2'];} else {$day2 = date("d",time());}

	if(isset($_GET['page'])){$page = $_GET['page'];} else {$page = 1;}

	$selectDate1 = "{$year1}-{$month1}-{$day1}";
	$selectDate2 = "{$year2}-{$month2}-{$day2}";

	$gateDao = new WB_GATECONTROL_DAO;
	$equipDao = new WB_EQUIP_DAO;

	$gateVo = $gateDao->SELECT("LEFT(RegDate, 10) BETWEEN '{$selectDate1}' AND '{$selectDate2}'");
	$countRec = count($gateVo);

	$url = "frame/gateList.php?page=";

	$list = 25;
	$block = 20;

	$pageNum = ceil($countRec/$list); // 총 페이지
	$blockNum = ceil($pageNum/$block); // 총 블록
	$nowBlock = ceil($page/$block);

	$s_page = ($nowBlock * $block) - ($block - 1);

	if ($s_page <= 1)
	{
		$s_page = 1;
	}
	$e_page = $nowBlock*$block;
	if ($pageNum <= $e_page)
	{
		$e_page = $pageNum;
	}
?>

<div class="cs_frame"> <!-- 차단기 제어 내역 -->
    <div class="cs_selectBox">
   		<div class="cs_date">
			<form id="id_form" name="form" method="get" action="">
				<input type="hidden" name="arr" value="gateList.php">

				<select name="year1" id="year1">
					<?php
						for( $y = 2020; $y < date("Y", time()) + 1; $y++)
						{
							$year1 == $y ? $selected = "selected" : $selected = "";
							echo "<option value='{$y}' {$selected}>{$y}</option>";
						}
					?>
				</select> 년

				<select name="month1" id="month1">
					<?php
						for( $m = 1; $m <= 12; $m++ )
						{
							$m < 10 ? $date = "0{$m}" : $date = $m;
							$month1 == $date ? $selected = "selected" : $selected = "";

							echo "<option value='{$date}'{$selected}>{$date}</option>";
						}
					?>
				</select> 월

				<select name="day1" id="day1">
					<?php
						for( $d = 1; $d <= 31; $d++ )
						{
							$d < 10 ? $date = "0{$d}" : $date = $d;
							$day1 == $date ? $selected = "selected" : $selected = "";

							echo "<option value='{$date}'{$selected}>{$date}</option>";
						}
					?>
				</select> 일
				~
				<select name="year2" id="year2">
					<?php
						for( $y = 2020; $y < date("Y", time()) + 1; $y++)
						{
							$year2 == $y ? $selected = "selected" : $selected = "";
							echo "<option value='{$y}' {$selected}>{$y}</option>";
						}
					?>
				</select> 년

				<select name="month2" id="month2">
					<?php
						for( $m = 1; $m <= 12; $m++ )
						{
							$m < 10 ? $date = "0{$m}" : $date = $m;
							$month2 == $date ? $selected = "selected" : $selected = "";

							echo "<option value='{$date}'{$selected}>{$date}</option>";
						}
					?>
				</select> 월

				<select name="day2" id="day2">
					<?php
						for( $d = 1; $d <= 31; $d++ )
						{
							$d < 10 ? $date = "0{$d}" : $date = $d;
							$day2 == $date ? $selected = "selected" : $selected = "";

							echo "<option value='{$date}'{$selected}>{$date}</option>";
						}
					?>
				</select> 일

				<input type="hidden" name="mode" value="result">
				<div class="cs_search" id="id_search">검색</div>
			</form>
		</div>
	</div>

   	<table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
    		<th>지역명</th>
            <th>차단기 상태</th>
            <th>현재 상태</th>
            <th>날짜</th>
    	</tr>
        <?php
			$count = ($page-1) * $list;
			$listCnt = $countRec - $count;

			$gateVo = $gateDao->SELECT("LEFT(RegDate, 10) BETWEEN '{$selectDate1}' AND '{$selectDate2}'", "GCtrCode DESC", "{$count},{$list}");
			if( $gateVo[0]->{key($gateVo[0])})
			{
				foreach( $gateVo as $v )
				{
					$equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'");
					echo "<tr>";
						echo "<td>{$equipVo->NM_DIST_OBSV}</td>";
						echo "<td>";
							switch( strtolower($v->Gate) )
							{
								case "close" :
									echo "닫힘";
									break;

								case "open" :
									echo "열림";
									break;

								case "check" :
									echo "상태확인";
									break;

								default :
									echo "-";
									break;
							}
						echo "</td>";
						echo "<td>";
							switch( strtolower($v->GStatus) )
							{
								case "start":
									echo "<font color='#6E6E6E'>동작중</font>";
									break;
								case "ing":
									echo "<font color='#6E6E6E'>동작중</font>";
									break;
								case "end":
									echo "<font color='#2826D4'>동작완료</font>";
									break;
								case "error":
									echo "<font color='#F41E22'>에러</font>";
									break;
								case "fail":
									echo "<font color='#F41E22'>동작실패</font>";
									break;
								default:
									echo "-";
									break;
							}
						echo "</td>";
						echo "<td>{$v->RegDate}</td>";
					echo "</tr>";
				}
			}
		?>
    </table>

		<!-- Pageing block begin -->
		<div class="cs_page">
			<?php if( $page != 1 )
			{
				echo "<div class='cs_pages' id='id_page' data-url='".$url.($page-1)."' data-idx='1'>이전</div>";
			}
			for ($p=$s_page; $p<=$e_page; $p++)
			{
				$act = "";
				if($p == $page) $act = "active";
				echo "<div class='cs_pages ".$act."' id='id_page' data-url='".$url.$p."' data-idx='1'>".$p."</div>";
			}
			if( $page != $pageNum )
			{
				echo "<div class='cs_pages' id='id_page' data-url='".$url.($page+1)."' data-idx='1'>다음</div>";
			}?>
		</div>
		<!-- Pageing block end-->
</div> <?php //frame?>
