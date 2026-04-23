CREATE DATABASE pentest_db;
USE pentest_db;

CREATE TABLE victims (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    real_name VARCHAR(255),
    age VARCHAR(10),
    reason TEXT,
    email VARCHAR(255),
    ip VARCHAR(45),
    geo TEXT,
    user_agent TEXT,
    remote_ip VARCHAR(45),
    referer TEXT,
    timestamp DATETIME
);
