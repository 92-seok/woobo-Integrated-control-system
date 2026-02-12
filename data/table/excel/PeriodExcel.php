<?php
$area = $_GET['area'];
$dType = $_GET['dType'];
if (isset($_GET['equip'])) {
    $equip = $_GET['equip'];
} else {
    $equip = '';
}
if (isset($_GET['floodType'])) {
    $floodType = $_GET['floodType'];
} else {
    $floodType = 'water';
}

if (isset($_GET['sDate1'])) {
    $year1 = substr($_GET['sDate1'], 0, 4);
    $month1 = substr($_GET['sDate1'], 5, 2);
    $day1 = substr($_GET['sDate1'], 8, 2);
} else {
    $year1 = date('Y', time());
    $month1 = date('m', time());
    $day1 = date('d', time());
}

if (isset($_GET['sDate2'])) {
    $year2 = substr($_GET['sDate2'], 0, 4);
    $month2 = substr($_GET['sDate2'], 5, 2);
    $day2 = substr($_GET['sDate2'], 8, 2);
} else {
    $year2 = date('Y', time());
    $month2 = date('m', time());
    $day2 = date('d', time());
}

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=' . $dType . '_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');

$selectDate1 = $year1 . $month1 . $day1;
$selectDate2 = $year2 . $month2 . $day2;

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/data/server/dataInfo.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = new WB_EQUIP_VO();

$datadao = new WB_DATA1HOUR_DAO($dType);
$datavo = new WB_DATA1HOUR_VO();
?>
<table border="1">
	<tr>
	<th width="150">날짜</th>
		<?php
  if ($dType == 'flood') {
      $rowspan = '2';
  } else {
      $rowspan = '1';
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

  //if( $dType == "flood" && $floodType == "water" ) $datadao = new WB_DATA1HOUR_DAO("water");

  if ($dType == 'flood') {
      $datadao = new WB_DATA1HOUR_DAO('water');
      $flooddao = new WB_DATA1HOUR_DAO('flood');

      $floodvo = $flooddao->SELECT("RegDate BETWEEN '{$selectDate1}' AND '{$selectDate2}' AND CD_DIST_OBSV = '{$area}'", $equip);

      $floodArray = [];
      foreach ($floodvo as $idx => $fv) {
          for ($i = 1; $i <= 24; $i++) {
              $floodArray[$idx]['MR' . $i] = $fv->{"MR{$i}"};
          }
      }
  }
  $datavo = $datadao->SELECT("RegDate BETWEEN '{$selectDate1}' AND '{$selectDate2}' AND CD_DIST_OBSV = '{$area}'", $equip);

  foreach ($datavo as $idx => $v) {
      $strMin = '';
      $strMax = '';
      $strSum = '';

      $max = '';
      $min = '';
      $sum = 0;

      echo '<tr>';
      echo "<td style='font-weight:bold; background-color:#f2f2f2' rowspan='{$rowspan}'>" . date('Y월 m월 d일', strtotime($v->RegDate)) . '</td>';

      for ($i = 1; $i <= 24; $i++) {
          echo "<td style='text-align: right'>";
          if ($v->{"MR{$i}"} == '') {
              echo '-';
          } else {
              echo "<font color='#4900FF'>";
              if ($dType == 'snow') {
                  echo number_format($v->{"MR{$i}"} / 10, 1);
              } elseif ($dType == 'water') {
                  echo number_format($v->{"MR{$i}"} / 1000, 1);
              } elseif ($dType == 'dplace') {
                  echo number_format($v->{"MR{$i}"}, 3);
              } else {
                  echo number_format($v->{"MR{$i}"}, 1);
              }

              if ($sum == 0) {
                  $max = $v->{"MR{$i}"};
                  $min = $v->{"MR{$i}"};
              } else {
                  if ($max < $v->{"MR{$i}"}) {
                      $max = $v->{"MR{$i}"};
                  }
                  if ($min > $v->{"MR{$i}"}) {
                      $min = $v->{"MR{$i}"};
                  }
              }
              $sum += $v->{"MR{$i}"};
              echo '</font>';
          }
          echo '</td>';
      }

      switch ($dType) {
          case 'rain':
              $strMax = is_numeric($max) ? number_format($max, 1) : $max;
              echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";

              $strSum = is_numeric($sum) ? number_format($sum, 1) : $sum;
              echo "<td style='color:a30003; font-weight:bold'>{$strSum}</td>";
              break;

          case 'snow':
              $strMax = is_numeric($max) ? number_format($max / 10, 1) : $max;
              echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
              break;

          case 'water':
              $strMax = is_numeric($max) ? number_format($max / 1000, 1) : $max;
              echo "<td style='background:#E7E9C9; font-weight:bold'>{$strMax}</td>";

              $strMin = is_numeric($min) ? number_format($min / 1000, 1) : $min;
              echo "<td style='background:#D8E5F8; font-weight:bold'>{$strMin}</td>";
              break;

          case 'dplace':
              $strMax = is_numeric($max) ? number_format($max, 3) : $max;
              echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
              break;

          case 'flood':
              break;

          default:
              $strMax = is_numeric($max) ? number_format($max, 1) : $max;
              echo "<td style='background:#FAE4D6; font-weight:bold'>{$strMax}</td>";
      }
      echo '</tr>';

      if ($dType == 'flood') {
          for ($i = 1; $i <= 24; $i++) {
              echo "<td style='text-align: right'>";
              if ($floodArray[$idx]["MR{$i}"] == '' || $floodArray[$idx]["MR{$i}"] == null) {
                  echo '-';
              } else {
                  if ($floodArray[$idx]["MR{$i}"][0] === '0') {
                      echo 'X';
                  } elseif ($floodArray[$idx]["MR{$i}"][0] === '1') {
                      echo 'O';
                  }

                  if ($floodArray[$idx]["MR{$i}"][1] === '0') {
                      echo 'X';
                  } elseif ($floodArray[$idx]["MR{$i}"][1] === '1') {
                      echo 'O';
                  }

                  if ($floodArray[$idx]["MR{$i}"][2] === '0') {
                      echo 'X';
                  } elseif ($floodArray[$idx]["MR{$i}"][2] === '1') {
                      echo 'O';
                  }
              }
              echo '</td>';
          }
      }
  }
  ?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
