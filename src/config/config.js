import dotenv from 'dotenv';

dotenv.config();

// config 데이터 접근할 때 코드 템플릿 지원 함수
function required(key, defaultValue = undefined) {
    const value = process.env[key] || defaultValue;
    if (value === undefined) {
        throw new Error(`Key ${key} is undefined`);
    }
    return value;
}

export const config = {
    host: {
        port: parseInt(required('NODE_PORT', 5555)),
    },
    db: {
        host: required('DB_HOST'),
        port: parseInt(required('DB_PORT')),
        database: required('DB_NAME'),
        user: required('DB_USERNAME'),
        password: required('DB_PASSWORD'),
        charset: required('DB_CHARSET'),
    },
};
