<?php
$year = $_GET['year'];
$month = $_GET['month'];
$day = $_GET['day'];

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=InOutCarDay_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$selectdate = $year . '-' . $month . '-' . $day;
?>

<table border="1">
	<tr>
	<?php
 echo "<th style='background:#5e60cd;' width='100'>주차장</th>";
 echo "<th style='background:#5e60cd;' width='50'>상태</th>";

 for ($i = 0; $i < 24; $i++) {
     echo "<th style='background:#5e60cd;' width='50'>" . $i . '</th>';
 }

 echo "<th style='background:#5e60cd;' width='50'>계</th>";
 echo '</tr>';

 $nameSql = 'select * from wb_ParkGateGroup where 1 order by ParkGroupCode asc';
 $nameRes = mysqli_query($conn, $nameSql);
 while ($nameRow = mysqli_fetch_assoc($nameRes)) { ?>
<tr>	
	<td rowspan="2"><?= $nameRow['ParkGroupName'] ?></td>
	<td>입차</td>
<?php
$Insql = "select * from wb_ParkCarInCnt where RegDate = '" . $selectdate . "' and ParkGroupCode = '" . $nameRow['ParkGroupCode'] . "'";
$Inres = mysqli_query($conn, $Insql);
$Incount = mysqli_num_rows($Inres);
if ($Incount > 0) {

    $Inrow = mysqli_fetch_assoc($Inres);
    for ($i = 0; $i < 24; $i++) { ?>
	<td>
	<?php if ($Inrow['MR' . $i] != '') {
     echo $Inrow['MR' . $i];
 } else {
     echo '0';
 } ?>
	</td>
	<?php }

    // for
    ?>
	<td>
	<?php if ($Inrow['DaySum'] != '') {
     echo $Inrow['DaySum'];
 } else {
     echo '0';
 } ?>
	</td>
	<?php
} else {
    for ($i = 0; $i < 24; $i++) { ?>
	<td><?php echo '0'; ?></td>
	<?php }
    // for
    ?>
	<td><?php echo '0'; ?></td>
	<?php
}

     // else
     ?>
</tr>

<tr>
	<td>출차</td>
<?php
$Outsql = "select * from wb_ParkCarOutCnt where RegDate = '" . $selectdate . "' and ParkGroupCode = '" . $nameRow['ParkGroupCode'] . "'";
$Outres = mysqli_query($conn, $Outsql);
$Outcount = mysqli_num_rows($Outres);
if ($Outcount > 0) {

    $Outrow = mysqli_fetch_assoc($Outres);
    for ($i = 0; $i < 24; $i++) { ?>
	<td>
	<?php if ($Outrow['MR' . $i] != '') {
     echo $Outrow['MR' . $i];
 } else {
     echo '0';
 } ?>
	</td>
	<?php }

    // for
    ?>
	<td>
	<?php if ($Outrow['DaySum'] != '') {
     echo $Outrow['DaySum'];
 } else {
     echo '0';
 } ?>
	</td>        
	<?php
} else {
    for ($i = 0; $i < 24; $i++) { ?>
	<td><?php echo '0'; ?></td>
	<?php }
    // for
    ?>
	<td><?php echo '0'; ?></td>
	<?php
}

     // else
     ?>
</tr>
<?php }

//nameRow
?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
