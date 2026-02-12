import * as dao from '../db/dao.js';

import { logger } from '../util/logger.js';
import * as wbLib from '../util/wbLib.js';

export async function getParkingData(lprData) {
    // CD_DIST_OBSV
    const cdDistObsv = lprData.serial.substring(0, 4);

    // 0 = 입차, 1 = 출차
    let inOut = null;
    if (lprData.serial.substring(6, 7) === '0') {
        inOut = '0';
    } else if (lprData.serial.substring(6, 7) === '1') {
        inOut = '1';
    }

    const date = lprData.date;
    const carNum = lprData.carNum;
    const carBin = lprData.carBin;

    const dateConvert = wbLib.convertToCompactFormat(date);

    logger.info(`getParkingData ::: ${cdDistObsv}, ${inOut}, ${date}, ${carNum}`);
    //console.log(wbLib.getToday(), 'getParkingData ::: ', cdDistObsv, inOut, date, carNum);

    await dao.updateParkCarHist(carNum); // 차량 중복 확인(출차했는데 안찍혀있거나 하는 차들 거르기)
    await dao.insertParkCarHist(cdDistObsv, date, carNum, inOut); // wb_parkcarhist INSERT
    await dao.insertParkCarImg(carBin, carNum, date); // wb_parkcarimg INSERT
    await dao.insertParkInOutCnt(cdDistObsv, dateConvert, inOut); // wb_parkcarincnt, outcnt INSERT
}
