# Project
# Project: IoT 환경 모니터링 시스템 (LAMP Stack 기반)

## 프로젝트 개요

본 프로젝트는 VMware 위에 구동되는 Ubuntu 24.04 환경에서 LAMP Stack(Linux, Apache, MySQL, PHP)을 구축하고,
Python으로 가상의 IoT 센서 데이터(온도, 습도, CO2)를 생성하여 MySQL에 저장한 뒤,
PHP로 작성된 동적 HTML 페이지를 통해 데이터를 실시간으로 모니터링하는 시스템을 구현한다.

---

## 목표 기능

1. **LAMP Stack 환경 구성**
   - VMware에  설치 
   - Apache2, MySQL, PHP, phpMyAdmin 설치 및 설정

2. **가상 데이터 생성기 (injector.py)**
   - Python 스크립트로 IoT 센서 데이터를 1초 간격으로 자동 생성
   - 생성 데이터 항목: 온도(°C), 습도(%), CO2 농도(ppm)
   - 생성된 데이터를 MySQL `sensor_data` 테이블에 INSERT

3. **실시간 모니터링 웹페이지 (PHP)**
   - `monitor.php`: MySQL에서 최신 데이터를 조회하여 동적 HTML로 출력
   - 5초마다 자동 새로고침(meta refresh 또는 AJAX)
   - 테이블 형태로 최근 20건 데이터 표시
   - 온도/CO2 임계값 초과 시 색상 경고 표시

4. **GitHub 업로드**
   - 작업 폴더 전체를 GitHub 레포지토리에 push
   - `process.md`에 전체 구축 과정 및 Mermaid 블록다이어그램 포함

---

## 프로젝트 폴더 구조

```
lamp-iot-monitor/
├── project.md          # 프로젝트 계획 및 설명 (본 파일)
├── process.md          # 구축 과정 정리 + Mermaid 다이어그램
├── injector.py         # 가상 IoT 데이터 생성 및 DB 삽입 스크립트
├── monitor.php         # 실시간 데이터 모니터링 PHP 페이지
├── db_setup.sql        # DB 및 테이블 생성 SQL 스크립트
└── README.md           # GitHub 레포 소개
```

---

## 기술 스택

| 구분 | 기술 |
|------|------|
| 가상화 환경 | VMware Workstation + Ubuntu 24.04 |
| 웹 서버 | Apache2 |
| 데이터베이스 | MySQL 8.0 |
| 서버 스크립트 | PHP 8.x |
| 데이터 생성 | Python 3.x (mysql-connector-python) |
| 버전 관리 | Git / GitHub |

---

## DB 설계

- **Database명**: `iot_monitor`
- **Table명**: `sensor_data`

| 컬럼명 | 타입 | 설명 |
|--------|------|------|
| id | INT AUTO_INCREMENT PK | 고유 식별자 |
| temperature | FLOAT | 온도 (°C), 범위: 15.0 ~ 40.0 |
| humidity | FLOAT | 습도 (%), 범위: 30.0 ~ 90.0 |
| co2 | INT | CO2 농도 (ppm), 범위: 400 ~ 2000 |
| recorded_at | DATETIME | 데이터 기록 시각 |

---

## 구현 순서 (TODO)

- [ ] 1. VMware에 Ubuntu 24.04 설치
- [ ] 2. LAMP Stack 설치 (apache2, mysql-server, php, libapache2-mod-php, php-mysql)
- [ ] 3. `db_setup.sql` 실행하여 DB 및 테이블 생성
- [ ] 4. `injector.py` 작성 및 실행 테스트
- [ ] 5. `monitor.php` 작성 및 Apache 웹 디렉터리(/var/www/html)에 배치
- [ ] 6. 브라우저에서 실시간 모니터링 확인
- [ ] 7. 동작 영상 녹화
- [ ] 8. GitHub 레포 생성 및 전체 폴더 push
- [ ] 9. `process.md` 작성 (Mermaid 다이어그램 포함)
- [ ] 10. 동작영상 URL + GitHub 레포명 txt 파일 작성 후 제출

---

## 참고 환경

- Host OS: Windows 11
- Guest OS: Ubuntu 24.04 LTS (VMware Workstation)
- 개발자: [본인 이름/학번]
- 과목: 캡스톤 디자인 / 임베디드 리눅스
- 제출일: [제출 기한 입력]

