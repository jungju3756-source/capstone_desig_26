# IoT 환경 모니터링 시스템 구축 과정

---

## 1. 시스템 구성도 (블록 다이어그램)

```mermaid
block-beta
    columns 3

    A["🖥️ Python\ninjector.py"]:1
    space:1
    B["📦 MariaDB\niot_monitor"]:1

    space:3

    C["🌐 Apache2\nWeb Server"]:1
    space:1
    D["📊 monitor.php\nPHP Page"]:1

    space:3

    E["🌍 Web Browser\nClient"]:1
    space:1
    F["📈 Chart.js\n실시간 그래프"]:1

    A-- "INSERT\n1초 간격" -->B
    B-- "SELECT\nLIMIT 100" -->D
    D-- "HTML 렌더링" -->C
    C-- "HTTP Response" -->E
    E-- "시각화" -->F
    F-- "5초 Auto Refresh" -->E
```

---

## 2. 데이터 흐름도

```mermaid
flowchart TD
    A([🚀 시스템 시작]) --> B[MariaDB 서비스 시작\nsudo systemctl start mariadb]
    B --> C[Apache2 서비스 시작\nsudo systemctl start apache2]
    C --> D[injector.py 실행\npython3 injector.py]

    D --> E{1초마다 반복}
    E --> F[가상 센서 데이터 생성\n온도: 15~40°C\n습도: 30~90%\nCO2: 400~2000ppm]
    F --> G[MariaDB INSERT\niot_monitor.sensor_data]
    G --> E

    C --> H{5초마다 반복}
    H --> I[브라우저 HTTP 요청\nGET /monitor.php]
    I --> J[PHP: MariaDB 쿼리\nSELECT LIMIT 100]
    J --> K[HTML + Chart.js 렌더링]
    K --> L[실시간 대시보드 표시\n온도 / 습도 / CO2 그래프]
    L --> H

    style A fill:#4CAF50,color:#fff
    style G fill:#2196F3,color:#fff
    style L fill:#9C27B0,color:#fff
```

---
process.md
## 3. 시스템 아키텍처 계층도

```mermaid
graph TB
    subgraph HOST["🖥️ Host (Windows 11)"]
        Browser["🌍 Web Browser"]
    end

    subgraph VM["⚙️ VMware - Ubuntu 24.04 / Zorin OS"]
        subgraph LAMP["LAMP Stack"]
            Apache["🌐 Apache2\nPort 80"]
            PHP["🐘 PHP 8.x\nmonitor.php"]
            MariaDB["🗄️ MariaDB 10.x\niot_monitor DB"]
        end

        subgraph PY["Python"]
            Injector["🐍 injector.py\n가상 센서 데이터 생성"]
        end
    end

    Browser -->|"HTTP GET\n192.168.0.53/monitor.php"| Apache
    Apache --> PHP
    PHP -->|"SELECT * LIMIT 100"| MariaDB
    Injector -->|"INSERT 1초 간격"| MariaDB

    style HOST fill:#e3f2fd
    style VM fill:#f3e5f5
    style LAMP fill:#e8f5e9
    style PY fill:#fff3e0
```

---

## 4. 데이터베이스 구조

```mermaid
erDiagram
    sensor_data {
        INT id PK "AUTO_INCREMENT"
        FLOAT temperature "온도 °C (15~40)"
        FLOAT humidity "습도 % (30~90)"
        INT co2 "CO2 ppm (400~2000)"
        DATETIME recorded_at "기록 시각 DEFAULT NOW()"
    }
```

---

## 5. 구축 순서

```mermaid
sequenceDiagram
    participant Dev as 개발자
    participant OS as Ubuntu/Zorin OS
    participant DB as MariaDB
    participant Web as Apache + PHP
    participant Py as Python

    Dev->>OS: sudo apt install mariadb-server
    OS-->>Dev: MariaDB 설치 완료

    Dev->>DB: sudo mysql < db_setup.sql
    DB-->>Dev: iot_monitor DB & sensor_data 테이블 생성

    Dev->>OS: sudo apt install apache2 php php-mysql
    OS-->>Web: Apache + PHP 설치 완료

    Dev->>Web: sudo cp monitor.php /var/www/html/
    Web-->>Dev: PHP 페이지 배치 완료

    Dev->>Py: pip3 install mysql-connector-python
    Py-->>Dev: 패키지 설치 완료

    Dev->>Py: python3 injector.py &
    loop 1초 간격
        Py->>DB: INSERT INTO sensor_data
        DB-->>Py: OK
    end

    Dev->>Web: 브라우저에서 monitor.php 접속
    loop 5초 간격
        Web->>DB: SELECT * LIMIT 100
        DB-->>Web: 센서 데이터 반환
        Web-->>Dev: 실시간 대시보드 + 그래프 표시
    end
```

---

## 6. 임계값 경고 로직

```mermaid
flowchart LR
    A[센서 데이터 수신] --> B{온도 체크}
    B -->|"> 35°C"| C[🔴 위험 경고\nalert-danger]
    B -->|"> 30°C"| D[🟡 주의 경고\nalert-warning]
    B -->|"정상"| E[✅ 정상]

    A --> F{CO2 체크}
    F -->|"> 1500ppm"| G[🔴 위험 경고\nalert-danger]
    F -->|"> 1000ppm"| H[🟡 주의 경고\nalert-warning]
    F -->|"정상"| I[✅ 정상]

    style C fill:#f8d7da
    style G fill:#f8d7da
    style D fill:#fff3bf
    style H fill:#fff3bf
    style E fill:#d4edda
    style I fill:#d4edda
```

---

## 7. 폴더 구조

```
lamp-iot-monitor/
├── project.md          # 프로젝트 계획
├── process.md          # 구축 과정 (본 파일)
├── db_setup.sql        # DB & 테이블 생성 SQL
├── injector.py         # 가상 IoT 데이터 생성 스크립트
├── monitor.php         # 실시간 모니터링 PHP 페이지
└── README.md           # GitHub 소개
```
