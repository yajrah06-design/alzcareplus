<?php
session_start();

// Include database connection
require_once 'includes/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Enhanced dashboard data
$dashboardData = [
    'total_patients' => 156,
    'active_patients' => 142,
    'critical_alerts' => 8,
    'pending_medications' => 23,
    'total_caregivers' => 45,
    'system_uptime' => '99.8%',
    'response_time' => '2.3s',
    'monthly_admissions' => 12,
    'monthly_discharges' => 8,
    'avg_patient_age' => 74.2,
    'medication_compliance' => 94.5,
    'satisfaction_score' => 4.8
];

// Real-time activity data
$recentActivities = [
    ['time' => '2 min ago', 'type' => 'alert', 'patient' => 'Agnes B.', 'action' => 'Heart rate elevated', 'status' => 'critical'],
    ['time' => '5 min ago', 'type' => 'medication', 'patient' => 'Kurt B.', 'action' => 'Medication taken', 'status' => 'completed'],
    ['time' => '8 min ago', 'type' => 'gps', 'patient' => 'Willard V.', 'action' => 'Left safe zone', 'status' => 'warning'],
    ['time' => '12 min ago', 'type' => 'health', 'patient' => 'Taylor S.', 'action' => 'Blood pressure normal', 'status' => 'normal'],
    ['time' => '15 min ago', 'type' => 'alert', 'patient' => 'Harry S.', 'action' => 'Missed medication', 'status' => 'critical'],
    ['time' => '18 min ago', 'type' => 'caregiver', 'patient' => 'Emma W.', 'action' => 'Caregiver assigned', 'status' => 'completed']
];

// Performance metrics
$performanceMetrics = [
    'response_time' => [2.1, 2.3, 2.0, 2.5, 2.2, 2.4, 2.3],
    'patient_satisfaction' => [4.7, 4.8, 4.9, 4.6, 4.8, 4.7, 4.8],
    'system_uptime' => [99.9, 99.8, 99.7, 99.9, 99.8, 99.9, 99.8],
    'alert_resolution' => [95, 92, 96, 94, 93, 95, 94]
];

// Critical alerts
    $criticalAlerts = [
    ['patient' => 'Agnes B.', 'alert' => 'Heart rate: 110 bpm', 'time' => '2 min ago', 'priority' => 'high'],
    ['patient' => 'Harry S.', 'alert' => 'Missed medication', 'time' => '15 min ago', 'priority' => 'medium'],
    ['patient' => 'Willard V.', 'alert' => 'GPS boundary alert', 'time' => '8 min ago', 'priority' => 'high']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AlzCare+</title>
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
            
            <!-- Welcome Section -->
            <div class="welcome-section">
                <div class="welcome-content">
                    <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                    <p>Here's what's happening with your patients today</p>
                </div>
                <div class="welcome-stats">
                    <div class="stat-item">
                        <span class="stat-label">System Status</span>
                        <span class="stat-value online">Online</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Uptime</span>
                        <span class="stat-value"><?php echo $dashboardData['system_uptime']; ?></span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Response Time</span>
                        <span class="stat-value"><?php echo $dashboardData['response_time']; ?></span>
                    </div>
                    </div>
                </div>
            
            <!-- Critical Alerts Banner -->
                <?php if (!empty($criticalAlerts)): ?>
            <div class="critical-alerts-banner">
                <div class="banner-header">
                                <i class="fas fa-exclamation-triangle"></i>
                    <span>Critical Alerts</span>
                    <span class="alert-count"><?php echo count($criticalAlerts); ?></span>
                            </div>
                <div class="alerts-list">
                        <?php foreach ($criticalAlerts as $alert): ?>
                    <div class="alert-item priority-<?php echo $alert['priority']; ?>">
                        <div class="alert-info">
                            <span class="patient-name"><?php echo htmlspecialchars($alert['patient']); ?></span>
                            <span class="alert-message"><?php echo htmlspecialchars($alert['alert']); ?></span>
                        </div>
                        <span class="alert-time"><?php echo $alert['time']; ?></span>
                            </div>
                        <?php endforeach; ?>
                </div>
                <button class="btn btn-danger btn-sm" onclick="window.location.href='alerts.php'">
                    View All Alerts
                </button>
            </div>
                <?php endif; ?>
            
            <!-- Key Metrics Dashboard -->
            <div class="metrics-dashboard">
                <div class="metric-card primary">
                    <div class="metric-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?php echo $dashboardData['total_patients']; ?></div>
                        <div class="metric-label">Total Patients</div>
                        <div class="metric-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+<?php echo $dashboardData['monthly_admissions']; ?> this month</span>
                        </div>
                    </div>
                </div>
                
                <div class="metric-card warning">
                    <div class="metric-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?php echo $dashboardData['critical_alerts']; ?></div>
                        <div class="metric-label">Critical Alerts</div>
                        <div class="metric-trend negative">
                            <i class="fas fa-arrow-up"></i>
                            <span>+3 from yesterday</span>
                    </div>
                    </div>
                </div>
                
                <div class="metric-card success">
                    <div class="metric-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?php echo $dashboardData['medication_compliance']; ?>%</div>
                        <div class="metric-label">Medication Compliance</div>
                        <div class="metric-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+2.1% this week</span>
                        </div>
                    </div>
                </div>
                
                <div class="metric-card info">
                    <div class="metric-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="metric-content">
                        <div class="metric-value"><?php echo $dashboardData['satisfaction_score']; ?></div>
                        <div class="metric-label">Satisfaction Score</div>
                        <div class="metric-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+0.2 this month</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Main Dashboard Grid -->
            <div class="dashboard-grid">
                <!-- Performance Analytics -->
                <div class="dashboard-card large">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> System Performance</h3>
                        <div class="card-actions">
                            <select class="time-range-select">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                            </select>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
                
                <!-- Real-time Activity Feed -->
                <div class="dashboard-card">
                <div class="card-header">
                        <h3><i class="fas fa-stream"></i> Real-time Activity</h3>
                        <div class="card-actions">
                            <button class="btn btn-sm btn-outline" onclick="refreshActivity()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="activity-feed">
                        <?php foreach ($recentActivities as $activity): ?>
                        <div class="activity-item status-<?php echo $activity['status']; ?>">
                            <div class="activity-icon">
                                <i class="fas fa-<?php 
                                    echo $activity['type'] === 'alert' ? 'exclamation-triangle' : 
                                        ($activity['type'] === 'medication' ? 'pills' : 
                                        ($activity['type'] === 'gps' ? 'map-marker-alt' : 
                                        ($activity['type'] === 'health' ? 'heartbeat' : 'user'))); 
                                ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php echo htmlspecialchars($activity['patient']); ?></div>
                                <div class="activity-description"><?php echo htmlspecialchars($activity['action']); ?></div>
                            </div>
                            <div class="activity-time"><?php echo $activity['time']; ?></div>
                </div>
                                <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Patient Distribution -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-pie"></i> Patient Distribution</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="patientDistributionChart"></canvas>
                    </div>
                </div>
                
                <!-- System Health -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-server"></i> System Health</h3>
                    </div>
                    <div class="system-health">
                        <div class="health-item">
                            <div class="health-label">CPU Usage</div>
                            <div class="health-bar">
                                <div class="health-fill" style="width: 45%"></div>
                            </div>
                            <div class="health-value">45%</div>
                        </div>
                        <div class="health-item">
                            <div class="health-label">Memory Usage</div>
                            <div class="health-bar">
                                <div class="health-fill" style="width: 62%"></div>
                            </div>
                            <div class="health-value">62%</div>
                        </div>
                        <div class="health-item">
                            <div class="health-label">Storage Usage</div>
                            <div class="health-bar">
                                <div class="health-fill" style="width: 78%"></div>
                            </div>
                            <div class="health-value">78%</div>
                        </div>
                        <div class="health-item">
                            <div class="health-label">Network</div>
                            <div class="health-bar">
                                <div class="health-fill" style="width: 23%"></div>
                            </div>
                            <div class="health-value">23%</div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Alerts Summary -->
                <div class="dashboard-card">
                    <div class="card-header">
                        <h3><i class="fas fa-bell"></i> Recent Alerts</h3>
                        <a href="alerts.php" class="card-link">View All</a>
                    </div>
                    <div class="alerts-summary">
                        <div class="alert-stat">
                            <div class="stat-number critical"><?php echo $dashboardData['critical_alerts']; ?></div>
                            <div class="stat-label">Critical</div>
                        </div>
                        <div class="alert-stat">
                            <div class="stat-number warning">12</div>
                            <div class="stat-label">Warning</div>
                        </div>
                        <div class="alert-stat">
                            <div class="stat-number info">28</div>
                            <div class="stat-label">Info</div>
                        </div>
                    </div>
                    <div class="recent-alerts-list">
                        <div class="alert-item critical">
                            <div class="alert-content">
                                <div class="alert-title">Heart Rate Alert</div>
                                <div class="alert-patient">Agnes B. - 2 min ago</div>
                            </div>
                            <div class="alert-status">Critical</div>
                        </div>
                        <div class="alert-item warning">
                            <div class="alert-content">
                                <div class="alert-title">GPS Boundary</div>
                                <div class="alert-patient">Willard V. - 8 min ago</div>
                            </div>
                            <div class="alert-status">Warning</div>
                        </div>
                        <div class="alert-item info">
                            <div class="alert-content">
                                <div class="alert-title">Medication Taken</div>
                                <div class="alert-patient">Kurt B. - 5 min ago</div>
                            </div>
                            <div class="alert-status">Info</div>
                        </div>
                </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/dashboard.js"></script>
</body>
</html> 