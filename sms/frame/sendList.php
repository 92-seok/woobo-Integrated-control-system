<?php
	include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbconn.php";
if( isset($_GET['page']) ){  $page = $_GET['page']; }else{ $page = 1; }

$allRecSql = "select * from wb_smslist where 1";
$allRecRes = mysqli_query( $conn, $allRecSql );
$countRec = mysqli_num_rows( $allRecRes );

$url = "frame/sendList.php?page=";

$list = 10;
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
<div class="cs_frame"> <!-- 발송내역 -->
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
			<th width="3%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
        	<th width="3%">no</th>
            <th>제목</th>
            <th width="23%">발송시간</th>
            <th width="8%">처리건수</th>
            <th width="8%">성공</th>
            <th width="8%">실패</th>
        </tr>
        <?php
		$count = ($page - 1) * $list;
		$listCnt = $countRec - $count;
		$sql = mysqli_query($conn, "select *,
									(select count(*) from wb_sendmessage where (SendStatus = 'OK') and SCode = a.SCode) as okcount,
									(select count(*) from wb_sendmessage where (SendStatus = 'Error') and SCode = a.SCode) as failcount
									from wb_smslist as a
									order by SCode desc limit $count,$list");
		while($row = mysqli_fetch_assoc($sql))
		{
			$user = explode(",", $row["GCode"]);
		?>
        <tr style="cursor:pointer">
			<td style="text-align: center;"><input type="checkbox" name="smsChk" class="cs_smsChk" value="<?=$row["SCode"] ?>"></td>
        	<td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=$listCnt--?></td>
            <td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=$row['SMSTitle']?></td>
            <td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=$row['SMSDate']?></td>
            <td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=count($user) ?></td>
            <td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=$row['okcount']?></td>
            <td id="id_smsList" data-num="<?=$row['SCode']?>" data-type="sms"><?=$row['failcount']?></td>
        </tr>
        <?php } ?>
    </table>

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

	<div style="float: right;">
		<div class="cs_btn" id="id_delBtn" data-num="<?=$page?>" data="result">발송내역 삭제</div>
	</div>

</div> <?php //frame?>