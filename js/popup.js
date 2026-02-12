
let autoCount = -1;
let viewCount = 10;
let refreshTime = 60 / viewCount;
let allRefresh = 6;
let pStatus = true;
var autoalertTimer;

$(document).ready(function () {
    /* 통합 관제 시스템 어시스던트 */
    popupPage();
    // getPopupData('alert');
    getPopupData('radar');
    autoalertTimer = autoAlert(autoalertTimer, 1);

    $(document).on('click', '#id_autoAlertChk', () => {
        autoalertTimer = autoAlert(autoalertTimer, 1);
    });

    // 계측, 장비 현황 Item Drop
    $(document).on('click', '.cs_pLargeTitle', function (e) {
        let type = e.target.attributes['value'].value;
        let text = e.target.innerText;
        let stat = e.target.attributes['stat'].value;

        if (stat == 'close') {
            e.target.style.backgroundColor = '#f2f2f2';
            e.target.innerText = text.replace('>', '∨');
            e.target.attributes['stat'].value = 'open';
            if (type == 'rdrimg' || type == 'satimg' || type == 'chkAlert' || type == 'chkResult') {
                document.querySelector(`#${type}`).style.display = 'block';
                if (type == 'chkResult') document.querySelector('#id_data_alert').style.backgroundColor = 'blue';
            } else {
                $(`.${type}`).css('display', 'table');
                if (type != 'content' && type != 'way' && type != 'list') e.target.parentElement.children[1].style.display = 'table';
                else if (type == 'content') document.querySelector('#asBtn').style.display = 'table';
                else if (type == 'way') document.querySelector('#sendBtn').style.display = 'table';
            }
        } else {
            e.target.style.backgroundColor = '#fff';
            e.target.innerText = text.replace('∨', '>');
            e.target.attributes['stat'].value = 'close';
            if (type == 'rdrimg' || type == 'satimg' || type == 'chkAlert' || type == 'chkResult') {
                document.querySelector(`#${type}`).style.display = 'none';
                if (type == 'chkResult') document.querySelector('#id_data_alert').style.backgroundColor = '#fff';
            } else {
                $(`.${type}`).css('display', 'none');
                if (type != 'content' && type != 'way' && type != 'list') e.target.parentElement.children[1].style.display = 'none';
                else if (type == 'content') document.querySelector('#asBtn').style.display = 'none';
                else if (type == 'way') document.querySelector('#sendBtn').style.display = 'none';
            }
        }
    });

    // 계측, 장비 전체 점검
    $(document).on('click', '#id_refresh', function (e) {
        let val = e.target.attributes['value'].value;

        switch (val) {
            case '01':
            case '02':
            case '03':
            case '04':
            case '06':
            case '08':
            case '21':
                $.ajax({
                    url: '/include/server/dataPopup.php',
                    dataType: 'html',
                    async: true,
                    cache: false,
                    success: function (data) {
                        $('#id_data_data').empty().append(data);
                        document.querySelector('.cs_successMessage').innerText = '계측현황 업데이트를 완료했습니다.';
                        $('.cs_successMessage').fadeIn(1000);
                    },
                    error: function (request, status, error) {
                        console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
                    },
                });
                break;
            case '17':
            case '18':
            case '20':
                let openPopup = window.open(`/include/server/equipgroupsetting.php?val=${val}`, 'Message Box', 'width=400, height=285, left=10, top=10, resizable=no, status=no, toolbar=no, scrollbars=no');
                openPopup.onbeforeunload = (e) => {
                    $.ajax({
                        url: '/include/server/equipPopup.php',
                        dataType: 'html',
                        async: true,
                        cache: false,
                        success: function (data) {
                            $('#id_data_equip').empty().append(data);
                        },
                        error: function (request, status, error) {
                            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
                        },
                    });
                };
                break;
        }

        setTimeout(() => {
            $('.cs_successMessage').fadeOut(1000, 'linear');
        }, 2 * 1000);
    });

    // 장비 현황 점검 시작 (30초)
    $(document).on('click', '#id_check', function (e) {
        let val = e.target.attributes['value'].value;

        let openPopup = window.open(`/include/server/equipsetting.php?val=${val}`, 'Message Box', 'width=400, height=350, left=10, top=10, resizable=no, status=no, toolbar=no, scrollbars=no');
        openPopup.onbeforeunload = (e) => {
            $.ajax({
                url: '/include/server/equipPopup.php',
                dataType: 'html',
                async: true,
                cache: false,
                success: function (data) {
                    $('#id_data_equip').empty().append(data);
                },
                error: function (request, status, error) {
                    console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
                },
            });
        };
    });

    // 장비 현황 AS접수 클릭 이벤트
    $(document).on('click', '#id_as', function (e) {
        let type = e.target.attributes['value'].value;

        $.ajax({
            url: '/include/server/assetting.php',
            dataType: 'json',
            type: 'GET',
            data: { class: 'equipAS', type: type },
            async: true,
            cache: false,
            success: function (data) {
                // Popup A/S접수페이지로 변경
                getPopupData('as');

                // pCate active ClassName 제거
                let form = document.querySelectorAll('.cs_pCate');
                for (let i = 0; i < form.length; i++) {
                    form[i].classList.remove('active');
                }

                // pCate A/S쪽 active ClassName 추가
                document.querySelectorAll('.cs_pCate')[3].classList.add('active');

                // 대분류, 중분류, 장비 자동선택
                if (data.large == 'measurement') document.querySelector('#large').options[2].selected = true;
                else if (data.large == 'equip') document.querySelector('#large').options[3].selected = true;
                $('#middle').empty().append(`<option value='${data.middle}'>${data.middleNM}</option>`);
                $('#equip').empty().append(`<option value='${data.CD_DIST_OBSV}' equip='${data.sensor}'>${data.NM_DIST_OBSV} ${data.sensor}</option>`);

                // 내용 장비 상태 오류로 변경
                if (data.large == 'measurement') document.querySelector('#content').options[3].selected = true;
                else if (data.large == 'equip') document.querySelector('#content').options[1].selected = true;
                document.querySelector('#input').style.display = 'none';
            },
            error: function (request, status, error) {
                console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
            },
        });
    });

    // AS 대분류
    $(document).on('change', '#large', function () {
        let type = document.querySelector('#large').value;

        $.ajax({
            url: '/include/server/assetting.php',
            dataType: 'html',
            type: 'GET',
            data: { class: 'large', type: type },
            async: true,
            cache: false,
            success: function (data) {
                $('#middle').empty().append(data);
                $('#equip').empty().append("<option value= '' disabled selected>중분류 선택</option>");
            },
            error: function (request, status, error) {
                console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
            },
        });
    });

    // AS 중분류
    $(document).on('change', '#middle', function () {
        let type = document.querySelector('#middle').value;

        $.ajax({
            url: '/include/server/assetting.php',
            dataType: 'html',
            type: 'GET',
            data: { class: 'middle', type: type },
            async: true,
            cache: false,
            success: function (data) {
                $('#equip').empty().append(data);
            },
            error: function (request, status, error) {
                console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
            },
        });
    });

    // AS 내용
    $(document).on('change', '#content', function () {
        let type = document.querySelector('#content').value;

        if (type == 'input') document.querySelector('#input').style.display = 'table-row';
        else document.querySelector('#input').style.display = 'none';
    });

    // AS 추가하기 버튼 활성화
    $(document).on('click', '#asBtn', function () {
        let table = document.querySelector('#id_asList');
        let large = document.querySelector('#large');
        let middle = document.querySelector('#middle');
        let equip = document.querySelector('#equip');
        let content = document.querySelector('#content');
        let form = document.querySelector('#id_sendform');
        let input = '';

        let formContent = document.createElement('input');
        let tr = document.createElement('tr');
        let td = [];
        let deleteIcon = document.createElement('span');
        deleteIcon.setAttribute('class', 'material-symbols-outlined delete');
        deleteIcon.setAttribute('value', equip.value);
        deleteIcon.innerText = 'delete';

        if (content.options[4].selected) {
            input = document.querySelector('#inputContent').value;
        } else input = content[content.selectedIndex].innerText;

        for (let i = 0; i < 4; i++) {
            td[i] = document.createElement('td');
        }

        td[0].innerText = table.rows.length;
        td[1].innerText = equip[equip.selectedIndex].innerText;
        td[2].innerText = input;

        td[3].setAttribute('id', 'id_asListSubBtn');
        td[3].style.cursor = 'pointer';
        td[3].appendChild(deleteIcon);

        formContent.setAttribute('type', 'hidden');
        formContent.setAttribute('name', `${equip.value}`);
        formContent.setAttribute('value', `${input}`);

        tr.setAttribute('align', 'center');
        for (let i = 0; i < 4; i++) {
            tr.appendChild(td[i]);
        }

        if (large.value == '' || middle.value == '') {
            alert('장비를 선택해주세요.');
            return false;
        }

        if (content.value == '') {
            alert('A/S 내용을 선택해주세요.');
            return false;
        }

        table.appendChild(tr);
        form.appendChild(formContent);

        // AS테이블 리셋
        large.options[0].selected = true;
        $('#middle').empty().append("<option value= '' disabled selected>대분류 선택</options>");
        $('#equip').empty().append("<option value= '' disabled selected>대분류 선택</options>");
        content.options[0].selected = true;
        document.querySelector('#input').style.display = 'none';
        document.querySelector('#inputContent').value = '';
    });

    // ASList 요소 제거 이벤트
    $(document).on('click', '#id_asListSubBtn', function (e) {
        let val = e.target.attributes['value'].value;
        document.querySelector(`input[name='${val}']`).remove();

        e.target.parentElement.parentElement.remove();
        let table = document.querySelector('#id_asList');

        for (let i = 1; i < table.rows.length; i++) {
            table.rows[i].cells[0].innerText = i;
        }
    });

    // A/S 접수 (문자, 메일 보내기)
    $(document).on('click', '#sendBtn', function () {
        let formData = FormToObject(document.querySelector('#id_sendform'));

        if (formData.mailChk != undefined) {
            let mailpatten = /[\w\-\.]+\@[\w\-\.]+\.[\w\-\.]/g;

            if (formData.email == '' || !mailpatten.test(formData.email)) {
                alert('E-Mail 주소가 적혀있지 않거나, E-Mail 패턴이 맞지 않습니다.');
                return false;
            }
        }
        if (formData.phoneChk != undefined) {
            if (!PhonePattenChk(formData.phoneNum)) {
                alert('접수 번호의 번호 체계에 맞지 않습니다.');
                return false;
            }
        }
        if (formData.from == '') {
            alert('발신인(지역명)을 적어주세요.');
            return false;
        }
        if (formData.from.length > 10) {
            alert('발신인(지역명)은 10글자 내로 적어주세요.');
            return false;
        }

        formData.receivedType = 'menual';
        formData.uId = sessionStorage.getItem('uid');

        console.log(formData);

        $.ajax({
            url: '/include/server/sendMail.php',
            type: 'post',
            data: JSON.stringify(formData),
            async: false,
            cache: false,
            success: function () {
                document.querySelector('.cs_successMessage').innerText = 'A/S접수를 완료했습니다.';
                $('.cs_successMessage').fadeIn(1000);

                $.ajax({
                    url: '/include/server/asPopup.php',
                    dataType: 'html',
                    async: true,
                    cache: false,
                    success: function (data) {
                        $('#id_data_as').empty().append(data);
                    },
                    error: function (request, status, error) {
                        console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
                    },
                });
            },
            error: function (request, status, error) {
                console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
            },
        });
        setTimeout(() => {
            $('.cs_successMessage').fadeOut(1000, 'linear');
        }, 2 * 1000);
    });

    // Pop Up 숨기기/보이기
    $(document).on('click', '#id_pBtn', function () {
        let pStatus = sessionStorage.getItem('pStatus');
        if (pStatus == 'true') {
            $('#id_popup').animate({ right: '-360px' });
            $('#id_pBtn').css('background-image', 'url(/image/popup_open.png)');
            pStatus = 'false';
            frameBoxResize(pStatus);
        } else if (pStatus == 'false') {
            $('#id_popup').animate({ right: '0px' });
            $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
            pStatus = 'true';
            frameBoxResize(pStatus);
        } else {
            //sessionStorage 날아가서 pStatus가 null 일때 Popup 상태 확인하여 작동
            let right = document.querySelector('#id_popup').style.right;

            if (right == '0px') {
                $('#id_popup').animate({ right: '-360px' });
                $('#id_pBtn').css('background-image', 'url(/image/popup_open.png)');
                pStatus = 'false';
                frameBoxResize(pStatus);
            } else {
                $('#id_popup').animate({ right: '0px' });
                $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
                pStatus = 'true';
                frameBoxResize(pStatus);
            }
        }
        sessionStorage.setItem('pStatus', pStatus);
    });

    // Pop Up Button Mouse Over Event
    // $(document).on('mouseover', '#id_pBtn', function () {
    //     let pStatus = sessionStorage.getItem('pStatus');
    //     if (pStatus == 'false') {
    //         $('#id_popup').animate({ right: '-360px' }, 150, 'swing');
    //     }
    // });

    // $(document).on('mouseout', '#id_pBtn', function () {
    //     let pStatus = sessionStorage.getItem('pStatus');
    //     if (pStatus == 'false') {
    //         $('#id_popup').animate({ right: '-380px' }, 150, 'swing');
    //     }
    // });

    // Pop Up Tab Click Event
    $(document).on('click', '.cs_pCate', function () {
        $('.cs_pCate').removeClass('active');
        $(this).addClass('active');

        let type = $(this).attr('data-type');
        getPopupData(type);

        autoCount = $(this).index();
    });
    /* 통합 관제 시스템 어시스던트 */

    // // 팝업이 처음 로드될 때 위성영상 탭 데이터 초기화
    // setTimeout(function() {
    //     if ($('#id_data_radar').is(':visible')) {
    //         radarTab();
    //     }
    // }, 100);
});

/*****************************************************************************************
									Pop Up Part Function
 *****************************************************************************************/
// AI점검현황 Update
function autoAlert(alertTimer, alertCount = 0) {
    let divide = 60; // 점검 할 시간 (초)

    if (document.querySelector('#id_autoAlertChk').checked) {
        // 장비 자동 AS & 점검요망 PART
        if (alertCount % divide == 0) {
            let resultContainer = document.querySelector('#id_resultContainer');
            let alertContainer = document.querySelector('#id_alertContainer');
            // let count = alertContainer.childElementCount;

            // 한 Popup상 45개의 글만 추가. 45개 초과시 첫번째 요소 삭제 후 글 추가
            // if( alertContainer.childElementCount > 45 )
            // {
            // 	alertContainer.children[(count-1)].remove();
            // }

            let auth = sessionStorage.getItem('auth');
            if (auth == null) {
                alert('세션이 만료되었습니다.');
                window.location.href = '/login/logout.php';
            } else if (auth == 'admin') {
                let ajax = new XMLHttpRequest();
                let url = `/include/server/realtimealert.php?alertType=equip`;

                ajax.open('GET', url);
                ajax.responseType = 'json';
                ajax.send();

                ajax.onload = () => {
                    if (ajax.status === 200) {
                        const data = ajax.response;
                        let sec = 0;

                        let today = new Date();
                        let year = today.getFullYear();
                        let month = ('0' + (today.getMonth() + 1)).slice(-2);
                        let day = ('0' + today.getDate()).slice(-2);
                        let hours = ('0' + today.getHours()).slice(-2);
                        let minutes = ('0' + today.getMinutes()).slice(-2);
                        let seconds = ('0' + today.getSeconds()).slice(-2);
                        let logTime = `${year}.${month}.${day} ${hours}:${minutes}`;

                        if (data == null || (Object.keys(data[0]).length == 0 && Object.keys(data[1]).length == 0 && Object.keys(data[2]).length == 0)) {
                            let content = document.createElement('div');
                            content.textContent = `[${logTime}] 장비 점검 결과 - 정상`;
                            content.setAttribute('class', 'cs_alertComent');

                            alertContainer.prepend(content);
                        } else {
                            let comment = data[0];
                            let asSend = data[1];
                            let alert = data[2];

                            if (Object.keys(comment).length != 0) {
                                let rtContent = document.createElement('div');
                                rtContent.style.color = 'sandyBrown';
                                rtContent.textContent = `[${logTime}] 장비 점검 결과 - 오류`;
                                rtContent.setAttribute('class', 'cs_alertComent');

                                alertContainer.prepend(rtContent);

                                let cnt = Object.keys(comment).length;
                                let content = document.createElement('div');
                                content.setAttribute('class', 'cs_alertComent');

                                let startDivide = document.createElement('p');
                                startDivide.setAttribute('class', 'cs_alertP');
                                startDivide.style.overflow = 'hidden';
                                startDivide.style.whiteSpace = 'nowrap';
                                startDivide.innerHTML = `----------------------------------------------------------------`;
                                content.appendChild(startDivide);

                                let startP = document.createElement('p');
                                startP.setAttribute('class', 'cs_alertP');
                                startP.style.fontWeight = 'bold';
                                startP.innerHTML = `[${logTime}] 자동 A/S 접수<br/>`;

                                content.appendChild(startP);

                                for (let key in comment) {
                                    let equipP = document.createElement('p');
                                    equipP.setAttribute('class', 'cs_alertP');
                                    equipP.innerHTML = `&nbsp;&nbsp;- ${comment[key]}<br/>`;

                                    content.appendChild(equipP);
                                }

                                let endP = document.createElement('p');
                                endP.setAttribute('class', 'cs_alertP');
                                endP.innerHTML = `&nbsp;&nbsp;점검결과 오류 ${cnt}건`;

                                content.appendChild(endP);

                                let endDivide = document.createElement('p');
                                endDivide.setAttribute('class', 'cs_alertP');
                                endDivide.style.overflow = 'hidden';
                                endDivide.style.whiteSpace = 'nowrap';
                                endDivide.innerHTML = `----------------------------------------------------------------`;
                                content.appendChild(endDivide);

                                asSend.uId = sessionStorage.getItem('uid');
                                asSend.mailChk = 'on';
                                asSend.phoneChk = 'on';
                                asSend.receivedType = 'auto';

                                const sendAjax = new XMLHttpRequest();

                                sendAjax.open('POST', '/include/server/sendMail.php');
                                sendAjax.setRequestHeader('content-type', 'application/json');
                                sendAjax.send(JSON.stringify(asSend));

                                sendAjax.onload = () => {
                                    if (sendAjax.status !== 200) console.log('Error');
                                };

                                if (Object.keys(comment).length > 1) {
                                    setTimeout(
                                        () => {
                                            resultContainer.prepend(content);
                                        },
                                        (0 + sec) * 1000
                                    );
                                } else resultContainer.prepend(content);
                                sec += 0.5;
                            }

                            if (Object.keys(alert).length != 0) {
                                let rtContent = document.createElement('div');
                                rtContent.style.color = 'sandyBrown';
                                rtContent.textContent = `[${logTime}] 장비 점검 결과 - 점검 필요`;
                                rtContent.setAttribute('class', 'cs_alertComent');

                                alertContainer.prepend(rtContent);

                                let cnt = Object.keys(alert).length;
                                let content = document.createElement('div');
                                content.setAttribute('class', 'cs_alertComent');

                                let startDivide = document.createElement('p');
                                startDivide.setAttribute('class', 'cs_alertP');
                                startDivide.style.overflow = 'hidden';
                                startDivide.style.whiteSpace = 'nowrap';
                                startDivide.innerHTML = `----------------------------------------------------------------`;
                                content.appendChild(startDivide);

                                let startP = document.createElement('p');
                                startP.setAttribute('class', 'cs_alertP');
                                startP.style.fontWeight = 'bold';
                                startP.innerHTML = `[${logTime}] 점검 필요 항목<br/>`;

                                content.appendChild(startP);

                                for (let key in alert) {
                                    let equipP = document.createElement('p');
                                    equipP.setAttribute('class', 'cs_alertP');
                                    equipP.innerHTML = `&nbsp;&nbsp;- ${alert[key]}<br/>`;

                                    content.appendChild(equipP);
                                }

                                let endP = document.createElement('p');
                                endP.setAttribute('class', 'cs_alertP');
                                endP.innerHTML = `&nbsp;&nbsp;점검결과 점검 필요 ${cnt}건`;

                                content.appendChild(endP);

                                let endDivide = document.createElement('p');
                                endDivide.setAttribute('class', 'cs_alertP');
                                endDivide.style.overflow = 'hidden';
                                endDivide.style.whiteSpace = 'nowrap';
                                endDivide.innerHTML = `----------------------------------------------------------------`;
                                content.appendChild(endDivide);

                                if (Object.keys(alert).length > 1) {
                                    setTimeout(
                                        () => {
                                            resultContainer.prepend(content);
                                        },
                                        (0 + sec) * 1000
                                    );
                                } else resultContainer.prepend(content);
                                sec += 0.5;
                            }
                        }
                    } else console.log('Error');
                };
            } else {
                clearTimeout(alertTimer);
                document.querySelector('#id_autoAlertChk').checked = false;
            }

            // $.ajax(
            // 	{
            // 		url: "/include/server/dataPopup.php",
            // 		dataType:"html",
            // 		async:true,
            // 		cache:false,
            // 		success: function(data)
            // 		{
            // 			$("#id_data_data").empty().append(data);
            // 		},
            // 		error:function(request,status,error)
            // 		{
            // 			console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            // 		}
            // 	});
            //
            // $.ajax(
            // 	{
            // 		url: "/include/server/equipPopup.php",
            // 		dataType:"html",
            // 		async:true,
            // 		cache:false,
            // 		success: function(data)
            // 		{
            // 			$("#id_data_equip").empty().append(data);
            // 		},
            // 		error:function(request,status,error)
            // 		{
            // 			console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            // 		}
            // 	});
            //
            // $.ajax(
            // 	{
            // 		url: "/include/server/radarPopup.php",
            // 		dataType:"html",
            // 		async:true,
            // 		cache:false,
            // 		success: function(data)
            // 		{
            // 			$("#id_data_radar").empty().append(data);
            // 		},
            // 		error:function(request,status,error)
            // 		{
            // 			console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            // 		}
            // 	});
        }

        // 경보 자동 알림 PART
        if (alertCount < 2) {
            alertCount = 3 * divide + 1; // 경보 체크 타이밍 + 1초 ((분 * 60) + 초)

            let resultContainer = document.querySelector('#id_resultContainer');
            let alertContainer = document.querySelector('#id_alertContainer');
            const alertajax = new XMLHttpRequest();
            let url = `/include/server/realtimealert.php?alertType=alert`;

            alertajax.open('GET', url);
            alertajax.responseType = 'json';
            alertajax.send();

            alertajax.onload = () => {
                if (alertajax.status === 200) {
                    const data = alertajax.response;
                    let sec = 0;

                    if (!!data) {
                        data.forEach((el, idx) => {
                            let today = new Date();
                            let year = today.getFullYear();
                            let month = ('0' + (today.getMonth() + 1)).slice(-2);
                            let day = ('0' + today.getDate()).slice(-2);
                            let hours = ('0' + today.getHours()).slice(-2);
                            let minutes = ('0' + today.getMinutes()).slice(-2);
                            let seconds = ('0' + today.getSeconds()).slice(-2);
                            let logTime = `${year}.${month}.${day} ${hours}:${minutes}`;

                            if (idx === 0) {
                                if (Object.keys(el).length == 0) {
                                    let content = document.createElement('div');
                                    content.textContent = `[${logTime}] 경보 발령 확인 결과 - 정상`;
                                    content.setAttribute('class', 'cs_alertComent');

                                    alertContainer.prepend(content);
                                } else {
                                    let alertComent = document.querySelector('#id_resultContainer').querySelector('#id_alertComent');
                                    if (alertComent != null) alertComent.remove();

                                    let rtcontent = document.createElement('div');
                                    rtcontent.style.color = 'Orange';
                                    rtcontent.textContent = `[${logTime}] 경보 발령 확인 결과 - 발령중`;
                                    rtcontent.setAttribute('class', 'cs_alertComent');
                                    alertContainer.prepend(rtcontent);

                                    let cnt = Object.keys(el).length;
                                    let content = document.createElement('div');
                                    content.setAttribute('class', 'cs_alertComent');
                                    content.setAttribute('id', 'id_alertComent');

                                    let startDivide = document.createElement('p');
                                    startDivide.setAttribute('class', 'cs_alertP');
                                    startDivide.style.overflow = 'hidden';
                                    startDivide.innerHTML = `----------------------------------------------------`;
                                    content.appendChild(startDivide);

                                    let startP = document.createElement('p');
                                    startP.setAttribute('class', 'cs_alertP');
                                    startP.style.fontWeight = 'bold';
                                    startP.innerHTML = `[${logTime}] 자동 경보 발령 확인 결과<br/>`;

                                    content.appendChild(startP);

                                    for (let key in el) {
                                        let equipP = document.createElement('p');
                                        equipP.setAttribute('class', 'cs_alertP');
                                        equipP.innerHTML = `&nbsp;&nbsp;- ${el[key]} ${key}발령<br/>`;

                                        content.appendChild(equipP);
                                    }

                                    let endP = document.createElement('p');
                                    endP.setAttribute('class', 'cs_alertP');
                                    endP.innerHTML = `&nbsp;&nbsp;점검결과 경보발령 ${cnt}건`;

                                    content.appendChild(endP);

                                    let endDivide = document.createElement('p');
                                    endDivide.setAttribute('class', 'cs_alertP');
                                    endDivide.style.overflow = 'hidden';
                                    endDivide.innerHTML = `----------------------------------------------------`;
                                    content.appendChild(endDivide);

                                    resultContainer.prepend(content);
                                    sec += 0.5;
                                }
                            }
                        });
                    } else {
                        console.log('Error', 'data is null');
                    }
                } else {
                    console.log('Error');
                }
            };
        }

        let EquipCheckCount = alertCount % divide;
        document.querySelector('#id_alertCount').innerText = `(${EquipCheckCount})`;

        alertCount--;
        alertTimer = setTimeout(() => {
            alertTimer = autoAlert(alertTimer, alertCount);
        }, 1 * 1000);

        return alertTimer;
    } else {
        document.querySelector('#id_alertCount').innerText = ``;
        clearTimeout(alertTimer);

        return alertTimer;
    }
}

// 팝업 상태
function popupPage() {
    let pStatus = sessionStorage.getItem('pStatus');

    if (pStatus == 'false') {
        $('#id_popup').css('right', '-380px');
        $('#id_pBtn').css('background-image', 'url(/image/popup_open.png)');
        frameBoxResize('false');
    } else if (pStatus == 'true') {
        $('#id_popup').css('right', '0px');
        $('#id_pBtn').css('background-image', 'url(/image/popup_close.png)');
        frameBoxResize('true');
    }
}

// 5분마다 PopUp 메뉴 전환 (사용 안할 예정)
function checkClock() {
    if (refreshTime <= 0 && pType == 'main') {
        removeMarker();
        getMarker();

        allRefresh--;
        refreshTime = 60 / viewCount;
        getPopupData('alert');
        getPopupData('data');
        getPopupData('equip');
        getPopupData('as');
        getPopupData('radar');
    } else if (allRefresh <= 0 && pType == 'main') {
        window.location.reload();
    }

    if (allRefresh <= 0 && pType == 'main') {
        window.location.reload();
    } else if (refreshTime <= 0) {
        allRefresh--;
        refreshTime = 600 / viewCount;
        getPopupData('alert');
        getPopupData('data');
        getPopupData('equip');
        getPopupData('as');
        getPopupData('radar');

        if (pType == 'main') {
            removeMarker();
            getMarker();
        }
    }

    autoCount++;
    if (autoCount == 5) {
        autoCount = 0;
    }
    $('.cs_pCate').eq(autoCount).trigger('click'); //popup창 로테이션

    //console.log( autoCount + " " +refreshTime + " " + allRefresh ); // 디버그

    refreshTime--;
    setTimeout('checkClock()', viewCount * 1000); // 50초마다 printClock() 함수 호출
}

// 해당 PopUp 메뉴로 전환
function getPopupData(type) {
    let dataForm = document.querySelectorAll('.cs_dataForm');

    switch (type) {
        case 'data':
            dataTab();
            break;
        case 'equip':
            equipTab();
            break;
        case 'radar':
            radarTab();
            break;
        case 'cctv':
            cctvTab();
            break;
    }

    for (let i = 0; i < dataForm.length; i++) {
        dataForm[i].style.display = 'none';
    }
    document.querySelector(`#id_data_${type}`).style.display = 'block';
}
/*****************************************************************************************
									Pop Up Part Function
 *****************************************************************************************/
function dataTab() {
    $.ajax({
        url: '/include/server/dataPopup.php',
        dataType: 'html',
        async: true,
        cache: false,
        success: function (data) {
            $('#id_data_data').empty().append(data);
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}
function equipTab() {
    $.ajax({
        url: '/include/server/equipPopup.php',
        dataType: 'html',
        async: true,
        cache: false,
        success: function (data) {
            $('#id_data_equip').empty().append(data);
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}
function radarTab() {
    $.ajax({
        url: '/include/server/radarPopup.php',
        dataType: 'html',
        async: true,
        cache: false,
        success: function (data) {
            // $('#id_data_radar').empty().append(data);

            //위성 레이더 api
            const now = new Date();
            const minutes = now.getUTCMinutes();
            const roundedMinutes = Math.floor((minutes - 10) / 10) * 10;
            const adjustedDate = new Date(Date.UTC(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), now.getUTCHours(), roundedMinutes));

            const year = adjustedDate.getUTCFullYear();
            const month = String(adjustedDate.getUTCMonth() + 1).padStart(2, '0'); // 월은 0부터 시작하므로 +1
            const day = String(adjustedDate.getUTCDate()).padStart(2, '0');
            const hours = String(adjustedDate.getUTCHours()).padStart(2, '0');
            const mins = String(adjustedDate.getUTCMinutes()).padStart(2, '0');
            const UTCDate = `${year}${month}${day}${hours}${minutes - 10}`;
            const formattedDate = `${year}${month}${day}${hours}${mins}`;

            if (UTCDate === formattedDate) {
                const apiKey = 'n65C0BAR6m2GMFZXZwdZrx88DOJcPRlZ98ncirg5WQjBusPiIJ6rGnAulINcnwnZNherQp1hQkDDNNc04pu5Hg%3D%3D';
                let satXhr = new XMLHttpRequest();
                let satUrl = 'https://apis.data.go.kr/1360000/SatlitImgInfoService/getInsightSatlit';
                let satParams = '?' + encodeURIComponent('serviceKey') + '=' + apiKey;
                satParams += '&' + encodeURIComponent('pageNo') + '=' + encodeURIComponent('1');
                satParams += '&' + encodeURIComponent('numOfRows') + '=' + encodeURIComponent('1');
                satParams += '&' + encodeURIComponent('dataType') + '=' + encodeURIComponent('XML');
                satParams += '&' + encodeURIComponent('sat') + '=' + encodeURIComponent('G2');
                satParams += '&' + encodeURIComponent('data') + '=' + encodeURIComponent('ir105');
                satParams += '&' + encodeURIComponent('area') + '=' + encodeURIComponent('ko');
                satParams += '&' + encodeURIComponent('time') + '=' + encodeURIComponent(formattedDate);
                satXhr.open('GET', satUrl + satParams);
                let satImg = 'https://www.kma.go.kr/repositary/image/sat/gk2a/KO/gk2a_ami_le1b_rgb-true_ko010lc_' + formattedDate + '.thn.png';
                satXhr.send();

                let rdrXhr = new XMLHttpRequest();
                let rdrUrl = 'https://apis.data.go.kr/1360000/SatlitImgInfoService/getInsightSatlit';
                var rdrParams = '?' + encodeURIComponent('serviceKey') + '=' + apiKey; /*Service Key*/
                rdrParams += '&' + encodeURIComponent('pageNo') + '=' + encodeURIComponent('1'); /**/
                rdrParams += '&' + encodeURIComponent('numOfRows') + '=' + encodeURIComponent('10'); /**/
                rdrParams += '&' + encodeURIComponent('dataType') + '=' + encodeURIComponent('XML'); /**/
                rdrParams += '&' + encodeURIComponent('data') + '=' + encodeURIComponent('CMP_WRC'); /**/
                rdrParams += '&' + encodeURIComponent('time') + '=' + encodeURIComponent(formattedDate); /**/
                rdrXhr.open('GET', (rdrUrl = +rdrParams));
                let rdrImg = 'https://www.kma.go.kr/repositary/image/rdr/img/RDR_CMP_WRC_' + formattedDate + '.png';
                rdrXhr.send();

                $('#id_data_radar').empty().append(data);
                $('#satimg').html("<div class='cs_radarImage' id='satimg' style='background-image:url(" + satImg + ");'></div>");
                $('#rdrimg').html("<div class='cs_radarImage' id='rdrimg' style='background-image:url(" + rdrImg + ");'></div>");

                // // 위성영상과 레이더영상 요소가 존재하는지 확인 후 이미지 적용
                // setTimeout(function() {
                //     if ($('#satimg').length > 0) {
                //         $('#satimg').css('background-image', 'url(' + satImg + ')');
                //     }
                //     if ($('#rdrimg').length > 0) {
                //         $('#rdrimg').css('background-image', 'url(' + rdrImg + ')');
                //     }
                // }, 100);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function cctvTab() {
    // 호출 경로 확인
    const stack = new Error().stack;
    const callerLine = stack.split('\n')[2]?.trim() || 'unknown';
    console.info('cctvTab 호출됨:', callerLine);

    $.ajax({
        url: '/include/server/cctvPopup.php',
        dataType: 'html',
        async: true,
        cache: false,
        success: function (data) {
            $('#id_data_cctv').empty().append(data);

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


            // 썸네일에 영상 표출 (스크립트 로드 후)
            setTimeout(function() {
                // console.log('CCTV 탭 로드 완료, 영상 연결 시도...');

                // cs_cctv_canvas 클래스를 가진 요소들을 찾아서 연결
                const canvasElements = document.querySelectorAll('.cs_cctv_canvas');
                // console.log('찾은 canvas 요소 개수:', canvasElements.length);

                // 모달 클릭 핸들러는 한 번만 바인딩 (중복 방지)
                setupCctvModal();

                // 찾은 요소들을 순회하며 연결
                canvasElements.forEach((canvas, i) => {
                    if (canvas) {
                        const cctvKey = canvas.getAttribute('data-key');
                        // console.log('CCTV 연결 시도:', cctvKey, canvas.id, canvas);

                        // 상태 요소들 가져오기
                        const indicator = document.getElementById(`indicator${cctvKey}`);
                        const errorOverlay = document.getElementById(`error${cctvKey}`);

                        // 기존 연결이 있다면 해제
                        if (window.cams && window.cams[cctvKey] && window.cams[cctvKey].readyState === WebSocket.OPEN) {
                            console.log('기존 연결 해제:', cctvKey);
                            window.cams[cctvKey].close();
                        }

                        // WebSocket 연결 (현재 활성화된 cam1만)
                        // if (cctvKey == '1') {
                        // console.log('WebSocket 연결 시도: ws://' + window.cctvHost + ':' + window.cctvPort);

                        // 연결 시도 시 인디케이터를 로딩 상태로 설정
                        if (indicator) {
                            indicator.className = 'cs_status_indicator'; // 기본 상태 (회색)
                            indicator.title = '연결 중...';
                        }

                        // 환경변수 검증
                        if (!window.cctvHost || !window.cctvPort) {
                            console.error('CCTV 환경변수가 설정되지 않았습니다. .env 파일을 확인해주세요.');
                            handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                            return;
                        }

                        let webSocket, port;

                        // hickvision, paros cctv 각각 3개씩 표출되도록 하드 코딩 (추후 수정 필요)
                        if (i <= 2) {
                            port = window.cctvPort;
                        } else {
                            port = Number(window.cctvPort) + 1;
                        }

                        // // webSocket = new WebSocket(`ws://${window.cctvHost}:${port}`);
                        // console.log(window.cctvHost);
                        // console.log(window.cctvPort);
                        // console.log(port);
                        const connStr =  `ws://${window.cctvHost}:${port}`;
                        console.log(connStr);
                        // webSocket = new WebSocket(`ws://${window.cctvHost}:${window.cctvPort}`);
                        webSocket = new WebSocket(connStr);

                        // 썸네일용으로 캔버스 크기 조정
                        // canvas.width = 320;
                        // canvas.height = 240;

                        // 캡처 완료 플래그 (중복 방지)
                        let captured = false;

                        // WebSocket 이벤트 핸들러 설정
                        webSocket.onopen = function() {
                            console.log('WebSocket 연결 성공:', cctvKey);
                        };

                        webSocket.onerror = function(error) {
                            console.error('WebSocket 연결 실패:', cctvKey, error);
                            handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                        };

                        webSocket.onclose = function(event) {
                            if (!captured && event.code !== 1000) {
                                console.error('WebSocket 예상치 못한 연결 종료:', cctvKey, event.code);
                                handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                            }
                        };

                        // jsmpeg로 영상 스트림 표출 (2D 컨텍스트 강제 사용)
                        let jsmpegInstance;
                        try {
                            jsmpegInstance = new jsmpeg(webSocket, {
                                canvas: canvas,
                                autoplay: true,
                                forceCanvas2D: true, // WebGL 대신 2D 컨텍스트 강제 사용
                                ondecodeframe: function() {
                                // 첫 번째 프레임만 캡처 (중복 방지)
                                if (!captured) {
                                    captured = true;
                                    // console.log('첫 프레임 디코딩 완료, 캡처 시작:', cctvKey);

                                    // 성공 상태로 인디케이터 업데이트
                                    if (indicator) {
                                        indicator.className = 'cs_status_indicator success';
                                        indicator.title = '연결 성공';
                                    }

                                    // 다음 렌더링 사이클에서 캡처 (프레임이 완전히 그려진 후)
                                    requestAnimationFrame(() => {
                                        try {
                                            // console.log('CCTV 캡처 시작:', cctvKey);
                                            // console.log('캔버스 크기:', canvas.width, canvas.height);

                                            // 캔버스를 품질 0.8 이미지로 변환
                                            const imgUrl = canvas.toDataURL("image/jpeg", 1);
                                            // console.log('CCTV 썸네일 캡처 완료:', cctvKey, 'bytes:', imgUrl.length);

                                            // WebSocket 연결 해제 (스트리밍 중지)
                                            if (webSocket.readyState === WebSocket.OPEN) {
                                                webSocket.close();
                                            }

                                            // 캔버스를 이미지로 교체
                                            canvas.style.display = 'none';
                                            const imgElement = document.createElement('img');
                                            imgElement.src = imgUrl;
                                            imgElement.style.width = '100%';
                                            imgElement.style.height = '100%';
                                            imgElement.style.objectFit = 'cover';
                                            imgElement.style.cursor = 'pointer'; // 커서 변경
                                            imgElement.className = 'cs_cctv_thumbnail'; // 클래스 추가
                                            imgElement.setAttribute('data-key', cctvKey); // data-key 속성 추가
                                            imgElement.setAttribute('data-name', canvas.getAttribute('data-name')); // data-name 속성 추가
                                            imgElement.setAttribute('data-port', port); // 썸네일별 포트 저장
                                            canvas.parentNode.appendChild(imgElement);

                                        } catch (error) {
                                            console.error('CCTV 썸네일 생성 실패:', cctvKey, error);
                                            handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                                        }
                                    });
                                }
                            }
                        });
                        } catch (error) {
                            console.error('jsmpeg 초기화 실패:', cctvKey, error);
                            handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                            if (webSocket.readyState === WebSocket.OPEN) {
                                webSocket.close();
                            }
                            return;
                        }

                        // 타임아웃 설정 (10초 후에도 프레임이 없으면 실패 처리)
                        setTimeout(() => {
                            if (!captured) {
                                console.error('CCTV 연결 타임아웃:', cctvKey);
                                handleConnectionError(cctvKey, indicator, errorOverlay, canvas);
                                if (webSocket.readyState === WebSocket.OPEN) {
                                    webSocket.close();
                                }
                            }
                        }, 10000);



                            // 전역 변수에 저장
                            // if (!window.cams) window.cams = [];
                            // window.cams[cctvKey] = webSocket;
                        // } else {
                        //     console.log('현재 활성화되지 않은 카메라:', cctvKey);
                        // }
                    }
                });
            }, 1000); // 스크립트 로드 대기 시간 증가
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

// CCTV 연결 오류 처리 함수
function handleConnectionError(cctvKey, indicator, errorOverlay, canvas) {
    console.log('CCTV 연결 실패 처리:', cctvKey);

    // 인디케이터를 실패 상태로 변경
    if (indicator) {
        indicator.className = 'cs_status_indicator error';
        indicator.title = '연결 실패';
    }

    // 캔버스 숨기기
    if (canvas) {
        canvas.style.display = 'none';
    }

    // 오류 오버레이 표시
    if (errorOverlay) {
        errorOverlay.style.display = 'flex';
    }
}

// CCTV 모달창 설정
function setupCctvModal() {
    // 기존 바인딩 제거 후 재바인딩하여 중복 호출 방지
    $(document).off('click', '.cs_cctv_thumbnail');

    // CCTV 썸네일 클릭 시 모달창 열기
    $(document).on('click', '.cs_cctv_thumbnail', function() {
        var cctvKey = $(this).data('key');
        var cctvName = $(this).data('name'); // data-name에서 지역명 가져오기
        var port = $(this).data('port'); // 썸네일에 저장된 포트 사용

        // openCCTVModal 함수 호출
        openCCTVModal(cctvKey, cctvName, port);
    });

    // 모달창 배경 클릭 시 닫기
    $(document).on('click', '.cctv-modal', function(e) {
        if (e.target === this) {
            // 타이머가 있다면 취소
            if (window.cctvModalTimer) {
                clearTimeout(window.cctvModalTimer);
                window.cctvModalTimer = null;
            }
            $('#cctvModal').fadeOut(300);
        }
    });

    // ESC 키로 모달창 닫기
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#cctvModal').is(':visible')) {
            // 타이머가 있다면 취소
            if (window.cctvModalTimer) {
                clearTimeout(window.cctvModalTimer);
                window.cctvModalTimer = null;
            }
            $('#cctvModal').fadeOut(300);
        }
    });
}
