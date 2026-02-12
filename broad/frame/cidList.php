<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
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
    $allRecSql = 'select * from wb_brdcid where 1';
    $url = 'frame/cidList.php?page=';
} else {
    $allRecSql = "select * from wb_brdcid where CD_DIST_OBSV = '" . $area . "'";
    $url = 'frame/cidList.php?area=' . $area . '&page=';
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
				<input type="hidden" name="url" value="cidList.php">
                <select name="area">
                    <option value="all" selected>전체</option>
                    <?php
                    $aSql = "select * from wb_equip where GB_OBSV = '17' and USE_YN = '1'";
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

	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:40px; box-shadow:0px 5px 3px 3px #ebebeb;">
		<tr align="center"> 
			<th width="3%"><label><input type="checkbox" name="allCheck" id="id_allCheck"></label></th>
			<th width="3%">no</th>
			<th width="30%">장비명</th>
			<th width="22%">CID</th>
			<th width="20%">등록일</th>
			<th width="20%">등록상태</th>
		</tr>
		<?php
  $count = ($page - 1) * $list;
  $listCnt = $countRec - $count;
  $where = '';
  if ($area != 'all') {
      $where = " where a.CD_DIST_OBSV = '" . $area . "' ";
  }
  $sql = 'select * from wb_brdcid as a left join wb_equip as b on a.CD_DIST_OBSV = b.CD_DIST_OBSV ' . $where . ' order by CidCode desc limit ' . $count . ',' . $list;
  $res = mysqli_query($conn, $sql);

  while ($row = mysqli_fetch_assoc($res)) {
      if (strpos($row['Cid'], '-')) {
          $cid = $row['Cid'];
      } else {
          if (strlen($row['Cid']) == 10) {
              $cid = substr($row['Cid'], 0, 3) . '-' . substr($row['Cid'], 3, 3) . '-' . substr($row['Cid'], 6, 4);
          } else {
              $cid = substr($row['Cid'], 0, 3) . '-' . substr($row['Cid'], 3, 4) . '-' . substr($row['Cid'], 7, 4);
          }
      } ?>
		<tr align="center"> 
			<td style="text-align: center;"><input type="checkbox" name="brChk" class="cs_brdChk" value="<?= $row['CidCode'] ?>"></td>
			<td><?= $listCnt-- ?></td>
			<td style="padding-left:10px;"><?= $row['NM_DIST_OBSV'] ?></td>
			<td style="padding-left:10px;"><?= $cid ?></td>
			<td><?= date('Y-m-d H:i:s', strtotime($row['RegDate'])) ?></td>
			<td>
			<?php if ($row['CStatus'] == 'start' || $row['CStatus'] == 'ing') {
       echo "<span style='color:#777;'>등록중</span>";
   } elseif ($row['CStatus'] == 'end') {
       echo "<span style='color:blue;'>등록완료</span>";
   } elseif ($row['CStatus'] == 'error') {
       echo "<span style='color:red;'>등록오류</span>";
   } ?>
			</td>
		</tr>
        <?php
  }

// end whild
?>
	</table>
	
	<div class="cs_page">
		<?php
  if ($page != 1) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='5'>이전</div>";
  }
  for ($p = $s_page; $p <= $e_page; $p++) {
      $act = '';
      if ($p == $page) {
          $act = 'active';
      }
      echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='5'>" . $p . '</div>';
  }
  if ($page != $pageNum) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='5'>다음</div>";
  }
  ?>
	</div>
                
    <div style="float: right;">
        <div class="cs_btn" id="id_add_cid_Btn">CID 등록</div>
        <div class="cs_btn" id="id_del_cid_Btn" data-num="<?= $page ?>" data="cid">CID 삭제</div>
	</div>	
</div>