<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';

// JSON 데이터를 PHP 배열로 변환합니다.
$inputJSON = file_get_contents('php://input');
$form = json_decode($inputJSON, true);

$disCode = $form['disCode'] ?? '';

$displayment_dao = new WB_DISPLAYMENT_DAO();
// $displayment_vo = $displayment_dao->SELECT();
$displayment_vo = $displayment_dao->SELECT_SINGLE("DisCode = '{$disCode}'");
if ($displayment_vo === false) {
    return false;
}

$res = [];

$res['HtmlData'] = $displayment_vo->HtmlData;

// $res["logAfter"] = strip_tags($dis_vo->HtmlData);
$res['resultCode'] = 200;
$res['resultMessage'] = 'change';
echo json_encode($res);
return;

// $resultArray = array();
// $disstr = "";
// $strsub = "";

// $cSql = "select * from wb_displayment where disCode = {$data['scen']}";
// $cRes = mysqli_query( $conn, $cSql );
// $cRow = mysqli_fetch_array($cRes);

// $resultArray['HtmlData'] = $cRow["HtmlData"];
// $resultArray['code'] = "30";

// echo json_encode( $resultArray );
