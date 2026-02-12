<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$groupDao = new WB_ISUALERTGROUP_DAO();
$criDao = new WB_ISUALERT_DAO();
$equipDao = new WB_EQUIP_DAO();
$smsDao = new WB_SMSUSER_DAO();
$parkGroupDao = new WB_PARKGATEGROUP_DAO();

$groupVo = new WB_ISUALERTGROUP_VO();
$criVo = new WB_ISUALERT_VO();
$equipVo = new WB_EQUIP_VO();
$smsVo = new WB_SMSUSER_VO();
$parkGroupVo = new WB_PARKGATEGROUP_VO();

$type = $_GET['type'] ?? '';
$c = '';

if ($type == 'upd') {
    $groupVo->GCode = $_GET['num'];

    $groupVo = $groupDao->SELECT_SINGLE("GCode = '{$groupVo->GCode}'");
    $criCheck = explode(',', $groupVo->AltCode);
    for ($i = 1; $i <= 4; $i++) {
        $equip[$i] = explode(',', $groupVo->{"Equip{$i}"});
        $sms[$i] = explode(',', $groupVo->{"SMS{$i}"});
        $flood[$i] = explode(',', $groupVo->{"FloodSMSAuto{$i}"});
    }
} else {
    $criCheck = [];
    for ($i = 1; $i <= 4; $i++) {
        $equip[$i] = [];
        $sms[$i] = [];
        $flood[$i] = [];
    }
}
?>
<div class="cs_frame">
    <form action="../server/alertSave.php" method="post" id="id_form">
        <input type="hidden" name="num" value="<?= $groupVo->GCode ?>">
        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
            <tr>
                <th width="10%">경보 자동사용</th>
                <td align="left" style="text-indent:10px;">
                    <input type="radio" name="AltUse" class='cs_use' value="Y"
                        <?php if ($groupVo->AltUse == 'Y' || $groupVo->AltUse == '') {
                            echo 'checked';
                        } ?>> 사용
                    <input type="radio" name="AltUse" class='cs_use' value="N"
                        <?php if ($groupVo->AltUse == 'N') {
                            echo 'checked';
                        } ?>> 사용안함
                </td>
            </tr>
        </table>

        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
            <tr>
                <th width="10%">경보 이름</th>
                <td align="left" style="text-indent:10px;">
                    <input type="text" name="GName" value="<?= $groupVo->GName ?>" id="id_title"
                        style="border:1px solid #d9d9d9; width:95%; height:20px;">
                </td>
            </tr>
        </table>

        <div style="margin-top:20px;">◈ 임계치</div>
        <div class="alertList" style="position:relative;">
            <div class="cs_blockBox" <?php if ($groupVo->AltUse == 'N') {
                echo "style='display:block;'";
            } ?>>경보 수동 사용 중
                입니다. </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr align="center">
                    <th width="5%"><label><input type="checkbox" name="criCheckAll" id="criCheckAll"></label></th>
                    <th width="15%">장비명</th>
                    <th width="20%">1단계</th>
                    <th width="20%">2단계</th>
                    <th width="20%">3단계</th>
                    <th width="20%">4단계</th>
                </tr>
                <?php
                $criVo = $criDao->SELECT();
                foreach ($criVo as $v) {
                    $equType = strtolower($v->EquType);

                    echo "<tr align='center'>";

                    if (in_array($v->AltCode, $criCheck)) {
                        $c = 'checked';
                    } else {
                        $c = '';
                    }
                    echo "<td><label><input type='checkbox' name='AltCode' value='{$v->AltCode}' {$c}></label></td>";

                    echo "<td style='text-align: left; padding-left:10px;'>";
                    if ($equType != 'news') {
                        $eName = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'");
                        if (!isset($eName->NM_DIST_OBSV)) {
                            $eName->NM_DIST_OBSV = '장비를 알 수 없음';
                        }
                    }

                    switch ($equType) {
                        case 'news':
                            echo '[특보] 기상청 예보';
                            break;
                        case 'rain':
                            echo "[강우] {$eName->NM_DIST_OBSV}";
                            echo "({$v->RainTime}시간)";
                            break;
                        case 'water':
                            echo "[수위] {$eName->NM_DIST_OBSV}";
                            break;
                        case 'dplace':
                            echo "[변위] {$eName->NM_DIST_OBSV}";
                            break;
                        case 'flood':
                            echo "[침수] {$eName->NM_DIST_OBSV}";
                            break;
                        case 'soil':
                            echo "[함수비] {$eName->NM_DIST_OBSV}";
                            break;
                        case 'tilt':
                            echo "[경사] {$eName->NM_DIST_OBSV}";
                            break;
                        case 'snow':
                            echo "[침수] {$eName->NM_DIST_OBSV}";
                            break;
                    }
                    echo '</td>';

                    for ($i = 1; $i <= 4; $i++) {
                        echo "<td style='padding: 5px; text-align:left;'>";
                        switch ($equType) {
                            case 'news':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    $news = explode(',', $v->{"L{$i}Std"});
                                    $r = [];
                                    if (in_array('20', $news)) {
                                        array_push($r, '호우주의보');
                                    }
                                    if (in_array('21', $news)) {
                                        array_push($r, '태풍주의보');
                                    }
                                    if (in_array('70', $news)) {
                                        array_push($r, '호우경보');
                                    }
                                    if (in_array('71', $news)) {
                                        array_push($r, '태풍경보');
                                    }

                                    echo implode(',', $r);
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'rain':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    echo "{$v->{"L{$i}Std"}} mm";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'water':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    // $val = $v->{"L{$i}Std"} / 1000;
                                    $val = number_format($v->{"L{$i}Std"} / 1000, 1);
                                    echo "{$val} M";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'dplace':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    $dplace = explode('/', $v->{"L{$i}Std"});

                                    echo "[누적] {$dplace[0]} mm<br>";
                                    echo "[속도] {$dplace[1]} mm/일";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'soil':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    echo "{$v->{"L{$i}Std"}} %";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'snow':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    $val = $v->{"L{$i}Std"} / 10;
                                    echo "{$val} M";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'tilt':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    echo "{$v->{"L{$i}Std"}} °";
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;

                            case 'flood':
                                if (strtolower($v->{"L{$i}Use"}) == 'on') {
                                    if ($v->{"L{$i}Std"} == '1') {
                                        echo '5 Cm';
                                    } elseif ($v->{"L{$i}Std"} == '2') {
                                        echo '13 Cm';
                                    } elseif ($v->{"L{$i}Std"} == '3') {
                                        echo '21 Cm';
                                    }
                                } else {
                                    echo "<span style='color:gray'>미사용</span>";
                                }
                                break;
                        }
                        echo '</td>';
                    }
                }
                ?>
            </table>
        </div>

        <div class="alertList" style="position:relative;">
            <div class="cs_blockBox" <?php if ($groupVo->AltUse == 'N') {
                echo "style='display:block;'";
            } ?>>경보 수동 사용 중
                입니다. </div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
                <tr>
                    <th width="10%"></th>
                    <th width="22%">1단계</th>
                    <th width="22%">2단계</th>
                    <th width="22%">3단계</th>
                    <th width="22%">4단계</th>
                </tr>

                <tr>
                    <th height="235px">동작장비</th>
                    <?php for ($i = 1; $i <= 4; $i++) {
                        echo "<td style='padding:10px;text-align:left;text-indent:20px;'>";
                        echo "<div class='tableBox'>";

                        $equipVo = $equipDao->SELECT("GB_OBSV IN ('17', '18', '20') AND USE_YN = '1'", 'GB_OBSV ASC, NM_DIST_OBSV');
                        foreach ($equipVo as $v) {
                            if (in_array($v->CD_DIST_OBSV, $equip[$i])) {
                                $c = 'checked';
                            } else {
                                $c = '';
                            }

                            echo '<div>';
                            echo "<input type='checkbox' name='Equip{$i}' value='{$v->CD_DIST_OBSV}' {$c}>";
                            echo '<span>';

                            echo '[';

                            if ($v->GB_OBSV == '17') {
                                echo '예경보';
                            } elseif ($v->GB_OBSV == '18') {
                                echo '전광판';
                            } elseif ($v->GB_OBSV == '20') {
                                echo '차단기';
                            }

                            echo "] {$v->NM_DIST_OBSV}<br>";

                            echo "<font style='color:#777; margin-left:50px;'>{$v->DTL_ADRES}</font>";
                            echo '</span>';
                            echo '</div>';
                        }

                        echo '</td>';
                    } ?>
                </tr>

                <tr>
                    <th>sms전송</th>
                    <?php for ($i = 1; $i <= 4; $i++) {
                        echo "<td style='padding:10px; text-align:left; text-indent:20px;'>";
                        echo "<div class='tableBox'>";

                        $smsVo = $smsDao->SELECT();
                        foreach ($smsVo as $v) {
                            if (in_array($v->GCode, $sms[$i])) {
                                $c = 'checked';
                            } else {
                                $c = '';
                            }

                            echo "<div style='margin-bottom:3px;'>";
                            echo "<input type='checkbox' name='SMS{$i}' value='{$v->GCode}' {$c}>";
                            echo "[{$v->Division}-{$v->UName}] {$v->Phone}";
                            echo '</div>';
                        }

                        echo '</div>';
                        echo '</td>';
                    } ?>
                </tr>

                <?php
                $equipVo = $equipDao->SELECT("GB_OBSV IN ('20') AND USE_YN = '1'", 'NM_DIST_OBSV');
                if ($equipVo[0]->{key($equipVo[0])}) {
                    $parkGroupVo = $parkGroupDao->SELECT();

                    echo '<tr>';
                    echo '<th>주차장 그룹</th>';
                    for ($i = 1; $i <= 4; $i++) {
                        echo "<td style='padding:10px; text-align:left; text-indent:20px;'>";
                        echo "<div class='tableBox'>";

                        foreach ($parkGroupVo as $v) {
                            if (in_array($v->ParkGroupCode, $flood[$i])) {
                                $c = 'checked';
                            } else {
                                $c = '';
                            }

                            echo "<div style='margin-bottom:3px;'>";
                            echo "<input type='checkbox' name='FloodSMS{$i}' value='{$v->ParkGroupCode}' {$c}>";
                            echo "[{$v->ParkGroupName}] {$v->ParkGroupAddr}";
                            echo '</div>';
                        }

                        echo '</div>';
                        echo '</td>';
                    }
                    echo '</tr>';
                }
                ?>

                <tr>
                    <th>담당자 승인여부</th>
                    <?php for ($i = 1; $i <= 4; $i++) {
                        if (strtolower($groupVo->{"Auto{$i}"}) == 'on') {
                            $c = 'checked';
                        } else {
                            $c = '';
                        }

                        echo '<td>';
                        echo "<input type='radio' id='id_auto' name='Auto{$i}' value='on' {$c}> 자동승인";

                        // 경보 추가시에 담당자 승인여부 기본값은 수동승인으로 한다
                        if ($type === 'ins') {
                            $c = 'checked';
                        } else {
                            if (strtolower($groupVo->{"Auto{$i}"}) == 'off') {
                                $c = 'checked';
                            } else {
                                $c = '';
                            }
                        }

                        echo "<input type='radio' id='id_auto' name='Auto{$i}' value='off' {$c}> 수동승인";
                        echo '</td>';
                    } ?>
                </tr>

                <tr>
                    <th>담당자 연락처</th>
                    <td colspan="4" style="padding:10px; text-align:left; text-indent:10px;">
                        <input type="text" value="<?= $groupVo->AdmSMS ?>" name="AdmSMS" id="id_admSMS"
                            style="border:1px solid #d9d9d9; height:20px; width:70%;">
                        <div class="info" style="margin-top:10px;">
                            ※ 담당자 승인여부가 "수동승인"인경우 입력된 연락처로 SMS알림이 전송됩니다.<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;여러명 입력시 ','로 구분해 입력하시면 됩니다.<br>
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;ex) 01012345678, 010-1234-5678, 010 1234
                            5678
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </form>

    <div class="cs_btnBox" style="justify-content:center;">
        <?php if ($type == 'upd') {
            echo "<div class='cs_btn' id='id_groupAlertsaveBtn' data-type='upd'>수 정</div>";
            echo "<div class='cs_btn' id='id_groupAlertsaveBtn' data-type='del'>삭 제</div>";
        } else {
            echo "<div class='cs_btn' id='id_groupAlertsaveBtn' data-type='ins'>추 가</div>";
            echo "<div class='cs_btn' id='id_groupAlertsaveBtn' data-type='can'>취 소</div>";
        } ?>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            <font class="cs_helpIcon">●</font> 경보 자동사용<br />
            &nbsp;- 추가한 경보그룹을 사용할지 여부를 선택합니다.<br />
            &nbsp;- ‘사용안함’ 선택시, 경보는 발령되지 않습니다.<br />
            <font class="cs_helpIcon">●</font> 경보 이름<br />
            &nbsp;- 경보그룹의 이름을 입력합니다.<br /><br />

            ◈ 임계치<br />
            &nbsp;- 경보발령의 기준이 되는 임계치를 선택합니다.<br />
            &nbsp;- 선택한 단계별 임계치에 데이터값이 도달했을 때, 경보가 발령됩니다.<br />
            &nbsp;- 임계치는 상단의 ‘임계값설정’에서 추가/수정할 수 있습니다.<br /><br />

            &nbsp;<font class="cs_helpIcon">●</font> 동작장비<br />
            &nbsp;&nbsp;- 경보발령시 동작할 장비를 단계별로 선택합니다.<br />
            &nbsp;<font class="cs_helpIcon">●</font> SMS전송<br />
            &nbsp;&nbsp;- 경보발령시 문자를 전송받을 연락처를 선택합니다.<br />
            &nbsp;&nbsp;- 연락처는 ‘SMS관리’탭의 ‘연락처관리’에서 추가/수정할 수 있습니다.<br />
            &nbsp;<font class="cs_helpIcon">●</font> 담당자 승인여부<br />
            &nbsp;&nbsp;- 자동승인 : 임계치 조건 도달시, 담당자의 승인없이 자동으로 경보가 발령됩니다.<br />
            &nbsp;&nbsp;- 수동승인 : 임계치 조건 도달시, 담당자에게 SMS만 전송되며 경보발령은 수동으로 해야합니다.<br />
            &nbsp;<font class="cs_helpIcon">●</font> 담당자 연락처<br />
            &nbsp;&nbsp;- 담당자 승인여부를 ‘수동승인’으로 선택한 경우, 연락처를 입력합니다.<br />
            &nbsp;&nbsp;- 경보발령조건 충족시, 입력한 연락처로 SMS가 발송됩니다.
        </div>
    </div>
</div>