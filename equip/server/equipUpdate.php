<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

function updateQuery($col, $val)
{
    if ($GLOBALS['qType'] == 'add') {
        $GLOBALS['insertcol'] = "{$GLOBALS['insertcol']}, {$col}";
        $GLOBALS['insertval'] = "{$GLOBALS['insertval']}, {$val}";
    } elseif ($GLOBALS['qType'] == 'upd') {
        $sql = "UPDATE wb_equip SET {$col} = {$val} WHERE CD_DIST_OBSV = {$GLOBALS['num']}";
    } elseif ($GLOBALS['qType'] == 'upds') {
        $sql = "UPDATE wb_equip SET {$col} = {$val} WHERE CD_DIST_OBSV IN ({$GLOBALS['num']})";
    }

    if ($GLOBALS['qType'] != 'add') {
        try {
            mysqli_query($GLOBALS['conn'], $sql);
            $GLOBALS['result']['code'] = '00';
        } catch (Exception $e) {
            $GLOBALS['result']['code'] = '22';
            $GLOBALS['result']['msg'] = $e;
        }
    }
}

function chkQuery($table, $subob = '')
{
    if ($subob == '') {
        $sql = "SELECT * FROM {$table} WHERE CD_DIST_OBSV = '{$GLOBALS['num']}'";
    } else {
        $sql = "SELECT * FROM {$table} WHERE CD_DIST_OBSV = '{$GLOBALS['num']}' and SUB_OBSV = {$subob}";
    }
    $res = mysqli_query($GLOBALS['conn'], $sql);
    $cnt = mysqli_num_rows($res);

    $result = false;
    if ($cnt > 0) {
        $result = true;
    }
    return $result;
}

$data = json_decode(file_get_contents('php://input'), true);
$result = [];

$qType = $data['equip21'];
$gbtype = $data['equip11'];

if ($qType != 'add') {
    $num = $data['equip20'];
} else {
    $num = $data['equip0'];
}

if ($num != '') {
    $insertcol = 'CD_DIST_OBSV';
    $insertval = "'{$num}'";
    $substr = false;
} else {
    $substr = true;
}

for ($qType != 'upds' ? ($i = 0) : ($i = 4); $i < 20; $i++) {
    if ($data['equip' . $i] != '') {
        switch ($i) {
            // case 0:
            //     updateQuery('CD_DIST_OBSV', "'{$data['equip' . $i]}'");
            //     break;
            case 1:
                updateQuery('NM_DIST_OBSV', "'{$data['equip' . $i]}'");
                break;
            case 2:
                $arr = explode(':', $data['equip' . $i]);
                updateQuery('ConnIP', "'{$arr[0]}'");
                updateQuery('ConnPort', "'{$arr[1]}'");
                break;
            case 3:
                updateQuery('ConnPhone', "'{$data['equip' . $i]}'");
                break;
            case 4:
                $arr = explode(',', $data['equip' . $i]);
                updateQuery('LAT', $arr[0]);
                updateQuery('LON', $arr[1]);
                break;
            case 5:
                updateQuery('DTL_ADRES', "'{$data['equip' . $i]}'");
                break;
            case 6:
                updateQuery('SizeX', $data['equip' . $i]);
                break;
            case 7:
                updateQuery('SizeY', $data['equip' . $i]);
                break;
            case 8:
                updateQuery('ConnType', "'{$data['equip' . $i]}'");
                break;
            case 9:
                updateQuery('ConnModel', "'{$data['equip' . $i]}'");
                break;
            case 10:
                updateQuery('ErrorChk', $data['equip' . $i]);
                break;
            case 11:
                updateQuery('GB_OBSV', "'{$data['equip' . $i]}'");
                break;
            case 12:
                updateQuery('LastDate', "'{$data['equip' . $i]}'");
                break;
            case 13:
                updateQuery('LastStatus', "'{$data['equip' . $i]}'");
                break;
            case 14:
                updateQuery('RainBit', $data['equip' . $i]);
                break;
            case 15:
                updateQuery('SeeLevelUse', $data['equip' . $i]);
                break;
            case 16:
                updateQuery('SubOBCount', $data['equip' . $i]);
                break;
            case 17:
                updateQuery('DetCode', "'{$data['equip' . $i]}'");
                break;
            case 18:
                updateQuery('DSCODE', "'{$data['equip' . $i]}'");
                break;
            case 19:
                updateQuery('USE_YN', "'{$data['equip' . $i]}'");
                break;
        }
    }
}

if ($qType == 'add') {
    $result['code'] = '00';
    if ($substr) {
        $insertcol = substr($insertcol, 1, strlen($insertcol) - 1);
        $insertval = substr($inserval, 1, strlen($insertval) - 1);
    } else {
        $equipCnt = chkQuery('wb_equip');
        if ($equipCnt) {
            $result['code'] = '21';
            $result['msg'] = 'Duplicate equipment number.';
        }
    }

    if ($result['code'] == '00') {
        $sql = "INSERT INTO wb_equip ({$insertcol}) VALUES ({$insertval})";
        mysqli_query($conn, $sql);

        if ($gbtype != '' && $num != '') {
            if ($gbtype == '01') {
                $rainChk = chkQuery('wb_raindis');
                if (!$rainChk) {
                    mysqli_query($conn, "INSERT INTO wb_raindis (CD_DIST_OBSV, RegDate) VALUES ({$num}, now())");
                }
            } elseif ($gbtype == '02') {
                $waterChk = chkQuery('wb_waterdis');
                if (!$waterChk) {
                    mysqli_query($conn, "INSERT INTO wb_waterdis (CD_DIST_OBSV, RegDate) VALUES ({$num}, now())");
                }
            } elseif ($gbtype == '03') {
                if ($data['equip16'] != '') {
                    for ($i = 0; $i < $data['equip16']; $i++) {
                        $dplaceChk = chkQuery('wb_dplacedis', $i);
                        if (!$dplaceChk) {
                            mysqli_query(
                                $conn,
                                "INSERT INTO wb_dplacedis (CD_DIST_OBSV, SUB_OBSV, RegDate) VALUES ({$num}, {$i}, now())",
                            );
                        }
                    }
                }
                $dplaceChk = chkQuery('wb_dplacedis', 1);
                if (!$dplaceChk) {
                    mysqli_query(
                        $conn,
                        "INSERT INTO wb_dplacedis (CD_DIST_OBSV, SUB_OBSV, RegDate) VALUES ({$num}, 1, now())",
                    );
                }
            } elseif ($gbtype == '06') {
                $snowChk = chkQuery('wb_snowdis');
                if (!$snowChk) {
                    mysqli_query($conn, "INSERT INTO wb_snowdis (CD_DIST_OBSV, RegDate) VALUES ({$num}, now())");
                }
            } elseif ($gbtype == '17') {
                $brdChk = chkQuery('wb_brdstatus');
                if (!$brdChk) {
                    mysqli_query($conn, "INSERT INTO wb_brdstatus (CD_DIST_OBSV, UDate) VALUES ({$num}, now())");
                }
            } elseif ($gbtype == '18') {
                $displayChk = chkQuery('wb_disstatus');
                if (!$displayChk) {
                    mysqli_query(
                        $conn,
                        "INSERT INTO wb_disstatus (CD_DIST_OBSV, LastDate, Power, Relay, Bright, ExpStatus) VALUES ({$num}, now(), '0/0/0/0', '0000', '3/3/3/3/3/3/3/9/9/9/9/9/9/9/9/9/9/9/9/3/3/3/3/3', 'ad')",
                    );
                }
            } elseif ($gbtype == '20') {
                $gateChk = chkQuery('wb_gatestatus');
                if (!$gateChk) {
                    mysqli_query($conn, "INSERT INTO wb_gatestatus (CD_DIST_OBSV, RegDate) VALUES ({$num}, now())");
                }
            }
        }

        $result['code'] = '00';
    }
} elseif ($qType == 'del') {
    $sql = "DELETE FROM wb_equip ({$insertcol}) VALUES ({$insertval})";
    mysqli_query($conn, $sql);
}

echo json_encode($result);

?>
