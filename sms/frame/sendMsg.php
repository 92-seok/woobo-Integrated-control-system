<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php'; ?>
<div class="cs_frame">
    <div class="cs_smsBox">
        <div>
            <font color="#2b3280">◈</font> 수신자선택
        </div>
        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
            <tr>
                <th width="8%"><input type="checkbox" id="id_allCheck"></th>
                <th width="23%">부서명</th>
                <th>직책</th>
                <th>별칭</th>
                <th>연락처</th>
            </tr>
            <?php
            include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
            $addr_sql = 'select * from wb_smsuser';
            $addr_res = mysqli_query($conn, $addr_sql);
            while ($addr_row = mysqli_fetch_assoc($addr_res)) {

                if (strlen($addr_row['Phone']) == 11) {
                    $pcnt = 4;
                } else {
                    $pcnt = 3;
                }

                if (strpos($addr_row['Phone'], '-')) {
                    $phone_number = $addr_row['Phone'];
                } else {
                    if (strlen($addr_row['Phone']) == 10) {
                        $phone_number = substr($addr_row['Phone'], 0, 3) . '-' . substr($addr_row['Phone'], 3, 3) . '-' . substr($addr_row['Phone'], 6, 4);
                    } else {
                        $phone_number = substr($addr_row['Phone'], 0, 3) . '-' . substr($addr_row['Phone'], 3, 4) . '-' . substr($addr_row['Phone'], 7, 4);
                    }
                }
                ?>
            <tr>
                <td><input type="checkbox" class="cs_smsChk" value="<?= $addr_row['GCode'] ?>"></td>
                <td><?= $addr_row['Division'] ?></td>
                <td><?= $addr_row['UPosition'] ?></td>
                <td><?= $addr_row['UName'] ?></td>
                <td><?= $phone_number ?></td>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <div class="">
        <div style="display:flex;justify-content:space-between; margin-top: 20px;">
            <div>
                <font color="#2b3280">◈</font> 메세지 입력
            </div>
            <div style="font-size:14px;">[<span id="id_subByte">0</span>/<span id="totalByte">70</span>(글자)]</div>
        </div>
        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th style="width:30%; height:40px; font-size:14px">제목</th>
                <td><input type="text" size="10" maxlength="30" id="id_smstitle" autocomplete="off"
                        style="height:30px; width:97%;"></td>
            </tr>
            <tr style="height:275px;">
                <td colspan='2'>
                    <textarea id="id_content" class="content" cols="50" rows="10"
                        style="resize:none; width:98%; height:90%; margin-top:10px; border:1px solid #c7c7c7;"></textarea>
                </td>
            </tr>
        </table>

        <div style="float: right; margin-top:15px;">
            <div class="cs_btn" id="id_sendBtn">전송</div>
        </div>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            ◈ 수신자 선택<br />
            &nbsp;- SMS를 전송받을 수신자를 선택합니다.<br />
            &nbsp;- 수신자는 상단의 ‘연락처관리’에서 추가 또는 수정할 수 있습니다.<br /><br />

            ◈ 메세지 입력<br />
            &nbsp;- 보낼 문자의 제목과 내용을 입력합니다.<br />
            &nbsp;- 제목은 상단의 ‘발송내역’에 기록됩니다.<br /><br />
        </div>
    </div>
</div> <!-- cs_frame -->