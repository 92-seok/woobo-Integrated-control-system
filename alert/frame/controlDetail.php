<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php'; ?>
<style>
	.cs_datatable td
	{
		text-align:left;
		text-indent:10px;
	}
</style>
<div class="cs_frame">
	<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
	<?php
 include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

 $listDao = new WB_ISULIST_DAO();
 $groupDao = new WB_ISUALERTGROUP_DAO();

 $listVo = new WB_ISULIST_VO();
 $groupVo = new WB_ISUALERTGROUP_VO();

 $num = $_GET['num'];

 $listVo = $listDao->SELECT_SINGLE("IsuCode = '{$num}'");

 if (isset($listVo->{key($listVo)})) {
     echo '<tr>';
     echo "<th width='13%'>경보이름</th>";
     echo '<td>';
     for ($i = 1; $i <= 4; $i++) {
         if ($listVo->IsuKind == "level{$i}") {
             echo "임계치 {$i}단계";
         }
     }
     echo '</td>';
     echo '</tr>';

     echo '<tr>';
     echo "<th width='13%'>경보시작</th>";
     echo '<td><strong>[' . ($listVo->IsuSrtAuto == 'manual' ? '수동발령' : '자동발령') . ']</strong> ' . date('Y-m-d H:i', strtotime($listVo->IsuSrtDate)) . '</td>';
     echo '</tr>';

     echo '<tr>';
     echo "<th width='13%'>경보종료</th>";
     echo '<td><strong>[';
     if ($listVo->IsuEndAuto == '') {
         echo ' ';
     } elseif ($listVo->IsuEndAuto == 'manual') {
         echo '수동종료';
     } elseif ($listVo->IsuEndAuto == 'auto') {
         echo '자동종료';
     } elseif ($listVo->IsuEndAuto == 'advance') {
         echo '상향조정종료';
     } elseif ($listVo->IsuEndAuto == 'retreat') {
         echo '하향조정종료';
     }
     echo ']</strong>';
     echo date('Y-m-d H:i', strtotime($listVo->IsuEndDate)) . '</td>';
     echo '</tr>';

     echo '<tr>';
     echo "<th width='13%'>발생사유</th>";
     echo "<td style='line-height:1.5em;'>";
     if ($listVo->Occur == 'manual') {
         echo '수동제어';
     } else {
         $occur = explode(',', $listVo->Occur);

         for ($i = 0; $i < count($occur); $i++) {
             $equip = '';
             if ($occur[$i] == 'rain1') {
                 $equip = '강우(1시간)';
             } elseif ($occur[$i] == 'rain2') {
                 $equip = '강우(2시간)';
             } elseif ($occur[$i] == 'rain3') {
                 $equip = '강우(3시간)';
             } elseif ($occur[$i] == 'rain6') {
                 $equip = '강우(6시간)';
             } elseif ($occur[$i] == 'rain12') {
                 $equip = '강우(12시간)';
             } elseif ($occur[$i] == 'rain24') {
                 $equip = '강우(24시간)';
             } elseif ($occur[$i] == 'water') {
                 $equip = '수위';
             } elseif ($occur[$i] == 'dplace') {
                 $equip = '변위';
             } elseif ($occur[$i] == 'news') {
                 $equip = '특보';
             } elseif ($occur[$i] == 'flood') {
                 $equip = '침수';
             }

             echo $i + 1 . ". {$equip}<br>";
         }
     }
     echo '</td>';
     echo '</tr>';

     echo '<tr>';
     echo "<th width='13%'>동작장비</th>";
     echo '<td>';
     if (isset($listVo->Equip) && $listVo->Equip != '') {
         $equipDao = new WB_EQUIP_DAO();
         $equipVo = new WB_EQUIP_VO();

         $equipVo = $equipDao->SELECT("CD_DIST_OBSV IN ({$listVo->Equip})");
         if (isset($equipVo[0]->{key($equipVo[0])})) {
             $arr = [];
             foreach ($equipVo as $v) {
                 if ($v->GB_OBSV == '17') {
                     $equip = '예경보';
                 } elseif ($v->GB_OBSV == '20') {
                     $equip = '차단기';
                 } elseif ($v->GB_OBSV == '18') {
                     $equip = '전광판';
                 }

                 array_push($arr, "<strong>({$equip})</strong>{$v->NM_DIST_OBSV}");
             }
         }
         echo implode(', ', $arr);
     }
     echo '</td>';
     echo '</tr>';

     echo '<tr>';
     echo "<th width='13%'>현재상태</th>";
     echo '<td>';
     if ($listVo->IStatus == 'm-start') {
         echo "<span style='color:blue;'>발령대기</span>";
     } elseif ($listVo->IStatus == 'start' || $listVo->IStatus == 'ing') {
         echo "<span style='color:red;'>경보발령중</span>";
     } elseif ($listVo->IStatus == 'end') {
         echo "<span style='color:#555;'>경보발령종료</span>";
     }
     echo '</td>';
     echo '</tr>';
 }
 ?>
	</table>
</div>