<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$groupDao = new WB_ISUALERTGROUP_DAO();
$listDao = new WB_ISULIST_DAO();

$groupVo = new WB_ISUALERTGROUP_VO();
$listVo = new WB_ISULIST_VO();

$groupVo = $groupDao->SELECT();
$countRec = count($groupVo);
?>
<div class="cs_frame">
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
        <tr align="center">
            <th width="3%">no</th>
            <th>경보이름</th>
            <th width="10%">자동여부</th>
            <th width="15%">경보상태</th>
            <th width="15%">경보제어</th>
        </tr>

        <?php
        $count = 1;
        foreach ($groupVo as $v) {
            if ($v->AltUse == 'Y') {
                $c = '사용';
            } else {
                $c = '사용안함';
            }

            echo "<tr align='center' id='id_alerList' data-num='{$v->GCode}' data-type='upd' style='cursor:pointer;'>";
            echo "<td>{$count}</td>";
            echo "<td style='text-align: left; padding-left:10px;'>{$v->GName}</td>";
            echo "<td>{$c}</td>";

            $listVo = $listDao->SELECT_SINGLE("GCode = '{$v->GCode}'", 'IsuCode Desc');
            if (isset($listVo->IsuCode)) {
                $level = '';
                $color = '';
                if ($listVo->IStatus == 'm-start' || $listVo->IStatus == 'start' || $listVo->IStatus == 'ing') {
                    if ($listVo->IsuKind == 'level1') {
                        $level = '1단계';
                        $color = '#2359c4';
                    } elseif ($listVo->IsuKind == 'level2') {
                        $level = '2단계';
                        $color = '#01b56e';
                    } elseif ($listVo->IsuKind == 'level3') {
                        $level = '3단계';
                        $color = '#f7c415';
                    } elseif ($listVo->IsuKind == 'level4') {
                        $level = '4단계';
                        $color = '#da3539';
                    }
                }

                if ($listVo->IStatus == 'm-start') {
                    echo "<td><span style='color:{$color};font-weight: bold;'>{$level} 발령대기</span></td>";
                    echo "<td><div class='cs_btn' id='id_startBtn' data-num='{$listVo->IsuCode}' data-type='alert' style='float:none;margin-top:0px;width:85px;margin-left:0px;border-radius:26px;background-color:{$color};padding:5px;'>경보발령</div></td>";
                }
                // else if( $listVo->IStatus == "start" || $listVo->IStatus == "ing" )
                // {
                // 	echo "<td><span style='color:{$color};font-weight: bold;'>{$level} 발령중</span></td>";
                // 	echo "<td><div class='cs_btn' id='id_endBtn' data-num='{$listVo->IsuCode}' data-type='alert' style='float:none;margin-top:0px;width:85px;margin-left:0px;border-radius:26px;background-color:{$color};padding:5px;'>경보발령종료</div></td>";
                // }
                elseif ($listVo->IStatus == 'start' || $listVo->IStatus == 'ing') {
                    echo "<td><span style='color:{$color};font-weight: bold;'>{$level} 발령중</span></td>";
                    echo "<td><div class='' id='' data-num='{$listVo->IsuCode}' data-type='alert' style='float:none;margin-top:0px;width:85px;margin-left:0px;border-radius:26px;background-color:{$color};padding:5px;'>경보발령중</div></td>";
                } elseif ($listVo->IStatus == 'end' || $listVo->IStatus == '') {
                    echo "<td><span style='color:blue;'>정상</span></td>";
                    echo '<td>-</td>';
                }
            } else {
                echo "<td><span style='color:blue;'>정상</span></td>";
                echo '<td>-</td>';
            }

            echo '</tr>';
            $count++;
        }
        ?>
    </table>

    <div class='cs_btnBox' style="justify-content:flex-end;">
        <div class="cs_btn" id="id_alerList" data-num="" data-type="ins">경보 추가</div>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            - 경보발령과 관련된 내용(경보발령조건, 동작장비 등)을 그룹화합니다.<br />
            - 경보그룹이 없다면, 상단의 ‘임계값설정’으로 이동합니다.<br /><br />

            <font class="cs_helpIcon">●</font> 경보이름<br />
            &nbsp;- 추가한 경보그룹의 이름입니다.<br />
            <font class="cs_helpIcon">●</font> 자동여부<br />
            &nbsp;- 사 용 : 경보그룹설정시, 담당자 승인여부를 ‘자동승인’으로 설정<br />
            &nbsp;&nbsp;→ 임계치 조건 도달시, 담당자의 승인없이 자동으로 경보가 발령됩니다.<br />
            &nbsp;- 사용안함 : 경보그룹설정시, 담당자 승인여부를 ‘수동승인’으로 설정<br />
            &nbsp;&nbsp;→ 임계치 조건 도달시, 담당자에게 SMS만 전송되며 상단의 ‘경보수동제어’에서 수동으로 발령해야합니다.<br />
            <font class="cs_helpIcon">●</font> 경보상태<br />
            &nbsp;- 경보가 발령되지 않으면 ‘정상’, 경보발령시 ‘경보발령중’으로 표시됩니다.<br />
            <font class="cs_helpIcon">●</font> 경보제어<br />
            &nbsp;- 경보가 발령되지 않으면 ‘-’, 경보발령시 [경보발령종료]를 클릭하여 경보상태를 제어할 수 있습니다.<br />
        </div>
    </div>
</div>