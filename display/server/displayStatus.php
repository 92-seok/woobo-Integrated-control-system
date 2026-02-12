<?php
function getPowerStatus($power)
{
    $retArray = ['Error', 'Error', 'Error'];

    if ($power == '0') {
        $retArray = ['OFF', 'OFF', 'OFF', 'OFF'];
    } elseif ($power == '1') {
        $retArray = ['OFF', 'OFF', 'OFF', 'ON'];
    } elseif ($power == '2') {
        $retArray = ['OFF', 'OFF', 'ON', 'OFF'];
    } elseif ($power == '3') {
        $retArray = ['OFF', 'OFF', 'ON', 'ON'];
    } elseif ($power == '4') {
        $retArray = ['OFF', 'ON', 'OFF', 'OFF'];
    } elseif ($power == '5') {
        $retArray = ['OFF', 'ON', 'OFF', 'ON'];
    } elseif ($power == '6') {
        $retArray = ['OFF', 'ON', 'ON', 'OFF'];
    } elseif ($power == '7') {
        $retArray = ['OFF', 'ON', 'ON', 'ON'];
    } elseif ($power == '8') {
        $retArray = ['ON', 'OFF', 'OFF', 'OFF'];
    } elseif ($power == '9') {
        $retArray = ['ON', 'OFF', 'OFF', 'ON'];
    } elseif ($power == '10') {
        $retArray = ['ON', 'OFF', 'ON', 'OFF'];
    } elseif ($power == '11') {
        $retArray = ['ON', 'OFF', 'ON', 'ON'];
    } elseif ($power == '12') {
        $retArray = ['ON', 'ON', 'OFF', 'OFF'];
    } elseif ($power == '13') {
        $retArray = ['ON', 'ON', 'OFF', 'ON'];
    } elseif ($power == '14') {
        $retArray = ['ON', 'ON', 'ON', 'OFF'];
    } elseif ($power == '15') {
        $retArray = ['ON', 'ON', 'ON', 'ON'];
    } else {
        $retArray = ['-', '-', '-', '-'];
    }

    return $retArray;
}

function getRelay($relay)
{
    $retArray = ['OFF', 'OFF', 'OFF', 'OFF'];

    if ($relay == '0') {
        $retArray = ['OFF', 'OFF', 'OFF', 'OFF'];
    } elseif ($relay == '1') {
        $retArray = ['OFF', 'OFF', 'OFF', 'ON'];
    } elseif ($relay == '2') {
        $retArray = ['OFF', 'OFF', 'ON', 'OFF'];
    } elseif ($relay == '3') {
        $retArray = ['OFF', 'OFF', 'ON', 'ON'];
    } elseif ($relay == '4') {
        $retArray = ['OFF', 'ON', 'OFF', 'OFF'];
    } elseif ($relay == '5') {
        $retArray = ['OFF', 'ON', 'OFF', 'ON'];
    } elseif ($relay == '6') {
        $retArray = ['OFF', 'ON', 'ON', 'OFF'];
    } elseif ($relay == '7') {
        $retArray = ['OFF', 'ON', 'ON', 'ON'];
    } elseif ($relay == '8') {
        $retArray = ['ON', 'OFF', 'OFF', 'OFF'];
    } elseif ($relay == '9') {
        $retArray = ['ON', 'OFF', 'OFF', 'ON'];
    } elseif ($relay == '10') {
        $retArray = ['ON', 'OFF', 'ON', 'OFF'];
    } elseif ($relay == '11') {
        $retArray = ['ON', 'OFF', 'ON', 'ON'];
    } elseif ($relay == '12') {
        $retArray = ['ON', 'ON', 'OFF', 'OFF'];
    } elseif ($relay == '13') {
        $retArray = ['ON', 'ON', 'OFF', 'ON'];
    } elseif ($relay == '14') {
        $retArray = ['ON', 'ON', 'ON', 'OFF'];
    } elseif ($relay == '15') {
        $retArray = ['ON', 'ON', 'ON', 'ON'];
    } else {
        $retArray = ['-', '-', '-', '-'];
    }

    return $retArray;
}

function getIntRelay($relay)
{
    $data = explode('/', $relay);
    $returnData = 0;

    if ($data[0] == 'OFF' && $data[1] == 'OFF' && $data[2] == 'OFF' && $data[2] == 'OFF') {
        $returnData = 0;
    } elseif ($data[0] == 'OFF' && $data[1] == 'OFF' && $data[2] == 'OFF' && $data[2] == 'ON') {
        $returnData = 1;
    } elseif ($data[0] == 'OFF' && $data[1] == 'OFF' && $data[2] == 'ON' && $data[2] == 'OFF') {
        $returnData = 2;
    } elseif ($data[0] == 'OFF' && $data[1] == 'OFF' && $data[2] == 'ON' && $data[2] == 'ON') {
        $returnData = 3;
    } elseif ($data[0] == 'OFF' && $data[1] == 'ON' && $data[2] == 'OFF' && $data[2] == 'OFF') {
        $returnData = 4;
    } elseif ($data[0] == 'OFF' && $data[1] == 'ON' && $data[2] == 'OFF' && $data[2] == 'ON') {
        $returnData = 5;
    } elseif ($data[0] == 'OFF' && $data[1] == 'ON' && $data[2] == 'ON' && $data[2] == 'OFF') {
        $returnData = 6;
    } elseif ($data[0] == 'OFF' && $data[1] == 'ON' && $data[2] == 'ON' && $data[2] == 'ON') {
        $returnData = 7;
    } elseif ($data[0] == 'ON' && $data[1] == 'OFF' && $data[2] == 'OFF' && $data[2] == 'OFF') {
        $returnData = 8;
    } elseif ($data[0] == 'ON' && $data[1] == 'OFF' && $data[2] == 'OFF' && $data[2] == 'ON') {
        $returnData = 9;
    } elseif ($data[0] == 'ON' && $data[1] == 'OFF' && $data[2] == 'ON' && $data[2] == 'OFF') {
        $returnData = 10;
    } elseif ($data[0] == 'ON' && $data[1] == 'OFF' && $data[2] == 'ON' && $data[2] == 'ON') {
        $returnData = 11;
    } elseif ($data[0] == 'ON' && $data[1] == 'ON' && $data[2] == 'OFF' && $data[2] == 'OFF') {
        $returnData = 12;
    } elseif ($data[0] == 'ON' && $data[1] == 'ON' && $data[2] == 'OFF' && $data[2] == 'ON') {
        $returnData = 13;
    } elseif ($data[0] == 'ON' && $data[1] == 'ON' && $data[2] == 'ON' && $data[2] == 'OFF') {
        $returnData = 14;
    } elseif ($data[0] == 'ON' && $data[1] == 'ON' && $data[2] == 'ON' && $data[2] == 'ON') {
        $returnData = 15;
    }

    return $returnData;
}
?>
