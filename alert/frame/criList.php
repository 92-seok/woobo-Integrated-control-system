<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
?>

<div class="cs_frame">
    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin:20px 0px;">
        <tr align="center">
            <th width="3%">no</th>
            <th width="5%">장비타입</th>
            <th>장비명</th>
            <th width="20%">1단계</th>
            <th width="20%">2단계</th>
            <th width="20%">3단계</th>
            <th width="20%">4단계</th>
        </tr>
        <?php
        $equipDao = new WB_EQUIP_DAO();
        $eName = new WB_EQUIP_VO();

        $dao = new WB_ISUALERT_DAO();
        $vo = new WB_ISUALERT_VO();

        $vo = $dao->SELECT();
        $count = 1;

        foreach ($vo as $v) {
            $equType = $v->EquType;

            echo "<tr align='center' id='id_criList' data-num='{$v->AltCode}' data-type='upd' style='cursor:pointer;'>";
            echo "<td>{$count}</td>";

            echo '<td>';
            switch ($equType) {
                case 'news':
                    echo '특보';
                    break;
                case 'rain':
                    echo '강우';
                    echo "({$v->RainTime}시간)";
                    break;
                case 'water':
                    echo '수위';
                    break;
                case 'dplace':
                    echo '변위';
                    break;
                case 'flood':
                    echo '침수';
                    break;
                case 'soil':
                    echo '함수비';
                    break;
                case 'tilt':
                    echo '경사';
                    break;
                case 'snow':
                    echo '적설';
                    break;
            }
            echo '</td>';

            if ($equType != 'news') {
                $eName = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$v->CD_DIST_OBSV}'");
                if (!isset($eName->NM_DIST_OBSV)) {
                    $eName->NM_DIST_OBSV = '장비를 알 수 없음';
                }
            } else {
                $eName->NM_DIST_OBSV = '기상청 예보';
            }

            echo "<td style='text-align: left; padding-left:10px;'>{$eName->NM_DIST_OBSV} ({$v->CD_DIST_OBSV})</td>";

            for ($i = 1; $i <= 4; $i++) {
                echo "<td style='padding: 5px; text-align:left;'>";
                switch ($equType) {
                    case 'news':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            $news = explode(',', $v->{"L{$i}Std"});
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

                            echo implode(',', $r);
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'rain':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            echo "{$v->{"L{$i}Std"}} mm";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'water':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            // $val = $v->{"L{$i}Std"} / 1000;
                            $val = number_format($v->{"L{$i}Std"} / 1000, 1);
                            echo "{$val} M";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'dplace':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            $dplace = explode('/', $v->{"L{$i}Std"});

                            echo "[누적] {$dplace[0]} mm<br>";
                            echo "[속도] {$dplace[1]} mm/일";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'soil':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            echo "{$v->{"L{$i}Std"}} %";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'snow':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            $val = $v->{"L{$i}Std"} / 10;
                            echo "{$val} M";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'tilt':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            echo "{$v->{"L{$i}Std"}} °";
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;

                    case 'flood':
                        if (strtolower($v->{"L{$i}Use"}) == 'on') {
                            if ($v->{"L{$i}Std"} == '1') {
                                echo '5 Cm';
                            } elseif ($v->{"L{$i}Std"} == '2') {
                                echo '13 Cm';
                            } elseif ($v->{"L{$i}Std"} == '3') {
                                echo '21 Cm';
                            }
                        } else {
                            echo "<span style='color:gray'>미사용</span>";
                        }
                        break;
                }
                echo '</td>';
            }
            echo '</tr>';
            $count++;
        }
        ?>
    </table>
    <div class='cs_btnBox' style="justify-content:flex-end;">
        <div class='cs_btn' id='id_criList' data-num='' data-type='ins'>임계값 추가</div>
    </div>
    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            - 경보발령의 기준이 되는 임계값을 설정합니다.
        </div>
    </div>
</div>