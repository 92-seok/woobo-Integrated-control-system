<?php
$dType = $_GET['dType'];

if (isset($_GET['sDate'])) {
    $year = substr($_GET['sDate'], 0, 4);
    $month = substr($_GET['sDate'], 5, 2);
    $day = substr($_GET['sDate'], 8, 2);
} else {
    $year = date('Y', time());
    $month = date('m', time());
    $day = date('d', time());
}

$selectDate = $year . $month . $day;

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=' . $dType . 'Day_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = new WB_EQUIP_VO();
$equip_vo = $equip_dao->SELECT("GB_OBSV = '{$area_code}' AND USE_YN = '1'");

$dao = new WB_DATA1HOUR_DAO($dType);
$vo = new WB_DATA1HOUR_VO();
?>

<table border="1">
	<tr>
	<?php
 if ($dType == 'dplace') {
     echo "<th colspan='2' width='200'>지역명</th>";
 } else {
     echo "<th width='150'>지역명</th>";
 }

 if ($dType == 'flood') {
     echo "<th width='50'>타입</th>";
 }

 for ($i = 0; $i < 24; $i++) {
     echo "<th width='50'>{$i}</th>";
 }

 if ($dType == 'rain') {
     echo "<th width='50'>최고</th>";
     echo "<th width='50'>계</th>";
 } elseif ($dType == 'water') {
     echo "<th width='50'>최대</th>";
     echo "<th width='50'>최소</th>";
 } elseif ($dType == 'dplace' || $dType == 'snow') {
     echo "<th width='50'>최고</th>";
 }
 echo '</tr>';

 foreach ($equip_vo as $evo) {
     echo '<tr>';

     /* 지역명 & Sub_OBSV || 침수 Row 갯수 */
     if ($dType == 'dplace') {
         $row = $evo->SubOBCount;
     } elseif ($dType == 'flood') {
         $row = '4';
     } else {
         $row = '1';
     }
     if ($evo->DTL_ADRES == null || $evo->DTL_ADRES == '') {
         echo "<td rowspan='{$row}' style='font-weight:bold; background-color:#f2f2f2; border-right:2px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}</td>";
     } else {
         echo "<td rowspan='{$row}' style='font-weight:bold; background-color:#f2f2f2; border-right:2px solid #e0e0e0;'>{$evo->NM_DIST_OBSV}<br>({$evo->DTL_ADRES})</td>";
     }
     /* 변위, 침수는 한 쿼리에 두줄 이상 사용하기에 따로 구현 */
     if ($dType != 'dplace' && $dType != 'flood') {
         // 강우, 수위, 함수비, 적설, 경사
         $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");
         $max = 0;

         for ($i = 1; $i <= 24; $i++) {
             echo '<td>';
             if ($vo->{"MR{$i}"} != '') {
                 if ($dType == 'snow') {
                     echo number_format($vo->{"MR{$i}"} / 10, 1);
                 }
                 // 적설 Cm
                 elseif ($dType == 'water') {
                     echo number_format($vo->{"MR{$i}"} / 1000, 1);
                 }
                 // 수위 M
                 elseif ($dType == 'rain') {
                     echo number_format($vo->{"MR{$i}"}, 1);
                     if ($max < $vo->{"MR{$i}"}) {
                         $max = $vo->{"MR{$i}"};
                     } // rain1hour Table에는 DayMax Column이 없어서 따로 구함
                 } else {
                     echo number_format($vo->{"MR{$i}"}, 1);
                 }
             } else {
                 echo '-';
             }
             echo '</td>';
         }

         // DB는 mm 수집 & (Rain : mm , Water : M , Snow : Cm) 소수점 한자리까지만 표출 (Min, Max, Sum)
         switch ($dType) {
             case 'snow':
                 $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax / 10, 1) : '-';
                 break;

             case 'water':
                 $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax / 1000, 1) : '-';
                 $strMin = is_numeric($vo->DayMin) ? number_format($vo->DayMin / 1000, 1) : '-';
                 break;

             case 'rain':
                 $strMax = is_numeric($max) ? number_format($max, 1) : '-';
                 $strSum = is_numeric($vo->DaySum) ? number_format($vo->DaySum, 1) : '-';
                 break;
         }

         if ($dType == 'rain') {
             echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
             echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
         } elseif ($dType == 'snow') {
             echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
         } elseif ($dType == 'water') {
             echo "<td style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";
             echo "<td style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
         }
     } elseif ($dType == 'dplace') {
         // 변위
         // $i = Sub_OBSV로 $i++ 될때마다 쿼리로 데이터값 받아와 mm로 표출
         for ($i = 1; $i <= $evo->SubOBCount; $i++) {
             if ($i != 1) {
                 echo '<tr>';
             }
             echo "<td width='20' style='background-color:#f2f2f2;'>{$i}</td>";

             $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'", $i);

             for ($j = 1; $j <= 24; $j++) {
                 echo '<td>';
                 if ($vo->{"MR{$j}"} != '') {
                     echo number_format($vo->{"MR{$j}"}, 1);
                 } else {
                     echo '-';
                 }
                 echo '</td>';
             }

             $strMax = is_numeric($vo->DayMax) ? number_format($vo->DayMax, 1) : '-';
             echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

             if ($i != $evo->SubOBCount) {
                 echo '</tr>';
             }
         }
     } elseif ($dType == 'flood') {
         // 침수
         // 침수수위 $data 배열에 담기
         $dao = new WB_DATA1HOUR_DAO('water');
         $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

         // 침수상태 $flood 배열에 담기
         $dao = new WB_DATA1HOUR_DAO('flood');
         $floodvo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$evo->CD_DIST_OBSV}' AND IFNULL(DATE_FORMAT(RegDate, '%Y%m%d'), RegDate) = '{$selectDate}'");

         // 침수수위 표출
         echo "<td style='background-color:#f2f2f2;'>수위</td>";
         for ($i = 1; $i <= 24; $i++) {
             echo '<td>';
             if ($vo->{"MR{$i}"} != '') {
                 echo number_format($vo->{"MR{$i}"} / 1000, 1);
             } else {
                 echo '-';
             }
             echo '</td>';
         }
         echo '</tr>';

         // 침수상태 표출
         for ($j = 0; $j <= 2; $j++) {
             echo '<tr>';
             echo "<td style='background-color:#f2f2f2;'>침수" . ($j + 1) . '</td>';
             for ($i = 1; $i <= 24; $i++) {
                 echo '<td>';

                 if ($floodvo->{"MR{$i}"} == '') {
                     echo '-';
                 } else {
                     if ($floodvo->{"MR{$i}"}[$j] == '0') {
                         echo 'X';
                     } elseif ($floodvo->{"MR{$i}"}[$j] == '1') {
                         echo 'O';
                     } else {
                         echo '-';
                     }
                 }

                 echo '</td>';
             }
             if ($j != 3) {
                 echo '</tr>';
             }
         }
     }

     echo '</tr>';
 }
 ?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
