// JavaScript Document

$(document).ready(function () {

    // GATE_CCTV가 활성화된 경우 CCTV 탭 자동 열기
    if (window.gateCctv) {
        $(document).ready(function() {
            $('.cs_pCate').css('width', '20%');
        });
        // CCTV 페이지 로드 시 우측패널 자동 열기 및 CCTV 탭 활성화
        $(document).ready(function() {
            // 우측 패널 열기
            $('#id_popup').animate({ right: '0px' });
            $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
            // frameBoxResize('true');

            // CCTV 탭 활성화 및 내용 표시
            setTimeout(function() {
                // 모든 탭 비활성화
                $('.cs_pCate').removeClass('active');

                // CCTV 탭 활성화
                $('.cs_pCate[data-type="cctv"]').addClass('active');

                // CCTV 탭 내용 표시
                getPopupData('cctv');
            }, 0);
        });
    }

    $(document).on('click', '#id_allCheck', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $('.cs_gateChk').prop('checked', true);
        } else {
            $('.cs_gateChk').prop('checked', false);
        }
    });

    /* Page (공용) */
    $(document).on('click', '#id_page', function () {
        let url = $(this).attr('data-url');
        let idx = $(this).attr('data-idx');

        if (idx == '1') {
            let form = $('#id_form').serialize();
            url += `&${form}&dType=after`;
        } else url += `dType=before`;
        getFrame(`${url}`, idx, 'true');
    });

    /* Search! */
    $(document).on('click', '#id_search', function () {
        let url = '';
        let type = $('input[name=mode]').val();
        let form = $('#id_form').serialize();
        form = form.substr(4, form.length);
        form = form.replace('&', '?');

        if (type == 'result') url = 'frame/' + form + '&page=1&dType=after';
        else url = 'frame/' + form + 'dType=after';

        getFrame(`${url}`, -1, 'false');
    });

    /* Excel! */
    $(document).on('click', '#id_excel', function () {
        let form = $('#id_form').serialize();
        form = form.substr(4, form.length);
        form = form.replace('&', '?');
        form = form.replace('.', 'Excel.');

        let url = 'frame/excel/' + form;

        window.location.href = url;
    });

    /* 주차장그룹 디테일 진입 */
    $(document).on('click', '#id_grpList', function () {
        let num = $(this).attr('data-num');
        getFrame('frame/parkingCareAdd.php?num=' + num, 0, 'true');
    });

    /* 주차장그룹 등록/수정/삭제 */
    $(document).on('click', '#id_addBtn', function () {
        let num = '';
        let name = $('#id_title').val();
        let addr1 = $('#id_addr1').val();
        let addr2 = $('#id_addr2').val();
        let type = $(this).attr('data-type');
        let code = '';

        if (type != 'delete') {
            let count = 0;
            $('.cs_gateChk:checked').each(function () {
                if (count++ == 0) {
                    code = $(this).val();
                } else {
                    code = code + ',' + $(this).val();
                }
            });

            if (name == '') {
                alert('이름을 입력하세요');
                return;
            }

            if (addr1 == '') {
                alert('주소를 입력하세요');
                return;
            }

            if (code == '') {
                alert('차단기를 선택하세요');
                return;
            }
        }

        if (type != 'insert') num = $(this).attr('data-num');

        saveAddr(num, name, addr1, addr2, code, type);
    });

    /* 차량 입출차 내역 Delete! */
    $(document).on('click', '#id_delBtn', function () {
        let num = '';
        let type = $(this).attr('data');
        let count = 0;

        $('.cs_gateChk:checked').each(function () {
            if (count++ == 0) num = $(this).val();
            else num = num + ',' + $(this).val();
        });

        if (confirm('선택하신 내역을 정말 삭제하시겠습니까?') == true) removeCarHist(num, type);
    });

    /* Check 문자 발송 위해 popup 띄우기 */
    $(document).on('click', '#id_msgBtn', function () {
        let num = '';
        let count = 0;
        let checkHnsLpr = $('#checkHnsLprValue').val();

        $('.cs_gateChk:checked').each(function () {
            if (count++ === 0) {
                num = $(this).val();
            } else {
                num = num + ',' + $(this).val();
            }
        });

        if (num === '') {
            alert('문자 전송 할 차량을 선택하세요.');
            return false;
        }

        window.open(`frame/msgbox.php?num=${num}&checkHnsLpr=${checkHnsLpr}`, 'Message Box', 'width=510, height=170, left=10, top=10, status=no, toolbar=no, scrollbars=no');
    });

    /* 차량 입출차 내역 차량 번호 이미지 활성화 */
    $(document).on('mouseover', '.cs_imgLink', function (e) {
        var url = $(this).attr('data-url');
        let pageYHeight = 0;

        if (e.pageY >= 625) pageYHeight = 625;
        else pageYHeight = e.pageY;

        $('body').append("<div class='cs_imgBox'></div>");
        $('.cs_imgBox')
            .css('border', '2px solid #5E60CD')
            .css('top', pageYHeight + 'px')
            .css('left', e.pageX - 385 + 'px')
            .fadeIn('fast');

        $.ajax({
            url: url,
            dataType: 'html',
            type: 'GET',
            async: true,
            cache: false,
            success: function (data) {
                $('.cs_imgBox').html(data);
            },
            error: function (request, status, error) {
                alert('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
            },
        });
    });

    $(document).on('mouseout', '.cs_imgLink', function () {
        $('.cs_imgBox').remove();
    });

    /* 일단 보류 */
    $(document).on('click', '#id_remove', function () {
        var code = '';
        var count = 0;
        $('.userCode:checked').each(function () {
            if (count++ == 0) code = $(this).val();
            else code = code + ',' + $(this).val();
        });

        if (code == '') {
            alert('삭제할 번호를 선택해주세요');
            return;
        }

        if (confirm('삭제하시겠습니까?') == true) {
            removeuser(code);
        }
    });

    $(document).on('click', '#id_addmsgBtn', function () {
        getFrame('frame/parkingMentAdd.php?dType=before', 4, 'true');
    });

    /* 차단기 수동 제어 */
    $(document).on('click', '#id_gateBtn', function (e) {
        let num = $(this).attr('data-num');
        let gate = $(this).attr('data-type');

        if (confirm('차단기 상태를 변경하시겠습니까?') == true) {
            let gateBtn = document.querySelectorAll(`.gate${num}`);

            gateBtn.forEach((el) => {
                el.style.backgroundColor = '#5e60cd';
            });

            e.target.style.backgroundColor = '#282bca';
            saveGate(num, gate);
        }
    });

    // SMS 전송
    $(document).on('click', '#id_sendBtn', function () {
        let content = document.querySelector('#id_content').value;
        let num = document.querySelector('#id_number').value;
        let checkHnsLpr = document.querySelector('#id_checkHnsLpr').value;

        if (content == '') {
            alert('전송할 내용을 입력해주세요.');
            return false;
        }

        $.ajax({
            url: '../server/usersms.php',
            type: 'POST',
            async: true,
            cache: false,
            data: {
                type: 'insert',
                content: content,
                num: num,
                checkHnsLpr: checkHnsLpr,
            },
            success: function (data) {
                let num = JSON.parse(data);
                alert('정상적으로 처리되었습니다.');
                getlog('gate', 'frame/parkingCar.php', 'Car SMS Send', num['num'], '', '', content);
                window.close();
            },
            error: function (request, status, error) {
                //alert("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
        });
    });
});

/* 차량 입출차 내역 삭제 */
function removeCarHist(num, type) {
    $.ajax({
        url: 'server/removeuser.php',
        type: 'POST',
        data: { num: num, type: type },
        async: true,
        cache: false,
        success: function (data) {
            let num = JSON.parse(data);
            alert('정상적으로 처리되었습니다.');
            getlog('gate', 'frame/parkingCar.php', 'Car History Delete', num['num']);
            getFrame('frame/parkingCar.php?dType=before', -1, 'false');
        },
        error: function (request, status, error) {
            alert('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

//주소 검색하기!! (오픈소스)
function sample6_execDaumPostcode() {
    new daum.Postcode({
        oncomplete: function (data) {
            // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

            // 각 주소의 노출 규칙에 따라 주소를 조합한다.
            // 내려오는 변수가주소 변수
            var extraAddr = ''; // 참고항목 변수

            //사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
            if (data.userSelectedType === 'R') {
                // 사용자가 도로명 주소를 선택했을 경우
                addr = data.roadAddress;
            } else {
                // 사용자가 지번 주소를 선택했을 경우(J)
                addr = data.jibunAddress;
            }

            // 우편번호와 주소 정보를 해당 필드에 넣는다.
            document.getElementById('id_addr1').value = addr;
            // 커서를 상세주소 필드로 이동한다.
            document.getElementById('id_addr2').focus();
            //값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
            var addr = ''; //
        },
    }).open();
}

function saveAddr(num, name, addr1, addr2, code, type) {
    $.ajax({
        url: 'server/serverAddr.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: {
            num: num,
            name: name,
            addr1: addr1,
            addr2: addr2,
            code: code,
            type: type,
        },
        success: function (data) {
            alert('저장되었습니다');

            if (data.code == '00') getlog('gate', 'frame/parkingCare.php', 'Parking Area Insert', data.equip, data.before, data.after);
            else if (data.code == '10') getlog('gate', 'frame/parkingCare.php', 'Parking Area Update', data.equip, data.before, data.after);
            else getlog('gate', 'frame/parkingCare.php', 'Parking Area Delete', data.equip, data.before, data.after);

            getFrame('frame/parkingCare.php', -1, 'false');
        },
        error: function (request, status, error) {
            alert('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveGate(num, gate) {
    $.ajax({
        url: 'server/serverGate.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: { num: num, gate: gate, saveType: 'save' },
        success: function (data) {
            if (data.code == '00') {
                alert('변경되었습니다');
                getlog('gate', 'frame/passiveGate.php', 'Gate Control', data.equip, '', gate);
            } else {
                alert(data.message);
            }
        },
        error: function (request, s0tatus, error) {
            alert('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

// CCTV 관련 변수들
let currentPlayer = null;
let currentSocket = null;
let currentTimer = null;
let cctvDataCache = {};


/* DB 상태를 확인하고 버튼 색상 및 상태 텍스트를 업데이트하는 함수 */
function updateButtonStates() {
    $.ajax({
        url: '/gate/frame/check_status.php',
        method: 'GET',
        dataType: 'json',
        success: function(serverStatuses) {
            // 서버에서 받은 상태를 기반으로 각 버튼의 상태를 업데이트합니다.
            for (const num in serverStatuses) {
                if (serverStatuses.hasOwnProperty(num)) {
                    const status = serverStatuses[num].toLowerCase();

                    // 해당 차단기의 모든 버튼에서 'active' 클래스 제거
                    $(`.gate${num}`).removeClass("active");

                    // 현재 상태에 해당하는 버튼에 'active' 클래스 추가
                    $(`.gate${num}[data-type='${status}']`).addClass("active");

                    // '상태' 텍스트 업데이트
                    $(`#status-text-${num}`).text(status);

                    // 현재 상태 라벨 업데이트
                    const currentStatusLabel = (status === 'open') ? '열림' : (status === 'close') ? '닫힘' : status;
                    $(`#status-text-${num}`).text(currentStatusLabel);
                }
            }
        },
        error: function(xhr, status, error) {
            console.error('상태 확인 실패:', status, error);
        }
    });
}

/* 필요 시 jsmpg 로드 */
function ensureJsmpgLoaded(callback){
    if (typeof jsmpeg !== 'undefined') {
        console.log('JSMpeg 이미 로드됨');
        callback();
        return;
    }

    console.log('JSMpeg 로드 중...');
    var s = document.createElement('script');
    s.src = '/js/jsmpg.js';
    s.onload = function() {
        console.log('JSMpeg 로드 완료');
        callback();
    };
    s.onerror = function() {
        console.error('JSMpeg 로드 실패');
        callback();
    };
    document.head.appendChild(s);
}

// 기존 스트림 정리 함수
function cleanupCctv(){
    // 기존 웹소켓/캔버스 방식 정리
    if (currentTimer) { clearTimeout(currentTimer); currentTimer = null; }
    if (currentPlayer && currentPlayer.destroy) { try { currentPlayer.destroy(); } catch(e){} }
    currentPlayer = null;
    if (currentSocket && currentSocket.readyState === WebSocket.OPEN) {
        try { currentSocket.close(); } catch(e){}
    }
    currentSocket = null;

    // HTTP API 방식 정리
    const video = document.getElementById('cctv-video');
    if (video) {
        // 비디오 스트림 중단
        video.pause();
        video.src = '';
        video.load(); // 비디오 요소 리셋

        // 이벤트 리스너 제거 (메모리 누수 방지)
        video.onloadstart = null;
        video.oncanplay = null;
        video.onended = null;
        video.onerror = null;
    }
}

// CCTV 데이터 로드 함수
function loadCctvData(callback) {
    if (Object.keys(cctvDataCache).length > 0) {
        callback();
        return;
    }

    $.ajax({
        url: '/include/server/cctvData.php',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log('CCTV 데이터 로드됨:', data);

            // CCTV 관련 스크립트 로드
            if (!window.cctvScriptsLoaded) {
                // jsmpg.js 로드
                if (typeof JSMpeg === 'undefined') {
                    var script1 = document.createElement('script');
                    script1.src = '/js/jsmpg.js';
                    document.head.appendChild(script1);
                }

                // // cctv.js 로드
                // var script2 = document.createElement('script');
                // script2.src = '/js/cctv.js';
                // document.head.appendChild(script2);

                window.cctvScriptsLoaded = true;
            }

            // 콜백함수에 cctv 데이터 전달
            callback(data);
        },
        error: function(xhr, status, error) {
            console.error('CCTV 데이터 로드 실패:', error);
            return;
        }
    });
}

/* 일정시간 동안 CCTV 이미지 반복 갱신 함수 (열림/닫힘 버튼 클릭 시 호출) */
function showCCTVForDuration(duration, eType) {
    // 플레이스홀더/스트림 영역 토글
    $('#cctvPlaceholder').hide();
    $('#cctvStreamArea').show();

     // CCTV 데이터 로드 후 실행
    loadCctvData(function(cctvData) {
            // 기존 스트림 정리
            cleanupCctv();

            // 차단기의 EType 값과 CCTV의 CD_DIST_OBSV 값이 일치하는 CCTV 데이터(targetCctvData) 찾기
            let targetCctvData;
            for (const item of cctvData) {
                console.log(item);
                if (item.cd_dist_obsv === eType) {
                    targetCctvData = item;
                    break;
                }
            }

            // 일치하는 CCTV 데이터가 없으면 에러 메시지 출력
            if (!targetCctvData) {
                console.error('해당 차단기에 연결된 CCTV 데이터를 찾을 수 없습니다:', eType);
                return;
            }

            // cctv 관련 변수 선언
            const canvas = document.getElementById('cctvCanvas');
            const cctvHost = window.cctvHost; // .env 파일에 테스트용으로 설정된 호스트 주소(localhost) (TODO: 추후 .env.sample 파일에 실제 서버 호스트 주소로 환경변수 변경 요망)
            const cctvPort = targetCctvData.conn_port;

            // 선언한 변수들 확인
            if (!canvas) {
                console.error('CCTV 캔버스 요소를 찾을 수 없습니다.');
                return;
            }
            if (!cctvHost) {
                console.error('CCTV_HOST 환경변수가 없습니다.');
                return;
            }
            if (!cctvPort) {
                console.error('CCTV 데이터에 유효한 conn_port 값이 없습니다.');
                return;
            }

            currentSocket = new WebSocket(`ws://${cctvHost}:${cctvPort}`);

            currentPlayer = new jsmpeg(currentSocket, {
                canvas: canvas,
                autoplay: true,
                forceCanvas2D: true,
                onSourceEstablished: function(source) {
                    console.log('CCTV 소스 설정됨:', source);
                },
                onVideoFrame: function(decoder, timestamp) {
                    console.log('비디오 프레임 수신:', timestamp);
                },
                onDecode: function(decoder, time) {
                    console.log('디코딩 진행:', time);
                }
            });
            console.log('JSMpeg 플레이어 생성됨:', currentPlayer);

            currentSocket.onerror = function(err){
                console.error('CCTV 소켓 오류', err);
                console.error(`연결 실패한 CCTV: ${targetCctvData.nm_dist_obsv} (${cctvHost}:${cctvPort})`);
            };

            console.log('=== CCTV 캡처 시작 ===');

            // duration 동안 1초마다 CCTV 이미지 반복 갱신하는 타이머
            window.captureInterval = setInterval(function(){
                const now = new Date();

                // HH:mm:ss 형식으로 시간 포맷
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const timestamp = `${hours}:${minutes}:${seconds}`;

                // 이미지 캡처 로직
                if (currentPlayer) {
                    try {
                        // 캔버스에서 현재 프레임을 이미지로 캡처
                        const capturedImage = canvas.toDataURL('image/jpeg', 0.8);

                        // 캡처된 이미지를 화면에 표시 (기존 캔버스 위에 오버레이)
                        const imgElement = document.createElement('img');
                        imgElement.src = capturedImage;
                        imgElement.style.cssText = `
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            object-fit: fill;
                            z-index: 10;
                        `;

                        // 기존 오버레이 이미지가 있다면 제거
                        const existingOverlay = document.querySelector('#cctvCanvas + img');
                        if (existingOverlay) {
                            existingOverlay.remove();
                        }

                        // 새로운 오버레이 이미지 추가
                        imgElement.id = 'capturedFrame';
                        canvas.parentNode.appendChild(imgElement);

                        console.log('CCTV 프레임 캡처 완료:', timestamp);
                    } catch (error) {
                        console.error('CCTV 프레임 캡처 실패:', error);
                    }
                }

            }, 2000); // 2초 간격으로 캡처

            // 일정시간(duration) 후 종료 및 플레이스홀더 복귀
            currentTimer = setTimeout(function(){
                console.log(`=== CCTV 캡처 종료: ${targetCctvData.nm_dist_obsv} ===`);

                // 타이머 종료 후 현재상태 이미지로 최종 업데이트
                const now = new Date();

                // HH:mm:ss 형식으로 시간 포맷
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                const timestamp = `${hours}:${minutes}:${seconds}`;

                // 이미지 캡처 로직
                if (currentPlayer) {
                    try {
                        // 캔버스에서 현재 프레임을 이미지로 캡처
                        const capturedImage = canvas.toDataURL('image/jpeg', 0.8);

                        // 캡처된 이미지를 화면에 표시 (기존 캔버스 위에 오버레이)
                        const imgElement = document.createElement('img');
                        imgElement.src = capturedImage;
                        imgElement.style.cssText = `
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            object-fit: fill;
                            z-index: 10;
                        `;

                        // 기존 오버레이 이미지가 있다면 제거
                        const existingOverlay = document.querySelector('#cctvCanvas + img');
                        if (existingOverlay) {
                            existingOverlay.remove();
                        }

                        // 새로운 오버레이 이미지 추가
                        imgElement.id = 'capturedFrame';
                        canvas.parentNode.appendChild(imgElement);

                        console.log('현재상태 CCTV 프레임으로 최종 업데이트 완료:', timestamp);
                    } catch (error) {
                        console.error('현재상태 CCTV 프레임으로 최종 업데이트 실패:', error);
                    }
                }

                cleanupCctv();
                // $('#cctvStreamArea').hide();
                // $('#cctvPlaceholder').show();
            }, duration);




    });
}


/* CCTV 10초 스트리밍 함수 (로우 클릭 시 호출) */
function playCctvForTenSeconds(eType){
    // 플레이스홀더/스트림 영역 토글
    $('#cctvPlaceholder').hide();
    $('#cctvStreamArea').show();

    // CCTV 데이터 로드 후 실행
    loadCctvData(function(cctvData) {
            // 기존 스트림 정리
            cleanupCctv();

            // 차단기의 EType 값과 CCTV의 CD_DIST_OBSV 값이 일치하는 CCTV 데이터(targetCctvData) 찾기
            let targetCctvData;
            for (const item of cctvData) {
                console.log(item);
                if (item.cd_dist_obsv === eType) {
                    targetCctvData = item;
                    break;
                }
            }

            // 일치하는 CCTV 데이터가 없으면 에러 메시지 출력
            if (!targetCctvData) {
                console.error('해당 차단기에 연결된 CCTV 데이터를 찾을 수 없습니다:', eType);
                return;
            }

            // cctv 관련 변수 선언
            const canvas = document.getElementById('cctvCanvas');
            const cctvHost = window.cctvHost; // .env 파일에 테스트용으로 설정된 호스트 주소(localhost) (TODO: 추후 .env.sample 파일에 실제 서버 호스트 주소로 환경변수 변경 요망)
            const cctvPort = targetCctvData.conn_port;

            // 선언한 변수들 확인
            if (!canvas) {
                console.error('CCTV 캔버스 요소를 찾을 수 없습니다.');
                return;
            }
            if (!cctvHost) {
                console.error('CCTV_HOST 환경변수가 없습니다.');
                return;
            }
            if (!cctvPort) {
                console.error('CCTV 데이터에 유효한 conn_port 값이 없습니다.');
                return;
            }

            currentSocket = new WebSocket(`ws://${cctvHost}:${cctvPort}`);

            currentPlayer = new jsmpeg(currentSocket, {
                    canvas: canvas,
                    autoplay: true,
                });

            currentSocket.onerror = function(err){
                console.error('CCTV 소켓 오류', err);
                console.error(`연결 실패한 CCTV: ${targetCctvData.nm_dist_obsv} (${cctvHost}:${cctvPort})`);
            };

            // 10초 후 정리 및 플레이스홀더 복귀
            currentTimer = setTimeout(function(){
                console.log(`CCTV 스트리밍 종료: ${targetCctvData.nm_dist_obsv}`);
                cleanupCctv();
                // $('#cctvStreamArea').hide();
                // $('#cctvPlaceholder').show();

                // 모든 CCTV 버튼을 플레이 상태로 되돌리기
                $('.cctv-icon-btn').each(function() {
                    const button = $(this);
                    button.find('.stop-icon').hide();
                    button.find('.play-icon').show();
                    button.attr('data-state', 'play');
                    button.attr('title', '실시간 CCTV 재생');
                });

                // 로우 강조 제거
                $('.henry_datatable tbody tr.gate-row').removeClass('selected');

                /// TEST popup.js
                cctvTab();
            }, 1000 * 20); // 20초
    });

}

// cctv.js api 추가 전 기존 차단기 페이지 초기화 함수 (삭제 금지)
function initPassiveGate() {
    // 도움말 툴팁 클릭 이벤트 핸들러
    $("#id_help, #id_helpMessage").on("click", function() {
        $(".cs_help").slideToggle(200);
    });

    // 열림/닫힘 버튼 클릭 이벤트 핸들러 - 해당 차단기의 CCTV 이미지를 일정시간 동안 반복 갱신
    $(document).on("click", ".cs_btn[data-type='open'], .cs_btn[data-type='close']", function(e) {
        const button = $(this);
        const num = $(this).attr("data-num");
        const gate = $(this).attr("data-type");

        if (confirm("차단기 상태를 변경하시겠습니까?")) {
            // 기존에 재생 중인 스트림이 있다면 정리
            if (currentPlayer || currentSocket || currentTimer) {
                console.log('기존 스트림 정리 중...');
                cleanupCctv();

                // 모든 CCTV 버튼을 플레이 상태로 되돌리기
                $('.cctv-icon-btn').each(function() {
                    const btn = $(this);
                    btn.find('.stop-icon').hide();
                    btn.find('.play-icon').show();
                    btn.attr('data-state', 'play');
                    btn.attr('title', '실시간 CCTV 재생');
                });

            }

            // 로우 강조 처리
            $('.henry_datatable tbody tr.gate-row').removeClass('selected');
            button.closest('tr').addClass('selected');

            const eType = $(this).attr("data-etype");
            if (!eType) return;
            // CCTV 이미지 10초간 반복 갱신
            // showCCTVForDuration(12000, String(eType)); (TODO: 추후 서버단에서 다른 방식으로 구현 요망. 임시로 스트리밍 함수로 대체.)
            playCctvForTenSeconds(String(eType));

            // 기존 saveGate 함수 호출 (만약 있다면)
            if (typeof saveGate === 'function') {
                saveGate(num, gate);
            }
        }
    });

    // CCTV버튼 클릭 이벤트 핸들러 - 플레이/정지 상태에 따른 동작
    $(document).on('click', '.cctv-icon-btn', function(e) {
        const button = $(this);
        const currentState = button.attr('data-state');
        const eType = button.attr("data-etype");

        if (!eType) return;

        if (currentState === 'play') {
            // 플레이 상태: 스트리밍 시작
            // 기존에 재생 중인 스트림이 있다면 정리
            if (currentPlayer || currentSocket || currentTimer) {
                console.log('기존 스트림 정리 중...');
                cleanupCctv();
                $('#cctvStreamArea').hide();
                $('#cctvPlaceholder').show();

                // 모든 CCTV 버튼을 플레이 상태로 되돌리기
                $('.cctv-icon-btn').each(function() {
                    const btn = $(this);
                    btn.find('.stop-icon').hide();
                    btn.find('.play-icon').show();
                    btn.attr('data-state', 'play');
                    btn.attr('title', '실시간 CCTV 재생');
                });

            }

            // 로우 강조 처리
            $('.henry_datatable tbody tr.gate-row').removeClass('selected');
            button.closest('tr').addClass('selected');

            // 아이콘을 정지 아이콘으로 변경
            button.find('.play-icon').hide();
            button.find('.stop-icon').show();
            button.attr('data-state', 'stop');
            button.attr('title', 'CCTV 정지');

            // CCTV 10초간 스트리밍 시작
            playCctvForTenSeconds(String(eType));

        } else if (currentState === 'stop') {
            // 정지 상태: 스트리밍 수동 종료
            // 로우 강조 제거
            $('.henry_datatable tbody tr.gate-row').removeClass('selected');

            // 아이콘을 플레이 아이콘으로 변경
            button.find('.stop-icon').hide();
            button.find('.play-icon').show();
            button.attr('data-state', 'play');
            button.attr('title', '실시간 CCTV 재생');

            // CCTV 정리 및 플레이스홀더 복귀
            cleanupCctv();
            $('#cctvStreamArea').hide();
            $('#cctvPlaceholder').show();
        }
    });

    // 페이지 로드 시 초기 상태를 업데이트
    updateButtonStates();

    // 5~10초 마다 상태 업데이트를 반복
    setInterval(updateButtonStates, 5000);
}


/* 저품질 API 연동 함수 (차단기관리 페이지용) */
function initLowQualityGate(eType) {
// 플레이스홀더/스트림 영역 토글
    $('#cctvPlaceholder').hide();
    $('#cctvStreamArea').show();

    // CCTV 데이터 로드 후 실행
    loadCctvData(function(cctvData) {
            // 기존 스트림 정리
            cleanupCctv();

            // 차단기의 EType 값과 CCTV의 CD_DIST_OBSV 값이 일치하는 CCTV 데이터(targetCctvData) 찾기
            let targetCctvData;
            for (const item of cctvData) {
                console.log(item);
                if (item.cd_dist_obsv === eType) {
                    targetCctvData = item;
                    break;
                }
            }

            // 일치하는 CCTV 데이터가 없으면 에러 메시지 출력
            if (!targetCctvData) {
                console.error('해당 차단기에 연결된 CCTV 데이터를 찾을 수 없습니다:', eType);
                return;
            }

            const video = document.getElementById('cctv-video');
            const cctvHost = window.cctvHost; // .env 파일에 테스트용으로 설정된 호스트 주소(localhost) (TODO: 추후 .env.sample 파일에 실제 서버 호스트 주소로 환경변수 변경 요망)

            // 요청을 위한 파라미터 설정
            // DB에 등록된 cctv 장비 기준으로 하드코딩된 부분 (TODO: 추후 서버단에서 cdDistObsv 값에 따라 다른 방식으로 구현 요망)
            const nameDistObsv = targetCctvData.nm_dist_obsv;
            const cdDistObsv = targetCctvData.cd_dist_obsv;
            const connIP = targetCctvData.conn_ip;

            // let camera;
            // if (nameDistObsv === 'CCTV_HIKVISION') {
            //     camera = 'hikvision';
            // } else if (nameDistObsv === 'CCTV_PHAROS') {
            //     camera = 'pharos';
            // } else {
            //     console.error('해당하는 name_dist_obsv의 CCTV 데이터를 찾을 수 없습니다:', nameDistObsv);
            //     return;
            // }

            const duration = 11;

            // api 요청
            const STREAM_URL = `http://${cctvHost}:3001/stream-low?conn_ip=${connIP}&duration=${duration}`;
            video.src = STREAM_URL;

            // 디버깅용 코드
            video.onloadstart = function() {
                console.log('CCTV 스트림 로딩 중', nameDistObsv, cdDistObsv);
            };

            video.oncanplay = function() {
                console.log('CCTV 스트림 재생 중', nameDistObsv, cdDistObsv);
            };

            video.onended = function() {
                console.log('CCTV 스트림 재생 종료', nameDistObsv, cdDistObsv);
            };

            video.onerror = function(e) {
                console.error('CCTV 스트림 재생 오류:', e);
            };

    });
}

/* 고품질 API 연동 함수 (CCTV관리 페이지용) */
function initHighQualityGate(eType) {
// 플레이스홀더/스트림 영역 토글
    $('#cctvPlaceholder').hide();
    $('#cctvStreamArea').show();

    // CCTV 데이터 로드 후 실행
    loadCctvData(function(cctvData) {
            // 기존 스트림 정리
            cleanupCctv();

            // 차단기의 EType 값과 CCTV의 CD_DIST_OBSV 값이 일치하는 CCTV 데이터(targetCctvData) 찾기
            let targetCctvData;
            for (const item of cctvData) {
                console.log(item);
                if (item.cd_dist_obsv === eType) {
                    targetCctvData = item;
                    break;
                }
            }

            // 일치하는 CCTV 데이터가 없으면 에러 메시지 출력
            if (!targetCctvData) {
                console.error('해당 차단기에 연결된 CCTV 데이터를 찾을 수 없습니다:', eType);
                return;
            }

            const video = document.getElementById('cctv-video');
            const cctvHost = window.cctvHost; // .env 파일에 테스트용으로 설정된 호스트 주소(localhost) (TODO: 추후 .env.sample 파일에 실제 서버 호스트 주소로 환경변수 변경 요망)

            // 요청을 위한 파라미터 설정
            // DB에 등록된 cctv 장비 기준으로 하드코딩된 부분 (TODO: 추후 서버단에서 cdDistObsv 값에 따라 다른 방식으로 구현 요망)
            const nameDistObsv = targetCctvData.nm_dist_obsv;
            const cdDistObsv = targetCctvData.cd_dist_obsv;
            const connIP = targetCctvData.conn_ip;

            // let camera;
            // if (nameDistObsv === 'CCTV_HIKVISION') {
            //     camera = 'hikvision';
            // } else if (nameDistObsv === 'CCTV_PHAROS') {
            //     camera = 'pharos';
            // } else {
            //     console.error('해당하는 name_dist_obsv의 CCTV 데이터를 찾을 수 없습니다:', name_dist_obsv);
            //     return;
            // }

            const duration = 10;

            // api 요청
            const STREAM_URL = `http://${cctvHost}:3001/stream-high?conn_ip=${connIP}&duration=${duration}`;
            video.src = STREAM_URL;

            // 디버깅용 코드
            video.onloadstart = function() {
                console.log('CCTV 스트림 로딩 중', nameDistObsv, cdDistObsv);
            };

            video.oncanplay = function() {
                console.log('CCTV 스트림 재생 중', nameDistObsv, cdDistObsv);
            };

            video.onended = function() {
                console.log('CCTV 스트림 재생 종료', nameDistObsv, cdDistObsv);
            };

            video.onerror = function(e) {
                console.error('CCTV 스트림 재생 오류:', e);
            };

    });
}

/* 차단기 페이지 초기화 함수 */
// function initPassiveGate() {
//     // 도움말 툴팁 클릭 이벤트 핸들러
//     $("#id_help, #id_helpMessage").on("click", function() {
//         $(".cs_help").slideToggle(200);
//     });

//     // 열림/닫힘 버튼 클릭 이벤트 핸들러 - 해당 차단기의 CCTV 이미지를 일정시간 동안 반복 갱신
//     $(document).on("click", ".cs_btn[data-type='open'], .cs_btn[data-type='close']", function(e) {
//         const button = $(this);
//         const num = $(this).attr("data-num");
//         const gate = $(this).attr("data-type");

//         if (confirm("차단기 상태를 변경하시겠습니까?")) {
//             // 기존에 재생 중인 스트림이 있다면 정리
//             if (currentPlayer || currentSocket || currentTimer) {
//                 console.log('기존 스트림 정리 중...');
//                 cleanupCctv();

//                 // 모든 CCTV 버튼을 플레이 상태로 되돌리기
//                 $('.cctv-icon-btn').each(function() {
//                     const btn = $(this);
//                     btn.find('.stop-icon').hide();
//                     btn.find('.play-icon').show();
//                     btn.attr('data-state', 'play');
//                     btn.attr('title', '실시간 CCTV 재생');
//                 });

//             }

//             // 로우 강조 처리
//             $('.henry_datatable tbody tr.gate-row').removeClass('selected');
//             button.closest('tr').addClass('selected');

//             const eType = $(this).attr("data-etype");
//             if (!eType) return;

//             // 저품질 스트림 시작
//             initLowQualityGate(eType);

//             // 기존 saveGate 함수 호출 (만약 있다면)
//             if (typeof saveGate === 'function') {
//                 saveGate(num, gate);
//             }
//         }
//     });

//     // CCTV버튼 클릭 이벤트 핸들러 - 플레이/정지 상태에 따른 동작
//     $(document).on('click', '.cctv-icon-btn', function(e) {
//         const button = $(this);
//         const currentState = button.attr('data-state');
//         const eType = button.attr("data-etype");

//         if (!eType) return;

//         if (currentState === 'play') {
//             // 플레이 상태: 스트리밍 시작
//             // 기존에 재생 중인 스트림이 있다면 정리
//             if (currentPlayer || currentSocket || currentTimer) {
//                 console.log('기존 스트림 정리 중...');
//                 cleanupCctv();
//                 $('#cctvStreamArea').hide();
//                 $('#cctvPlaceholder').show();

//             }

//             // 로우 강조 처리
//             $('.henry_datatable tbody tr.gate-row').removeClass('selected');
//             button.closest('tr').addClass('selected');

//             // 저품질 스트림 시작
//             initLowQualityGate(eType);

//         } else if (currentState === 'stop') {
//             // 정지 상태: 스트리밍 수동 종료
//             // 로우 강조 제거
//             $('.henry_datatable tbody tr.gate-row').removeClass('selected');

//             // CCTV 정리 및 플레이스홀더 복귀
//             cleanupCctv();
//             $('#cctvStreamArea').hide();
//             $('#cctvPlaceholder').show();
//         }
//     });

//     // 페이지 로드 시 초기 상태를 업데이트
//     updateButtonStates();

//     // 5~10초 마다 상태 업데이트를 반복
//     setInterval(updateButtonStates, 5000);
// }
