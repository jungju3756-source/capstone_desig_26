#!/usr/bin/env python3
"""
IoT 센서 데이터 생성 및 MySQL 삽입 스크립트
온도, 습도, CO2 데이터를 1초 간격으로 자동 생성하여 DB에 저장
"""

import mysql.connector
import time
import random
from datetime import datetime

# MySQL 연결 설정
DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': 'qwer1234',  # MySQL root 비밀번호
    'database': 'iot_monitor'
}

def generate_sensor_data():
    """가상 센서 데이터 생성"""
    temperature = round(random.uniform(15.0, 40.0), 1)
    humidity = round(random.uniform(30.0, 90.0), 1)
    co2 = random.randint(400, 2000)

    return temperature, humidity, co2

def insert_data(conn, temperature, humidity, co2):
    """데이터베이스에 센서 데이터 삽입"""
    try:
        cursor = conn.cursor()
        query = "INSERT INTO sensor_data (temperature, humidity, co2) VALUES (%s, %s, %s)"
        cursor.execute(query, (temperature, humidity, co2))
        conn.commit()
        cursor.close()

        timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
        print(f"[{timestamp}] 데이터 저장 - 온도: {temperature}°C, 습도: {humidity}%, CO2: {co2}ppm")
    except mysql.connector.Error as err:
        print(f"데이터베이스 오류: {err}")
        conn.rollback()

def main():
    """메인 실행 함수"""
    try:
        # MySQL 연결
        conn = mysql.connector.connect(**DB_CONFIG)
        print("MySQL 데이터베이스 연결 성공")
        print("센서 데이터 생성을 시작합니다. (Ctrl+C로 중지)\n")

        # 1초 간격으로 데이터 생성 및 삽입
        while True:
            temp, humid, co2 = generate_sensor_data()
            insert_data(conn, temp, humid, co2)
            time.sleep(1)

    except mysql.connector.Error as err:
        print(f"데이터베이스 연결 오류: {err}")
        print("MySQL이 실행 중이고 데이터베이스가 생성되었는지 확인하세요.")

    except KeyboardInterrupt:
        print("\n\n센서 데이터 생성을 중지합니다.")

    finally:
        if conn.is_connected():
            conn.close()
            print("MySQL 연결 종료")

if __name__ == "__main__":
    main()
