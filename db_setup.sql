-- IoT 모니터링 시스템 데이터베이스 설정
-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS iot_monitor;
USE iot_monitor;

-- 센서 데이터 테이블 생성
CREATE TABLE IF NOT EXISTS sensor_data (
    id INT AUTO_INCREMENT PRIMARY KEY,
    temperature FLOAT NOT NULL COMMENT '온도 (°C)',
    humidity FLOAT NOT NULL COMMENT '습도 (%)',
    co2 INT NOT NULL COMMENT 'CO2 농도 (ppm)',
    recorded_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT '데이터 기록 시각',
    INDEX idx_recorded_at (recorded_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 초기 데이터 샘플 (선택사항)
INSERT INTO sensor_data (temperature, humidity, co2) VALUES
(22.5, 45.0, 520),
(23.1, 48.2, 580),
(21.8, 42.5, 510);
