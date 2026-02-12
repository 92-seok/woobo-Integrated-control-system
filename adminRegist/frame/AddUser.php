<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['idx'])) {
    $idx = $_GET['idx'];
} else {
    echo "<script>alert('잘못된 접근 방식입니다.')</script>";
    echo "<script>window.location.replace('/main.php')</script>";
}
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_USER_DAO();
$vo = new WB_USER_VO();

$type = 'insert';

if ($idx > 0) {
    $where = "idx = $idx";
    $vo = $dao->SELECT_SINGLE($where);
    $type = 'update';
    $sIdx = $_GET['sIdx'];
}
?>

<style>
.cs_datatable td {
    text-align: left;
    padding-left: 15px;
}
</style>

<div class="cs_frame">
    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
        <tr>
            <th width="20%">아이디</th>
            <td><input type="text" name="id" id="id" maxlength="20" value=<?= $vo->uId ?>> * 최소 5자에서 최대 20자. * 특수문자를
                입력하실 수 없습니다.</td>
        </tr>
        <tr>
            <th>비밀번호</th>
            <td><input type="password" autocomplete="off" maxlength="20" name="pwd" id="pwd"> * 영문/숫자/특수문자를 조합한 최소 8자 이상
                입력해주세요.</td>
        </tr>
        <tr>
            <th>비밀번호 확인</th>
            <td><input type="password" autocomplete="off" maxlength="20" name="pwdc" id="pwdc"></td>
        </tr>
        <tr>
            <th>별칭</th>
            <td><input type="text" name="uname" id="uname" maxlength="10" value="<?= $vo->uName ?>"> * 지역명으로 적으시면 A/S접수
                시 위치 확인이 더욱 수월합니다.</td>
        </tr>
        <tr>
            <th>전화번호</th>
            <td><input type="text" onKeyup="this.value=this.value.replace(/[^-0-9]/g,'');" maxlength="25" name="uphone"
                    id="uphone" maxlength="20" value="<?= $vo->uPhone ?>"> * 휴대폰 번호만 입력해 주세요.(긴급상황 SMS알림)</td>
        </tr>
        <tr>
            <th>관리 등급</th>
            <td>
                <select name="auth" id="id_auth">
                    <option value="" <?php if ($vo->Auth == '') {
                        echo 'selected';
                    } ?> disabled hidden>관리 등급을 선택하세요.
                    </option>
                    <option value="admin" <?php if ($vo->Auth == 'admin') {
                        echo 'selected';
                    } ?>>관리자</option>
                    <option value="guest" <?php if ($vo->Auth == 'guest') {
                        echo 'selected';
                    } ?>>사용자</option>
                </select> * 사용자는 데이터,보고서 확인만 가능합니다.
            </td>
        </tr>
        <?php
        echo '<tr>';
        echo '<th>접속 허용 IP<br/>(사용 여부)</th>';
        echo '<td>';
        echo "<div style='display:flex;justify-content:flex-start;align-items:center;'>";
        echo "<input type='text' maxlength='20' name='ip' id='ip' value='{$vo->ip}' " .
            ($vo->ipUse == 'Y' ? '' : 'disabled') .
            '>';
        echo "<div style='width:20px'></div>";
        echo "<div class='cs_useToggle " . ($vo->ipUse == 'Y' ? 'on' : 'off') . "' id='id_useToggle'>";
        echo "<div class='cs_toggleBtn " . ($vo->ipUse == 'Y' ? 'on' : 'off') . "' id='id_toggleBtn'></div>";
        echo '</div>';
        echo "<div style='width:20px'></div>";
        echo " * IP 입력시 숫자 대신 '*'을 입력하면 해당 대역대의 접근이 가능합니다.";
        echo '</div>';
        echo '</td>';
        echo '</tr>';

        if ($type == 'insert') {
            echo '<tr>';
            echo '<th>주의사항</th>';
            echo '<td> * 아이디 생성 후 최초 접속시 IP가 등록됩니다.</td>';
            echo '</tr>';
        }
        ?>
        <input type="hidden" name="idx" id="idx" value="<?= $idx ?>">
        <input type="hidden" name="saveType" id="saveType" value="<?= $type ?>">
    </table>
    <div class='cs_btnBox'>
        <?php if ($idx > 0) { ?>
        <div class="cs_btn" id="id_adminBtn" onclick="regBtn()">정보 수정</div>
        <?php if ($sIdx != $idx) { ?>
        <div class="cs_btn" id="id_deladminBtn" data-num="<?= $idx ?>">계정 삭제</div>
        <?php }} else { ?>
        <div class="cs_btn" id="id_adminBtn" onclick="regBtn()">계정 등록</div>
        <?php } ?>
    </div>
</div>
</div>
<script>
$(document).ready(function() {
    let type = "<?= $type ?>";
    if (type == "update") {
        $("#id").prop("disabled", true);
        $(".cs_authtype").prop("disabled", true);
    }
});
</script>