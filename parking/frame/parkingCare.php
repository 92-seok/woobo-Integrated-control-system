<?php 
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/dbconn.php";

	if(isset($_GET['page'])){$page = $_GET['page'];} else {$page = 1;}
	
	$allRecSql = "select * from wb_parkgategroup order by ParkGroupCode desc";
	$allRecRes = mysqli_query($conn, $allRecSql);
	$countRec = mysqli_num_rows($allRecRes);
	
	$url = "frame/parkingCare.php?page=";

	$list = 10;
	$block = 10;
	
	$pageNum = ceil($countRec/$list);
	$blockNum = ceil($pageNum/$block);
	$nowBlock = ceil($page/$block);
	
	$s_page = ($nowBlock * $block) - ($block - 1);
	
	if($s_page <= 1) {
		$s_page = 1;	
	}
	
	$e_page = $nowBlock * $block;
	
	if($pageNum <= $e_page) {
		$e_page = $pageNum;	
	}
?>
<style>
	.table tr:hover {
		background:#D9D9EC;
		cursor:pointer;
	}
</style>
<body>
<div class="cs_frame"> <!-- 주차장그룹 관리 -->
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
        	<th width="3%">no</th>
        	<th>주차장</th>
            <th>주소</th>
            <th>차단기</th>
        </tr>
        <?php 
			$s_point = ($page - 1) * $list;		
			$sql = mysqli_query($conn, "select * from wb_parkgategroup order by ParkGroupCode asc limit $s_point,$list");
			$count = ($page - 1) * $list;
			while($row = mysqli_fetch_assoc($sql)) {
		?>
        <tr id="id_grpList" data-num = "<?=$row['ParkGroupCode']?>" style="cursor:pointer;">
        	<td><?=++$count?></td>
        	<td><?=$row['ParkGroupName']?></td>
            <td><?=$row['ParkGroupAddr']?></td>
            <td><?=$row['ParkJoinGate']?></td>
        </tr>
        <?php } ?>
    </table>
    
	<div class="cs_page">
		<?php if( $page != 1 )
		{
			echo "<div class='cs_pages' id='id_page' data-url='".$url.($page-1)."' data-idx='0'>이전</div>";
		} 
		for ($p=$s_page; $p<=$e_page; $p++) 
		{
			$act = "";
			if($p == $page) $act = "active";
			echo "<div class='cs_pages ".$act."' id='id_page' data-url='".$url.$p."' data-idx='0'>".$p."</div>";
		}
		if( $page != $pageNum )
		{
			echo "<div class='cs_pages' id='id_page' data-url='".$url.($page+1)."' data-idx='0'>다음</div>";
		}?>
	</div>
     
     <div class="cs_btnBox" style="justify-content:flex-end;">
     	<div class="cs_btn" id="id_grpList" data-num="-1">추 가</div>
     </div>
</div> <?php //frame?>