<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/sessionUseTime.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbconn.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbdao.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/dbvo.php';

// CCTV 데이터 조회 (wb_equip에서 GB_OBSV가 'CC'이고 USE_YN이 '1'인 데이터)
$sql = "SELECT CD_DIST_OBSV, NM_DIST_OBSV, ConnIP FROM wb_equip WHERE GB_OBSV = 'CC' AND USE_YN = '1' ORDER BY CD_DIST_OBSV ASC";

$result = mysqli_query($conn, $sql);
$cctvData = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // ConnIP가 null이거나 빈 값인 경우 기본값 설정
        // $connIP = !empty($row['ConnIP']) ? $row['ConnIP'] : '192.168.1.' . str_pad($row['CD_DIST_OBSV'], 3, '0', STR_PAD_LEFT);
        $connIP = !empty($row['ConnIP']) ? $row['ConnIP'] : 'localhost' . str_pad($row['CD_DIST_OBSV'], 3, '0', STR_PAD_LEFT);

        $cctvData[] = [
            'cd_dist_obsv' => $row['CD_DIST_OBSV'],
            'nm_dist_obsv' => $row['NM_DIST_OBSV'],
            'conn_ip' => $connIP
        ];
    }
}

// 디버깅: CCTV 데이터 확인
echo '<!-- CCTV 데이터: ' . json_encode($cctvData) . ' -->';
?>

<div class="cs_cctv_popup">
    <div class="cs_cctv_grid">
        <?php foreach ($cctvData as $index => $cctv): ?>
        <div class="cs_cctv_item">
            <div class="cs_cctv_title">
                <div class="cs_cctv_title_left">
                    <div class="cs_status_indicator" id="indicator<?php echo $cctv['cd_dist_obsv']; ?>" title="연결 상태"></div>
                    <span class="cs_cctv_name"><?php echo htmlspecialchars($cctv['nm_dist_obsv']); ?></span>
                </div>
                <span class="cs_cctv_ip"><?php echo htmlspecialchars($cctv['conn_ip']); ?></span>
            </div>
            <div class="cs_cctv_container">
                <canvas id="canvas<?php echo $cctv['cd_dist_obsv']; ?>" class="cs_cctv_canvas" data-key="<?php echo $cctv['cd_dist_obsv']; ?>" data-name="<?php echo htmlspecialchars($cctv['nm_dist_obsv']); ?>" width="320" height="240"></canvas>
                <div class="cs_error_overlay" id="error<?php echo $cctv['cd_dist_obsv']; ?>" style="display: none;">
                   <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-triangle-alert-icon lucide-triangle-alert"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    <!-- <span class="cs_error_text">연결 실패</span> -->
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- CCTV 모달창 -->
<div id="cctvModal" class="cctv-modal">
    <div class="cctv-modal-content">
        <div class="cctv-modal-header">
            <h3 id="cctvModalTitle">CCTV 영상</h3>
            <span class="cctv-modal-close">&times;</span>
        </div>
        <div class="cctv-modal-body">
            <div id="cctvModalContent">
                <p>모달 내용이 여기에 표시됩니다.</p>
            </div>
        </div>
    </div>
</div>

<style>
.cs_cctv_popup {
    padding: 0;
    height: 100%;
    overflow-y: auto;
}

.cs_cctv_grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-auto-rows: min-content;
    gap: 0;
    width: 100%;
}

.cs_cctv_item {
    display: flex;
    flex-direction: column;
    border: 1px solid #ddd;
    overflow: hidden;
    background-color: #f9f9f9;
    aspect-ratio: 1;
}

.cs_cctv_title {
    background-color: #7b7b7b;
    color: white;
    text-align: center;
    padding: 5px;
    font-size: 12px;
    font-weight: bold;
    border-bottom: 1px solid #ddd;
    display: flex;
    flex-direction: column;
    gap: 4px;
    align-items: center;
    /* min-height: 40px; */
    justify-content: center;
}

.cs_cctv_title_left {
    display: flex;
    align-items: center;
    gap: 4px;
    justify-content: center;
    flex-wrap: wrap;
}

.cs_cctv_name {
    word-break: break-all;
    text-align: center;
    max-width: 100%;
    overflow-wrap: break-word;
}

.cs_cctv_ip {
    word-break: break-all;
    text-align: center;
    max-width: 100%;
    overflow-wrap: break-word;
    font-size: 12px;
}

/* CCTV 컨테이너 */
.cs_cctv_container {
    flex: 1;
    position: relative;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cs_cctv_canvas {
    width: 100%;
    height: 100%;
    background-color: #000;
    cursor: pointer;
    transition: background-color 0.2s;
    border-radius: 4px;
    overflow: hidden;
}

/* 상태 인디케이터 */
.cs_status_indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background-color: #666; /* 기본 상태 (로딩 중) */
    flex-shrink: 0;
    transition: background-color 0.3s ease;
}

.cs_status_indicator.success {
    background-color: #28a745; /* 녹색 - 성공 */
    box-shadow: 0 0 4px rgba(40, 167, 69, 0.5);
}

.cs_status_indicator.error {
    background-color: #dc3545; /* 적색 - 실패 */
    box-shadow: 0 0 4px rgba(220, 53, 69, 0.5);
}

/* 오류 오버레이 */
.cs_error_overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    color: #666;
    background-color: #000;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    border-radius: 4px;
    cursor: pointer;
}

.cs_error_icon {
    width: 48px;
    height: 48px;
    color: #dc3545;
    stroke-width: 1.5;
}

.cs_error_text {
    color: #495057;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
}

.cs_cctv_canvas:hover {
    background-color: #d0d0d0;
    transition: transform 0.2s ease;
}

/* CCTV 모달창 스타일 */
.cctv-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.cctv-modal-content {
    background-color: #fefefe;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 0;
    border: 1px solid #888;
    width: 90%;
    max-width: 1000px;
    max-height: 80vh;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.cctv-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    background-color: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.cctv-modal-header h3 {
    margin: 0;
    color: #333;
    font-size: 18px;
}

.cctv-modal-close {
    color: #aaa;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    line-height: 1;
}

.cctv-modal-close:hover,
.cctv-modal-close:focus {
    color: #000;
}

.cctv-modal-body {
    padding: 20px;
    min-height: 200px;
}

/* CCTV 영상 캔버스 스타일 */
.cctv-modal-body canvas {
    width: 100%;
    height: 100%;
    background-color: #000;
    border-radius: 4px;
}

/* 모달창 크기 조정 */
.cctv-modal-content {
    background-color: #fefefe;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 0;
    border: 1px solid #888;
    width: 90%;
    max-width: 1000px;
    max-height: 80vh;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}
</style>
