<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['year1'])) {
    $year1 = $_GET['year1'];
} else {
    $year1 = date('Y', strtotime('-7 days'));
}
if (isset($_GET['month1'])) {
    $month1 = $_GET['month1'];
} else {
    $month1 = date('m', strtotime('-7 days'));
}
if (isset($_GET['day1'])) {
    $day1 = $_GET['day1'];
} else {
    $day1 = date('d', strtotime('-7 days'));
}
if (isset($_GET['year2'])) {
    $year2 = $_GET['year2'];
} else {
    $year2 = date('Y', time());
}
if (isset($_GET['month2'])) {
    $month2 = $_GET['month2'];
} else {
    $month2 = date('m', time());
}
if (isset($_GET['day2'])) {
    $day2 = $_GET['day2'];
} else {
    $day2 = date('d', time());
}

$startDate = new DateTime($year1 . '-' . $month1 . '-' . $day1);
$endDate = new DateTime($year2 . '-' . $month2 . '-' . $day2);
$diff = date_diff($startDate, $endDate);
$count = $diff->days + 1;

$array = [];
$maxTotal = 0;
for ($i = 0; $i < $count; $i++) {
    $searchDate = date('Y-m-d', strtotime($year1 . '-' . $month1 . '-' . $day1 . ' +' . $i . ' days'));
    $sql =
        "select left(BrdDate,10) as bDate, count(*) as total,
					IFNULL(( select count(*) from wb_brdlist where (BType = 'general' or BType = 'reserve') and BrdDate like '" .
        $searchDate .
        "%' ), 0) as general,
					IFNULL(( select count(*) from wb_brdlist where BType = 'level1' and BrdDate like '" .
        $searchDate .
        "%' ), 0) as level1,
					IFNULL(( select count(*) from wb_brdlist where BType = 'level2' and BrdDate like '" .
        $searchDate .
        "%' ), 0) as level2,
					IFNULL(( select count(*) from wb_brdlist where BType = 'level3' and BrdDate like '" .
        $searchDate .
        "%' ), 0) as level3,
					IFNULL(( select count(*) from wb_brdlist where BType = 'level4' and BrdDate like '" .
        $searchDate .
        "%' ), 0) as level4
			from wb_brdlist as A
			where left(BrdDate,10) = '" .
        $searchDate .
        "'
			group by left(BrdDate,10)";

    $res = mysqli_query($conn, $sql);
    $rowCount = mysqli_num_rows($res);

    if ($rowCount > 0) {
        $row = mysqli_fetch_array($res);

        $subArray = ['date' => date('m/d', strtotime($searchDate)), 'total' => $row['total'], 'general' => $row['general'], 'level1' => $row['level1'], 'level2' => $row['level2'], 'level3' => $row['level3'], 'level4' => $row['level4']];

        if ($maxTotal < $row['total']) {
            $maxTotal = $row['total'];
        }
    } else {
        $subArray = ['date' => date('m/d', strtotime($searchDate)), 'total' => 0, 'general' => 0, 'level1' => 0, 'level2' => 0, 'level3' => 0, 'level4' => 0];
    }

    array_push($array, $subArray);
}
?>	
<div class="cs_frame" style="padding-bottom:30px">
	<div class="cs_selectBox">
        <div class="cs_date">
			<form name="form" id="id_form" method="get" action="">
			<input type="hidden" name="url" value="broadReport.php">

                <select name="year1" id="id_select">
                <?php for ($y = 2022; $y < date('Y', time()) + 1; $y++) {
                    if ($year1 == $y) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    } ?>
                    <option value="<?= $y ?>"<?= $selected ?>><?= $y ?></option>
                <?php
                } ?>
                </select> 년
                
                <select name="month1" id="id_select">
                <?php for ($m = 1; $m < 13; $m++) {

                    if ($m < 10) {
                        $date = '0' . $m;
                    } else {
                        $date = $m;
                    }
                    if ($month1 == $date) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    ?>
                    <option value="<?= $date ?>"<?= $selected ?>><?= $date ?></option>
                <?php
                } ?>
                </select> 월
                
                <select name="day1" id="id_select">
                <?php for ($d = 1; $d < 32; $d++) {

                    if ($d < 10) {
                        $date = '0' . $d;
                    } else {
                        $date = $d;
                    }
                    if ($day1 == $date) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    ?>
                    <option value="<?= $date ?>"<?= $selected ?>><?= $date ?></option>
                <?php
                } ?>
                </select> 일
                ~
                  <select name="year2" id="id_select">
                <?php for ($y = 2022; $y < date('Y', time()) + 1; $y++) {
                    if ($year2 == $y) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    } ?>
                    <option value="<?= $y ?>"<?= $selected ?>><?= $y ?></option>
                <?php
                } ?>
                </select> 년
                
                <select name="month2" id="id_select">
                <?php for ($m = 1; $m < 13; $m++) {

                    if ($m < 10) {
                        $date = '0' . $m;
                    } else {
                        $date = $m;
                    }
                    if ($month2 == $date) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    ?>
                    <option value="<?= $date ?>"<?= $selected ?>><?= $date ?></option>
                <?php
                } ?>
                </select> 월
                
                <select name="day2" id="id_select">
                <?php for ($d = 1; $d < 32; $d++) {

                    if ($d < 10) {
                        $date = '0' . $d;
                    } else {
                        $date = $d;
                    }
                    if ($day2 == $date) {
                        $selected = 'selected';
                    } else {
                        $selected = '';
                    }
                    ?>
                    <option value="<?= $date ?>"<?= $selected ?>><?= $date ?></option>
                <?php
                } ?>
                </select> 일
				<div class="cs_search" id="id_search" data-num="date_search">검색</div>
            </form>
        </div>
    </div> <?php
//selectBox
?>
	
	<div class="cs_container" style="height:100%;"> 
		<div class="graph" style="height:100%;">
		<?php
  $listHeight = 100 / $count;
  if ($maxTotal > 0) {
      $barWidth = 100 / $maxTotal;
  } else {
      $barWidth = 0;
  }
  for ($i = 0; $i < $count; $i++) { ?>
			<div class="gList" style="height:<?= $listHeight ?>%; background-color:#f7f7f7">
				<div class="date"><span><?= $array[$i]['date'] ?></span></div>
				<div class="data">
					<div class="barBox">
						<?php if ($array[$i]['general'] > 0) { ?>
						<div class="bar general" style="width:<?= $barWidth * $array[$i]['general'] ?>%;"><span><?= $array[$i]['general'] ?></span></div>
						<?php } ?>
						<?php if ($array[$i]['level1'] > 0) { ?>
						<div class="bar level1" style="width: <?= $barWidth * $array[$i]['level1'] ?>%;"><span><?= $array[$i]['level1'] ?></span></div>
						<?php } ?>
						<?php if ($array[$i]['level2'] > 0) { ?>
						<div class="bar level2" style="width:<?= $barWidth * $array[$i]['level2'] ?>%;"><span><?= $array[$i]['level2'] ?></span></div>
						<?php } ?>
						<?php if ($array[$i]['level3'] > 0) { ?>
						<div class="bar level3" style="width:<?= $barWidth * $array[$i]['level3'] ?>%;"><span><?= $array[$i]['level3'] ?></span></div>
						<?php } ?>
						<?php if ($array[$i]['level4'] > 0) { ?>
						<div class="bar level4" style="width:<?= $barWidth * $array[$i]['level4'] ?>%;"><span><?= $array[$i]['level4'] ?></span></div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }
  ?>
	</div>
	
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="width:40%; height:100%;">
		<tr align="center" style="height:30px;"> 
			<th width="14%">날짜</th>
			<th width="14%">일반방송</th>
			<th width="14%">1단계 경보</th>
			<th width="14%">2단계 경보</th>
			<th width="14%">3단계 경보</th>
			<th width="14%">4단계 경보</th>
			<th width="14%">총계</th>
		</tr>
		<?php for ($i = 0; $i < $count; $i++) { ?>
		<tr>
			<td style="height:auto;"><?= $array[$i]['date'] ?></td>
			<td style="height:auto;"><?= $array[$i]['general'] ?></td>
			<td style="height:auto;"><?= $array[$i]['level1'] ?></td>
			<td style="height:auto;"><?= $array[$i]['level2'] ?></td>
			<td style="height:auto;"><?= $array[$i]['level3'] ?></td>
			<td style="height:auto;"><?= $array[$i]['level4'] ?></td>
			<td style="height:auto;"><?= $array[$i]['total'] ?> </td>
		</tr>
		<?php } ?>
	</table>
</div>