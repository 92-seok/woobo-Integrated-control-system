import morgan from 'morgan';
import dotenv from 'dotenv';
import momentTimeZone from 'moment-timezone';

import { logger } from '../util/logger.js';

dotenv.config();

const timeZone = 'Asia/Seoul';

morgan.token('date', (req, res, tz) => {
    return momentTimeZone().tz(tz).format('YYYY-MM-DD HH:mm:ss');
});

function format() {
    const productionFormat = ':remote-addr - :remote-user [:date[' + timeZone + ']] ":method :url HTTP/:http-version" :status :res[content-length] ":referrer" ":user-agent"';
    const developmentFormat = ':remote-addr - :remote-user [:date[' + timeZone + ']] ":method :url HTTP/:http-version" :status :res[content-length] - :response-time ms';
    return process.env.NODE_ENV === 'production' ? productionFormat : developmentFormat;
}

const stream = {
    write: (message) => {
        logger.info(`SERVER LOG: ${message.trim()}`);
    },
};

const skip = (_, res) => {
    if (process.env.NODE_ENV === 'production') {
        return res.statusCode < 400;
    }
    return false;
};
export const morganMiddleware = morgan(format(), { stream, skip });
