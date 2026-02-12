<?php
session_start(); ?>
<!doctype html>
<html>

<head>
    <title><?= $_SESSION['title'] ?></title>

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <link rel="stylesheet" type="text/css" href="/css/login.css" />
    <link rel="stylesheet" type="text/css" href="/font/nanumSquare/nanumSquare.css" />
    <link rel="shortcut icon" href="/image/favicon.ico">

    <style>
    .cs_loginLeft,
    .cs_btn {
        background-color: <?= $_SESSION['color'] ?>;
    }

    .cs_btn:hover {
        background-color: <?= $_SESSION['backgroundColorHover'] ?>;
    }

    .cs_datatable th {
        background-color: <?= $_SESSION['color'] ?>;
    }

    input {
        width: 95%;
        height: 60%;
    }
    </style>
</head>

<body>
    <div class="cs_login_frame" style="padding:0px;">
        <div class="cs_container">
            <div class="cs_loginLeft">
                <div class="cs_SystemTitle">
                    <div class="cs_icon" style="background-image:url(../image/<?= $_SESSION['system'] ?>/parkIcon.png)">
                    </div>
                    <div class="cs_Stitle"><?= $_SESSION['title'] ?></div>
                    <div class="cs_Etitle"><?= $_SESSION['enTitle'] ?></div>
                </div>
            </div>
            <div class="cs_center">
                <div class="cs_title">W E L C O M E</div>
                <form id="id_form">
                    <table class="cs_datatable" border="0" cellpadding="0" cellspacing="0" rules="all">
                        <tr>
                            <th width="20%">아이디</th>
                            <td><input type="text" name="id" id="id" maxlength="20"></td>
                        </tr>
                        <tr>
                            <th>비밀번호</th>
                            <td><input type="password" autocomplete="off" maxlength="20" name="pwd" id="pwd"></td>
                        </tr>
                        <tr>
                            <th>비밀번호 확인</th>
                            <td><input type="password" autocomplete="off" maxlength="20" name="pwdc" id="pwdc"></td>
                        </tr>
                        <tr>
                            <th>별칭</th>
                            <td><input type="text" name="uname" id="uname" maxlength="10"></td>
                        </tr>
                        <tr>
                            <th>전화번호</th>
                            <td><input type="text" onKeyup="this.value=this.value.replace(/[^-0-9]/g,'');" name="uphone"
                                    id="uphone" maxlength="20"></td>
                        </tr>
                        <tr>
                            <th>주의사항</th>
                            <td> * 관리자가 승인하여야 해당 ID를 사용 할 수 있습니다.</td>
                        </tr>
                        <tr>
                            <td colspan='2' id='id_about' style='font-weight:bold;padding:15px;'> - </td>
                        </tr>
                    </table>
                </form>

                <div style='display:flex;'>
                    <div class="cs_btn" id='id_login' onclick="join()">등 록</div>
                    <div style='width:50px;'></div>
                    <div class='cs_btn' id='id_join' style='width:180px;' onclick="back()">취 소</div>
                </div>
            </div>
            <script>
            window.onload = () => {
                $(document).on("keyup", "#uphone", function() {
                    $(this).val($(this).val().replace(/[^0-9]/g, "").replace(
                        /(^02|^0505|^1[0-9]{3}|^0[0-9]{2})([0-9]+)?([0-9]{4})/, "$1-$2-$3").replace(
                        "--", "-"));
                });
                let inputElements = document.querySelectorAll("input");
                inputElements.forEach((el) => {
                    el.addEventListener("focusin", (e) => {
                        let type = e.target.attributes["id"].value;

                        if (type == "id") document.querySelector("#id_about").innerHTML =
                            " * 최소 5자에서 최대 20자<br/> * 특수문자를 입력 하실 수 없습니다.";
                        else if (type == "pwd" || type == "pwdc") document.querySelector(
                            "#id_about").innerHTML = " * 영문/숫자/특수문자를 조합한 최소 8자 이상";
                        else if (type == "uname") document.querySelector("#id_about").innerHTML =
                            " * 지역명으로 적으시면 A/S접수 시 위치 확인이 더욱 수월합니다.";
                        else if (type == "uphone") document.querySelector("#id_about").innerHTML =
                            " * 휴대폰 번호만 입력해 주세요.(긴급상황 SMS알림)";
                    })
                })
            }

            function join() {
                let obj = FormToObject(document.querySelector("#id_form"));

                let pattern = /[\!\@\#\$\%\^\&\*\(\)\-\_\=\+\\\|\'\"\;\:\/\?\.\>\,\<\`\~\]\[\}\{]/g;
                let pwpatten =
                    /^.*(?=.{8,})(?=.*[0-9])(?=.*[a-zA-Z])(?=.*[\/\?\.\>\<\,\'\"\:\;\\\|\]\}\[\{\=\+\-\_\)\(\*\&\^\%\$\#\@\!\`\~]).*$/;
                let enNumCheck = /[^a-z|^0-9]/gi;
                let strId = obj.id.replace(/\s/g, "");
                let strpwd = obj.pwd.replace(/\s/g, "");
                let strpwdc = obj.pwdc.replace(/\s/g, "");
                let strname = obj.uname.replace(/\s/g, "");
                let phonechk = PhonePattenChk(obj.uphone);
                let reservedId = ["admin", "administrator", "root", "system"];

                if (strId == '') {
                    alert("아이디를 입력하세요");
                    return;
                }

                if (strId.length < 5 || strId.length > 20) {
                    alert("아이디는 최소 5자에서 최대 20자 이내로 입력해주세요.");
                    return;
                }

                if (reservedId.indexOf(strId.toLowerCase()) != -1) {
                    alert("아이디 생성 규칙에 적합하지 않습니다.");
                    return;
                }

                if (pattern.test(strId)) {
                    alert("아이디에 특수문자를 입력 하실 수 없습니다.");
                    return;
                }

                // if( enNumCheck.test(obj.id.replace(/\s/g, "")) )
                // {
                //         alert("아이디는 영문 숫자 조합만 사용 가능합니다.");
                //         return;	
                // }

                if (strpwd == '') {
                    alert("비밀번호를 입력하세요");
                    return;
                }

                if (strpwd.length < 12) {
                    alert("비밀번호는 최소 12자 이상으로 입력해주세요.");
                    return;
                }

                // if (!pwpatten.test(strpwd)) {
                //     alert("비밀번호는 영문, 숫자, 특수문자를 조합하여 입력해주세요.");
                //     return;
                // }

                if (strpwdc == '') {
                    alert("비밀번호 확인을 입력하세요");
                    return;
                }

                if (strpwd != strpwdc) {
                    alert("비밀번호가 일치하지 않습니다.");
                    return;
                }

                if (strname.length > 10) {
                    alert("별칭은 10자 이하로 입력해주세요.");
                    return;
                }

                if (!phonechk) {
                    alert("휴대폰 번호의 번호 체계에 맞지 않습니다.");
                    return;
                }

                obj.id = btoa(encodeURI(obj.id));
                obj.pwd = btoa(encodeURI(obj.pwd));
                obj.pwdc = btoa(encodeURI(obj.pwdc));
                obj.uname = btoa(encodeURI(obj.uname));
                obj.uphone = btoa(encodeURI(obj.uphone));

                const ajax = new XMLHttpRequest();

                ajax.open("POST", "joinOK.php");
                ajax.setRequestHeader('content-type', 'application/json');
                ajax.send(JSON.stringify(obj));

                ajax.onreadystatechange = (e) => {
                    const xhr = e.target;
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        let data = JSON.parse(xhr.response);

                        if (data.code === "200") {
                            alert("정상적으로 처리되었습니다.");
                            history.back();
                        }
                        if (data.code === "400") {
                            alert("중복되는 ID가 있습니다. 다시 입력해주세요.");
                            return false;
                        }
                    }
                }
            }

            function back() {
                history.back();
            }

            function FormToObject(f) {
                let returnObject = new Object();
                try {
                    let myFormData = new FormData(f);

                    myFormData.forEach((value, key) => {
                        if (returnObject[key] == undefined) returnObject[key] = value;
                        else returnObject[key] += `,${value}`;
                    });
                } catch (e) {
                    console.log(e.message);
                }
                return returnObject;
            }

            function PhonePattenChk(phone) {
                let phoneChk1 = /^01([0|1|6|7|8|9]?)-?([0-9]{3,4})-?([0-9]{4})$/;
                let phoneChk2 = /^01([0|1|6|7|8|9]?)([0-9]{7,8})$/;

                var strphone = phone.replace(/\s/g, "");

                if (strphone != '' && (!phoneChk1.test(strphone) || !phoneChk2.test(strphone))) return false;

                return strphone;
            }
            </script>
</body>

</html>