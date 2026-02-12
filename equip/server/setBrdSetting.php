<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

//sType : sType, param1 : param1, param2 : param2
$sType = $_POST['sType'];
$param1 = $_POST['param1'];
$param2 = $_POST['param2'];
$equip = $_POST['equip'];

$equipList = explode(',', $equip);

if ($sType == 'S060') {
    $param1 = date('YmdHis', time());
}

for ($i = 0; $i < count($equipList); $i++) {
    $sql =
        "insert into wb_brdsend (CD_DIST_OBSV, RCMD, Parm1, Parm2, RegDate, BStatus) 
				values ( '" .
        $equipList[$i] .
        "', '" .
        $sType .
        "', '" .
        $param1 .
        "', '" .
        $param2 .
        "', now(), 'start' )";

    $res = mysqli_query($conn, $sql);
}

$resultArray = ['code' => '00'];
echo json_encode($resultArray);

?>
