<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_ISUALERT_DAO();
$vo = new WB_ISUALERT_VO();

$equipDao = new WB_EQUIP_DAO();
$equipVo = new WB_EQUIP_VO();

$vo->AltCode = $_GET['num'];
$type = $_GET['type'];

if ($type == 'upd') {
    $vo = $dao->SELECT_SINGLE("AltCode = '{$vo->AltCode}'");
}
?>

<style>
.cs_datatable td {
    text-align: left;
    text-indent: 10px;
    position: relative;
}

.cs_detail {
    display: none;
    position: relative;

}

.cs_block {
    position: absolute;
    width: 100%;
    height: 100%;
    left: 0px;
    top: 0px;
    background-color: rgba(67, 64, 64, 0.35);

    box-sizing: border-box;
    -moz-box-sizing: border-box;
    -ms-box-sizing: border-box;
    -o-box-sizing: border-box;
    -webkit-box-sizing: border-box;
}
</style>
<div class="cs_frame">
    <form action="" method="post" id="id_form">
        <input type="hidden" name="EquType" id="id_type" value="<?= $vo->EquType ?>">
        <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="all" style="margin-top:20px;">
            <tr>
                <th width="13%">계측장비</th>
                <td align="left" style="text-indent:10px;">
                    <select name="CD_DIST_OBSV" id="id_select">
                        <?php if ($type == 'ins') {
                            echo "<option value='' data-type='none' selected disabled>센서 선택</option>";
                            echo "<option value='0' data-type='news'>[특보] 기상청 예보</option>";

                            $equipVo = $equipDao->SELECT("GB_OBSV IN ('01','02','03','04','05','08','21') and USE_YN = '1'");
                            foreach ($equipVo as $v) {
                                switch ($v->GB_OBSV) {
                                    case '01':
                                        $areaType = '강우';
                                        $equipType = 'rain';
                                        break;

                                    case '02':
                                        $areaType = '수위';
                                        $equipType = 'water';
                                        break;

                                    case '03':
                                        $areaType = '변위';
                                        $equipType = 'dplace';
                                        break;

                                    case '04':
                                        $areaType = '함수비';
                                        $equipType = 'soil';
                                        break;

                                    case '06':
                                        $areaType = '적설';
                                        $equipType = 'snow';
                                        break;

                                    case '08':
                                        $areaType = '경사';
                                        $equipType = 'tilt';
                                        break;

                                    case '21':
                                        $areaType = '침수';
                                        $equipType = 'flood';
                                        break;

                                    default:
                                        $GB_OBSV = $v->GB_OBSV;
                                        $areaType = "계측기({$GB_OBSV})";
                                        $equipType = '(null)';
                                        break;
                                }
                                echo "<option value='{$v->CD_DIST_OBSV}' data-type='{$equipType}'>[{$areaType}]{$v->NM_DIST_OBSV}</option>";
                            }
                        } else {
                            if ($vo->CD_DIST_OBSV == '0') {
                                echo "<option value='0' data-type='news' selected>[특보] 기상청 예보</option>";
                            } else {
                                $equipVo = $equipDao->SELECT_SINGLE("CD_DIST_OBSV = '{$vo->CD_DIST_OBSV}'");

                                switch ($equipVo->GB_OBSV) {
                                    case '01':
                                        $areaType = '강우';
                                        $equipType = 'rain';
                                        break;

                                    case '02':
                                        $areaType = '수위';
                                        $equipType = 'water';
                                        break;

                                    case '03':
                                        $areaType = '변위';
                                        $equipType = 'dplace';
                                        break;

                                    case '04':
                                        $areaType = '함수비';
                                        $equipType = 'soil';
                                        break;

                                    case '06':
                                        $areaType = '적설';
                                        $equipType = 'snow';
                                        break;

                                    case '08':
                                        $areaType = '경사';
                                        $equipType = 'tilt';
                                        break;

                                    case '21':
                                        $areaType = '침수';
                                        $equipType = 'flood';
                                        break;

                                    default:
                                        $GB_OBSV = $equipVo->GB_OBSV;
                                        $areaType = "계측기({$GB_OBSV})";
                                        $equipType = '(null)';
                                        break;
                                }
                                echo "<option value='{$equipVo->CD_DIST_OBSV}' data-type='{$equipType}' selected>[{$areaType}]{$equipVo->NM_DIST_OBSV}</option>";
                            }
                        } ?>
                    </select>
                    <input type="hidden" name="equipType" id="id_equipType" value="">
                </td>
            </tr>
        </table>

        <?php
        $typeArr = ['news', 'rain', 'water', 'dplace', 'soil', 'snow', 'tilt', 'flood'];
        for ($i = 0; $i < 8; $i++) {
            echo "<div class='cs_detail {$typeArr[$i]}'>";
            echo "<table border='0' cellpadding='0' cellspacing='0' class='cs_datatable' rules='all' style='margin-top:20px;'>";
            if ($typeArr[$i] == 'rain') {
                echo '<tr>';
                echo "<th width='13%'>누적 기준 시간</th>";
                echo '<td>';
                echo "<select name='RainTime' id='id_rainTime' " . ($type == 'upd' ? 'disabled' : '') . '>';
                echo "<option value='1' " . ($type == 'upd' && $vo->RainTime == '1' ? 'selected' : '') . '>1시간</option>';
                echo "<option value='2' " . ($type == 'upd' && $vo->RainTime == '2' ? 'selected' : '') . '>2시간</option>';
                echo "<option value='3' " . ($type == 'upd' && $vo->RainTime == '3' ? 'selected' : '') . '>3시간</option>';
                echo "<option value='6' " . ($type == 'upd' && $vo->RainTime == '6' ? 'selected' : '') . '>6시간</option>';
                echo "<option value='12' " . ($type == 'upd' && $vo->RainTime == '12' ? 'selected' : '') . '>12시간</option>';
                echo "<option value='24' " . ($type == 'upd' && $vo->RainTime == '24' ? 'selected' : '') . '>24시간</option>';
                echo '</select>';
                echo '</td>';
                echo '</tr>';
            }

            for ($j = 1; $j <= 4; $j++) {
                if ($type == 'upd' && strtolower($vo->{"L{$j}Use"}) == 'on') {
                    $d = 'display:none;';
                    $c = 'checked';
                } else {
                    $d = '';
                    $c = '';
                }

                echo '<tr>';
                echo "<th width='13%'>";
                echo "<input type='checkbox' name='{$typeArr[$i]}Check_{$j}' class='cs_alertCheck' id='id_{$typeArr[$i]}Check_{$j}' value='{$j}' $c>{$j}단계";
                echo '</th>';
                echo '<td>';
                echo "<div class='cs_block' id='id_{$typeArr[$i]}Block_{$j}' style='{$d}'></div>";

                switch ($typeArr[$i]) {
                    case 'news':
                        if ($type == 'upd' && strtolower($vo->{"L{$j}Use"}) == 'on') {
                            $news = explode(',', $vo->{"L{$j}Std"});
                        } else {
                            $news = [];
                        }

                        echo "<input type='checkbox' name='news_{$j}' class='cs_news_{$j}' value='20' " . ($type == 'upd' && in_array(20, $news) ? 'checked' : '') . '> 호우주의보';
                        echo "<input type='checkbox' name='news_{$j}' class='cs_news_{$j}' value='21' " . ($type == 'upd' && in_array(21, $news) ? 'checked' : '') . '> 태풍주의보';
                        echo "<input type='checkbox' name='news_{$j}' class='cs_news_{$j}' value='70' " . ($type == 'upd' && in_array(70, $news) ? 'checked' : '') . '> 호우경보';
                        echo "<input type='checkbox' name='news_{$j}' class='cs_news_{$j}' value='71' " . ($type == 'upd' && in_array(71, $news) ? 'checked' : '') . '> 태풍경보';
                        break;

                    case 'rain':
                        echo "<input type='text' name='rain_{$j}' id='id_rainData_{$j}' value='{$vo->{"L{$j}Std"}}'> mm</td>";
                        break;

                    case 'water':
                        if ($vo->{"L{$j}Std"}) {
                            $val = $vo->{"L{$j}Std"} / 1000;
                        } else {
                            $val = '0';
                        }

                        echo "<input type='text' name='water_{$j}' id='id_waterData_{$j}' value='{$val}'> M</td>";
                        break;

                    case 'dplace':
                        if ($type == 'upd' && strtolower($vo->{"L{$j}Use"}) == 'on') {
                            $data = explode('/', $vo->{"L{$j}Std"});
                        } else {
                            $data = ['', ''];
                        }

                        echo "<div>누적<input type='text' name='dplace_{$j}' id='id_dplace_{$j}' value='{$data[0]}'> mm</div>";
                        echo "<div>속도<input type='text' name='dpspeed_{$j}' id='id_dpspeed_{$j}' value='{$data[1]}'> mm/일</div>";
                        break;

                    case 'soil':
                        echo "<input type='text' name='soil_{$j}' id='id_soilData_{$j}' value='{$vo->{"L{$j}Std"}}'> %</td>";
                        break;

                    case 'snow':
                        $val = $vo->{"L{$j}Std"} / 10;
                        echo "<input type='text' name='snow_{$j}' id='id_snowData_{$j}' value='{$val}'> Cm</td>";
                        break;

                    case 'tilt':
                        echo "<input type='text' name='tilt_{$j}' id='id_tiltData_{$j}' value='{$vo->{"L{$j}Std"}}'> °</td>";
                        break;

                    case 'flood':
                        echo "<input type='radio' name='flood_{$j}' class='cs_flood_{$j}' value='1' " . ($type == 'upd' && $vo->{"L{$j}Std"} == '1' ? 'checked' : '') . '> 5Cm';
                        echo "<input type='radio' name='flood_{$j}' class='cs_flood_{$j}' value='2' " . ($type == 'upd' && $vo->{"L{$j}Std"} == '2' ? 'checked' : '') . '> 13Cm';
                        echo "<input type='radio' name='flood_{$j}' class='cs_flood_{$j}' value='3' " . ($type == 'upd' && $vo->{"L{$j}Std"} == '3' ? 'checked' : '') . '> 21Cm';
                        break;

                    default:
                        echo "<input type='text' name='{$typeArr[$i]}_{$j}' id='id_{$typeArr[$i]}Data_{$j}' value='{$vo->{"L{$j}Std"}}'> mm</td>";
                }

                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
        ?>
    </form>

    <div class="cs_btnBox">
        <?php if ($type == 'ins') {
            echo "<div class='cs_btn' id='id_criSaveBtn' data-type='ins'>저 장</div>";
        } elseif ($type == 'upd') {
            echo "<div class='cs_btn' id='id_criSaveBtn' data-type='upd'>수 정</div>";
            echo "<div class='cs_btn' id='id_criSaveBtn' data-type='del'>삭 제</div>";
        } ?>
    </div>

    <div id="id_helpForm">
        <div id="id_help" stat="close">
            <div><span class="material-symbols-outlined help">help_outline</span></div>&nbsp;
            <div id='id_helpMessage'> 도움말 보기</div>
        </div>
        <div class='cs_help'>
            1. 계측장비를 선택합니다.<br />
            2. 단계를 선택합니다.<br />
            &nbsp;(선택하지 않은 단계는 사용되지 않습니다)<br />
            3. 임계값을 입력합니다.<br />
            &nbsp;(입력한 임계치는 경보발령의 기준이 됩니다)<br />
        </div>
    </div>
</div>

</div>

<script>
$(document).ready(function() {
    let EquType = $("#id_select").children().attr("data-type");

    if (EquType != "none") {
        $(".cs_detail").css("display", "none");
        $("." + EquType).css("display", "block");
    }
})
</script>