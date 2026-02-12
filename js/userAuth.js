/* 사용자 */
$(document).ready(function (e) {
    $(document).on('click', '#id_addadminBtn', function (e) {
        if (e.target.innerText == '승인') {
            let num = $(this).attr('data-num');
            let left = e.pageX - 100;
            let top = e.pageY + 150;
            window.open(
                `okmessage.php?type=first&Idx=${num}`,
                'Message Box',
                `width=325, height=55, left=${left}, top=${top}, resizable=no, status=no, toolbar=no, scrollbars=no`
            );
        } else {
            let num = $(this).attr('data-num');
            getFrame(`frame/AddUser.php?idx=${num}&sIdx=${sessionIdx}`, pType, 1, 'true');
        }
    });

    $(document).on('click', '#id_useToggle', () => {
        if ($('#id_useToggle').hasClass('on')) {
            $('#id_useToggle').removeClass('on');
            $('#id_useToggle').addClass('off');

            $('#id_toggleBtn').removeClass('on');
            $('#id_toggleBtn').addClass('off');

            $('#ip').attr('disabled', true);
        } else {
            $('#id_useToggle').removeClass('off');
            $('#id_useToggle').addClass('on');

            $('#id_toggleBtn').removeClass('off');
            $('#id_toggleBtn').addClass('on');

            $('#ip').attr('disabled', false);
        }
    });

    $(document).on('click', '#id_deladminBtn', function () {
        if (confirm('선택한 계정을 삭제하시겠습니까?') == true) {
            idx = $(this).attr('data-num');
            delUser(idx);
        }
    });

    $(document).on('click', '.cs_authtype', function () {
        $('.cs_authtype').prop('checked', false);
        $(this).prop('checked', true);
    });

    /* 전화번호 입력 시 숫자만 가능, 하이픈 추가 */
    $(document).on('keyup', '#uphone', function () {
        $(this).val(
            $(this)
                .val()
                .replace(/[^0-9]/g, '')
                .replace(/(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})/, '$1-$2-$3')
                .replace('--', '-')
        );
    });
});

function adduser(idx, id, pw, name, phone, auth, saveType, ip, ipUse) {
    $.ajax({
        url: '/adminRegist/server/executeUser.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: {
            id: id,
            pw: pw,
            auth: auth,
            name: name,
            phone: phone,
            saveType: saveType,
            idx: idx,
            ip: ip,
            ipUse: ipUse,
        },
        success: function (data) {
            if (data.code == '200') {
                alert('등록되었습니다');
                getlog('admin', 'root/manageUser.php', data.action, data.name, data.before, data.after);
                getFrame('frame/manageUser.php', pType, -1, 'false');
            } else {
                alert(data.message);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function delUser(idx) {
    $.ajax({
        url: '/adminRegist/server/executeUser.php',
        dataType: 'json',
        type: 'POST',
        async: true,
        cache: false,
        data: { idx: btoa(encodeURI(idx)), saveType: btoa(encodeURI('delete')), num: '' },
        success: function (data) {
            if (data.code == '200') {
                alert('삭제되었습니다.');
                getlog('admin', 'root/manageUser.php', data.action, data.name, data.before, data.after);
                getFrame('frame/manageUser.php', pType, -1, 'false');
            } else {
                alert(data.message);
            }
        },
        error: function (request, status, error) {
            console.log('code:' + request.status + '\n' + 'message:' + request.responseText + '\n' + 'error:' + error);
        },
    });
}

function regBtn() {
    var idx = $('#idx').val();
    var id = $('#id').val();
    var pwd = $('#pwd').val();
    var pwdc = $('#pwdc').val();
    var uname = $('#uname').val();
    var uphone = $('#uphone').val();
    var auth = $('#id_auth').val();
    var saveType = $('#saveType').val();
    var ip = $('#ip').val();
    var ipUse = $('#id_useToggle').hasClass('on') ? 'Y' : 'N';
    let reservedId = ['admin', 'administrator', 'root'];

    var pattern = /[\!\@\#\$\%\^\&\*\(\)\-\_\=\+\\\|\'\"\;\:\/\?\.\>\,\<\`\~\]\[\}\{]/g;
    let pwpatten =
        /^.*(?=.{8,})(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[\/\?\.\>\<\,\'\"\:\;\\\|\]\}\[\{\=\+\-\_\)\(\*\&\^\%\$\#\@\!\`\~]).*$/;
    var ippatten =
        /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|\*)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|\*)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|\*)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|\*)$/;
    var enNumCheck = /[^a-z|^0-9]/gi;
    var strId = id.replace(/\s/g, '');
    var strpwd = pwd.replace(/\s/g, '');
    var strpwdc = pwdc.replace(/\s/g, '');
    var strname = uname.replace(/\s/g, '');
    let phonechk = PhonePattenChk(uphone);

    if (strId == '') {
        alert('아이디를 입력하세요');
        return;
    }

    if (strId.length < 5 || strId.length > 20) {
        alert('아이디는 최소 5자에서 최대 20자 이내로 입력해주세요.');
        return;
    }

    if (reservedId.indexOf(strId.toLowerCase()) != -1) {
        alert('아이디 생성 규칙에 적합하지 않습니다.');
        return;
    }

    if (pattern.test(strId)) {
        alert('아이디에 특수문자를 입력 하실 수 없습니다.');
        return;
    }

    // if(enNumCheck.test(strId))
    // {
    //     alert("아이디는 영문 숫자 조합만 사용 가능합니다.");
    //     return;
    // }

    if (idx == '-1') {
        if (strpwd == '') {
            alert('비밀번호를 입력하세요');
            return;
        }

        if (strpwd.length < 10) {
            alert('비밀번호는 최소 10자 이상 입력해주세요.');
            return;
        }

        // if (!pwpatten.test(strpwd)) {
        //     alert('비밀번호는 영문, 숫자, 특수문자를 조합하여 입력해주세요.');
        //     return;
        // }

        if (strpwdc == '') {
            alert('비밀번호 확인을 입력하세요');
            return;
        }

        if (strpwd != strpwdc) {
            alert('비밀번호가 일치하지 않습니다.');
            return;
        }
    } else {
        if (strpwd != '') {
            if (strpwd.length < 12) {
                alert('비밀번호는 최소 12자 이상 입력해주세요.');
                return;
            }

            // if (!pwpatten.test(strpwd)) {
            //     alert('비밀번호는 영문, 숫자, 특수문자를 조합하여 입력해주세요.');
            //     return;
            // }

            if (strpwdc == '') {
                alert('비밀번호 확인을 입력하세요');
                return;
            }

            if (strpwd != strpwdc) {
                alert('비밀번호가 일치하지 않습니다.');
                return;
            }
        }
    }

    if (strname.length > 10) {
        alert('별칭은 10자 이하로 입력해주세요.');
        return;
    }
    if (!phonechk) {
        alert('휴대폰 번호의 번호 체계에 맞지 않습니다.');
        return;
    }

    if (auth == '') {
        alert('관리 등급을 선택하세요');
        return;
    }

    if (ipUse == 'Y') {
        if (ip == '') {
            alert('ip를 입력해주세요.');
            return;
        }

        if (!ippatten.test(ip)) {
            alert('ip가 체계에 맞지 않습니다.');
            return;
        } else {
            let ipArea = ip.split('.');
            let mask = '';

            for (let i = 0; i < 4; i++) {
                if (ipArea[i] == '*') mask += '1';
                else mask += '0';
            }

            let maskChk = parseInt(mask, 2);

            if (maskChk != 15 && maskChk != 7 && maskChk != 3 && maskChk != 1) {
                alert("'*'을 사용한 대역대가 체계에 맞지 않습니다.");
                return;
            } else if (maskChk == 15) ipUse = 'N';
        }
    }

    idx = btoa(encodeURI(idx));
    strId = btoa(encodeURI(strId));
    strpwd = btoa(encodeURI(strpwd));
    uname = btoa(encodeURI(uname));
    phonechk = btoa(encodeURI(phonechk));
    auth = btoa(encodeURI(auth));
    saveType = btoa(encodeURI(saveType));
    ip = btoa(encodeURI(ip));
    ipUse = btoa(encodeURI(ipUse));

    adduser(idx, strId, strpwd, uname, phonechk, auth, saveType, ip, ipUse);
}
