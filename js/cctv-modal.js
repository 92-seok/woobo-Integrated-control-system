// CCTV 모달창 열기 함수
window.openCCTVModal = function(cctvKey, cctvName, port) {
    // 기존 타이머가 있다면 취소
    if (window.cctvModalTimer) {
        clearTimeout(window.cctvModalTimer);
        window.cctvModalTimer = null;
    }
    
    // 모달창 제목 설정
    $('#cctvModalTitle').text(cctvName + ' CCTV 영상');
    
    // 모달창 내용에 캔버스 추가
    let modalContent = `
        <div style="width: 100%; height: 400px; background-color: #000;">
            <canvas id="modalCanvas${cctvKey}" style="width: 100%; height: 100%;"></canvas>
        </div>
    `;
    
    $('#cctvModalContent').html(modalContent);
    
    // 모달창 표시
    $('#cctvModal').fadeIn(300);
    
    // WebSocket 변수를 함수 스코프에서 선언
    let webSocket;
    
    // 모달창이 표시된 후 영상 스트림 연결
    setTimeout(function() {
        const cctvKeyNum = parseInt(cctvKey);
        // const port = cctvKeyNum < 10 ? `900${cctvKeyNum}` : `90${cctvKeyNum}`;
        
        console.log("port: ", port);

        // WebSocket 연결   
        // webSocket = new WebSocket(`ws://192.168.83.88:${port}`);
        webSocket = new WebSocket(`ws://${cctvHost}:${port}`);

        console.log(`ws://${cctvHost}:${port}`);
        const canvas = document.getElementById(`modalCanvas${cctvKey}`);
        
        // jsmpeg로 영상 스트림 표출
        const jsmpegInstance = new jsmpeg(webSocket, { 
            canvas: canvas,
            autoplay: true
        });
        
        // 모달창이 닫힐 때 WebSocket 연결 해제 및 타이머 취소
        $('#cctvModal').off('hidden.bs.modal').on('hidden.bs.modal', function() {
            if (window.cctvModalTimer) {
                clearTimeout(window.cctvModalTimer);
                window.cctvModalTimer = null;
            }
            if (webSocket && webSocket.readyState === WebSocket.OPEN) {
                webSocket.close();
            }
        });
        
        // 모달창 닫기 버튼 클릭 시에도 연결 해제 및 타이머 취소
        $('.cctv-modal-close').off('click').on('click', function() {
            if (window.cctvModalTimer) {
                clearTimeout(window.cctvModalTimer);
                window.cctvModalTimer = null;
            }
            if (webSocket && webSocket.readyState === WebSocket.OPEN) {
                webSocket.close();
            }
            $('#cctvModal').fadeOut(300);
        });
    }, 100);
    
    // 10초 후 자동으로 모달창 닫기 (타이머 ID 저장)
    window.cctvModalTimer = setTimeout(function() {
        $('#cctvModal').fadeOut(300);
        // WebSocket 연결 해제
        if (webSocket && webSocket.readyState === WebSocket.OPEN) {
            webSocket.close();
        }
        // 타이머 변수 초기화
        window.cctvModalTimer = null;
    }, 10000);
}