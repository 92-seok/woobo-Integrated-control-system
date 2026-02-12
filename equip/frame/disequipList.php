<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['area'])) {
    $area = $_GET['area'];
} else {
    $area = 'all';
}

if ($area == 'all') {
    $allRecSql = 'select * from wb_dissend where 1';
    $url = 'frame/setEList.php?page=';
} else {
    $allRecSql = "select * from wb_dissend where CD_DIST_OBSV = '" . $area . "'";
    $url = 'frame/setEList.php?area=' . $area . '&page=';
}
$allRecRes = mysqli_query($conn, $allRecSql);
$countRec = mysqli_num_rows($allRecRes);

$list = 10;
$block = 20;

$pageNum = ceil($countRec / $list); // 총 페이지
$blockNum = ceil($pageNum / $block); // 총 블록
$nowBlock = ceil($page / $block);

$s_page = $nowBlock * $block - ($block - 1);

if ($s_page <= 1) {
    $s_page = 1;
}
$e_page = $nowBlock * $block;
if ($pageNum <= $e_page) {
    $e_page = $pageNum;
}
?>
<div class="cs_frame">
	<div class="cs_selectBox">
		<div class="cs_date" style="float:right;">
            <form name="form" id="id_form" method="get" action="" style="display:inline-block;">
				<input type="hidden" name="url" value="setEList.php">
                <select name="area">
                    <option value="all" selected>전체</option>
                    <?php
                    $aSql = "select * from wb_equip where GB_OBSV = '18' and USE_YN = '1'";
                    $aRes = mysqli_query($conn, $aSql);
                    while ($aRow = mysqli_fetch_array($aRes)) {
                        $chk = '';
                        if ($area == $aRow['CD_DIST_OBSV']) {
                            $chk = 'selected';
                        }
                        echo "<option value='" . $aRow['CD_DIST_OBSV'] . "' " . $chk . '>' . $aRow['NM_DIST_OBSV'] . '</option>';
                    }
                    ?>
			    </select>
				<div class="cs_search" id="id_search">검색</div>
		    </form>
        </div>
    </div>

	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:10px; box-shadow:0px 5px 3px 3px #ebebeb;">
		<tr align="center"> 
			<th width="5%">no</th>
			<th align="left" width="23%" style="padding-left:10px;">장비명</th>
			<th align="left" width="23%" style="padding-left:10px;">제어내용</th>
			<th width="23%">제어일자</th>
			<th width="23%">제어상태</th>
		</tr>
		<?php
  $count = ($page - 1) * $list;

  if ($area == 'all') {
      $sql = 'select * from wb_dissend as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV  order by SendCode desc limit ' . $count . ',' . $list;
  } else {
      $sql = "select * from wb_dissend as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV where a.CD_DIST_OBSV = '" . $area . "' order by SendCode desc limit " . $count . ',' . $list;
  }

  $res = mysqli_query($conn, $sql);
  while ($row = mysqli_fetch_assoc($res)) { ?>
		<tr align="center"> 
			<td><?= ++$count ?></td>
			<td style="text-align: left; padding-left:10px;"><?= $row['NM_DIST_OBSV'] ?></td>
			<td style="text-align: left; padding-left:10px;">
			<?php if ($row['RCMD'] == 'D010') {
       echo '표출모드 설정';
   } elseif ($row['RCMD'] == 'D020') {
       echo '현재표출 상태 확인';
   } elseif ($row['RCMD'] == 'D030') {
       echo '일반 문구 전체 삭제';
   } elseif ($row['RCMD'] == 'D040') {
       echo '긴급 문구 전체 삭제';
   } elseif ($row['RCMD'] == 'D050') {
       echo '일반 문구 추가';
   } elseif ($row['RCMD'] == 'D060') {
       echo '일반 문구 추가';
   } elseif ($row['RCMD'] == 'D070') {
       echo '평시 이미지 다운로드';
   } elseif ($row['RCMD'] == 'D080') {
       echo '긴급 문구 추가';
   } elseif ($row['RCMD'] == 'D090') {
       echo '긴급 문가 추가';
   } elseif ($row['RCMD'] == 'D100') {
       echo '긴급 이미지 다운로드';
   } elseif ($row['RCMD'] == 'S010') {
       echo '전원상태 조회';
   } elseif ($row['RCMD'] == 'S020') {
       echo '밝기 설정';
   } elseif ($row['RCMD'] == 'S040') {
       echo '시각정보 설정';
   } elseif ($row['RCMD'] == 'S050') {
       echo '릴레이 상태 요청';
   } elseif ($row['RCMD'] == 'S060') {
       echo '릴레이 상태 설정';
   } elseif ($row['RCMD'] == 'S110') {
       echo '장비 리셋';
   } ?>
			</td>
			<td><?= date('Y-m-d H:i:s', strtotime($row['RegDate'])) ?></td>
			<td>
			<?php if ($row['BStatus'] == 'start' || $row['BStatus'] == 'ing') {
       echo "<span style='color:#777;'>동작요청</span>";
   } elseif ($row['BStatus'] == 'end') {
       echo "<span style='color:blue;'>동작완료</span>";
   } elseif ($row['BStatus'] == 'error') {
       echo "<span style='color:red;'>동작오류</span>";
   } ?>
			</td>
		</tr>
	<?php }

// end whild
?>
	</table>
       
	<div class="cs_page">
		<?php
  if ($page != 1) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='3'>이전</div>";
  }
  for ($p = $s_page; $p <= $e_page; $p++) {
      $act = '';
      if ($p == $page) {
          $act = 'active';
      }
      echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='3'>" . $p . '</div>';
  }
  if ($page != $pageNum) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='3'>다음</div>";
  }
  ?>
	</div>      
</div>