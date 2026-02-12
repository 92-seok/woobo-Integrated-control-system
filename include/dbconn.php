<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 환경 변수 로드
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
// $DOTENV_TEST = getenv("DB_HOST") ?? "load .env failed";
// die($DOTENV_TEST);

// 환경 변수 + 기본값 처리 (빈 문자열 및 falsy 값도 모두 체크)
$MYSQLI_DB_HOST     = getenv('DB_HOST')     ?: '127.0.0.1';
$MYSQLI_DB_PORT     = getenv('DB_PORT')     ?: '3306';
$MYSQLI_DB_NAME     = getenv('DB_NAME')     ?: 'warning';
$MYSQLI_DB_CHARSET  = getenv('DB_CHARSET')  ?: 'utf8';
$MYSQLI_DB_USERNAME = getenv('DB_USERNAME') ?: 'userWooboWeb';
$MYSQLI_DB_PASSWORD = getenv('DB_PASSWORD') ?: 'wooboWeb!@';

try {
    // mysqli_init + 옵션 설정 (타임아웃 등)
    $conn = mysqli_init();
    // // 연결 타임아웃(초) — 필요 시 조정
    // mysqli_options($mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, 5);

    // 실제 연결 (mysqli_report가 켜져있어 실패 시 예외 던짐)
    $conn->real_connect(
        $MYSQLI_DB_HOST,
        $MYSQLI_DB_USERNAME,
        $MYSQLI_DB_PASSWORD,
        $MYSQLI_DB_NAME,
        (int)$MYSQLI_DB_PORT
    );

    // 문자셋 설정
    $conn->set_charset($MYSQLI_DB_CHARSET);

    // 디버깅용 로그
    // var_dump($MYSQLI_DB_USERNAME);
    // error_log("DB 연결 성공: {$MYSQLI_DB_HOST}, 사용자: {$MYSQLI_DB_USERNAME}");

} catch (mysqli_sql_exception $e) {
    $error_code = $e->getCode();
    $error_message = $e->getMessage();
    error_log("DB 연결 실패 - Host: {$MYSQLI_DB_HOST}, User: {$MYSQLI_DB_USERNAME}, Error: ({$error_code}) {$error_message}");
    throw new Exception("DB 연결 실패. ({$e->getCode()}) {$e->getMessage()}", 0, $e);
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbvo.php';

class DBCONNECT {
    private $host;
    private $port;
    private $dbname;
    private $charset;
    private $username;
    private $password;

    public $db_conn;

    function __construct() {
        global $MYSQLI_DB_HOST;
        global $MYSQLI_DB_PORT;
        global $MYSQLI_DB_NAME;
        global $MYSQLI_DB_CHARSET;
        global $MYSQLI_DB_USERNAME;
        global $MYSQLI_DB_PASSWORD;

        $this->host = $MYSQLI_DB_HOST;
        $this->port = $MYSQLI_DB_PORT;
        $this->dbname = $MYSQLI_DB_NAME;
        $this->charset = $MYSQLI_DB_CHARSET;
        $this->username = $MYSQLI_DB_USERNAME;
        $this->password = $MYSQLI_DB_PASSWORD;

        try {
            $this->db_conn = new PDO("mysql:host={$this->host}:{$this->port};dbname={$this->dbname};charset={$this->charset}", "{$this->username}", "{$this->password}");
            $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $error_code = $e->getCode();
            $error_message = $e->getMessage();
            throw new Exception("DB 연결 실패. ({$error_code}) {$error_message}");
       }
    }
}
