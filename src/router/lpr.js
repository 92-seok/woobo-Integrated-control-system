import express from 'express';

import { logger } from '../util/logger.js';
//import * as wbLib from "../util/wbLib.js";
import { getParkingData } from '../parking/parking.js';

const lpr = express.Router();

// LPR API
lpr.use(express.json());

lpr.post(`/lpr`, async (req, res, next) => {
    try {
        if (!req.body || Object.keys(req.body).length === 0) {
            logger.error(`잘못된 요청: JSON 데이터가 없습니다.`);
            return res.status(400).send({
                error: '잘못된 요청: 데이터가 없습니다.',
            });
        }

        logger.info(`JSON 응답 확인 ::: ${req.body.serial}, ${req.body.date}, ${req.body.carNum}`);
        //console.log(wbLib.getToday(), 'json 응답 확인 ::: ', req.body.serial, ', ', req.body.date, ', ', req.body.carNum);

        const jsonData = req.body;

        // 필수 필드 검증
        if (!jsonData.serial || !jsonData.date || !jsonData.carNum) {
            logger.error(`잘못된 JSON 요청: serial, date, carNum은 필수 데이터입니다.`);
            return res.status(400).send({
                error: '잘못된 요청: serial, date, carNum은 필수 데이터입니다.',
            });
        }

        let lprData = {
            serial: jsonData.serial,
            date: jsonData.date,
            carNum: jsonData.carNum,
            carBin: jsonData.carBin,
        };

        res.status(200).json({
            resultCode: 200,
            serial: lprData.serial,
            carNum: lprData.carNum,
            date: lprData.date,
        });

        await getParkingData(lprData);
    } catch (error) {
        res.status(400).json({
            resultCode: 400,
            resultMessage: error.message,
        });
        next(error);
    }
});

export { lpr };
