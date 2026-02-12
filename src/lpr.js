import express from 'express';
import dotenv from 'dotenv';
import cors from 'cors';

import { logger } from './util/logger.js';

import { config } from './config/config.js';
import { db } from './db/dbConn.js';
import { morganMiddleware } from './middleware/morganMiddleware.js';
import * as wbLib from './util/wbLib.js';

import { lpr } from './router/lpr.js';

dotenv.config();

const app = express();

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: false }));

db.getConnection().then(async (connection) => {
    logger.info(`DB Connection Success :: ${config.db.host}, ${config.db.database}`);
    //console.log("DB Connection Success :: ", config.db.host, ", ", config.db.database);
});

// 서버 로그용 morgan
app.use(morganMiddleware);

// LPR Router
app.post(`/lpr`, lpr);

app.use((err, req, res, next) => {
    logger.error(`Middleware Error Process : ${err}`);
    //console.error(wbLib.getToday(), "Middleware Error Process : ", err);

    res.status(500).json({
        success: false,
        timestamp: wbLib.getToday(),
        path: req.originalUrl,
        method: req.method,
        message: '서버에 문제가 발생하였습니다. ',
        errorCode: '500_INTERNAL_SERVER_ERROR!! ',
    });
});

app.listen(config.host.port, () => {
    logger.info(`Server is running on port ${config.host.port}`);
    //console.log(`Server is running on port ${config.host.port}`);
});
