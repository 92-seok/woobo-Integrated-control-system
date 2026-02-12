<?php
header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=Report_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
?>

<?php
$sql = "SELECT DISTINCT GB_OBSV FROM wb_equip WHERE USE_YN = '1' ORDER BY GB_OBSV ASC";
$res = mysqli_query($conn, $sql);
$gb_obsv = [];
while ($row = mysqli_fetch_assoc($res)) {
    array_push($gb_obsv, $row['GB_OBSV']);
}

echo "<div style='font-size:18px'>";
echo date('Y년 m월 d일') . ' Day Report';
echo '</div>';
echo '<br>';

// 수위
if (in_array('02', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 수위</font><font style="font-size:10px"> (Cm) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#5fbaef;">
			<th colspan="2">지역명</th>
			<th colspan="2">현재</th>
		</tr>
		<?php
  $water_sql = "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.*
					from wb_equip as a left join wb_waterdis as b
					on a.CD_DIST_OBSV = b.CD_DIST_OBSV
					where GB_OBSV = '02' and USE_YN = '1' order by a.CD_DIST_OBSV asc";
  $water_res = mysqli_query($conn, $water_sql);
  while ($water_row = mysqli_fetch_assoc($water_res)) { ?>
		<tr>
			<td colspan="2" align="center"><?= $water_row['NM_DIST_OBSV'] ?></td>
			<td colspan="2" align="center"><?= $water_row['water_now'] ?></td>
		</tr>
		<?php }

    // waterWhile
    ?>
	</table>
	<br>
<?php }

// 강우
if (in_array('01', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 강우</font><font style="font-size:10px"> (mm) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#738bd5;">
			<th>지역명</th>
			<th><?= date('m월 d일', strtotime('-2days')) ?></th>
			<th><?= date('m월 d일', strtotime('-1days')) ?></th>
			<th><?= date('m월 d일', time()) ?></th>
			<th>합계</th>
		</tr>
		<?php
  $rain_sql =
      "select  a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.today, c.yesterday, d.yyesterday from wb_equip as a left join
					(select CD_DIST_OBSV, DaySum as today
					from wb_rain1hour where RegDate = '" .
      date('Ymd', time()) .
      "')as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV left join
					(select CD_DIST_OBSV, DaySum as yesterday 
					from wb_rain1hour where RegDate = '" .
      date('Ymd', strtotime('-1days')) .
      "')as c on a.CD_DIST_OBSV = c.CD_DIST_OBSV left join
					(select CD_DIST_OBSV, DaySum as yyesterday
					from wb_rain1hour where RegDate = '" .
      date('Ymd', strtotime('-2days')) .
      "')as d on a.CD_DIST_OBSV = d.CD_DIST_OBSV
					where GB_OBSV = '01' and USE_YN = '1' order by CD_DIST_OBSV asc";
  $rain_res = mysqli_query($conn, $rain_sql);
  while ($rain_row = mysqli_fetch_assoc($rain_res)) { ?>
		<tr>
			<td align="center"><?= $rain_row['NM_DIST_OBSV'] ?></td>
			<td align="center">
				<?php if ($rain_row['yyesterday'] == '') {
        echo '-';
    } else {
        echo number_format($rain_row['yyesterday'], 1);
    } ?>
			</td>
			<td align="center">
				<?php if ($rain_row['yesterday'] == '') {
        echo '-';
    } else {
        echo number_format($rain_row['yesterday'], 1);
    } ?>
			</td>
			<td align="center">
				<?php if ($rain_row['today'] == '') {
        echo '-';
    } else {
        echo number_format($rain_row['today'], 1);
    } ?>
			</td>
			<td align="center"><?= number_format($rain_row['yyesterday'] + $rain_row['yesterday'] + $rain_row['today'], 1) ?></td>
		</tr>
		<?php }

    // rainWhile
    ?>
	</table>
	<br>
<?php }

// 변위
if (in_array('03', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 변위</font><font style="font-size:10px"> (mm) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#e49479;">
			<th colspan="2">지역명</th>
			<th>현재</th>
			<th>누적</th>
		</tr>
		<?php
  $dplace_sql = "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.*
						from wb_equip as a left join wb_dplacedis as b
						on a.CD_DIST_OBSV = b.CD_DIST_OBSV
						where GB_OBSV = '03' and USE_YN = '1' order by a.CD_DIST_OBSV asc, b.SUB_OBSV";
  $dplace_res = mysqli_query($conn, $dplace_sql);
  while ($dplace_row = mysqli_fetch_assoc($dplace_res)) { ?>
		<tr>
			<td align="center"><?= $dplace_row['NM_DIST_OBSV'] ?></td>
			<td align="center"><?= $dplace_row['SUB_OBSV'] ?></td>
			<td align="center"><?= number_format($dplace_row['dplace_now'], 1) ?></td>
			<td align="center"><?= number_format($dplace_row['dplace_today'], 1) ?></td>
		</tr>
		<?php }

    //dplaceWhile
    ?>
	</table>
	<br>
<?php }

// 함수비
if (in_array('04', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 함수비율</font><font style="font-size:10px"> (%) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#e49479;">
			<th colspan="2">지역명</th>
			<th colspan="2">현재</th>
		</tr>
		<?php
  $soil_sql = 'SELECT NM_DIST_OBSV, ErrorChk, MR' . date('G', time()) . " as now FROM wb_soil1hour as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE GB_OBSV = '04' and USE_YN = '1' and RegDate = '" . date('Ymd', time()) . "'";
  $soil_res = mysqli_query($conn, $soil_sql);
  while ($soil_row = mysqli_fetch_assoc($soil_res)) { ?>
		<tr>
			<td colspan="2"><?= $soil_row['NM_DIST_OBSV'] ?></td>
			<td colspan="2"><?= number_format($soil_row['now'], 1) ?></td>
		</tr>
		<?php }

    //soilWhile
    ?>
	</table>
	<br>
<?php }

// 경사
if (in_array('08', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 경사</font><font style="font-size:10px"> (°) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#e49479;">
			<th colspan="2">지역명</th>
			<th colspan="2">현재</th>
		</tr>
		<?php
  $tilt_sql = 'SELECT NM_DIST_OBSV, ErrorChk, MR' . date('G', time()) . " as now FROM wb_tilt1hour as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV WHERE GB_OBSV = '08' and USE_YN = '1' and RegDate = '" . date('Ymd', time()) . "'";
  $tilt_res = mysqli_query($conn, $tilt_sql);
  while ($tilt_row = mysqli_fetch_assoc($tilt_res)) { ?>
		<tr>
			<td colspan="2"><?= $tilt_row['NM_DIST_OBSV'] ?></td>
			<td colspan="2"><?= number_format($tilt_row['now'], 1) ?></td>
		</tr>
		<?php }

    //tiltWhile
    ?>
	</table>
	<br>
<?php }

// 적설
if (in_array('06', $gb_obsv)) { ?>
	<div><font style="font-size:14px"> ◈ 적설</font><font style="font-size:10px"> (Cm) </font></div>
	<table border="1" align="center">
		<tr style="background-color:#46b7b6;">
			<th colspan="2">지역명</th>
			<th>현재</th>
			<th>누적</th>
		</tr>
		<?php
  $snow_sql = "select a.CD_DIST_OBSV, a.NM_DIST_OBSV, b.*
						from wb_equip as a left join wb_snowdis as b
						on a.CD_DIST_OBSV = b.CD_DIST_OBSV
						where GB_OBSV = '04' and USE_YN = '1' order by a.CD_DIST_OBSV asc";
  $snow_res = mysqli_query($conn, $snow_sql);
  while ($snow_row = mysqli_fetch_assoc($snow_res)) { ?>
		<tr>
			<td colspan="2" align="center"><?= $snow_row['NM_DIST_OBSV'] ?></td>
			<td align="center"><?= number_format($snow_row['snow_hour'], 1) ?></td>
			<td align="center"><?= number_format($snow_row['snow_today'], 1) ?></td>
		</tr>
		<?php }

    //snowWhile
    ?>
	</table>
	<br>
<?php }
?>

<div><font style="font-size:14px"> ◈ 경보현황</font></div>
	<table border="1" align="center">
		<tr style="background-color:#5b237c;">
			<th>지구명</th>
			<th>경보발령단계</th>
			<th>발령시간</th>
			<th>종료시간</th>
			<th>발생사유</th>
			<th>상태</th>
		</tr>
		<?php
  $sql = "select a.GCode, a.GName, b.*
				from wb_isualertgroup as a left join wb_isulist as b
				on a.GCode = b.GCode
				order by b.IsuSrtDate desc limit 0,5";
  $res = mysqli_query($conn, $sql);
  while ($row = mysqli_fetch_assoc($res)) {
      $explode = explode(',', $row['Occur']); ?>
		<tr>
			<td align="center"><?= $row['GName'] ?></td>
			<td align="center">
				<?php if ($row['IsuKind'] == 'level1') {
        echo '레벨1';
    } elseif ($row['IsuKind'] == 'level2') {
        echo '레벨2';
    } elseif ($row['IsuKind'] == 'level3') {
        echo '레벨3';
    } elseif ($row['IsuKind'] == 'level4') {
        echo '레벨4';
    } ?>
			</td>
			<td align="center"><?= $row['IsuSrtDate'] ?></td>
			<td align="center"><?= $row['IsuEndDate'] ?></td>
			<td align="center">
				<?php for ($i = 0; $i < count($explode); $i++) {
        if ($explode[$i] == '02') {
            if ($explode[$i] == $explode[0]) {
                echo '수위';
            } else {
                echo ', 수위';
            }
        } elseif ($explode[$i] == '03') {
            if ($explode[$i] == $explode[0]) {
                echo '변위';
            } else {
                echo ', 변위';
            }
        } elseif ($explode[$i] == '01') {
            if ($explode[$i] == $explode[0]) {
                echo '강우';
            } else {
                echo ', 강우';
            }
        } elseif ($explode[$i] == 'manual') {
            if ($explode[$i] == $explode[0]) {
                echo '수동제어';
            } else {
                echo ', 수동제어';
            }
        }
    } ?>
			</td>
			<td align="center">
				<?php if ($row['IStatus'] == 'm-start') {
        echo '수동 시작';
    } elseif ($row['IStatus'] == 'start') {
        echo '시작';
    } elseif ($row['IStatus'] == 'ing') {
        echo '발령 중';
    } elseif ($row['IStatus'] == 'end') {
        echo '종료';
    } ?>                
			</td>
		</tr>
		<?php
  }
  ?>
	</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
