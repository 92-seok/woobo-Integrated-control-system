<?php
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/display/server/displayStatus.php';

$num = $_GET['num'];

$sql =
    "select a.NM_DIST_OBSV, a.SizeX, a.SizeY, b.*  
            from wb_equip as a left join wb_disstatus as b
                    on a.CD_DIST_OBSV = b.CD_DIST_OBSV
            where a.CD_DIST_OBSV = '" .
    $num .
    "'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_array($res);
?>

<style>
    .cs_btn
    {
        width:fit-content;
        margin:auto;
    }
</style>
<div class="cs_frame">
    <div><font color="5490d9">◈</font> 기본정보</div>
    <input type="hidden" id="num" value="<?= $num ?>">
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
    <tr> 
        <th width="10%">장비명</th>
        <td width="40%" ><?= $row['NM_DIST_OBSV'] ?></td>
        <th width="10%">장비사이즈</th>
        <td width="40%"><?= $row['SizeX'] ?> × <?= $row['SizeY'] ?></td>
    </tr>
    
    <tr> 
        <th>장비리셋</th>
        <td><div class="cs_btn" id="disSetSend" data-type="S110">장비리셋</div></td>
        <th>시각설정</th>
        <td><div class="cs_btn" id="disSetSend" data-type="S040">시각설정</div></td>
    </tr>
    </table>
    
    <div style="margin-top:15px;"><font color="5490d9">◈</font> 파워 상태</div>
    <?php $power = explode('/', $row['Power']); ?>
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
    <tr> 
        <th width="10%">파워1</th>
        <th width="10%">파워2</th>
        <th width="10%">파워3</th>
        <th width="10%">파워4</th>
        <th width="10%">파워5</th>
        <th width="10%">파워6</th>
        <th width="10%">파워7</th>
        <th width="10%">파워8</th>
        <th width="20%">확인</th>
    </tr>
    <tr> 
        <?php for ($i = 0; $i < count($power); $i++) { ?>
        <td style="text-align: center;">
            <?php if ($power[$i] == 0) {
                echo 'OFF';
            } elseif ($power[$i] == 1) {
                echo 'ON';
            } ?>
        </td>
        <?php } ?>
        <td style="text-align: center;">
            <div class="cs_btn" id="disSetSend" data-type="S010">파워상태 확인</div>
        </td>
    </tr>
    </table>
    
    
    <div style="margin-top:15px;"><font color="5490d9">◈</font> 릴레이 상태</div>
    <?php $relay = getRelay($row['Relay']); ?>
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
    <tr> 
        <th width="20%">릴레이1</th>
        <th width="20%">릴레이2</th>
        <th width="20%">릴레이3</th>
        <th width="20%">릴레이4</th>
        <th width="20%">확인</th>
    </tr>
    <tr> 
        <td style="text-align: center;"><?= $relay[0] ?></td>
        <td style="text-align: center;"><?= $relay[1] ?></td>
        <td style="text-align: center;"><?= $relay[2] ?></td>
        <td style="text-align: center;"><?= $relay[3] ?></td>
        <td style="text-align: center;">
            <div class="cs_btn" id="disSetSend" data-type="S050">릴레이상태 확인</div>
        </td>
    </tr>
    <tr> 
        <td style="text-align: center;">
            <label><input type="radio" name="relay1" class="cs_relay1" value="ON" <?php if ($relay[0] == 'ON') {
                echo 'checked';
            } ?>>ON</label>
            <label><input type="radio" name="relay1" class="cs_relay1" value="OFF" <?php if ($relay[0] == '-' || $relay[0] == 'OFF') {
                echo 'checked';
            } ?> checked>OFF</label>
        </td>
        <td style="text-align: center;">
            <label><input type="radio" name="relay2" class="cs_relay2" value="ON" <?php if ($relay[1] == 'ON') {
                echo 'checked';
            } ?>>ON</label>
            <label><input type="radio" name="relay2" class="cs_relay2" value="OFF" <?php if ($relay[1] == '-' || $relay[1] == 'OFF') {
                echo 'checked';
            } ?> checked>OFF</label>
        </td>
        <td style="text-align: center;">
            <label><input type="radio" name="relay3" class="cs_relay3" value="ON" <?php if ($relay[2] == 'ON') {
                echo 'checked';
            } ?>>ON</label>
            <label><input type="radio" name="relay3" class="cs_relay3" value="OFF" <?php if ($relay[2] == '-' || $relay[2] == 'OFF') {
                echo 'checked';
            } ?> checked>OFF</label>
        </td>
        <td style="text-align: center;">
            <label><input type="radio" name="relay4" class="cs_relay4" value="ON" <?php if ($relay[3] == 'ON') {
                echo 'checked';
            } ?>>ON</label>
            <label><input type="radio" name="relay4" class="cs_relay4" value="OFF" <?php if ($relay[3] == '-' || $relay[3] == 'OFF') {
                echo 'checked';
            } ?> checked>OFF</label>
        </td>
        <td style="text-align: center;">
            <div class="cs_btn" id="disSetSend" data-type="S060">릴레이상태 설정</div>
        </td>
    </tr>
    </table>
    
    <div style="margin-top:15px;"><font color="5490d9">◈</font> 밝기 상태</div>
    <?php if ($row['Bright'] == '') {
        $bright = [10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10];
    } else {
        $bright = explode('/', $row['Bright']);
    } ?>
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" >
    <?php for ($i = 0; $i < 8; $i++) { ?>
    <tr> 
        <th width="16%"><?= $i + 1 ?>시</th>
        <td width="16%"style="text-align: center;">
            <select name="bright_<?= $i + 1 ?>" id="id_bright_<?= $i + 1 ?>">
                <?php for ($j = 1; $j <= 9; $j++) { ?>
                <option value="<?= $j ?>" <?php if ($bright[$i] == $j) {
    echo 'selected';
} ?>><?= $j ?></option>
                <?php } ?>
            </select>
        </td>
        
        <th width="16%"><?= $i + 9 ?>시</th>
        <td width="16%"style="text-align: center;">
            <select name="bright_<?= $i + 9 ?>" id="id_bright_<?= $i + 9 ?>">
                <?php for ($j = 1; $j <= 9; $j++) { ?>
                <option value="<?= $j ?>" <?php if ($bright[$i + 8] == $j) {
    echo 'selected';
} ?>><?= $j ?></option>
                <?php } ?>
            </select>
        </td>
        
        <th width="16%"><?= $i + 17 ?>시</th>
        <td width="16%"style="text-align: center;">
            <select name="bright_<?= $i + 17 ?>" id="id_bright_<?= $i + 17 ?>">
                <?php for ($j = 1; $j <= 9; $j++) { ?>
                <option value="<?= $j ?>" <?php if ($bright[$i + 16] == $j) {
    echo 'selected';
} ?>><?= $j ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
    <?php } ?>
    </table>
    <div style="float:right; width:100%; position:relative; font-size:12px; color:#848484">
        * 밝기 설정은 1 ~ 9 까지 가능합니다. 최대밝기는 9입니다.
        <div class="cs_btn" id="disSetSend" data-type="S020" style="position:absolute;right:0px;margin-top:10px;">밝기 설정</div>
    </div>
</div>