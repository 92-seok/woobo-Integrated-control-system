<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/display/server/displayStatus.php';

$num = $_POST['num'];
$type = $_POST['type'];
$sendData = $_POST['sendData'];

if ($type == 'S040') {
    $sql =
        "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
			values ('" .
        $num .
        "', '" .
        $type .
        "', now(), now(), 'start' )";
} elseif ($type == 'S060') {
    $relay = getIntRelay($sendData);

    $sql =
        "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
			values ('" .
        $num .
        "', '" .
        $type .
        "', '" .
        $relay .
        "', now(), 'start' )";
} else {
    $sql =
        "insert into wb_dissend (CD_DIST_OBSV, RCMD, Parm1, RegDate, BStatus ) 
			values ('" .
        $num .
        "', '" .
        $type .
        "', '" .
        $sendData .
        "', now(), 'start' )";
}

$res = mysqli_query($conn, $sql);

$resultArray = ['code' => '00'];

echo json_encode($resultArray);
?>
