<?php
if ($_SESSION['system'] == 'flood') {
    $_SESSION['enTopbar'] = 'p a r k i n g&nbsp;&nbsp;&nbsp;l o t&nbsp;&nbsp;&nbsp;f l o o d&nbsp;&nbsp;&nbsp;b l o c k i n g&nbsp;&nbsp;&nbsp;s y s t e m';
} elseif ($_SESSION['system'] == 'warning') {
    $_SESSION['enTopbar'] = 'e a r l y&nbsp;&nbsp;&nbsp;w a r n i n g&nbsp;&nbsp;&nbsp;s y s t e m';
} elseif ($_SESSION['system'] == 'dplace') {
    $_SESSION['enTopbar'] = 'd a n g e r o u s&nbsp;&nbsp;&nbsp;a r e a&nbsp;&nbsp;&nbsp;o f&nbsp;&nbsp;&nbsp;s l o p e&nbsp;&nbsp;&nbsp;s y s t e m';
} elseif ($_SESSION['system'] == 'ai') {
    $_SESSION['enTopbar'] = 'I n t e l l i g e n t&nbsp;&nbsp;&nbsp;i n t e g r a t e d&nbsp;&nbsp;&nbsp;c o n t r o l&nbsp;&nbsp;&nbsp;s y s t e m';
}

include_once $_SERVER['DOCUMENT_ROOT'] . '/version.php';
?>

<style>
.cs_top_bar {
    color: <?= $_SESSION['color'] ?>;
}
</style>
<div class="cs_top_bar" id="id_top_bar">
    <div style='display:inline-block;height:45px;text-align:center;'><?= $_SESSION['enTopbar'] ?></div>
    <div class="cs_logo">By woobosystem <?= $version ?></div>
</div>

<div class="cs_top_bar_news" id="id_top_bar_submenu_main" style='top:45px;'>
    <div class="cs_top_news_img"></div>
    <marquee width="100%" height="45px" scrolldelay="1"
        style="font-size:18px; line-height:45px; z-index:99; color:#ccc;">
        <?php
        $content = [];
        $str_path = $_SERVER['DOCUMENT_ROOT'] . '/files/KMA_log.txt';
        $fp = fopen($str_path, 'r');
        if ($fp !== false) {
            $fr = fread($fp, 1000);
            if ($fr !== false) {
                $content = explode('○ ', iconv('EUC-KR', 'UTF-8', $fr));
            }
            fclose($fp);
        }

        for ($i = 1; $i < count($content); $i++) {
            $detail = explode(')', $content[$i]);

            if ($detail[0] == '(강수' || $detail[0] == '(기온' || $detail[0] == '(주말전망') {
                echo $content[$i] . '<br><br>';
            }
        }
        ?>
    </marquee>
</div>