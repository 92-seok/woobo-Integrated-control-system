<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';

if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['chk'])) {
    $chk = $_GET['chk'];
} else {
    $chk = 'all';
}
if (isset($_GET['move'])) {
    $move = $_GET['move'];
} else {
    $move = '0';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';

$dao = new WB_LOG_DAO();
$vo = new WB_LOG_VO();

if ($chk == 'all') {
    if ($move == '1') {
        $where = '1';
    } else {
        $where = "EventType != 'Move'";
    }
} else {
    if ($move == '1') {
        $where = "pType = '{$chk}'";
    } else {
        $where = "pType = '{$chk}' AND EventType != 'Move'";
    }
}

$vo = $dao->SELECT($where);

if (isset($vo[0]->{key($vo[0])})) {
    $countRec = count($vo);
} else {
    $countRec = 0;
}

$list = 25;
$block = 20;

$pageNum = ceil($countRec / $list); // 총 페이지
$blockNum = ceil($pageNum / $block); // 총 블록
$nowBlock = ceil($page / $block);

$s_page = $nowBlock * $block - ($block - 1);

if ($s_page <= 1) {
    $s_page = 1;
}
$e_page = $nowBlock * $block;
if ($pageNum <= $e_page) {
    $e_page = $pageNum;
}

$count = ($page - 1) * $list;
$listCnt = $countRec - $count;
?>
<style>
	table th
	{
		background-color:#f9d9ca;
	}

	#select
	{
		width: 100px;
		height: 30px;
		font-size: 16px;
		margin-bottom: 25px;
	}
</style>
<div class="cs_frame">
    <div class="cs_selectBox">
        <div class="cs_date" style="float:right;margin-bottom:10px;">
            <select name="type" id="id_pTypeSelect">
                <option value="all" <?php if ($chk == 'all') {
                    echo 'selected';
                } ?>>전체</option>
                <?php
                $typeArr = [];
                $res = $dao->SELECT_QUERY('SELECT DISTINCT pType FROM wb_log');
                foreach ($res as $v) {
                    array_push($typeArr, $v['pType']);
                }

                if (in_array('data', $typeArr)) {
                    echo "<option value='data' " . ($chk == 'data' ? 'selected' : '') . '>데이터</option>';
                }
                if (in_array('broad', $typeArr)) {
                    echo "<option value='broad' " . ($chk == 'broad' ? 'selected' : '') . '>방송</option>';
                }
                if (in_array('display', $typeArr)) {
                    echo "<option value='display' " . ($chk == 'display' ? 'selected' : '') . '>전광판</option>';
                }
                if (in_array('gate', $typeArr)) {
                    echo "<option value='gate' " . ($chk == 'gate' ? 'selected' : '') . '>차단기</option>';
                }
                if (in_array('alert', $typeArr)) {
                    echo "<option value='alert' " . ($chk == 'alert' ? 'selected' : '') . '>임계치</option>';
                }
                if (in_array('admin', $typeArr)) {
                    echo "<option value='admin' " . ($chk == 'admin' ? 'selected' : '') . '>계정</option>';
                }
                ?>
            </select>
            <input type="checkbox" id="id_moveSelect" name="move" <?php if ($move == '1') {
                echo 'checked';
            } ?>>페이지 이동 포함
            <div class="cs_excel" id="id_excel">엑셀다운</div>
        </div>
    </div>

    <table border="0" cellpadding="0" cellspacing="0" class="cs_datatable" rules="rows" style="margin-top:10px;">
        <tr align="center"> 
            <th width="3%">no</th>
            <th>RegDate</th>
            <th>IP(ID)</th>
            <th>Page</th>
            <th>Event</th>
        </tr>
        <?php
        $vo = $dao->SELECT($where, 'idx DESC', "{$count},{$list}");
        if (isset($vo[0]->{key($vo[0])})) {
            foreach ($vo as $v) {
                echo "<tr class='cs_trList' style='cursor:pointer' data-idx='{$v->idx}'>";
                echo "<td>{$v->idx}</td>";
                echo "<td>{$v->RegDate}</td>";
                echo "<td>{$v->ip}({$v->userID})</td>";
                echo '<td>';
                switch ($v->pType) {
                    case 'data':
                        echo '데이터';
                        break;
                    case 'broad':
                        echo '방송';
                        break;
                    case 'display':
                        echo '전광판';
                        break;
                    case 'equip':
                        echo '장비';
                        break;
                    case 'SMS':
                        echo 'SMS';
                        break;
                    case 'gate':
                        echo '차단기';
                        break;
                    case 'alert':
                        echo '임계치';
                        break;
                    case 'admin':
                        echo '계정';
                        break;
                    case 'login':
                        echo '로그인';
                        break;
                    case 'report':
                        echo '보고서';
                        break;
                }
                echo '</td>';
                echo "<td>{$v->EventType}</td>";
                echo '</tr>';
            }
        }
        ?>
    </table>

    <div class="cs_page">
        <?php
        if ($page != 1) {
            echo "<div class='cs_pages' id='id_page' data-idx='" . ($page - 1) . "'>이전</div>";
        }
        for ($p = $s_page; $p <= $e_page; $p++) {
            $act = '';
            if ($p == $page) {
                $act = 'active';
            }
            echo "<div class='cs_pages {$act}' id='id_page' data-idx='{$p}'>" . $p . '</div>';
        }
        if ($page != $pageNum) {
            echo "<div class='cs_pages' id='id_page' data-idx='" . ($page + 1) . "'>다음</div>";
        }
        ?>
    </div>
</div>

<script>
    let page = "<?= $page ?>";
    let chk = "<?= $chk ?>";
    let move = "<?= $move ?>";
    let listE = document.querySelectorAll(".cs_pages");
    listE.forEach((el) => 
    {
        el.addEventListener("click", (e) =>
        {
            page = e.target.attributes["data-idx"].value;
            getFrame(`frame/logList.php?page=${page}&chk=${chk}&move=${move}`, pType, 1, "true");
        })
    })

    listE = document.querySelectorAll(".cs_trList");
    listE.forEach((el) => 
    {
        el.addEventListener("click", (e) => 
        {
            let idx = el.attributes["data-idx"].value;
            getFrame(`frame/logDetail.php?page=${page}&chk=${chk}&move=${move}&idx=${idx}`, pType, 1, "true");
        })
    })

    listE = document.querySelector("#id_moveSelect");
    listE.addEventListener("click", (e) => 
    {
        if( e.target.checked ) move = "1";
        else move = "0";

        getFrame(`frame/logList.php?chk=${chk}&move=${move}`, pType, 1, "true");
    })

    listE = document.querySelector("#id_pTypeSelect");
    listE.addEventListener("change", () => 
    {
        chk = listE.value;
        getFrame(`frame/logList.php?chk=${chk}`, pType, 1, "true");
    })

    listE = document.querySelector("#id_excel");
    listE.addEventListener("click", (e) => 
    {
        let count = "<?= $count ?>";
        let list = "<?= $list ?>";

        window.location.href = `frame/excel/logListExcel.php?chk=${chk}&move=${move}&count=${count}&list=${list}`;
    })
</script>