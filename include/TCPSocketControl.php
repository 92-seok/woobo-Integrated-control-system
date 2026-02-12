<?php
//The Client
// error_reporting(E_ALL);
// set_time_limit(0);

$address = '192.168.83.106'; // 접속할 IP //
$port = 4096; // 접속할 PORT //
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP); // TCP 통신용 소켓 생성 //김민
socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, ['sec' => 5, 'usec' => 0]);
socket_set_option($socket, SOL_SOCKET, SO_SNDTIMEO, ['sec' => 5, 'usec' => 0]);
if ($socket === false) {
    //echo "socket_create() 실패! 이유: " . socket_strerror(socket_last_error()) . "\n";
    //echo "<br>";
} else {
    //echo "socket 성공적으로 생성.\n";
    //echo "<br>";
}

//echo "다음 IP '$address' 와 Port '$port' 으로 접속중...";
//echo "<BR>";
$result = socket_connect($socket, $address, $port); // 소켓 연결 및 $result에 접속값 지정 //
if ($result === false) {
    //echo "socket_connect() 실패.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
    //echo "<br>";
} else {
    //echo "다음 주소로 연결 성공 : $address.\n";
    //echo "<br>";
}

$hex = 'FF FA 46 33 52 A1 00 FF FE';
$i = hex2bin(str_replace(' ', '', $hex));

// 결과 : one / two / three 보내고자 하는 전문 //
//echo "서버로 보내는 전문 : $i|종료|.\n";

$result = socket_write($socket, $i, strlen($i)); // 실제로 소켓으로 보내는 명령어 //
if ($result === false) {
    //echo "socket_write() 실패.\nReason: ($result) " . socket_strerror(socket_last_error($socket)) . "\n";
} else {
    //echo "다음 주소로 쓰기 성공 : $address.\n";
}

//echo "<br>";
($input = socket_read($socket, 1024)) or die("Could not read from Socket\n"); // 소켓으로 부터 받은 REQUEST 정보를 $input에 지정 //
//echo "<br>";

print_r($input); //REQUEST 정보 출력//
//socket_close($socket);
?>
