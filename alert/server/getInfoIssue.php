<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$groupDao = new WB_ISUALERTGROUP_DAO();
$listDao = new WB_ISUALERT_DAO();
$equipDao = new WB_EQUIP_DAO();

$groupVo = new WB_ISUALERTGROUP_VO();
$listVo = new WB_ISUALERT_VO();
$equipVo = new WB_EQUIP_VO();

$groupVo->GCode = $_POST['num'];

$groupVo = $groupDao->SELECT_SINGLE("GCode = '{$groupVo->GCode}'");
$alert = explode(',', $groupVo->AltCode);

for ($i = 1; $i <= 4; $i++) {
    echo "<div class='cs_detailBox'>";
    echo "<div class='cs_label'>{$i}단계</div>";
    echo "<div class='cs_infotitle'>※ 임계치 조건</div>";

    $listVo = $listDao->SELECT("AltCode IN ({$groupVo->AltCode})");
    foreach ($listVo as $v) {
        if (strtolower($v->{"L{$i}Use"}) == 'on') {
            $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'");
            if ($equipVo->NM_DIST_OBSV) {
                $name = $equipVo->NM_DIST_OBSV;
            } else {
                $name = '알 수 없는 장비';
            }

            switch (strtolower($v->EquType)) {
                case 'news':
                    $L1Std1 = explode(',', $v->{"L{$i}Std"});
                    $news = [];
                    if (in_array(20, $L1Std1)) {
                        array_push($news, '호우주의보');
                    }
                    if (in_array(21, $L1Std1)) {
                        array_push($news, '호우특보');
                    }
                    if (in_array(70, $L1Std1)) {
                        array_push($news, '태풍주의보');
                    }
                    if (in_array(71, $L1Std1)) {
                        array_push($news, '태풍특보');
                    }

                    echo '- 특보<br/>';
                    echo ' ' . implode(', ', $news) . '<br/>';
                    break;

                case 'rain':
                    echo "- 강우 [{$v->RainTime}시간]<br/>";
                    echo " {$name} : {$v->{"L{$i}Std"}}mm<br/>";
                    break;

                case 'water':
                    $val = $v->{"L{$i}Std"} / 1000;
                    echo '- 수위<br/>';
                    echo " {$name} : {$val}M<br/>";
                    break;

                case 'dplace':
                    if (isset($v->{"L{$i}Std"})) {
                        $data = explode('/', $v->{"L{$i}Std"});
                    } else {
                        $data = ['', ''];
                    }

                    echo '- 변위<br/>';
                    echo " {$name} : ";
                    echo "[누적] {$data[0]}mm, ";
                    echo "[속도] {$data[1]}mm/일<br/>";
                    break;

                case 'soil':
                    echo '- 함수비<br/>';
                    echo " {$name} : {$v->{"L{$i}Std"}}%<br/>";
                    break;

                case 'snow':
                    $val = $v->{"L{$i}Std"} / 10;
                    echo '- 적설<br/>';
                    echo " {$name} : {$val}Cm<br/>";
                    break;

                case 'tilt':
                    echo '- 경사<br/>';
                    echo " {$name} : {$v->{"L{$i}Std"}}°<br/>";
                    break;

                case 'flood':
                    if ($v->{"L{$i}Std"} == '1') {
                        $val = '5Cm';
                    } elseif ($v->{"L{$i}Std"} == '2') {
                        $val = '13Cm';
                    } elseif ($v->{"L{$i}Std"} == '3') {
                        $val = '21Cm';
                    }

                    echo '- 침수<br/>';
                    echo " {$name} : {$val}<br/>";
                    break;
            }
        }
    }

    echo "<div class='cs_infotitle'>※ 동작장비</div>";

    if ($groupVo->{"Equip{$i}"}) {
        $equipVo = $equipDao->SELECT("CD_DIST_OBSV IN ({$groupVo->{"Equip{$i}"}})");
        foreach ($equipVo as $v) {
            if (isset($v->NM_DIST_OBSV)) {
                $name = $v->NM_DIST_OBSV;
            } else {
                $name = '알 수 없는 장비';
            }

            if ($v->GB_OBSV == '17') {
                echo "- 방송 : {$name}<br/>";
            } elseif ($v->GB_OBSV == '18') {
                echo "- 전광판 : {$name}<br/>";
            } elseif ($v->GB_OBSV == '20') {
                echo "- 차단기 : {$name}<br/>";
            }
        }
    }

    if (strtolower($groupVo->{"Auto{$i}"} == 'on')) {
        $accept = '자동승인';
    } elseif (strtolower($groupVo->{"Auto{$i}"}) == 'off') {
        $accept = '수동승인';
    } else {
        $accept = '해당 없음';
    }

    echo "<div class='cs_infotitle'>";
    echo "※ 담당자 승인여부 : {$accept}";
    echo '</div>';
    echo '</div>';
}
?>
