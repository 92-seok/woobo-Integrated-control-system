// JavaScript Document
/*global pType,getFrame,getlog,FormToObject,addTextBar,html2canvas*/

$(document).ready(function (e) {
    /* Checkbox 전체 체크 (공용) */
    $(document).on('click', '#id_allCheck', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $('.cs_isuChk').prop('checked', true);
        } else {
            $('.cs_isuChk').prop('checked', false);
        }
    });

    /* Page 전환 버튼 (공용) */
    $(document).on('click', '#id_page', function () {
        let url = $(this).attr('data-url');
        let idx = $(this).attr('data-idx');
        getFrame(url, pType, idx, 'true');
    });

    $(document).on('click', '#id_criList', function () {
        let num = $(this).attr('data-num');
        let type = $(this).attr('data-type');

        getFrame(`frame/criForm.php?num=${num}&type=${type}`, pType, 3, 'true');
    });

    $(document).on('click', '#id_alerDeleteBtn', function () {
        let type = $(this).attr('data-type');
        let equip = '';
        let count = 0;

        $('.cs_isuChk:checked').each(function () {
            if (count++ == 0) equip = $(this).attr('value');
            else equip = equip + ',' + $(this).attr('value');
        });
        if (confirm('선택하신 경보를 정말 삭제하시겠습니까?') == true) deleteAlert(equip, type);
    });

    $(document).on('click', '#id_alerList', function (e) {
        if (e.target.nodeName == 'TD') {
            let type = $(this).attr('data-type');
            let num = $(this).attr('data-num');
            getFrame(`frame/alertForm.php?num=${num}&type=${type}`, pType, 1, 'true');
        } else {
            let type = $(this).attr('data-type');
            let num = $(this).attr('data-num');
            getFrame(`frame/alertForm.php?num=${num}&type=${type}`, pType, 1, 'true');
        }
    });

    $(document).on('change', '#id_select', function () {
        let type = $(this).find('option:selected').attr('data-type');

        $('.cs_detail').css('display', 'none');
        $('.' + type).css('display', 'block');
    });

    $(document).on('click', '.cs_alertCheck', function () {
        let chk = $(this).is(':checked');
        let chk_id = $(this).attr('id');
        chk_id = chk_id.replace('Check', 'Block');

        if (chk == true) {
            $('#' + chk_id).css('display', 'none');
        } else {
            $('#' + chk_id).css('display', 'block');
        }
    });

    $(document).on('click', '#criCheckAll', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $("input[name='AltCode']").prop('checked', true);
        } else {
            $("input[name='AltCode']").prop('checked', false);
        }
    });

    $(document).on('click', '#id_criSaveBtn', function (e) {
        let chkBool = false;

        let equip = document.querySelector('#id_select').value;
        let rainTime = document.querySelector('#id_rainTime').value;
        let equipType = document.querySelector('#id_select').selectedOptions[0].attributes['data-type'].value;
        let type = e.target.attributes['data-type'].value;

        if (type == 'del') {
            if (confirm('해당 경보를 정말 삭제하시겠습니까?') == false) return false;
            else saveCri(type);
        } else {
            if (equipType == 'rain') {
                chkBool = rainFormChk();
            } else if (equipType == 'snow') {
                chkBool = SnowFormChk();
            } else if (equipType == 'water') {
                chkBool = waterFormChk();
            } else if (equipType == 'dplace') {
                chkBool = dplaceFormChk();
            } else if (equipType == 'news') {
                chkBool = newsFormChk();
            } else if (equipType == 'flood') {
                chkBool = floodFormChk();
            } else if (equipType == 'soil') {
                chkBool = true;
            } else if (equipType == 'tilt') {
                chkBool = true;
            } else {
                alert('계측장비를 선택해 주세요');
                return;
            }

            if (chkBool == true) {
                document.querySelector('#id_type').value = equipType;
                checkAlert(equip, equipType, rainTime, type);
            }
        }
    });

    $(document).on('click', '.cs_use', function () {
        var use = $('.cs_use:checked').val();

        if (use == 'Y') $('.cs_blockBox').hide();
        else $('.cs_blockBox').show();
    });

    $(document).on('click', '#id_groupAlertsaveBtn', function () {
        let type = $(this).attr('data-type');

        if (type == 'del' && confirm('해당 경보를 정말 삭제하시겠습니까?') == false) return false;

        saveAlert(type);
    });

    $(document).on('click', '#id_startBtn', function () {
        var num = $(this).attr('data-num');
        let bck = $(this).attr('data-type');

        if (confirm('경보발령을 하시겠습니까?') == true) {
            saveIssue('update', num, '', bck);
        }
    });

    $(document).on('click', '#id_endBtn', function () {
        var num = $(this).attr('data-num');
        let bck = $(this).attr('data-type');

        if (confirm('경보발령을 종료하시겠습니까?') == true) {
            saveIssue('end', num, '', bck);
        }
    });

    $(document).on('click', '#id_sendBtn', function () {
        var num = $(this).attr('data-num');
        let bck = $(this).attr('data-type');
        var level = $('#id_issueType_' + num).val();

        if (level == null) {
            alert('경보제어 단계를 선택해주세요');
            return;
        }

        if (confirm('경보발령을 하시겠습니까?') == true) {
            saveIssue('insert', num, level, bck);
        }
    });

    $(document).on('click', '#id_infoBtn', function () {
        var num = $(this).attr('data-num');
        getInfo(num);
    });

    $(document).on('click', '#id_alertList', function () {
        var num = $(this).attr('data-num');
        getFrame(`frame/controlDetail.php?num=${num}`, pType, 2, 'true');
    });

    $(document).on('click', '#id_saveMentBtn', function () {
        let chkBool = false;
        chkBool = mentChk();

        if (chkBool == true) {
            saveMent();
        }
    });

    $(document).on('click', '#id_levelBtn', function () {
        let level = $(this).attr('data-num');
        getFrame(`frame/setAlertEachScen.php?level=${level}`, pType, 5, 'true');
    });

    $(document).on('click', '#id_textLevelBtn', function () {
        let level = $(this).attr('data-num');
        getFrame(`frame/setAlertEachTextScen.php?level=${level}`, pType, 5, 'true');
    });

    $(document).on('click', '#id_disBtn', function () {
        console.log('on click', '#id_disBtn');

        let DisCode = $(this).attr('value');
        let type = $(this).attr('data-type');

        if (type == 'insert' || type == 'update') {
            $('.cs_loading').css('display', 'block');

            // const summernote_img = document.querySelector(".note-editable");
            const summernote_img = document.querySelector('#id_summernote_img');
            html2canvas(summernote_img).then(function (canvas) {
                var img = canvas.toDataURL('image/png');
                document.querySelector('#id_imageTag').value = img;
            });

            setTimeout(() => {
                let obj = FormToObject(document.querySelector('#id_form'));
                obj['savetype'] = type;
                obj['DisCode'] = DisCode;
                console.log(obj);
                saveWarnEachScen(obj);
            }, 1000);
        } else {
            let obj = FormToObject(document.querySelector('#id_form'));
            obj['savetype'] = type;
            obj['DisCode'] = DisCode;

            if (type == 'cancel') {
                summernoteReset($('#id_summernote'));
                DelCanSwitch(DisCode, 'del');
                DeleteBtnSwitch(document.querySelectorAll('.cs_btn'), 'block');
            } else {
                if (type == 'delete') {
                    if (confirm('내용을 정말 삭제 하시겠습니까?') == false) {
                        return;
                    }
                } else {
                    if (DeleteBtnSwitch(document.querySelectorAll('.cs_btn'), 'chk')) {
                        alert('수정 중인 시나리오가 있습니다. 취소 후 다시 시도해주세요.');
                        return false;
                    }
                }
                saveWarnEachScen(obj);
            }
        }
    });

    $(document).on('click', '#id_updateBtn', function () {
        let level = $('.cs_btn.select').attr('data-num');
        let mentNum = $(this).attr('value');
        let page = $(this).attr('data-page');

        getFrame(`frame/setAlertEachScen.php?page=${page}&level=${level}&mentNum=${mentNum}`, pType, 5, 'true');
    });

    $(document).on('click', '#id_cancelBtn', function () {
        let level = $('.cs_btn.select').attr('data-num');
        let page = $(this).attr('data-page');

        getFrame(`frame/setAlertEachScen.php?page=${page}&level=${level}`, pType, 5, 'true');
    });
});

function deleteAlert(equip, type) {
    let url = '';
    if (type == 'group') url = 'frame/alertList.php';
    else if (type == 'control') url = 'frame/controllList.php';

    $.ajax({
        url: 'server/alertDelete.php',
        dataType: 'json',
        type: 'post',
        data: { equip: equip, type: type },
        async: false,
        cache: false,
        success: function (data) {
            alert('정상 처리되었습니다.');
            getlog('alert', url, data.action, data.name, '', '', data.content);
            getFrame(url, pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function checkAlert(equip, equipType, rainTime, type) {
    $.ajax({
        url: 'server/chkAlertOverlap.php',
        dataType: 'json',
        type: 'post',
        data: { equip: equip, equipType: equipType, rainTime: rainTime, type: type },
        async: false,
        cache: false,
        success: function (data) {
            if (data.code == '00') {
                if (type == 'ins') {
                    if (confirm('이미등록된 데이터입니다. 현재 내용으로 업데이트 하시겠습니까?') == false) return false;
                }
                saveCri('upd');
            } else if (data.code == '01') {
                saveCri('ins');
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveCri(type) {
    document.querySelector("select[name='RainTime']").disabled = false;
    let formData = FormToObject(document.querySelector('#id_form'));
    formData.type = type;

    $.ajax({
        url: 'server/criSave.php',
        dataType: 'json',
        type: 'post',
        data: JSON.stringify(formData),
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 저장되었습니다.');
            getlog('alert', 'frame/criList.php', data.action, data.name, data.before, data.after);
            getFrame('frame/criList.php', pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveAlert(type) {
    let formData = FormToObject(document.querySelector('#id_form'));
    formData.type = type;
    formData.systemType = sessionStorage.getItem('systemType');

    if (formData.GName == '') {
        alert('경보 이름을 입력해주세요');
        document.querySelector('#id_title').focus();
        return false;
    }

    if (formData.GName.indexOf(';') !== -1) {
        alert("경보 이름에 특수문자 ';'가 들어갈 수 없습니다.");
        return false;
    }

    if (formData.AltCode == undefined) {
        alert('임계치 기준을 선택해주세요');
        return false;
    }

    if (formData.AdmSMS == '') {
        alert('SMS를 받을 담당자 연락처를 입력해 주세요');
        document.querySelector('#id_admSMS').focus();
        return false;
    }

    let smsChecker = formData.AdmSMS.split(',');
    let chkCnt = 0;
    let AdmSMS = '';
    smsChecker.forEach((e) => {
        e = e.replace(/-/g, '');
        e = e.replace(/\s/g, '');

        if (isNaN(e)) chkCnt++;
        if (AdmSMS != '') AdmSMS += ',';
        AdmSMS += e;
    });
    if (chkCnt > 0) {
        alert('담당자 연락처는 핸드폰 번호만 입력이 가능합니다.');
        document.querySelector('#id_admSMS').focus();
        return false;
    }

    smsChecker = AdmSMS.split(',');
    chkCnt = 0;
    for (let i = 0; i < smsChecker.length; i++) {
        for (let j = i + 1; j <= smsChecker.length; j++) {
            if (smsChecker[i] == smsChecker[j]) chkCnt++;
        }
    }
    if (chkCnt > 0) {
        alert('담당자 연락처를 중복되게 적었습니다.');
        document.querySelector('#id_admSMS').focus();
        return false;
    }
    formData.AdmSMS = AdmSMS;

    console.log(formData);

    $.ajax({
        url: 'server/alertSave.php',
        dataType: 'json',
        type: 'post',
        data: JSON.stringify(formData),
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 저장되었습니다.');
            getlog('alert', 'frame/alertList.php', data.action, data.name, data.before, data.after);
            getFrame('frame/alertList.php', pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveIssue(type, num, level, bck) {
    $.ajax({
        url: 'server/saveIssue.php',
        dataType: 'json',
        type: 'post',
        data: { type: type, num: num, level: level },
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 저장되었습니다.');

            let page = 'controlissue';
            if (bck == 'alert') page = 'alertList';

            getlog('alert', `frame/${page}.php`, data.action, data.name, data.before, data.after);
            getFrame(`frame/${page}.php`, pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveMent() {
    let formData = $('#id_form').serializeObject();

    $.ajax({
        url: 'server/iMentSave.php',
        dataType: 'json',
        type: 'post',
        data: JSON.stringify(formData),
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 저장되었습니다.');
            const MentCode = data?.MentCode;
            if (MentCode == '1') {
                getlog('alert', 'frame/issueMent.php', data.action, 'Alert Ment', data.before, data.after);
                getFrame('frame/issueMent.php', pType, -1, 'false');
            } else if (MentCode == '2') {
                getlog('alert', 'frame/issueMent2.php', data.action, 'Alert Ment', data.before, data.after);
                getFrame('frame/issueMent2.php', pType, -1, 'false');
            } else {
                console.error('Unknown MentCode:', MentCode);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function getInfo(num) {
    $.ajax({
        url: 'server/getInfoIssue.php',
        dataType: 'html',
        type: 'post',
        data: { num: num },
        async: false,
        cache: false,
        success: function (data) {
            $('.cs_info').empty().append(data);
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveWarnEachScen(obj) {
    $.ajax({
        url: 'server/saveWarnEachScen.php',
        dataType: 'json',
        type: 'POST',
        data: JSON.stringify(obj),
        async: true,
        cache: false,
        success: function (data) {
            if (data.code == '200') {
                if (obj.savetype == 'select') {
                    summernoteReset($('#id_summernote'), data.html);
                    DelCanSwitch(data.DisCode, 'can');
                    DeleteBtnSwitch(document.querySelectorAll('.cs_btn'), 'none');
                } else {
                    alert('정상적으로 처리되었습니다.');
                    getlog('alert', 'frame/setAlertEachScen.php', data.action, data.name, data.before, data.after);
                    // getFrame(`frame/setAlertEachScen.php?level=${obj.level}`, pType, -1, "false");
                    getFrame(`frame/setAlertEachScen.php?level=${obj.level}`, pType, -1, 'true');
                }
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function summernoteReset(node, html = '') {
    node.summernote('reset');
    if (html != '') node.summernote('pasteHTML', html);
    node.summernote('fontSize', 40);
    node.summernote('backColor', 'black');
    node.summernote('foreColor', '#ffffff');
    node.summernote('lineHeight', 1.3);
}

function DelCanSwitch(code, o) {
    if (o == 'can') {
        $('.updateBtn').attr('value', code);
        $('.updateBtn').css('display', 'block');
        $('.insertBtn').css('display', 'none');

        $(`.deleteBtn_${code}`).text('취소');
        $(`.deleteBtn_${code}`).attr('data-type', `cancel`);
        $(`.deleteBtn_${code}`).attr('class', `cs_btn cancelBtn_${code}`);
    } else if (o == 'del') {
        $('.updateBtn').attr('value', '');
        $('.updateBtn').css('display', 'none');
        $('.insertBtn').css('display', 'block');

        $(`.cancelBtn_${code}`).text('삭제');
        $(`.cancelBtn_${code}`).attr('data-type', `delete`);
        $(`.cancelBtn_${code}`).attr('class', `cs_btn deleteBtn_${code}`);
    }
}

function DeleteBtnSwitch(node, o) {
    let chk = 0;
    node.forEach((e) => {
        let t = e.attributes['data-type'];
        if (t) {
            if (o == 'chk') {
                if (t.value == 'cancel') chk++;
            } else {
                if (t.value == 'delete') e.style.display = o;
            }
        }
    });

    return chk;
}

/***************************************************************** 이하 아래 유효성 검사 *****************************************************************/
//뉴스
function newsFormChk() {
    let useChk1 = $('#id_newsCheck_1').is(':checked');
    let useChk2 = $('#id_newsCheck_2').is(':checked');
    let useChk3 = $('#id_newsCheck_3').is(':checked');
    let useChk4 = $('#id_newsCheck_4').is(':checked');

    let count = 0;
    let news1 = '';
    $('.cs_news_1:checked').each(function () {
        if (count == 0) {
            news1 = $(this).val();
        } else {
            news1 = news1 + ',' + $(this).val();
        }
        count++;
    });

    let news2 = '';
    count = 0;
    $('.cs_news_2:checked').each(function () {
        if (count == 0) {
            news2 = $(this).val();
        } else {
            news2 = news2 + ',' + $(this).val();
        }
        count++;
    });

    let news3 = '';
    count = 0;
    $('.cs_news_3:checked').each(function () {
        if (count == 0) {
            news3 = $(this).val();
        } else {
            news3 = news3 + ',' + $(this).val();
        }
        count++;
    });

    let news4 = '';
    count = 0;
    $('.cs_news_4:checked').each(function () {
        if (count == 0) {
            news4 = $(this).val();
        } else {
            news4 = news4 + ',' + $(this).val();
        }
        count++;
    });

    if (useChk1 == false && useChk2 == false && useChk3 == false && useChk4 == false) {
        alert('임계치 단계를 선택해주세요.');
        return false;
    }

    if (useChk1 == true && news1 == '') {
        alert('1단계 특보 값을 입력해 주세요');
        return false;
    }

    if (useChk2 == true && news2 == '') {
        alert('2단계 특보 값을 입력해 주세요');
        return false;
    }

    if (useChk3 == true && news3 == '') {
        alert('3단계 특보 값을 입력해 주세요');
        return false;
    }

    if (useChk4 == true && news4 == '') {
        alert('4단계 특보 값을 입력해 주세요');
        return false;
    }

    return true;
}

//강우
function rainFormChk() {
    let patten = /[^0-9.]+$/;
    let useChk = Array();
    let rainData = Array();
    let errChk = true;
    let errMsg = '';

    useChk[1] = $('#id_rainCheck_1').is(':checked');
    useChk[2] = $('#id_rainCheck_2').is(':checked');
    useChk[3] = $('#id_rainCheck_3').is(':checked');
    useChk[4] = $('#id_rainCheck_4').is(':checked');

    rainData[1] = $('#id_rainData_1').val();
    rainData[2] = $('#id_rainData_2').val();
    rainData[3] = $('#id_rainData_3').val();
    rainData[4] = $('#id_rainData_4').val();

    for (let i = 1; i <= 4; i++) {
        if (useChk[i] == true && rainData[i] == '') {
            errChk = false;
            errMsg = `${i}단계 강우 값을 입력해 주세요.`;
            break;
        }

        if (useChk[i] == true && patten.test(rainData[i])) {
            errChk = false;
            errMsg = `강우 임계값은 숫자만 입력이 가능합니다.`;
            break;
        }

        if (useChk[i] == true && rainData[i] == '0') {
            errChk = false;
            errMsg = `강우 값에 0이 들어갈 수 없습니다.`;
            break;
        }

        if (i > 1) {
            for (let j = i - 1; j > 0; j--) {
                if (useChk[i] == true && parseInt(rainData[i]) < parseInt(rainData[j])) {
                    errChk = false;
                    errMsg = `${i}단계 강우 값이 ${j}단계 강우 값보다 낮습니다.`;
                    break;
                }
            }
        }
    }

    if (useChk[1] == false && useChk[2] == false && useChk[3] == false && useChk[4] == false) {
        errChk = false;
        errMsg = '임계치 단계를 선택해주세요.';
    }

    if (!errChk) alert(errMsg);

    return errChk;
}

//적설
function SnowFormChk() {
    let patten = /[^0-9.]+$/;
    let useChk = Array();
    let snowData = Array();
    let errChk = true;
    let errMsg = '';

    useChk[1] = $('#id_snowCheck_1').is(':checked');
    useChk[2] = $('#id_snowCheck_2').is(':checked');
    useChk[3] = $('#id_snowCheck_3').is(':checked');
    useChk[4] = $('#id_snowCheck_4').is(':checked');

    snowData[1] = $('#id_snowData_1').val();
    snowData[2] = $('#id_snowData_2').val();
    snowData[3] = $('#id_snowData_3').val();
    snowData[4] = $('#id_snowData_4').val();

    for (let i = 1; i <= 4; i++) {
        if (useChk[i] == true && snowData[i] == '') {
            errChk = false;
            errMsg = `${i}단계 적설 값을 입력해 주세요.`;
            break;
        }

        if (useChk[i] == true && patten.test(snowData[i])) {
            errChk = false;
            errMsg = `적설 임계값은 숫자만 입력이 가능합니다.`;
            break;
        }

        if (useChk[i] == true && snowData[i] == '0') {
            errChk = false;
            errMsg = `적설 값에 0이 들어갈 수 없습니다.`;
            break;
        }

        if (i > 1) {
            for (let j = i - 1; j > 0; j--) {
                if (useChk[i] == true && parseInt(snowData[i]) < parseInt(snowData[j])) {
                    errChk = false;
                    errMsg = `${i}단계 적설 값이 ${j}단계 적설 값보다 낮습니다.`;
                    break;
                }
            }
        }
    }

    if (useChk[1] == false && useChk[2] == false && useChk[3] == false && useChk[4] == false) {
        errChk = false;
        errMsg = '임계치 단계를 선택해주세요.';
    }

    if (!errChk) alert(errMsg);

    return errChk;
}

//수위
function waterFormChk() {
    let patten = /[^0-9.]+$/;
    let useChk = Array();
    let waterData = Array();
    let errChk = true;
    let errMsg = '';

    useChk[1] = $('#id_waterCheck_1').is(':checked');
    useChk[2] = $('#id_waterCheck_2').is(':checked');
    useChk[3] = $('#id_waterCheck_3').is(':checked');
    useChk[4] = $('#id_waterCheck_4').is(':checked');

    waterData[1] = $('#id_waterData_1').val();
    waterData[2] = $('#id_waterData_2').val();
    waterData[3] = $('#id_waterData_3').val();
    waterData[4] = $('#id_waterData_4').val();

    for (let i = 1; i <= 4; i++) {
        if (useChk[i] == true && waterData[i] == '') {
            errChk = false;
            errMsg = `${i}단계 수위 값을 입력해 주세요.`;
            break;
        }

        if (useChk[i] == true && patten.test(waterData[i])) {
            errChk = false;
            errMsg = `수위 임계값은 숫자만 입력이 가능합니다.`;
            break;
        }

        if (useChk[i] == true && waterData[i] == '0') {
            errChk = false;
            errMsg = `수위 값에 0이 들어갈 수 없습니다.`;
            break;
        }

        if (i > 1) {
            for (let j = i - 1; j > 0; j--) {
                if (useChk[i] == true && parseInt(waterData[i]) < parseInt(waterData[j])) {
                    errChk = false;
                    errMsg = `${i}단계 수위 값이 ${j}단계 수위 값보다 낮습니다.`;
                    break;
                }
            }
        }
    }

    if (useChk[1] == false && useChk[2] == false && useChk[3] == false && useChk[4] == false) {
        errChk = false;
        errMsg = '임계치 단계를 선택해주세요.';
    }

    if (!errChk) alert(errMsg);

    return errChk;
}

//변위
function dplaceFormChk() {
    let patten = /[^0-9.]+$/;
    let useChk = Array();
    let dplaceData = Array();
    let dspeedData = Array();
    let errChk = true;
    let errMsg = '';

    useChk[1] = $('#id_dplaceCheck_1').is(':checked');
    useChk[2] = $('#id_dplaceCheck_2').is(':checked');
    useChk[3] = $('#id_dplaceCheck_3').is(':checked');
    useChk[4] = $('#id_dplaceCheck_4').is(':checked');

    dplaceData[1] = $('#id_dplace_1').val();
    dplaceData[2] = $('#id_dplace_2').val();
    dplaceData[3] = $('#id_dplace_3').val();
    dplaceData[4] = $('#id_dplace_4').val();

    dspeedData[1] = $('#id_dpspeed_1').val();
    dspeedData[2] = $('#id_dpspeed_2').val();
    dspeedData[3] = $('#id_dpspeed_3').val();
    dspeedData[4] = $('#id_dpspeed_4').val();

    for (let i = 1; i <= 4; i++) {
        if (useChk[i] == true && (dplaceData[i] == '' || dspeedData[i] == '')) {
            errChk = false;
            errMsg = `${i}단계 변위 값을 입력해 주세요.`;
            break;
        }

        if (useChk[i] == true && (patten.test(dplaceData[i]) || patten.test(dspeedData[i]))) {
            errChk = false;
            errMsg = `변위 임계값은 숫자만 입력이 가능합니다.`;
            break;
        }

        if (useChk[i] == true && (dplaceData[i] == '0' || dspeedData[i] == '0')) {
            errChk = false;
            errMsg = `변위 값에 0이 들어갈 수 없습니다.`;
            break;
        }

        if (useChk[i] == true && i > 1) {
            for (let j = i - 1; j > 0; j--) {
                if (Number(dplaceData[j]) >= Number(dplaceData[i])) {
                    errChk = false;
                    errMsg = `${j}단계 변위 누적값이 ${i}단계 변위 누적값보다 높거나 같습니다.`;
                }

                if (Number(dspeedData[j]) >= Number(dspeedData[i])) {
                    errChk = false;
                    errMsg = `${j}단계 변위 속도값이 ${i}단계 변위 속도값보다 높거나 같습니다.`;

                    break;
                }
            }
        }
    }

    if (useChk[1] == false && useChk[2] == false && useChk[3] == false && useChk[4] == false) {
        errChk = false;
        errMsg = '임계치 단계를 선택해주세요.';
    }

    if (!errChk) alert(errMsg);

    return errChk;
}

//침수
function floodFormChk() {
    let useChk1 = $('#id_floodCheck_1').is(':checked');
    let useChk2 = $('#id_floodCheck_2').is(':checked');
    let useChk3 = $('#id_floodCheck_3').is(':checked');
    let useChk4 = $('#id_floodCheck_4').is(':checked');

    let floodData1 = $('.cs_flood_1:checked').val();
    let floodData2 = $('.cs_flood_2:checked').val();
    let floodData3 = $('.cs_flood_3:checked').val();
    let floodData4 = $('.cs_flood_4:checked').val();

    if (useChk1 == false && useChk2 == false && useChk3 == false && useChk4 == false) {
        alert('임계치 단계를 선택해주세요.');
        return false;
    }

    if (useChk1 == true && floodData1 == undefined) {
        alert('1단계 수위 값을 입력해 주세요');
        return false;
    }

    if (useChk2 == true && floodData2 == '') {
        alert('2단계 수위 값을 입력해 주세요');
        return false;
    }

    if (useChk3 == true && floodData3 == '') {
        alert('3단계 수위 값을 입력해 주세요');
        return false;
    }

    if (useChk4 == true && floodData4 == '') {
        alert('4단계 수위 값을 입력해 주세요');
        return false;
    }

    return true;
}

//멘트
function mentChk() {
    var broad1 = $('#id_broad1').val();
    var broad2 = $('#id_broad2').val();
    var broad3 = $('#id_broad3').val();
    var broad4 = $('#id_broad4').val();
    var SMS1 = $('#id_SMS1').val();
    var SMS2 = $('#id_SMS2').val();
    var SMS3 = $('#id_SMS3').val();
    var SMS4 = $('#id_SMS4').val();
    let specialChar = /[\`\~\!\@\#\$\^\*\(\)\_\+\-\=\[\]\{\}\\\|\'\"\;\:\/\?\<\>]/g;

    if (broad1 == '') {
        alert('경보방송 1단계 멘트를 입력해주세요');
        return false;
    }

    if (broad2 == '') {
        alert('경보방송 2단계 멘트를 입력해주세요');
        return false;
    }

    if (broad3 == '') {
        alert('경보방송 3단계 멘트를 입력해주세요');
        return false;
    }

    if (broad4 == '') {
        alert('경보방송 4단계 멘트를 입력해주세요');
        return false;
    }

    if (SMS1 == '') {
        alert('SMS 1단계 멘트를 입력해주세요');
        return false;
    }

    if (SMS2 == '') {
        alert('SMS 2단계 멘트를 입력해주세요');
        return false;
    }

    if (SMS3 == '') {
        alert('SMS 3단계 멘트를 입력해주세요');
        return false;
    }

    if (SMS4 == '') {
        alert('SMS 4단계 멘트를 입력해주세요');
        return false;
    }

    for (let j = 1; j <= 4; j++) {
        if (specialChar.test(eval(`broad${j}`))) {
            alert(`경보방송 ${j}단계 내용에 특수 문자를 사용할 수 없습니다.`);
            return;
        }

        if (eval(`broad${j}`).length > 500) {
            alert(`경보방송 ${j}단계 내용을 500자 내로 입력해주세요.`);
            $(`#id_broad${j}`).val($(`#id_broad${j}`).val().slice(0, 500));
            return false;
        }
    }

    for (let j = 1; j <= 4; j++) {
        let strCnt = eval(`SMS${j}`).length;

        if (strCnt > 70) {
            alert(`SMS ${j}단계 발송 내용이 70글자를 넘었습니다.`);
            $(`#id_SMS${j}`).val($(`#id_SMS${j}`).val().slice(0, 70));
            return false;
        }
    }

    return true;
}

function subText(textBar) {
    let sT = document.querySelector('#saveText');
    let saveText = sT.value.length > 0 ? sT.value.split('|') : new Array();

    let tN = document.querySelector('#textNum');
    let textNum = tN.value.length > 0 ? tN.value.split('|') : new Array();

    let id = textBar.getAttribute('num');

    for (let i = textNum.length - 1; i >= 0; i--) {
        if (textNum[i] == id) {
            textNum.splice(i, 1);
            saveText.splice(i, 1);
        }
    }

    textBar.parentNode.parentNode.remove();
    sT.value = saveText.join('|');
    tN.value = textNum.join('|');

    displaySample(saveText);

    if (saveText.length < 2) {
        document.querySelector('#displayAddTable').style.display = '';
    }
}

function textChk(textBar) {
    if (window.event.key === 'Enter') {
        addTextBar();
    }

    let addText = textBar.value;
    let replacedAddText = addText.replace(/\#1|\#2|\#3|\#4|\#5|\#6|\#7|/gi, '');
    let byte = byteCheck(replacedAddText);

    $('.byte span').text(byte);

    if (byte > 20) {
        alert('최대 입력가능한 수를 넘겼습니다.');
        document.querySelector('#addText').value = '';
        return;
    }
}

function displaySample(saveText, x = 400, y = 200) {
    let html = '';

    for (let i = 0; i < saveText.length; i++) {
        if (i % 2 == 0) {
            html += `<div class="displayBoard" style="width: ${x}px; height: ${y}px;">`;
        }

        html += `<div class="block">`;

        let splitText = saveText[i].split('#');

        for (let j = 0; j < splitText.length; j++) {
            let color = '';
            let text = splitText[j];
            const first = splitText[j].charAt(0);

            switch (first) {
                case '1':
                    color = 'rgba(255,0,0,1.00)';
                    text = text.substr(1);
                    break;

                case '2':
                    color = 'rgba(0,255,0,1.00)';
                    text = text.substr(1);
                    break;

                case '3':
                    color = 'rgba(255,255,0,1.00)';
                    text = text.substr(1);
                    break;

                case '4':
                    color = 'rgba(0,0,255,1.00)';
                    text = text.substr(1);
                    break;

                case '5':
                    color = 'rgba(255,0,255,1.00)';
                    text = text.substr(1);
                    break;

                case '6':
                    color = 'rgba(0,255,255,1.00)';
                    text = text.substr(1);
                    break;

                case '7':
                    color = 'rgba(255,255,255,1.00)';
                    text = text.substr(1);
                    break;

                default:
                    color = 'rgba(255,0,0,1.00)';
                    break;
            }

            html += `<span id="id_span" class="spanClass" style="color:${color};">${text}</span>`;
        }

        html += '</div>';

        if (i % 2 == 1 || i == saveText.length) {
            html += '</div>';
        }
    }

    $('.displayBox').empty().append(html);
}

function addColor(color) {
    let cursorPos = $('#addText').prop('selectionStart');
    let v = $('#addText').val();
    let textBefore = v.substring(0, cursorPos);
    let textAfter = v.substring(cursorPos, v.length);

    $('#addText').val(textBefore + color + textAfter);
    $('#addText').focus();
}

function byteCheck(el) {
    let codeByte = 0;
    for (let idx = 0; idx < el.length; idx++) {
        let oneChar = escape(el.charAt(idx));
        if (oneChar.length == 1) {
            codeByte++;
        } else if (oneChar.indexOf('%u') != -1) {
            codeByte += 2;
        } else if (oneChar.indexOf('%') != -1) {
            codeByte++;
        }
    }
    return codeByte;
}
