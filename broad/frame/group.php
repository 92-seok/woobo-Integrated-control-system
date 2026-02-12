<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
?>
<style>
    /* CSS Document */
	#id_equip
	{
		width: 100%;	
		height:90%;
		font-size: 14px;
		text-align: center;
		line-height:350px;
		color: #777;
	}

    .cs_datatable td
    {
        height:35px;
    }

    #id_save_group_btn
    {
        width: 15%;
        height: 100%;
        display: inline-block;
        line-height: 35px;
        border-left: 1px solid #cfcfcf;
        font-weight: bold;
        background-color: #36b8f4;
        color: #fff;
        margin: auto;
        margin-right: 0px;
    }
</style>
<div class="cs_frame">
	<div class="cs_container" style="height:90%;"> 
		<div class="cs_broadbox" style="width:35%">
            <div>◈ 그룹관리</div>
            <div style="float:right;font-size:14px;">*그룹명 더블클릭시 그룹명 변경이 가능합니다.</div>
            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
                <tr>
                    <th width="85%" style="border:none;">그룹명</th>
                    <th style="border:none;"></th>
                </tr>
                <?php
                $gSql = 'select * from wb_brdgroup where 1';
                $gRes = mysqli_query($conn, $gSql);
                while ($gRow = mysqli_fetch_array($gRes)) {
                    echo "<tr style='cursor:pointer;' value='" . $gRow['GCode'] . "' beforeName='{$gRow['GName']}'>";
                    echo "<td class='cs_trList' value='" . $gRow['GCode'] . "'>" . $gRow['GName'] . '</td>';
                    echo "<th id='id_delete_group_btn' value='" . $gRow['GCode'] . "' style='padding-left:0px;'>삭제</th>";
                    echo '</tr>';
                }
                ?>
                <tr>
                    <td width="85%"><input type="text" style="width:91%; height:20px;"></td>
                    <th id='id_insert_group_btn' style='cursor:pointer; padding-left:0px;'>추가</th>
                </tr>
            </table>
		</div>		 
        
        <div class="cs_broadbox" style="width:50%">
            <div>◈ 그룹별 장비관리</div>
            <div id="id_equip">
                <div style="width:100%; height:100%; background-color:#f7f7f7; margin-top:10px;">그룹을 선택해주세요.<br></div>
            </div>
        </div>
    </div>
    <div class="cs_btnBox">
        <div class="cs_btn" id="id_group_save" style="width:200px;">저장</div>
    </div>
    <div id="id_helpForm" style="width:92%;margin:2% auto;">
        <div id="id_help" stat="close">
			<div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
			<div id='id_helpMessage'> 도움말 보기</div>
		</div>
		<div class='cs_help'>
            ◈ 그룹관리<br/>
            <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 그룹 추가<br/></font>
            <font class="cs_smallfont">&nbsp;&nbsp;- 빈 칸에 방송그룹의 이름(예.지역명)을 입력하고 [추가]를 클릭합니다.<br/></font>
            <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 그룹 삭제<br/></font>
            <font class="cs_smallfont">&nbsp;&nbsp;- 그룹 우측의 [삭제]를 클릭합니다.<br/></font>
            <font class="cs_smallfont">&nbsp;<font class="cs_helpIcon">●</font> 그룹명 수정<br/></font>
            <font class="cs_smallfont">&nbsp;&nbsp;- 그룹명을 더블클릭합니다.<br/></font>
            <font class="cs_smallfont">&nbsp;&nbsp;- 수정 후 [저장]을 클릭합니다.<br/><br/></font>
            ◈ 그룹별 장비관리<br/>
            &nbsp;- 그룹을 선택하고, 그룹에 포함시킬 방송장비를 선택한 후 하단의 [저장]을 클릭합니다.<br/>
		</div>
	</div>
</div>