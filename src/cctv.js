// = HIKVISION CCTV 정보 =
// IP : 192.168.80.20
// ID : admin
// PW : pass13579@
// = 파로스 CCTV =
// IP: 192.168.80.54
// ID : admin
// PW : pass13579@

// console.info("start cctv app.js");

// 환경변수 로드
import dotenv from 'dotenv';
dotenv.config();

import Stream from 'node-rtsp-stream';
import express from 'express';
import cors from 'cors';
import { spawn } from 'child_process';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';
import { dirname } from 'path';

// ES6 모듈에서 __dirname 대체
const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

// 환경변수 기본값 설정
const CCTV_ID = process.env.CCTV_ID || 'admin';
const CCTV_PW = process.env.CCTV_PW || 'pass13579@';

const CCTV_WS_IP = '192.168.0.80';
const CCTV_WS_PORT = 9003;
// const CCTV_WS_IP = 'localhost';
// const CCTV_WS_PORT = 9001;

// // EJS 설정
// app.set('view engine', 'ejs');
// app.set('views', __dirname);

// 캡처 상태 관리
let captureStatus = {
	hikvision: false,
	pharos: false
};

// UDP 포트 설정
const UDP_PORTS = {
	hikvision: 5000,
	pharos: 5001
};

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

// 스트림 재시작 함수
Stream.prototype.restartStream = function () {
	console.log('Restarting stream...');
	this.stop(); // Stop the current stream
	this.startMpeg1Stream(); // Restart the stream
	this.pipeStreamToSocketServer(); // Re-pipe the stream to the socket server
	console.log('Stream restarted successfully.');
};

const stream1 = new Stream({
	name: 'hikvision',
	streamUrl: 'rtsp://admin:pass13579@@192.168.80.20/stream1',
	wsPort: 9001,
	ffmpegOptions: {
		// RTSP 스트림 수신 프로토콜을 TCP로 설정함 (안정성 향상)
		'-rtsp_transport': 'tcp',
		// 출력 포맷을 mpeg1video로 지정함 (웹소켓 스트리밍 표준)
		'-f': 'mpeg1video',
		// 비디오 비트레이트를 10Mbps로 설정함 (고화질)
		'-b:v': '10000k',
		// 최대 비트레이트를 10Mbps로 제한하여 대역폭 급증을 방지함
		'-maxrate': '10000k',
		// 디코딩 버퍼 크기를 10Mb로 설정하여 비트레이트 변동을 제어함
		'-bufsize': '10000k',
		// 오디오 스트림을 비활성화함
		'-an': '',
		// 출력 영상의 프레임률을 30fps로 설정함
		'-r': '30',
	},
});

stream1.wsServer.on('connection', (ws, req) => {
	// 웹소켓 연결 시
	const ip = req.headers['x-forwarded-for'] || req.connection.remoteAddress;
	console.log('새로운 클라이언트 접속', ip);

	ws.on('message', (message) => {
		// 클라이언트로부터 메시지
		console.log(message.toString());
	});

	ws.on('error', (error) => {
		// 에러 시
		console.error(error);
	});

	ws.on('close', () => {
		// 연결 종료 시
		console.log('클라이언트 접속 해제', ip);
		clearInterval(ws.interval);
	});
});

const stream2 = new Stream({
	name: 'pharos',
	streamUrl: 'rtsp://admin:pass13579@@192.168.80.54/stream2',
	//streamUrl: 'rtsp://210.99.70.120:1935/live/cctv001.stream',
	wsPort: 9002,
	ffmpegOptions: {
		// RTSP 스트림 수신 프로토콜을 TCP로 설정함 (안정성 향상)
		'-rtsp_transport': 'tcp',
		// 출력 포맷을 mpeg1video로 지정함 (웹소켓 스트리밍 표준)
		'-f': 'mpeg1video',
		// 비디오 비트레이트를 10Mbps로 설정함 (고화질)
		'-b:v': '10000k',
		// 최대 비트레이트를 10Mbps로 제한하여 대역폭 급증을 방지함
		'-maxrate': '10000k',
		// 디코딩 버퍼 크기를 10Mb로 설정하여 비트레이트 변동을 제어함
		'-bufsize': '10000k',
		// 오디오 스트림을 비활성화함
		'-an': '',
		// 출력 영상의 프레임률을 30fps로 설정함
		'-r': '30',
	},
});

stream2.wsServer.on('connection', (ws, req) => {
	// 웹소켓 연결 시
	const ip = req.headers['x-forwarded-for'] || req.connection.remoteAddress;
	console.log('새로운 클라이언트 접속', ip);

	ws.on('message', (message) => {
		// 클라이언트로부터 메시지
		console.log(message.toString());
	});

	ws.on('error', (error) => {
		// 에러 시
		console.error(error);
	});

	ws.on('close', () => {
		// 연결 종료 시
		console.log('클라이언트 접속 해제', ip);
		clearInterval(ws.interval);
	});
});

// ffmpeg에서 Error가 발생하면 메시지를 비교하여
// restartStream 함수를 실행하여 재기동을 한다.
function streamErrorCheck(streamName) {
	streamName.stream.on('close', () => {
		console.warn('Stream closed, restarting...');
		streamName.restartStream();
	});
	streamName.stream.on('error', (err) => {
		console.error('Stream error:', err);
		streamName.restartStream();
	});
	streamName.mpeg1Muxer.on('ffmpegStderr', (data) => {
		if (data.includes('muxing overhead')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Could not write header for output file')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Invalid input file')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('matches no streams')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Output file is empty')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Unknown encoder')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Trailing options were found')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('No pixel format specified')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Could not open file')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Could not get frame')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Could not find tag for codec foo in stream')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Streamcopy requested for')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes("WASAPI can't initialize")) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
		if (data.includes('Non-monotonous DTS')) {
			console.log('Logging Time : ', getDateAndTime());
			console.log(data);
			streamName.restartStream();
		}
	});
}

// === Express 서버 설정 ===
const app = express();
app.use(cors());
app.use(express.json());

// 정적 파일 서빙 (js 폴더 포함)
app.use('/js', express.static(path.join(__dirname, 'js')));

/// /js 경로를 정적 서빙하므로 하단 라우팅들은 불필요
// // JavaScript 라이브러리 서빙
// app.get('/js/jsmpeg.min.js', (req, res) => {
// 	res.sendFile(path.join(__dirname, 'jsmpeg.min.js'));
// });
// // jsmpg.js 라이브러리 서빙
// app.get('/js/jsmpg.js', (req, res) => {
// 	res.setHeader('Content-Type', 'application/javascript');
// 	res.sendFile(path.join(__dirname, 'js/jsmpg.js'));
// });

// 이미지 저장 디렉토리 생성
const captureDir = path.join(__dirname, 'captures');
if (!fs.existsSync(captureDir)) {
	fs.mkdirSync(captureDir, { recursive: true });
}

// 고품질 실시간 스트림 API
app.get('/stream-high', (req, res) => {
	const { conn_ip, duration } = req.query;

	console.log("conn_ip, duration", conn_ip, duration);

	// let rtspUrl;
	// if (camera === 'hikvision') {
	// 	rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	// } else if (camera === 'pharos') {
	// 	rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	// } else {
	// 	return res.status(400).json({ error: 'Invalid camera' });
	// }

	const RTSP_URL = `rtsp://${CCTV_ID}:${CCTV_PW}@${conn_ip}/stream1`;

	res.writeHead(200, {
		'Content-Type': 'video/mp4',
		'Cache-Control': 'no-cache',
		'Connection': 'keep-alive',
		'Access-Control-Allow-Origin': '*',
		'Accept-Ranges': 'bytes'
	});

	const ffmpeg = spawn('ffmpeg', [
		'-i', RTSP_URL,
		'-rtsp_transport', 'tcp',
		'-t', duration.toString(),
		'-c:v', 'libx264',
		'-preset', 'ultrafast',
		'-tune', 'zerolatency',
		'-b:v', '2000k',
		'-maxrate', '2000k',
		'-bufsize', '2000k',
		'-vf', 'scale=1280:720',
		'-r', '30',
		'-g', '30',
		'-an',
		'-f', 'mp4',
		'-frag_duration', '1000000',
		'-movflags', 'frag_keyframe+empty_moov',
		'pipe:1'
	]);

	ffmpeg.stdout.pipe(res);

	ffmpeg.stderr.on('data', (data) => {
		console.log(`FFmpeg HIGH stderr: ${data}`);
	});

	ffmpeg.on('close', (code) => {
		console.log(`FFmpeg HIGH process exited with code ${code}`);
		if (!res.headersSent) {
			res.end();
		}
	});

	ffmpeg.on('error', (err) => {
		console.error('FFmpeg HIGH error:', err);
		if (!res.headersSent) {
			res.status(500).end('Stream error');
		}
	});

	req.on('close', () => {
		console.log('Client disconnected from HIGH quality stream');
		ffmpeg.kill('SIGKILL');
	});
});

// 저품질 실시간 스트림 API (HTTP)
app.get('/stream-low', (req, res) => {
	const { conn_ip, duration } = req.query;

	// let rtspUrl;
	// if (camera === 'hikvision') {
	// 	rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	// } else if (camera === 'pharos') {
	// 	rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	// } else {
	// 	return res.status(400).json({ error: 'Invalid camera' });
	// }

	const RTSP_URL = `rtsp://${CCTV_ID}:${CCTV_PW}@${conn_ip}/stream1`;

	res.writeHead(200, {
		'Content-Type': 'video/mp4',
		'Cache-Control': 'no-cache',
		'Connection': 'keep-alive'
	});

	const ffmpeg = spawn('ffmpeg', [
		'-i', RTSP_URL,
		'-rtsp_transport', 'tcp',
		'-t', duration.toString(),
		'-c:v', 'libx264',
		'-preset', 'ultrafast',
		'-tune', 'zerolatency',
		'-b:v', '500k',
		'-maxrate', '500k',
		'-bufsize', '500k',
		'-vf', 'scale=320:240',
		'-r', '10',
		'-an',
		'-f', 'mp4',
		'-frag_duration', '1000000',
		'-movflags', 'frag_keyframe+empty_moov',
		'pipe:1'
	]);

	ffmpeg.stdout.pipe(res);

	ffmpeg.stderr.on('error', (err) => {
		console.error('FFmpeg LOW error:', err);
	});

	ffmpeg.on('close', () => {
		res.end();
	});

	req.on('close', () => {
		if (!ffmpeg.killed) {
			ffmpeg.kill('SIGTERM');
		}
	});
});

// 즉시 캡처 API (스트리밍 중에 사용)
app.get('/snap/:camera', (req, res) => {
	const { camera } = req.params;
	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const filename = `${camera}_${timestamp}.jpg`;
	const outputPath = path.join(captureDir, filename);

	let rtspUrl;
	if (camera === 'hikvision') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	} else if (camera === 'pharos') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	} else {
		return res.status(400).json({ error: 'Invalid camera' });
	}

	const ffmpeg = spawn('ffmpeg', [
		'-i', rtspUrl,
		'-rtsp_transport', 'tcp',
		'-frames:v', '1',
		'-q:v', '2',
		'-y',
		outputPath
	]);

	ffmpeg.on('close', (code) => {
		if (code === 0) {
			res.json({
				success: true,
				image: `/captures/${filename}`,
				timestamp: new Date()
			});
		} else {
			res.status(500).json({ error: 'Capture failed' });
		}
	});
});

// 기본 10초 캡처 API
app.get('/capture/:camera', async (req, res) => {
	const { camera } = req.params;
	const duration = 10;

	// 캡처 중복 방지 체크
	if (captureStatus[camera]) {
		return res.status(429).json({
			error: `${camera} is currently capturing. Please wait.`,
			status: 'busy'
		});
	}

	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const outputDir = path.join(captureDir, `${camera}_${timestamp}`);

	// 카메라별 RTSP URL 설정
	let rtspUrl;
	if (camera === 'hikvision') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	} else if (camera === 'pharos') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	} else {
		return res.status(400).json({ error: 'Invalid camera. Use hikvision or pharos' });
	}

	// 캡처 상태 설정
	captureStatus[camera] = true;

	// 출력 디렉토리 생성
	if (!fs.existsSync(outputDir)) {
		fs.mkdirSync(outputDir, { recursive: true });
	}

	try {
		const images = await captureImagesFromStream(rtspUrl, duration, outputDir);
		res.json({
			success: true,
			camera: camera,
			duration: duration,
			captured: images.length,
			images: images,
			message: `${duration}초 동안 ${images.length}개 이미지 캡처 완료`
		});
	} catch (error) {
		console.error('Capture error:', error);
		res.status(500).json({ error: 'Image capture failed', details: error.message });
	} finally {
		// 캡처 상태 해제
		captureStatus[camera] = false;
	}
});

// 커스텀 시간 캡처 API
app.get('/capture/:camera/:duration', async (req, res) => {
	const { camera, duration } = req.params;

	// 캡처 중복 방지 체크
	if (captureStatus[camera]) {
		return res.status(429).json({
			error: `${camera} is currently capturing. Please wait.`,
			status: 'busy'
		});
	}

	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const outputDir = path.join(captureDir, `${camera}_${timestamp}`);

	// 카메라별 RTSP URL 설정
	let rtspUrl;
	if (camera === 'hikvision') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	} else if (camera === 'pharos') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	} else {
		return res.status(400).json({ error: 'Invalid camera. Use hikvision or pharos' });
	}

	// 캡처 상태 설정
	captureStatus[camera] = true;

	// 출력 디렉토리 생성
	if (!fs.existsSync(outputDir)) {
		fs.mkdirSync(outputDir, { recursive: true });
	}

	try {
		const images = await captureImagesFromStream(rtspUrl, duration, outputDir);
		res.json({
			success: true,
			camera: camera,
			duration: duration,
			captured: images.length,
			images: images,
			message: `${duration}초 동안 ${images.length}개 이미지 캡처 완료`
		});
	} catch (error) {
		console.error('Capture error:', error);
		res.status(500).json({ error: 'Image capture failed', details: error.message });
	} finally {
		// 캡처 상태 해제
		captureStatus[camera] = false;
	}
});

// 비디오 캡처 API (10초 동안 촬영하여 브라우저에서 재생 가능한 MP4 반환)
app.get('/video/:camera', async (req, res) => {
	const { camera } = req.params;
	const duration = 10;

	// 캡처 중복 방지 체크
	if (captureStatus[camera]) {
		return res.status(429).json({
			error: `${camera} is currently capturing. Please wait.`,
			status: 'busy'
		});
	}

	const timestamp = new Date().toISOString().replace(/[:.]/g, '-');
	const outputPath = path.join(captureDir, `${camera}_${timestamp}.mp4`);

	// 카메라별 RTSP URL 설정
	let rtspUrl;
	if (camera === 'hikvision') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.20/stream1';
	} else if (camera === 'pharos') {
		rtspUrl = 'rtsp://admin:pass13579@@192.168.80.54/stream1';
	} else {
		return res.status(400).json({ error: 'Invalid camera. Use hikvision or pharos' });
	}

	// 캡처 상태 설정
	captureStatus[camera] = true;

	try {
		const videoPath = await captureVideoFromStream(rtspUrl, duration, outputPath);

		// 파일 크기 확인
		const stats = fs.statSync(videoPath);
		const fileSizeInMB = (stats.size / (1024 * 1024)).toFixed(2);

		res.setHeader('Content-Type', 'video/mp4');
		res.setHeader('Content-Length', stats.size);
		res.setHeader('Content-Disposition', `inline; filename="${camera}_${timestamp}.mp4"`);

		const stream = fs.createReadStream(videoPath);
		stream.pipe(res);

		// 응답 완료 후 임시 파일 삭제 (선택사항)
		stream.on('end', () => {
			setTimeout(() => {
				try {
					fs.unlinkSync(videoPath);
				} catch (err) {
					console.log('임시 파일 삭제 실패:', err.message);
				}
			}, 1000);
		});

		console.log(`비디오 스트리밍 완료: ${camera}, 파일 크기: ${fileSizeInMB}MB`);

	} catch (error) {
		console.error('Video capture error:', error);
		res.status(500).json({ error: 'Video capture failed', details: error.message });
	} finally {
		// 캡처 상태 해제
		captureStatus[camera] = false;
	}
});

// 캡처된 이미지 파일 서빙
app.use('/captures', express.static(captureDir));

// captures 디렉토리 목록 API
app.get('/captures/', (req, res) => {
	try {
		const files = fs.readdirSync(captureDir);
		res.json({ success: true, files: files });
	} catch (error) {
		res.status(500).json({ error: 'Failed to read captures directory' });
	}
});

// favicon 처리
app.get('/favicon.ico', (req, res) => {
	res.status(204).send();
});

// 메인 페이지 서빙
app.get('/', (req, res) => {
	res.sendFile(path.join(__dirname, 'cctv.html'));
});

app.get('/cctv', (req, res) => {
	res.sendFile(path.join(__dirname, 'cctv.html'));
});

app.get('/test', (req, res) => {
	res.sendFile(path.join(__dirname, 'cctv.html'));
});

// Express 서버 시작
const PORT = process.env.PORT || 3001;
app.listen(PORT, () => {
	console.log(`CCTV Capture Server running on port ${PORT}`);
	console.log(`API 사용법:`);
	console.log(`- HIKVISION 10초 이미지 캡처: http://localhost:${PORT}/capture/hikvision`);
	console.log(`- 파로스 5초 이미지 캡처: http://localhost:${PORT}/capture/pharos/5`);
	console.log(`- HIKVISION 10초 비디오: http://localhost:${PORT}/video/hikvision`);
	console.log(`- 파로스 10초 비디오: http://localhost:${PORT}/video/pharos`);
});

// ffmpeg를 사용한 이미지 캡처 함수
function captureImagesFromStream(rtspUrl, duration, outputDir) {
	return new Promise((resolve, reject) => {
		const images = [];
		const ffmpegArgs = [
			'-i', rtspUrl,
			'-rtsp_transport', 'tcp',
			'-t', duration.toString(),
			'-vf', 'fps=1',
			'-y',
			path.join(outputDir, 'frame_%03d.jpg')
		];

		console.log(`Starting ${duration}초 capture from ${rtspUrl}`);
		const ffmpeg = spawn('ffmpeg', ffmpegArgs);

		ffmpeg.stderr.on('data', (data) => {
			const output = data.toString();
			console.log('ffmpeg:', output);
		});

		ffmpeg.on('close', (code) => {
			console.log(`ffmpeg process exited with code ${code}`);

			if (code === 0) {
				try {
					const files = fs.readdirSync(outputDir);
					const imageFiles = files.filter(file => file.endsWith('.jpg')).sort();

					imageFiles.forEach(file => {
						images.push({
							filename: file,
							url: `/captures/${path.basename(outputDir)}/${file}`,
							timestamp: new Date()
						});
					});

					resolve(images);
				} catch (error) {
					reject(error);
				}
			} else {
				reject(new Error(`ffmpeg exited with code ${code}`));
			}
		});

		ffmpeg.on('error', (error) => {
			console.error('ffmpeg error:', error);
			reject(error);
		});

		setTimeout(() => {
			if (!ffmpeg.killed) {
				ffmpeg.kill('SIGTERM');
				reject(new Error('Capture timeout'));
			}
		}, (duration + 5) * 1000);
	});
}

// ffmpeg를 사용한 비디오 캡처 함수 (용량 최적화)
function captureVideoFromStream(rtspUrl, duration, outputPath) {
	return new Promise((resolve, reject) => {
		const ffmpegArgs = [
			'-i', rtspUrl,
			'-rtsp_transport', 'tcp',
			'-t', duration.toString(),
			'-c:v', 'libx264',
			'-preset', 'fast',
			'-crf', '28',
			'-maxrate', '500k',
			'-bufsize', '1000k',
			'-vf', 'scale=640:360',
			'-r', '15',
			'-an',
			'-movflags', '+faststart',
			'-y',
			outputPath
		];

		console.log(`Starting ${duration}초 video capture from ${rtspUrl}`);
		const ffmpeg = spawn('ffmpeg', ffmpegArgs);

		ffmpeg.stderr.on('data', (data) => {
			const output = data.toString();
			// 중요한 로그만 출력 (용량/시간 관련)
			if (output.includes('time=') || output.includes('size=') || output.includes('bitrate=')) {
				console.log('ffmpeg video:', output.trim());
			}
		});

		ffmpeg.on('close', (code) => {
			console.log(`ffmpeg video process exited with code ${code}`);

			if (code === 0) {
				if (fs.existsSync(outputPath)) {
					resolve(outputPath);
				} else {
					reject(new Error('Video file was not created'));
				}
			} else {
				reject(new Error(`ffmpeg exited with code ${code}`));
			}
		});

		ffmpeg.on('error', (error) => {
			console.error('ffmpeg video error:', error);
			reject(error);
		});

		// 타임아웃 설정 (duration + 10초 여유)
		setTimeout(() => {
			if (!ffmpeg.killed) {
				ffmpeg.kill('SIGTERM');
				reject(new Error('Video capture timeout'));
			}
		}, (duration + 10) * 1000);
	});
}

// 애러체크
streamErrorCheck(stream1);
streamErrorCheck(stream2);
