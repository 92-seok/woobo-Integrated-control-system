<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';

// JSON 데이터를 PHP 배열로 변환합니다.
$inputJSON = file_get_contents('php://input');
$form = json_decode($inputJSON, true);

$viewType = $form['viewType'] ?? '';
$kind = $form['kind'] ?? '';
$c16FileNameView = $form['c16FileNameView'] ?? '';
$c16FileNameSend = $form['c16FileNameSend'] ?? '';
$c16FileNameVideo = $form['c16FileNameVideo'] ?? '';

// insert/update/delete/end
$type = $form['type'] ?? '';

$res = [];

try {
    $eqp_dao = new WB_EQUIP_DAO();
    $dis_dao = new WB_DISPLAY_DAO();
    $send_dao = new WB_DISSEND_DAO();

    // CD_DIST_OBSV
    $cdDistObsv = $form['cdDistObsv'];
    // DisCode
    $disCode = $form['disCode'] ?? '';

    // LOG 처리
    $eqp_vo = $eqp_dao->SINGLE("CD_DIST_OBSV = '{$cdDistObsv}'");
    if ($eqp_vo === false) {
        throw new Exception('Dont search cdDistObsv from DB');
    }

    $res['logName'] = $eqp_vo->NM_DIST_OBSV;
    $res['logBefore'] = '';
    $res['logAfter'] = '';

    if ($type == 'update' || $type == 'end') {
        $dis_vo = $dis_dao->SINGLE("DisCode = '{$disCode}'");
        if ($dis_vo !== false) {
            $res['logBefore'] = strip_tags($dis_vo->HtmlData);
        }
    } elseif ($type == 'delete') {
        $deleteMessage = [];

        foreach ($disCode as $code) {
            $dis_vo = $dis_dao->SINGLE("DisCode = '{$code}'");
            if ($dis_vo !== false) {
                array_push($deleteMessage, strip_tags($dis_vo->HtmlData));
            }

            $res['logBefore'] = join(',', $deleteMessage);
        }
    }

    $dis_vo = new WB_DISPLAY_VO();
    $send_vo = new WB_DISSEND_VO();
    $send_vo->CD_DIST_OBSV = $cdDistObsv;
    $send_vo->RCMD = 'D060';
    $send_vo->BStatus = 'start';
    $send_vo->RegDate = date('Y-m-d H:i:s');

    // End는 표출만 N으로 바꾸고 전광판에 알리기
    if ($type == 'end') {
        $dis_dao->Update("Exp_YN = 'N'", "DisCode = '{$disCode}'");
        $send_dao->INSERT($send_vo);

        $res['logEvent'] = 'Scenario End';
        $res['resultCode'] = 200;
        $res['resultMessage'] = 'Scenario End';
        echo json_encode($res);
        return;
    }

    // Update와 Delete 파일 지우기
    if ($type == 'update') {
        $dis_vo = $dis_dao->SINGLE("DisCode = '{$disCode}'");
        if ($dis_vo === false) {
            throw new Exception("Dont search row from DB ({$disCode})");
        }

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dis_vo->ViewImg)) {
            safe_unlink($_SERVER['DOCUMENT_ROOT'] . $dis_vo->ViewImg);
        }
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dis_vo->SendImg)) {
            safe_unlink($_SERVER['DOCUMENT_ROOT'] . $dis_vo->SendImg);
        }
    } elseif ($type == 'delete') {
        foreach ($disCode as $code) {
            $dis_vo = $dis_dao->SINGLE("DisCode = '{$code}'");
            if ($dis_vo === false) {
                throw new Exception("Dont search row from DB ({$code})");
            }

            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dis_vo->ViewImg)) {
                safe_unlink($_SERVER['DOCUMENT_ROOT'] . $dis_vo->ViewImg);
            }
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $dis_vo->SendImg)) {
                safe_unlink($_SERVER['DOCUMENT_ROOT'] . $dis_vo->SendImg);
            }
        }
    }

    // Delete Row 삭제 처리
    if ($type == 'delete') {
        $alertEquip = false;
        foreach ($disCode as $code) {
            $dis_vo = $dis_dao->SINGLE("DisCode = '{$code}'");
            $dis_dao->DELETE("DisCode = '{$code}'");

            if ($dis_vo->Exp_YN == 'Y') {
                $alertEquip = true;
            }
        }

        if ($alertEquip === true) {
            $send_dao->INSERT($send_vo);
        }

        $res['logEvent'] = 'Scenario Delete';
        $res['resultCode'] = 200;
        $res['resultMessage'] = 'Scenario Delete';
        echo json_encode($res);
        return;
    }

    // Insert와 Update object 만들기
    if ($type == 'insert' || $type == 'update') {
        $dis_vo->CD_DIST_OBSV = $cdDistObsv;
        $dis_vo->SaveType = 'local';
        $dis_vo->DisEffect = $form['disEffect'];
        $dis_vo->DisSpeed = $form['disSpeed'];
        $dis_vo->DisTime = (int) $form['disTime'];
        $dis_vo->EndEffect = $form['endEffect'];
        $dis_vo->EndSpeed = $form['endSpeed'];
        $dis_vo->StrTime = $form['strDate'];
        $dis_vo->EndTime = $form['endDate'];
        $dis_vo->Relay = (string) $form['relay'];
        $dis_vo->DisType = 'ad';
        $dis_vo->Exp_YN = 'Y';
        $dis_vo->RegDate = date('Y-m-d H:i:s');
    }

    //		// 저장 파일 종류
    //		$kind = $form["kind"];

    // Insert와 Update 파일 생성
    if ($type == 'insert' || $type == 'update') {
        $imgTag = $form['viewImg'];

        switch ($viewType) {
            case 'text':
                $dis_vo->ViewImg = "displayImage/{$cdDistObsv}_text_" . date('YmdHis') . '.png';
                $dis_vo->SendImg = $form['sendImg'];
                $dis_vo->HtmlData = $form['htmlData'];

                $dis_dao->base64toImage($imgTag, "../../{$dis_vo->ViewImg}");
                //$dis_dao->imageResize("../../{$dis_vo->ViewImg}", 1.6, 1);
                break;

            // c16전광판일때
            case 'c16':
                // image일경우
                if ($kind == 'image') {
                    // wb_display 에 저장
                    $dis_vo->ViewImg = "MC/{$c16FileNameView}";
                    $dis_vo->SendImg = "MC/{$c16FileNameSend}";
                    $dis_vo->HtmlData = $form['htmlData'];

                    // 파일 생성
                    $dis_dao->base64toImage($imgTag, "../../{$dis_vo->ViewImg}");
                    $dis_dao->base64toImage($imgTag, "../../{$dis_vo->SendImg}");

                    // 이미지 크기 줄이는부분 일단 주석처리함.
                    //$dis_dao->imageResize("../../{$dis_vo->SendImg}", 0.25, 0.25);

                    // video일 경우
                } elseif ($kind == 'video') {
                    $formTest = $form['isSave'];

                    if ($form['isSave'] == 'true') {
                        //$file = explode(".", $form["viewImg"]);

                        $dis_vo->ViewImg = "MC/{$c16FileNameVideo}";
                        $dis_vo->SendImg = "MC/{$c16FileNameVideo}";
                        //$dis_vo->SendImg = "displayImage/{$cdDistObsv}_video_".date("YmdHis").".{$file[1]}";

                        $dis_vo->HtmlData = $dis_vo->ViewImg;
                    } else {
                        throw new Exception('동영상이 저장되지 않았습니다.', 400);
                    }
                }

                break;

            case 'image':
                $dis_vo->ViewImg = "displayImage/{$cdDistObsv}_text_" . date('YmdHis') . '.png';
                $dis_vo->SendImg = "displayImage/{$cdDistObsv}_thumb_" . date('YmdHis') . '.png';
                $dis_vo->HtmlData = $form['htmlData'];

                $dis_dao->base64toImage($imgTag, "../../{$dis_vo->ViewImg}");
                $dis_dao->base64toImage($imgTag, "../../{$dis_vo->SendImg}");
                // $dis_dao->imageResize("../../{$dis_vo->SendImg}", 0.25, 0.25);
                $dis_dao->imageResize("../../{$dis_vo->SendImg}", 0.5, 0.5);
                break;

            case 'video':
                if ($form['isSave'] == 'true') {
                    $file = explode('.', $form['viewImg']);

                    $dis_vo->ViewImg = $form['viewImg'];
                    $dis_vo->SendImg = "displayImage/{$cdDistObsv}_video_" . date('YmdHis') . ".{$file[1]}";

                    $dis_vo->HtmlData = $dis_vo->ViewImg;
                } else {
                    throw new Exception('동영상이 저장되지 않았습니다.', 400);
                }
                break;

            default:
                throw new Exception('접근 형식이 잘못되었습니다.', 400);
        }
    }

    if ($type == 'insert') {
        $dis_dao->INSERT($dis_vo);
        $send_dao->INSERT($send_vo);

        $res['logEvent'] = 'Scenario Add';
    } elseif ($type == 'update') {
        $dis_dao->UPDATE("DisEffect = '{$dis_vo->DisEffect}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("DisSpeed = '{$dis_vo->DisSpeed}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("DisTime = '{$dis_vo->DisTime}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("EndEffect = '{$dis_vo->EndEffect}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("EndSpeed = '{$dis_vo->EndSpeed}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("StrTime = '{$dis_vo->StrTime}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("EndTime = '{$dis_vo->EndTime}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("Relay = '{$dis_vo->Relay}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("ViewImg = '{$dis_vo->ViewImg}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE("SendImg = '{$dis_vo->SendImg}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");
        $dis_dao->UPDATE(
            "HtmlData = '{$dis_vo->HtmlData}'                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  ",
            "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'"
        );
        $dis_dao->UPDATE("Exp_YN =  '{$dis_vo->Exp_YN}'", "DisCode = '{$disCode}' AND CD_DIST_OBSV = '{$cdDistObsv}'");

        $send_dao->INSERT($send_vo);
        $res['logEvent'] = 'Scenario Update';
    }

    $res['logAfter'] = strip_tags($dis_vo->HtmlData);
    $res['resultCode'] = 200;
    $res['resultMessage'] = 'update';
    echo json_encode($res);
    return;
} catch (Exception $ex) {
    $res['resultCode'] = $ex->getCode();
    $res['resultMessage'] = $ex->getMessage();
    echo json_encode($res);
    return;
}
