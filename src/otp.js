import mariadb from 'mysql';
import express from 'express';
import cors from 'cors';
import bodyParser from 'body-parser';
import * as dao from './dbjs/dao-js.js';
import path from 'path';
import fs from 'fs';
import dotenv from 'dotenv';

dotenv.config();
const DB_HOST = process.env.DB_HOST ?? '127.0.0.1';
const DB_PORT = process.env.DB_PORT ?? 3306;
const DB_NAME = process.env.DB_NAME ?? 'warning';
const DB_USERNAME = process.env.DB_USERNAME ?? 'userWooboWeb';
const DB_PASSWORD = process.env.DB_PASSWORD ?? 'wooboWeb!@';
const NODE_HOST = process.env.NODE_HOST ?? '127.0.0.1';
const NODE_PORT = process.env.NODE_PORT ?? 5555;
console.info('DB_HOST:', DB_HOST);
console.info('DB_PORT:', DB_PORT);
console.info('NODE_HOST:', NODE_HOST);
console.info('NODE_PORT:', NODE_PORT);

const app = express();

app.use(cors());
app.use(bodyParser.json({ limit: '50mb' }));
// bodyParser를 사용해 raw body를 처리
app.use(bodyParser.urlencoded({ extended: true, limit: '50mb' }));

// region connect database
// 데이터베이스 연결 관리
let dbWarning;

function handleDisconnect() {
    dbWarning = mariadb.createConnection({
        // host: "127.0.0.1",
        // host: "192.168.80.80", // dev
        // port: "3306",
        // database: "warning",
        // user: "userWooboWeb",
        // password: "wooboWeb!@"
        host: DB_HOST,
        port: DB_PORT,
        database: DB_NAME,
        user: DB_USERNAME,
        password: DB_PASSWORD,
    });

    dbWarning.connect((err) => {
        if (err) {
            console.error('Error connecting to mariadb database(DB Name : warning)', err);
            setTimeout(handleDisconnect, 2000); // 연결 실패 시 2초 후 재시도
            return;
        }
        console.log('Connected to mariadb database(DB Name : warning)');
    });

    dbWarning.on('error', function (err) {
        console.log('db error', err);
        if (err.code === 'PROTOCOL_CONNECTION_LOST') {
            // 연결이 끊겼을 때 재연결 시도
            handleDisconnect();
        } else {
            throw err; // 다른 오류는 계속해서 예외를 발생시킵니다.
        }
    });
}

console.log('START handleDisconnect');
handleDisconnect(); // 연결 초기화 및 관리 시작
// endregion

// 서버 시작
app.listen(NODE_PORT, '0.0.0.0', () => {
    console.log(`Express server is running on port ${NODE_PORT}`);
});

// 루트 경로에 대한 GET 요청 처리
app.get('/', (req, res) => {
    res.send('Welcome to the Express server!');
});

// region 데이터 베이스 값 전달
/************** 데이터 베이스 값 전달 *******************/
// region OTP 검증을 위한 POST 요청 처리
app.post('/verify-otp', (req, res) => {
    console.log('verify-otp');
    const { indexNum, typingNum } = req.body;
    console.log('indexNum, typingNum: ', indexNum, typingNum);
    dao.verifyOtp(dbWarning, indexNum, typingNum, (err, isValid) => {
        if (err) {
            console.error('Database query error:', err);
            return res.status(500).json({ error: 'Internal server error' });
        }
        res.json({ success: isValid });
    });
});
// endregion
/***************************************************/
// endregion

// region
/************** 이미지 저장 *******************/
// bodyParser를 사용해 raw body를 처리

// 이미지 저장을 위한 라우트
app.post('/save-image', (req, res) => {
    console.log('save-image');
    // 이미지 데이터와 파일명 받기
    const { imageData, filename } = req.body;
    // 이미지 데이터에서 Base64 부분만 분리
    const base64Data = imageData.replace(/^data:image\/png;base64,/, '');

    // 파일 경로 설정
    const filePath = path.join(__dirname, 'displayImage', filename);

    // 파일 저장
    fs.writeFile(filePath, base64Data, 'base64', (error) => {
        if (error) {
            console.error(error);
            return res.status(500).send('Error saving the image');
        }
        res.send({ message: 'Image saved successfully', path: filePath });
    });
});

// endregion
