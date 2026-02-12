<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_EQUIP_DAO();
$vo = new WB_EQUIP_VO();

$type = $_GET['type'];
$class = $_GET['class'];

if ($class == 'large') {
    $class = $_GET['class'];

    $vo = $dao->SELECT("USE_YN = '1'");
    $equipArr = [];
    $i = 0;

    foreach ($vo as $v) {
        $equipArr[$i++] = $v->GB_OBSV;
    }

    if ($type == 'all') {
        echo "<option value='' disabled selected>중분류 선택</option>";
        echo "<option value='all'>전체</option>";
        //계측
        if (in_array('01', $equipArr)) {
            echo "<option value='01'>강우</option>";
        }
        if (in_array('02', $equipArr)) {
            echo "<option value='02'>수위</option>";
        }
        if (in_array('03', $equipArr)) {
            echo "<option value='03'>변위</option>";
        }
        if (in_array('04', $equipArr)) {
            echo "<option value='04'>함수비</option>";
        }
        if (in_array('06', $equipArr)) {
            echo "<option value='06'>적설</option>";
        }
        if (in_array('08', $equipArr)) {
            echo "<option value='08'>경사</option>";
        }
        if (in_array('21', $equipArr)) {
            echo "<option value='21'>침수</option>";
        }

        //장비
        if (in_array('17', $equipArr)) {
            echo "<option value='17'>방송</option>";
        }
        if (in_array('18', $equipArr)) {
            echo "<option value='18'>전광판</option>";
        }
        if (in_array('20', $equipArr)) {
            echo "<option value='20'>차단기</option>";
        }
    } elseif ($type == 'measurement') {
        echo "<option value='' disabled selected>중분류 선택</option>";
        echo "<option value='measurementAll'>전체</option>";
        if (in_array('01', $equipArr)) {
            echo "<option value='01'>강우</option>";
        }
        if (in_array('02', $equipArr)) {
            echo "<option value='02'>수위</option>";
        }
        if (in_array('03', $equipArr)) {
            echo "<option value='03'>변위</option>";
        }
        if (in_array('04', $equipArr)) {
            echo "<option value='04'>함수비</option>";
        }
        if (in_array('06', $equipArr)) {
            echo "<option value='06'>적설</option>";
        }
        if (in_array('08', $equipArr)) {
            echo "<option value='08'>경사</option>";
        }
        if (in_array('21', $equipArr)) {
            echo "<option value='21'>침수</option>";
        }
    } elseif ($type == 'equip') {
        echo "<option value='' disabled selected>중분류 선택</option>";
        echo "<option value='equipAll'>전체</option>";
        if (in_array('17', $equipArr)) {
            echo "<option value='17'>방송</option>";
        }
        if (in_array('18', $equipArr)) {
            echo "<option value='18'>전광판</option>";
        }
        if (in_array('20', $equipArr)) {
            echo "<option value='20'>차단기</option>";
        }
    }
} elseif ($class == 'middle') {
    if ($type == 'measurementAll') {
        $vo = $dao->SELECT("USE_YN = '1' AND GB_OBSV IN ('01', '02', '03', '04', '06', '08', '21')");
    } elseif ($type == 'equipAll') {
        $vo = $dao->SELECT("USE_YN = '1' AND GB_OBSV IN ('17', '18', '20')");
    } else {
        $vo = $dao->SELECT("USE_YN = '1' AND GB_OBSV = '{$type}'");
    }

    foreach ($vo as $v) {
        if ($v->GB_OBSV == '03') {
            for ($i = 1; $i <= $v->SubOBCount; $i++) {
                echo "<option value='{$v->CD_DIST_OBSV}' equip='{$i}'>{$v->NM_DIST_OBSV} {$i}</option>";
            }
        } else {
            echo "<option value='{$v->CD_DIST_OBSV}'>{$v->NM_DIST_OBSV}</option>";
        }
    }
} elseif ($class == 'equipAS') {
    $result = [];
    if (strpos($type, ',')) {
        $arr = explode(',', $type);
        $type = $arr[0];
    }
    $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$type}'");

    $result['large'] = 'measurement';
    $result['sensor'] = '';

    switch ($vo->GB_OBSV) {
        case '01':
            $result['middle'] = '01';
            $result['middleNM'] = '강우';
            break;
        case '02':
            $result['middle'] = '02';
            $result['middleNM'] = '수위';
            break;
        case '03':
            $result['middle'] = '03';
            $result['middleNM'] = '변위';
            $result['sensor'] = $arr[1];
            break;
        case '04':
            $result['middle'] = '04';
            $result['middleNM'] = '함수비';
            break;
        case '06':
            $result['middle'] = '06';
            $result['middleNM'] = '적설';
            break;
        case '08':
            $result['middle'] = '08';
            $result['middleNM'] = '경사';
            break;
        case '21':
            $result['middle'] = '21';
            $result['middleNM'] = '침수';
            break;
        case '17':
            $result['middle'] = '17';
            $result['middleNM'] = '방송';
            $result['large'] = 'equip';
            break;
        case '18':
            $result['middle'] = '18';
            $result['middleNM'] = '전광판';
            $result['large'] = 'equip';
            break;
        case '20':
            $result['middle'] = '20';
            $result['middleNM'] = '차단기';
            $result['large'] = 'equip';
            break;
    }

    $result['CD_DIST_OBSV'] = $vo->CD_DIST_OBSV;
    $result['NM_DIST_OBSV'] = $vo->NM_DIST_OBSV;

    echo json_encode($result);
}
