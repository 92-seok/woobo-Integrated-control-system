
/*
 *
 * ====================================
 * ====================================
 *   _    _ ______  _      _____ ______
 * | |  | || ___ \| |    |_   _|| ___ \
 * | |  | || |_/ /| |      | |  | |_/ /
 * | |/\| || ___ \| |      | |  | ___ \
 * \  /\  /| |_/ /| |____ _| |_ | |_/ /
 *  \/  \/ \____/ \_____/ \___/ \____/
 * =====================================
 * =====================================
 *
 * */

import os from 'os';

// 현재 PC의 IP 주소를 얻는 함수
export function getLocalIP() {
    const networkInterfaces = os.networkInterfaces();
    for (const interfaceName in networkInterfaces) {
        const interfaces = networkInterfaces[interfaceName];
        for (const iface of interfaces) {
            if (iface.family === 'IPv4' && !iface.internal) {
                return iface.address; // 내부 네트워크가 아닌 IPv4 주소를 반환
            }
        }
    }
    return '127.0.0.1'; // 기본값으로 로컬호스트 반환
}

// yyyy-mm-dd hh:mm:ss 형식으로 변환하는 함수
export function formatDate(eventTime) {
    // 20240101235959 형식의 문자열을 분리
    const year = eventTime.substring(0, 4);
    const month = eventTime.substring(4, 6);
    const day = eventTime.substring(6, 8);
    const hour = eventTime.substring(8, 10);
    const minute = eventTime.substring(10, 12);
    const second = eventTime.substring(12, 14);

    // 'yyyy-mm-dd hh:mm:ss' 형식으로 반환
    return `${year}-${month}-${day} ${hour}:${minute}:${second}`;
}

//현재날짜

export function getToday() {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

export function yyyymmddhhmmss() {
    const now = new Date();

    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 +1
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');

    return `${year}${month}${day}${hours}${minutes}${seconds}`;
}


// 현재 날짜 시간
function getDateAndTime() {
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = ('0' + (currentDate.getMonth() + 1)).slice(-2); // 월은 0부터 시작하므로 1을 더하고, 두 자리로 맞추기 위해 slice 사용
    const day = ('0' + currentDate.getDate()).slice(-2); // 일도 두 자리로 맞추기 위해 slice 사용
    const hours = ('0' + currentDate.getHours()).slice(-2); // 시간도 두 자리로 맞추기 위해 slice 사용
    const minutes = ('0' + currentDate.getMinutes()).slice(-2); // 분도 두 자리로 맞추기 위해 slice 사용
    const seconds = ('0' + currentDate.getSeconds()).slice(-2); // 초도 두 자리로 맞추기 위해 slice 사용

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

// 'yyyy-mm-dd hh:mm:ss' 형식의 문자열을 'yyyymmddhhmmss' 형식으로 변환하는 함수
export function convertToCompactFormat(dateTimeStr) {
    // 공백과 -,:, 문자열을 제거
    return dateTimeStr.replace(/[-: ]/g, '');
}