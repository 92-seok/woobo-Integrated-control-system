<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$topMenuDao = new DAO_T();
$topMenuVo = new WB_EQUIP_VO();
$menuArr = [];
$firstCode = $_SESSION['firstCode'];

$topMenuVo = $topMenuDao->SELECT_QUERY("SELECT DISTINCT GB_OBSV as GB FROM wb_equip WHERE USE_YN = '1'");
foreach ($topMenuVo as $v) {
    array_push($menuArr, $v['GB']);
}
if (count($menuArr) == 0) {
    array_push($menuArr, '01');
}
?>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_data">

    <!-- //강우데이터 메뉴 표출 여부 -->
    <?php if (in_array('01', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="rain">강우데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="rain"># 지역별강우</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="rain"># 일별강우</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="rain"># 월별강우</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="rain"># 연별강우</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="rain"># 기간별강우</div>
        </div>
    </div>
    <?php } ?>

    <!-- //수위데이터 메뉴 표출 여부 -->
    <?php if (in_array('02', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="water">수위데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="water"># 지역별수위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="water"># 일별수위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="water"># 월별수위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="water"># 연별수위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="water"># 기간별수위</div>
        </div>
    </div>
    <?php } ?>

    <!-- //변위데이터 메뉴 표출 여부 -->
    <?php if (in_array('03', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="dplace">변위데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="dplace"># 지역별변위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="dplace"># 일별변위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="dplace"># 월별변위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="dplace"># 연별변위</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="dplace"># 기간별변위</div>
        </div>
    </div>
    <?php } ?>

    <!-- // 함수비데이터 메뉴 표출 여부 -->
    <?php if (in_array('04', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="soil">함수비데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="soil"># 지역별함수비</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="soil"># 일별함수비</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="soil"># 월별함수비</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="soil"># 연별함수비</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="soil"># 기간별함수비</div>
        </div>
    </div>
    <?php } ?>

    <!-- // 적설데이터 메뉴 표출 여부 -->
    <?php if (in_array('06', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Day.php" data-type="snow">적설데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="snow"># 지역별적설</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="snow"># 일별적설</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="snow"># 월별적설</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="snow"># 연별적설</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="snow"># 기간별적설</div>
        </div>
    </div>
    <?php } ?>

    <!-- // 경사데이터 메뉴 표출 여부 -->
    <?php if (in_array('08', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Time.php" data-type="tilt">경사데이터
        <div class="cs_sub_ul" id="id_sub_ul">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="tilt"># 지역별경사</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="tilt"># 일별경사</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="tilt"># 월별경사</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="tilt"># 연별경사</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="tilt"># 기간별경사</div>
        </div>
    </div>
    <?php } ?>

    <!-- //침수데이터 메뉴 표출 여부 -->
    <?php if (in_array('21', $menuArr)) { ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="table/Day.php" data-type="flood">침수데이터
        <div class="cs_sub_ul" id="id_sub_ul" style="margin-left: -200px">
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Time.php" data-type="flood"># 지역별침수</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Day.php" data-type="flood"># 일별침수</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Month.php" data-type="flood"># 월별침수</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Year.php" data-type="flood"># 연별침수</div>
            <div class="cs_sub_btn" id="id_sub_btn" data-url="table/Period.php" data-type="flood"># 기간별침수</div>
        </div>
    </div>
    <?php } ?>

</div> <!-- id_top_bar_submenu_data -->

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_broad">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/broadForm.php">방송하기</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/broadResult.php">방송내역</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/broadReport.php">결과통계</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/mentList.php">멘트관리</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/group.php">그룹관리</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/cidList.php">CID관리</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_display">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/eachEquList.php">전광판 목록</div>
    <!-- 일괄 전송시 전광판 종류가 다른 것이 섞여있으면 시나리오 내용을 각각 입력할 수 없는 문제가 있어서 일단 메뉴 숨김 -->
    <!-- <div class="cs_sub_link" id="id_sub_link" data-url="frame/groupScenForm.php">그룹전송</div> -->
    <!-- <div class="cs_sub_link" id="id_sub_link" data-url="frame/setEquList.php">장비설정</div> -->
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_gate">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/passiveGate.php">차단기 수동제어</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/gateList.php">차단기 제어 내역</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_sms">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/sendMsg.php">문자발송</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/sendList.php">발송내역</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/addrControl.php">연락처관리</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_alert">
    <!--<div class="cs_sub_link active" id="id_sub_link" data-url="frame/controlIssue.php">경보그룹설정</div>-->
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/alertList.php">경보그룹설정</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/controllList.php">경보발령내역</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/criList.php">임계값설정</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/issueMent.php">경보멘트</div>
    <!-- <div class="cs_sub_link" id="id_sub_link" data-url="frame/issueMent2.php">향골계곡멘트</div> -->
    <!-- //전광판이 없다면 메뉴 표출 안함 -->
    <?php if (in_array('18', $menuArr)) { ?>
    <?php
    // 전광판 메뉴 조건별 UI 노출
    $alertEquipDao = new WB_EQUIP_DAO();
    $alertEquipVo = $alertEquipDao->SELECT("GB_OBSV = '18' AND USE_YN IN ('1', 'Y')");

    $existImageDisplay = false; // "EWDPL_LAN" 이미지전광판
    $existTextDisplay = false; // "EWDISPLAY_LAN" 문자전광판

    foreach ($alertEquipVo as $v) {
        if ($v->ConnModel === 'EWDPL_LAN') {
            $existImageDisplay = true;
        }
        if ($v->ConnModel === 'EWDISPLAY_LAN') {
            $existTextDisplay = true;
        }
    }
    ?>
    <?php if ($existImageDisplay): ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/setAlertEachScen.php?level=1">경보전광판 관리</div>
    <?php endif; ?>
    <?php if ($existTextDisplay): ?>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/setAlertEachTextScen.php?level=1">경보 글자전광판 관리
    </div>
    <?php endif; ?>
    <?php } ?>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_cctv">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/cctv1.php">테스트지역1</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/cctv2.php">테스트지역2</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_parking">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/parkingCar.php">차량 입출차 내역</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/parkingCare.php">주차장그룹 관리</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/InOutCareDay.php">차량 입출차 통계</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/pm2Status.php">LPR On/Off 관리</div>
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_equip">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/equip.php">총 장비</div>
    <!-- <div class="cs_sub_link" id="id_sub_link" data-url="frame/brdequip.php">방송장비</div> -->
    <!-- <div class="cs_sub_link" id="id_sub_link" data-url="frame/disequip.php">전광판</div> -->
</div>

<div class="cs_top_bar_submenu" id="id_top_bar_submenu_admin">
    <div class="cs_sub_link active" id="id_sub_link" data-url="frame/manageUser.php">계정관리</div>
    <div class="cs_sub_link" id="id_sub_link" data-url="frame/logList.php">로그관리</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const codeNum = "<?= $firstCode ?>";
    const typeRain = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="rain"]');
    const typeWater = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="water"]');
    const typeDplace = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="dplace"]');
    const typeSoil = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="soil"]');
    const typeSnow = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="snow"]');
    const typeTilt = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="tilt"]');
    const typeFlood = document.querySelectorAll('.cs_sub_link#id_sub_link[data-type="flood"]');
    const allSubMenu = document.querySelectorAll('.cs_sub_link#id_sub_link');

    allSubMenu.forEach(element => {
        element.classList.remove('active');
    });

    console.info("codeNum:", codeNum)

    const safeType = (typeof type === 'undefined' || type === null || type === '') ? 'na' : type;
    const safeEquip = (typeof equip === 'undefined' || equip === null || equip === '') ? 'na' : equip;
    console.log("pType:", pType, " | safeType:", safeType, " | safeEquip:", safeEquip);
    switch (safeType) {
        case 'rain':
            typeRain.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="rain"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'water':
            typeWater.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="water"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'dplace':
            typeDplace.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="dplace"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'soil':
            typeSoil.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="soil"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'snow':
            typeSnow.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="snow"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'tilt':
            typeTilt.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="tilt"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        case 'flood':
            typeFlood.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="flood"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
        default:
            typeRain.forEach(element => {
                element.classList.add('active');
                const subLink = element.querySelector('.cs_sub_btn[data-type="rain"]');
                subLink.classList.add('active');
                console.log(subLink);
            });
            break;
    }

});
</script>