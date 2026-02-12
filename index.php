<?php declare(strict_types=1) ?>
<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();

///////////////////////////////////////////////////////////////////////////////
/// 웹서버 실행에 필요한 필수 config ini 설정들을 세션 시작하기 전에 index 에서 확인
try {
    $ini_settings = [
        // 'display_errors' => 'On',
        // 'display_startup_errors' => 'On',
        // 'error_reporting' => E_ALL,
        // 'session.cookie_httponly' => 1,
        // 'session.use_strict_mode' => 0,
        // 'session.use_only_cookies' => 1,
        // 'session.cookie_secure' => 0
        'date.timezone' => 'Asia/Seoul'
    ];
    foreach ($ini_settings as $key => $value) {
        $config = ini_get($key);
        if ($config != $value) {
            throw new Exception("php.ini 설정이 잘못되었습니다. ( {$key} = {$value} )");
        }
    }

    // 타임존 확인
    $timezone = ini_get('date.timezone');
    if (strcmp($timezone, date_default_timezone_get()) !== 0) {
        throw new Exception("php.ini 설정과 스크립트의 타임존이 일치하지 않습니다. ( {$timezone} )");
    }
    if (strcmp($timezone, 'Asia/Seoul') !== 0) {
        throw new Exception("php.ini 설정의 타임존이 서울이 아닙니다. ( {$timezone} )");
    }
    $extensions = get_loaded_extensions();
    if (empty($extensions)) {
        throw new Exception('INI 설정의 ext 확장 모듈 로딩에 실패했습니다.');
    }
    $loaded_extensions = implode(',', $extensions);
    if (strlen($loaded_extensions) === 0) {
        throw new Exception("INI 설정의 ext 확장 모듈 로딩에 실패했습니다. ( {$loaded_extensions} )");
    }

    // 그래픽 모듈 확인
    $gd = extension_loaded('gd');
    $gd2 = extension_loaded('gd2');
    if ($gd === false && $gd2 === false) {
        throw new Exception("INI 설정의 GD 그래픽스 라이브러리 모듈이 설정되지 않았습니다. ( {$loaded_extensions} )");
    }

    // echo phpinfo(INFO_MODULES);
    $apache_modules = apache_get_modules();
    $apache_version = apache_get_version();
    if (in_array('mod_rewrite', $apache_modules) === false) {
        throw new Exception('아파치 httpd.conf 설정에서 LoadModule rewrite_module modules/mod_rewrite.so 모듈이 로딩되지 않았습니다.');
    }

    // .env, .htaccess 파일 존재 여부 확인
    if (!file_exists('.env')) {
        throw new Exception('ENV DB 설정 파일이 존재하지 않습니다.');
    }
    if (!file_exists('.htaccess')) {
        throw new Exception('.htaccess 파일이 존재하지 않습니다.');
    }

    // 카카오맵 API 키 값 확인
    $KAKAO_API = getenv('KAKAO_API') ?? false;
    if ($KAKAO_API == false) {
        throw new Exception('KAKAO_API 환경 변수가 설정되지 않았습니다.');
    }

    // 데이터베이스 접속 테스트
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/DBConnector.php';
    $dbTest = new DBCONNECT();
    $testConn = $dbTest->connect();
    $stmt = $testConn->query("SELECT 1");
    if (!$stmt) {
        throw new Exception('데이터베이스 쿼리 실행에 실패했습니다.');
    }
} catch (PDOException|Exception $e) {
    echo '<h1>오류 발생</h1>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
    exit();
}

///////////////////////////////////////////////////////////////////////////////
/// 세션 시작
session_start();

if (!isset($_SESSION['system'])) {
    $_SESSION['system'] = 'ai';
}

// Login창에서 세션 유지 시간을 넘는 유저를 위해 loginOK.php에서 한번 더 설정
if ($_SESSION['system'] == 'flood') {
    $_SESSION['title'] = '둔치주차장 침수차단시스템';
    $_SESSION['enTitle'] = 'dangerous area of slope system';
    $_SESSION['color'] = '#6c34dd;';
    $_SESSION['backgroundColorHover'] = 'background-color:#3C1097;';
} elseif ($_SESSION['system'] == 'warning') {
    $_SESSION['title'] = '조기예경보시스템';
    $_SESSION['enTitle'] = 'ealry warning system';
    $_SESSION['color'] = '#01a25e;';
    $_SESSION['backgroundColorHover'] = 'background-color:#0A7747;';
} elseif ($_SESSION['system'] == 'dplace') {
    $_SESSION['title'] = '급경사지시스템';
    $_SESSION['enTitle'] = 'dangerous area of slope system';
    $_SESSION['color'] = '#01a25e;';
    $_SESSION['backgroundColorHover'] = 'background-color:#0A7747;';
} elseif ($_SESSION['system'] == 'ai') {
    $_SESSION['title'] = '지능형 통합관제 시스템 v1.0';
    $_SESSION['enTitle'] = 'Intelligent integrated control system';
    $_SESSION['color'] = '#6c34dd;';
    $_SESSION['backgroundColorHover'] = 'background-color:#3C1097;';
}

///////////////////////////////////////////////////////////////////////////////
/// 헤더
header('Content-Type: text/html; charset=utf-8');

if (!isset($_SESSION['lastSessionUseTime'])) {
    include 'login/login.php'; //echo "<script>window.location.replace('login/login.php')</script>";
} else {
    $_SESSION['sessionUseTime'] = 60 * 60 * 24; // 24시간 (세션 유지 시간)
    // $_SESSION["sessionUseTime"] = 0; // Dev
    $_SESSION['lastSessionUseTime'] = time();
    echo "<script>window.location.replace('/main.php')</script>";
}
