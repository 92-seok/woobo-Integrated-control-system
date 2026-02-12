<?php
if (!session_id()) {
    session_start();
}

if (isset($_SESSION['userIdx']) && isset($_SESSION['system'])) {
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
    loadenv();
    $GATE_CCTV = getenv('GATE_CCTV') ?? false;

    $userDao = new WB_USER_DAO();
    $userVo = $userDao->SELECT_SINGLE("idx = '{$_SESSION['userIdx']}'");
    $auth = $userVo->Auth;
    $uId = $userVo->uId ?? 'N/A';
    $uName = $userVo->uName ?? 'N/A';
} else {
    echo '<script>';
    echo "alert('세션이 만료되었습니다.');";
    echo "window.location.href = '/login/logout.php';";
    echo '</script>';
}

switch ($_SESSION['system']) {
    case 'flood':
        $logOutColor = '#4c19b4';
        $logOutColorHover = '#3C1097';
        break;
    case 'warning':
        $logOutColor = '#037c47';
        $logOutColorHover = '#0A7747';
        break;
    case 'dplace':
        $logOutColor = '#037c47';
        $logOutColorHover = '#0A7747';
        break;
    case 'ai':
        $logOutColor = '#4c19b4';
        $logOutColorHover = '#3C1097';
        break;
}

$url = explode('/', $_SERVER['PHP_SELF']);
$lastUrl = $url[1];
$headerfix = strpos($lastUrl, '.') ? substr($lastUrl, 0, strpos($lastUrl, '.')) : $lastUrl;

$menuDao = new DAO_T();
$menuVo = new WB_EQUIP_VO();
$menuArr = [];
$sessionfix = $_SESSION['system'];
$menuList = ['data', 'broad', 'display', 'gate', 'parking', 'sms', 'alert'];
$rootMenuArr = ['data', 'broad', 'display', 'gate', 'parking', 'sms', 'alert'];
if ($GATE_CCTV == 'true') {
    array_push($rootMenuArr, 'cctv');
    array_push($menuList, 'cctv');
}

$menuVo = $menuDao->SELECT_QUERY("SELECT DISTINCT GB_OBSV as GB FROM wb_equip WHERE USE_YN = '1'");
$equipNum = ['01', '02', '03', '04', '06', '08', '21'];

foreach ($menuVo as $dataEquip) {
    if (in_array($dataEquip['GB'], $equipNum)) {
        $equipSession = [$dataEquip['GB']];
        break;
    }
}

switch ($equipSession[0]) {
    case '01':
        $_SESSION['firstData'] = 'rain';
        $_SESSION['firstCode'] = '01';
        break;
    case '02':
        $_SESSION['firstData'] = 'water';
        $_SESSION['firstCode'] = '02';
        break;
    case '03':
        $_SESSION['firstData'] = 'dplace';
        $_SESSION['firstCode'] = '03';
        break;
    case '04':
        $_SESSION['firstData'] = 'soil';
        $_SESSION['firstCode'] = '04';
        break;
    case '06':
        $_SESSION['firstData'] = 'snow';
        $_SESSION['firstCode'] = '06';
        break;
    case '08':
        $_SESSION['firstData'] = 'tilt';
        $_SESSION['firstCode'] = '08';
        break;
    case '21':
        $_SESSION['firstData'] = 'flood';
        $_SESSION['firstCode'] = '21';
        break;
    default:
        $_SESSION['firstData'] = 'na';
        $_SESSION['firstCode'] = '99';
        break;
}

foreach ($menuVo as $v) {
    switch ($v['GB']) {
        case '01':
        case '02':
        case '03':
        case '04':
        case '05':
        case '06':
        case '08':
        case '21':
            array_push($menuArr, 'data');
            break;
        case '17':
            array_push($menuArr, 'sms');
            array_push($menuArr, 'broad');
            break;
        case '18':
            array_push($menuArr, 'display');
            break;
        case '20':
            array_push($menuArr, 'gate');
            break;
        case 'CC':
            if ($GATE_CCTV == 'true') {
                array_push($menuArr, 'cctv');
            }
            break;
        case 'LP':
            array_push($menuArr, 'parking');
            break;
    }
}
array_push($menuArr, 'alert');
array_push($menuArr, 'parking');
?>

<style>
.cs_menu {
    background-color: <?= $_SESSION['color'] ?>;
}

.cs_sub_link.active {
    color: <?= $_SESSION['color'] ?>;
}

.cs_logout {
    background-color: <?= $logOutColor ?>;
}

.cs_logout:hover {
    background-color: <?= $logOutColorHover ?>;
}
</style>

<div class="cs_menu" id="id_cs_menu">
    <div class="cs_icon_box" id="id_menu_list_icon" data-url="/main.php">
        <div class="cs_icon"></div>
        <div class="cs_title">지능형 통합관제 시스템</div>
    </div>
    <div class="cs_icon_line"></div>
    <?php if ($auth == 'guest') {
        /******** Guest Menu ********/
        //Main
        $postfix = $headerfix == 'main' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_main' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";

        // //Data
        // $postfix = ( $headerfix == "data" ) ? "_active" : "";
        // echo "<div class='cs_menu_list' id='id_menu_list_data' data-url='/data/dataFrame.php' style='background-image:url(/image/{$sessionfix}/data_menu{$postfix}.jpg);'></div>";

        // //Report
        // $postfix = ( $headerfix == "report" ) ? "_active" : "";
        // echo "<div class='cs_menu_list' id='id_menu_list_report' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";

        // Display
        $postfix = $headerfix == 'display' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_display' data-url='/display/displayFrame.php' style='background-image:url(/image/{$sessionfix}/display_menu{$postfix}.jpg);'></div>";

        // CCTV
        if ($GATE_CCTV == 'true') {
            $postfix = $headerfix == 'cctv' ? '_active' : '';
            echo "<div class='cs_menu_list' id='id_menu_list_cctv' data-url='/cctv/cctvFrame.php' style='background-image:url(/image/{$sessionfix}/cctv_menu{$postfix}.jpg);'></div>";
        }
    } elseif ($auth == 'admin') {
        /******** Admin Menu ********/
        //Main
        $postfix = $headerfix == 'main' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_main' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";

        //Data, Broad, Display, Gate, SMS, Alert
        foreach ($menuList as $m) {
            if (in_array($m, $menuArr)) {
                $postfix = $headerfix == $m ? '_active' : '';
                echo "<div class='cs_menu_list' id='id_menu_list_{$m}' data-url='/{$m}/{$m}Frame.php' style='background-image:url(/image/{$sessionfix}/{$m}_menu{$postfix}.jpg);'></div>";
            }
        }

        // //Broad
        // $m = "broad";
        // echo "<div class='cs_menu_list' id='id_menu_list_broad' data-url='http://152.99.148.155:12900' style='background-image:url(/image/{$sessionfix}/{$m}_menu.jpg);'></div>";

        //Report
        $postfix = $headerfix == 'report' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_report' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";

        //AdminiRegist
        $postfix = $headerfix == 'adminRegist' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_admin' data-url='/adminRegist/adminFrame.php' style='background-image:url(/image/{$sessionfix}/user_menu{$postfix}.png);'></div>";
    } elseif ($auth == 'root') {
        /******** Develop Menu ********/
        //Main
        $postfix = $headerfix == 'main' ? '_active' : '';
        echo "<div class='cs_menu_list rootMenu' id='id_menu_list_main' data-url='/main.php' style='background-image:url(/image/{$sessionfix}/main_menu{$postfix}.jpg);'></div>";

        //Data, Broad, Display, Gate, SMS, Alert
        foreach ($menuList as $m) {
            if (in_array($m, $rootMenuArr)) {
                $postfix = $headerfix == $m ? '_active' : '';
                echo "<div class='cs_menu_list rootCloseMenu' id='id_menu_list_{$m}' data-url='/{$m}/{$m}Frame.php' style='background-image:url(/image/{$sessionfix}/{$m}_menu{$postfix}.jpg);'></div>";
            }
        }

        //Report
        $postfix = $headerfix == 'report' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_report' data-url='/report/reportFrame.php' style='background-image:url(/image/{$sessionfix}/report_menu{$postfix}.jpg);'></div>";

        //Equip
        $postfix = $headerfix == 'equip' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_equip' data-url='/equip/equipFrame.php' style='background-image:url(/image/{$sessionfix}/equip_menu{$postfix}.png);'></div>";

        //Log
        $postfix = $headerfix == 'log' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_log' data-url='/log/logFrame.php' style='background-image:url(/image/{$sessionfix}/log_menu{$postfix}.png);'></div>";

        //AdminiRegist
        $postfix = $headerfix == 'adminRegist' ? '_active' : '';
        echo "<div class='cs_menu_list' id='id_menu_list_admin' data-url='/adminRegist/adminFrame.php' style='background-image:url(/image/{$sessionfix}/user_menu{$postfix}.png);'></div>";
    } ?>
    <div class='cs_menu_list' id='id_menu_list_scroll'>
        <!-- 스크롤 여백용 빈 메뉴칸 -->
        &nbsp;
    </div>";
    <div class="cs_name">
        사용자: <?= $uId ?> <?= $uName ?>
    </div>
    <div class='cs_menu_list cs_logout' id='id_menu_list_logout' data-url='/login/logout.php'>
        로그아웃
    </div>

</div> <!-- cs_menu -->

<!-- 최고관리자용 메뉴 -->
<? if ($auth == 'root'): ?>
<script>
let rootMenu = document.querySelector(".rootMenu");
if (rootMenu != null) {
    console.log("Root menu open");

    rootMenu.addEventListener("mouseover", () => {
        let menu = document.querySelectorAll(".rootCloseMenu");
        for (let i = 0; i < menu.length; i++) {
            menu[i].style.display = "block";
        }
    });
}
</script>
<? endif ?>

<script>
function resizeMenu() {
    let $menu = document.querySelector("#id_cs_menu");
    // 좌상단 로고 이미지 세로 235px
    // 좌측 메뉴 1개 세로 60px (10개)
    // 좌하단 로그아웃 세로 40px
    if (window.innerHeight < 235 + (60 * 10) + 40) {
        $menu.classList.add("cs_menu_scroll");
    } else {
        $menu.classList.remove("cs_menu_scroll");
    }
}

window.onload = function() {
    resizeMenu();
    window.addEventListener("resize", function() {
        resizeMenu();
    });
};
</script>
