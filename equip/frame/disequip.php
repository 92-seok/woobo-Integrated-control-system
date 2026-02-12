<?php
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/display/server/displayStatus.php';
?>
<div class="cs_frame">
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:20px; box-shadow:0px 5px 3px 3px #ebebeb;">
		<tr align="center"> 
			<th width="5%">no</th>
			<th width="15%">장비명</th>
			<th width="15%">전원상태</th>
			<th width="15%">릴레이상태</th>
			<th width="15%">표출상태</th>
			<th>밝기</th>
		</tr>
		<?php
  $sql = "select a.NM_DIST_OBSV, b.*
				from wb_equip as a left join wb_disstatus as b
					on a.CD_DIST_OBSV = b.CD_DIST_OBSV
				where GB_OBSV = '18' and USE_YN = '1' 
				order by a.CD_DIST_OBSV asc";

  $res = mysqli_query($conn, $sql);
  $count = 0;

  while ($row = mysqli_fetch_assoc($res)) {

      $power = explode('/', $row['Power']);
      $relay = getRelay($row['Relay']);
      ?>
		<tr align="center" class="cs_disEquList" data-num="<?= $row['CD_DIST_OBSV'] ?>" style="cursor:pointer"> 
			<td><?= ++$count ?></td>
			<td style="text-align: left; padding-left:10px;"><?= $row['NM_DIST_OBSV'] ?></td>
			<td>
			<?php for ($i = 0; $i < count($power); $i++) {
       if ($i > 0) {
           echo '/';
       }
       if ($power[$i] == 0) {
           echo 'OFF';
       } elseif ($power[$i] == 1) {
           echo 'ON';
       }
   } ?>
			</td>
			<td><?= $relay[0] . ' / ' . $relay[1] . ' / ' . $relay[2] . ' / ' . $relay[3] ?></td>
			<td>
			<?php if ($row['ExpStatus'] == 'ad') {
       echo '일반';
   } elseif ($row['ExpStatus'] == 'emg') {
       echo '긴급';
   } else {
       echo '-';
   } ?>
			</td>
			<td>
			<?php if ($row['Bright'] == '') {
       echo '-';
   } else {
       echo $row['Bright'];
   } ?>
			</td>
		</tr>
	<?php
  }

// end whild
?>
	</table>
</div>