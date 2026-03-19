<?php
$servername = "localhost";
$username = "root";
$password = "qwer1234";
$dbname = "iot_monitor";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("데이터베이스 연결 실패: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 그래프용 데이터 (최근 100건, 시간 오름차순)
$chart_rows = [];
$chart_result = $conn->query("SELECT temperature, humidity, co2, recorded_at FROM sensor_data ORDER BY recorded_at DESC LIMIT 100");
if ($chart_result && $chart_result->num_rows > 0) {
    while ($row = $chart_result->fetch_assoc()) {
        $chart_rows[] = $row;
    }
    $chart_rows = array_reverse($chart_rows);
}

// 테이블용 데이터 (최근 20건)
$table_rows = [];
$table_result = $conn->query("SELECT id, temperature, humidity, co2, recorded_at FROM sensor_data ORDER BY recorded_at DESC LIMIT 20");
if ($table_result && $table_result->num_rows > 0) {
    while ($row = $table_result->fetch_assoc()) {
        $table_rows[] = $row;
    }
}

// 최신 데이터
$latest_data = null;
$latest_result = $conn->query("SELECT temperature, humidity, co2, recorded_at FROM sensor_data ORDER BY recorded_at DESC LIMIT 1");
if ($latest_result) {
    $latest_data = $latest_result->fetch_assoc();
}

// 차트용 JSON
$chart_labels = [];
$chart_temps = [];
$chart_humidity = [];
$chart_co2 = [];
foreach ($chart_rows as $r) {
    $chart_labels[] = date('H:i:s', strtotime($r['recorded_at']));
    $chart_temps[]   = (float)$r['temperature'];
    $chart_humidity[] = (float)$r['humidity'];
    $chart_co2[]     = (int)$r['co2'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5">
    <title>IoT 센서 모니터링 시스템</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        header { text-align: center; color: white; margin-bottom: 30px; }
        header h1 { font-size: 2.5em; margin-bottom: 10px; }
        header p { font-size: 1.1em; opacity: 0.9; }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            text-align: center;
        }
        .card-title { font-size: 0.9em; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px; font-weight: 600; }
        .card-value { font-size: 2.5em; font-weight: bold; margin-bottom: 10px; }
        .card-unit  { font-size: 1em; color: #999; }
        .card.temperature { border-left: 5px solid #ff6b6b; }
        .card.humidity    { border-left: 5px solid #4dabf7; }
        .card.co2         { border-left: 5px solid #51cf66; }
        .card.temperature .card-value { color: #ff6b6b; }
        .card.humidity    .card-value { color: #4dabf7; }
        .card.co2         .card-value { color: #51cf66; }

        .alert { padding: 8px 12px; border-radius: 5px; font-size: 0.85em; margin-top: 10px; font-weight: 600; }
        .alert-warning { background: #fff3bf; color: #856404; border: 1px solid #ffeaa7; }
        .alert-danger  { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .timestamp { font-size: 0.8em; color: #999; margin-top: 15px; }

        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        .section h2 { margin-bottom: 20px; color: #333; font-size: 1.5em; }

        table { width: 100%; border-collapse: collapse; font-size: 0.95em; }
        table thead { background: #f8f9fa; border-bottom: 2px solid #dee2e6; }
        table th { padding: 15px; text-align: left; font-weight: 600; color: #495057; }
        table td { padding: 12px 15px; border-bottom: 1px solid #dee2e6; }
        table tbody tr:hover { background: #f8f9fa; }
        .temp-high { color: #ff6b6b; font-weight: bold; }
        .temp-low  { color: #4dabf7; font-weight: bold; }
        .co2-high  { color: #ff6b6b; font-weight: bold; }

        .chart-wrap { position: relative; height: 400px; }
        .refresh-info { text-align: center; color: white; margin-top: 20px; font-size: 0.9em; }
    </style>
</head>
<body>
<div class="container">
    <header>
        <h1>🌡️ IoT 센서 모니터링 시스템</h1>
        <p>실시간 환경 데이터 모니터링 대시보드</p>
    </header>

    <?php if ($latest_data): ?>

    <!-- 대시보드 카드 -->
    <div class="dashboard">
        <div class="card temperature">
            <div class="card-title">온도</div>
            <div class="card-value"><?= number_format($latest_data['temperature'], 1) ?></div>
            <div class="card-unit">°C</div>
            <?php if ($latest_data['temperature'] > 35): ?>
                <div class="alert alert-danger">⚠️ 매우 높음!</div>
            <?php elseif ($latest_data['temperature'] > 30): ?>
                <div class="alert alert-warning">⚡ 높음</div>
            <?php endif; ?>
            <div class="timestamp">범위: 15.0 ~ 40.0°C</div>
        </div>
        <div class="card humidity">
            <div class="card-title">습도</div>
            <div class="card-value"><?= number_format($latest_data['humidity'], 1) ?></div>
            <div class="card-unit">%</div>
            <div class="timestamp">범위: 30.0 ~ 90.0%</div>
        </div>
        <div class="card co2">
            <div class="card-title">CO2 농도</div>
            <div class="card-value"><?= $latest_data['co2'] ?></div>
            <div class="card-unit">ppm</div>
            <?php if ($latest_data['co2'] > 1500): ?>
                <div class="alert alert-danger">⚠️ 매우 높음!</div>
            <?php elseif ($latest_data['co2'] > 1000): ?>
                <div class="alert alert-warning">⚡ 높음</div>
            <?php endif; ?>
            <div class="timestamp">범위: 400 ~ 2000ppm</div>
        </div>
    </div>

    <!-- 그래프 -->
    <div class="section">
        <h2>📈 데이터 추세 그래프 (최근 100건)</h2>
        <div class="chart-wrap">
            <canvas id="sensorChart"></canvas>
        </div>
    </div>

    <!-- 테이블 -->
    <div class="section">
        <h2>📊 최근 센서 데이터 (최근 20건)</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th><th>온도 (°C)</th><th>습도 (%)</th><th>CO2 (ppm)</th><th>기록 시간</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($table_rows) > 0): ?>
                <?php foreach ($table_rows as $row): ?>
                    <?php
                        $tc = $row['temperature'] > 35 ? 'temp-high' : ($row['temperature'] < 20 ? 'temp-low' : '');
                        $cc = $row['co2'] > 1500 ? 'co2-high' : '';
                    ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td class="<?= $tc ?>"><?= number_format($row['temperature'], 1) ?></td>
                        <td><?= number_format($row['humidity'], 1) ?></td>
                        <td class="<?= $cc ?>"><?= $row['co2'] ?></td>
                        <td><?= $row['recorded_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">데이터가 없습니다.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="refresh-info">⟳ 5초마다 자동으로 새로고침됩니다</div>

    <?php else: ?>
    <div class="section" style="text-align:center; color:#666;">
        <h2>데이터가 없습니다</h2>
        <p>injector.py를 실행하여 센서 데이터를 생성하세요.</p>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js 및 차트 초기화 - body 끝에 배치 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var labels   = <?= json_encode($chart_labels) ?>;
    var temps    = <?= json_encode($chart_temps) ?>;
    var humidity = <?= json_encode($chart_humidity) ?>;
    var co2      = <?= json_encode($chart_co2) ?>;

    var ctx = document.getElementById('sensorChart');
    if (ctx && labels.length > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: '온도 (°C)',
                        data: temps,
                        borderColor: '#ff6b6b',
                        backgroundColor: 'rgba(255,107,107,0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'yTemp',
                        pointRadius: 3,
                        pointBackgroundColor: '#ff6b6b'
                    },
                    {
                        label: '습도 (%)',
                        data: humidity,
                        borderColor: '#4dabf7',
                        backgroundColor: 'rgba(77,171,247,0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'yHumid',
                        pointRadius: 3,
                        pointBackgroundColor: '#4dabf7'
                    },
                    {
                        label: 'CO2 (ppm)',
                        data: co2,
                        borderColor: '#51cf66',
                        backgroundColor: 'rgba(81,207,102,0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        yAxisID: 'yCo2',
                        pointRadius: 3,
                        pointBackgroundColor: '#51cf66'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { usePointStyle: true, padding: 15, font: { size: 12, weight: 'bold' } }
                    }
                },
                scales: {
                    x: {
                        ticks: { maxTicksLimit: 20, maxRotation: 45 },
                        title: { display: true, text: '시간' }
                    },
                    yTemp: {
                        type: 'linear', position: 'left',
                        min: 10, max: 45,
                        title: { display: true, text: '온도 (°C)', color: '#ff6b6b' },
                        ticks: { color: '#ff6b6b' },
                        grid: { color: 'rgba(255,107,107,0.1)' }
                    },
                    yHumid: {
                        type: 'linear', position: 'right',
                        min: 20, max: 100,
                        title: { display: true, text: '습도 (%)', color: '#4dabf7' },
                        ticks: { color: '#4dabf7' },
                        grid: { display: false }
                    },
                    yCo2: {
                        type: 'linear', position: 'right',
                        min: 350, max: 2100,
                        offset: true,
                        title: { display: true, text: 'CO2 (ppm)', color: '#51cf66' },
                        ticks: { color: '#51cf66' },
                        grid: { display: false }
                    }
                }
            }
        });
    }
</script>
</body>
</html>
