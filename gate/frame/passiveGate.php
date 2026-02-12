<?php
include_once $_SERVER["DOCUMENT_ROOT"]."/include/sessionUseTime.php";
include_once $_SERVER["DOCUMENT_ROOT"]."/include/dotenv.php";
loadenv();

$CCTV_HOST = getenv('CCTV_HOST');
$CCTV_PORT = getenv('CCTV_PORT');
$GATE_CCTV = getenv('GATE_CCTV') ?? false;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap');

    /* Global Styles */
    .cs_frame {
        font-family: 'Noto Sans KR', sans-serif;
        background-color: #f0f2f5;
        padding: 20px;
        border-radius: 10px;
        display: flex; /* flexbox 사용 */
        gap: 20px; /* 두 컬럼 사이의 간격 */
    }

    /* Card Containers */
    .cs_card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        flex: 1; /* 두 컬럼이 동일한 너비를 갖도록 설정 */
    }

    /* Breaker Table */
    .cs_table_container h2 {
        font-size: 1.5em;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    .henry_datatable {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .henry_datatable th, .henry_datatable td {
        padding: 12px 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    /* 1열(차단기 이름)은 왼쪽 정렬 유지, 나머지 열들은 중앙 정렬 */
    .henry_datatable th:nth-child(1), .henry_datatable td:nth-child(1) {
        text-align: left;
    }

    .henry_datatable th:not(:nth-child(1)), .henry_datatable td:not(:nth-child(1)) {
        text-align: center;
    }

    .henry_datatable th {
        background-color: #f8f9fa;
        font-weight: 500;
        color: #555;
    }

    .henry_datatable tr:last-child td {
        border-bottom: none;
    }

    .henry_datatable tr:hover {
        background-color: #f5f5f5;
    }

    /* Breaker Buttons */
    .cs_btn_group {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .cs_btn {
        font-size: 16px;
        padding: 8px 12px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.1s;
        font-weight: 400;
        /* color: #fff; */
        /* background-color: #7374c2ff; Inactive color */
        color: #000;
        background-color: #f8f9fa;
        border: 1.8px solid #e0e0e0;
    }

    .cs_btn:hover {
        background-color: #e8eaed;
    }

    .cs_btn.active {
        border: none;
        color: #fff;
        font-weight: 500;
        background-color: #7374c2ff; /* Active color */
        box-shadow: 0 2px 4px rgba(40, 43, 202, 0.4);
        transform: translateY(-1px);
    }
    .cs_btn.active:hover {
        background-color: #4F509B;
    }

    /* CCTV Section */
    .cctv_section {
        display: flex;
        flex-direction: column;
    }

    .cctv_section h2 {
        font-size: 1.5em;
        font-weight: 700;
        margin-bottom: 15px;
        color: #333;
    }

    /* CCTV Icon Button */
    .cctv-icon-btn {
        background-color: #f8f9fa;
        border: 1.8px solid #7374c2ff;
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 5px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        /* color: #2124e4ff; 파란색 */
        color: #7374c2ff;
        font-size: 12px;
        font-weight: 500;
        min-width: 40px;
        min-height: 42px;
    }

    .cctv-icon-btn:hover {
        background-color: #e8eaed;
    }

    /* .cctv-icon-btn {
        cursor: pointer;
        padding: 8px 12px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 500;
        min-width: 40px;
        min-height: 42px;
        transition: background-color 0.3s, transform 0.1s;
        border: none;
        color: #fff;
        background-color: #7374c2ff;
        box-shadow: 0 2px 4px rgba(40, 43, 202, 0.4);
        transform: translateY(-1px);
    }

    .cctv-icon-btn:hover {
        background-color: #4F509B;

    } */

    .cctv-icon-btn:active {
        transform: translateY(0);
        box-shadow: 0 1px 2px rgba(115, 116, 194, 0.3);
    }

    .cctv-icon-btn svg {
        width: 20.5px;
        height: 20.5px;
    }

    /* CCTV 플레이스홀더 스타일 */
    .cctv-placeholder {
        width: 100%;
        height: 360px;
        border: 2px dashed #e0e0e0;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    /* .cctv-placeholder:hover {
        border-color: #7374c2;
        background-color: #f0f2f5;
    } */

    .placeholder-content {
        text-align: center;
        color: #666;
    }

    .placeholder-icon {
        font-size: 3em;
        margin-bottom: 15px;
        opacity: 0.6;
    }

    .placeholder-icon svg {
        width: 60px;
        height: 60px;
        color: #7374c2;
        opacity: 0.7;
    }

    .placeholder-text {
        font-size: 1.1em;
        font-weight: 500;
        line-height: 1.4;
    }

    /* CCTV 스트리밍 영역 스타일 */
    .cctv-stream-area {
        width: 100%;
        position: relative;
    }

    .cctv-canvas {
        width: 100% !important;
        height: 360px !important;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #000;
        display: block;
        object-fit: contain;
    }
    .cctv-video {
        width: 100% !important;
        height: 360px !important;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #000;
        display: block;
        object-fit: contain;
    }

    .cctv-controls {
        margin-top: 10px;
        text-align: center;
    }

    .replay-btn {
        padding: 8px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        background-color: #7374c2;
        color: #fff;
        font-weight: 500;
        transition: background-color 0.3s;
    }

    /* .replay-btn:hover {
        background-color: #2124e4;
    } */

    #cctvIframe {
        width: 100%;
        height: 360px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        flex-grow: 1; /* Allows the iframe to fill the available height */
    }
    /* 행 강조 */
    .gate-row.selected {
        background-color: #eef2ff;
    }

    /* Help Section */
    #id_helpForm {
        position: relative;
        /* width: 100%; */
        max-width: 560px;
        /* margin-top: 10px; */
    }

    #id_help {
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        color: #555;
        font-weight: 500;
    }

    #id_help:hover {
        color: #282bca;
    }

    #id_help .material-symbols-outlined {
        font-size: 22px;
        font-weight: 600;
    }

    .cs_help {
        display: none; /* Initially hidden */
        position: absolute;
        bottom: 150%; /* Position above the help button */
        left: 50%;
        transform: translateX(-50%);
        background-color: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        padding: 10px;
        margin-bottom: 5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        min-width: 250px;
        z-index: 100000;
        line-height: 1.5;
        font-size: 0.9em;
        white-space: nowrap;
    }

    thead th {
        white-space: nowrap;
    }

    tbody td {
        height: fit-content;
    }

    /* IP/Port Chip Style */
    .ip-port-chip {
        display: inline-block;
        padding: 4px 10px;
        background-color: #e8eaed;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 500;
        color: #555;
        white-space: nowrap;
    }
</style>

<div class="cs_frame">
    <div class="cs_card cs_table_container">
        <h2>차단기 제어</h2>
        <table class="henry_datatable" width="100%">
            <thead>
                <!-- 테이블 헤더 행 (1행) -->
                <tr>
                    <th>차단기</th>        <!-- 1열: 차단기 이름 -->
                    <th>현재 상태</th>     <!-- 2열: 현재 상태 텍스트 -->
                    <th style="display: flex; justify-content: center; align-items: center; gap: 6px;">
                        <span style="white-space: nowrap;">상태 변경</span>
                        <div id="id_helpForm">
                            <div id="id_help" style="display: inline-block; width: content-fit" stat="close">
                                <div><span class="material-symbols-outlined help">help_outline</span></div>
                            </div>
                            <div class='cs_help'>
                                - <b>열림/닫힘 버튼</b>을 클릭하여 차단기의 상태를 제어합니다.<br>
                                - <b>보라색</b>으로 표시된 버튼은 차단기의 <b>현재 상태</b>를 나타냅니다.
                            </div>
                        </div>
                    </th>     <!-- 3열: 열림/닫힘 버튼 -->
                    <?php if ($GATE_CCTV == 'true') { ?>
                        <th>CCTV</th>         <!-- 4열: CCTV 재생/정지 버튼 -->
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
            <?php
                include_once $_SERVER["DOCUMENT_ROOT"]."/include/dbdao.php";

                $statusDao = new WB_GATESTATUS_DAO;
                $equipDao = new WB_EQUIP_DAO;

                $equipVo = $equipDao->SELECT("GB_OBSV = '20' AND USE_YN = '1'");
                if( isset($equipVo[0]->{key($equipVo[0])}) )
                {
                    foreach( $equipVo as $v )
                    {
                        $statusVo = $statusDao->SELECT_SINGLE("CD_DIST_OBSV='{$v->CD_DIST_OBSV}'");
                        // 데이터가 없으면 생성
                        if( !isset($statusVo->{key($statusVo)}) )
                        {
                            $statusVo->CD_DIST_OBSV = $v->CD_DIST_OBSV;
                            $statusVo->RegDate = date("Y-m-d H:i:s");
                            $statusVo->Gate = "open"; // 기본 상태를 "열림"으로 설정
                            $statusVo->EType = $v->EType;

                            $statusDao->INSERT($statusVo);
                        }

                        // ===== 테이블 데이터 행 시작 (각 차단기별로 1행씩 생성) =====
                        echo "<tr class='gate-row' data-etype='{$v->EType}'>";

                        // 1열: 차단기 이름 + IP/Port 칩
                        echo "<td>";
                        echo "<div style='display: flex; align-items: center; gap: 8px;'>";
                        echo "<span style='min-width: 150px;'>{$v->NM_DIST_OBSV}</span>";
                        echo "<span class='ip-port-chip'>{$v->ConnIP}:{$v->ConnPort}</span>";
                        echo "</div>";
                        echo "</td>";

                        // 2열: 현재 상태 텍스트
                        $currentStatusLabel = isset($statusVo->Gate) ? ($statusVo->Gate == 'open' ? '열림' : ($statusVo->Gate == 'close' ? '닫힘' : $statusVo->Gate)) : 'unknown';
                        echo "<td id='status-text-{$v->CD_DIST_OBSV}'>{$currentStatusLabel}</td>";

                        // 3열: 상태 변경 버튼 (열림/닫힘)
                        echo "<td>";
                        echo "<div class='cs_btn_group'>";

                        $isOpenActive = strtolower($statusVo->Gate) == "open" ? 'active' : '';
                        $isCloseActive = strtolower($statusVo->Gate) == "close" ? 'active' : '';

                        echo "<button class='cs_btn gate{$v->CD_DIST_OBSV} {$isOpenActive}' data-num='{$v->CD_DIST_OBSV}' data-type='open' data-etype='{$v->EType}'>열림</button>";
                        echo "<button class='cs_btn gate{$v->CD_DIST_OBSV} {$isCloseActive}' data-num='{$v->CD_DIST_OBSV}' data-type='close' data-etype='{$v->EType}'>닫힘</button>";

                        echo "</div>";
                        echo "</td>";

                        // 4열: CCTV 재생/정지 버튼
                        echo "<td style='display: flex; justify-content: center; align-items: center;'>";
                        echo "<button class='cctv-icon-btn' data-num='{$v->CD_DIST_OBSV}' data-etype='{$v->EType}' data-state='play' title='실시간 CCTV 재생'>";
                        echo "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='play-icon'>";
                        echo "<path d='M5 5a2 2 0 0 1 3.008-1.728l11.997 6.998a2 2 0 0 1 .003 3.458l-12 7A2 2 0 0 1 5 19z'/>";
                        echo "</svg>";
                        echo "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='stop-icon' style='display: none;'>";
                        echo "<rect width='18' height='18' x='3' y='3' rx='2'/>";
                        echo "</svg>";
                        echo "</button>";
                        echo "</td>";

                        // // 4열: CCTV 아이콘 버튼 --> canvas & 웹소켓 스트리밍에서 비디오 스트리밍으로 표출함에 따라 UI 변경 (삭제 금지)
                        // if ($GATE_CCTV == 'true') {
                        // echo "<td style='display: flex; justify-content: center; align-items: center;'>";
                        // echo "<button class='cctv-icon-btn' data-num='{$v->CD_DIST_OBSV}' data-etype='{$v->EType}' data-state='play' title='실시간 CCTV 재생'>";
                        // echo "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='lucide lucide-cctv-icon lucide-cctv'>";
                        // echo "<path d='M16.75 12h3.632a1 1 0 0 1 .894 1.447l-2.034 4.069a1 1 0 0 1-1.708.134l-2.124-2.97'/>";
                        // echo "<path d='M17.106 9.053a1 1 0 0 1 .447 1.341l-3.106 6.211a1 1 0 0 1-1.342.447L3.61 12.3a2.92 2.92 0 0 1-1.3-3.91L3.69 5.6a2.92 2.92 0 0 1 3.92-1.3z'/>";
                        // echo "<path d='M2 19h3.76a2 2 0 0 0 1.8-1.1L9 15'/>";
                        // echo "<path d='M2 21v-4'/>";
                        // echo "<path d='M7 9h.01'/>";
                        // echo "</svg>";
                        // echo "</button>";
                        // echo "</td>";
                        // }

                        // ===== 테이블 데이터 행 종료 =====
                        echo "</tr>";
                    }
                }
            ?>
            </tbody>
        </table>
        <!-- <br><br><br>
        <div id="id_helpForm">
            <div id="id_help" stat="close">
                <div><span class="material-symbols-outlined help">help_outline</span></div>
                <div id='id_helpMessage'>도움말 보기</div>
            </div>
            <div class='cs_help'>
                - 열림/닫힘 버튼을 클릭하여 차단기의 상태를 제어합니다.<br>
                - 보라색으로 표시된 버튼은 차단기의 현재 상태를 나타냅니다.
            </div>
        </div> -->
    </div>

<?php if ($GATE_CCTV == 'true') { ?>
    <div class="cs_card cctv_section">
        <h2>실시간 CCTV</h2>

        <!-- 기존 코드 -->
        <!-- <img id="cctvStream" src="http://localhost:17777/cctv_stream" alt="CCTV Stream" style="width:100%; height:auto; border-radius: 8px;"> -->
        <!-- <iframe id="cctvIframe" src="http://192.168.83.234/capture?_cb=2025" title="CCTV 어플리케이션"></iframe> -->

        <!-- <video id="cctv-video" controls autoplay muted></video>

        <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
        <script src="/gate/frame/playCCTV.js"></script> -->

        <!-- CCTV 플레이스홀더 -->
        <div id="cctvPlaceholder" class="cctv-placeholder">
            <div class="placeholder-content">
                <div class="placeholder-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-cctv-icon lucide-cctv">
                        <path d="M16.75 12h3.632a1 1 0 0 1 .894 1.447l-2.034 4.069a1 1 0 0 1-1.708.134l-2.124-2.97"/>
                        <path d="M17.106 9.053a1 1 0 0 1 .447 1.341l-3.106 6.211a1 1 0 0 1-1.342.447L3.61 12.3a2.92 2.92 0 0 1-1.3-3.91L3.69 5.6a2.92 2.92 0 0 1 3.92-1.3z"/>
                        <path d="M2 19h3.76a2 2 0 0 0 1.8-1.1L9 15"/>
                        <path d="M2 21v-4"/>
                        <path d="M7 9h.01"/>
                    </svg>
                </div>
                <div class="placeholder-text">재생 버튼을 클릭하면 이곳에 CCTV 영상이 표출됩니다.</div>
            </div>
        </div>

        <!-- CCTV 스트리밍 영역 -->
        <div id="cctvStreamArea" class="cctv-stream-area" style="display: none;">
            <canvas id="cctvCanvas" class="cctv-canvas"></canvas>
            <!-- <video id="cctv-video" class="cctv-video" controls autoplay muted></video> -->
        </div>
    </div>
<?php } ?>
</div>

<script>
    /* ==== 기존 코드 시작 ==== */

    // // CCTV 이미지 주소 (rtsp를 웹에서 볼 수 없으므로 기존의 http 주소 사용)
    // const cctvBaseUrl = 'http://192.168.83.234/capture';

    // // DB 상태를 확인하고 버튼 색상 및 상태 텍스트를 업데이트하는 함수
    // function updateButtonStates() {
    //     $.ajax({
    //         url: '/gate/frame/check_status.php',
    //         method: 'GET',
    //         dataType: 'json',
    //         success: function(serverStatuses) {
    //             // 서버에서 받은 상태를 기반으로 각 버튼의 상태를 업데이트합니다.
    //             for (const num in serverStatuses) {
    //                 if (serverStatuses.hasOwnProperty(num)) {
    //                     const status = serverStatuses[num].toLowerCase();

    //                     // 해당 차단기의 모든 버튼에서 'active' 클래스 제거
    //                     $(`.gate${num}`).removeClass("active");

    //                     // 현재 상태에 해당하는 버튼에 'active' 클래스 추가
    //                     $(`.gate${num}[data-type='${status}']`).addClass("active");

    //                     // '상태' 텍스트 업데이트
    //                     $(`#status-text-${num}`).text(status);
    //                 }
    //             }
    //         },
    //         error: function(xhr, status, error) {
    //             console.error('상태 확인 실패:', error);
    //         }
    //     });
    // }

    // $(document).ready(function() {
    //     // Handle help tooltip
    //     $("#id_help, #id_helpMessage").on("click", function() {
    //         $(".cs_help").slidetoggle(200);
    //         const helpText = $(".cs_help");
    //         // if ($(this).attr("stat") === "close") {
    //         //     helpText.fadeIn(200);
    //         //     $(this).attr("stat", "open");
    //         // } else {
    //         //     helpText.fadeOut(200);
    //         //     $(this).attr("stat", "close");
    //         // }
    //     });

    //     // Breaker button event handling
    //     $(document).on("click", ".cs_btn[data-type='open'], .cs_btn[data-type='close']", function(e) {
    //         const num = $(this).attr("data-num");
    //         const gate = $(this).attr("data-type");

    //         if (confirm("차단기 상태를 변경하시겠습니까?")) {
    //             // Remove 'active' class from all buttons for this breaker
    //             $(`.gate${num}`).removeClass("active");

    //             // Add 'active' class to the clicked button
    //             $(this).addClass("active");

    //             // Show CCTV for 10 seconds after a successful status change
    //             showWebCamForDuration(10000); // 10000ms = 10초

    //             // 기존 saveGate 함수 호출 (만약 있다면)
    //             if (typeof saveGate === 'function') {
    //                 saveGate(num, gate);
    //             }
    //         }
    //     });
    // });

    // // 페이지 로드 시 초기 상태를 업데이트
    // updateButtonStates();

    // // 5~10초 마다 상태 업데이트를 반복
    // setInterval(updateButtonStates, 8000);

    // /**
    //  * 지정된 시간 동안 웹캠 이미지를 반복 갱신하는 함수
    //  * @param {number} duration - 웹캠을 표시할 시간 (밀리초 단위)
    //  */
    // function showWebCamForDuration(duration) {
    //     const cctvIframe = $('#cctvIframe');

    //     // CCTV 프레임을 보이게 함
    //     cctvIframe.show();

    //     //10초 동안 CCTV 이미지를 반복 갱신하는 타이머
    //     const refreshInterval = setInterval(function() {
    //         const now = new Date();
    //         const timestamp = now.getTime();
    //         cctvIframe.attr('src', `${cctvBaseUrl}?_cb=${timestamp}`);
    //         console.log('CCTV 이미지가 새로운 타임스탬프로 업데이트되었습니다:', timestamp);
    //     }, 1000); // 1초마다 갱신

    //     // 1초 후 타이머를 멈추고 프레임을 숨김
    //     setTimeout(function() {
    //         clearInterval(refreshInterval);
    //         //cctvIframe.hide(); // CCTV 프레임을 다시 숨김
    //         console.log('10초 경과, CCTV 갱신을 중지합니다.');
    //     }, duration);
    // }

    /* ==== 기존 코드 끝 ==== */

	// CCTV 환경변수를 전역 변수로 선언 (gate.js에서 사용)
	// window.cctvHost = '<?= getenv("CCTV_HOST") ?: "localhost" ?>';
    window.cctvHost = '<?= $CCTV_HOST ?>';
    window.cctvPort = '<?= $CCTV_PORT ?>';

    const CCTV_HOST = '<?= $CCTV_HOST ?>';
    const CCTV_PORT = '<?= $CCTV_PORT ?>';
    const GATE_CCTV = '<?= $GATE_CCTV  == 'true' ?>';

	$(document).ready(function() {
		// gate.js의 초기화 함수 호출
		if (typeof initPassiveGate === 'function') {
			initPassiveGate();
		}
	});
</script>

<style>
/* 기존 스타일 그대로 유지하고, CCTV iframe을 기본적으로 숨기도록 추가 */
.cctv_section #cctvIframe {
    display: true; /* CCTV를 기본적으로 숨기기 */
}
</style>
