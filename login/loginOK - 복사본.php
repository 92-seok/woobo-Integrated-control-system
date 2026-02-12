<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// ✅ 올바른 headers_sent() 사용법
$file = '';
$line = 0;

if (headers_sent($file, $line)) {
    // 헤더가 어디서 전송되었는지 정확한 위치 출력
    echo "Headers already sent at file: $file, line: $line<br>";
    error_log("Headers already sent at file: $file, line: $line");

    // 추가 디버깅 정보
    echo "HTTP Response code: " . http_response_code() . "<br>";

    // 이미 전송된 헤더들 확인
    $headers = headers_list();
    echo "Already sent headers:<br>";
    foreach ($headers as $header) {
        echo "- $header<br>";
    }
    exit; // 더 이상 진행하지 않음
}

// 출력 버퍼링 시작 (헤더 전송 방지)
ob_start();

// POST 요청에서 JSON 데이터 읽기
$data = json_decode(file_get_contents('php://input'), true);

$id = urldecode(base64_decode(addcslashes($data['id'], '')));
$password = base64_decode($data['pw']);
$pw = strtoupper(hash('sha512', $password));
$ip = base64_decode($data['ip']);
$result = [];

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$loginOKDao = new WB_USER_DAO();
$loginOKVo = new WB_USER_VO();

$logDao = new WB_LOG_DAO();
$logVo = new WB_LOG_VO();
$logSearchVo = new WB_LOG_VO();
$satellite = new KMA_SATELLITE_DAO();
$radar = new KMA_RADAR_DAO();

$logVo->RegDate = date('Y-m-d H:i:s');
$logVo->ip = $ip;
$logVo->userID = $id;
$logVo->pType = 'login';
$logVo->Page = 'login.php';

$failDate = date('Y-m-d H:i:s', strtotime('-10 minutes'));
$logSearchVo = $logDao->SELECT_SINGLE("userID = '{$id}' AND RegDate >= '{$failDate}' AND EventType = 'login Block'");

if (isset($logSearchVo->{key($logSearchVo)})) {
    $diffTime = new DateTime($logSearchVo->RegDate);
    $inputTime = new DateTime();
    $interval = $inputTime->diff($diffTime);

    $h = 9 - $interval->i;
    $s = 60 - $interval->s;

    $result['code'] = '03';
    $result['msg'] = 'block';
    $result['endTime'] = "{$h}분 {$s}초";
} else {
    $loginOKVo = $loginOKDao->SELECT_SINGLE("uId = '{$id}' AND uPwd = '{$pw}'");

    if (isset($loginOKVo->{key($loginOKVo)})) {
        $ipChk = true;
        if ($loginOKVo->ipUse == 'Y') {
            if ($loginOKVo->ip) {
                $mask = 4;
                $allIpArea = explode('.', $loginOKVo->ip);
                $sIpArea = explode('.', $ip);
                for ($i = 0; $i < 4; $i++) {
                    if ($allIpArea[$i] == '*') {
                        $mask--;
                    }
                }

                for ($i = 0; $i < $mask; $i++) {
                    if ($allIpArea[$i] != $sIpArea[$i]) {
                        $ipChk = false;
                    }
                }
            } else {
                $loginOKVo->ip = $ip;
                $loginOKDao->UPDATE($loginOKVo);
            }
        }

        if (!$ipChk) {
            $result['code'] = '02';
            $result['msg'] = 'ip';

            $logVo->EventType = 'login Fail';
            $logDao->INSERT($logVo);
        } elseif ($loginOKVo->Auth == null) {
            $result['code'] = '04';
        } else {
            $result['code'] = '00';

            $logVo->EventType = 'login Success';
            $logDao->INSERT($logVo);

            $now = new DateTime('now', new DateTimeZone('UTC'));
            $minutes = (int) $now->format('i');
            $roundedMinutes = floor(($minutes - 10) / 10) * 10;
            $now->setTime($now->format('H'), $roundedMinutes);
            $dataTime = $now->format('YmdHi');

            $satImg = 'https://www.kma.go.kr/repositary/image/sat/gk2a/KO/gk2a_ami_le1b_rgb-true_ko010lc_' . $dataTime . '.thn.png';
            $rdrImg = 'https://www.kma.go.kr/repositary/image/rdr/img/RDR_CMP_WRC_' . $dataTime . '.png';

            // 세션 처리
            $system = 'ai';

            if (session_status() === PHP_SESSION_ACTIVE) {
                if (isset($_SESSION['system'])) {
                    $system = $_SESSION['system'];
                }

                $_SESSION = [];

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                session_destroy();
                usleep(100000);
            }

            // ✅ 세션 시작 전 다시 한번 헤더 확인
            if (headers_sent($file, $line)) {
                $result['code'] = '99';
                $result['msg'] = "headers_sent_at_{$file}_line_{$line}";
                error_log("Cannot start session: headers sent at $file:$line");
            } else {
                $sessionStarted = session_start();

                if ($sessionStarted && session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);

                    $_SESSION['system'] = $system;

                    switch ($_SESSION['system']) {
                        case 'flood':
                            $_SESSION['title'] = '둔치주차장 침수차단시스템';
                            $_SESSION['enTitle'] = 'dangerous area of slope system';
                            $_SESSION['color'] = '#6c34dd;';
                            $_SESSION['backgroundColorHover'] = 'background-color:#3C1097;';
                            break;
                        case 'warning':
                            $_SESSION['title'] = '조기예경보시스템';
                            $_SESSION['enTitle'] = 'ealry warning system';
                            $_SESSION['color'] = '#01a25e;';
                            $_SESSION['backgroundColorHover'] = 'background-color:#0A7747;';
                            break;
                        case 'dplace':
                            $_SESSION['title'] = '급경사지시스템';
                            $_SESSION['enTitle'] = 'dangerous area of slope system';
                            $_SESSION['color'] = '#01a25e;';
                            $_SESSION['backgroundColorHover'] = 'background-color:#0A7747;';
                            break;
                        case 'ai':
                        default:
                            $_SESSION['title'] = '지능형 통합관제 시스템 v1.0';
                            $_SESSION['enTitle'] = 'Intelligent integrated control system';
                            $_SESSION['color'] = '#6c34dd;';
                            $_SESSION['backgroundColorHover'] = 'background-color:#3C1097;';
                            break;
                    }

                    $_SESSION['sessionUseTime'] = 60 * 60 * 24; // 24시간 (세션 유지 시간)
                    $_SESSION['lastSessionUseTime'] = time();
                    $_SESSION['ip'] = $ip;
                    $_SESSION['userIdx'] = $loginOKVo->idx;

                    switch ($loginOKVo->Auth) {
                        case 'root': $_SESSION['Auth'] = 0; break;
                        case 'admin': $_SESSION['Auth'] = 1; break;
                        case 'guest': $_SESSION['Auth'] = 2; break;
                        default: $_SESSION['Auth'] = 3;
                    }

                    $session_id = session_id();
                    $_SESSION['session_id'] = $session_id;
                    $result['session_id'] = $session_id;
                } else {
                    $result['code'] = '99';
                    $result['msg'] = 'session_start_failed';
                }
            }
        }
    } else {
        // ID/PW 불일치 처리 (기존 코드와 동일)
        $logSearchVo = $logDao->SELECT("userID = '{$id}' AND RegDate >= '{$failDate}' AND pType = 'login'", 'idx ASC');
        if (isset($logSearchVo[0]->{key($logSearchVo[0])})) {
            $logCnt = 0;
            foreach ($logSearchVo as $v) {
                if ($v->EventType == 'login Fail') {
                    $logCnt++;
                } elseif ($v->EventType == 'login Success') {
                    $logCnt = 0;
                }
            }

            if ($logCnt >= 9) {
                $result['code'] = '01';
                $result['msg'] = 'block';
                $result['endTime'] = '10분 0초';

                $logVo->EventType = 'login Fail';
                $logDao->INSERT($logVo);

                $logVo->EventType = 'login Block';
                $logDao->INSERT($logVo);
            } else {
                $result['code'] = '01';
                $result['msg'] = $logCnt + 1;

                $logVo->EventType = 'login Fail';
                $logDao->INSERT($logVo);
            }
        } else {
            $result['code'] = '01';
            $result['msg'] = 1;

            $logVo->EventType = 'login Fail';
            $logDao->INSERT($logVo);
        }
    }
}

// ✅ 출력 버퍼 정리 후 JSON 헤더 전송
ob_clean(); // 버퍼 내용 지우기
header('Content-Type: application/json');
echo json_encode($result);
