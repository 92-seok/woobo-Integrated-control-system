<?php
$data = json_decode(file_get_contents('php://input'), true);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';

$disDao = new WB_DISPLAY_DAO();
$disVo = new WB_DISPLAY_VO();

$mentDao = new WB_ISUMENT_DAO();
$mentVo = new WB_ISUMENT_VO();

$result = [];

try {
    $type = $data['type'];
    $level = $data['level'];

    switch ($type) {
        case 'select':
            $disCode = $data['disCode'];
            $disVo = $disDao->SINGLE("DisCode = '{$disCode}'");
            if ($disVo === null) {
                throw new Exception("Dont search row from DB ({$disCode})", 400);
            }
            break;

        case 'update':
        case 'delete':
            $disCode = $data['disCode'];
            $disVo = $disDao->SINGLE("DisCode = '{$disCode}'");

            if ($disVo === false) {
                throw new Exception("Dont search row from DB ({$disCode})", 400);
            }

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $disVo->ViewImg)) {
                safe_unlink($_SERVER['DOCUMENT_ROOT'] . $disVo->ViewImg);
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $disVo->SendImg)) {
                safe_unlink($_SERVER['DOCUMENT_ROOT'] . $disVo->SendImg);
            }

            $mentVo = $mentDao->SINGLE();
            $disMentLevel = $mentVo->{"DisMent{$level}"};
            $disCodes = !empty($disMentLevel) ? explode(',', $mentVo->{"DisMent{$level}"}) : [];
            $newDisCodes = [];
            foreach ($disCodes as $dc) {
                if ($dc != $disCode) {
                    array_push($newDisCodes, $dc);
                }
            }
            $newDisCodeStr = implode(',', $newDisCodes);
            // $mentDao->Update("DisMent{$level} = '{$newDisCodeStr}'", 'MentCode = 1');
            $mentDao->Update("DisMent{$level} = '{$newDisCodeStr}'", '1=1');

            $disDao->DELETE("DisCode = '{$disCode}'");
            if ($type == 'delete') {
                break;
            }

        case 'insert':
            $disVo = new WB_DISPLAY_VO();
            $disVo->CD_DIST_OBSV = 0;
            $disVo->SaveType = 'local';
            $disVo->DisEffect = '1';
            $disVo->DisSpeed = '5';
            $disVo->DisTime = 5;
            $disVo->EndEffect = '1';
            $disVo->EndSpeed = '5';
            $disVo->StrTime = date('Y-m-d H:i:s');
            $disVo->EndTime = date('Y-m-d H:i:s', strtotime('+1 years'));
            $disVo->Relay = '0';
            $disVo->ViewImg = 'displayImage/0_text_' . date('YmdHis') . '.png';
            $disVo->SendImg = $data['sendImg'];
            $disVo->HtmlData = $data['htmlData'];
            $disVo->DisType = 'emg';
            $disVo->Exp_YN = 'Y';
            $disVo->RegDate = $disVo->StrTime;

            $imgTag = $data['viewImg'];
            $disDao->base64toImage($imgTag, "../../{$disVo->ViewImg}");
            $disDao->imageResize("../../{$disVo->ViewImg}", 1.6, 1);
            $disDao->INSERT($disVo);
            $insertId = $disDao->INSERTID();

            $mentVo = $mentDao->SINGLE();
            $disMentLevel = $mentVo->{"DisMent{$level}"};
            $disCodes = !empty($disMentLevel) ? explode(',', $mentVo->{"DisMent{$level}"}) : [];
            array_push($disCodes, $insertId);
            $newDisCodeStr = implode(',', $disCodes);
            // $mentDao->Update("DisMent{$level} = '{$newDisCodeStr}'", 'MentCode = 1');
            $mentDao->Update("DisMent{$level} = '{$newDisCodeStr}'", '1=1');
            break;
    }

    $result['resultCode'] = 200;
    $result['body'] = $disVo->SendImg;
    echo json_encode($result);
    return;
} catch (Exception $ex) {
    $result['resultCode'] = $ex->getCode();
    $result['body'] = $ex->getMessage();
    echo json_encode($result);
    return;
}
