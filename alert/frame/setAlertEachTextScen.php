<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$level = 1;
if (isset($_GET['level'])) {
    $level = $_GET['level'];
} else {
    $level = 1;
}

$disDao = new WB_DISPLAY_DAO();
$disVo = new WB_DISPLAY_VO();

$mentDao = new WB_ISUMENT_DAO();
$mentVo = new WB_ISUMENT_VO();

$eqpDao = new WB_EQUIP_DAO();
$eqpVo = new WB_EQUIP_VO();
?>

<style>
.cs_btn {
    float: none;
    margin: 0 auto;
}

.cs_btn.select {
    background-color: #f94143;
}

.note-editable {
    padding: 0px !important;
}

p {
    margin: 0px !important;
}

input {
    border: 1px solid #d9d9d9;
}
</style>

<div class="cs_loading">
    <div class="cs_message">데이터 전송중입니다.<br>잠시만 기다려주세요.</div>
</div>

<div class="cs_frame">
    <div class="cs_container">
        <div class="cs_displaybox">

            <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all"
                style="box-shadow:none; border:none;">
                <tr style="border: none;">
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 1) {
                            echo 'select';
                        } ?>" id="id_textLevelBtn" data-num="1">1단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 2) {
                            echo 'select';
                        } ?>" id="id_textLevelBtn" data-num="2">2단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 3) {
                            echo 'select';
                        } ?>" id="id_textLevelBtn" data-num="3">3단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 4) {
                            echo 'select';
                        } ?>" id="id_textLevelBtn" data-num="4">4단계</div>
                    </td>
                </tr>
            </table>

            <table id="1" border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">사이즈</th>
                    <th>내용</th>
                    <th width="15%">시나리오 종료</th>
                </tr>
                <?php
                $mentVo = $mentDao->SELECT();
                $disMentLevel = $mentVo->{"DisMent{$level}"};
                if (!empty($disMentLevel)) {
                    $disList = explode(',', $mentVo->{"DisMent{$level}"});
                    $count = 1;
                    $n = '';

                    foreach ($disList as $l) {

                        $disVo = $disDao->SELECT_SINGLE("DisCode = '{$l}'");
                        if ($disVo->CD_DIST_OBSV === null) {
                            continue;
                        }
                        if ($disVo->SendImg === null) {
                            continue;
                        }
                        if (strpos($disVo->SendImg, 'displayImage/') !== false) {
                            // only image, not text
                            continue;
                        }
                        ?>
                <tr align='center'>
                    <td><?= $count++ ?></td>
                    <td>320 x 64 (Fixed)</td>
                    <td>
                        <img alt='이미지를 찾을 수 없습니다.' src='/<?= $disVo->SendImg ?>' onclick='controlDisplay(this)'
                            data-type='select' title='코멘트 변경' value='<?= $disVo->DisCode ?>' style='cursor:pointer;'>
                    </td>
                    <td>
                        <div class='cs_btn delBtn' onclick='controlDisplay(this)' value='<?= $disVo->DisCode ?>'
                            data-type='delete' style='width:80%;'>삭제</div>
                    </td>
                </tr>
                <?php
                    } // foreach
                }

// if
?>
            </table>

            <div class="blank" style="padding-bottom: 50px;"></div>

            <form action="" method="post" id="id_form" onsubmit="return false">
                <div style="margin-top:15px;">◈ 시나리오 내용</div>
                <h4>색상을 먼저 선택하신 후, 전광판에 들어갈 내용을 빈 칸에 입력해주세요</h4>
                <h5>(#1 빨강, #2초록, #3노랑, #4파랑, #5자주, #6하늘색, #7흰색)</h5>
                <div id='text'>
                    <div class="colorBtn">
                        <div class="color color1 btn-3d red" data-color="#1" onclick="addColor('#1')">#1</div>
                        <div class="color color2 btn-3d green" data-color="#2" onclick="addColor('#2')">#2</div>
                        <div class="color color3 btn-3d yellow" data-color="#3" onclick="addColor('#3')">#3</div>
                        <div class="color color4 btn-3d blue" data-color="#4" onclick="addColor('#4')">#4</div>
                        <div class="color color5 btn-3d purple" data-color="#5" onclick="addColor('#5')">#5</div>
                        <div class="color color6 btn-3d cyan" data-color="#6" onclick="addColor('#6')">#6</div>
                        <div class="color color7 btn-3d white" data-color="#7" onclick="addColor('#7')">#7</div>
                    </div>
                    <div class='addMent'>
                        <div class="addingMent">
                            <table border="0" cellpadding="0" cellspacing="0" class="displayTable" rules="all">
                            </table>
                        </div>
                        <table border="0" cellpadding="0" cellspacing="0" class="displayTable" id="displayAddTable"
                            rules="all" style="margin-top: 0px;">
                            <tr>
                                <td style="padding: 0px;"><input type="text" id="addText" value="" maxlength="30"
                                        onkeyup="textChk(this)"></td>
                                <td style="padding: 0px;" width="10%" align="center">
                                    <!-- <div class="addBtn" onclick="addTextBar()">+ 전광판 내용 입력</div> -->
                                    <div id="id_addMentBtn" class="addMentBtn" onclick="addTextBar()">+ 전광판 내용 입력</div>
                                </td>
                            </tr>
                        </table>
                        <div class="byte"><span>0</span> / 20 Byte</div>
                    </div>
                    <div class="displayBox"></div>
                    <div style="display:none;">
                        <textarea id="saveText"></textarea>
                        <textarea id="textNum"></textarea>
                        <input type="hidden" id="checkCount" value=0>
                    </div>
                </div>
                <input type="hidden" name="level" value="<?= $level ?>">
            </form>

            <div class="cs_btnBox" style="margin-top:20px;">
                <div class="cs_btn updateBtn" onclick='controlDisplay(this)' style="display:none;" data-type="update"
                    value="">코멘트 수정</div>
                <div class="cs_btn insertBtn" onclick='controlDisplay(this)' data-type="insert" value="0">코멘트 추가</div>
            </div>

            <div id="id_helpForm">
                <div id="id_help" stat="close">
                    <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
                    <div id='id_helpMessage'> 도움말 보기</div>
                </div>
                <div class='cs_help'>
                    - 단계별 경보발령시, 전광판에 표출될 문구입니다.<br />
                    - [1단계], [2단계], [3단계], [4단계]를 클릭하여 문구를 추가/삭제할 수 있습니다.<br /><br />
                </div>
            </div>

        </div>
    </div>
</div>

<script>
let isSelect = false;

function addTextBar() {
    let sT = document.querySelector("#saveText");
    let saveText = sT.value.length > 0 ? sT.value.split("|") : new Array();

    let tN = document.querySelector("#textNum");
    let textNum = tN.value.length > 0 ? tN.value.split("|") : new Array();

    let addText = document.querySelector("#addText").value;
    const constAddText = addText;

    let checkCount = textNum.length;
    addText = addText.replace(/\#1|\#2|\#3|\#4|\#5|\#6|\#7|/gi, "");

    if (addText == '') {
        alert("전광판에 등록할 메세지를 입력해주세요");
        return;
    }

    let re = /[@\#$%^&*\()\-=+_']/gi;
    if (re.test(addText)) //특수문자가 포함되면 삭제하여 값으로 다시셋팅
    {
        alert("특수문자는 사용할 수 없습니다.");
        document.querySelector("#addText").value = "";
        return;
    }

    let byte = byteCheck(addText);
    if (byte > 20) {
        alert("최대 입력가능한 수를 넘겼습니다.");
        document.querySelector("#addText").value = constAddText.substring(0, 18);
        return;
    }

    if (saveText.length > 0) {
        document.querySelector("#displayAddTable").style.display = "none";
    }

    let html = "";
    html += `<tr id="subTr${checkCount}">`;
    html += `<td>`;
    html += `<div class="textMent">${constAddText}</div>`;
    html += `</td>`;
    html += `<td><div class="subMentBtn" num="${checkCount}" onclick="subText(this)">- 전광판 내용 삭제</div></td>`;
    html += `</tr>`;

    saveText.push(constAddText);
    textNum.push(checkCount);

    $(".addingMent .displayTable").append(html);

    sT.value = saveText.join('|');
    tN.value = textNum.join('|');

    displaySample(saveText);

    // 상태 초기화
    $("#addText").val('');
    $(".byte span").text("0");
    checkCount++;
}

function resetAddingMent() {
    let delBtnArr = document.querySelectorAll(".delBtn") ?? new Array();
    let oldMent = document.querySelector(".addingMent").children[0].children;
    if (oldMent.length < 1) {
        return;
    }

    for (let i = 0; i <= oldMent.length; i++) {
        oldMent[0].remove();
    }

    document.querySelector("#saveText").value = "";
    document.querySelector("#textNum").value = "";
    displaySample(new Array());

    document.querySelector("#displayAddTable").style.display = "";
    delBtnArr.forEach((e) => {
        if (e.getAttribute("data-type") == "cancel") {
            e.setAttribute("data-type", "delete");
            e.innerText = "삭제";
        } else {
            e.style.display = "";
        }
    });

    isSelect = false;
}


async function controlDisplay(t) {
    let type = t.getAttribute("data-type");
    let body = new Object();

    body.type = type;
    body.level = document.querySelector(".cs_btn.select").getAttribute("data-num");

    switch (type) {
        case "select":
            if (isSelect) return;

            body.disCode = t.getAttribute("value");

            await fetch("server/saveWarnEachTextScen.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "json"
                    },
                    body: JSON.stringify(body)
                })
                .then((res) => res.json())
                .then((data) => {
                    if (data.resultCode == 100) {
                        let delBtnArr = document.querySelectorAll(".delBtn") ?? new Array();
                        let contents = data.body;
                        let content = contents.split("|");
                        let sT = document.querySelector("#saveText");
                        let tN = document.querySelector("#textNum");
                        let tNArr = new Array();
                        let chkCnt = 0;

                        resetAddingMent();

                        content.forEach((c) => {
                            let tableEl = document.querySelector(".displayTable");
                            let trEl = document.createElement("tr");
                            let tdEl = document.createElement("td");
                            let divEl = document.createElement("div");
                            trEl.id = `subTr${chkCnt}`;
                            divEl.classList.add("textMent");
                            divEl.innerText = c;
                            tdEl.appendChild(divEl);
                            trEl.appendChild(tdEl);

                            tdEl = document.createElement("td");
                            tdEl.style.width = "10%";
                            tdEl.align = "center";
                            divEl = document.createElement("div");
                            divEl.classList.add("subMentBtn");
                            divEl.setAttribute("num", chkCnt);
                            divEl.addEventListener("click", () => subText(divEl));
                            divEl.innerText = "- 전광판 내용 삭제";
                            tdEl.appendChild(divEl);
                            trEl.appendChild(tdEl);

                            tableEl.appendChild(trEl);

                            tNArr.push(chkCnt++);
                        });

                        if (content.length > 0) {
                            document.querySelector("#displayAddTable").style.display = "none";
                        }

                        delBtnArr.forEach((e) => {
                            if (e.getAttribute("value") == body.disCode) {
                                e.setAttribute("data-type", "cancel");
                                e.innerText = "취소";
                            } else {
                                e.style.display = "none";
                            }
                        });

                        document.querySelector(".insertBtn").style.display = "none";
                        document.querySelector(".updateBtn").style.display = "";
                        document.querySelector(".updateBtn").setAttribute("value", body.disCode);

                        sT.value = contents;
                        tN.value = tNArr.join("|");

                        displaySample(content);
                        isSelect = true;

                    }
                });
            break;

        case "update":
            body.disCode = t.getAttribute("value");

        case "insert":
            imgNode = document.querySelector(".displayBoard");
            if (imgNode === null) {
                alert("전광판에 등록할 메세지를 입력해주세요.");
                return;
            }

            console.log(body);
            await html2canvas(document.querySelector(".displayBoard"))
                .then((canvas) => {
                    var img = canvas.toDataURL("image/png");
                    body.viewImg = img;
                    body.sendImg = document.querySelector("#saveText").value;
                    body.htmlData = document.querySelector(".displayBox").innerHTML;
                });

            await fetch("server/saveWarnEachTextScen.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "json"
                    },
                    body: JSON.stringify(body)
                })
                .then((res) => {
                    return res.json();
                })
                .then((data) => {
                    if (data.resultCode == 200) {
                        alert("정상적으로 처리되었습니다.");
                        getFrame(`frame/setAlertEachTextScen.php?level=${body.level}`, pType, 4, "false");
                    }
                })
                .catch((ex) => {
                    console.log(ex);
                });
            break;

        case "delete":
            if (confirm("삭제하시겠습니까?") == false) return;

            body.disCode = t.getAttribute("value");
            console.log(body);
            await fetch("server/saveWarnEachTextScen.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "json"
                    },
                    body: JSON.stringify(body)
                })
                .then((res) => {
                    return res.json();
                })
                .then((data) => {
                    if (data.resultCode == 200) {
                        alert("정상적으로 처리되었습니다.");
                        getFrame(`frame/setAlertEachTextScen.php?level=${body.level}`, pType, 4, "false");
                    }
                })
                .catch((ex) => {
                    console.log(ex);
                });
            break;

        case "cancel":
            resetAddingMent();
            document.querySelector(".insertBtn").style.display = "";
            document.querySelector(".updateBtn").style.display = "none";
            document.querySelector(".updateBtn").setAttribute("value", "");
            break;
    }
}
</script>