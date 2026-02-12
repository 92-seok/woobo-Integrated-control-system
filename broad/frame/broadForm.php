<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

if (isset($_GET['dType'])) {
    $type = $_GET['dType'];
} else {
    $type = 'normal';
}
if (isset($_GET['num'])) {
    $num = $_GET['num'];
} else {
    $num = '1';
}

$equip_dao = new WB_EQUIP_DAO();
$brdlist_dao = new WB_BRDLIST_DAO();
$brdgroup_dao = new WB_BRDGROUP_DAO();
$brdalert_dao = new WB_BRDALERT_DAO();
$brdment_dao = new WB_BRDMENT_DAO();

$equip_vo = new WB_EQUIP_VO();
$brdlist_vo = new WB_BRDLIST_VO();
$brdgroup_vo = new WB_BRDGROUP_VO();
$brdment_vo = new WB_BRDMENT_VO();

if ($type == 'replay') {
    $brdlist_vo = $brdlist_dao->SELECT("BCode = '{$num}'");
} else {
    $brdlist_vo->BCode = $num;
}

$equipList = explode(',', $brdlist_vo->CD_DIST_OBSV);
?>
<div class="cs_frame">
    <div class="cs_container" style="flex-direction:column; justify-content:flex-start;align-items:flex-start;">
        <div class="cs_broadbox" style="margin-top:5px;">
            <div class=''>
                <div>◈ 그룹 선택 </div>
            </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th width="90%">그룹명</th>
                </tr>

                <tr class="cs_groupChk active" id="id_groupChk" value='all' style='cursor:pointer;'>
                    <td style="font-weight:bold;">전&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;체</td>
                </tr>
                <?php
                $brdgroup_vo = $brdgroup_dao->SELECT();
                foreach ($brdgroup_vo as $v) {
                    echo "<tr class='cs_groupChk' id='id_groupChk' style='cursor:pointer;' value='{$v->GCode}'>";
                    echo "<td>{$v->GName}</td>";
                    echo '</tr>';
                }
                ?>
            </table>
        </div>

        <div class="cs_broadbox" id="id_equip" style="margin-top:15px;">
            <div class='cs_titleInGoogleIcon'>
                <div>◈ 장비 선택 </div>
            </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr align="center">
                    <th width="5%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
                    <?php if ($_SESSION['Auth'] === 0): ?>
                    <th width="" class="cs_admin">DSCODE</th>
                    <th width="" class="cs_admin">LastStatus</th>
                    <th width="" class="cs_admin">ConnType</th>
                    <th width="" class="cs_admin">ConnPhone</th>
                    <th width="" class="cs_admin">Data</th>
                    <?php endif; ?>
                    <th width="10%">장비명</th>
                    <th width="20%">주소</th>
                    <th width="20%">전화번호</th>
                    <th width="10%">상태</th>
                </tr>
                <?php
                $equip_vo = $equip_dao->SELECT("GB_OBSV = '17' and USE_YN = '1'");
                foreach ($equip_vo as $v) {
                    if (in_array($v->CD_DIST_OBSV, $equipList)) {
                        $chk = 'checked';
                    } else {
                        $chk = '';
                    }

                    // 전화번호에 '-'가 있을 경우 그대로 표출 그 외 10글자는 "3-3-4", 11글자는 "3-4-4" 표출
                    if (strpos($v->ConnPhone, '-')) {
                        $phone_number = $v->ConnPhone;
                    } else {
                        if (strlen($v->ConnPhone) == 10) {
                            $phone_number = substr($v->ConnPhone, 0, 3) . '-' . substr($v->ConnPhone, 3, 3) . '-' . substr($v->ConnPhone, 6, 4);
                        } else {
                            $phone_number = substr($v->ConnPhone, 0, 3) . '-' . substr($v->ConnPhone, 3, 4) . '-' . substr($v->ConnPhone, 7, 4);
                        }
                    }

                    // Equip Table LastStatus에서 상태 정보 가져오기!
                    if (strtolower($v->LastStatus) == 'ok') {
                        $state = "<span style='color:blue'>정상</span>";
                    } elseif (strtolower($v->LastStatus) == 'ing') {
                        $state = "<span style='color:blue'>전송 중</span>";
                    } elseif (strtolower($v->LastStatus) == 'fail') {
                        $state = "<span style='color:red'>점검요망</span>";
                    } else {
                        $state = "<span style='color:gray'>알수 없음</span>";
                    }

                    echo '<tr> ';
                    echo "<td><input type='checkbox' name='equipChk' class='cs_brdChk' value='{$v->CD_DIST_OBSV}' {$chk}>";
                    if ($_SESSION['Auth'] === 0):
                        echo "<div class='cs_admin'>";
                    else:
                        echo '<div>';
                    endif;
                    // echo "{$v->CD_DIST_OBSV}";
                    echo '</div>';
                    echo '</td>';
                    if ($_SESSION['Auth'] === 0):
                        echo "<td class='cs_admin' style='text-align:left; padding-left:10px;'>{$v->DSCODE}</td>";
                        echo "<td class='cs_admin' style='text-align:left; padding-left:10px;'>{$v->LastStatus}</td>";
                        echo "<td class='cs_admin' style='text-align:left; padding-left:10px;'>{$v->ConnType}</td>";
                        echo "<td class='cs_admin' style='text-align:left; padding-left:10px;'>{$v->ConnPhone}</td>";
                        echo "<td class='cs_admin' style='text-align:left; padding-left:10px;'>{$v->Data}</td>";
                    endif;
                    echo "<td>{$v->NM_DIST_OBSV}</td>";
                    echo "<td>{$v->DTL_ADRES}</td>";
                    echo "<td>{$phone_number}</td>";
                    echo "<td>{$state}</td>";
                    echo '</tr>';
                }
                ?>
            </table>
        </div>

        <div class="cs_broadbox" style="margin-top:15px;">
            <div class='cs_titleInGoogleIcon'>
                <div>◈ 상세 내용 </div>
            </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows"
                style="text-align:left;">
                <tr>
                    <th align="center" width="20%">방송 제목</th>
                    <td>
                        <input type="text" name="title" id="id_title" maxlength="50" value="<?= $brdlist_vo->Title ?>"
                            style="height:20px; width:95%; border:1px solid #d9d9d9; margin-left:5px;">
                    </td>
                </tr>
                <tr>
                    <th align="center" rowspan="2">방송 시간</th>
                    <td style="height:35px">
                        <input type="radio" name="tType" value="general" id="id_tType" checked> 즉시방송
                        <input type="radio" name="tType" value="reserve" id="id_tType"> 예약방송
                    </td>
                </tr>
                <tr>
                    <td style="height:35px">
                        <input type="text" name="sDate" class="cs_sDate" id="id_sDate"
                            style="width:100px; height:20px;margin-left:5px;"
                            value="<?= date('Y-m-d', strtotime('+3 hours')) ?>" disabled>

                        <select name="sTime" class="cs_sTime" id="id_sTime" style="width:40px; height:25px;" disabled>
                            <?php for ($i = 0; $i < 24; $i++) {
                                if ($i < 10) {
                                    $print = '0' . $i;
                                } else {
                                    $print = $i;
                                } ?>
                            <option value="<?= $print ?>" <?php if ($i == date('G', strtotime('+3 hours'))) {
    echo 'selected';
} ?>><?= $print ?></option>
                            <?php
                            } ?>
                        </select> 시

                        <select name="sMin" class="cs_sMin" id="id_sMin" style="width:40px; height:25px;" disabled>
                            <?php for ($i = 0; $i < 60; $i = $i + 5) {
                                if ($i < 10) {
                                    $print = "0{$i}";
                                } else {
                                    $print = $i;
                                }

                                echo "<option value='{$print}'>{$print}</option>";
                            } ?>
                        </select> 분
                    </td>
                </tr>
                <tr>
                    <th align="center">방송 횟수</th>
                    <td>
                        <select name="repeat" id="id_repeat"
                            style="width:97%; height:20px; border:1px solid #d9d9d9;margin-left:5px;">
                            <?php for ($i = 1; $i < 10; $i++) {
                                $sel = '';
                                $repeat = $brdlist_vo->BRepeat;
                                if ($repeat == $i) {
                                    $sel = 'selected';
                                }
                                echo "<option value='" . $i . "' " . $sel . '>' . $i . '회</option>';
                            } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th align="center" rowspan="2">방송 종류</th>
                    <td style="height:35px">
                        <select name="bType" id="id_bType"
                            style="width:97%; height:20px; border:1px solid #d9d9d9;margin-left:5px;">
                            <option value="tts" <?php if ($brdlist_vo->BrdType == '' || $brdlist_vo->BrdType == 'tts') {
                                echo 'selected';
                            } ?>>TTS 방송</option>
                            <option value="alert" <?php if ($brdlist_vo->BrdType == 'alert') {
                                echo 'selected';
                            } ?>>예경보 방송</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="height:35px">
                        <select name="bMent" id="id_bMent"
                            style="width:97%; height:20px; border:1px solid #d9d9d9;margin-left:5px;">
                            <?php
                            if ($brdlist_vo->BrdType == '' || $brdlist_vo->BrdType == 'tts') {
                                $brdment_vo = $brdment_dao->SELECT("BUse = 'ON'");
                                echo '<option value="" selected>직접입력</option>';
                            } elseif ($brdlist_vo->BrdType == 'alert') {
                                $brdment_vo = $brdalert_dao->SELECT();
                            }

                            foreach ($brdment_vo as $v) {
                                if (!empty($v->AltCode)) {
                                    $ment = $v->AltCode;
                                    if ($ment == $v->AltCode) {
                                        // $sel = 'selected';
                                    }

                                    echo "<option value='{$v->AltCode}' {$sel}>{$v->Title}</option>";
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding:10px;">
                        <textarea name="content" class="cs_content" id="id_content"
                            style="border:1px solid #d9d9d9"><?= $brdlist_vo->TTSContent ?></textarea>
                    </td>
                </tr>
            </table>
            <div class="cs_btnBox">
                <div class="cs_btn" id="id_addBtn">방송하기</div>
            </div>
        </div>

        <div class="cs_broadbox" style="margin-top:15px;display:none;">
            <div>◈ CID 입력</div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr>
                    <th width="20%">CID</th>
                    <td width="80%" align="left" style="padding-left:10px;"><input type="text" name="cid" id="id_cid"
                            value="" maxlength="13" style="height:20px; width:95%; border:1px solid #d9d9d9"></td>
                </tr>
            </table>
            <div class="cs_btnBox">
                <div class="cs_btn" id="id_ins_cid_Btn">CID 등록</div>
            </div>
        </div>

        <div id="id_helpForm">
            <div id="id_help" stat="close">
                <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
                <div id='id_helpMessage'> 도움말 보기</div>
            </div>
            <div class='cs_help'>
                ◈ 그룹선택<br /></font>
                - 방송그룹을 선택합니다.<br /></font>
                - 그룹이 없다면 [전체] 또는 상단의 ‘그룹관리’로 이동하여 그룹을 설정합니다.<br /><br /></font>
                ◈ 장비선택<br /></font>
                - 방송할 장비(들)을 선택합니다.<br /><br /></font>
                ◈ 상세내용<br /></font>
                - 방송내용을 선택합니다.<br /><br /></font>
                <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 방송제목<br /></font>
                <font class="cs_smallfont">&nbsp;&nbsp;- 방송내역에 기록되는 제목입니다.<br /></font>
                <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 방송시간<br /></font>
                <font class="cs_smallfont">&nbsp;&nbsp;- 즉시방송 또는 예약방송을 선택합니다.<br /></font>
                <font class="cs_smallfont">&nbsp;&nbsp;- 예약방송시, 방송할 날짜와 시간을 선택합니다.<br /></font>
                <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 방송횟수<br /></font>
                <font class="cs_smallfont">&nbsp;&nbsp;- 최대 9회까지 설정할 수 있습니다.<br /></font>
                <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 방송종류<br /></font>
                <font class="cs_smallfont">&nbsp;&nbsp;- TTS 방송 : 직접 방송 내용을 입력하거나 상단의 ‘멘트관리’에서 사전에 추가한 내용을 선택합니다.<br />
                </font>
                <font class="cs_smallfont">&nbsp;&nbsp;- 예경보방송 : 호우주의보, 대설주의보 등 예경보 상황에 대한 주의 멘트를 선택합니다.</font>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function(e) {
    $("#id_sDate").datepicker({
        dateFormat: "yy-mm-dd",
        minDate: "<?= date('Y-m-d', time()) ?>",
        dayNamesMin: ["일", "월", "화", "수", "목", "금", "토"],
        monthNames: ["1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월"],
        showMonthAfterYear: true
    });

    var type = "<?= $type ?>";
    if (type == "cidsave") {
        document.getElementsByClassName("cs_broadbox").item(2).style.display = "none";
        document.getElementsByClassName("cs_broadbox").item(3).style.display = "block";
        document.querySelector("#id_helpForm").style.display = "none";
    }
});
</script>