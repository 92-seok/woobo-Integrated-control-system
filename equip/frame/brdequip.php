<?php
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
?>
<style>
	.cs_datatable td
	{
		margin-top:5px;
	}
</style>

<div class="cs_frame">
	<div class="cs_container" style="height:100%;"> 
		<div class="cs_broadbox" style="width:20%">
			<div>◈ 그룹 선택</div>
			<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
				<tr>
					<th width="90%">그룹명</th>
				</tr>

				<tr class="cs_groupChk active" id="id_groupChk" value='all' style='cursor:pointer;'>
					<td style="font-weight:bold;">전&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;체</td>
				</tr>
				<?php
    $gSql = 'select * from wb_brdgroup where 1';
    $gRes = mysqli_query($conn, $gSql);
    while ($gRow = mysqli_fetch_array($gRes)) {
        echo "<tr class='cs_groupChk' id='id_groupChk' style='cursor:pointer;' value='" . $gRow['GCode'] . "'>";
        echo '<td>' . $gRow['GName'] . '</td>';
        echo '</tr>';
    }
    ?>
			</table>
		</div>
		
		<div class="cs_broadbox" id="id_equip" style="width:40%">
			<div>◈ 장비 선택</div>
			<table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
				<tr align="center"> 
					<th width="10%"><input type="checkbox" name="allCheck" id="id_allCheck"></th>
					<th width="35%">장비명</th>
					<th width="35%">전화번호</th>
					<th width="20%">상태</th>
				</tr>
				<?php
    $eSql = "select * from wb_equip as a where GB_OBSV = '17' and USE_YN = '1'";
    $eRes = mysqli_query($conn, $eSql);
    while ($eRow = mysqli_fetch_array($eRes)) {
        if (strpos($eRow['ConnPhone'], '-')) {
            $phone = $eRow['ConnPhone'];
        } else {
            if (strlen($eRow['ConnPhone']) == 10) {
                $phone = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 3) . '-' . substr($eRow['ConnPhone'], 6, 4);
            } else {
                $phone = substr($eRow['ConnPhone'], 0, 3) . '-' . substr($eRow['ConnPhone'], 3, 4) . '-' . substr($eRow['ConnPhone'], 7, 4);
            }
        } ?>
				<tr> 
					<td><input type="checkbox" name="equipChk" class="cs_eChk" value="<?= $eRow['CD_DIST_OBSV'] ?>"></td>
					<td style="text-align:left; padding-left:10px;"><?= $eRow['NM_DIST_OBSV'] ?></td>
					<td><?= $phone ?></td>
					<td>
						<?php
      if (strtolower($eRow['LastStatus']) == 'ok') {
          echo "<span style='color:blue'>정상</span>";
      }
      if (strtolower($eRow['LastStatus']) == 'ing') {
          echo "<span style='color:blue'>점검중</span>";
      } elseif (strtolower($eRow['LastStatus']) == 'fail') {
          echo "<span style='color:red'>점검요망</span>";
      }
      ?>
					</td>
				</tr>
				<?php
    }
    ?>
			</table>
		</div>

		<div class="cs_broadbox" style="width:35%; background-color:#fff;">
            <div>◈ 상세내용</div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr align="center"> 
                    <th width="20%" style="border:1px solid #d9d9d9">볼륨설정</th>
                    <td>
                        <select id="vType">
                            <option value="MIC" selected>마이크</option>
                            <option value="LTI">LTE In</option>
                            <option value="AXI">Line In</option>
                            <option value="AXO">Line Out</option>
                            <!-- <option value="PLY">녹음파일재생</option>
                            <option value="MON">모니터링</option> -->
                        </select>
                    </td>
                    <td>
                        <input type="range" id="volume" min="0" max="6" step="1" value="0">
                    </td>
                    <th width="20%" class="tBtn" id="brdSetSend" data-type="S080">저장</td>
                </tr>

                <tr align="center"> 
                    <th style="border:1px solid #d9d9d9">출력설정</th>
                    <td>
                        <input type="checkbox" class="output" value="MO"> 모니터링
                        <input type="checkbox" class="output" value="RF"> RF 송신
                    </td>
                    <td>
                        <input type="checkbox" class="output" value="LO"> Line Out
                        <input type="checkbox" class="output" value="RO"> Relay Out
                    </td>
                    <th class="tBtn" id="brdSetSend" data-type="S100">저장</td>
                </tr>

                <tr align="center"> 
                    <th style="border:1px solid #d9d9d9">릴레이 설정</th>
                    <td>
                        릴레이1&nbsp;&nbsp;
                        <select id="relay1">
                            <option value="ON" checked>ON</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>    
                    <td>
                        릴레이2&nbsp;&nbsp;
                        <select id="relay2">
                            <option value="ON" checked>ON</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </td>
                    <th class="tBtn" id="brdSetSend" data-type="S120">저장</td>
                </tr>

                <tr align="center"> 
                    <th style="border:1px solid #d9d9d9">차임횟수 설정</th>
                    <td>
                        시작시&nbsp;&nbsp;
                        <select id="sBell">
                            <?php for ($i = 0; $i <= 4; $i++) { ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                            <?php } ?>
                        </select>&nbsp;&nbsp;회
                    </td>
                    <td>
                        종료시&nbsp;&nbsp;
                        <select id="eBell">
                            <?php for ($i = 0; $i <= 4; $i++) { ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                            <?php } ?>
                        </select>&nbsp;&nbsp;회
                    </td>
                    <th class="tBtn" id="brdSetSend" data-type="S140">저장</td>
                </tr>
            </table>

            <div calss="cs_btnbox" style="width:100%;">
                <div class="cs_btn" id="brdSetSend" data-type="S170">장비 상태 확인</div>
                <div class="cs_btn" id="brdSetSend" data-type="S060">시각동기화</div>
                <div class="cs_btn" id="brdSetSend" data-type="S180">장비 재부팅</div>
            </div>
        </div>
    </div> 
</div>