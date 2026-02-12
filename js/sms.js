// JavaScript Document

$(document).ready(function (e) {
    $(document).on('click', '#id_allCheck', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $('.cs_smsChk').prop('checked', true);
        } else {
            $('.cs_smsChk').prop('checked', false);
        }
    });

    /* Page (공용) */
    $(document).on('click', '#id_page', function () {
        let url = $(this).attr('data-url');
        let idx = $(this).attr('data-idx');
        //alert(url+"\n"+idx);
        getFrame(url, pType, idx, 'true');
    });

    $(document).on('keyup', '#id_content', function () {
        let strCnt = $('#id_content').val().length;

        if (strCnt > 70) {
            alert('문자 발송 내용이 70글자를 넘었습니다.');
            $('#id_content').val($('#id_content').val().slice(0, 70));
            return false;
        }
        $('#id_subByte').text(strCnt);
    });

    $(document).on('click', '#id_sendBtn', function () {
        let code = '';
        let content = $('#id_content').val();
        let subByte = 0;
        let smstitle = $('#id_smstitle').val();
        $('.cs_smsChk:checked').each(function () {
            code = `${code},${$(this).val()}`;
        });

        if (smstitle == '') {
            alert('제목을 입력하세요');
            return;
        }

        if (content == '') {
            alert('내용을 입력하세요');
            return;
        }

        if (code == '') {
            alert('수신자가 선택되지 않았습니다.');
            return;
        }

        if (content.length > 70) {
            alert('문자 발송 내용이 70글자를 넘었습니다.');
            $('#id_content').val($('#id_content').val().slice(0, 70));
            return false;
        }

        code = code.substring(1, code.length);
        sendsms(code, smstitle, content);
    });

    $(document).on('click', '#id_smsList', function () {
        let num = $(this).attr('data-num');
        let type = $(this).attr('data-type');

        if (type == 'sms') getFrame(`frame/sendDetail.php?num=${num}`, pType, 1, 'true');
        else if (type == 'user') getFrame(`frame/addrDetail.php?num=${num}`, pType, 2, 'true');
    });

    $(document).on('click', '#id_retry', function () {
        let code = $(this).attr('data-code');
        let num = $(this).attr('data-num');

        if (confirm('선택하신 내용을 재전송 하시겠습니까?') == true) reTrySMS(code, num);
    });

    $(document).on('click', '#id_delBtn', function () {
        var equip = '';
        var count = 0;

        $('.cs_smsChk:checked').each(function () {
            if (count++ == 0) equip = $(this).attr('value');
            else equip = equip + ',' + $(this).attr('value');
        });

        if (equip == '') {
            alert('삭제 할 문자내역을 선택해주세요.');
            return false;
        }

        if (confirm('선택하신 내역을 정말 삭제하시겠습니까?') == true) addrAdd('listDelete', equip, '', '', '', '');
    });

    $(document).on('click', '#id_addrBtn', function () {
        let num = $(this).attr('data-num');
        let type = $(this).attr('data-type');
        let departments = $('input[name=departments]').val();
        let name = $('input[name=name]').val();
        let position = $('input[name=position]').val();
        let phone = $('input[name=phone]').val();
        let phoneChk = PhonePattenChk(phone);

        if (name == '') {
            alert('별칭을 입력해주세요.');
            return false;
        }

        if (!phoneChk) {
            alert('휴대폰 번호의 번호 체계에 맞지 않습니다.');
            return false;
        }

        addrAdd(type, num, departments, name, position, phoneChk);
    });

    $(document).on('keydown', "input[name='search']", function (e) {
        if (e.keyCode === 13) {
            e.preventDefault();
            if ($("input[name='search']").val().indexOf(';') !== -1) {
                alert("';' 특수문자는 사용할 수 없습니다.");
                return;
            }
            let param = $('#id_form').serialize();

            getFrame(`frame/addrControl.php?${param}`, pType, -1, 'false');
        }
    });

    $(document).on('click', '#id_search', function (event) {
        event.preventDefault();

        if ($("input[name='search']").val().indexOf(';') !== -1) {
            alert("';' 특수문자는 사용할 수 없습니다.");
            return;
        }
        let param = $('#id_form').serialize();

        getFrame(`frame/addrControl.php?${param}`, pType, -1, 'false');
    });
});

function sendsms(code, title, content) {
    $.ajax({
        url: 'server/sendsms.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: { code: code, title: title, content: content },
        success: function (data) {
            if (data.code == '00') {
                alert(data.message);
                getlog('SMS', 'frame/sendMsg.php', 'SMS Send', code, '', '', content);
                getFrame('frame/sendMsg.php', pType, -1, 'false');
            } else {
                alert(data.message);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function addrAdd(type, num, departments, name, position, phone) {
    $.ajax({
        url: 'server/addrAdd.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: { type: type, num: num, departments: departments, name: name, position: position, phone: phone },
        success: function (data) {
            if (data.code == '00') getlog('SMS', 'frame/addrControl.php', 'Address Update', data.name, data.before, data.after);
            else if (data.code == '10') getlog('SMS', 'frame/addrControl.php', 'Address Insert', data.name, data.before, data.after);
            else if (data.code == '20') getlog('SMS', 'frame/addrControl.php', 'Address Delete', data.name, data.before);
            else if (data.code == '30') getlog('SMS', 'frame/sendList.php', 'Message Delete', data.name, data.before);

            if (data.code == '00' || data.code == '10' || data.code == '20') {
                alert('정상적으로 처리되었습니다.');
                getFrame('frame/addrControl.php', pType, -1, 'false');
            } else if (data.code == '30') {
                alert('정상적으로 처리되었습니다.');
                getFrame('frame/sendList.php', pType, -1, 'false');
            } else if (data.code == '01') {
                alert(data.content);
                return false;
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function reTrySMS(code, num) {
    $.ajax({
        url: 'server/reTryMsg.php',
        type: 'POST',
        async: true,
        cache: false,
        data: { code: code },
        success: function (data) {
            data = JSON.parse(data);
            alert('정상적으로 처리되었습니다.');
            getlog('sms', 'frame/sendDetail.php', 'SMS Send Again', data['equip'], '', '', data['content']);
            getFrame('frame/sendDetail.php?num=' + num, pType, -1, 'false');
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

// function byteCheck(content)
// {
// 	var codeByte = 0;
// 	for (var idx = 0; idx < content.length; idx++)
// 	{
// 		var oneChar = escape(content.charAt(idx));
// 		if ( oneChar.length == 1 ) codeByte ++;
// 		else if (oneChar.indexOf("%u") != -1) codeByte += 2;
// 		else if (oneChar.indexOf("%") != -1) codeByte ++;
// 	}
// 	return codeByte;
// }
