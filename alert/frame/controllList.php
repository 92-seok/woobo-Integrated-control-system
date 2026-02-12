<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}

$listDao = new WB_ISULIST_DAO();
$groupDao = new WB_ISUALERTGROUP_DAO();

$listVo = new WB_ISULIST_VO();
$groupVo = new WB_ISUALERTGROUP_VO();

$listVo = $listDao->SELECT();
if (isset($listVo[0]->{key($listVo[0])})) {
    $countRec = count($listVo);
} else {
    $countRec = 0;
}

$url = 'frame/controllList.php?page=';

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
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
        <tr align="center"> 
			<th width="3%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
            <th width="3%">no</th>
            <th width="16%">경보이름</th>
            <th width="6%">발생종류</th>
            <th width="11%">발생시간</th>
            <th width="11%">발생타입</th>
            <th width="11%">종료시간</th>
            <th width="11%">종료타입</th>
            <th width="11%">현재상태</th>
        </tr>
        <?php
        $count = ($page - 1) * $list;
        $listVo = $listDao->SELECT('1', 'IsuCode DESC', "{$count},{$list}");
        if (isset($listVo[0]->{key($listVo[0])})) {
            foreach ($listVo as $v) {
                $groupVo = $groupDao->SELECT_SINGLE("GCode = '{$v->GCode}'");

                echo "<tr align='center' style='cursor:pointer;'>";
                //체크박스
                echo "<td style='text-align:center;'>";
                echo "<input type='checkbox' name='isuChk' class='cs_isuChk' value='{$v->IsuCode}'>";
                echo '</td>';
                //No
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>{$countRec}</td>";
                //경보이름
                echo "<td id='id_alertList' data-num='{$v->IsuCode}' style='text-align:left; padding-left:10px;'>{$groupVo->GName}</td>";
                //발생종류
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>";
                for ($i = 1; $i <= 4; $i++) {
                    if ($v->IsuKind == "level{$i}") {
                        echo "{$i}단계";
                    }
                }
                echo '</td>';
                //발생시간
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>" . date('Y-m-d H:i', strtotime($v->IsuSrtDate)) . '</td>';
                //발생타입
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>" . ($v->IsuSrtAuto == 'manual' ? '수동발령' : '자동발령') . '</td>';
                //종료시간
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>" . ($v->IsuEndDate ? date('Y-m-d H:i', strtotime($v->IsuEndDate)) : '-') . '</td>';
                //종류타입
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>";
                if ($v->IsuEndAuto == '') {
                    echo '-';
                } elseif ($v->IsuEndAuto == 'manual') {
                    echo '수동종료';
                } elseif ($v->IsuEndAuto == 'end') {
                    echo '자동종료';
                } elseif ($v->IsuEndAuto == 'advance') {
                    echo '상향조정종료';
                } elseif ($v->IsuEndAuto == 'retreat') {
                    echo '하향조정종료';
                }
                echo '</td>';
                //현재상태
                echo "<td id='id_alertList' data-num='{$v->IsuCode}'>";
                if ($v->IStatus == 'm-start') {
                    echo "<span style='color:blue;'>발령대기</span>";
                } elseif ($v->IStatus == 'start' || $v->IStatus == 'ing') {
                    echo "<span style='color:red;'>경보발령중</span>";
                } elseif ($v->IStatus == 'end') {
                    echo "<span style='color:#555;'>경보발령종료</span>";
                }
                echo '</td>';
                echo '</tr>';

                $countRec--;
            }
        }
        ?>
	</table>
       
		<div class="cs_page">
		<?php
  if ($page != 1) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='1'>이전</div>";
  }
  for ($p = $s_page; $p <= $e_page; $p++) {
      $act = '';
      if ($p == $page) {
          $act = 'active';
      }
      echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='1'>" . $p . '</div>';
  }
  if ($page != $pageNum) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='1'>다음</div>";
  }
  ?>
	</div>  
	
	<div class='cs_btnBox' style="justify-content:flex-end;">
		<div class="cs_btn" id="id_alerDeleteBtn" data-type="control">경보 삭제</div>  
	</div>

</div>