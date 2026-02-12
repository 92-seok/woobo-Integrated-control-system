<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/const.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';

$cdDistObsv = (string) $_GET['cdDistObsv'];
$page = $_GET['page'] ?? 1;
$now = date('Y-m-d H:i:s');

$eqp_dao = new WB_EQUIP_DAO();
$dis_dao = new WB_DISPLAY_DAO();

$eqp_vo = $eqp_dao->SINGLE("CD_DIST_OBSV = '{$cdDistObsv}'");
$viewDis_vo = $dis_dao->SELECT(
    "CD_DIST_OBSV = '{$cdDistObsv}' AND '{$now}' BETWEEN StrTime AND EndTime AND Exp_YN = 'Y'",
);
$totalCount = $dis_dao->rowCount("CD_DIST_OBSV = '{$cdDistObsv}'");

$inPage = new INSERT_PAGE($page, $totalCount, 10);
$regisDis_vo = $dis_dao->SELECT("CD_DIST_OBSV = '{$cdDistObsv}'", 'DisCode DESC', $inPage->limit[0], $inPage->limit[1]);

$ip = $eqp_vo->ConnIP;
$port = $eqp_vo->ConnPort;
$sizeX = $eqp_vo->SizeX;
$sizeY = $eqp_vo->SizeY;

/// TODO: hsyi "c16" sizeX x sizeY 별도 처리 필요한지 확인해야 함
// $isVerticalImage = ($eqp_vo->SizeX < $eqp_vo->SizeY) ? true : false;
// if ($isVerticalImage) {
//     $sizeX = $eqp_vo->SizeX * 4;
//     $sizeY = $eqp_vo->SizeY * 4;
// } else {
//     if ($eqp_vo->SizeX < 300 ) {
//         $sizeX = $eqp_vo->SizeX * 3;
//         $sizeY = $eqp_vo->SizeY * 3;
//     } else {
//         $sizeX = $eqp_vo->SizeX * 2;
//         $sizeY = $eqp_vo->SizeY * 2;
//     }
// }
?>

<div class="cs_frame">
    <div class="cs_container">
        <div class="cs_displaybox">

            <div id='id_eachscenario'>
                <h4>◈ 전광판 기본정보</h4>
                <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                    <tr align="center">
                        <?php if ($_SESSION['Auth'] === 0): ?>
                        <th class="cs_admin" width="5%">장비ID</th>
                        <th class="cs_admin" width="5%">구분</th>
                        <th class="cs_admin" width="15%">로거모델</th>
                        <?php endif; ?>
                        <th width="15%">장비명</th>
                        <th width="15%">사이즈</th>
                        <th>설치지역</th>
                        <th width="15%">IP(Port)</th>
                        <th width="15%">전원상태</th>
                    </tr>
                    <tr id='id_disList' onclick='eachScenDetail("<?= $eqp_vo->CD_DIST_OBSV ?>")'
                        style='cursor:pointer;'>
                        <?php if ($_SESSION['Auth'] === 0): ?>
                        <td class="cs_admin"><?= $eqp_vo->CD_DIST_OBSV ?></td>
                        <td class="cs_admin"><?= id2gb($eqp_vo->GB_OBSV) ?></td>
                        <td class="cs_admin"><?= $eqp_vo->ConnModel ?></td>
                        <?php endif; ?>
                        <td><?= $eqp_vo->NM_DIST_OBSV ?></td>
                        <td><?= $eqp_vo->SizeX . ' x ' . $eqp_vo->SizeY ?></td>
                        <td><?= $eqp_vo->DTL_ADRES ?></td>
                        <td><?= $eqp_vo->ConnIP . ' : ' . $eqp_vo->ConnPort ?></td>
                        <td>
                            <?php if (strtolower($eqp_vo->LastStatus) == 'ok'): ?>
                            <span style='color:blue'>정상</span>
                            <?php else: ?>
                            <span style='color:red'>점검요망</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>

            <h4 class="">◈ 표출중 시나리오 리스트</h4>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr>
                    <th width="6%">No</th>
                    <th width="34%">표시일자</th>
                    <th>내용</th>
                    <th width="20%">시나리오 종료</th>
                </tr>
                <?php
                $count = 1;
                foreach ($viewDis_vo as $vo) { ?>
                <tr style='text-align:center;'>
                    <td>
                        <?= $count++ ?>
                    </td>
                    <td>
                        <?php if ($vo->StrTime > $now) {
                            echo "<span style='color:red'>[표시대기]</span>;";
                        } ?>
                        <?= date('Y-m-d H', strtotime($vo->StrTime)) .
                            ' ~ ' .
                            date('Y-m-d H', strtotime($vo->EndTime)) ?>
                    </td>
                    <td>
                        <?php if (strpos($vo->ViewImg, 'video') !== false): ?>
                        <button
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, <?= $vo->SaveType ?>)'
                            style='cursor:pointer;border:none;background-color:rgba(0,0,0,0);'><?= $vo->ViewImg ?></button>
                        <?php else: ?>
                        <?php if ($sizeX < $sizeY): ?>
                        <img src='/<?= $vo->ViewImg ?>' style='cursor: pointer;'
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, "<?= $vo->SaveType ?>")'>
                        <?php else: ?>
                        <img src='/<?= $vo->ViewImg ?>' width='<?= $sizeX ?>' style='cursor: pointer;'
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, "<?= $vo->SaveType ?>")'>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class='cs_btn'
                            onclick='endEachScen(<?= $vo->DisCode ?>, "<?= $cdDistObsv ?>", <?= $page ?>, "<?= $ip ?>", <?= $port ?>, <?= $sizeX ?>, <?= $sizeY ?>)'
                            style='float:none; margin:auto;'>시나리오 종료</div>
                    </td>
                </tr>
                <?php }
                ?>
            </table>

            <h4>◈ 등록된 리스트</h4>

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr>
                    <th width="3%"><label><input type="checkbox" name="allCheck" id="id_allCheck"></label></th>
                    <th width="3%">No</th>
                    <th width="34%">표시일자</th>
                    <th width="">내용</th>
                    <th width="20%">표시상태</th>
                </tr>
                <?php foreach ($regisDis_vo as $vo) { ?>
                <tr style='text-align:center'>
                    <td><label><input type='checkbox' name='disChk' class='cs_disChk'
                                value='<?= $vo->DisCode ?>'></label></td>
                    <td><?= $inPage->listCnt-- ?></td>
                    <td>
                        <?php if ($vo->StrTime > $now) {
                            echo "<span style='color:red'>[표시대기]</span>";
                        } ?>
                        <?= date('Y-m-d H', strtotime($vo->StrTime)) .
                            ' ~ ' .
                            date('Y-m-d H', strtotime($vo->EndTime)) ?>
                    </td>
                    <td>
                        <?php if (strpos($vo->ViewImg, 'video') !== false): ?>
                        <button
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, "<?= $vo->SaveType ?>")'
                            style='cursor:pointer;border:none;background-color:rgba(0,0,0,0);'><?= $vo->ViewImg ?></button>
                        <?php else: ?>
                        <?php if ($eqp_vo->SizeX < $eqp_vo->SizeY): ?>
                        <img src='/<?= $vo->ViewImg ?>' style='cursor: pointer;'
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, "<?= $vo->SaveType ?>")'>
                        <?php else: ?>
                        <img src='/<?= $vo->ViewImg ?>' width='<?= $sizeX ?>' style='cursor: pointer;'
                            onclick='updEachScen(<?= $vo->DisCode ?>, "<?= $vo->CD_DIST_OBSV ?>", <?= $page ?>, "<?= $vo->SaveType ?>")'>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($vo->Exp_YN == 'Y' && $vo->StrTime <= $now && $vo->EndTime >= $now): ?>
                        <span style="color:blue">표시중</span>
                        <?php elseif ($vo->Exp_YN == 'N'): ?>
                        <span style="color:gray">수동 종료</span>
                        <?php elseif ($vo->StrTime > $now): ?>
                        <span style="color:red">표시 대기</span>
                        <?php elseif ($vo->EndTime > $now): ?>
                        <span style="color:gray">표시 종료</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php } ?>
            </table>

            <?= $inPage->inPage($page, "{$_SERVER['PHP_SELF']}?cdDistObsv={$cdDistObsv}&page=") ?>

            <div class='cs_btnBox'>
                <div class="cs_btn" onclick="addEachScen('<?= $cdDistObsv ?>', <?= $page ?>)">시나리오 추가</div>
                <div class="cs_btn" onclick="delEachScen('<?= $cdDistObsv ?>', <?= $page ?>)">시나리오 삭제</div>
            </div>

            <div id="id_helpForm">
                <div id="id_help" stat="close">
                    <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
                    <div id='id_helpMessage'> 도움말 보기</div>
                </div>
                <div class='cs_help'>
                    ◈ 전광판 기본정보<br />
                    &nbsp;- 장비명, 사이즈, IP, 주소 등 설치된 전광판의 기본 정보입니다.<br /><br />

                    ◈ 표출중 시나리오 리스트<br />
                    &nbsp;- 전광판에 현재 표출중인 내용(시나리오)입니다.<br /><br />

                    ◈ 등록된 리스트 <br />
                    &nbsp;- 이전에 전송했었던 시나리오 목록입니다.<br /><br />

                    [시나리오 추가/삭제]<br />
                    &nbsp;- 하단의 [시나리오 추가] 또는 [시나리오 삭제]를 클릭합니다.<br /><br />

                    [시나리오 수정]<br />
                    &nbsp;- ‘◈표출중 시나리오 리스트’ 또는 ‘◈등록된 리스트’의 내용(검은 화면)부분을 클릭합니다.
                </div>
            </div>

            <div class="blank" style="padding-bottom: 50px;"></div>

        </div>
    </div>
</div>