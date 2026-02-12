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

/* 2021.10.06 추가 by Park Jong-Sung */
.overlay {
    position: absolute;
    /* Sit on top of the page content */
    display: none;
    /* Hidden by default */
    width: 100%;
    /* Full width (cover the whole page) */
    height: 100%;
    /* Full height (cover the whole page) */
    top: 0;
    left: 0;
    background-color: rgba(0, 0, 0, 0.5);
    /* Black background with opacity */
    z-index: 2;
    /* Specify a stack order in case you're using a different order for other elements */
    cursor: default;
    /* Add a pointer on hover */
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
                        } ?>" id="id_levelBtn" data-num="1">1단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 2) {
                            echo 'select';
                        } ?>" id="id_levelBtn" data-num="2">2단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 3) {
                            echo 'select';
                        } ?>" id="id_levelBtn" data-num="3">3단계</div>
                    </td>
                    <td width="16%" style="border: none;">
                        <div class="cs_btn <?php if ($level == 4) {
                            echo 'select';
                        } ?>" id="id_levelBtn" data-num="4">4단계</div>
                    </td>
                </tr>
            </table>

            <table id="1" border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all">
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">사이즈</th>
                    <?php if ($_SESSION['Auth'] === 0): ?>
                    <th class="cs_admin" width="10%">전광판ID</th>
                    <th class="cs_admin" width="10%">장비ID</th>
                    <?php endif; ?>
                    <th>내용</th>
                    <th width="15%">시나리오 종료</th>
                </tr>
                <?php
                $mentVo = $mentDao->SELECT();
                $disMentLevel = $mentVo->{"DisMent{$level}"};
                if (!empty($disMentLevel)) {
                    $disList = explode(',', $disMentLevel);
                    $count = 1;
                    $n = '';

                    foreach ($disList as $disCode) {

                        $disVo = $disDao->SELECT_SINGLE("DisCode = '{$disCode}'");
                        if ($disVo->CD_DIST_OBSV === null) {
                            continue;
                        }
                        if ($disVo->SendImg === null) {
                            continue;
                        }

                        if (strpos($disVo->SendImg, 'displayImage/') === false) {
                            // only text, not image
                            continue;
                        }

                        $filename = '../../' . $disVo->SendImg;
                        if (!file_exists($filename)) {
                            echo $filename . ' 파일이 존재하지 않습니다';
                            continue;
                        }

                        $size = getimagesize($filename);
                        if ($size === false) {
                            echo $filename . ' 비정상적인 이미지 파일입니다';
                            continue;
                        }
                        $image_width = $size[0];
                        $image_height = $size[1];
                        ?>
                <tr align='center'>
                    <td><?= $count ?></td>
                    <td> 320 x 64 </td>
                    <?php if ($_SESSION['Auth'] === 0): ?>
                    <td class="cs_admin"><?= $disVo->DisCode ?></td>
                    <td class="cs_admin"><?= $disVo->CD_DIST_OBSV ?></td>
                    <?php endif; ?>
                    <td>
                        <img alt='이미지를 찾을 수 없습니다.' src='<?= '/' . $disVo->SendImg ?>' id='id_disBtn' data-type='select'
                            title='코멘트 변경' value='<?= $disVo->DisCode ?>' style='cursor:pointer;object-fit:scale-down;'>
                    </td>
                    <td>
                        <div class="cs_btn deleteBtn_<?= $disVo->DisCode ?>" id="id_disBtn"
                            value="<?= $disVo->DisCode ?>" data-type="delete" style="width:80%;">삭제</div>
                    </td>
                </tr>
                <?php
                    } // for disList
                }

// if disMentLevel
?>
            </table>

            <div class="blank" style="padding-bottom: 50px;"></div>

            <form action="" method="post" id="id_form">
                <div style="margin-top:15px;">◈ 시나리오 내용</div>
                <div>에디터에 입력한 결과가 빨간색 테두리 안의 이미지 파일로 저장됩니다</div>
                <div>영역을 벗어난 부분은 저장되지 않습니다 (경보전광판관리)</div>

                <div class="cs_frame" style="font-size: 12px;">
                    <textarea name="summernote" id="id_summernote"></textarea>
                    <textarea name="imageTag" id="id_imageTag" style="display:none;"></textarea>
                </div>

                <input type="hidden" name="level" value="<?= $level ?>">

                <div style="float: left; width: 50%; height: 50%; padding: 10px;">
                    <div style="border: 5px solid red; width: <?= 320 * 2 ?>px; height: <?= 64 * 2 ?>px; padding: 0px;">
                        <div class="" name="summernote_img" id="id_summernote_img"
                            style="float: left; border: 0; color: white; background-color: black; width: <?= 320 * 2 ?>px; height: <?= 64 * 2 ?>px;">
                            썸머노트이미지
                        </div>
                    </div>
                </div>
            </form>

            <div class="cs_btnBox" style="margin-top:20px;">
                <div class="cs_btn updateBtn" id="id_disBtn" style="display:none;" data-type="update" value="">코멘트 수정
                </div>
                <div class="cs_btn insertBtn" id="id_disBtn" data-type="insert" value="0">코멘트 추가</div>
            </div>

            <div id="id_helpForm">
                <div id="id_help" stat="close">
                    <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
                    <div id='id_helpMessage'> 도움말 보기</div>
                </div>
                <div class='cs_help'>
                    - 단계별 경보발령시, 전광판에 표출될 문구입니다.<br />
                    - [1단계], [2단계], [3단계], [4단계]를 클릭하여 문구를 추가/삭제할 수 있습니다.<br /><br />

                    ◈ 시나리오 내용<br />
                    &nbsp;- 글씨 색상, 정렬, 글씨체, 글씨 크기, 진하기를 결정한 후, 전광판에 전송할 내용을 입력합니다.<br />
                </div>
            </div>

        </div>
    </div>
</div>

<script async src="/js/summernote-lite.min.js"></script>
<script>
$(document).ready(function(e) {
    $('#id_summernote').summernote({
        disableResizeEditor: false,
        // width: <?= 320 * 2 + 2 ?>,
        // height: <?= 64 * 2 ?>,
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
        fontSizes: ['5', '10', '15', '20', '30', '35', '40', '60', '70', '80', '100'],
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

    $('#id_summernote').on('summernote.change', function(we, contents, $editable) {
        console.log('summernote.change', we, contents, $editable);
    });

    $("#id_summernote").summernote('fontSize', 44);
    $('#id_summernote').summernote('backColor', 'black');
    $("#id_summernote").summernote('foreColor', '#ffffff');
    $("#id_summernote").summernote('lineHeight', 1.0);

});
</script>