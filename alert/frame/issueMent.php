<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php'; ?>
<style>
textarea {
    resize: none;
    width: 100%;
    height: 80px;
    padding: 5px 8px;
    line-height: 1.5em;

    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -ms-box-sizing: border-box;
    -o-box-sizing: border-box;
    -webkit-box-sizing: border-box;
}

.cs_frame {
    height: auto;
    padding-bottom: 30px;
}

textarea {
    border: 1px solid #d9d9d9;
}
</style>

<div class="cs_frame">
    <?php
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

    $dao = new WB_ISUMENT_DAO();
    $vo = new WB_ISUMENT_VO();

    $vo = $dao->SELECT(1);
    ?>
    <form action="" method="post" id="id_form">
        <input type="hidden" name="MentCode" id="id_MentCode" value="<?= $vo->MentCode ?>" />
        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
            <tr>
                <th width="10%">경보방송 1단계</th>
                <td><textarea name="broad1" id="id_broad1"><?= $vo->BrdMent1 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">경보방송 2단계</th>
                <td><textarea name="broad2" id="id_broad2"><?= $vo->BrdMent2 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">경보방송 3단계</th>
                <td><textarea name="broad3" id="id_broad3"><?= $vo->BrdMent3 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">경보방송 4단계</th>
                <td><textarea name="broad4" id="id_broad4"><?= $vo->BrdMent4 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">SMS 1단계</th>
                <td><textarea name="SMS1" id="id_SMS1"><?= $vo->SMSMent1 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">SMS 2단계</th>
                <td><textarea name="SMS2" id="id_SMS2"><?= $vo->SMSMent2 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">SMS 3단계</th>
                <td><textarea name="SMS3" id="id_SMS3"><?= $vo->SMSMent3 ?></textarea></td>
            </tr>
            <tr>
                <th width="10%">SMS 4단계</th>
                <td><textarea name="SMS4" id="id_SMS4"><?= $vo->SMSMent4 ?></textarea></td>
            </tr>
        </table>
    </form>
    <div class="cs_btnBox">
        <div class="cs_btn" id="id_saveMentBtn" style="float:none; margin: 10px auto;">저 장</div>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            - 경보방송 1~4단계 : 단계별 경보발령시, 동작할 방송장비의 방송 멘트입니다.<br />
            - SMS 1~4단계 : 단계별 경보발령시, 수신인에게 전송될 SMS 멘트입니다.<br />
        </div>
    </div>
</div>