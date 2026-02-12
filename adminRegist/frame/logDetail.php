<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['idx'])) {
    $idx = $_GET['idx'];
} else {
    echo "<script>alert('잘못된 접근입니다.');</script>";
    echo '<script>window.history.back();</script>';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/adminRegist/server/pageName.php';

$dao = new WB_LOG_DAO();
$vo = new WB_LOG_VO();

$vo = $dao->SELECT_SINGLE("idx = {$idx}");
$vo->Page = pageName($vo->Page);
?>
<style>
.cs_datatable td
{
    text-align:left;
    padding-left: 15px;
}
</style>
<div class="cs_frame">
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:10px;">
        <?php
        echo '<tr>';
        echo "<th width='13%'>No</th>";
        echo "<td>{$vo->idx}</td>";
        echo '</tr>';

        echo '<tr>';
        echo "<th width='13%'>날짜</th>";
        echo "<td>{$vo->RegDate}</td>";
        echo '</tr>';

        echo '<tr>';
        echo "<th width='13%'>IP</th>";
        echo "<td>{$vo->ip}</td>";
        echo '</tr>';

        echo '<tr>';
        echo "<th width='13%'>사용자</th>";
        echo "<td>{$vo->userID}</td>";
        echo '</tr>';

        echo '<tr>';
        echo "<th width='13%'>페이지</th>";
        echo '<td>';
        switch ($vo->pType) {
            case 'data':
                echo '[데이터]';
                break;
            case 'broad':
                echo '[방송]';
                break;
            case 'display':
                echo '[전광판]';
                break;
            case 'gate':
                echo '[차단기]';
                break;
            case 'alert':
                echo '[임계치]';
                break;
            case 'admin':
                echo '[계정]';
                break;
        }
        echo "({$vo->Page})";
        echo '</td>';
        echo '</tr>';

        echo '<tr>';
        echo "<th width='13%'>이벤트</th>";
        echo "<td>{$vo->EventType}</td>";
        echo '</tr>';

        echo "<tr height='150px'>";
        echo "<th width='13%'>대상</th>";
        echo '<td>';
        if ($vo->EventType == 'SMS Send') {
            $data = explode(',', $vo->equip);
            foreach ($data as $val) {
                $SMSDao = new WB_SMSUSER_DAO();
                $SMSVo = new WB_SMSUSER_VO();

                $SMSVo = $SMSDao->SELECT_SINGLE("GCode = '{$val}'");
                if ($SMSVo->{key($SMSVo)}) {
                    echo "{$SMSVo->UName}   ";
                } else {
                    echo '알 수 없음 (삭제)    ';
                }
            }
        } else {
            echo "{$vo->equip}";
        }
        echo '</td>';
        echo '</tr>';

        echo "<tr height='150px'>";
        echo "<th width='13%'>이벤트 발생 전</th>";
        echo '<td>';
        echo "{$vo->EventBefore}";
        echo '</td>';
        echo '</tr>';

        echo "<tr height='150px'>";
        echo "<th width='13%'>이벤트 발생 후</th>";
        echo '<td>';
        echo "{$vo->EventAfter}";
        echo '</td>';
        echo '</tr>';

        echo "<tr height='150px'>";
        echo "<th width='13%'>문구</th>";
        echo "<td>{$vo->EventContent}</td>";
        echo '</tr>';
        ?>
    </table>
</div>