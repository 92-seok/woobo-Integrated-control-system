const Stream = require("node-rtsp-stream");
const cam1 = "rtsp://admin:pass13579@@192.168.80.54:554/stream1";
const cam2 = "rtsp://admin:pass13579@@192.168.80.54:554/stream2";
const cam3 = "rtsp://admin:pass13579@@192.168.80.54:554/stream3";

// 커밋 확인
// test 123 123

stream1 = new Stream({
  name: "camTest1",
  streamUrl: cam1,
  wsPort: 9001,
  width: 1920,
  height: 1080,
});

stream2 = new Stream({
  name: "camTest2",
  streamUrl: cam2,
  wsPort: 9002,
  width: 640,
  height: 480,
});

stream3 = new Stream({
  name: "camTest3",
  streamUrl: cam3,
  wsPort: 9004,
  width: 320,
  height: 240,
});

// 애러체크
streamErrorCheck(stream1);
streamErrorCheck(stream2);
streamErrorCheck(stream3);


// ffmpeg에서 Error가 발생하면 메시지를 비교하여
// restartStream 함수를 실행하여 재기동을 한다.
function streamErrorCheck(streamName){
  streamName.mpeg1Muxer.on("ffmpegStderr", (data) => {
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


