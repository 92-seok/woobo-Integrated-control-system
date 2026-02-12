<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
// $DOTENV_TEST = getenv("DB_HOST") ?? "load .env failed";
// die($DOTENV_TEST);

$PDO_DB_HOST = getenv('DB_HOST') ?? '127.0.0.1';
$PDO_DB_PORT = getenv('DB_PORT') ?? 3306;
$PDO_DB_NAME = getenv('DB_NAME') ?? 'warning';
$PDO_DB_CHARSET = getenv('DB_CHARSET') ?? 'utf8mb4';
$PDO_DB_USERNAME = getenv('DB_USERNAME') ?? 'userWooboWeb';
$PDO_DB_PASSWORD = getenv('DB_PASSWORD') ?? 'wooboWeb!@';

class DBCONNECT {
    private $host;
    private $port;
    private $dbname;
    private $charset;
    private $username;
    private $password;

    public $db_conn;

    function __construct() {
        global $PDO_DB_HOST;
        global $PDO_DB_PORT;
        global $PDO_DB_NAME;
        global $PDO_DB_CHARSET;
        global $PDO_DB_USERNAME;
        global $PDO_DB_PASSWORD;

        $this->host = $PDO_DB_HOST;
        $this->port = $PDO_DB_PORT;
        $this->dbname = $PDO_DB_NAME;
        $this->charset = $PDO_DB_CHARSET;
        $this->username = $PDO_DB_USERNAME;
        $this->password = $PDO_DB_PASSWORD;
    }

    function connect() {
        try {
            $this->db_conn = new PDO("mysql:host={$this->host}:{$this->port};dbname={$this->dbname};charset={$this->charset}", "{$this->username}", "{$this->password}");
            $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $error_code = $e->getCode();
            $error_message = $e->getMessage();
            throw new Exception("DB 연결 실패. ({$error_code}) {$error_message}");

            // // 에러 타입별 메시지
            // if (strpos($error_message, 'Connection refused') !== false) {
            //     echo("Database server is not responding. Please check the server address.");
            // } elseif (strpos($error_message, 'No route to host') !== false) {
            //     echo("Cannot reach database server. Please check the IP address.");
            // } elseif (strpos($error_message, 'Unknown host') !== false) {
            //     echo("Database host not found. Please check the hostname/IP.");
            // } else {
            //     echo("Database connection error: " . $error_message);
            // }
            
        }

        return $this->db_conn;
    }
}

function warningLog($message) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';

    // $trace =  debug_print_backtrace();
    $caller = '';
    // $trace =  debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
    $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 5);
    $count = 0;
    foreach ($trace as $t) {
        $FILE = $t['file'] ? basename($t['file']) : '';
        $LINE = $t['line'] ?? '';
        $FUNC = $t['function'] ?? '';
        $caller .= "#{$count} {$FILE}@{$FUNC}:{$LINE} < ";
        $count++;
    }

    $path = $_SERVER['DOCUMENT_ROOT'] . '/include/db/log';
    $fileName = date('Y-m') . '.dbconn.txt';

    $write = new writeLog($path, $fileName);
    // $write->write($message .= " (DBConnector.php) {$FILE}:{$LINE}");
    $write->write($message . " // stack: {$caller}");
}