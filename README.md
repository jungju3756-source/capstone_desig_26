# 🌡️ IoT 환경 모니터링 시스템

LAMP Stack(Linux, Apache, MySQL, PHP) 기반의 실시간 IoT 센서 데이터 모니터링 시스템입니다.
Python으로 생성한 가상 센서 데이터(온도, 습도, CO2)를 MySQL에 저장하고, PHP 웹 페이지를 통해 실시간으로 모니터링합니다.

---

## 📋 프로젝트 개요

본 프로젝트는 다음을 구현합니다:

- **LAMP Stack 환경**: VMware 위의 Ubuntu 24.04에서 Apache2, MySQL, PHP 구성
- **데이터 생성**: Python 스크립트로 1초 간격 IoT 센서 데이터 자동 생성
- **실시간 모니터링**: PHP 웹 페이지로 동적 데이터 표시 및 5초마다 자동 새로고침
- **데이터 시각화**: 온도/CO2 임계값 초과 시 색상 경고 표시

---

## 🛠️ 기술 스택

| 구분 | 기술 |
|------|------|
| 가상화 환경 | VMware Workstation + Ubuntu 24.04 LTS |
| 웹 서버 | Apache2 |
| 데이터베이스 | MySQL 8.0 |
| 서버 스크립트 | PHP 8.x |
| 데이터 생성 | Python 3.x (mysql-connector-python) |
| 버전 관리 | Git / GitHub |

---

## 📁 프로젝트 구조

```
lamp-iot-monitor/
├── project.md          # 프로젝트 계획 및 설명
├── process.md          # 구축 과정 정리 + Mermaid 다이어그램
├── injector.py         # 가상 IoT 데이터 생성 및 DB 삽입 스크립트
├── monitor.php         # 실시간 데이터 모니터링 PHP 페이지
├── db_setup.sql        # DB 및 테이블 생성 SQL 스크립트
└── README.md           # 프로젝트 소개 (본 파일)
```

---

## 🗄️ 데이터베이스 설계

**Database명**: `iot_monitor`
**Table명**: `sensor_data`

| 컬럼명 | 타입 | 설명 |
|--------|------|------|
| id | INT AUTO_INCREMENT PK | 고유 식별자 |
| temperature | FLOAT | 온도 (°C), 범위: 15.0 ~ 40.0 |
| humidity | FLOAT | 습도 (%), 범위: 30.0 ~ 90.0 |
| co2 | INT | CO2 농도 (ppm), 범위: 400 ~ 2000 |
| recorded_at | DATETIME | 데이터 기록 시각 (기본값: CURRENT_TIMESTAMP) |

---

## 🚀 설치 및 실행 방법

### 1단계: 필수 패키지 설치

```bash
# Ubuntu에서 LAMP Stack 설치
sudo apt update
sudo apt install -y apache2 mysql-server php libapache2-mod-php php-mysql

# PHP MySQL 드라이버 설치
sudo apt install -y python3-pip
pip3 install mysql-connector-python
```

### 2단계: MySQL 데이터베이스 설정

```bash
# MySQL 진입 (root 비밀번호가 없을 경우)
sudo mysql -u root

# SQL 스크립트 실행
source /path/to/db_setup.sql;

# 데이터베이스 확인
SHOW DATABASES;
USE iot_monitor;
SHOW TABLES;
```

### 3단계: PHP 파일 배치

```bash
# monitor.php를 Apache 웹 디렉터리에 복사
sudo cp monitor.php /var/www/html/

# 권한 설정 (필요시)
sudo chown www-data:www-data /var/www/html/monitor.php
sudo chmod 644 /var/www/html/monitor.php
```

### 4단계: Python 데이터 생성 스크립트 실행

```bash
# injector.py의 MySQL 비밀번호 설정
# injector.py 파일에서 DB_CONFIG의 'password' 값을 설정

# 스크립트 실행
python3 injector.py

# 백그라운드 실행 (선택사항)
nohup python3 injector.py > injector.log 2>&1 &
```

### 5단계: 웹 브라우저에서 모니터링

```
http://localhost/monitor.php
또는
http://<서버IP>/monitor.php
```

---

## 📊 모니터링 페이지 기능

### 실시간 대시보드
- **온도**: 현재 온도값 표시, 35°C 이상 빨간 경고
- **습도**: 현재 습도값 표시
- **CO2**: 현재 CO2 농도, 1500ppm 이상 빨간 경고

### 데이터 테이블
- 최근 20건의 센서 데이터 표시
- 기록 시간순 정렬
- 임계값 초과 시 하이라이트 표시

### 자동 새로고침
- 5초마다 자동으로 페이지 새로고침
- 항상 최신 데이터 표시

---

## 📝 주요 파일 설명

### `db_setup.sql`
- MySQL 데이터베이스 및 테이블 생성
- 샘플 데이터 3건 삽입

### `injector.py`
- 가상 센서 데이터 생성 (온도: 15~40°C, 습도: 30~90%, CO2: 400~2000ppm)
- 1초 간격으로 MySQL에 데이터 INSERT
- Ctrl+C로 종료 가능

### `monitor.php`
- MySQL에서 최신 및 최근 20건 데이터 조회
- 반응형 대시보드 UI 제공
- 임계값 기반 색상 경고 표시
- meta refresh로 5초마다 자동 새로고침

---

## 🔧 트러블슈팅

### MySQL 연결 오류
```
데이터베이스 연결 실패: Access denied for user 'root'@'localhost'
```
**해결방법**: injector.py와 monitor.php에서 MySQL 비밀번호 확인

### 데이터가 표시되지 않음
- `injector.py`가 실행 중인지 확인
- MySQL에 데이터가 INSERT되었는지 확인:
  ```sql
  SELECT * FROM sensor_data;
  ```

### Apache 권한 오류
```bash
# Apache 소유권 설정
sudo chown -R www-data:www-data /var/www/html/
sudo chmod -R 755 /var/www/html/
```

### PHP MySQL 확장 미설치
```bash
sudo apt install -y php-mysql
sudo systemctl restart apache2
```

---

## 📈 성능 및 확장

- **데이터 수집**: 현재 1초 간격, 필요시 조정 가능
- **데이터 보관**: 무제한 저장, 필요시 아카이빙 구성 가능
- **성능 최적화**:
  - 인덱스 생성 (recorded_at)
  - 주기적인 오래된 데이터 정리
  - 캐시 활용

---

## 📄 라이선스

이 프로젝트는 교육 목적으로 제작되었습니다.

---

## 🙋 지원

문제가 발생하면 다음을 확인하세요:
1. LAMP Stack이 올바르게 설치되었는지
2. MySQL이 실행 중이고 데이터베이스가 생성되었는지
3. 방화벽이 Apache(포트 80)를 차단하지 않는지
4. Python 스크립트의 MySQL 인증정보가 올바른지

---

**Last Updated**: 2026-03-19
