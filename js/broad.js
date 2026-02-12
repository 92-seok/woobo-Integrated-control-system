// JavaScript Document

$(document).ready(function (e) {
    /* Checkbox 전체 체크 (공용) */
    $(document).on('click', '#id_allCheck', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $('.cs_brdChk').prop('checked', true);
        } else {
            $('.cs_brdChk').prop('checked', false);
        }
    });

    /* 날짜, 장비 검색! (공용) */
    $(document).on('click', '#id_search', function (event) {
        event.preventDefault();
        let url = $('#id_form').serialize();
        let type = $('#id_search').attr('data-num');

        if (type == 'date_search') {
            let beforeDate = new Date(
                document.querySelector('#id_form select[name=year1]').value,
                document.querySelector('#id_form select[name=month1]').value - 1,
                document.querySelector('#id_form select[name=day1]').value
            );
            let afterDate = new Date(
                document.querySelector('#id_form select[name=year2]').value,
                document.querySelector('#id_form select[name=month2]').value - 1,
                document.querySelector('#id_form select[name=day2]').value
            );
            let diffDate = afterDate.getTime() - beforeDate.getTime();

            if (diffDate < 0) {
                beforeDate = new Date(afterDate - 432000000);
                url =
                    'url=broadReport.php&year1=' +
                    beforeDate.getFullYear() +
                    '&month1=' +
                    (beforeDate.getMonth() + 1) +
                    '&day1=' +
                    beforeDate.getDate() +
                    url.substr(url.search('year2') - 1, url.length - url.search('year2') + 1);
            } else {
                if (diffDate > 3024000000) {
                    beforeDate = new Date(afterDate - 3024000000);
                    url =
                        'url=broadReport.php&year1=' +
                        beforeDate.getFullYear() +
                        '&month1=' +
                        (beforeDate.getMonth() + 1) +
                        '&day1=' +
                        beforeDate.getDate() +
                        url.substr(url.search('year2') - 1, url.length - url.search('year2') + 1);
                }
            }
        }

        url = url.substr(4, url.length);
        url = 'frame/' + url.replace('&', '?');

        getFrame(url, pType, -1, 'false');
    });

    /* Page (공용) */
    $(document).on('click', '#id_page', function () {
        let url = $(this).attr('data-url');
        let idx = $(this).attr('data-idx');
        getFrame(url, pType, idx, 'true');
    });

    ////////////////* Broad Form 영역 *//////////////////
    /* Group 활성화 */
    $(document).on('click', '#id_groupChk', function () {
        var num = $(this).attr('value');

        $('.cs_groupChk').removeClass('active');
        $(this).addClass('active');

        setGroupPanel('select_broadForm', '', num);
    });

    /* 예약 시간 활성화/비활성화 */
    $(document).on('click', '#id_tType', function () {
        var disData = $('#id_tType:checked').val();

        if (disData == 'reserve') {
            $('#id_sDate').prop('disabled', false);
            $('#id_sTime').prop('disabled', false);
            $('#id_sMin').prop('disabled', false);
        } else if (disData == 'general') {
            $('#id_sDate').attr('disabled', 'disabled');
            $('#id_sTime').prop('disabled', 'disabled');
            $('#id_sMin').prop('disabled', 'disabled');
        }
    });

    /* 방송 종류 변경시 Ment 비동기식 변경 */
    $(document).on('change', '#id_bType', function () {
        var type = $('#id_bType').val();
        document.querySelector('#id_content').value = '';

        if (type == 'tts') {
            $('#id_content').prop('readonly', false);
        } else if (type == 'alert') {
            $('#id_content').prop('readonly', true);
        }

        getBrdMent(type, 'type');
    });

    /* 멘트 종류 변경시 Ment 비동기식 변경 */
    $(document).on('change', '#id_bMent', function () {
        var type = $('#id_bType').val();
        var ment = $('#id_bMent').val();

        document.querySelector('#id_content').value = '';

        if (type == 'tts' && (ment == '' || ment == 'type')) {
            $('#id_content').focus();
            return false;
        }

        getBrdMent(type, ment);
    });

    $(document).on('keyup', '#id_content', function () {
        let str = $('#id_content').val();

        if (str.length > 500) {
            alert('문자 내용은 500자를 넘길 수 없습니다.');
            $('#id_content').val($('#id_content').val().slice(0, 500));
            return false;
        }
    });

    /* 방송하기 Start!!! */
    $(document).on('click', '#id_addBtn', function () {
        var equip = '';
        // var dscode = "";
        var count = 0;
        let specialChar = /[\`\~\!\@\^\*\(\)\_\+\-\=\[\]\{\}\\\|\'\"\;\:\/\?\<\>]/g;

        $('.cs_brdChk:checked').each(function () {
            if (count++ == 0) {
                equip = $(this).attr('value');
            } else {
                equip = equip + ',' + $(this).attr('value');
            }
        });

        var title = $('#id_title').val();
        var tType = $('#id_tType:checked').val();
        var sDate = $('#id_sDate').val();
        var sTime = $('#id_sTime').val();
        var sMin = $('#id_sMin').val();
        var repeat = $('#id_repeat').val();
        var type = $('#id_bType').val();
        var ment = $('#id_bMent').val();
        var content = $('#id_content').val();

        /* Error 체크 */
        if (equip == '') {
            alert('방송을 등록할 장비를 선택해주세요');
            return;
        }

        if (title == '') {
            alert('제목을 입력해주세요');
            return;
        }

        if (tType == 'reserve') {
            if (sDate == '') {
                alert('예약일자를 입력해 주세요');
                return;
            }

            try {
                let dateChk =
                    /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;
                let dateRegex = sDate.split('-');
                let y = parseInt(dateRegex[0], 10),
                    m = parseInt(dateRegex[1], 10),
                    d = parseInt(dateRegex[2], 10);

                if (!dateChk.test(d + '-' + m + '-' + y)) {
                    alert('예약일자를 다시 확인해 주세요');
                    return;
                }
            } catch (err) {
                alert('예약일자를 다시 확인해 주세요');
                return;
            }
        }

        if (content == '') {
            alert('방송할 내용을 입력해주세요');
            return;
        }
        if (specialChar.test(content)) {
            alert('방송 내용에 특수 문자를 사용할 수 없습니다.');
            return;
        }
        if (content.length > 500) {
            alert('방송 내용은 500자를 넘길 수 없습니다.');
            $('#id_content').val($('#id_content').val().slice(0, 500));
            return false;
        }

        content = content.replaceAll(/\n/g, ' ');

        sendBroad(equip, title, tType, sDate, sTime, sMin, repeat, type, ment, content);
    });

    ////////////////* Broad Result 영역 *//////////////////
    /* 게시판 디테일 진입 */
    $(document).on('click', '#id_brdList', function () {
        var num = $(this).attr('value');
        var page = $(this).attr('data-num');

        getFrame('frame/broadResultDetail.php?num=' + num + '&page=' + page, pType, 1, 'true');
    });

    /* 게시물 삭제 버튼 */
    $(document).on('click', '#id_delBtn', function () {
        var num = '';
        var count = 0;
        var page = $(this).attr('data-num');
        var data = $(this).attr('data');

        if (data == 'result') {
            $('.cs_brdChk:checked').each(function (key, val) {
                num += "'" + val.value + "',";
            });
            num = num.slice(0, -1);
            if (num == '') {
                alert('삭제할 방송내역을 선택해주세요');
                return;
            }
        } else {
            num = data;
        }

        if (confirm('정말 삭제하시겠습니까?')) deleteBroad(num, page);
        else return;
    });

    $(document).on('click', '#id_replay', function () {
        var num = $(this).attr('data-num');
        getFrame('frame/broadForm.php?dType=replay&num=' + num, pType, 1, 'true');
    });

    $(document).on('click', '#id_retry', function () {
        let parm = $(this).attr('data-parm');
        let num = $(this).attr('data-num');
        let page = $(this).attr('data-page');

        if (confirm('방송을 재전송하시겠습니까?') == true) reTryBroad(num, parm, page);
    });

    ////////////////* Ment 영역 *//////////////////
    /* 멘트 추가 버튼 */
    $(document).on('click', '#id_addmntBtn', function () {
        getFrame('frame/mentForm.php?type=insert', pType, 3, 'true');
    });

    /* 멘트 게시물 진입 */
    $(document).on('click', '#id_mntList', function () {
        var num = $(this).attr('value');

        getFrame('frame/mentForm.php?num=' + num + '&type=update', pType, 3, 'true');
    });

    /* 멘트 추가/수정/삭제 */
    $(document).on('click', '#id_mntBtn', function () {
        let type = $(this).attr('data-type');

        if (type == 'mdelete') {
            let num = '';
            let count = 0;
            $('.cs_brdChk:checked').each(function () {
                if (count++ == 0) num = $(this).attr('value');
                else num = num + ',' + $(this).attr('value');
            });

            mentSave(type, num, '', '', '');
        } else {
            let num = $('#id_num').val();
            let title = $('#id_title').val();
            let content = $('#id_content').val();
            let specialChar = /[\`\~\!\@\#\$\^\*\(\)\_\+\-\=\[\]\{\}\\\|\'\"\;\:\/\?\<\>]/g;

            if (content == '') {
                alert('방송에 사용할 멘트를 입력해주세요');
                return;
            }
            if (specialChar.test(content)) {
                alert('방송 내용에 특수 문자를 사용할 수 없습니다.');
                return;
            }
            if (content.length > 500) {
                alert('방송 내용은 500자를 넘길 수 없습니다.');
                $('#id_content').val($('#id_content').val().slice(0, 500));
                return false;
            }

            let before = document.querySelector('#id_beforeContent').value;
            mentSave(type, num, title, content, before);
        }
    });

    ////////////////* Group 영역 *//////////////////
    /* 그룹별 장비관리 띄우기 */
    $(document).on('click', '.cs_trList', function () {
        let group_code = $(this).attr('value');
        let type = 'select';

        $('tbody').children('tr').removeClass('active');
        $(this).parents('tr').addClass('active');

        //select문으로 진입

        setGroupPanel(type, 'blank', group_code);
    });

    /* 그룹 이름 변경 위해 텍스트박스 띄우기 */
    $(document).on('dblclick', '.cs_trList', function () {
        let text = $(this).text();
        $(this).empty();
        $(this).css('display', 'flex').css('justify-content', 'space-between');
        $(this).append("<input type='text' maxlength='30' value='" + text + "' style='width:75%; height:25px; margin:auto;'>");
        $(this).append("<div id='id_save_group_btn'>저장</div>");

        $(this).next().text('취소');
        $(this).next().attr('id', 'id_cancel_group_btn');
    });

    /* 그룹 이름 변경 */
    $(document).on('click', '#id_save_group_btn', function (e) {
        let beforeGroupName = e.target.parentNode.parentNode.attributes['beforename'].value;
        let group_code = $(this).parents('td').attr('value');
        let group_value = $(this).prev().val();
        let type = 'update';

        //update문으로 진입
        setGroupPanel(type, group_value, group_code, beforeGroupName);
    });

    $(document).on('click', '#id_cancel_group_btn', function () {
        getFrame('frame/group.php', pType, -1, 'false');
    });

    /* 그룹 삭제 */
    $(document).on('click', '#id_delete_group_btn', function (e) {
        let beforeName = e.target.parentElement.attributes['beforeName'].value;
        let group_code = $(this).attr('value');
        let type = 'delete';

        //delete문으로 진입
        setGroupPanel(type, '', group_code, beforeName);
    });

    /* 그룹 추가 */
    $(document).on('click', '#id_insert_group_btn', function () {
        let group_value = $(this).prev().children(':first').val();
        let type = 'insert';

        //insert문으로 진입
        setGroupPanel(type, group_value, 'blank', '');
    });

    /* 그룹에 장비 추가! */
    $(document).on('click', '#id_group_save', function () {
        let groupName = document.querySelector('tr.active').attributes['beforename'].value;
        let beforeEquip = document.querySelector('#id_before').attributes['value'].value;
        let group_value = '';
        let type = 'eupdate';
        let group_code = $('tr.active').attr('value');
        $('.cs_brdChk:checked').each(function () {
            group_value = group_value + ',' + $(this).attr('value');
        });

        //insert문으로 진입 (BEquip)
        console.log(beforeEquip);
        setGroupPanel(type, group_value.substring(1, group_value.length), group_code, beforeEquip, group_value.substring(1, group_value.length), groupName);
    });

    /************************** CID 영역 **************************/
    $(document).on('click', '#id_add_cid_Btn', function () {
        getFrame('frame/broadForm.php?dType=cidsave', pType, 5, 'true');
    });

    /* CID 추가 버튼! */
    $(document).on('click', '#id_ins_cid_Btn', function () {
        let ucid = document.getElementById('id_cid').value;
        let cid = PhonePattenChk(ucid);
        let equip = '';

        $('.cs_brdChk:checked').each(function () {
            equip = equip + ',' + $(this).attr('value');
        });

        if (!equip) {
            alert('CID를 등록할 장비를 선택하세요');
            return;
        }

        if (!cid) {
            alert('휴대폰 번호의 번호 체계에 맞지 않습니다.');
            return;
        }

        equip = equip.substring(1, equip.length);
        saveCID(equip, cid, '', 'insert');
    });

    /* CID 삭제 버튼! */
    $(document).on('click', '#id_del_cid_Btn', function () {
        let num = '';

        $('.cs_brdChk:checked').each(function () {
            num = num + "or CidCode = '" + $(this).attr('value') + "' ";
        });

        num = num.substring(3, num.length);
        saveCID('', '', num, 'delete');
    });
});

function sendBroad(equip, title, tType, sDate, sTime, sMin, repeat, type, ment, content) {
    // return alert(equip);
    $.ajax({
        url: 'server/sendBroadcast.php',
        dataType: 'json',
        type: 'post',
        data: { equip: equip, title: title, tType: tType, sDate: sDate, sTime: sTime, sMin: sMin, repeat: repeat, type: type, ment: ment, content: content },
        async: false,
        cache: false,
        success: function (data) {
            if (data.code == '00') {
                alert('정상적으로 방송이 등록되었습니다.');
                getlog('broad', 'frame/broadForm.php', 'BroadCast Send', equip, '', '', content);
                getFrame('frame/broadForm.php', pType, -1, 'false');
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function getEquipList(num) {
    $.ajax({
        url: 'server/getEquipList.php',
        dataType: 'html',
        type: 'post',
        data: { num: num },
        async: false,
        cache: false,
        success: function (data) {
            $('#id_equipList').empty().append(data);
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function getBrdMent(type, ment) {
    let title = document.querySelector('#id_bMent');
    let content = document.querySelector('#id_content');
    $.ajax({
        url: 'server/getBrdMent.php',
        type: 'post',
        data: { type: type, ment: ment },
        async: false,
        cache: false,
        success: function (data) {
            let result = JSON.parse(data);
            if (ment == 'type') {
                $('#id_bMent').empty();
                let element = document.createElement('option');

                if (type == 'tts') {
                    element.setAttribute('value', '');
                    element.setAttribute('selected', '');
                    element.innerText = '직접 입력';
                } else {
                    element.setAttribute('value', '');
                    element.setAttribute('selected', '');
                    element.setAttribute('disabled', '');
                    element.innerText = '예경보 내용 선택';
                }

                title.appendChild(element);

                for (let val in result) {
                    element = document.createElement('option');
                    element.setAttribute('value', val);
                    element.innerText = `${result[val]}`;

                    title.appendChild(element);
                }
            } else {
                $('#id_content').empty();

                for (let val in result) {
                    content.value = result[val];
                }
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function deleteBroad(num, page) {
    $.ajax({
        url: 'server/broadDelete.php',
        dataType: 'json',
        type: 'post',
        data: { num: num },
        async: false,
        cache: false,
        success: function (data) {
            if (data.code == '00') {
                alert('정상적으로 처리되었습니다.');
                getlog('broad', 'frame/broadResult.php', 'Broad List Delete');
                getFrame(`frame/broadResult.php?page=${page}`, pType, -1, 'false');
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function mentSave(type, num, title, content, before) {
    $.ajax({
        url: 'server/mentSave.php',
        type: 'post',
        data: { type: type, num: num, title: title, content: content },
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 처리되었습니다.');

            if (type == 'mdelete') getlog(pType, 'frame/mentList.php', 'Ment Delete', num);
            else if (type == 'delete') getlog(pType, 'frame/mentList.php', 'Ment Delete', num);
            else getlog('broad', 'frame/mentList.php', 'Ment Update', before, '', content, '');
            getFrame('frame/mentList.php', pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function setGroupPanel(type, group_value, group_code, before, after = '', content = '') {
    if (type == 'select') {
        $('#id_equip').css('line-height', '0px');
    } else if (type == 'insert') {
        if (group_value == '') {
            alert('그룹명을 입력해주세요.');
            return;
        }
    }

    $.ajax({
        url: 'server/setBrdGroup.php',
        dataType: 'html',
        type: 'post',
        data: { group_value: group_value, type: type, group_code: group_code },
        async: false,
        cache: false,
        success: function (data) {
            if (type == 'select' || type == 'select_broadForm') {
                $('#id_equip').empty().append(data);
            } else {
                alert('정상적으로 처리되었습니다.');
                if (type == 'insert') getlog('broad', 'frame/group.php', 'Broad Group Add', '', '', group_value);
                else if (type == 'update') getlog('broad', 'frame/group.php', 'Broad Group Name Update', before, '', group_value);
                else if (type == 'delete') getlog('broad', 'frame/group.php', 'Broad Group Delete', before);
                else if (type == 'eupdate') getlog('broad', 'frame/group.php', 'Broad Group Update', content, before, after);
                getFrame('frame/group.php', pType, -1, 'false');
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function saveCID(equip, cid, num, type) {
    $.ajax({
        url: 'server/cidSave.php',
        dataType: 'json',
        type: 'post',
        data: { equip: equip, cid: cid, num: num, type: type },
        async: false,
        cache: false,
        success: function (data) {
            if (data.code == '00') {
                alert('정상적으로 처리되었습니다.');
                if (type == 'insert') getlog('broad', 'frame/cidList.php', 'CID Update', equip, data.before, cid);
                else getlog('broad', 'frame/cidList.php', 'CID Delete', equip, data.before);
                getFrame('frame/cidList.php', pType, -1, 'false');
            } else {
                console.log(data);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function reTryBroad(num, parm, page) {
    $.ajax({
        url: 'server/reTryBroad.php',
        type: 'post',
        data: { num: num, parm: parm },
        async: false,
        cache: false,
        success: function (data) {
            alert('정상적으로 처리되었습니다.');
            getlog('broad', 'frame/broadResultDetail.php', 'Broad Send Again', num, '', '', data.content);
            getFrame('frame/broadResultDetail.php?num=' + parm + '&page=' + page, pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}
