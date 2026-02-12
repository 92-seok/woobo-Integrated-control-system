// OTP 검증
export function verifyOtp(dbConnection, indexNum, typingNum, callback) {
    const query = 'SELECT OTP FROM wb_otp WHERE IDX = ?';
    dbConnection.query(query, [indexNum], (err, results) => {
        if (err) {
            return callback(err, null);
        }
        const isValid = results.length > 0 && results[0].OTP === typingNum;
        return callback(null, isValid);
    });
}
