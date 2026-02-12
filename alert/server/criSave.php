<?php
$data = json_decode(file_get_contents('php://input'), true);

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$equipDao = new WB_EQUIP_DAO();
$equipVo = new WB_EQUIP_VO();

$dao = new WB_ISUALERT_DAO();
$vo = new WB_ISUALERT_VO();

$type = $data['type'];
$vo->CD_DIST_OBSV = $data['CD_DIST_OBSV'];
$vo->EquType = $data['EquType'];

if ($vo->EquType == 'rain') {
    $vo->RainTime = $data['RainTime'];
}
for ($i = 1; $i <= 4; $i++) {
    if (!isset($data["{$vo->EquType}Check_{$i}"])) {
        $vo->{"L{$i}Use"} = 'OFF';
        $vo->{"L{$i}Std"} = '';
    } else {
        $vo->{"L{$i}Use"} = 'ON';

        switch ($vo->EquType) {
            case 'water':
                $vo->{"L{$i}Std"} = $data["{$vo->EquType}_{$i}"] * 1000;
                break;

            case 'dplace':
                $vo->{"L{$i}Std"} = "{$data["{$vo->EquType}_{$i}"]}/{$data["dpspeed_{$i}"]}";
                break;

            case 'snow':
                $vo->{"L{$i}Std"} = $data["{$vo->EquType}_{$i}"] * 10;
                break;

            default:
                $vo->{"L{$i}Std"} = $data["{$vo->EquType}_{$i}"];
                break;
        }
    }
}

if ($type == 'ins') {
    $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");

    $result['action'] = "{$vo->EquType} Cri Insert";
    $result['name'] = $vo->EquType == 'rain' ? "{$equipVo->NM_DIST_OBSV}[{$vo->RainTime}]" : "{$equipVo->NM_DIST_OBSV}";
    $result['before'] = '';
    $result['after'] = '';

    for ($i = 1; $i <= 4; $i++) {
        $result['after'] .= "{$i}단계({$vo->{"L{$i}Use"}}) : ";
        if (strtolower($vo->{"L{$i}Use"}) == 'on') {
            switch ($vo->EquType) {
                case 'news':
                    $news = explode(',', $vo->{"L{$i}Std"});
                    $r = [];
                    if (in_array('20', $news)) {
                        array_push($r, '호우주의보');
                    }
                    if (in_array('21', $news)) {
                        array_push($r, '태풍주의보');
                    }
                    if (in_array('70', $news)) {
                        array_push($r, '호우경보');
                    }
                    if (in_array('71', $news)) {
                        array_push($r, '태풍경보');
                    }

                    $result['after'] .= implode(',', $r) . '<br/>';
                    break;

                case 'rain':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} mm<br/>";
                    break;

                case 'water':
                    $val = $vo->{"L{$i}Std"} / 1000;
                    $result['after'] .= "{$val} M<br/>";
                    break;

                case 'dplace':
                    $data = explode('/', $vo->{"L{$i}Std"});
                    $result['after'] .= "[누적]{$data[0]} mm / ";
                    $result['after'] .= "[속도]{$data[1]} mm/일<br/> ";
                    break;

                case 'soil':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} %<br/>";
                    break;

                case 'snow':
                    $val = $vo->{"L{$i}Std"} / 10;
                    $result['after'] .= "{$val} Cm<br/>";
                    break;

                case 'tilt':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} °<br/>";
                    break;

                case 'flood':
                    if ($vo->{"L{$i}Std"} == '1') {
                        $result['after'] .= '5 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '2') {
                        $result['after'] .= '13 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '3') {
                        $result['after'] .= '21 Cm<br/>';
                    }
                    break;

                default:
                    $result['after'] .= "{$vo->{"L{$i}Std"}}<br/>";
            }
        }
    }

    ///
    $vo->NowType = '0';
    $vo->ChkCount = '0';

    $dao->INSERT($vo);
} elseif ($type == 'upd') {
    $bVo = new WB_ISUALERT_VO();
    if ($vo->EquType == 'rain') {
        $bVo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}' AND RainTime = '{$vo->RainTime}'");
    } else {
        $bVo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");
    }

    $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");

    $result['action'] = "{$vo->EquType} Cri Update";
    $result['name'] = $vo->EquType == 'rain' ? "{$equipVo->NM_DIST_OBSV}[{$vo->RainTime}]" : "{$equipVo->NM_DIST_OBSV}";
    $result['before'] = '';
    $result['after'] = '';

    for ($i = 1; $i <= 4; $i++) {
        $result['before'] .= "{$i}단계({$bVo->{"L{$i}Use"}}) : ";
        if (strtolower($bVo->{"L{$i}Use"}) == 'on') {
            switch ($vo->EquType) {
                case 'news':
                    $news = explode(',', $bVo->{"L{$i}Std"});
                    $r = [];
                    if (in_array('20', $news)) {
                        array_push($r, '호우주의보');
                    }
                    if (in_array('21', $news)) {
                        array_push($r, '태풍주의보');
                    }
                    if (in_array('70', $news)) {
                        array_push($r, '호우경보');
                    }
                    if (in_array('71', $news)) {
                        array_push($r, '태풍경보');
                    }

                    $result['before'] .= implode(',', $r) . '<br/>';
                    break;

                case 'rain':
                    $result['before'] .= "{$bVo->{"L{$i}Std"}} mm<br/>";
                    break;

                case 'water':
                    $val = $bVo->{"L{$i}Std"} / 1000;
                    $result['before'] .= "{$val} M<br/>";
                    break;

                case 'dplace':
                    $data = explode('/', $bVo->{"L{$i}Std"});
                    $result['before'] .= "[누적]{$data[0]} mm / ";
                    $result['before'] .= "[속도]{$data[1]} mm/일<br/> ";
                    break;

                case 'soil':
                    $result['before'] .= "{$bVo->{"L{$i}Std"}} %<br/>";
                    break;

                case 'snow':
                    $val = $bVo->{"L{$i}Std"} / 10;
                    $result['before'] .= "{$val} Cm<br/>";
                    break;

                case 'tilt':
                    $result['before'] .= "{$bVo->{"L{$i}Std"}} °<br/>";
                    break;

                case 'flood':
                    if ($bVo->{"L{$i}Std"} == '1') {
                        $result['before'] .= '5 Cm<br/>';
                    } elseif ($bVo->{"L{$i}Std"} == '2') {
                        $result['before'] .= '13 Cm<br/>';
                    } elseif ($bVo->{"L{$i}Std"} == '3') {
                        $result['before'] .= '21 Cm<br/>';
                    }
                    break;

                default:
                    $result['before'] .= "{$bVo->{"L{$i}Std"}}<br/>";
            }
        }

        $result['after'] .= "{$i}단계({$vo->{"L{$i}Use"}}) : ";
        if (strtolower($vo->{"L{$i}Use"}) == 'on') {
            switch ($vo->EquType) {
                case 'news':
                    $news = explode(',', $vo->{"L{$i}Std"});
                    $r = [];
                    if (in_array('20', $news)) {
                        array_push($r, '호우주의보');
                    }
                    if (in_array('21', $news)) {
                        array_push($r, '태풍주의보');
                    }
                    if (in_array('70', $news)) {
                        array_push($r, '호우경보');
                    }
                    if (in_array('71', $news)) {
                        array_push($r, '태풍경보');
                    }

                    $result['after'] .= implode(',', $r) . '<br/>';
                    break;

                case 'rain':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} mm<br/>";
                    break;

                case 'water':
                    $val = $vo->{"L{$i}Std"} / 1000;
                    $result['after'] .= "{$val} M<br/>";
                    break;

                case 'dplace':
                    $data = explode('/', $vo->{"L{$i}Std"});
                    $result['after'] .= "[누적]{$data[0]} mm / ";
                    $result['after'] .= "[속도]{$data[1]} mm/일<br/> ";
                    break;

                case 'soil':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} %<br/>";
                    break;

                case 'snow':
                    $val = $vo->{"L{$i}Std"} / 10;
                    $result['after'] .= "{$val} Cm<br/>";
                    break;

                case 'tilt':
                    $result['after'] .= "{$vo->{"L{$i}Std"}} °<br/>";
                    break;

                case 'flood':
                    if ($vo->{"L{$i}Std"} == '1') {
                        $result['after'] .= '5 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '2') {
                        $result['after'] .= '13 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '3') {
                        $result['after'] .= '21 Cm<br/>';
                    }
                    break;

                default:
                    $result['after'] .= "{$vo->{"L{$i}Std"}}<br/>";
            }
        }
    }

    $vo->AltCode = $bVo->AltCode;
    $dao->UPDATE($vo);
} elseif ($type == 'del') {
    if ($vo->EquType == 'rain') {
        $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}' AND RainTime = '{$vo->RainTime}'");
    } else {
        $vo = $dao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");
    }
    $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");

    $result['action'] = "{$vo->EquType} Cri Delete";
    $result['name'] = $vo->EquType == 'rain' ? "{$equipVo->NM_DIST_OBSV}[{$vo->RainTime}]" : "{$equipVo->NM_DIST_OBSV}";
    $result['before'] = '';
    $result['after'] = '';

    for ($i = 1; $i <= 4; $i++) {
        $result['before'] .= "{$i}단계({$vo->{"L{$i}Use"}}) : ";
        if (strtolower($vo->{"L{$i}Use"}) == 'on') {
            switch ($vo->EquType) {
                case 'news':
                    $news = explode(',', $vo->{"L{$i}Std"});
                    $r = [];
                    if (in_array('20', $news)) {
                        array_push($r, '호우주의보');
                    }
                    if (in_array('21', $news)) {
                        array_push($r, '태풍주의보');
                    }
                    if (in_array('70', $news)) {
                        array_push($r, '호우경보');
                    }
                    if (in_array('71', $news)) {
                        array_push($r, '태풍경보');
                    }

                    $result['before'] .= implode(',', $r) . '<br/>';
                    break;

                case 'rain':
                    $result['before'] .= "{$vo->{"L{$i}Std"}} mm<br/>";
                    break;

                case 'water':
                    $val = $vo->{"L{$i}Std"} / 1000;
                    $result['before'] .= "{$val} M<br/>";
                    break;

                case 'dplace':
                    $data = explode('/', $vo->{"L{$i}Std"});
                    $result['before'] .= "[누적]{$data[0]} mm / ";
                    $result['before'] .= "[속도]{$data[1]} mm/일<br/> ";
                    break;

                case 'soil':
                    $result['before'] .= "{$vo->{"L{$i}Std"}} %<br/>";
                    break;

                case 'snow':
                    $val = $vo->{"L{$i}Std"} / 10;
                    $result['before'] .= "{$val} Cm<br/>";
                    break;

                case 'tilt':
                    $result['before'] .= "{$vo->{"L{$i}Std"}} °<br/>";
                    break;

                case 'flood':
                    if ($vo->{"L{$i}Std"} == '1') {
                        $result['before'] .= '5 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '2') {
                        $result['before'] .= '13 Cm<br/>';
                    } elseif ($vo->{"L{$i}Std"} == '3') {
                        $result['before'] .= '21 Cm<br/>';
                    }
                    break;

                default:
                    $result['before'] .= "{$vo->{"L{$i}Std"}}<br/>";
            }
        }
    }

    $dao->DELETE($vo);
}

echo json_encode($result);
?>
