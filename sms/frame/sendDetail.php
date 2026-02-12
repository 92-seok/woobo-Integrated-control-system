<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$num = $_GET['num'];

$sql = "select * from wb_smslist where SCode = '" . $num . "'";
$res = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($res);
?>
<div class="cs_frame"> <!-- 발송내역 (Detail) -->
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="rows">
    	<tr>
        	<th width="200">제목</th>
            <td colspan="3" align="left" style="padding-left:10px;"><?= $row['SMSTitle'] ?></td>
        </tr>
        <tr>
            <th style="height:10px">발송시간</th>                    
            	<td colspan="3" align="left" style="padding-left:10px;"><?= $row['SMSDate'] ?></td>                               
        </tr>
        <tr>	
            <th colspan="2">수신인</th>
            <th colspan="2">내용</th>
        </tr>
		<tr>
        	<td style="overflow-y:auto; padding:0px;height:130px; border-right:1px solid #d9d9d9" colspan="2">
            	<div style="margin-top:-65px;">
                	<table cellpadding="0" cellspacing="0" width="100%" rules="rows" style="border-bottom:1px solid #d9d9d9">
						<tr>
                        	<th width="20%">별칭</th>
							<th width="20%">번호</th>
                            <th width="20%">전송상태</th>
							<th width="20%">처리시간</th>
							<th width="20%">재전송</th>
                        </tr>   
                        <?php
                        $explode = explode(',', $row['GCode']);

                        for ($i = 0; $i < count($explode); $i++) {

                            $who_sql =
                                "select b.UName, a.MsgCode, a.PhoneNum, a.sendStatus, a.RetDate from wb_sendMessage a 
										left join wb_smsuser b on a.phoneNum = b.Phone 
										where a.SCode = '" .
                                $row['SCode'] .
                                "' and b.GCode = '" .
                                $explode[$i] .
                                "'";
                            $who_res = mysqli_query($conn, $who_sql);
                            $who_row = mysqli_fetch_assoc($who_res);
                            if ($who_row < 1) {
                                $who_row = [];
                                $who_row['UName'] = '알 수 없음<br/>(유저 삭제)';
                                $who_row['PhoneNum'] = '알 수 없음<br/>(유저 삭제)';
                                $who_row['sendStatus'] = 'Delete';
                                $who_row['RetDate'] = '알 수 없음<br/>(정보 삭제)';
                            }
                            ?>              
                        <tr>
                        	<td><?= $who_row['UName'] ?></td>
                            <td><?= $who_row['PhoneNum'] ?></td>
                            <td>
							<?php if ($who_row['sendStatus'] == 'OK') {
           echo '성공';
       } elseif ($who_row['sendStatus'] == 'ing') {
           echo '발송중';
       } else {
           echo '실패';
       } ?>
                            </td>
							<td style="padding:0px 10px"><?= $who_row['RetDate'] ?></td>
							<?php if ($who_row['sendStatus'] != 'Delete') {
           echo "<td style='padding:0px 10px'><div class='cs_btn' id='id_retry' data-code='{$who_row['MsgCode']}' data-num='{$num}' style='width: 60%;margin-top: 0px;margin-left: 0px;height: 30%;border-radius: 10px;padding: 8px; line-height:7px;'>재전송</div></td>";
       } else {
           echo '<td></td>';
       } ?>
                        </tr>
                        <?php
                        }
                        ?>
                    </table> 
                </div>
            </td>
            <td colspan="2">
				<div class="box" style="text-align: left; padding: 10px;  box-sizing: border-box;height: 100%;width: 100%;">
					<?= nl2br($row['SMSContent']) ?>
                </div>
            </td>                              
        </tr>
    </table>
</div>