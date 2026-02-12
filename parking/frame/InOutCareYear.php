<?php
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/sessionUseTime.php";
	include_once $_SERVER["DOCUMENT_ROOT"] . "/include/dbconn.php";
	
	if(isset($_GET['year'])) {$year = $_GET['year'];} else {$year = date("Y", time());}
?>

<div class="cs_frame"> <!-- 차량 입출차 통계 (#연별) -->
	<div style="display:flex;">
        <div class="cs_sub_btn" id="id_sub_btn" data-url="frame/InOutCareDay.php"># 일별</div>
        <div class="cs_sub_btn" id="id_sub_btn" data-url="frame/InOutCareMonth.php"># 월별</div>
        <div class="cs_sub_btn active" id="id_sub_btn" data-url="frame/InOutCareYear.php"># 연별</div>
    </div>
    
    <div class="cs_selectBox">
    	<div class="cs_date">
        	<form id="id_form" name="form" method="get" action="" style="display:inline-block;">
				<input type="hidden" name="arr" value="InOutCareYear.php">
				<select name="year">
					<?php for($i = 2021; $i < date("Y", time())+1; $i++) { 
						if($year == $i) {$selected = "selected";} else {$selected = "";}
					?>
					<option value="<?=$i?>"<?=$selected?>><?=$i?></option>
					<?php } ?>
				</select> 년
				
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
            <th>최고</th>
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
				$Insql = "select left(RegDate, 6)as CMonth, sum(DaySum)as carSum
						from wb_ParkCarInCnt
						where left(RegDate, 4) = '".$year."' and ParkGroupCode = '".$nameRow['ParkGroupCode']."'
						group by CMonth";		
				$Inres = mysqli_query($conn, $Insql);
				$Inarray = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$max = -1000;
				while($Inrow = mysqli_fetch_assoc($Inres)) {
					if($Inrow['carSum']) {
						$Inarray[date("n", strtotime($Inrow['CMonth']."01"))] = $Inrow['carSum'];
					}
					
					for($i = 0; $i < 12; $i++) {
						if($max < $Inarray[$i]) {
							$max = $Inarray[$i];	
						}
					}
				}
				
				if($max == -1000) {$max = 0;}
			?>
        	<?php 
				$sum = 0;	
				for($i = 1; $i < 15; $i++) { 
			?>
				<?php if($i == 13) { ?>
                <td style="background:#FAE4D6; font-weight:bold"><?=$max?></td>
                <?php } else if($i == 14) { ?>
                <td style="color:#a30003; font-weight:bold"><?=$sum?></td>
                <?php } else { ?>
                <td>
				<?php if(!$Inarray[$i]) {
						echo "0";
					  } else {
						echo $Inarray[$i]; 
						
						$sum = $sum + $Inarray[$i]; 
					  }
				?>
                </td>
                <?php } ?>
           <?php } //for?>           
        </tr>

        <tr>
            <td>출차</td>
        	<?php 
				$Outsql = "select left(RegDate, 6)as CMonth, sum(DaySum)as carSum
						from wb_ParkCarOutCnt
						where left(RegDate, 4) = '".$year."' and ParkGroupCode = '".$nameRow['ParkGroupCode']."'
						group by CMonth";	
				$Outres = mysqli_query($conn, $Outsql);
				$Outarray = array(0,0,0,0,0,0,0,0,0,0,0,0,0);
				$max = -1000;
				while($Outrow = mysqli_fetch_assoc($Outres)) {
					if($Outrow['carSum']) {
						$Outarray[date("n", strtotime($Outrow['CMonth']."01"))]	= $Outrow['carSum'];
					}
					
					for($i = 0; $i < 12; $i++) {
						if($max < $Outarray[$i]) {
							$max = $Outarray[$i];	
						}
					}
				}
				
				if($max == -1000) {$max = 0;}
			?>
        	<?php 
				$sum = 0;	
				for($i = 1; $i < 15; $i++) { 
			?>
				<?php if($i == 13) { ?>
                <td style="background:#FAE4D6; font-weight:bold"><?=$max?></td>
                <?php } else if($i == 14) { ?>
                <td style="color:#a30003; font-weight:bold"><?=$sum?></td>
                <?php } else { ?>
                <td>
				<?php if(!$Outarray[$i]) {
						echo "0";
					  } else {
						echo $Outarray[$i]; 
						
						$sum = $sum + $Outarray[$i]; 
					  }
				?>
                </td>
                <?php } ?>
           <?php } //for?>
        </tr>
      <?php } //nameRow?>
    </table>
</div> <?php //frame?>
