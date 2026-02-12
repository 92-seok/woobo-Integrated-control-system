import mysql from 'mysql2';
import { config } from '../config/config.js';

const pool = mysql.createPool({
    host: config.db.host,
    port: config.db.port,
    database: config.db.database,
    user: config.db.user,
    password: config.db.password,
});

export const db = pool.promise();
