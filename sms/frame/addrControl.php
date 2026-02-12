<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_SMSUSER_DAO();
$vo = new WB_SMSUSER_VO();

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['recType'])) {
    $recType = $_GET['recType'];
} else {
    $recType = '';
}
if (isset($_GET['search'])) {
    $search = $_GET['search'];
} else {
    $search = '';
}

$url = 'frame/addrControl.php?page=';
$where = '1';
$list = 10;
$block = 20;

if ($search != '') {
    if ($recType == 'name') {
        $where = "UName like '%" . $search . "%'";
    } elseif ($recType == 'number') {
        $where = "Phone like '%" . $search . "%'";
    }
}
?>
<div class="cs_frame"> <!-- 연락처관리 -->
    <div class="cs_selectBox" style="margin-top:10px;display: flex;flex-direction: row-reverse;">              
		<form name="form" id="id_form" method="get" action="">
			검색종류 : 
			<select name="recType" id="recType">
				<option value="name" <?php if ($recType == 'name') {
        echo 'selected';
    } ?>>별칭</option>
				<option value="number" <?php if ($recType == 'number') {
        echo 'selected';
    } ?>>전화번호</option>
			</select>
			&nbsp;
			
			검색내용 : 
			<input type="text" name="search" size="10" value="<?= $search ?>" autocomplete="off">
			&nbsp;                   
			<div class="cs_search" id="id_search">검색</div>
		</form>    
    </div>
    
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
        	<th width="60">no</th>
            <th>부서명</th>
			<th>직책</th>
			<th>별칭</th>
            <th>전화번호</th>
           
        </tr>
        <?php
        $count = ($page - 1) * $list;
        $vo = $dao->SELECT($where);

        foreach ($vo as $v) {
            $count++;

            if (strpos($v->Phone, '-')) {
                $phone_number = $v->Phone;
            } else {
                if (strlen($v->Phone) == 10) {
                    $phone_number = substr($v->Phone, 0, 3) . '-' . substr($v->Phone, 3, 3) . '-' . substr($v->Phone, 6, 4);
                } else {
                    $phone_number = substr($v->Phone, 0, 3) . '-' . substr($v->Phone, 3, 4) . '-' . substr($v->Phone, 7, 4);
                }
            }

            echo "<tr id='id_smsList' data-num='{$v->GCode}' data-type='user' style='cursor:pointer;'>";
            echo "<td>{$count}</td>";
            echo "<td>{$v->Division}</td>";
            echo "<td>{$v->UPosition}</td>";
            echo "<td>{$v->UName}</td>";
            echo "<td>{$phone_number}</td>";
            echo '</tr>';
        }
        ?>
    </table>

	<?php
 $pageNum = ceil(count($vo) / $list); // 총 페이지
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
	<div class="cs_page">
		<?php
  if ($page != 1) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page - 1) . "' data-idx='2'>이전</div>";
  }
  for ($p = $s_page; $p <= $e_page; $p++) {
      $act = '';
      if ($p == $page) {
          $act = 'active';
      }
      echo "<div class='cs_pages " . $act . "' id='id_page' data-url='" . $url . $p . "' data-idx='2'>" . $p . '</div>';
  }
  if ($page != $pageNum) {
      echo "<div class='cs_pages' id='id_page' data-url='" . $url . ($page + 1) . "' data-idx='2'>다음</div>";
  }
  ?>
	</div>
    
	<div style="float: right; margin-top:15px;">
		<div class="cs_btn" id="id_smsList" data-num="-1" data-type="user">추 가</div>
	</div>

	<div id="id_helpForm">
		<div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
		- 문자를 전송받을 연락처를 관리합니다.<br/>
		- 추가한 연락처는 ‘문자발송’ - ‘수신자선택’ 또는 ‘임계치관리’탭의 ‘경보그룹설정’에서 확인할 수 있습니다.
		</div>
	</div>

</div>
