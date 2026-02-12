<?php
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$getText = $_GET['dType'];
$explode = explode('/', $getText);
$type = $explode[0];
$equip = $explode[1];

$dis = '';
$num = '';
$name = '';
$ip = '';
$phone = '';
$latlon = '';
$add = '';
$xSize = '';
$ySize = '';
$connType = '';
$connModel = '';
$err = '';
$GB = '';
$date = '';
$stat = '';
$rain = '';
$see = '';
$sub = '';
$det = '';
$ds = '';
$use = '';

if ($type == 'upds') {
    $dis = 'disabled';
} elseif ($type == 'upd') {
    $sql = "SELECT * FROM wb_equip WHERE CD_DIST_OBSV IN ({$equip})";
    $res = mysqli_query($conn, $sql);
    $count = mysqli_num_rows($res);
    if (0 < $count) {
        while ($row = mysqli_fetch_assoc($res)) {
            $num = "{$row['CD_DIST_OBSV']}";
            $name = "{$row['NM_DIST_OBSV']}";
            if (empty($row['ConnIP']) && empty($row['ConnPort'])) {
                $ip = '';
            } else {
                $ip = "{$row['ConnIP']}:{$row['ConnPort']}";
            }
            $phone = "{$row['ConnPhone']}";
            if (empty($row['LAT']) && empty($row['LON'])) {
                $latlon = '';
            } else {
                $latlon = "{$row['LAT']}, {$row['LON']}";
            }
            $add = "{$row['DTL_ADRES']}";
            $xSize = "{$row['SizeX']}";
            $ySize = "{$row['SizeY']}";
            $connType = "{$row['ConnType']}";
            $connModel = "{$row['ConnModel']}";
            $err = "{$row['ErrorChk']}";
            $GB = "{$row['GB_OBSV']}";
            $date = "{$row['LastDate']}";
            $stat = "{$row['LastStatus']}";
            $rain = "{$row['RainBit']}";
            $see = "{$row['SeeLevelUse']}";
            $sub = "{$row['SubOBCount']}";
            $det = "{$row['DetCode']}";
            $ds = "{$row['DSCODE']}";
            $use = "{$row['USE_YN']}";
        }
    }
}
?>
<div class="cs_frame">
    <form name="date" id="id_form" method="post" action="">
        <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
            <tr>
                <th width="9%"><span class="required-label">장비번호ID *</span><sup class="required-sup">필수</sup></th>
                <td width="38%"><input class="input-fixed" type="text" maxlength="25" name="equip0" <?= $dis ?>
                        value="<?= $num ?>" <?= $dis ?>>
                    숫자로만 적어주세요</td>
                <th width="9%"><span class="required-label">장비이름 *</span><sup class="required-sup">필수</sup></th>
                <td><input class="input-fixed" type="text" maxlength="25" name="equip1" <?= $dis ?>
                        value="<?= $name ?>">
                </td>
            </tr>
            <tr>
                <th>장비주소</th>
                <td>
                    <div class="input-wrapper">
                        <input type="text" maxlength="25" name="equip2" <?= $dis ?> value="<?= $ip ?>">
                        예) 20.200.9.2:4096<br />
                        <라우터 IP 할당 규칙>
                            <div class="input-description">
                                .1 게이트웨이 .2 강우로거 .3 수위로거 .4 침수로거 .5 전광판PC<br />
                                .6 적설로거,차단기PC1 .7 차단기PC2 .8 차단기로거 .9 변위로거
                            </div>
                    </div>
                </td>
                <th>CDMA번호 (ConnPhone)</th>
                <td><input type="text" maxlength="11" name="equip3" <?= $dis ?> value="<?= $phone ?>">
                    예) 01200000000</td>
            </tr>
            <tr>
                <th><span class="required-label">위경도 (LAT,LON) *</span><sup class="required-sup">필수</sup></th>
                <td><input type="text" name="equip4" value="<?= $latlon ?>" placeholder="">예) 37.43342,127.1735</td>
                <th>주소</th>
                <td><input type="text" name="equip5" value="<?= $add ?>" style="width:80%;"></td>
            </tr>
            <tr>
                <th>전광판 X Size</th>
                <td><input type="text" maxlength="5" name="equip6" value="<?= $xSize ?>">예) 320</td>
                <th>전광판 Y Size</th>
                <td><input type="text" maxlength="5" name="equip7" value="<?= $ySize ?>">예) 64</td>
            </tr>
            <tr>
                <th>ConnType</th>
                <td><input type="text" maxlength="25" name="equip8" value="<?= $connType ?>"></td>
                <th>ConnModel</th>
                <td><input type="text" maxlength="25" name="equip9" value="<?= $connModel ?>"></td>
            </tr>
            <tr>
                <th>ErrorChk</th>
                <td><input type="text" maxlength="1" name="equip10" value="<?= $err ?>"> 0~5 </td>
                <th><span class="required-label">장비구분 *<sup class="required-sup">필수</sup><br>GB_OBSV</span></th>
                <td>
                    <div class="input-wrapper">
                        <input style="width:40px" type="text" maxlength="2" name="equip11" value="<?= $GB ?>">
                        예) 01~08, 17~21, LP, CC
                        <div class="input-description">
                            01 (강우 rain), 02 (수위water), 03 (변위 dplace), 04 (함수비 soil), 06 (적설 snow), 08 (경사tilt)<br />
                            [비표준] 17 (방송), 18 (전광판), 19 (CCTV), 20 (차단기), 21 (침수감지기), LP (LPR), CC (CCTV)
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>LastDate</th>
                <td><input type="text" maxlength="19" name="equip12" value="<?= $date ?>"
                        placeholder="yyyy-mm-dd hh:mm:ss">마지막 통신 시간 (yyyy-mm-dd hh:mm:ss)</td>
                <th>LastStatus</th>
                <td><input type="text" maxlength="4" name="equip13" value="<?= $stat ?>">
                    예) "OK", "Fail"</td>
            </tr>
            <tr>
                <th>RainBit</th>
                <td><input type="text" maxlength="5" name="equip14" value="<?= $rain ?>"></td>
                <th>SeeLevelUse</th>
                <td><input type="text" maxlength="1" name="equip15" value="<?= $see ?>"></td>
            </tr>
            <tr>
                <th>SubOBCount</th>
                <td><input type="text" maxlength="2" name="equip16" value="<?= $sub ?>"></td>
                <th>DetCode</th>
                <td><input type="text" maxlength="2" name="equip17" value="<?= $det ?>"></td>
            </tr>
            <tr>
                <th>DSCode</th>
                <td><input type="text" maxlength="10" name="equip18" value="<?= $ds ?>"></td>
                <th><span class="required-label">USE_YN *</span><sup class="required-sup">필수</sup></th>
                <td><input type="text" maxlength="1" name="equip19" value="<?= $use ?>">장비사용유무 (1은 사용, 0은 미사용)
                </td>
            </tr>
        </table>
        <input type="hidden" name="equip20" value="<?= $equip ?>">
        <input type="hidden" name="equip21" value="<?= $type ?>">
    </form>

    <div style="float:right;">
        <div class="cs_btn" id="id_map">지도</div>
        <div class="cs_btn" id="id_chgBtn"><?= $type == 'add' ? '추가' : '수정' ?></div>
        <div class="cs_btn" id="id_delBtn">삭제</div>
    </div>
</div>