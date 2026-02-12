<!doctype html>
<html>

<head>
    <title>침수차단알림 안내문자 발송</title>
    <link rel="stylesheet" type="text/css" href="/css/include.css" />
    <link rel="stylesheet" type="text/css" href="/css/frame.css" />
</head>

<body>
    <?php
    $num = $_GET['num'];
    $checkHnsLpr = $_GET['checkHnsLpr'];

    include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';

    $sql = 'SELECT * FROM wb_parksmsment';
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($res);
    ?>
    <div>
        <div><textarea name="content" id="id_content"
                style="font-family:'Nanum Square';resize:none;border:none;width:500px;height:80px;border:1px solid #d9d9d9;"><?= $row['Content'] ?></textarea>
        </div>
        <div class="cs_btn" id="id_sendBtn">차량안내문자 발송요청</div>
        <input type="hidden" id="id_number" value="<?= $num ?>">
        <input type="hidden" id="id_checkHnsLpr" value="<?= $checkHnsLpr ?>">
    </div>
    <!--<script src="/js/jquery-1.9.1.js"></script>-->
    <script src="/js/jquery-3.7.1.js"></script>
    <script src="/js/jquery-migrate-3.5.0.js"></script>
    <!-- 이 팝업 창은 "window.open" 새 창으로 열리기 때문에, 메뉴 프레임에 필요한 include.js 파일을 사용할 필요 없다. -->
    <script src="/js/gate.js"></script>
</body>

</html>