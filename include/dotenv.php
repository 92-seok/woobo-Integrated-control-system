<?php
/// Copy .env.sample File And Paste .env File

/**
 * 값을 적절한 타입으로 변환합니다.
 *
 * @param string $value 변환할 값
 * @return mixed 변환된 값
 */
function parseValue($value)
{
    $lowerValue = strtolower($value);

    if ($lowerValue === 'true') {
        return true;
    } elseif ($lowerValue === 'false') {
        return false;
    } elseif ($lowerValue === 'null' || $lowerValue === '') {
        return null;
    } elseif (is_numeric($value)) {
        return strpos($value, '.') !== false ? (float)$value : (int)$value;
    }

    return $value;
}

/**
 * .env 파일을 로드하여 환경 변수로 설정합니다.
 * .env.sample 파일을 복사하여 .env 파일을 생성하세요.
 *
 * @throws Exception .env 파일이 없거나 읽을 수 없을 때
 */
function loadenv()
{
    // 현재 스크립트의 디렉토리를 기준으로 .env 파일 경로 설정 (web 폴더 내부)
    // $path = __DIR__ . '/../.env';
    $path = $_SERVER['DOCUMENT_ROOT'] . '/.env';

    if (!file_exists($path)) {
        throw new Exception("File not found: {$path}");
    }

    if (!is_readable($path)) {
        throw new Exception("File not readable: {$path}");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {

        // 주석 및 잘못된 라인 건너뛰기
        if (empty(trim($line))) {
            continue;
        }
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        if (strpos($line, '=') === false) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, "\x00..\x1F\"");

        // 빈 값 건너뛰기
        if (empty($name)) {
            continue;
        }
        // 따옴표 제거
        if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
            (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
            $value = substr($value, 1, -1);
        }

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            // 환경변수는 문자열만 지원하므로 문자열로 저장
            putenv(sprintf('%s=%s', $name, $value));
            // $_ENV와 $_SERVER에는 파싱된 타입으로 저장
            $parsedValue = parseValue($value);
            $_ENV[$name] = $parsedValue;
            $_SERVER[$name] = $parsedValue;
        }
    }
}

/**
 * 환경 변수를 읽어 적절한 타입으로 반환합니다.
 *
 * @param string $name 환경 변수 이름
 * @param mixed $default 기본값 (환경 변수가 없을 경우 반환)
 * @return mixed 환경 변수 값 또는 기본값
 */
function readenv(string $name, $default = null) {
    $value = getenv($name);

    if ($value === false) {
        return $default;
    }

    $lower = strtolower($value);

    // boolean
    if (in_array($lower, ['true', '1', 'yes', 'on'], true)) {
        return true;
    }
    if (in_array($lower, ['false', '0', 'no', 'off'], true)) {
        return false;
    }

    // null
    if ($lower === 'null') {
        return null;
    }

    // 숫자
    if (is_numeric($value)) {
        return strpos($value, '.') !== false ? (float)$value : (int)$value;
    }

    // 기본 문자열
    return $value;
}

// /**
//  * 값을 적절한 타입으로 변환합니다.
//  *
//  * @param string $value 변환할 값
//  * @return mixed 변환된 값
//  */
// function parseValue($value)
// {
//     $lowerValue = strtolower($value);

//     if ($lowerValue === 'true') {
//         return true;
//     } elseif ($lowerValue === 'false') {
//         return false;
//     } elseif ($lowerValue === 'null' || $lowerValue === '') {
//         return null;
//     } elseif (is_numeric($value)) {
//         return strpos($value, '.') !== false ? (float)$value : (int)$value;
//     }

//     return $value;
// }

// /**
//  * 환경 변수를 타입이 보존된 값으로 읽습니다.
//  *
//  * @param string $key 환경 변수 키
//  * @param mixed $default 기본값 (변수가 없을 때 반환)
//  * @return mixed 타입이 보존된 환경 변수 값
//  */
// function readenv($key, $default = null)
// {
//     // $_ENV에 타입이 보존된 값이 저장되어 있음
//     if (array_key_exists($key, $_ENV)) {
//         return $_ENV[$key];
//     }

//     return $default;
// }

// /**
//  * 환경 변수를 문자열로 읽습니다.
//  *
//  * @param string $key 환경 변수 키
//  * @param string $default 기본값
//  * @return string 환경 변수 값
//  */
// function readenv_string($key, $default = '')
// {
//     $value = readenv($key, $default);
//     return (string)$value;
// }

// /**
//  * 환경 변수를 정수로 읽습니다.
//  *
//  * @param string $key 환경 변수 키
//  * @param int $default 기본값
//  * @return int 환경 변수 값
//  */
// function readenv_int($key, $default = 0)
// {
//     $value = readenv($key, $default);
//     return (int)$value;
// }

// /**
//  * 환경 변수를 실수로 읽습니다.
//  *
//  * @param string $key 환경 변수 키
//  * @param float $default 기본값
//  * @return float 환경 변수 값
//  */
// function readenv_float($key, $default = 0.0)
// {
//     $value = readenv($key, $default);
//     return (float)$value;
// }

// /**
//  * 환경 변수를 boolean으로 읽습니다.
//  *
//  * @param string $key 환경 변수 키
//  * @param bool $default 기본값
//  * @return bool 환경 변수 값
//  */
// function readenv_bool($key, $default = false)
// {
//     $value = readenv($key, $default);
//     if (is_bool($value)) {
//         return $value;
//     }
//     // 문자열인 경우 변환
//     $lowerValue = strtolower((string)$value);
//     return in_array($lowerValue, ['true', '1', 'yes', 'on'], true);
// }

// /**
//  * 환경 변수를 배열로 읽습니다 (콤마로 구분).
//  *
//  * @param string $key 환경 변수 키
//  * @param array $default 기본값
//  * @param string $separator 구분자
//  * @return array 환경 변수 값
//  */
// function readenv_array($key, $default = [], $separator = ',')
// {
//     $value = readenv($key);
//     if ($value === null) {
//         return $default;
//     }
//     if (is_array($value)) {
//         return $value;
//     }
//     $items = explode($separator, (string)$value);
//     return array_map('trim', $items);
// }

// /**
//  * 필수 환경 변수가 설정되어 있는지 확인합니다.
//  *
//  * @param array $required 필수 환경 변수 키 배열
//  * @throws Exception 필수 환경 변수가 없을 때
//  */
// function require_env(array $required)
// {
//     $missing = [];
//     foreach ($required as $key) {
//         if (!array_key_exists($key, $_ENV)) {
//             $missing[] = $key;
//         }
//     }
//     if (!empty($missing)) {
//         throw new Exception(
//             "필수 환경 변수가 설정되지 않았습니다: " . implode(', ', $missing)
//         );
//     }
// }
