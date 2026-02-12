<?php
header('Content-Type: application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/include/db/dao/dao.php';

$equip_dao = new WB_EQUIP_DAO();
$equip_vo = $equip_dao->SELECT("USE_YN IN ('1', 'Y')");

$data_dao = new WB_DATA_DAO();
$now = new DateTime();

$mk = [];
$count = 0;
$h = intval($now->format('H'));

// ini_set('display_errors', 1);

foreach ($equip_vo as $vo) {
    try {
        if ($vo->LAT === '' || $vo->LON == '') {
            continue;
        }

        $addEquip = [];

        $addEquip['cdDistObsv'] = $vo->CD_DIST_OBSV;
        $addEquip['nmDistObsv'] = $vo->NM_DIST_OBSV;
        $addEquip['gbObsv'] = $vo->GB_OBSV;
        $addEquip['lat'] = $vo->LAT;
        $addEquip['lon'] = $vo->LON;

        $dto = new WB_DATA_VO();
        $dto->cdDistObsv = $vo->CD_DIST_OBSV;
        $dto->regDate = $now;

        switch ($vo->GB_OBSV) {
            case '01':
                $addEquip['color'] = '#42569d';
                $addEquip['ImageFile'] = '/image/rainMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'rain';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['today'] = $dataVo->sum;
                    $addEquip['now'] = $dataVo->data[$h];
                } else {
                    $addEquip['today'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '02':
                $addEquip['color'] = '#329fe0';
                $addEquip['ImageFile'] = '/image/waterMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'water';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['today'] = number_format($dataVo->max / 1000, 2);
                    $addEquip['now'] = number_format($dataVo->data[$h] / 1000, 2);
                } else {
                    $addEquip['today'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '03':
                $addEquip['color'] = '#a5614a';
                $addEquip['ImageFile'] = '/image/dplaceMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';
                $addEquip['subOBCount'] = $vo->SubOBCount;
                $addEquip['today'] = [];
                $dto->type = 'dplace';

                for ($i = 1; $i <= intval($vo->SubOBCount); $i++) {
                    $dto->subObsv = $i;
                    $dataVo = $data_dao->SELECT1DAY($dto);
                    if ($dataVo) {
                        $addEquip['today'][$i] = $dataVo->data[$h];
                    } else {
                        $addEquip['today'] = null;
                    }
                }

                array_push($mk, $addEquip);
                break;

            case '04':
                $addEquip['color'] = '#a5614a';
                $addEquip['ImageFile'] = '/image/dplaceMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'soil';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['today'] = $dataVo->max;
                    $addEquip['now'] = $dataVo->data[$h];
                } else {
                    $addEquip['today'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '06':
                $addEquip['color'] = '#8643ae';
                $addEquip['ImageFile'] = '/image/snowMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'snow';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $newSnow = $dto->data[$h] - $dto->data[0];
                    if ($newSnow < 0) {
                        $newSnow = 0;
                    }
                    $addEquip['today'] = number_format($dto->max / 10, 1);
                    $addEquip['now'] = number_format($dto->data[$h] / 10, 1);
                    $addEquip['newSnow'] = number_format($newSnow / 10, 1);
                } else {
                    $addEquip['today'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '08':
                $addEquip['color'] = '#a5614a';
                $addEquip['ImageFile'] = '/image/dplaceMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'tilt';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['today'] = $dataVo->max;
                    $addEquip['now'] = $dataVo->data[$h];
                } else {
                    $addEquip['today'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '21':
                $addEquip['color'] = '#f94045';
                $addEquip['ImageFile'] = '/image/floodMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $dto->type = 'flood';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['flood'] = $dataVo->data[$h];
                } else {
                    $addEquip['flood'] = null;
                }

                $dto->type = 'water';
                $dataVo = $data_dao->SELECT1DAY($dto);
                if ($dataVo) {
                    $addEquip['water'] = $dataVo->data[$h];
                } else {
                    $addEquip['water'] = null;
                }

                array_push($mk, $addEquip);
                break;

            case '17':
                $addEquip['color'] = '#f3732c';
                $addEquip['ImageFile'] = '/image/broadMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';
                array_push($mk, $addEquip);
                break;

            case '18':
                $addEquip['color'] = '#ffb200';
                $addEquip['ImageFile'] = '/image/displayMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $disStatusDao = new WB_DISSTATUS_DAO();
                $disStatusVo = $disStatusDao->SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");
                if ($disStatusVo !== false) {
                    $disLastDate = new DateTime($disStatusVo->LastDate);
                    $now = new DateTime();
                    $diffLastDate = $now->getTimestamp() - $disLastDate->getTimestamp();
                    // 30분 = 1800초
                    if ($diffLastDate <= 1800) {
                        $disExpStatus = $disStatusVo->ExpStatus;
                        if ($disExpStatus === 'emg') {
                            $addEquip['mode'] = '긴급';
                        } elseif ($disExpStatus === 'ad') {
                            $addEquip['mode'] = '평시';
                        } else {
                            $addEquip['mode'] = '정상';
                        }
                    } else {
                        $addEquip['mode'] = '-';
                    }
                } else {
                    $addEquip['mode'] = 'N/A';
                }

                array_push($mk, $addEquip);
                break;

            case '20':
                $addEquip['color'] = '#e66ba1';
                $addEquip['ImageFile'] = '/image/gateMarker' . (strtolower($vo->LastStatus) != 'fail' ? '' : '_error') . '.png';

                $gateDao = new WB_GATESTATUS_DAO();
                $gateVo = $gateDao->SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");
                if ($gateVo !== false) {
                    $addEquip['mode'] = $gateVo->Gate == 'close' ? '닫힘' : '열림';
                }

                array_push($mk, $addEquip);
                break;
        }
    } catch (Exception $ex) {
        warningLog("mainMap::{$ex->getMessage()}");
        return false;
    }
}

echo json_encode($mk);
