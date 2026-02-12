/* eslint-disable no-undef */
// JavaScript Document

/* 웹브라우저 크기 조정시 Frame 크기 조정 */
window.addEventListener('resize', () => {
    frameBoxResize(sessionStorage.getItem('pStatus'));
});
/* 웹브라우저 크기 조정시 Frame 크기 조정 */

$(document).ready(function () {
    if (!pType) {
        alert('메뉴 프레임에 사용되는 include.js 에서 필수적으로 필요한 pType 변수가 정의되지 않았습니다.');
        return;
    }
    if (pType === '') {
        alert('메뉴 프레임에 사용되는 include.js 에서 필수적으로 필요한 pType 변수가 빈 문자열입니다.');
        return;
    }
    /* Page 전환 Log 기록 & Popup 상태 저장 및 메인페이지 팝업 열림 설정 */
    if (pType === 'main') {
        getlog('main', 'root/main.php', 'Move');
        //checkClock();
        let pStatus = sessionStorage.getItem('pStatus');

        if (pStatus == 'false') {
            setTimeout(() => {
                $('#id_popup').animate({ right: '0px' });
            }, 1 * 1000);
            $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
            pStatus = 'true';
            frameBoxResize(pStatus);

            sessionStorage.setItem('pStatus', pStatus);
        }
    } else if (pType === 'report') {
        getlog('report', 'root/report.php', 'Move');
    } else {
        // Top Bar Sub 메뉴 or 특보 전환
        $('#id_top_bar_submenu_' + pType).css('display', 'flex');
    }
    /* Page 전환 Log 기록 & Popup 상태 저장 및 메인페이지 팝업 열림 설정 */

    // 좌측 Main Menu 전환 시 띄울 첫 페이지
    startPage(pType);

    /* 도움말 */
    $(document).on('click', '#id_help', function () {
        let stat = $(this).attr('stat');

        if (stat == 'close') {
            $(this).attr('stat', 'open');
            $('.cs_help').css('display', 'block');
            document.querySelector('#id_helpMessage').innerText = '도움말 닫기';
        } else {
            $(this).attr('stat', 'close');
            $('.cs_help').css('display', 'none');
            document.querySelector('#id_helpMessage').innerText = '도움말 보기';
        }
    });
    /* 도움말 */

    /* 메뉴바 */
    // 좌측 메인 메뉴
    $(document).on('click', '.cs_menu_list', function () {
        if (sessionStorage.getItem('history') == null || sessionStorage.getItem('history') == '{}') {
            historyReset();
        }

        let data = JSON.parse(sessionStorage.getItem('history'));
        data.evt = 'true';
        sessionStorage.setItem('history', JSON.stringify(data));

        history.pushState(null, null, '');

                let url = $(this).attr('data-url');
        let menuId = $(this).attr('id');

        // CCTV 메뉴 클릭 시 우측 패널 열기
        if (menuId === 'id_menu_list_cctv') {

            // 사이드메뉴 액티브 스타일 적용 (배경 이미지 변경)
            $('.cs_menu_list').each(function() {
                var currentBg = $(this).css('background-image');
                if (currentBg.includes('_active')) {
                    var newBg = currentBg.replace('_active', '');
                    $(this).css('background-image', newBg);
                }
            });
            
            // CCTV 메뉴에 액티브 스타일 적용
            var cctvBg = $(this).css('background-image');
            if (!cctvBg.includes('_active')) {
                var activeBg = cctvBg.replace('.jpg', '_active.jpg');
                $(this).css('background-image', activeBg);
            }
            
            // 페이지 이동만 수행 (우측 패널과 탭은 새 페이지에서 처리)
            window.location.href = url;
            return;
        }

        // 다른 메뉴들에 대해서는 일반적인 페이지 이동
        window.location.href = url;
    });

    // 각 Frame 상단 서브 메뉴
    $(document).on('click', '#id_sub_link', function () {
        history.pushState(null, null, '');
        let idx = $(this).index();

        $('.cs_sub_link').removeClass('active');
        $(this).addClass('active');

        let url = $(this).attr('data-url');

        if (pType == 'data') {
            dType = $(this).attr('data-type');
            url += `?dType=${dType}`;
        }

        getFrame(url, pType, idx, 'true');
    });

    // Data Frame 서브메뉴 하부 버튼
    $(document).on('click', '#id_sub_btn', function (event) {
        event.stopPropagation();
        history.pushState(null, null, '');
        let url = $(this).attr('data-url');

        if (pType == 'data') {
            let dType = $(this).attr('data-type');
            let idx = $(this).parents('div').parents('div').index();

            $('.cs_sub_link').removeClass('active');
            $('.cs_sub_btn').removeClass('active');
            $(this).parents('div').parents('div').addClass('active');
            $(this).addClass('active');

            getFrame(`${url}?dType=${dType}`, pType, idx, 'true');
        } else if (pType == 'gate') {
            $('.cs_sub_btn').removeClass('active');
            $(this).addClass('active');

            getFrame(url, pType, 2, 'true');        
        } else if (pType == 'parking') {
            $('.cs_sub_btn').removeClass('active');
            $(this).addClass('active');

            getFrame(url, pType, 2, 'true');
        } else {
            console.log(`Unknown pType: ${pType}`);
        }
    });
    /* 메뉴바 */

    /* 뒤로가기 이벤트! */
    window.onpopstate = function (e) {
        let data = JSON.parse(sessionStorage.getItem('history'));

        let getType = data.type.split('#');
        let geturl = data.url.split('#');
        let getidx = data.idx.split('#');

        let mType = '';
        let url = '';
        try {
            for (let i = geturl.length - 1; i > 0; i--) {
                if (i == geturl.length - 1) {
                    data.url = geturl[i];
                    data.type = getType[i];
                    data.idx = getidx[i];

                    mType = getType[i];
                    url = geturl[i];
                } else {
                    data.url = geturl[i] + '#' + data.url;
                    data.type = getType[i] + '#' + data.type;
                    data.idx = getidx[i] + '#' + data.idx;
                }
            }
            data.evt = 'false';
            let bckevt = JSON.parse(sessionStorage.getItem('bckevt'));

            bckevt.type = getType[1];
            bckevt.url = geturl[1];
            bckevt.idx = getidx[1];
            bckevt.befortype = getType[0];
            bckevt.pType = pType;

            getlog(mType, url, 'Move');
            sessionStorage.setItem('bckevt', JSON.stringify(bckevt));
            sessionStorage.setItem('history', JSON.stringify(data));

            setTimeout(function () {
                bckevt.pType = '';

                sessionStorage.setItem('bckevt', JSON.stringify(bckevt));
            }, 3000);

            if (getType[1] == 'main') return;

            let sub = $('#id_top_bar_submenu_' + getType[1]).children();

            $('.cs_sub_link').removeClass('active');
            sub[getidx[1]].classList.add('active');

            getFrame(geturl[1], getType[1], -1, 'false');
        } catch (
            error // Browser Stack 처리
        ) {
            history.back(3);
            console.log(error);
        }
    };

    // Debug
    $(document).on('click', '.cs_logo', function () {
        historyReset();
    });
    /* 뒤로가기 이벤트! */

    $(document).on('click', '#id_testBtn', function () {
        let val = $(this).attr('value');

        if (val == 'test') val = 'equip';
        else val = 'test';

        $('#id_testBtn').attr('value', val);
    });
});

// Main Menu 전환 시 띄울 첫 페이지, document ready 에서 한 번 실행된다.
// 좌측 메인 메뉴 프레임 전환 시, 상단 메뉴가 있는 경우에 첫번째로 표출할 화면
function startPage(mType) {
    let data = JSON.parse(sessionStorage.getItem('history'));
    let stype = 'normal';
    let url = '';
    switch (mType) {
        case 'main':
        case 'report':
        case 'log':
        case 'cctv':
            // 전체 화면이나 팝업 등, 상단 메뉴가 없는 좌측 메뉴 프레임들
            return;
        case 'data':
            if (type == 'na') {
                stype = equip;
                url = 'table/Time.php';
            } else if (type == 'snow') {
                stype = 'snow';
                url = `table/Day.php?area=${equip}&dType=${stype}`;
            } else {
                stype = type;
                url = `table/Time.php?area=${equip}&dType=${stype}`;
            }
            break;
        case 'broad':
            url = `frame/broadForm.php?dType=${stype}`;
            break;
        case 'display':
            url = `frame/eachEquList.php?dType=${stype}`;
            break;
        case 'gate':
            url = `frame/passiveGate.php?dType=${stype}`;
            break;
        case 'sms':
            url = `frame/sendMsg.php?dType=${stype}`;
            break;
        case 'alert':
            url = `frame/alertList.php?dType=${stype}`;
            break;
        case 'parking':
            url = `frame/parkingCar.php?dType=${stype}`;
            break;
        case 'admin':
            url = `frame/manageUser.php?dType=${stype}`;
            break;
        case 'equip':
            url = `frame/equip.php?dType=${stype}`;
            break;
    }

    let idx = 0;
    if (mType == 'data') {
        if (stype == 'rain') idx = 0;
        else if (stype == 'water') idx = 1;
        else if (stype == 'dplace') idx = 2;
        else if (stype == 'snow') idx = 3;
        else if (stype == 'flood') idx = 4;
    } else if (mType == 'gate') {
        idx = 1; //임시
    }
    getFrame(url, mType, idx, data?.evt);
}

// 팝업 상태를 전역 변수로 관리
let isPopupOpen = false;

/* 반응형 Frame 틀 조정 */
function frameBoxResize(pState) {
    // 팝업 상태 업데이트
    if (pState !== undefined) {
        isPopupOpen = (pState == 'true');
    }
    
    // 현재 팝업 상태에 따라 popup 값 설정
    let popup = isPopupOpen ? 340 : 0;

    let winWidth = window.innerWidth - (220 + popup);
    let winHeight = window.innerHeight - 90;

    $('#id_frame_box').css({ width: `${winWidth}px` });
    $('#id_frame_box').css({ height: `${winHeight}px` });

    // $("#id_frame_box").animate({ width : `${winWidth}px` } );
    // $("#id_frame_box").animate({ height : `${winHeight}px` } );
    if (pType) {
        if (pType == 'main') {
            $('#id_top_bar').css({ width: `${winWidth}px` });
            $('#id_top_bar_submenu_main').css({ width: `${winWidth}px` });

            // $("#id_top_bar").animate({ width : `${winWidth}px` } );
            // $("#id_top_bar_submenu_main").animate({ width : `${winWidth}px` } );
        }
    }
}

/* 화면 전환 */
function getFrame(url, mType, idx, stack) {
    console.log('getFrame:', url, mType, idx, stack);

    $.ajax({
        url: url,
        dataType: 'html',
        type: 'GET',
        async: true,
        cache: false,
        success: function (data) {
            $('.cs_frame_box').empty().html(data);
            setFrame(url, mType, idx, stack);
            getlog(mType, url, 'Move');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

/* History에 Stack쌓고 sessionStorage에 추가 */
function setFrame(url, mType, idx, stack) {
    let data = JSON.parse(sessionStorage.getItem('history'));
    if (stack == 'false') {
        return;
    }

    if (sessionStorage.getItem('history') == null || sessionStorage.getItem('history') == '{}') {
        data = {};

        data.url = url;
        data.type = mType;
        data.idx = String(idx);
        data.evt = 'false';

        sessionStorage.setItem('pStatus', 'true');

        let backevt = {};

        backevt.mode = '';
        backevt.mType = '';
        backevt.url = '';
        backevt.type = '';
        backevt.befortype = '';
        backevt.idx = '';
        backevt.i = 0;

        sessionStorage.setItem('bckevt', JSON.stringify(backevt));
    } else {
        let typecnt = data.type.split('#');
        let urlcnt = data.url.split('#');
        let idxcnt = data.idx.split('#');
        if (data.evt == 'false') {
            for (let i = urlcnt.length - 1; i > 0; i--) {
                if (i == urlcnt.length - 1) {
                    data.url = urlcnt[i];
                    data.type = typecnt[i];
                    data.idx = String(idxcnt[i]);
                } else {
                    data.url = urlcnt[i] + '#' + data.url;
                    data.type = typecnt[i] + '#' + data.type;
                    data.idx = idxcnt[i] + '#' + data.idx;
                }
            }
        } else {
            if (urlcnt.length >= 50) {
                for (let i = urlcnt.length - 2; i >= 0; i--) {
                    if (i == urlcnt.length - 2) {
                        data.url = urlcnt[i];
                        data.type = typecnt[i];
                        data.idx = String(idxcnt[i]);
                    } else {
                        data.url = urlcnt[i] + '#' + data.url;
                        data.type = typecnt[i] + '#' + data.type;
                        data.idx = idxcnt[i] + '#' + data.idx;
                    }
                }
            }
        }
        data.url = url + '#' + data.url;
        data.type = mType + '#' + data.type;
        data.idx = idx + '#' + data.idx;
        data.evt = 'true';
    }
    sessionStorage.setItem('history', JSON.stringify(data));
}

/* Log 저장 */
function getlog(mType, url, action, equip = '', before = '', after = '', content = '') {
    let arrUrl = url.split('?');
    let lastUrl = arrUrl[0].split('/');
    let ip = sessionStorage.getItem('ip');
    let uid = sessionStorage.getItem('uid');

    $.ajax({
        url: '/include/session.php',
        type: 'POST',
        data: {
            mType: mType,
            uid: uid,
            url: lastUrl[1],
            ip: ip,
            action: action,
            equip: equip,
            before: before,
            after: after,
            content: content,
        },
        async: true,
        cache: false,
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

/* form 데이터 json형태의 객체로 변환 */
jQuery.fn.serializeObject = function () {
    let obj = null;
    try {
        // this[0].tagName이 form tag일 경우
        if (this[0].tagName && this[0].tagName.toUpperCase() == 'FORM') {
            let arr = this.serializeArray();
            if (arr) {
                obj = {};
                jQuery.each(arr, function () {
                    // obj의 key값은 arr의 name, obj의 value는 value값
                    obj[this.name] = this.value;
                });
            }
        }
    } catch (e) {
        alert(e.message);
    } finally {
    }
    return obj;
};

function FormToObject(f) {
    let returnObject = new Object();
    try {
        let myFormData = new FormData(f);

        myFormData.forEach((value, key, arr) => {
            if (returnObject[key] == undefined) returnObject[key] = value;
            else returnObject[key] += `,${value}`;
        });
    } catch (e) {
        console.log(e.message);
    }
    return returnObject;
}

/* form 데이터 json형태의 객체로 변환 */

/* History Stack Reset (Stack이 없는 경우 Error 방지) */
function historyReset() {
    let data = {};
    let backevt = {};

    data.url = 'main.php';
    data.type = 'main';
    data.idx = '0';
    data.evt = 'true';

    backevt.pType = 'main';
    backevt.url = 'main.php';
    backevt.type = 'main';
    backevt.befortype = 'main';
    backevt.idx = '0';
    backevt.i = 0;

    sessionStorage.setItem('history', JSON.stringify(data));
    sessionStorage.setItem('pStatus', 'true');
    sessionStorage.setItem('bckevt', JSON.stringify(backevt));
}

/* History Stack Reset (Stack이 없는 경우 Error 방지) */

/* 우측 패널 관련 함수들 */
function openRightPanel() {
    // 우측 패널이 없으면 생성
    if (!document.getElementById('cs_right_panel')) {
        createRightPanel();
    }
    
    // 우측 패널 열기
    const rightPanel = document.getElementById('cs_right_panel');
    if (rightPanel) {
        rightPanel.classList.add('open');
    }
}

function closeRightPanel() {
    const rightPanel = document.getElementById('cs_right_panel');
    if (rightPanel) {
        rightPanel.classList.remove('open');
    }
}

function PhonePattenChk(phone) {
    let phoneChk1 = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
    let phoneChk2 = /^01([0|1|6|7|8|9]?)([0-9]{7,8})$/;

    var strphone = phone.replace(/\s/g, '');

    if (strphone != '' && !phoneChk1.test(strphone)) {
        return false;
    }

    return strphone;
}

///////////////////////////////////////////////////////////////////////////////
// function includeHTML() {
//   var z, i, elmnt, file, xhttp;
//   /*loop through a collection of all HTML elements:*/
//   z = document.getElementsByTagName("*");
//   for (i = 0; i < z.length; i++) {
//     elmnt = z[i];
//     /*search for elements with a certain attribute:*/
//     file = elmnt.getAttribute("w3-include-html");
//     if (file) {
//       /*make an HTTP request using the attribute value as the file name:*/
//       xhttp = new XMLHttpRequest();
//       xhttp.onreadystatechange = function() {
//         if (this.readyState == 4) {
//           if (this.status == 200) {elmnt.innerHTML = this.responseText;}
//           if (this.status == 404) {elmnt.innerHTML = "Page not found.";}
//           /*remove the attribute, and call this function once more:*/
//           elmnt.removeAttribute("w3-include-html");
//           includeHTML();
//         }
//       }
//       xhttp.open("GET", file, true);
//       xhttp.send();
//       /*exit the function:*/
//       return;
//     }
//   }
// };

// includeHTML 사용법:
// <head>에 <script src="js/includeHTML.js"></script>를 넣어준다.
// <body>에 <div include-html="navbar.html"></div>를 넣고
// <script>includeHTML();</script>를 호출해준다.
// https://www.w3schools.com/howto/howto_html_include.asp

function includeHTML() {
    var allElements = document.getElementsByTagName('*');
    Array.prototype.forEach.call(allElements, (el) => {
        var includeHtml = el.dataset.includeHtml;
        if (includeHtml) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = () => {
                if (this.readyState == 4 && this.status == 200) {
                    el.outerHTML = this.responseText;
                }
            };
            xhttp.open('GET', includeHtml, true);
            xhttp.send();
        }
    });
}
