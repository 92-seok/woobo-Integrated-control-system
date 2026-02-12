// JavaScript Document

$(document).ready(function (e) {
    /* Checkbox 전체 체크 (공용) */
    $(document).on('click', '#id_allCheck', function () {
        var checked = $(this).is(':checked');

        if (checked == true) {
            $('.cs_disChk').prop('checked', true);
        } else {
            $('.cs_disChk').prop('checked', false);
        }
    });

    /* Page (공용) */
    $(document).on('click', '#id_page', function () {
        let url = $(this).attr('data-url');
        let idx = $(this).attr('data-idx');
        getFrame(url, pType, idx, 'true');
    });

    ////////////////* eachEquList 영역 *//////////////////
    // /* 해당 장비 정보 진입! */
    // $(document).on("click", "#id_disList", function()
    // {
    // 	let num = $(this).attr("data-num");
    // 	getFrame("", "frame/sendEachScen.php?num=" + num, pType, 0, "true");
    // });

    ////////////////* sendEachScen 영역 *//////////////////
    // /* 시나리오 추가 */
    // $(document).on("click", "#id_addEachScen",function()
    // {
    // 	let num = $(this).attr("data-num");
    // 	let page = $(this).attr("data-page");

    // 	getFrame("insert", "frame/eachScenForm.php?page=" + page + "&num=&areaCode=" + num, pType, 0, "true");
    // });

    // /* 시나리오 변경 */
    // $(document).on("click", "#id_updEachScen", function()
    // {
    // 	let equNum = $(this).attr("value");
    // 	let num = $(this).attr("data-num");
    // 	let type = $(this).attr("data-type");
    // 	let page = $(this).attr("data-page");

    // 	if( type == "mois" )
    // 	{
    // 		alert("행안부에서 등록된 시나리오는 수정할 수 없습니다.");
    // 		return;
    // 	}

    // 	getFrame("update", "frame/eachScenForm.php?page=" + page + "&num=" + equNum + "&areaCode=" + num, pType, 0, "true");
    // });

    // /* 해당 시나리오 종료 */
    // $(document).on("click", "#id_endEachScen", function()
    // {
    // 	let scen = $(this).attr("value");
    // 	let num = $(this).attr("data-num");
    // 	let page = $(this).attr("data-page");

    // 	if(confirm('해당 시나리오를 종료하시겠습니까?'))
    // 	{
    // 		let formobj = {scen : scen, type : "end"};
    // 		getScenario(formobj, num, page);
    // 	}
    // });

    // /* 그룹 시나리오 일괄 종료 */
    // $(document).on("click", "#id_endGroupScen", function()
    // {
    // 	let scen = "";	// cdDistObsv
    // 	let num = $(this).attr("data-num");
    // 	let page = $(this).attr("data-page");

    // 	$(".cs_disChk:checked").each(function()
    // 	{
    // 		scen = scen + "," + $(this).attr("value");
    // 	});

    // 	if(scen == "")
    // 	{
    // 		alert("삭제할 시나리오가 선택되지 않았습니다.");
    // 		return;
    // 	}

    // 	if(confirm('해당 시나리오를 종료하시겠습니까?'))
    // 	{
    // 		let formobj = {scen : scen, type : "groupEnd"};
    // 		getScenario(formobj, num, page);
    // 	}

    // });

    // /* 해당 시나리오 삭제!!!! */
    // $(document).on("click", "#id_delEachScen", function()
    // {
    // 	let scen = "";
    // 	let num = $(this).attr("data-num");
    // 	let page = $(this).attr("data-page");

    // 	$(".cs_disChk:checked").each(function()
    // 	{
    // 		scen = scen + "," + $(this).attr("value");
    // 	});

    // 	if (scen == "")
    // 	{
    // 		alert("삭제할 시나리오가 선택되지 않았습니다.");
    // 		return;
    // 	}

    // 	if (confirm('정말 삭제하시겠습니까?'))
    // 	{
    // 		let formobj = {scen : scen.substring(1, scen.length), type : "delete"};
    // 		getScenario(formobj, num, page);
    // 	}
    // });

    /* 시나리오 전송~! */
    $(document).on('click', '#id_savScen', async function () {
        let form = FormToObject(document.querySelector('#id_form'));

        let type = document.querySelector('#type').value;
        // let imgNode = document.querySelector(".note-editable");
        let imgNode = document.querySelector('#id_summernote_img');
        let relay = 0;

        let startDate = document.querySelector('#startDate').value;
        let startHour = document.querySelector('#startHour').value;
        let endDate = document.querySelector('#endDate').value;
        let endHour = document.querySelector('#endHour').value;

        // c16 전광판 사이즈
        form.sizeX = document.querySelector('#sizeX').value;
        form.sizeY = document.querySelector('#sizeY').value;

        form.viewType = document.querySelector('#viewType').value;
        form.kind = document.querySelector('#kind').value;

        // 파일명을 여기서 생성한다.
        form.c16FileNameView = `${form.cdDistObsv}_text_${getDateAndTime()}.png`;
        form.c16FileNameSend = `${form.cdDistObsv}_thumb_${getDateAndTime()}.png`;
        form.c16FileNameVideo = `${form.cdDistObsv}_video_${getDateAndTime()}.mp4`;

        if (!dateTest(startDate)) return;
        if (!dateTest(endDate)) return;

        form.strDate = `${startDate} ${startHour}:00:00`;
        form.endDate = `${endDate} ${endHour}:00:00`;

        document.querySelectorAll('.cs_relay').forEach((e) => {
            if (e.checked) {
                relay += parseInt(e.value);
            }
        });
        form.relay = relay;

        // c16 이미지 효과
        if (type == 'group') {
            form.disEffect = document.querySelector('#disEffect').value;
            form.disSpeed = document.querySelector('#disSpeed').value;
            form.endEffect = document.querySelector('#endEffect').value;
            form.endSpeed = document.querySelector('#endSpeed').value;
            form.disTime = document.querySelector('#disTime').value;
        } else {
            form.disEffectHttp = document.querySelector('#disEffectHttp').value;
            form.disSpeedHttp = document.querySelector('#disSpeedHttp').value;
            form.endEffectHttp = document.querySelector('#endEffectHttp').value;
            form.endSpeedHttp = document.querySelector('#endSpeedHttp').value;
            form.disTimeHttp = document.querySelector('#disTimeHttp').value;
        }

        // if (type == "group") {
        //     let equip = new Array();

        //     document.querySelectorAll(".cs_disChk").forEach((e) => {
        //         if (e.checked) {
        //             equip.push(e.value);
        //         }
        //     });

        //     if (equip.length < 1) {
        //         alert("장비를 선택해주세요.");
        //         return;
        //     }

        //     form.cdDistObsv = equip.join(",");
        // }

        if (form.viewType === 'text') {
            imgNode = document.querySelector('.displayBox');
            // imgNode = document.querySelector(".displayBoard");
            if (imgNode === null) {
                alert('전광판에 등록할 메세지를 입력해주세요.');
                return;
            }
        } else if (form.viewType === 'image') {
            if (document.querySelector('#kind') != null) {
                form.viewType = document.querySelector('#kind').value;
            } else {
                form.viewType = 'image';
                form.kind = 'image';
            }
        } else if (form.viewType === 'video') {
            if (form.file.name === '') {
                alert('비디오 파일을 첨부해주세요.');
                return;
            }

            let formData = new FormData(document.querySelector('#id_form'));
            formData.append('c16FileNameVideo', '');

            await getVideo(formData).then((data) => {
                // isSave는 정상 값을 받으면 trye를 반환한다.
                form.isSave = data;
                form.viewImg = form.file.name;
            });
        } else if (form.viewType === 'c16' && form.kind === 'video') {
            if (form.file.name === '') {
                alert('비디오 파일을 첨부해주세요.');
                return;
            }

            let formData = new FormData(document.querySelector('#id_form'));
            formData.append('c16FileNameVideo', form.c16FileNameVideo);

            await getVideoC16(formData).then((data) => {
                form.isSave = data;
                form.viewImg = form.file.name;
            });
        }

        html2canvas(imgNode)
            .then((canvas) => {
                form.viewImg = canvas.toDataURL('image/png');
            })
            .then(() => {
                switch (form.viewType) {
                    case 'text':
                        form.kind = 'text';
                        form.sendImg = document.querySelector('#saveText').value;
                        form.htmlData = document.querySelector('.displayBox').innerHTML;
                        break;

                    case 'image':
                        form.htmlData = document.querySelector('#id_summernote_img').innerHTML;
                        break;

                    case 'c16':
                        if (form.viewType === 'c16' && form.kind === 'image') {
                            form.htmlData = document.querySelector('#id_summernote_img').innerHTML;
                        }
                        break;

                    case 'video':
                        break;
                }

                // c16일경우 getScenarioC16 함수를, 아닐경우 getScenario 함수를 실행한다.
                if (form.viewType === 'c16') {
                    return getScenarioC16(form);
                } else {
                    return getScenario(form);
                }
            })
            .then((url) => {
                alert('전광판 이미지가 업로드되었습니다.');
                getFrame(url, 'display', -1, 'false');
            })
            .catch((ex) => {
                console.log(ex);
                console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
                alert('전광판 이미지 업로드에 실패하였습니다. 관리자에게 문의하세요.');
            });
    });

    /* 그룹 시나리오 전송 */
    $(document).on('click', '#id_savGroupScen', async function () {
        console.log('그룹 시나리오 일괄 전송');

        let form = FormToObject(document.querySelector('#id_form'));

        // let type = document.querySelector("#type").value;
        let chkEndType = form.type;
        console.log('chkEndType: ', form.type);
        form.type = 'insert';

        // let imgNode = document.querySelector(".note-editable");
        let imgNode = document.querySelector('#id_summernote_img');
        let relay = 0;

        let startDate = document.querySelector('#startDate').value;
        let startHour = document.querySelector('#startHour').value;
        let endDate = document.querySelector('#endDate').value;
        let endHour = document.querySelector('#endHour').value;

        // c16 전광판 사이즈
        form.sizeX = document.querySelector('#sizeX').value;
        form.sizeY = document.querySelector('#sizeY').value;

        form.viewType = document.querySelector('#viewType').value;
        form.kind = document.querySelector('#kind').value;

        // 파일명을 여기서 생성한다.
        form.c16FileNameView = `${form.cdDistObsv}_text_${getDateAndTime()}.png`;
        form.c16FileNameSend = `${form.cdDistObsv}_thumb_${getDateAndTime()}.png`;
        form.c16FileNameVideo = `${form.cdDistObsv}_video_${getDateAndTime()}.mp4`;

        if (!dateTest(startDate)) return;
        if (!dateTest(endDate)) return;

        form.strDate = `${startDate} ${startHour}:00:00`;
        form.endDate = `${endDate} ${endHour}:00:00`;

        document.querySelectorAll('.cs_relay').forEach((e) => {
            if (e.checked) {
                relay += parseInt(e.value);
            }
        });
        form.relay = relay;

        form.disEffect = document.querySelector('#disEffect').value;
        form.disSpeed = document.querySelector('#disSpeed').value;
        form.endEffect = document.querySelector('#endEffect').value;
        form.endSpeed = document.querySelector('#endSpeed').value;
        form.disTime = document.querySelector('#disTime').value;

        // // c16 이미지 효과
        // if (type == "group") {
        //     form.disEffect = document.querySelector("#disEffect").value;
        //     form.disSpeed = document.querySelector("#disSpeed").value;
        //     form.endEffect = document.querySelector("#endEffect").value;
        //     form.endSpeed = document.querySelector("#endSpeed").value;
        //     form.disTime = document.querySelector("#disTime").value;
        // } else {
        //     form.disEffectHttp = document.querySelector("#disEffectHttp").value;
        //     form.disSpeedHttp = document.querySelector("#disSpeedHttp").value;
        //     form.endEffectHttp = document.querySelector("#endEffectHttp").value;
        //     form.endSpeedHttp = document.querySelector("#endSpeedHttp").value;
        //     form.disTimeHttp = document.querySelector("#disTimeHttp").value;
        // }

        // if (type == "group") {
        let equip = new Array();

        document.querySelectorAll('.cs_disChk').forEach((e) => {
            if (e.checked) {
                equip.push(e.value);
            }
        });

        if (equip.length < 1) {
            alert('장비를 선택해주세요.');
            return;
        }

        form.cdDistObsv = equip.join(',');
        // }

        if (form.viewType === 'text') {
            imgNode = document.querySelector('.displayBox');
            if (imgNode === null) {
                alert('전광판에 등록할 메세지를 입력해주세요.');
                return;
            }
        } else if (form.viewType === 'image') {
            if (document.querySelector('#kind') != null) {
                form.viewType = document.querySelector('#kind').value;
            } else {
                form.viewType = 'image';
                form.kind = 'image';
            }
        } else if (form.viewType === 'video') {
            if (form.file.name === '') {
                alert('비디오 파일을 첨부해주세요.');
                return;
            }

            let formData = new FormData(document.querySelector('#id_form'));
            formData.append('c16FileNameVideo', '');

            await getVideo(formData).then((data) => {
                // isSave는 정상 값을 받으면 trye를 반환한다.
                form.isSave = data;
                form.viewImg = form.file.name;
            });
        } else if (form.viewType === 'c16' && form.kind === 'video') {
            if (form.file.name === '') {
                alert('비디오 파일을 첨부해주세요.');
                return;
            }

            let formData = new FormData(document.querySelector('#id_form'));
            formData.append('c16FileNameVideo', form.c16FileNameVideo);

            await getVideoC16(formData).then((data) => {
                form.isSave = data;
                form.viewImg = form.file.name;
            });
        }

        html2canvas(imgNode)
            .then((canvas) => {
                form.viewImg = canvas.toDataURL('image/png');
            })
            .then(() => {
                switch (form.viewType) {
                    case 'text':
                        form.kind = 'text';
                        form.sendImg = document.querySelector('#saveText').value;
                        form.htmlData = document.querySelector('.displayBox').innerHTML;
                        break;

                    case 'image':
                        form.kind = 'image';
                        form.sendImg = document.querySelector('#saveText').value;
                        form.htmlData = document.querySelector('#id_summernote_img').innerHTML;
                        break;

                    case 'c16':
                        if (form.viewType === 'c16' && form.kind === 'image') {
                            form.htmlData = document.querySelector('#id_summernote_img').innerHTML;
                        }
                        break;

                    case 'video':
                        break;
                }

                // c16일경우 getScenarioC16 함수를, 아닐경우 getScenario 함수를 실행한다.
                if (form.viewType === 'c16') {
                    return getScenarioC16(form);
                } else {
                    return getScenarioGroup(form);
                }
            })
            .then((url) => {
                alert('전광판 이미지가 업로드되었습니다.');
                getFrame(url, 'display', -1, 'false');
            })
            .catch((ex) => {
                console.log(ex);
                console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
                alert('전광판 이미지 업로드에 실패하였습니다. 관리자에게 문의하세요.');
            });
    });

    /* 그룹 시나리오 일괄 종료 */
    $(document).on('click', '#id_endGroupScen', async function () {
        console.log('그룹 시나리오 일괄 종료');

        // <div class='cs_btn' onclick='endEachScen(<?= $vo->DisCode ?>, "<?= $cdDistObsv ?>", <?= $page?>, "<?= $ip ?>", <?= $port ?>, <?= $sizeX ?>, <?= $sizeY ?>)' style='float:none; margin:auto;'>시나리오 종료</div>
        // endGroupScen();
        let form = FormToObject(document.querySelector('#id_form'));
        let chkEndType = form.type;
        console.log('chkEndType: ', form.type);
        // form.type = "insert";
        // form["disCode"] = disCode;
        // form["cdDistObsv"] = cdDistObsv;
        form['page'] = 1;
        form['type'] = 'end';

        // await getScenario(form)
        //     .then((url) => {
        //         alert("정상적으로 처리되었습니다.");
        //         getFrame(url, "display", -1, "false");
        //     })
        //     .catch((ex) => {
        //         console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
        //     });

        // let scen = ""; // cdDistObsv
        let num = $(this).attr('data-num') ?? '';
        let page = $(this).attr('data-page') ?? 1;
        console.log('num:', num);
        console.log('page:', page);

        // $(".cs_disChk:checked").each(function() {
        //     scen = scen + "," + $(this).attr("value");
        // });
        // console.log("scen:", scen);
        let equip = new Array();
        document.querySelectorAll('.cs_disChk').forEach((e) => {
            if (e.checked) {
                equip.push(e.value);
            }
        });

        if (equip.length === 0) {
            alert('삭제할 시나리오가 선택되지 않았습니다.');
            return;
        }

        form.cdDistObsv = equip.join(',');

        if (confirm('그룹 시나리오를 정말 모두 종료하시겠습니까?') == false) {
            alert('그룹 시나리오가 종료가 취소되었습니다.');
            return;
        }

        // let form = {
        //     scen : scen,
        //     type : "groupEnd"
        // };
        // getScenario(formobj, num, page);

        await getScenarioGroup(form)
            .then((url) => {
                alert('정상적으로 처리되었습니다.');
                getFrame(url, 'display', -1, 'false');
            })
            .catch((ex) => {
                console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
            });
    });

    $(document).on('click', '.addMentBtn', function () {
        console.log('addMentBtn.x:', this.dataset.x);
        console.log('addMentBtn.y:', this.dataset.y);

        const x = this.dataset.x;
        const y = this.dataset.y;

        let sT = document.querySelector('#saveText');
        let saveText = sT.value.length > 0 ? sT.value.split('|') : new Array();

        let tN = document.querySelector('#textNum');
        let textNum = tN.value.length > 0 ? tN.value.split('|') : new Array();

        let $addText = document.querySelector('#addText');
        let addTextValue = $addText.value;

        let checkCount = textNum.length;
        let replacedAddText = addTextValue.replace(/\#1|\#2|\#3|\#4|\#5|\#6|\#7|/gi, '');
        if (replacedAddText === '') {
            alert('전광판에 등록할 메세지를 입력해주세요');
            return;
        }

        let re = /[@$%^&*()\-=+_'\\]/gi;
        // re = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/gi;
        if (re.test(replacedAddText)) {
            //특수문자가 포함되면 삭제하여 값으로 다시셋팅
            alert('특수문자는 저장할 수 없습니다.');
            $addText.value = addTextValue.substring(0, addTextValue.length - 1);
            let byteLen = byteCheck($addText.value);
            $('.byte span').text(byteLen);
            return;
        }

        let byte = byteCheck(replacedAddText);
        if (byte > 20) {
            alert('최대 입력가능한 수를 넘겼습니다.');
            $addText.value = addTextValue.substring(0, addTextValue.length - 1);
            let byteLen = byteCheck($addText.value);
            $('.byte span').text(byteLen);
            return;
        }

        // $(".byte span").text(byte);

        // if( (saveText.length/2) > 15.9 ){
        // 	alert("시나리오는 최대 16개까지만 등록가능합니다.");
        // 	return;
        // }

        // if (saveText.length > 0) {
        //     document.querySelector("#displayAddTable").style.display = "none";
        // }

        let html = '';
        html += `<tr id='subTr${checkCount}'>`;
        html += `<td>`;
        html += `<div class="textMent">${addTextValue}</div>`;
        html += `</td>`;
        html += `<td><div class="subMentBtn" data-id="${checkCount}">- 전광판 내용 삭제 ${checkCount + 1}</div></td>`;
        html += '</tr>';

        saveText.push(addTextValue);
        textNum.push(checkCount);

        $('.addingMent .displayTable').append(html);

        sT.value = saveText.join('|');
        tN.value = textNum.join('|');

        displaySample(saveText, x, y);

        // 상태 초기화
        $('#addText').val('');
        $('.byte span').text('0');

        checkCount++;
    });

    $(document).on('click', '.subMentBtn', function () {
        let sT = document.querySelector('#saveText');
        let saveText = sT.value.length > 0 ? sT.value.split('|') : new Array();

        let tN = document.querySelector('#textNum');
        let textNum = tN.value.length > 0 ? tN.value.split('|') : new Array();

        let id = $(this).attr('data-id');

        for (let i = textNum.length - 1; i >= 0; i--) {
            if (textNum[i] == id) {
                textNum.splice(i, 1);
                saveText.splice(i, 1);
            }
        }

        $(this).parent().parent().remove();

        sT.value = saveText.join('|');
        tN.value = textNum.join('|');

        displaySample(saveText, 320, 64);

        // if (saveText.length < 2) {
        //     document.querySelector("#displayAddTable").style.display = "";
        // }
    });

    $(document).on('keyup', '#addText', function () {
        let $addText = document.querySelector('#addText');
        let addTextValue = $addText.value;
        let replacedAddText = addTextValue.replace(/\#1|\#2|\#3|\#4|\#5|\#6|\#7|/gi, '');

        let re = /[@$%^&*()\-=+_'\\]/gi;
        // re = /[`!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?~]/gi;
        if (re.test(addTextValue)) {
            //특수문자가 포함되면 삭제하여 값으로 다시셋팅
            alert('특수문자는 입력할 수 없습니다.');
            $addText.value = addTextValue.substring(0, addTextValue.length - 1);
            return;
        }

        let byte = byteCheck(replacedAddText);
        if (byte > 20) {
            alert('최대 입력가능한 수를 넘겼습니다.');
            $addText.value = addTextValue.substring(0, addTextValue.length - 1);
            let byteLen = byteCheck($addText.value);
            $('.byte span').text(byteLen);
            return;
        }

        $('.byte span').text(byte);
    });

    $(document).on('keydown', '#addText', function (e) {
        if (e.key === 'Enter') {
            $('.addMentBtn').trigger('click');
        }
    });

    $(document).on('click', '.color', function () {
        let color = $(this).attr('data-color');

        let cursorPos = $('#addText').prop('selectionStart');
        let v = $('#addText').val();
        let textBefore = v.substring(0, cursorPos);
        let textAfter = v.substring(cursorPos, v.length);

        $('#addText').val(textBefore + color + textAfter);
        $('#addText').focus();
    });

    //지정멘트 선택 event
    $(document).on('change', '#mentList', function () {
        let mtl = document.querySelector('#mentList').value;
        let form = { disCode: mtl, type: 'change' };
        // let num = "";
        // let page = 1;

        return new Promise((resolve, reject) => {
            const sendData = new XMLHttpRequest();
            const url = `server/displayScenarioMentChange.php`;

            sendData.open('POST', url);
            sendData.setRequestHeader('content-type', 'application/json');
            sendData.send(JSON.stringify(form));

            sendData.onreadystatechange = function (e) {
                const xhr = e.target;
                let ex = new Object();

                try {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        console.log(xhr.response);
                        const data = JSON.parse(xhr.response);

                        if (data.resultCode === 200) {
                            getlog('display', 'frame/eachScenForm.php', data.logEvent, data.logName, data.logBefore, data.logAfter);
                            // var textareaValue1 = $('#id_summernote').summernote('code');
                            $('#id_summernote').summernote('code', data.HtmlData);
                            resolve(data.HtmlData);
                        } else {
                            ex['status'] = data.resultCode;
                            ex['statusText'] = data.resultMessage;

                            throw new Error(ex);
                        }
                    } else if (xhr.status !== 200) {
                        ex['status'] = xhr.status;
                        ex['statusText'] = xhr.statusText;

                        throw new Error(ex);
                    }
                } catch (ex) {
                    reject(ex);
                }
            };
        });
    });
});

/* 해당 장비 정보 진입! */
function eachScenDetail(cdDistObsv) {
    let url = `frame/sendEachScen.php?cdDistObsv=${cdDistObsv}`;
    getFrame(url, pType, 0, 'true');
}

/* 시나리오 추가 */
function addEachScen(cdDistObsv, page) {
    let url = `frame/eachScenForm.php?dType=insert&cdDistObsv=${cdDistObsv}&page=${page}`;
    getFrame(url, pType, 0, 'true');
}

/* 시나리오 변경 */
function updEachScen(disCode, cdDistObsv, page, type = 'local') {
    if (type === 'mois') {
        alert('행안부에서 등록된 시나리오는 수정할 수 없습니다.');
        return;
    }

    let url = `frame/eachScenForm.php?dType=update&disCode=${disCode}&cdDistObsv=${cdDistObsv}&page=${page}`;
    getFrame(url, pType, 0, 'true');
}

/* 시나리오 종료 */
async function endEachScen(disCode, cdDistObsv, page, ip, port, sizeX, sizeY) {
    let form = new Object();
    form['disCode'] = disCode;
    form['cdDistObsv'] = cdDistObsv;
    form['page'] = page;
    form['type'] = 'end';
    // c16 정보
    form['endIp'] = ip;
    form['endPort'] = port;
    form['endSizeX'] = sizeX;
    form['endSizeY'] = sizeY;

    // c16이면
    if (cdDistObsv >= 1200 && cdDistObsv <= 1299) {
        await getScenarioC16(form)
            .then((url) => {
                alert('정상적으로 처리되었습니다.');
                getFrame(url, 'display', -1, 'false');
            })
            .catch((ex) => {
                console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
            });
    }

    await getScenario(form)
        .then((url) => {
            alert('정상적으로 처리되었습니다.');
            getFrame(url, 'display', -1, 'false');
        })
        .catch((ex) => {
            console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
        });
}

/* 시나리오 삭제 */
async function delEachScen(cdDistObsv, page) {
    let form = new Object();

    let chk = document.querySelectorAll('.cs_disChk');
    form['disCode'] = new Array();
    chk.forEach((e) => {
        if (e.checked) {
            form['disCode'].push(e.value);
        }
    });

    if (form['disCode'].length < 1) {
        alert('삭제할 시나리오가 선택되지 않았습니다.');
        return;
    }

    if (confirm('정말 삭제하시겠습니까?') == false) {
        alert('시나리오 삭제가 취소되었습니다.');
        return;
    }

    form['cdDistObsv'] = cdDistObsv;
    form['page'] = page;
    form['type'] = 'delete';

    await getScenario(form)
        .then((url) => {
            alert('정상적으로 처리되었습니다.');
            getFrame(url, 'display', -1, 'true');
        })
        .catch((ex) => {
            console.log(`code:${ex.status}\r\nmsg:${ex.statusText}`);
        });
}

function dateTest(date) {
    let dateChk =
        /^(?=\d)(?:(?:31(?!.(?:0?[2469]|11))|(?:30|29)(?!.0?2)|29(?=.0?2.(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00)))(?:\x20|$))|(?:2[0-8]|1\d|0?[1-9]))([-.\/])(?:1[012]|0?[1-9])\1(?:1[6-9]|[2-9]\d)?\d\d(?:(?=\x20\d)\x20|$))?(((0?[1-9]|1[012])(:[0-5]\d){0,2}(\x20[AP]M))|([01]\d|2[0-3])(:[0-5]\d){1,2})?$/;

    let dateRegex = date.split('-');
    let rtv = true;

    try {
        dateRegex.forEach((e) => {
            let dataNum = new Number(e);
            if (dataNum.toString() === 'NaN') {
                console.log('시나리오 시작일에는 숫자를 이용한 날짜 기입만 가능합니다.');
                rtv = false;
            }
        });

        let y = parseInt(dateRegex[0], 10),
            m = parseInt(dateRegex[1], 10),
            d = parseInt(dateRegex[2], 10);

        if (!dateChk.test(d + '-' + m + '-' + y)) {
            alert('시나리오 시작일을 다시 확인해 주세요');
            rtv = false;
        }
    } catch (err) {
        alert(err);
        rtv = false;
    } finally {
        return rtv;
    }
}

async function getScenarioC16(form) {
    // 전광판 표시 효과
    let disEffectHttp = form.disEffectHttp;
    let disSpeedHttp = form.disSpeedHttp;
    let endEffectHttp = form.disEffectHttp;
    let endSpeedHttp = form.endSpeedHttp;
    let disTimeHttp = form.disTimeHttp;

    // 전광판 사이즈
    let sizeX = form.sizeX;
    let sizeY = form.sizeY;

    // 이미지인지 동영상인지
    let kind = form.kind;

    // 이미지 파일명, 동영상 파일명
    let c16FileNameView = form.c16FileNameView;
    let c16FileNameVideo = form.c16FileNameVideo;

    // 시나리오 종료 시 종료 할 전광판의 아이피, 사이즈 정보
    let endIp = form.endIp;
    let endPort = form.endPort;
    let endSizeX = form.endSizeX;
    let endSizeY = form.endSizeY;

    // chkEndType이 end면 시나리오 종료
    let chkEndType = form.type;

    /********** c16에 보낼 JSON ********/
    // 시나리오 전송 후 파라미터 전송해야 함
    const imageDataScenario = {
        Sequence: {
            sequence: ['ST_003_240312042600'],
        },
        ST_003_240312042600: {
            'play.times': 1,
            display: {
                frames: [
                    {
                        x: 0,
                        y: 0,
                        ['width']: sizeX,
                        ['height']: sizeY,
                        objects: [
                            {
                                type: 'image',
                                file: '{ST_003_240312042600-fileName}',
                                effect: {
                                    'display.effect': '{ST_003_240312042600-displayEffect}',
                                    'clear.effect': '{ST_003_240312042600-clearEffect}',
                                    'hold.time': '{ST_003_240312042600-holdTime}',
                                    'display.time': '{ST_003_240312042600-displayTime}',
                                    'clear.time': '{ST_003_240312042600-clearTime}',
                                },
                            },
                        ],
                    },
                ],
            },
        },
    };

    const videoDataScenario = {
        Sequence: {
            sequence: ['ST_003_240312041700'],
        },
        ST_003_240312041700: {
            'play.times': 1,
            display: {
                frames: [
                    {
                        x: 0,
                        y: 0,
                        ['width']: sizeX,
                        ['height']: sizeY,
                        objects: [
                            {
                                type: 'movie',
                                file: '{ST_003_240312041700-fileName}',
                                playtimes: '{ST_003_240312041700-playtimes}',
                                volume: '{ST_003_240312041700-volume}',
                            },
                        ],
                    },
                ],
            },
        },
    };

    const endDataScenario = {
        Sequence: {
            sequence: ['ST_003_240312042600'],
        },
        ST_003_240312042600: {
            'play.times': 1,
            display: {
                frames: [
                    {
                        x: 0,
                        y: 0,
                        ['width']: endSizeX,
                        ['height']: endSizeY,
                        objects: [
                            {
                                type: 'image',
                                file: '{ST_003_240312042600-fileName}',
                                effect: {
                                    'display.effect': '{ST_003_240312042600-displayEffect}',
                                    'clear.effect': '{ST_003_240312042600-clearEffect}',
                                    'hold.time': '{ST_003_240312042600-holdTime}',
                                    'display.time': '{ST_003_240312042600-displayTime}',
                                    'clear.time': '{ST_003_240312042600-clearTime}',
                                },
                            },
                        ],
                    },
                ],
            },
        },
    };
    const imageDataParam = {
        ['{ST_003_240312042600-fileName}']: c16FileNameView,
        ['{ST_003_240312042600-displayEffect}']: disEffectHttp,
        ['{ST_003_240312042600-clearEffect}']: endEffectHttp,
        ['{ST_003_240312042600-holdTime}']: disTimeHttp,
        ['{ST_003_240312042600-displayTime}']: disSpeedHttp,
        ['{ST_003_240312042600-clearTime}']: endSpeedHttp,
    };

    const videoDataParam = {
        ['{ST_003_240312041700-fileName}']: c16FileNameVideo,
        ['{ST_003_240312041700-playtimes}']: 1,
        ['{ST_003_240312041700-volume}']: 100,
    };

    // 시나리오 종료 시에는 별도의 검은화면을 표시하는 방식임.
    //  blankImage는 MC 경로에 별도의 해당 전광판에 맞는 사이즈의 검은 화면 이미지가 필요함.
    const endDataParam = {
        ['{ST_003_240312042600-fileName}']: `blankImage${endSizeX}x${endSizeY}.png`,
        ['{ST_003_240312042600-displayEffect}']: 0,
        ['{ST_003_240312042600-clearEffect}']: 0,
        ['{ST_003_240312042600-holdTime}']: 0,
        ['{ST_003_240312042600-displayTime}']: 0,
        ['{ST_003_240312042600-clearTime}']: 0,
    };

    // console.log("JSON 파라미터 확인용 :c16FileNameView ",c16FileNameView);
    // console.log("JSON 파라미터 확인용 :disEffectHttp ",disEffectHttp);
    // console.log("JSON 파라미터 확인용 :endEffectHttp ",endEffectHttp);
    // console.log("JSON 파라미터 확인용 :disTimeHttp ",disTimeHttp);
    // console.log("JSON 파라미터 확인용 :disSpeedHttp ",disSpeedHttp);
    // console.log("JSON 파라미터 확인용 :endSpeedHttp ",endSpeedHttp);
    // console.log("JSON 파라미터 확인용 :sizeX ",sizeX);
    // console.log("JSON 파라미터 확인용 :sizeY ",sizeY);

    /**************************/
    return new Promise((resolve, reject) => {
        const sendData = new XMLHttpRequest();
        const url = `server/displayScenario.php`;

        sendData.open('POST', url);
        sendData.setRequestHeader('content-type', 'application/json');
        sendData.send(JSON.stringify(form));

        sendData.onreadystatechange = function (e) {
            const xhr = e.target;
            let ex = new Object();

            try {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.response);
                    const data = JSON.parse(xhr.response);

                    if (data.resultCode === 200) {
                        const changeUrl = `frame/sendEachScen.php?cdDistObsv=${form.cdDistObsv}&page=${form.page}`;

                        getlog('display', 'frame/sendEachScen.php', data.logEvent, data.logName, data.logBefore, data.logAfter);
                        resolve(changeUrl);

                        // 시나리오 종료시
                        if (chkEndType === 'end') {
                            console.log('resultCode 200 전송 확인');
                            // 시나리오 종료핧때에는 endIp, endPort, chkEndType을 파라미터에 추가함.
                            sendHttpScenario(endDataScenario, endIp, endPort, chkEndType);
                            sendHttpParams(endDataParam, endIp, endPort, chkEndType);
                        } else {
                            // 시나리오 종료가 아니면
                            if (kind === 'image') {
                                console.log('resultCode 200 전송 확인');
                                sendHttpScenario(imageDataScenario);
                                sendHttpParams(imageDataParam);
                            } else if (kind === 'video') {
                                sendHttpScenario(videoDataScenario);
                                sendHttpParams(videoDataParam);
                            }
                        }
                    } else {
                        ex['status'] = data.resultCode;
                        ex['statusText'] = data.resultMessage;

                        throw new Error(ex);
                    }
                } else if (xhr.status !== 200) {
                    ex['status'] = xhr.status;
                    ex['statusText'] = xhr.statusText;
                    console.log('c16 test 4');

                    throw new Error(ex);
                }
            } catch (ex) {
                reject(ex);
            }
        };
    });
}

async function getScenario(form) {
    return new Promise((resolve, reject) => {
        const sendData = new XMLHttpRequest();
        const url = `server/displayScenario.php`;

        sendData.open('POST', url);
        sendData.setRequestHeader('content-type', 'application/json');
        sendData.send(JSON.stringify(form));

        sendData.onreadystatechange = function (e) {
            const xhr = e.target;
            let ex = new Object();

            try {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.response);
                    const data = JSON.parse(xhr.response);

                    if (data.resultCode === 200) {
                        const changeUrl = `frame/sendEachScen.php?cdDistObsv=${form.cdDistObsv}&page=${form.page}`;

                        getlog('display', 'frame/sendEachScen.php', data.logEvent, data.logName, data.logBefore, data.logAfter);
                        resolve(changeUrl);
                    } else {
                        ex['status'] = data.resultCode;
                        ex['statusText'] = data.resultMessage;

                        throw new Error(ex);
                    }
                } else if (xhr.status !== 200) {
                    ex['status'] = xhr.status;
                    ex['statusText'] = xhr.statusText;

                    throw new Error(ex);
                }
            } catch (ex) {
                reject(ex);
            }
        };
    });
}

async function getScenarioGroup(form) {
    return new Promise((resolve, reject) => {
        const sendData = new XMLHttpRequest();
        const url = `server/displayScenarioGroup.php`;

        sendData.open('POST', url);
        sendData.setRequestHeader('content-type', 'application/json');
        sendData.send(JSON.stringify(form));

        sendData.onreadystatechange = function (e) {
            const xhr = e.target;
            let ex = new Object();

            try {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.response);
                    const data = JSON.parse(xhr.response);

                    if (data.resultCode === 200) {
                        // const changeUrl = `frame/sendEachScen.php?cdDistObsv=${form.cdDistObsv}&page=${form.page}`;
                        const changeUrl = `frame/groupScenForm.php?cdDistObsv=${form.cdDistObsv}&page=${form.page}`;

                        // getlog("display", "frame/sendEachScen.php", data.logEvent, data.logName, data.logBefore, data.logAfter);
                        getlog('display', 'frame/groupScenForm.php', data.logEvent, data.logName, data.logBefore, data.logAfter);
                        resolve(changeUrl);
                    } else {
                        ex['status'] = data.resultCode;
                        ex['statusText'] = data.resultMessage;

                        throw new Error(ex);
                    }
                } else if (xhr.status !== 200) {
                    ex['status'] = xhr.status;
                    ex['statusText'] = xhr.statusText;

                    throw new Error(ex);
                }
            } catch (ex) {
                reject(ex);
            }
        };
    });
}

function getVideoC16(formData) {
    return new Promise((resolve, reject) => {
        const sendData = new XMLHttpRequest();
        const url = `server/fileUploadResult.php`;

        sendData.open('POST', url, true);
        sendData.send(formData);

        sendData.onreadystatechange = function (e) {
            const xhr = e.target;

            try {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    resolve(xhr.response);
                } else if (xhr.status !== 200) {
                    throw new Error(xhr.response);
                }
            } catch (ex) {
                reject(ex);
            }
        };
    });
}

function getVideo(formData) {
    return new Promise((resolve, reject) => {
        const sendData = new XMLHttpRequest();
        const url = `server/fileUploadResult.php`;

        sendData.open('POST', url, true);
        sendData.send(formData);

        sendData.onreadystatechange = function (e) {
            const xhr = e.target;

            try {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    resolve(xhr.response);
                } else if (xhr.status !== 200) {
                    throw new Error(xhr.response);
                }
            } catch (ex) {
                reject(ex);
            }
        };
    });
}

function displaySample(saveText, x, y) {
    let html = '';

    for (let i = 0; i < saveText.length; i++) {
        if (i % 2 == 0) {
            html += `<div class="displayBoard" style="width: ${x}px;">`;
        }

        html += `<div class="block">`;

        let splitText = saveText[i].split('#');

        for (let j = 0; j < splitText.length; j++) {
            let first = splitText[j].substr(0, 1);
            if (first == '1' || first == '2' || first == '3' || first == '4' || first == '5' || first == '6' || first == '7') {
                let text = splitText[j].substr(1);

                let color = '';
                if (first == '1') color = 'rgba(255,0,0,1.00)';
                else if (first == '2') color = 'rgba(0,255,0,1.00)';
                else if (first == '3') color = 'rgba(255,255,0,1.00)';
                else if (first == '4') color = 'rgba(0,0,255,1.00)';
                else if (first == '5') color = 'rgba(255,0,255,1.00)';
                else if (first == '6') color = 'rgba(0,255,255,1.00)';
                else if (first == '7') color = 'rgba(255,255,255,1.00)';

                html = html + `<span id="id_span" class="spanClass" style="color: ${color};">${text}</span>`;
            } else {
                html = html + `<span id="id_span" class="spanClass" style="color:rgba(255,0,0,1.00);">${splitText[j]}</span>`;
            }
        }

        html = html + '</div>';

        if (i % 2 == 1 || i == saveText.length) {
            html = html + '</div>';
        }
    }

    console.log('html:', html);

    $('.displayBox').empty().append(html);
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

function sendHttpScenario(data, endIp, endPort, chkEndType) {
    const xhr = new XMLHttpRequest();

    let ip;
    let port;

    if (chkEndType === 'end') {
        ip = endIp;
        port = endPort;
    } else {
        ip = document.getElementById('ip').value;
        port = document.getElementById('port').value;
    }

    const url = `http://${ip}:${port}/scenario`;

    console.log('sendHttpScenario : ', url);
    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            // 성공적으로 데이터 전송 완료
            console.log('[status 200] Data send successfully(Scenario)');

            let response = xhr.responseText;
            console.log('Server response(Scenario) :', response);
        } else {
            // 데이터 전송 실패
            console.error('Failed to send data');
        }
    };
    xhr.onerror = function () {
        console.error('Request failed');
    };
    xhr.send(JSON.stringify(data));
}

function sendHttpParams(data, endIp, endPort, chkEndType) {
    const xhr = new XMLHttpRequest();

    let ip;
    let port;

    if (chkEndType === 'end') {
        ip = endIp;
        port = endPort;
    } else {
        ip = document.getElementById('ip').value;
        port = document.getElementById('port').value;
    }

    const url = `http://${ip}:${port}/params`;

    console.log('sendHttpParams : ', url);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function () {
        if (xhr.status === 200) {
            console.log('[status 200] Data send successfully(Params)');

            let response = xhr.responseText;
            console.log('Server response(Params) : ', response);
        } else {
            console.error('Failed to send data!! Error code : ', xhr.status);
        }
    };
    xhr.onerror = function () {
        console.error('Request failed');
    };
    xhr.send(JSON.stringify(data));
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

    return `${year}${month}${day}${hours}${minutes}${seconds}`;
}
