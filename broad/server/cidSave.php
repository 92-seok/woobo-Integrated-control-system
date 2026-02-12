<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

$equip = $_POST['equip'];
$cid = $_POST['cid'];
$num = $_POST['num'];
$type = $_POST['type'];
$resultArray = [];

if ($type == 'insert') {
    $equipList = explode(',', $equip);

    for ($i = 0; $i < count($equipList); $i++) {
        $chkSql = "select * from wb_brdcid where CD_DIST_OBSV = '" . $equipList[$i] . "' and Cid = '" . $cid . "'";
        $chkRes = mysqli_query($conn, $chkSql);
        $chkCount = mysqli_num_rows($chkRes);

        if ($chkCount > 0) {
            // data update
            $row = mysqli_fetch_assoc($chkRes);
            $resultArray['before'] = $row['Cid'];

            $sql =
                "update wb_brdcid
					set CStatus = 'start', RegDate = now()
					where CD_DIST_OBSV = '" .
                $equipList[$i] .
                "' and Cid = '" .
                $cid .
                "'";
        }
        // data Insert
        else {
            $resultArray['before'] = '';

            $sql =
                "insert into wb_brdcid ( CD_DIST_OBSV, Cid, CStatus, RegDate )
					values ('" .
                $equipList[$i] .
                "', '" .
                $cid .
                "', 'start', now() )";
        }
        $res = mysqli_query($conn, $sql);

        // 장비제어 등록
        $sSql =
            "insert into wb_brdsend ( CD_DIST_OBSV, RCMD, Parm1, RegDate , BStatus )
					values ('" .
            $equipList[$i] .
            "', 'S040', '" .
            $cid .
            "', now(), 'start' )";
        $sRes = mysqli_query($conn, $sSql);
    }
    $resultArray['code'] = '00';
} elseif ($type == 'delete') {
    $aSql = 'select * from wb_brdcid where ' . $num;
    $aRes = mysqli_query($conn, $aSql);
    $aRow = mysqli_fetch_array($aRes);

    $resultArray['before'] = $aRow['Cid'];

    // 장비제어 등록
    $sSql =
        "insert into wb_brdsend ( CD_DIST_OBSV, RCMD, Parm1, RegDate , BStatus )
				values ('" .
        $aRow['CD_DIST_OBSV'] .
        "', 'S050', '" .
        $aRow['Cid'] .
        "' , now(), 'start')";
    $sRes = mysqli_query($conn, $sSql);

    $sql = 'delete from wb_brdcid where ' . $num;
    $res = mysqli_query($conn, $sql);

    $resultArray['code'] = '00';
}

echo json_encode($resultArray);
?>
