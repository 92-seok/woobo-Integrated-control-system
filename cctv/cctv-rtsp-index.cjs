const Stream = require('node-rtsp-stream');
const cam1 = 'rtsp://admin:admin@192.168.80.54:554/stream2';
// const cam2 = 'rtsp://admin:1234@220.82.56.2:4020/video2';
// const cam3 = 'rtsp://admin:1234@210.91.119.126:4020/video2';
// const cam4 = 'rtsp://admin:1234@61.84.99.226:4020/video2';
// const cam5 = 'rtsp://admin:1234@220.70.118.188:4020/video2';
// const cam6 = 'rtsp://admin:1234@14.54.127.165:4020/video2';
// const cam7 = 'rtsp://admin:1234@59.29.127.69:4020/video2';
// const cam8 = 'rtsp://admin:1234@210.123.199.245:4020/video2';
// const cam9 = 'rtsp://admin:1234@121.159.32.189:4020/video2';
// const cam10 = 'rtsp://admin:1234@125.137.169.35:4020/video2';

// 현재 날짜 시간
function getDateAndTime() {
    const currentDate = new Date();
    const year = currentDate.getFullYear();
    const month = ('0' + (currentDate.getMonth() + 1)).slice(-2); // 월은 0부터 시작하므로 1을 더하고, 두 자리로 맞추기 위해 slice 사용
    const day = ('0' + currentDate.getDate()).slice(-2); // 일도 두 자리로 맞추기 위해 slice 사용
    const hours = ('0' + currentDate.getHours()).slice(-2); // 시간도 두 자리로 맞추기 위해 slice 사용
    const minutes = ('0' + currentDate.getMinutes()).slice(-2); // 분도 두 자리로 맞추기 위해 slice 사용
    const seconds = ('0' + currentDate.getSeconds()).slice(-2); // 초도 두 자리로 맞추기 위해 slice 사용

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

Stream.prototype.restartStream = function () {
    console.log('Restarting stream...');
    this.stop(); // Stop the current stream
    this.startMpeg1Stream(); // Restart the stream
    this.pipeStreamToSocketServer(); // Re-pipe the stream to the socket server
    console.log('Stream restarted successfully.');
};

function newStream() {
    return new Stream({
        name: 'camTest1',
        streamUrl: cam1,
        wsPort: 9000,
        //width: 300,
        //height: 240,
        width: 640,
        height: 480,
        ffmpegPath: 'ffmpeg',
        // ffmpegPath: 'ffmpeg',    // ffmpeg가 PATH에 설정되어 있는 경우
        // ffmpegPath: 'C:/ffmpeg/bin/ffmpeg.exe', // ffmpeg의 절대경로를 지정하는 경우
        ffmpegOptions: {
            // options ffmpeg flags
            // '-stats': '', // an option with no neccessary value uses a blank string
            // '-r': 24, // options with required values specify the value after the key
            '-rtsp_transport': 'tcp',
            // '-i': cam1,
            '-f': 'mpeg1video',
            '-b:v': '1000k',
            '-maxrate': '1000k',
            '-bufsize': '1000k',
            '-an': '',
            '-r': '24',
        },
    });
}

var stream1 = newStream();

// stream2 = new Stream({
//     name: 'camTest2',
//     streamUrl: cam2,
//     wsPort: 9002,
//     width: 1920,
//     height: 1080,
// });
//
// stream3 = new Stream({
//     name: 'camTest3',
//     streamUrl: cam3,
//     wsPort: 9003,
//     width: 1920,
//     height: 1080,
// });
//
// stream4 = new Stream({
//     name: 'camTest4',
//     streamUrl: cam4,
//     wsPort: 9004,
//     width: 1920,
//     height: 1080,
// });
// stream5 = new Stream({
//     name: 'camTest5',
//     streamUrl: cam5,
//     wsPort: 9005,
//     width: 1920,
//     height: 1080,
// });
// stream6 = new Stream({
//     name: 'camTest6',
//     streamUrl: cam6,
//     wsPort: 9006,
//     width: 1920,
//     height: 1080,
// });
// stream7 = new Stream({
//     name: 'camTest7',
//     streamUrl: cam7,
//     wsPort: 9007,
//     width: 1920,
//     height: 1080,
// });
// stream8 = new Stream({
//     name: 'camTest8',
//     streamUrl: cam8,
//     wsPort: 9008,
//     width: 1920,
//     height: 1080,
// });
// stream9 = new Stream({
//     name: 'camTest9',
//     streamUrl: cam9,
//     wsPort: 9009,
//     width: 1920,
//     height: 1080,
// });
// stream10 = new Stream({
//     name: 'camTest10',
//     streamUrl: cam10,
//     wsPort: 9010,
//     width: 1920,
//     height: 1080,
// });
// 애러체크
streamErrorCheck(stream1);
// streamErrorCheck(stream2);
// streamErrorCheck(stream3);

// ffmpeg에서 Error가 발생하면 메시지를 비교하여
// restartStream 함수를 실행하여 재기동을 한다.
function streamErrorCheck(streamName) {
    streamName.stream.on('close', () => {
        console.warn('Stream closed, restarting...');
        this.restartStream();
    });
    streamName.stream.on('error', (err) => {
        console.error('Stream error:', err);
        this.restartStream();
    });
    streamName.mpeg1Muxer.on('ffmpegStderr', (data) => {
        if (data.includes('muxing overhead')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Could not write header for output file')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Invalid input file')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('matches no streams')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Output file is empty')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Unknown encoder')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Trailing options were found')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('No pixel format specified')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Could not open file')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Could not get frame')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Could not find tag for codec foo in stream')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Streamcopy requested for')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes("WASAPI can't initialize")) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
        if (data.includes('Non-monotonous DTS')) {
            console.log('Logging Time : ', getDateAndTime());
            console.log(data);
            this.restartStream();
        }
    });
}
