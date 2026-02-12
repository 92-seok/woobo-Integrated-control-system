<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/const.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';

$type = $_GET['dType'] ?? 'normal';
// $disCode = $_GET["disCode"] ?? -1;
// $cdDistObsv = (string)$_GET["cdDistObsv"] ?? "";
// $page = $_GET["page"] ?? 1;
// $num = $_GET["num"] ?? 1;

$eqp_dao = new WB_EQUIP_DAO();
$dis_dao = new WB_DISPLAY_DAO();
$displayment_dao = new WB_DISPLAYMENT_DAO();

$eqp_vo = $eqp_dao->SELECT("GB_OBSV = '18' AND USE_YN IN ('1', 'Y')", 'SizeX DESC');
if (count($eqp_vo) == 1) {
    echo '<script>';
    echo "getFrame('frame/sendEachScen.php?cdDistObsv={$eqp_vo[0]->CD_DIST_OBSV}', pType, -1, 'true');";
    echo '</script>';
}

$dis_vo = new WB_DISPLAY_VO();
$dis_vo->DisEffect = '1';
$dis_vo->DisSpeed = '5';
$dis_vo->EndEffect = '1';
$dis_vo->EndSpeed = '5';
$dis_vo->Relay = '0';
$dis_vo->DisTime = '5';
$dis_vo->StrTime = date('Y-m-d H:i:s');
$dis_vo->EndTime = date('Y-m-d H:i:s', strtotime('+1 years'));
$dis_vo->HtmlData = '';

$displayment_vo = $displayment_dao->SELECT();

$now = date('Y-m-d H:i:s');

$startDate = date('Y-m-d', strtotime($dis_vo->StrTime));
$startHour = date('H', strtotime($dis_vo->StrTime));

// $endDate = date("Y-m-d", strtotime($dis_vo->EndTime));
if ($dis_vo->EndTime < $now) {
    $endDate = date('Y-m-d', strtotime('+1 years'));
} else {
    $endDate = date('Y-m-d', strtotime($dis_vo->EndTime));
}
$endHour = date('H', strtotime($dis_vo->EndTime));

/// TODO: hsyi 여러 크기의 전광판이 섞여있는 경우는 아직 고려하지 않음
$first_vo = $eqp_vo[0];

$viewType = 'text';
if ($first_vo->ConnModel == 'EWDISPLAY_LAN') {
    $viewType = 'text';
} elseif ($first_vo->ConnModel == 'EWDPL_LAN') {
    $viewType = 'image';
} else {
    echo "Unknown ConnModel {$viewType}";
}

/// 썸머노트 이미지 사이즈
// $sizeX = 320;
// $sizeY = 64;
$sizeX = $first_vo->SizeX;
$sizeY = $first_vo->SizeY;

/// TODO: hsyi "c16" sizeX x sizeY 별도 처리 필요한지 확인해야 함
$isVerticalImage = $first_vo->SizeX < $first_vo->SizeY ? true : false;
if ($isVerticalImage) {
    $sizeX = $first_vo->SizeX * 4;
    $sizeY = $first_vo->SizeY * 4;
} else {
    if ($first_vo->SizeX < 300) {
        $sizeX = $first_vo->SizeX * 3;
        $sizeY = $first_vo->SizeY * 3;
    } else {
        $sizeX = $first_vo->SizeX * 2;
        $sizeY = $first_vo->SizeY * 2;
    }
}
?>

<div class="cs_frame">
<div class="cs_container"> 
<div class="cs_displaybox">

    <div id='id_groupscenario'>
        <h4>◈ 전광판 선택</h4>
        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows">
            <tr align="center"> 
                <th width="3%"><input type='checkbox' name='allCheck' id='id_allCheck'></th>
                <?php if ($_SESSION['Auth'] === 0): ?>
                    <th class="cs_admin" width="5%">장비ID</th>
                    <th class="cs_admin" width="5%">구분</th>
                    <th class="cs_admin" width="15%">로거모델</th>
                <?php endif; ?>
                <th width="15%">장비명</th>
                <th width="15%">사이즈</th>
                <th>설치지역</th>
                <th width="15%">IP(Port)</th>
                <th width="15%">전원상태</th>
            </tr>
            <?php foreach ($eqp_vo as $vo) { ?>
            <tr>
                <th><input type='checkbox' class='cs_disChk' value='<?= $vo->CD_DIST_OBSV ?>'></th>
                <?php if ($_SESSION['Auth'] === 0): ?>
                    <td class="cs_admin"><?= $vo->CD_DIST_OBSV ?></td>
                    <td class="cs_admin"><?= id2gb($vo->GB_OBSV) ?></td>
                    <td class="cs_admin"><?= $vo->ConnModel ?></td>
                <?php endif; ?>
                <td><?= $vo->NM_DIST_OBSV ?></td>
                <td><?= $vo->SizeX . ' x ' . $vo->SizeY ?></td>
                <td><?= $vo->DTL_ADRES ?></td>
                <td><?= $vo->ConnIP . ' : ' . $vo->ConnPort ?></td>
                <td>
                    <?php if (strtolower($vo->LastStatus) == 'ok'): ?>
                        <span style='color:blue'>정상</span>
                    <?php else: ?>
                        <span style='color:red'>점검요망</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="">
        <form method="post" name="eachScen" action="" id="id_form" enctype="multipart/form-data">

            <!-- <input type="hidden" name="type" id="type" value="group"> -->
            <input type="hidden" name="type" id="type" value="<?= $type ?>">
            <input type="hidden" name="cdDistObsv" value="">
            <input type="hidden" name="viewType" id="viewType" value=<?= $viewType ?>>
            <input type="hidden" name="sizeX" id="sizeX" value="<?= $sizeX ?>">
            <input type="hidden" name="sizeY" id="sizeY" value="<?= $sizeY ?>">

            <div id="">
                <h4 style="margin-top:20px;">◈ 그룹 시나리오 설정</h4>
                <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                    <tr id='trList0'>
                        <th width='16%'>정보</th>
                        <td colspan='3'>
                            <select id='kind' name='kind' disabled>
                                <option value='image' selected>이미지</option>
                                <option value='video' hidden disabled>동영상</option>
                            </select>
                        </td>
                    </tr>
                    <tr id="trList1">
                        <th width="16%">표시효과</th>
                        <td width="34%">
                            <select name="disEffect" id="disEffect">
                                <option value="1" selected>즉시 표시</option>
                                <option value="2">좌측으로 스크롤</option>
                                <option value="3">위로 스크롤</option>
                                <option value="4">아래로 스크롤</option>
                                <option value="5">레이저 효과</option>
                                <option value="6">중심에서 상하로 벌어짐</option>
                                <option value="7">상하에서 중심으로 모여듬</option>
                                <option value="8">1단으로 좌측스크롤</option>
                            </select>
                        </td>
                        <th width="16%">표시속도</th>
                        <td width="34%">
                            <select name="disSpeed" id="disSpeed" style="width:45%;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5" selected>5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>

                            <div class="info" style="display: inline-block;">&nbsp;※ 1(빠름) ~ 8(느림)</div>
                        </td>
                    </tr>
                    <tr id="trList2">
                        <th>완료효과</th>
                        <td>
                            <select name="endEffect" id="endEffect">
                                <option value="1" selected>위로 스크롤</option>
                                <option value="2">아래로 스크롤</option>
                                <option value="3">위아래로 벌어짐</option>
                                <option value="4">중심으로 모여듬</option>
                                <option value="5">즉시 사라짐</option>
                                <option value="6">화면반전</option>
                                <option value="7">좌측으로 사라짐</option>
                            </select>
                        </td>
                        <th>완료속도</th>
                        <td>
                            <select name="endSpeed" id="endSpeed" style="width:45%;">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5" selected>5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                            </select>
                            <div class="info" style="display: inline-block;">&nbsp;※ 1(빠름) ~ 8(느림)</div>
                        </td>
                    </tr>
                    <tr id="trList3">
                        <th>표시유지시간</th>
                        <td>
                            <select name="disTime" id="disTime" style="width:20%;">
                                <?php for ($i = 1; $i <= 20; $i++) {
                                    echo "<option value='{$i}' " . ($dis_vo->DisTime == $i ? 'selected' : '') . ">{$i}</option>";
                                } ?>
                            </select> 초
                        </td>
                        <th>릴레이 동작여부</th>
                        <td>
                            <div class="cs_container" style="font-size: 13px;">
                                <div>1번</div>
                                <input type="checkbox" class="cs_relay" value="8" style="margin-right: 25px;zoom: 1.2;">
                                <div>2번</div>
                                <input type="checkbox" class="cs_relay" value="4" style="margin-right: 25px;zoom: 1.2;">
                                <div>3번</div>
                                <input type="checkbox" class="cs_relay" value="2" style="margin-right: 25px;zoom: 1.2;">
                                <div>4번</div>
                                <input type="checkbox" class="cs_relay" value="1">
                            </div>
                        </td>
                    </tr>
                    <tr id="trList4">
                        <th>표시시간</th>
                        <td>
                            <input type="text" id="startDate" value="<?= $startDate ?>" style="width:100px;">
                            <select id="startHour" style="width:10%; text-align:center; text-indent:0px;">
                                <?php for ($i = 0; $i < 24; $i++) {
                                    $pi = $i < 10 ? "0{$i}" : $i;
                                    echo "<option value='{$pi}'" . ($startHour == $pi ? 'selected' : '') . ">{$pi}</option>";
                                } ?>
                            </select>시
                        </td>
                        <th>완료시간</th>
                        <td>
                            <input type="text" id="endDate" value="<?= $endDate ?>" style="width:100px;">
                            <select id="endHour" style="width:10%; text-align:center; text-indent:0px;">
                                <?php for ($i = 0; $i < 24; $i++) {
                                    $pi = $i < 10 ? "0{$i}" : $i;
                                    echo "<option value='{$pi}'" . ($endHour == $pi ? 'selected' : '') . ">{$pi}</option>";
                                } ?>
                            </select> 시
                        </td>
                    </tr>
                </table>
            </div>

            <div class="" id="container">
                <h4 style="margin-top:20px; margin-bottom:10px;">◈ 시나리오 내용</h4>

                <table id="id_ment" border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                    <tbody>
                        <tr>
                            <th width="16%">멘트유형</th>
                            <td width="32%">
                                <select name="menttype" id="menttype" style="text-indent:0px; width: 70%;">
                                    <option value="1" selected>직접입력</option>
                                    <option value="2">지정문구</option>
                                </select>
                            </td>
                            <th width="16%">멘트종류</th>
                            <td width="32%">
                                <select name="mentList" id="mentList" style="width: 70%; text-indent:0px;" disabled>
                                    <option value="0" disabled selected>선택</option>
                                    <?php foreach ($displayment_vo as $v) {
                                        echo "<option value = '{$v->disCode}'> {$v->Title} </option>";
                                    } ?>
                                </select>    
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div id="image" class="cs_displaybox" style="float: left; margin: 0 auto">
                    <div style="float: none; height: 70px; line-height: 70px;">
                        <h3>
                            전광판 그룹에 들어갈 글자를 아래 에디터에 입력하세요
                        </h3>
                    </div>
                    <textarea name="summernote" id="id_summernote"><?= $dis_vo->HtmlData ?></textarea>
                    <textarea name="imageTag" id="id_imageTag" style="display:none;"></textarea>
                </div> <!-- image -->
                <div id="image2" style="float: left; padding: 15px;">
                    <div style="float: none; height: 70px; line-height: 70px;">
                        <span style="display: inline-block; vertical-align: middle; line-height: 18px;">
                            빨간색 영역이 전광판 이미지로 저장됩니다<br />
                            영역을 벗어난 부분은 잘려서 저장됩니다<br />
                            (이미지 크기: <?= $sizeX ?> x <?= $sizeY ?>)<br />
                        </span>
                    </div>
                    <div style="float: left; border: 5px solid red; width: <?= $sizeX ?>px; height: <?= $sizeY ?>px; padding: 0px;">
                        <div class="" name="summernote_img" id="id_summernote_img"
                            style="float: left; border: 0; color: white; background-color: black; width: <?= $sizeX ?>px; height: <?= $sizeY ?>px;">
                            썸머노트이미지
                        </div>
                    </div>
                </div> <!-- 썸머노트이미지 -->

                <div id="video">
                    <input type='file' name='file' id='imageFileOpen' accep='video/*'><br/>
                    <?php if ($type == 'update' && $viewType == 'video'): ?>
                        <video height="300" width="400" controls><source src="../../<?= $dis_vo->ViewImg ?>"></video>
                    <?php endif; ?>
                </div>

                <div id='text'>
                    <div class="colorBtnHelpText">아래 색상 버튼을 먼저 클릭하신 후, 전광판에 들어갈 내용을 입력해주세요</div>
                    <div class="colorBtn">
                        <div class="color color1 btn-3d red" data-color="#1">#1</div>
                        <div class="color color2 btn-3d green" data-color="#2">#2</div>
                        <div class="color color3 btn-3d yellow" data-color="#3">#3</div>
                        <div class="color color4 btn-3d blue" data-color="#4">#4</div>
                        <div class="color color5 btn-3d purple" data-color="#5">#5</div>
                        <div class="color color6 btn-3d cyan" data-color="#6">#6</div>
                        <div class="color color7 btn-3d white" data-color="#7">#7</div>
                    </div>
                    <div class='addMent'>
                        <div class="addingMent">
                            <table border="0" cellpadding="0" cellspacing="0" class="displayTable" rules="all">
                            </table>
                        </div>
                        <table border="0" cellpadding="0" cellspacing="0" class="displayTable" id="displayAddTable" rules="all">
                            <tr>
                                <td>
                                    <input type="text" class="displayMentInput" id="addText" value="" maxlength="30">
                                </td>
                                <td>
                                    <div id="id_addMentBtn" class="addMentBtn" data-x="<?= $sizeX ?>" data-y="<?= $sizeY ?>">+ 전광판 내용 입력</div>
                                </td>
                            </tr>
                        </table>
                        <div class="byte"><span>0</span> / 20 Byte</div>
                    </div>
                    <div class="displayBoxBorder">
                        <div class="displayBox" style="width: <?= $sizeX ?>px; height: <?= $sizeY ?>px;">
                            <?= $dis_vo->HtmlData ?>
                        </div>
                    </div>
                    <div style="display:none;">
                        <textarea id="saveText"><?= $dis_vo->SendImg ?></textarea>
                        <textarea id="textNum"></textarea>
                        <input type="hidden" id="checkCount" value=0>
                    </div>
                </div> <!-- text -->
            </div> <!-- container -->
        </form>
    </div>

    <div class='cs_btnBox'>
        <div class="cs_btn" name="id_savGroupScen" id="id_savGroupScen">일괄 전송</div>
        <div class="cs_btn" name="id_endGroupScen" id="id_endGroupScen">일괄 종료</div>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            ◈ 전광판 선택<br/>
            &nbsp;- 일괄적으로 시나리오를 전송할 전광판(들)을 선택합니다.<br/><br/>

            ◈ 시나리오 설정<br/><br/>
            &nbsp;<font class="cs_helpIcon">●</font> 표시/완료효과<br/>
            &nbsp;&nbsp;- 전광판에 문구가 표출/종료될 때의 효과를 설정합니다.<br/>
            &nbsp;<font class="cs_helpIcon">●</font> 표시/완료속도<br/>
            &nbsp;&nbsp;- 전광판에 문구가 표출/종료되는 속도입니다.<br/>
            &nbsp;&nbsp;- 속도가 1에 가까울수록 문구가 빠르게 표시되거나, 사라집니다.<br/>
            &nbsp;<font class="cs_helpIcon">●</font> 표시/완료시간<br/>
            &nbsp;&nbsp;- 전광판에 문구를 표출/종료할 기간을 설정합니다.<br/>
            &nbsp;<font class="cs_helpIcon">●</font> 표시유지시간<br/>
            &nbsp;&nbsp;- 전광판에 문구가 유지되는 시간입니다.<br/>
            &nbsp;&nbsp;- 최대 20초까지 설정할 수 있습니다.<br/>
            &nbsp;<font class="cs_helpIcon">●</font> 릴레이 동작여부<br/>
            &nbsp;&nbsp;- 시나리오 표출시, 전광판에 연결된 장비들을 같이 동작시킬 수 있습니다.<br/><br/>

            ◈ 시나리오 내용<br/>
            &nbsp;- 글씨 색상, 정렬, 글씨체, 글씨 크기, 진하기를 결정한 후, 전광판에 전송할 내용을 입력합니다.<br/><br/>

            &nbsp;- 전송된 시나리오는 ‘표출중 시나리오 리스트’에서 확인할 수 있습니다.
        </div>
    </div>

</div>
</div>
</div> <!-- cs_frame -->

<script async src="/js/summernote-lite.min.js"></script>

<script>

$(document).ready(function (e) {

    console.log("ready groupScenForm");

    const viewType = document.querySelector("#viewType").value;
    const type = document.querySelector("#type").value;
    const kind = document.querySelector("#kind");
    const updateText = document.querySelector("#saveText").value;
    const elImage = document.querySelector("#imageFileOpen");

    if (updateText.length > 0) {
        updateContents(updateText);
    }

    if (kind !== null) {
        kind.addEventListener("change", (e) => {
            selectBox(type, viewType);
        });
    }

    // 동영상 파일 유무, 파일 사이즈 확인
    // const elImage = document.querySelector("#imageFileOpen");
    elImage.addEventListener("change", (evt) => {
        // 파일이 선택되었는지 확인
        if (evt.target.files.length > 0) {
            const image = evt.target.files[0];
            if (!validImageType(image)) {
                alert("동영상 파일만 올려주세요.");
                $("#imageFileOpen").val("");
                return;
            } else {
                // if (evt.target.files && evt.target.files[0].size > (100 * 1024 * 1024)) {
                if (image.size > (100 * 1024 * 1024)) { // 100MB 이상인 경우
                    alert("파일 사이즈가 100MB를 넘습니다.");
                    $("#imageFileOpen").val("");
                }
            }
        } else {
            // 파일이 선택되지 않았을 때의 처리
            console.log("파일이 선택되지 않았습니다.");
        }
    });

    $("#startDate").datepicker(
        {
            dayNames       : ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'],
            dayNamesMin    : ['월', '화', '수', '목', '금', '토', '일'],
            monthNamesShort: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            monthNames     : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            dateFormat     : "yy-mm-dd"
        }
    );

    $("#endDate").datepicker(
        {
            dayNames       : ['월요일', '화요일', '수요일', '목요일', '금요일', '토요일', '일요일'],
            dayNamesMin    : ['월', '화', '수', '목', '금', '토', '일'],
            monthNamesShort: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'],
            monthNames     : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
            dateFormat     : "yy-mm-dd"
        }
    );

    $('#id_summernote').summernote({
        disableResizeEditor: false,
        // dialogsInBody: true,
        // dialogsFade: true,
        // width: <?= $sizeX * 2 ?>,
        // height: <?= $sizeY * 2 ?>,
        width: 426,
        height: 240,
        minHeight: null,
        maxHeight: null,
        toolbar: [
            ['color', ['forecolor']],
            ['para', ['paragraph']],
            ['font', ['fontname', 'fontsize', 'bold']],
            ['height', ['height']],
        ],
        fontNames: ['sans-serif', 'Arial', 'NanumGothic', 'NanumSquare'],
        fontSizes: ['5','10','15','20','30','35','40','60','70','80','100'],
        lineHeights: ['1.0', '1.2', '1.3'],
        callbacks: {
            onInit: function() {
                console.log('Summernote is launched');
            },
            onChange: function(contents, $editable) {
                const summernote = document.querySelector("#id_summernote");
                const summernote_img = document.querySelector("#id_summernote_img");
                var textareaValue1 = $('#id_summernote').summernote('code');
                summernote_img.innerHTML = contents;
            }
        }
    });

    $("#id_summernote").summernote('fontSize', 40);
    $('#id_summernote').summernote('backColor', 'black');
    $("#id_summernote").summernote('foreColor', '#ffffff');
    $("#id_summernote").summernote('lineHeight', 1.3);

    $("#menttype").on("change", function(){
        if (document.querySelector("#menttype").value == "1") {
            document.querySelector("#mentList").disabled = true;
            document.querySelector("#mentList").options[0].selected = true; 
        } else {
            document.querySelector("#mentList").disabled = false;
        }
    });

    selectBox(type, viewType);

    console.log("mount groupScenForm");
});


function selectBox(type, viewType) {
    let kind = document.querySelector("#kind");

    let tr0 = document.querySelector("#trList0");
    let tr1 = document.querySelector("#trList1");
    let tr2 = document.querySelector("#trList2");
    let tr3 = document.querySelector("#trList3");
    let tr4 = document.querySelector("#trList4");

    let image = document.querySelector("#image");
    let image2 = document.querySelector("#image2");
    let video = document.querySelector("#video");
    let text = document.querySelector("#text");
    let ment = document.querySelector("#id_ment");

    if (kind !== null) {
        tr0.style.display = "none";
    }
    tr1.style.display = "none";
    tr2.style.display = "none";
    tr3.style.display = "none";
    tr4.style.display = "none";

    image.style.display = "none";
    image2.style.display = "none";
    video.style.display = "none";
    text.style.display = "none";
    // ment.style.display = "none";

    if (type !== "update" && viewType !== "text" && viewType !== "c16") {
        if (kind !== null) {
            tr0.style.display = "table-row";
            viewType = kind.value;
        } else if (kind === null) {
            viewType = "image";
        }
    }

    console.log("viewType:", viewType);

    switch (viewType) {

        case "image":
            if (kind !== null) kind.selectIndex = 0;
            tr1.style.display = "table-row";
            tr2.style.display = "table-row";
            tr3.style.display = "table-row";
            tr4.style.display = "table-row";

            image.style.display = "block";
            image2.style.display = "block";

            break;

        case "video":
            kind.selectIndex = 1;
            video.style.display = "block";
            break;

        case "text":
            text.style.display = "block";
            break;
    }
}

function validImageType(image) {
    if (!image) return false;
    return (['video/mp4', 'video/x-ms-wmv', 'video/avi', 'video/mov', 'video/mkv', 'video/flv', 'video/ogg', 'video/webm', 'video/ogv'].indexOf(image.type) > -1);
}

function updateContents(textAreaInnerText) {
    let sT = document.querySelector("#saveText");
    let tN = document.querySelector("#textNum");

    let saveText = textAreaInnerText.split("|");
    let textNum = new Array();

    let checkCount = 0;

    saveText.forEach((text) => {
        let html = "";
        html += `<tr id='subTr${checkCount}'>`;
        html += `<td>`;
        html += `<div class="textMent">${text}</div>`;
        html += `</td>`;
        html += `<td><div class="subMentBtn" data-id="${checkCount}">- 전광판 내용 삭제 ${checkCount + 1}</div></td>`;
        html += '</tr>';

        textNum.push(checkCount++);

        $(".addingMent .displayTable").append(html);
    })

    displaySample(saveText);

    // if (saveText.length > 0) {
    //     document.querySelector("#displayAddTable").style.display = "none";
    // }

    // $(".displayBox").empty().append(html);
    // document.querySelector(".displayBox").innerHTML;

    let $displayBox = document.querySelector(".displayBox");
    $displayBox.style.width = "<?= $sizeX ?>px";
    $displayBox.style.height = "<?= $sizeY ?>px";
    console.log("displayBox:", $displayBox);

    let $displayBoard = document.querySelector(".displayBoard");
    $displayBoard.style.width = "<?= $sizeX ?>px";
    $displayBoard.style.height = "<?= $sizeY ?>px";
    console.log("displayBoard:", $displayBoard);

    let $span = document.querySelector("#id_span");
    $span.style.width = "<?= $sizeX ?>px";
    $span.style.height = "<?= $sizeY ?>px";
    console.log("span:", $span);

    sT.value = saveText.join('|');
    tN.value = textNum.join('|');
}

</script>
