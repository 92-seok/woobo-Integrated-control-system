<?php // declare(strict_types=1); // strict requirement

// define("강우", "01");
// define("수위", "02");
// define("변위", "03");
// define("함수비", "04");
// define("뉴스", "05");
// define("적설", "06");
// define("경사", "08");
// define("방송", "17");
// define("전광판", "18");
// define("CCTV", "19");
// define("차단기", "20");
// define("침수", "21");

define('AUTH_ROOT', 0);
define('AUTH_ADMIN', 1);
define('AUTH_GUEST', 2);
define('AUTH_DEFAULT', 3);

function isRootSession(): bool
{
    $auth = $_SESSION['Auth'] ?? AUTH_GUEST;
    if ($auth == AUTH_ROOT) {
        return true;
    }
    return false;
}

function isAdminSession(): bool
{
    $auth = $_SESSION['Auth'] ?? AUTH_GUEST;
    if ($auth == AUTH_ROOT) {
        return true;
    }
    if ($auth == AUTH_ADMIN) {
        return true;
    }
    return false;
}

function gb2id($def): string
{
    $result = match ($def) {
        '강우' => '01',
        '수위' => '02',
        '변위' => '03',
        '함수비' => '04',
        '뉴스' => '05',
        '적설' => '06',
        '경사' => '08',
        '방송' => '17',
        '전광판' => '18',
        'CCTV' => '19',
        '차단기' => '20',
        '침수' => '21',
        default => 'N/A',
    };
    return $result;
}

function id2gb($num): string
{
    $result = match ($num) {
        '01' => '강우',
        '02' => '수위',
        '03' => '변위',
        '04' => '함수비',
        '05' => '뉴스',
        '06' => '적설',
        '08' => '경사',
        '17' => '방송',
        '18' => '전광판',
        '19' => 'CCTV',
        '20' => '차단기',
        '21' => '침수',
        default => 'N/A',
    };
    return $result;
}

function isVertical($width, $height): bool
{
    if ($width < $height) {
        return true;
    }
    return false;
}
