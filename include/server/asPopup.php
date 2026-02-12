<?php
if (isset($_SESSION['userIdx'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
    $userDao = new WB_USER_DAO();
    $userVo = new WB_USER_VO();
    // $userVo = $userDao->SELECT_SINGLE("Auth = 'root'");
    $userVo = $userDao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
    $userIdx = $_SESSION['userIdx'];
} else {
    session_start();
    $userIdx = $_SESSION['userIdx'];
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
}

echo "<div class='cs_pLargeTitle' value='content' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 접수내용</div>";

echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable content'>";

// 장비 선택
// 대분류
echo "<tr align='center'>";
echo '<th>장비선택</th>';
echo '</tr>';

echo "<tr align='center'>";
echo '<th>';
echo "<select id='large'>";
echo "<option value= '' selected disabled>대분류 선택</option>";
echo "<option value='all'>전체</option>";
echo "<option value='measurement'>계측센서</option>";
echo "<option value='equip'>제어장비</option>";
echo '</select>';
echo '</th>';
echo '</tr>';

// 중분류
echo "<tr align='center'>";
echo '<th>';
echo "<select id='middle'>";
echo "<option value= '' disabled selected>중분류 선택</option>";
echo '</select>';
echo '</th>';
echo '</tr>';

// 소분류
echo "<tr align='center'>";
echo '<th>';
echo "<select id='equip'>";
echo "<option value= '' disabled selected>소분류 선택</option>";
echo '</select>';
echo '</th></tr>';

// A/S 내용 선택
echo "<tr align='center'>";
echo '<th>접수내용</th>';
echo '</tr>';

echo "<tr align='center'>";
echo '<th>';
echo "<select id='content'>";
echo "<option value= '' disabled selected>접수내용 선택</option>";
echo "<option value='equip'>장비상태 오류</option>";
echo "<option value='control'>장비제어 오류</option>";
echo "<option value='data'>데이터값 오류</option>";
echo "<option value='input'>직접 입력</option>";
echo '</th>';
echo '</tr>';

echo "<tr id='input' style='height:135px;display:none;'>";
echo "<th colspan='2'><textarea id='inputContent' style='width:90%;height:90%;margin-top:2%;resize:none;' value=''></textarea></th>";
echo '</tr>';

echo '</table>';

echo "<div style='display:flex;justify-content:center;margin:15px;cursor:pointer;'>";
echo "<div id='asBtn' style='width:50px;height: 25px;background-color:#42569d;line-height:25px;text-align:center;font-size:12px;color:white;font-weight:bold;'>추가하기</div>";
echo '</div>';

echo "<div class='cs_pLargeTitle' value='list' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 접수장비</div>";
?>
<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable list'>
	<tbody id='id_asList'>
		<tr align='center'>
			<th width='15%'>NO.</th>
			<th>접수장비</th>
			<th>접수내용</th>
			<th width='15%'>제거</th>
		</tr>
	</tbody>
</table>
<?php
echo "<form action='' method='post' id='id_sendform'>";

echo "<div class='cs_pLargeTitle' value='way' stat='open' style='background-color:#f2f2f2;'>∨&nbsp&nbsp 접수방법</div>";

echo '<div>';
echo "<table width='100%' border='0' cellpadding='0' cellspacing='0' class='cs_popuptable way'>";
echo '<tr>';
echo "<th colspan='3'>접수방법</th>";
echo '</tr>';
//				echo "<tr>";
//					echo "<th width='13%'>";
//					echo "<input name='mailChk' type='checkbox' checked>";
//					echo "</th>";
//					echo "<th width='13%'>이메일</th>";
//					echo "<th>";
//					echo "<input name='email' type='textbox' value='{$userVo->uName}' style='width:90%'>";
//					echo "</th>";
//				echo "</tr>";

echo '<tr>';
echo '<th>';
echo "<input name='phoneChk' type='checkbox' checked>";
echo '</th>';
echo '<th>핸드폰</th>';
echo '<th>';
echo "<input name='phoneNum' type='textbox' value='{$userVo->uPhone}' style='width:90%'>";
echo '</th>';
echo '</tr>';

$userVo = $userDao->SELECT_SINGLE("idx = '{$userIdx}'");
echo '<tr>';
echo '<th></th>';
echo '<th>발신인</th>';
echo '<th>';
echo "<input name='from' type='textbox' style='width:90%' maxlength='10' value='{$userVo->uName}' placeholder='OO군청'>";
echo '</th>';
echo '</tr>';

echo '</table>';
echo '</div>';

echo '</form>';
echo "<div style='display:flex;justify-content:center;margin:15px;cursor:pointer;'>";
echo "<div id='sendBtn' style='width:50px;height:25px;background-color:#42569d;line-height:25px;text-align:center;font-size:12px;color:white;font-weight:bold;'>접수하기</div>";
echo '</div>';

