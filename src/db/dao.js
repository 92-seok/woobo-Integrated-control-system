import { db } from './dbConn.js';
import { logger } from '../util/logger.js';
//import * as wbLib from "../util/wbLib.js";

/***************************************************************/
/*************************** LPR *******************************/

/**************************************************************/

export async function updateParkCarHist(carNum) {
    try {
        await db.execute(
            `UPDATE wb_parkcarhist
             SET OutFlag = 'Y'
             WHERE CarNum = ?
               AND OutFlag IS NULL`,
            [carNum]
        );
    } catch (error) {
        logger.error(`updateParkCarHist ::: ${error}`);
        //console.error(wbLib.getToday(), 'updateParkCarHist ::: ', error);
    }
}

export async function insertParkCarHist(cdDistObsv, date, carNum) {
    try {
        await db.execute(
            `INSERT INTO wb_parkcarhist (GateDate, GateSerial, CarNum)
                 VALUE (?, ?, ?)`,
            [date, cdDistObsv, carNum]
        );
    } catch (error) {
        logger.error(`insertParkCarHist ::: ${error}`);
        //console.error(wbLib.getToday(), 'insertParkingHist ::: ', error);
    }
}

export async function insertParkCarImg(carBin, carNum, date) {
    try {
        await db.execute(
            `INSERT INTO wb_parkcarimg (idx, CarNum_Img, CarNum_Imgname)
             SELECT idx, ?, ?
             FROM wb_parkcarhist
             WHERE GateDate = ?
               AND CarNum = ?
               ORDER BY idx DESC
               LIMIT 1`,
            [carBin, carNum, date, carNum]
        );
    } catch (error) {
        logger.error(`insertParkCarImg ::: ${error}`);
        //console.error(wbLib.getToday(), 'insertParkingHist ::: ', error);
    }
}

export async function insertParkInOutCnt(cdDistObsv, dateConvert, intOut) {
    try {
        let MR = `MR${parseInt(dateConvert.substring(8, 10))}`; // to HH + 1
        let RegDate = `${dateConvert.substring(0, 8)}`; // to 'yyyymmdd'

        switch (intOut) {
            case '0':
                await db.execute(
                    `INSERT INTO wb_parkcarincnt (ParkGroupCode, RegDate, ${MR})
                         (SELECT ParkGroupCode, '${RegDate}', 1
                          FROM wb_parkgategroup
                          WHERE ParkJoinGate LIKE '%${cdDistObsv}%')
                     ON DUPLICATE KEY
                         UPDATE ${MR} = ${MR} + 1`
                );
                break;
            case '1':
                await db.execute(
                    `INSERT INTO wb_parkcaroutcnt (ParkGroupCode, RegDate, ${MR})
                         (SELECT ParkGroupCode, '${RegDate}', 1
                          FROM wb_parkgategroup
                          WHERE ParkJoinGate LIKE '%${cdDistObsv}%')
                     ON DUPLICATE KEY
                         UPDATE ${MR} = ${MR} + 1`
                );
                break;
        }

        logger.info(`insertParkInCnt ::: ${MR} ${RegDate} ${cdDistObsv} ${intOut}`);
        //console.log(wbLib.getToday(), 'insertParkInCnt ::: ', MR, RegDate, cdDistObsv, intOut);
    } catch (error) {
        logger.error(`insertParkInCnt ::: ${error}`);
        //console.error(wbLib.getToday(), 'insertParkInCnt ::: ', error);
    }
}

/***************************************************************/
/***************************************************************/
/**************************************************************/
