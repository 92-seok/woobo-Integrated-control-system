<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

$num = $_GET['num'];
$page = $_GET['page'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$listdao = new WB_BRDLIST_DAO();
$listvo = new WB_BRDLIST_VO();

$listvo = $listdao->SELECT_SINGLE("BCode = '{$num}'");
?>
<div class="cs_frame">
    <div>◈ 상세내용</div>
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
        <tr>
            <th width="10%">제목</th>
            <td colspan="7" align="left" style="padding-left:10px;"><?= $listvo->Title ?></td>
        </tr>
        <tr>
            <th>방송시간</th>
            <td align="left" style="padding-left:10px;">
                <?php if ($listvo->BType == 'reserve') {
                    if ($listvo->RevType == 'reserve') {
                        echo date('Y-m-d H:i', strtotime($listvo->BrdDate)) . " <span style='color:red;font-weight:bold;'>[예약중]</span>";
                    } else {
                        echo date('Y-m-d H:i', strtotime($listvo->BrdDate)) . " <span style='color:blue;font-weight:bold;'>[방송완료]</span>";
                    }
                } else {
                    echo date('Y-m-d H:i', strtotime($listvo->RegDate));
                } ?>
            </td>
            <th>방송타입</th>
            <td width="15%" align="left" style="padding-left:10px;">
                <?php if ($listvo->BType == 'general') {
                    echo '일반방송';
                } elseif ($listvo->BType == 'reserve') {
                    echo '예약방송';
                } elseif ($listvo->BType == 'provin') {
                    echo '도청방송';
                } elseif ($listvo->BType == 'kiot') {
                    echo 'KIOT방송';
                } elseif ($listvo->BType == 'level1') {
                    echo '1단계 경보';
                } elseif ($listvo->BType == 'level2') {
                    echo '2단계 경보';
                } elseif ($listvo->BType == 'level3') {
                    echo '3단계 경보';
                } elseif ($listvo->BType == 'level4') {
                    echo '4단계 경보';
                } ?>
            </td>
            <th width="10%">방송횟수</th>
            <td width="15%" align="left" style="padding-left:10px;"><?= $listvo->BRepeat ?> 회</td>
            <th width="10%">방송종류</th>
            <td width="15%" align="left" style="padding-left:10px;">
                <?php if ($listvo->BrdType == 'tts') {
                    echo 'tts 방송';
                } elseif ($listvo->BrdType == 'alert') {
                    $alertdao = new WB_BRDALERT_DAO();
                    $alertvo = new WB_BRDMENT_VO();

                    $alertvo = $alertdao->SELECT("AltCode = '{$listvo->AltMent}'");
                    echo "예경보 방송 [{$alertvo[0]->Title}]";
                } ?>
            </td>
        </tr>
        <tr>
            <th colspan="4" style="text-align:center">방송 장비</th>
            <th colspan="4" style="text-align:center">방송 내용</th>
        </tr>
        <tr>
            <td colspan="4" style="vertical-align: top; height: 100%; padding: 0px;">
                <div style="width: 100%; height: 100%;  overflow-y:auto;">
                    <table border="1" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows"
                        style="margin:0px; box-shadow:none;">
                        <tr align="center">
                            <th width="20%">장비명</th>
                            <th width="20%">번호</th>
                            <th width="20%">전송상태</th>
                            <th width="20%">처리시간</th>
                            <th width="20%">재전송</th>
                        </tr>
                        <?php
                        $sql = "SELECT a.*, b.NM_DIST_OBSV, b.ConnPhone, c.RetDate FROM wb_brdlistdetail as a left join wb_equip as b
						on a.CD_DIST_OBSV = b.CD_DIST_OBSV left join wb_brdsend as c on a.BCode = c.Parm4 WHERE BCode = '{$listvo->BCode}' GROUP BY CD_DIST_OBSV";

                        $row = $listdao->SELECT_QUERY($sql);
                        foreach ($row as $v) {
                            if (strpos($v['ConnPhone'], '-')) {
                                $phone_number = $v['ConnPhone'];
                            } else {
                                if (strlen($v['ConnPhone']) == 10) {
                                    $phone_number = substr($v['ConnPhone'], 0, 3) . '-' . substr($v['ConnPhone'], 3, 3) . '-' . substr($v['ConnPhone'], 6, 4);
                                } else {
                                    $phone_number = substr($v['ConnPhone'], 0, 3) . '-' . substr($v['ConnPhone'], 3, 4) . '-' . substr($v['ConnPhone'], 7, 4);
                                }
                            }

                            if ($v['BrdStatus'] == 'start') {
                                $stat = '대기중';
                            } elseif ($v['BrdStatus'] == 'ing') {
                                $stat = '방송중';
                            } elseif ($v['BrdStatus'] == 'end') {
                                // $stat = '방송완료';
                                $stat = "<span style='color:green'>방송완료</span>";
                            } else {
                                // $stat = '방송오류';
                                $stat = "<span style='color:red'>방송오류</span>";
                            }

                            echo "<tr align='center'>";
                            echo "<td style='padding:0px 10px;'>{$v['NM_DIST_OBSV']}</td>";
                            echo "<td style='padding:0px 10px;'>{$phone_number}</td>";
                            echo "<td style='padding:0px 10px;'>{$stat}</td>";
                            echo "<td style='padding:0px 10px'>{$v['RetDate']}</td>";
                            echo "<td style='padding:0px 10px'>";
                            echo "<div class='cs_btn' id='id_retry' data-num='{$v['CD_DIST_OBSV']}' data-page='{$page}' data-parm='{$v['BCode']}' ";
                            echo "style='width: 60%;margin-top: 0px;margin-left: 0px;height: 30%;border-radius: 10px;padding: 8px; line-height:7px;'>재전송</div>";
                            echo '</td>';
                            echo '</tr>';
                        }
                        ?>
                    </table>
                </div>
            </td>
            <td colspan="4" style="text-align:left; vertical-align: top; padding: 20px; border:1px solid #d9d9d9;">
                <?= nl2br($listvo->TTSContent) ?></td>
        </tr>
    </table>

    <div class="cs_btnBox">
        <div class="cs_btn" id="id_replay" data-num="<?= $num ?>" style="float:none; margin-right: 10px;">다시 방송하기</div>
        <div class="cs_btn" id="id_delBtn" style="float:none;" data-num="<?= $page ?>" data="<?= $num ?>">삭 제</div>
    </div>
</div>