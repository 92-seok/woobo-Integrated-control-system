<?php
$year = $_GET['year'];

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=InOutCarYear_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$selectdate = $year;
?>

<table border="1">
	<tr>
	<?php
 echo "<th style='background:#5e60cd;' width='100'>주차장</th>";
 echo "<th style='background:#5e60cd;' width='50'>상태</th>";

 for ($i = 1; $i < 13; $i++) {
     echo "<th style='background:#5e60cd;' width='50'>" . $i . '</th>';
 }

 echo "<th style='background:#5e60cd;' width='50'>최고</th>";
 echo "<th style='background:#5e60cd;' width='50'>계</th>";
 echo '</tr>';

 $nameSql = 'select * from wb_ParkGateGroup where 1 order by ParkGroupCode asc';
 $nameRes = mysqli_query($conn, $nameSql);
 while ($nameRow = mysqli_fetch_assoc($nameRes)) { ?>
        <tr>
        	<td rowspan="2"><?= $nameRow['ParkGroupName'] ?></td>
            <td>입차</td>
        	<?php
         $Insql =
             "select left(RegDate, 6)as CMonth, sum(DaySum)as carSum
						from wb_ParkCarInCnt
						where left(RegDate, 4) = '" .
             $year .
             "' and ParkGroupCode = '" .
             $nameRow['ParkGroupCode'] .
             "'
						group by CMonth";
         $Inres = mysqli_query($conn, $Insql);
         $Inarray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
         $max = -1000;
         while ($Inrow = mysqli_fetch_assoc($Inres)) {
             if ($Inrow['carSum']) {
                 $Inarray[date('n', strtotime($Inrow['CMonth'] . '01'))] = $Inrow['carSum'];
             }

             for ($i = 0; $i < 12; $i++) {
                 if ($max < $Inarray[$i]) {
                     $max = $Inarray[$i];
                 }
             }
         }

         if ($max == -1000) {
             $max = 0;
         }
         ?>
        	<?php
         $sum = 0;
         for ($i = 1; $i < 15; $i++) { ?>
				<?php if ($i == 13) { ?>
                <td style="background:#FAE4D6; font-weight:bold"><?= $max ?></td>
                <?php } elseif ($i == 14) { ?>
                <td style="color:#a30003; font-weight:bold"><?= $sum ?></td>
                <?php } else { ?>
                <td>
				<?php if (!$Inarray[$i]) {
        echo '0';
    } else {
        echo $Inarray[$i];

        $sum = $sum + $Inarray[$i];
    } ?>
                </td>
                <?php } ?>
           <?php }

     //for
     ?>           
        </tr>

        <tr>
            <td>출차</td>
        	<?php
         $Outsql =
             "select left(RegDate, 6)as CMonth, sum(DaySum)as carSum
						from wb_ParkCarOutCnt
						where left(RegDate, 4) = '" .
             $year .
             "' and ParkGroupCode = '" .
             $nameRow['ParkGroupCode'] .
             "'
						group by CMonth";
         $Outres = mysqli_query($conn, $Outsql);
         $Outarray = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
         $max = -1000;
         while ($Outrow = mysqli_fetch_assoc($Outres)) {
             if ($Outrow['carSum']) {
                 $Outarray[date('n', strtotime($Outrow['CMonth'] . '01'))] = $Outrow['carSum'];
             }

             for ($i = 0; $i < 12; $i++) {
                 if ($max < $Outarray[$i]) {
                     $max = $Outarray[$i];
                 }
             }
         }

         if ($max == -1000) {
             $max = 0;
         }
         ?>
        	<?php
         $sum = 0;
         for ($i = 1; $i < 15; $i++) { ?>
				<?php if ($i == 13) { ?>
                <td style="background:#FAE4D6; font-weight:bold"><?= $max ?></td>
                <?php } elseif ($i == 14) { ?>
                <td style="color:#a30003; font-weight:bold"><?= $sum ?></td>
                <?php } else { ?>
                <td>
				<?php if (!$Outarray[$i]) {
        echo '0';
    } else {
        echo $Outarray[$i];

        $sum = $sum + $Outarray[$i];
    } ?>
                </td>
                <?php } ?>
           <?php }

     //for
     ?>
        </tr>
      <?php }

//nameRow
?>
    </table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
