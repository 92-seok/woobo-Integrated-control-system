const dbjs = require('../dbjs/dao-js');

dbjs.executeQueryDbWarning("SELECT * FROM wb_otp WHERE IDX = '6'")
    .then((result) => {
        console.log('Query Result: ', result);
        let sqlData = result;

        console.log('db-test ; ', sqlData);
    })
    .catch((error) => {
        console.error('Error occurred: ', error);
    });
