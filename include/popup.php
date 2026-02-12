<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dotenv.php';
loadenv();
$CCTV_HOST = getenv('CCTV_HOST');
$CCTV_PORT = getenv('CCTV_PORT');
$GATE_CCTV = getenv('GATE_CCTV') ?? false;

// 환경변수가 로드되지 않았을 경우 에러 처리
if (!$CCTV_HOST || !$CCTV_PORT) {
    error_log("CCTV 환경변수 로드 실패: CCTV_HOST={$CCTV_HOST}, CCTV_PORT={$CCTV_PORT}");
    // 환경변수가 없으면 빈 값으로 설정 (JavaScript에서 처리)
    $CCTV_HOST = '';
    $CCTV_PORT = '';
}
?>
<style>
.cs_popup .cs_pCate.active {
    /* color: <?= $_SESSION['color'] ?>; */
    /* border-top: 1px solid <?= $_SESSION['color'] ?>; */
    color: #fff;
    font-weight: bold;
    background-color: <?= $_SESSION['color'] ?>;
}
</style>
<div class="cs_popup" id="id_popup">
    <div class="cs_pBtn" id="id_pBtn"></div>
    <div class='cs_successMessage'>작업을 완료했습니다.</div>
    <div class="cs_pTitle">
        <div><span>지능형 어시스턴트</span></div>
        <div style="margin-bottom:4px;"><span class="material-symbols-outlined psychology_alt"
                style="cursor:unset;">psychology_alt</span></div>
    </div>

    <div class="cs_pCateTab">
        <div class="cs_pCate active" data-type="radar">위성영상</div>
        <div class="cs_pCate" data-type="alert">실시간현황</div>
        <div class="cs_pCate" data-type="data">계측현황</div>
        <div class="cs_pCate" data-type="equip">장비현황</div>
        <?php if ($GATE_CCTV == 'true') { ?>
            <div class="cs_pCate" data-type="cctv">CCTV</div>
        <?php } ?>
        <div class="cs_pCate" data-type="as">A/S접수</div>
    </div>

    <div class="cs_pContent">
        <div class="cs_dataForm" id="id_data_radar" style="overflow:hidden;">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/radarPopup.php'; ?>
        </div>
        <div class="cs_dataForm" id="id_data_alert" style="display:none;">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/alertPopup.php'; ?>
        </div>
        <div class="cs_dataForm" id="id_data_data" style="display:none;">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/dataPopup.php'; ?>
        </div>
        <div class="cs_dataForm" id="id_data_equip" style="display:none;">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/equipPopup.php'; ?>
        </div>
        <?php if ($GATE_CCTV == 'true') { ?>
            <div class="cs_dataForm" id="id_data_cctv" style="display:none;">
                <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/cctvPopup.php'; ?>
            </div>
        <?php } ?>
        <div class="cs_dataForm" id="id_data_as" style="display:none;">
            <?php include_once $_SERVER['DOCUMENT_ROOT'] . '/include/server/asPopup.php'; ?>
        </div>
    </div>
</div>
 <script>
    // CCTV 환경변수를 전역 변수로 선언 (popup.js에서 사용)
    window.cctvHost = '<?= $CCTV_HOST ?>';
    window.cctvPort = '<?= $CCTV_PORT ?>';
    window.gateCctv = '<?= $GATE_CCTV == 'true' ?>';
    const CCTV_HOST = '<?= $CCTV_HOST ?>';
    const CCTV_PORT = '<?= $CCTV_PORT ?>';
    const GATE_CCTV = '<?= $GATE_CCTV == 'true' ?>';

    // 환경변수 검증
    if (!window.cctvHost || !window.cctvPort) {
        console.error('CCTV 환경변수가 설정되지 않았습니다. .env 파일을 확인해주세요.');
        console.error('필요한 환경변수: CCTV_HOST, CCTV_PORT');
    } else {
        console.log('CCTV 환경변수 로드됨:', {
            cctvHost: window.cctvHost,
            cctvPort: window.cctvPort
        });
    }
</script>
<!--<script src="/js/jquery-1.9.1.js"></script>-->
<script src="/js/jquery-3.7.1.js"></script>
<script src="/js/jquery-migrate-3.5.0.js"></script>
<script src="/js/cctv-modal.js"></script>
<script src="/js/popup.js"></script>
