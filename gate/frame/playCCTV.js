// playCCTV.js
document.addEventListener('DOMContentLoaded', (event) => {
    // WebRTC나 HLS로 변환된 스트림 주소
    const streamUrl = 'rtsp://admin:pass13579@@192.168.80.54:554/stream1';
    //const streamUrl = 'http://rtsp://admin:woobo4781!@192.168.80.20:554/Streaming/Channels/101';

    // 비디오 요소를 가져옵니다.
    const video = document.getElementById('cctv-video');
    
    // 브라우저가 HLS를 지원하는지 확인합니다.
    if (Hls.isSupported()) {
        var hls = new Hls();
        hls.loadSource(streamUrl);
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        // HLS.js를 지원하지 않는 브라우저(예: Safari)용
        video.src = streamUrl;
        video.addEventListener('loadedmetadata', function() {
            video.play();
        });
    } else {
        console.error("브라우저에서 HLS를 지원하지 않습니다.");
    }
});