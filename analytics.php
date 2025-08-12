<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Sample analytics data
$patientStages = [
    'Stage 1' => 45,
    'Stage 2' => 35,
    'Stage 3' => 20
];

$alertTypes = [
    'High Heart Rate' => 25,
    'Missed Medication' => 30,
    'GPS Boundary' => 15,
    'Low Activity' => 20
];

$monthlyTrends = [
    'Jan' => 120,
    'Feb' => 135,
    'Mar' => 110,
    'Apr' => 145,
    'May' => 130,
    'Jun' => 140
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics & Reports - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Analytics & Reports Dashboard -->
            <div class="summary-cards">
                <div class="summary-card">
                    <div class="card-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-content">
                        <h3>Total Patients</h3>
                        <div class="card-value">100</div>
                        <span class="card-change positive">+12% from last month</span>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="card-content">
                        <h3>Active Alerts</h3>
                        <div class="card-value">24</div>
                        <span class="card-change negative">-8% from last week</span>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="card-content">
                        <h3>Medication Adherence</h3>
                        <div class="card-value">87%</div>
                        <span class="card-change positive">+5% from last month</span>
                    </div>
                </div>
                
                <div class="summary-card">
                    <div class="card-icon yellow">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3>Avg Response Time</h3>
                        <div class="card-value">3.2 min</div>
                        <span class="card-change positive">-0.8 min from last week</span>
                    </div>
                </div>
            </div>
            
            <!-- Filters & Quick Actions -->
            <div class="content-card" style="margin-bottom: 24px;">
                <div class="card-header">
                    <h2><i class="fas fa-sliders-h"></i> Analytics Filters</h2>
                    <div class="card-actions">
                        <select id="analyticsRange" class="form-select">
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
                            <option value="180">Last 6 months</option>
                            <option value="365">Last 12 months</option>
                        </select>
                        <button class="btn btn-secondary" onclick="downloadCanvasImage('trendsChart','trends.png')">
                            <i class="fas fa-download"></i> Export Chart
                        </button>
                        <button class="btn btn-primary" onclick="exportAnalyticsCSV()">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="analytics-grid" style="margin-bottom: 32px;">
                <!-- Patient Stages Chart -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-chart-pie"></i> Patient Stages Distribution</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="stagesChart"></canvas>
                    </div>
                </div>
                
                <!-- Alert Types Chart -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-chart-bar"></i> Alert Types Analysis</h2>
                    </div>
                    <div class="chart-container">
                        <canvas id="alertsChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Monthly Trends Chart -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-chart-line"></i> Monthly Patient Activity Trends</h2>
                    <select class="form-select" id="trendsRange">
                        <option value="6">Last 6 months</option>
                        <option value="12">Last 12 months</option>
                        <option value="24">Last 2 years</option>
                    </select>
                </div>
                <div class="chart-container">
                    <canvas id="trendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .card-change { font-size: 12px; font-weight: 500; }
        .card-change.positive { color: #10b981; }
        .card-change.negative { color: #dc2626; }
    </style>
    
    <script>
        // Patient Stages Chart
        const stagesCtx = document.getElementById('stagesChart').getContext('2d');
        new Chart(stagesCtx, {
            type: 'doughnut',
            data: {
                labels: ['Stage 1', 'Stage 2', 'Stage 3'],
                datasets: [{
                    data: [<?php echo $patientStages['Stage 1']; ?>, <?php echo $patientStages['Stage 2']; ?>, <?php echo $patientStages['Stage 3']; ?>],
                    backgroundColor: [
                        '#10b981',
                        '#f59e0b',
                        '#dc2626'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
        
        // Alert Types Chart
        const alertsCtx = document.getElementById('alertsChart').getContext('2d');
        new Chart(alertsCtx, {
            type: 'bar',
            data: {
                labels: ['High Heart Rate', 'Missed Medication', 'GPS Boundary', 'Low Activity'],
                datasets: [{
                    label: 'Alert Count',
                    data: [<?php echo $alertTypes['High Heart Rate']; ?>, <?php echo $alertTypes['Missed Medication']; ?>, <?php echo $alertTypes['GPS Boundary']; ?>, <?php echo $alertTypes['Low Activity']; ?>],
                    backgroundColor: [
                        '#dc2626',
                        '#f59e0b',
                        '#3b82f6',
                        '#10b981'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
        
        // Monthly Trends Chart
        const trendsCtx = document.getElementById('trendsChart').getContext('2d');
        const trendsChart = new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Patient Activity',
                    data: [<?php echo implode(', ', $monthlyTrends); ?>],
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Simple filter interactions
        document.getElementById('analyticsRange').addEventListener('change', () => {
            showNotification('Filters applied to analytics (mock)', 'info');
        });

        document.getElementById('trendsRange').addEventListener('change', (e) => {
            // Mock: extend labels for 12/24 months
            const months6 = ['Jan','Feb','Mar','Apr','May','Jun'];
            const months12 = ['Jul','Aug','Sep','Oct','Nov','Dec', ...months6];
            const months24 = [...months12, ...months12];
            const target = e.target.value === '12' ? months12 : (e.target.value === '24' ? months24 : months6);
            trendsChart.data.labels = target;
            // Generate mock data same length
            trendsChart.data.datasets[0].data = target.map((_, i) => 100 + Math.round(20 * Math.sin(i/2) + (i%3)*5));
            trendsChart.update();
        });

        // Export helpers
        window.downloadCanvasImage = function(canvasId, filename) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = filename || 'chart.png';
            link.click();
        }

        window.exportAnalyticsCSV = function() {
            const rows = [
                ['Metric','Value'],
                ['Total Patients','100'],
                ['Active Alerts','24'],
                ['Medication Adherence','87%']
            ];
            const csv = 'data:text/csv;charset=utf-8,' + rows.map(r => r.join(',')).join('\n');
            const link = document.createElement('a');
            link.href = encodeURI(csv);
            link.download = 'analytics_summary.csv';
            link.click();
            showNotification('Analytics exported as CSV', 'success');
        }
    </script>
    
</body>
</html> 