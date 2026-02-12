import mssql from 'mssql';
import mysql from 'mysql2/promise';

const lprConfig = {
    lprNdev: {
        user: 'sa',
        password: '0',
        server: '192.168.80.88',
        port: 1433,
        database: 'UPASS',
        cdDistObsv: '2101',
        description: '연구소 아너스 LPR',
        connectionTimeout: 1000 * 5, // 연결 최대 5초 대기
        requestTimeout: 1000 * 10, // 쿼리 최대 10초 대기
        options: {
            encrypt: false, // SSL 암호화 비활성화
            trustServerCertificate: true, // SSL 인증서 검증 비활성화
        },
    },
};

/* 로컬 디비 */
const wooboData = {
    host: '192.168.80.80',
    port: '3306',
    user: 'WBEarly',
    password: '#woobosys@early!',
    database: 'warning',
};

////////////////////////////////////////
// MAIN
////////////////////////////////////////

main_loop();

async function main_loop() {
    await linkLpr(lprConfig.lprNdev);

    // LTE 데이터 소진 방지를 위해 Polling 시간 0.1초 => 10초 100배로 늘림
    // (차 10대 지나가는 시간이 최소 이 정도 걸린다고 가정)
    setTimeout(() => main_loop(), 1000 * 10);
}

////////////////////////////////////////
// FUNCTION
////////////////////////////////////////

async function linkLpr(config) {
    let wooboDataConnection;
    let lprConnection;

    try {
        let cdDistObsv = config.cdDistObsv;
        let lastTime;
        let query;

        // wooboData 마지막 시간 조회
        {
            wooboDataConnection = await mysql.createPool(wooboData);
            console.log('Connected to wooboDB.');
            query = `SELECT IFNULL(MAX(EventDateTime), 0) as lastTime
                     FROM hns_lprdata
                     WHERE CD_DIST_OBSV = '${cdDistObsv}'`;
            const [wooboDataRows] = await wooboDataConnection.query(query);
            lastTime = wooboDataRows[0].lastTime;
        }

        // LPR 데이터 조회
        {
            lprConnection = await mssql.connect(config);
            console.log(`${new Date().toLocaleString()} : Connect to LPR(${config.description}).`);
            // MSSQL 데이터 SELECT
            query = `SELECT TOP 10 *
                     FROM dbo.CarImagesBase64
                     WHERE EventDateTime > '${lastTime}'`;
            let result = await lprConnection.request().query(query);
            let rows = result.recordset;

            // Mariadb 데이터 INSERT
            for (let row of rows) {
                console.log(`${row.EventDateTime}: ${row.CarNumber}`);

                //if (row.DeviceCode === "02")
                {
                    await wooboDataConnection.query(
                        `UPDATE hns_lprdata
                         SET OutFlag = 'Y'
                         WHERE CarNumber = '${row.CarNumber}'
                           AND OutFlag IS NULL`
                    );
                }

                await wooboDataConnection.query(
                    "INSERT IGNORE INTO hns_lprdata (CD_DIST_OBSV, RegistCode, EventDateTime, DeviceCode, CarNumber, ImageString, Reserved, OutFlag) VALUES (?, ?, ?, ?, ?, ?, ?, IF(DeviceCode = '02', 'Y', NULL))",
                    [+`${cdDistObsv}`, row.RegistCode, row.EventDateTime, row.DeviceCode, row.CarNumber, row.ImageString, row.Reserved]
                );

                // 차량 입출입 카운트
                let MR = `MR${parseInt(row.EventDateTime.substring(8, 10)) + 1}`; // to HH + 1
                let RegDate = `${row.EventDateTime.substring(0, 8)}`; // to 'yyyyMMdd'

                switch (row.DeviceCode) {
                    case '01':
                        query = `INSERT INTO wb_parkcarincnt (parkgroupcode, RegDate, ${MR}) (SELECT ParkGroupCode, '${RegDate}', 1
                                                                                              FROM wb_parkgategroup
                                                                                              WHERE parkjoingate LIKE '%${cdDistObsv}%')
                                 ON DUPLICATE KEY UPDATE ${MR} = ${MR} + 1`;
                        break;
                    case '02':
                        query = `INSERT INTO wb_parkcaroutcnt (parkgroupcode, RegDate, ${MR}) (SELECT ParkGroupCode, '${RegDate}', 1
                                                                                               FROM wb_parkgategroup
                                                                                               WHERE parkjoingate LIKE '%${cdDistObsv}%')
                                 ON DUPLICATE KEY UPDATE ${MR} = ${MR} + 1`;
                        break;
                }
                await wooboDataConnection.query(query);

                console.log(`${new Date().toLocaleString()} ${config.description} CarInOutCount complete.`);
            }
        }

        console.log(`${new Date().toLocaleString()} Sync ${config.description} to WOOBO DB complete.`);
    } catch (err) {
        console.error(`Error ${config.description} !!!!! : `, err);
    } finally {
        // 연결 종료
        try {
            console.log(`${new Date().toLocaleString()}  mysql disconnect`);
            wooboDataConnection.end();
        } catch {}
        try {
            console.log(`${new Date().toLocaleString()} mssql disconnect`);
            await lprConnection.close();
        } catch {}
    }
}
