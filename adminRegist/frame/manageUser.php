<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_USER_DAO();
$vo = new WB_USER_VO();

// $vo = $dao->SELECT("Auth != 'root' OR Auth IS NULL");
$vo = $dao->SELECT();
?>

<div class="cs_frame">

    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
        <tr style="background-color:#383838;">
            <th width="3%">no</th>
            <th>idx</th>
            <th>아이디</th>
            <th>별칭</th>
            <th>연락처</th>
            <th>등록날짜</th>
            <th>구분</th>
        </tr>
        <?php
        $count = 0;

        foreach ($vo as $v) {

            $count++;

            if (strpos($v->uPhone, '-')) {
                $phone_number = $v->uPhone;
            } else {
                if (strlen($v->uPhone) == 10) {
                    $phone_number =
                        substr($v->uPhone, 0, 3) . '-' . substr($v->uPhone, 3, 3) . '-' . substr($v->uPhone, 6, 4);
                } else {
                    $phone_number =
                        substr($v->uPhone, 0, 3) . '-' . substr($v->uPhone, 3, 4) . '-' . substr($v->uPhone, 7, 4);
                }
            }
            ?>
        <tr id='id_addadminBtn' data-num='<?= $v->idx ?>' title='계정 정보 수정' style='cursor:pointer;'>
            <td><?= $count ?></td>
            <td><?= $v->idx ?></td>
            <td><?= $v->uId ?></td>
            <td><?= $v->uName ?></td>
            <td><?= $phone_number ?></td>
            <td><?= $v->RegDate ?></td>

            <td>
                <?php if ($v->Auth == 'admin'): ?>
                관리자
                <?php elseif ($v->Auth == 'root'): ?>
                최고관리자
                <?php elseif ($v->Auth == 'guest'): ?>
                사용자
                <?php else: ?>
                <div class='cs_btn' data-num='{$v->idx}' style='margin:auto;width:30%;line-height:0.5;'>승인</div>
                <?php endif; ?>
            </td>
            <?php
        }
        ?>
    </table>
    <div class='cs_btnBox' style="justify-content:flex-end;">
        <div class="cs_btn" id="id_addadminBtn" data-num="-1">계정 추가</div>
    </div>