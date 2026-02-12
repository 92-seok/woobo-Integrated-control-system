<?php
$chk = $_GET['chk'];
$move = $_GET['move'];
$count = $_GET['count'];
$list = $_GET['list'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_LOG_DAO();
$vo = new WB_LOG_VO();

if ($chk == 'all') {
    if ($move == '1') {
        $where = '1';
    } else {
        $where = "EventType != 'Move'";
    }
} else {
    if ($move == '1') {
        $where = "pType = '{$chk}'";
    } else {
        $where = "pType = '{$chk}' AND EventType != 'Move'";
    }
}

header('Content-type:application/vnd.ms-excel');
header('Content-Disposition:attachment;filename=logList_' . date('YmdHis', time()) . '.xls');
header('Content-Description:PHP4 Generated Data');
header('Content-Type: text/html; charset=euc-kr');
?>

<table border="1" style="text-align:center;font-size:14px">
    <tr> 
        <th style="background-color:#f9d9ca; color:#fff; font-size:16px; font-weight:bold; width:75px;">no</th>
        <th style="background-color:#f9d9ca; color:#fff; font-size:16px; font-weight:bold; width:200px;">RegDate</th>
        <th style="background-color:#f9d9ca; color:#fff; font-size:16px; font-weight:bold; width:200px;">IP(ID)</th>
        <th style="background-color:#f9d9ca; color:#fff; font-size:16px; font-weight:bold; width:100px;">Page</th>
        <th style="background-color:#f9d9ca; color:#fff; font-size:16px; font-weight:bold; width:160px;">Event</th>
    </tr>
    <?php
    $vo = $dao->SELECT($where, 'idx DESC', "{$count},{$list}");
    if (isset($vo[0]->{key($vo[0])})) {
        foreach ($vo as $v) {
            echo "<tr class='cs_trList' style='cursor:pointer' data-idx='{$v->idx}'>";
            echo "<td>{$v->idx}</td>";
            echo "<td>{$v->RegDate}</td>";
            echo "<td>{$v->ip}({$v->userID})</td>";
            echo '<td>';
            switch ($v->pType) {
                case 'data':
                    echo '데이터';
                    break;
                case 'broad':
                    echo '방송';
                    break;
                case 'display':
                    echo '전광판';
                    break;
                case 'equip':
                    echo '장비';
                    break;
                case 'sms':
                    echo 'SMS';
                    break;
                case 'gate':
                    echo '차단기';
                    break;
                case 'alert':
                    echo '임계치';
                    break;
                case 'admin':
                    echo '계정';
                    break;
            }
            echo '</td>';
            echo "<td>{$v->EventType}</td>";
            echo '</tr>';
        }
    }
    ?>
</table>
<?php echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> "; ?>
