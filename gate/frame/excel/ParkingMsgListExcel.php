<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
<?php
header('Content-type:application/vnd.ms-excel; charset=utr-8');
header('Content-Disposition:attachment;filename=ParkingMsgList_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
include '../../../include/dbconn.php';
$year1 = $_GET['year1'];
$month1 = $_GET['month1'];
$day1 = $_GET['day1'];
$year2 = $_GET['year2'];
$month2 = $_GET['month2'];
$day2 = $_GET['day2'];
$selectDate1 = $year1 . '-' . $month1 . '-' . $day1;
$selectDate2 = $year2 . '-' . $month2 . '-' . $day2;
?>
	<table border="1">
		<tr height="50">
			<td colspan="4"><b>안내문자 전송내역</b></td>
			<td colspan="2">기간 : <?= $selectDate1 . ' ~ ' . $selectDate2 ?></td>
		</tr>
		<tr>
        	<th width="100">num</th>
        	<th width="200">차량번호</th>
            <th width="200">차주연락처</th>
            <th width="800">안내문구</th>
            <th width="250">처리일자</th>
            <th width="150">발송구분</th>
       </tr>     
	   <?php
    $allRecSql = "select * from wb_parksmslist where left(RegDate,10) between '" . $selectDate1 . "' and '" . $selectDate2 . "' order by Regdate desc";
    $allRecRes = mysqli_query($conn, $allRecSql);
    while ($row = mysqli_fetch_assoc($allRecRes)) { ?>
		<tr>
			<td style="mso-number-format:'\@'"><?= $row['idx'] ?></td>
			<td style="mso-number-format:'\@'"><?= $row['CarNum'] ?></td>
			<td style="mso-number-format:'\@'"><?= $row['CarPhone'] ?></td>
			<td style="mso-number-format:'\@'"><?= $row['SMSContent'] ?></td> 
			<td style="mso-number-format:'\@'"><?= $row['EndDate'] ?></td>
			<td style="mso-number-format:'\@'">
				<?php if ($row['SendType'] == 'usersend') {
        echo '수동요청';
    } elseif ($row['SendType'] == 'autosend') {
        echo '자동요청';
    } ?>
			</td>           
		</tr>
		<?php }
    ?>
	</table>