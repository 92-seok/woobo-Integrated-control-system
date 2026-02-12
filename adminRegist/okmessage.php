<?php
if (isset($_GET['type'])) {
    $type = $_GET['type'];
} else {
    $type = 'first';
}
if (isset($_GET['Idx'])) {
    $idx = $_GET['Idx'];
} else {
    echo '<script>';
    echo "alert('잘못된 접근입니다.');";
    echo '</script>';
}

if ($type != 'first') {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

    $dao = new WB_USER_DAO();
    $vo = new WB_USER_VO();

    $vo = $dao->SELECT_SINGLE("idx = '{$idx}'");
    $vo->Auth = $type;

    $dao->UPDATE($vo);

    echo '<script>';
    echo "window.opener.alert('정상적으로 처리되었습니다.');";
    echo 'window.close();';
    echo '</script>';
}
?>

<link rel="stylesheet" type="text/css" href="/css/frame.css" />
<style>
    .cs_btn
    {
        margin:unset;
        background-color: #383838;
    }
</style>
<div class='cs_btnBox'>
    <div class='cs_btn' value='admin' onclick='okmBtn(this)'>관리자</div>
    <div style='width:50px;'></div>
    <div class='cs_btn' value='guest' onclick='okmBtn(this)'>사용자</div>
    <div style='width:50px;'></div>
    <div class='cs_btn' value='close' onclick='okmBtn(this)'>취소</div>
</div>

<script>
    function okmBtn(e)
    {
        if( e.attributes["value"].value == "close" )
        {
            window.close();
        }
        else
        {
            window.location.href = `okmessage.php?type=${e.attributes["value"].value}&Idx=<?= $idx ?>`;
        }
    }
</script>