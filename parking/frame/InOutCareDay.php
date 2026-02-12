<?php
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/dbconn.php";
	
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y", time());}
	if(isset($_GET['month'])) {$month = $_GET['month'];} else {$month = date("m", time());}
	if(isset($_GET['day'])) {$day = $_GET['day'];} else {$day = date("d", time());}
	
	$selectdate = $year.$month.$day;
?>
<div class="cs_frame"> <!-- 차량 입출차 통계 (#일별) -->
    <div style="display:flex;">
        <div class="cs_sub_btn active" id="id_sub_btn" data-url="frame/InOutCareDay.php"># 일별</div>
        <div class="cs_sub_btn" id="id_sub_btn" data-url="frame/InOutCareMonth.php"># 월별</div>
        <div class="cs_sub_btn" id="id_sub_btn" data-url="frame/InOutCareYear.php"># 연별</div>
    </div>
    
    <div class="cs_selectBox">
    	<div class="cs_date">
        <form id="id_form" name="form" method="get" action="" style="display:inline-block;">
            <input type="hidden" name="arr" value="InOutCareDay.php">
            <select name="year">
                <?php for($i = 2021; $i < date("Y", time())+1; $i++) { 
					if($year == $i) {$selected = "selected";} else {$selected = "";}
				?>
                <option value="<?=$i?>"<?=$selected?>><?=$i?></option>
                <?php } ?>
            </select> 년
            
            <select name="month">
            	<?php for($i = 1; $i < 13; $i++) {
					if($i < 10) {$date = "0".$i;} else {$date = $i;} 
					if($date == $month) {$selected = "selected";} else {$selected = "";}
				?>
                <option value="<?=$date?>"<?=$selected?>><?=$date?></option>
                <?php } ?>
            </select> 월
            
            <select name="day">
            	<?php for($i = 1; $i < 32; $i++) { 
					if($i < 10) {$date = "0".$i;} else {$date = $i;}
					if($date == $day) {$selected = "selected";} else {$selected = "";}
				?>
                <option value="<?=$date?>"<?=$selected?>><?=$date?></option>
                <?php } ?>
            </select> 일
            
            <input type="hidden" name="mode" value="report">
            <div class="cs_search" id="id_search">검색</div>
        </form>
        <div class="cs_excel" id="id_excel">엑셀다운</div>     
        </div>
    </div>
    
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows" style="margin-top:40px">
    	<tr>
        	<th width="100">주차장</th>
            <th>상태</th>
        	<th>0</th>
            <th>1</th>
            <th>2</th>
            <th>3</th>
            <th>4</th>
            <th>5</th>
            <th>6</th>
            <th>7</th>
            <th>8</th>
            <th>9</th>
            <th>10</th>
            <th>11</th>
            <th>12</th>
            <th>13</th>
            <th>14</th>
            <th>15</th>
            <th>16</th>
            <th>17</th>
            <th>18</th>
            <th>19</th>
            <th>20</th>
            <th>21</th>
            <th>22</th>
            <th>23</th> 
            <th>계</th>          
        </tr>
        
        <?php 
			$nameSql = "select * from wb_ParkGateGroup where 1 order by ParkGroupCode asc";
			$nameRes = mysqli_query($conn, $nameSql);
			while($nameRow = mysqli_fetch_assoc($nameRes)) {
		?>
		<tr>	
        	<td rowspan="2"><?=$nameRow['ParkGroupName']?></td>
            <td>입차</td>
        <?php 
			$Insql = "select * from wb_ParkCarInCnt where RegDate = '".$selectdate."' and ParkGroupCode = '".$nameRow['ParkGroupCode']."'";
			$Inres = mysqli_query($conn, $Insql);
			$Incount = mysqli_num_rows($Inres);
			if($Incount > 0) {
				$Inrow = mysqli_fetch_assoc($Inres);
				for($i = 1; $i <= 24; $i++) { 
		?>
            <td>
			<?php if($Inrow['MR'.$i] != '') {
					echo $Inrow['MR'.$i];	
			      } else {
					echo "0";	
				  }
			?>
            </td>
            <?php } // for ?>
            <td>
			<?php if($Inrow['DaySum'] != '') {
					echo $Inrow['DaySum'];	
			      } else {
					echo "0";	
				  }
			?>
            </td>
            <?php } else { 
					for($i = 1; $i <= 24; $i++) { 
			?>
            <td><?php echo "0"?></td>
            <?php } // for?>
            <td><?php echo "0"?></td>
            <?php } // else?>
        </tr>

		<tr>
        	<td>출차</td>
		<?php 
            $Outsql = "select * from wb_ParkCarOutCnt where RegDate = '".$selectdate."' and ParkGroupCode = '".$nameRow['ParkGroupCode']."'";
            $Outres = mysqli_query($conn, $Outsql);
			$Outcount = mysqli_num_rows($Outres);
			if($Outcount > 0) {
            	$Outrow = mysqli_fetch_assoc($Outres);
          		for($i = 1; $i <= 24; $i++) { 
		?>
            <td>
			<?php if($Outrow['MR'.$i] != '') {
					echo $Outrow['MR'.$i];	
			      } else {
					echo "0";	
				  }
			?>
            </td>
            <?php } // for?>
            <td>
			<?php if($Outrow['DaySum'] != '') {
					echo $Outrow['DaySum'];	
			      } else {
					echo "0";	
				  }
			?>
            </td>        
        	<?php } else { 
					for($i = 1; $i <= 24; $i++) {
			?>
            <td><?php echo "0"?></td>
            <?php } // for?>
            <td><?php echo "0"?></td>
            <?php } // else?>
        </tr>
      <?php } //nameRow?>
    </table>
</div> <?php //frame?>