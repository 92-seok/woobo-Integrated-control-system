<?php
session_start();

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/display/server/displayStatus.php';
?>
<div class="inBox">
    <div class="title">장비상태설정1</div>

    <table border="0" cellpadding="0" cellspacing="0" class="disTable" rules="rows"
        style="margin-top:20px; box-shadow:0px 5px 3px 3px #ebebeb;">
        <tr align="center">
            <th width="5%">no</th>
            <th width="15%">장비명</th>
            <th width="15%">전원상태</th>
            <th width="15%">릴레이상태</th>
            <th width="15%">표출상태</th>
            <th>밝기</th>
        </tr>
        <?php
        $s_point = ($page - 1) * $list;
        $sql = "select a.NM_DIST_OBSV, a.DetCode, b.*
				from wb_equip as a left join wb_disstatus as b
					on a.CD_DIST_OBSV = b.CD_DIST_OBSV
				where GB_OBSV = '18' and USE_YN = '1' 
				order by a.CD_DIST_OBSV asc";

        $res = mysqli_query($conn, $sql);

        $count = ($page - 1) * $list;

        while ($row = mysqli_fetch_assoc($res)) {

            $power = explode('/', $row['Power']);
            $relay = getRelay($row['Relay']);
            ?>
        <tr align="center" class="trList" data-num="<?= $row['CD_DIST_OBSV'] ?>">
            <td><?= ++$count ?></td>
            <td style="text-align: left; padding-left:10px;"><?= $row['NM_DIST_OBSV'] ?></td>
            <td>
                <?php for ($i = 0; $i < $row['DetCode']; $i++) {
                    if ($i > 0) {
                        echo '/';
                    }
                    if ($power[$i] == 0) {
                        echo 'OFF';
                    } elseif ($power[$i] == 1) {
                        echo 'ON';
                    }
                } ?>
            </td>
            <td><?= $relay[0] . ' / ' . $relay[1] . ' / ' . $relay[2] . ' / ' . $relay[3] ?></td>
            <td>
                <?php if ($row['ExpStatus'] == 'ad') {
                    echo '일반';
                } elseif ($row['ExpStatus'] == 'emg') {
                    echo '긴급';
                } else {
                    echo '-';
                } ?>
            </td>
            <td>
                <?php if ($row['Bright'] == '') {
                    echo '-';
                } else {
                    echo $row['Bright'];
                } ?>
            </td>
        </tr>
        <?php
        }

// end whild
?>
    </table>

</div>

<!--<script src="../../js/jquery-1.9.1.js"></script>-->
<script src="../../js/jquery-3.7.1.js"></script>
<script src="../../js/jquery-migrate-3.5.0.js"></script>
<script>
$(document).ready(function(e) {

    $(document).on("click", ".trList", function() {

        var num = $(this).attr("data-num");
        window.location.href = "frame/disequipDetail.php?num=" + num;

    });

});
</script>