<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Enhanced health data with more comprehensive information
$healthRecords = [
    [
        'patient_id' => 'ASC 0001',
        'patient_name' => 'Agnes B.',
        'age' => 72,
        'heart_rate' => 78,
        'blood_pressure_systolic' => 128,
        'blood_pressure_diastolic' => 82,
        'blood_oxygen' => 96,
        'temperature' => 98.2,
        'blood_glucose' => 95,
        'weight' => 65.2,
        'sleep_hours' => 7.5,
        'activity_level' => 'Moderate',
        'medication_taken' => true,
        'last_updated' => 'Today, 10:30 AM',
        'status' => 'Normal',
        'trend' => 'stable',
        'alerts' => []
    ],
    [
        'patient_id' => 'ASC 0002',
        'patient_name' => 'Bautista K.',
        'age' => 68,
        'heart_rate' => 102,
        'blood_pressure_systolic' => 142,
        'blood_pressure_diastolic' => 90,
        'blood_oxygen' => 94,
        'temperature' => 99.1,
        'blood_glucose' => 120,
        'weight' => 70.1,
        'sleep_hours' => 6.2,
        'activity_level' => 'Low',
        'medication_taken' => false,
        'last_updated' => 'Today, 01:00 PM',
        'status' => 'Elevated',
        'trend' => 'increasing',
        'alerts' => ['High Blood Pressure', 'Missed Medication']
    ],
    [
        'patient_id' => 'ASC 0003',
        'patient_name' => 'Yanson A.',
        'age' => 75,
        'heart_rate' => 85,
        'blood_pressure_systolic' => 118,
        'blood_pressure_diastolic' => 76,
        'blood_oxygen' => 98,
        'temperature' => 97.8,
        'blood_glucose' => 88,
        'weight' => 62.8,
        'sleep_hours' => 8.1,
        'activity_level' => 'High',
        'medication_taken' => true,
        'last_updated' => 'Today, 06:15 AM',
        'status' => 'Normal',
        'trend' => 'stable',
        'alerts' => []
    ],
    [
        'patient_id' => 'ASC 0004',
        'patient_name' => 'Valencia W.',
        'age' => 70,
        'heart_rate' => 110,
        'blood_pressure_systolic' => 150,
        'blood_pressure_diastolic' => 95,
        'blood_oxygen' => 92,
        'temperature' => 100.2,
        'blood_glucose' => 145,
        'weight' => 68.5,
        'sleep_hours' => 5.8,
        'activity_level' => 'Very Low',
        'medication_taken' => false,
        'last_updated' => 'Today, 05:37 PM',
        'status' => 'Critical',
        'trend' => 'decreasing',
        'alerts' => ['Critical Heart Rate', 'High Temperature', 'Low Blood Oxygen', 'Missed Medication']
    ],
    [
        'patient_id' => 'ASC 0005',
        'patient_name' => 'Swift T.',
        'age' => 69,
        'heart_rate' => 72,
        'blood_pressure_systolic' => 125,
        'blood_pressure_diastolic' => 80,
        'blood_oxygen' => 97,
        'temperature' => 98.6,
        'blood_glucose' => 92,
        'weight' => 63.4,
        'sleep_hours' => 7.8,
        'activity_level' => 'Moderate',
        'medication_taken' => true,
        'last_updated' => 'Today, 09:45 AM',
        'status' => 'Normal',
        'trend' => 'stable',
        'alerts' => []
    ],
    [
        'patient_id' => 'ASC 0006',
        'patient_name' => 'Styles H.',
        'age' => 71,
        'heart_rate' => 95,
        'blood_pressure_systolic' => 135,
        'blood_pressure_diastolic' => 88,
        'blood_oxygen' => 95,
        'temperature' => 98.9,
        'blood_glucose' => 110,
        'weight' => 66.7,
        'sleep_hours' => 6.5,
        'activity_level' => 'Low',
        'medication_taken' => true,
        'last_updated' => 'Today, 11:20 AM',
        'status' => 'Elevated',
        'trend' => 'stable',
        'alerts' => ['Elevated Blood Pressure']
    ]
];

// Calculate statistics
$totalPatients = count($healthRecords);
$normalCount = count(array_filter($healthRecords, fn($r) => $r['status'] === 'Normal'));
$elevatedCount = count(array_filter($healthRecords, fn($r) => $r['status'] === 'Elevated'));
$criticalCount = count(array_filter($healthRecords, fn($r) => $r['status'] === 'Critical'));
$medicationTakenCount = count(array_filter($healthRecords, fn($r) => $r['medication_taken']));
$alertsCount = array_sum(array_map(fn($r) => count($r['alerts']), $healthRecords));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Monitoring - AlzCare+</title>
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
            
            <!-- Real-time Status Overview -->
            <div class="health-overview-grid">
                <div class="health-stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $totalPatients; ?></h3>
                        <p>Total Patients</p>
                    </div>
                </div>
                
                <div class="health-stat-card">
                    <div class="stat-icon normal">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $normalCount; ?></h3>
                        <p>Normal Status</p>
                    </div>
                </div>
                
                <div class="health-stat-card">
                    <div class="stat-icon warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $elevatedCount; ?></h3>
                        <p>Elevated</p>
                    </div>
                </div>
                
                <div class="health-stat-card">
                    <div class="stat-icon critical">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $criticalCount; ?></h3>
                        <p>Critical</p>
                    </div>
                </div>
                
                <div class="health-stat-card">
                    <div class="stat-icon success">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $medicationTakenCount; ?></h3>
                        <p>Medication Taken</p>
                    </div>
                </div>
                
                <div class="health-stat-card">
                    <div class="stat-icon alert">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?php echo $alertsCount; ?></h3>
                        <p>Active Alerts</p>
                    </div>
                </div>
            </div>
            
            <!-- Health Analytics Dashboard -->
            <div class="analytics-grid">
                <!-- Health Status Chart -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-bar"></i> Health Status Distribution</h3>
                        <div class="chart-legend" id="healthStatusLegend">
                            <span class="legend-item"><span class="legend-color normal"></span>Normal</span>
                            <span class="legend-item"><span class="legend-color warning"></span>Elevated</span>
                            <span class="legend-item"><span class="legend-color critical"></span>Critical</span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="healthStatusChart"></canvas>
                    </div>
                </div>
            
                <!-- Vital Signs Trends -->
            <div class="content-card">
                <div class="card-header">
                        <h3><i class="fas fa-chart-line"></i> Vital Signs Trends</h3>
                        <select id="vitalSignSelect" class="vital-sign-select">
                            <option value="heart_rate">Heart Rate</option>
                            <option value="blood_pressure">Blood Pressure</option>
                            <option value="temperature">Temperature</option>
                            <option value="blood_oxygen">Blood Oxygen</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="vitalSignsChart"></canvas>
                </div>
                </div>
            </div>
            
            <!-- Health Records Table -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-heartbeat"></i> Real-time Health Monitoring</h2>
                    <div class="card-actions">
                        <button class="btn btn-secondary" onclick="exportData()">
                            <i class="fas fa-download"></i>
                            Export Data
                        </button>
                        <button class="btn btn-primary" onclick="openAlertSettings()">
                            <i class="fas fa-cog"></i>
                            Alert Settings
                        </button>
                    </div>
                </div>
                
                <!-- Search and Filter Section - Moved here -->
                <div class="search-filter-section inside-card">
                    <div class="search-container">
                        <div class="search-box">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" id="searchInput" placeholder="Search patients by name, ID, or status..." class="search-input">
                            <button class="search-clear" id="clearSearch" style="display: none;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="filter-container">
                            <select id="statusFilter" class="filter-select">
                                <option value="">All Status</option>
                                <option value="Normal">Normal</option>
                                <option value="Elevated">Elevated</option>
                                <option value="Critical">Critical</option>
                            </select>
                        </div>
                        <div class="filter-container">
                            <select id="trendFilter" class="filter-select">
                                <option value="">All Trends</option>
                                <option value="stable">Stable</option>
                                <option value="increasing">Increasing</option>
                                <option value="decreasing">Decreasing</option>
                            </select>
                        </div>
                        <button class="btn btn-primary" onclick="refreshData()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="data-table" id="healthTable">
                        <thead>
                            <tr>
                                <th>PATIENT</th>
                                <th>VITAL SIGNS</th>
                                <th>HEALTH METRICS</th>
                                <th>MEDICATION</th>
                                <th>STATUS</th>
                                <th>TREND</th>
                                <th>ALERTS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="healthTableBody">
                            <?php foreach ($healthRecords as $record): ?>
                                <tr data-patient-id="<?php echo htmlspecialchars($record['patient_id']); ?>">
                                    <td>
                                        <div class="patient-info">
                                            <div class="patient-name"><?php echo htmlspecialchars($record['patient_name']); ?></div>
                                            <div class="patient-id"><?php echo htmlspecialchars($record['patient_id']); ?></div>
                                            <div class="patient-age"><?php echo $record['age']; ?> years</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="vital-signs">
                                            <div class="vital-item">
                                                <i class="fas fa-heartbeat"></i>
                                                <span class="<?php echo $record['heart_rate'] > 100 ? 'warning' : 'normal'; ?>"><?php echo $record['heart_rate']; ?> bpm</span>
                                            </div>
                                            <div class="vital-item">
                                                <i class="fas fa-tint"></i>
                                                <span class="<?php echo $record['blood_pressure_systolic'] > 140 ? 'warning' : 'normal'; ?>"><?php echo $record['blood_pressure_systolic']; ?>/<?php echo $record['blood_pressure_diastolic']; ?></span>
                                            </div>
                                            <div class="vital-item">
                                                <i class="fas fa-lungs"></i>
                                                <span class="<?php echo $record['blood_oxygen'] < 95 ? 'warning' : 'normal'; ?>"><?php echo $record['blood_oxygen']; ?>%</span>
                                            </div>
                                            <div class="vital-item">
                                                <i class="fas fa-thermometer-half"></i>
                                                <span class="<?php echo $record['temperature'] > 99 ? 'warning' : 'normal'; ?>"><?php echo $record['temperature']; ?>°F</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="health-metrics">
                                            <div class="metric-item">
                                                <span class="metric-label">Weight:</span>
                                                <span class="metric-value"><?php echo $record['weight']; ?> kg</span>
                                            </div>
                                            <div class="metric-item">
                                                <span class="metric-label">Sleep:</span>
                                                <span class="metric-value <?php echo $record['sleep_hours'] < 7 ? 'warning' : 'normal'; ?>"><?php echo $record['sleep_hours']; ?> hrs</span>
                                            </div>
                                            <div class="metric-item">
                                                <span class="metric-label">Activity:</span>
                                                <span class="metric-value"><?php echo $record['activity_level']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="medication-status">
                                            <div class="medication-indicator <?php echo $record['medication_taken'] ? 'taken' : 'missed'; ?>">
                                                <i class="fas fa-<?php echo $record['medication_taken'] ? 'check-circle' : 'times-circle'; ?>"></i>
                                                <span><?php echo $record['medication_taken'] ? 'Taken' : 'Missed'; ?></span>
                                            </div>
                                            <div class="last-updated"><?php echo htmlspecialchars($record['last_updated']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-normal';
                                        if ($record['status'] === 'Elevated') {
                                            $statusClass = 'status-warning';
                                        } elseif ($record['status'] === 'Critical') {
                                            $statusClass = 'status-critical';
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($record['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="trend-indicator <?php echo $record['trend']; ?>">
                                            <i class="fas fa-<?php 
                                                echo $record['trend'] === 'stable' ? 'minus' : 
                                                    ($record['trend'] === 'increasing' ? 'arrow-up' : 'arrow-down'); 
                                            ?>"></i>
                                            <span><?php echo ucfirst($record['trend']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="alerts-container">
                                            <?php if (empty($record['alerts'])): ?>
                                                <span class="no-alerts">No alerts</span>
                                            <?php else: ?>
                                                <?php foreach ($record['alerts'] as $alert): ?>
                                                    <span class="alert-badge"><?php echo htmlspecialchars($alert); ?></span>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary" title="View Details" onclick="viewHealthDetails('<?php echo htmlspecialchars($record['patient_id']); ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary" title="History" onclick="viewHealthHistory('<?php echo htmlspecialchars($record['patient_id']); ?>')">
                                                <i class="fas fa-history"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" title="Send Alert" onclick="sendAlert('<?php echo htmlspecialchars($record['patient_id']); ?>')">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <span id="healthCount"><?php echo count($healthRecords); ?> patients monitored</span>
                    <div class="footer-actions">
                        <button class="btn btn-sm btn-outline" onclick="toggleAutoRefresh()">
                            <i class="fas fa-sync-alt"></i>
                            <span id="autoRefreshText">Auto Refresh: OFF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Health Details Modal -->
    <div id="healthDetailsModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-heartbeat"></i> Patient Health Details</h3>
                <span class="close" onclick="closeModal('healthDetailsModal')">&times;</span>
            </div>
            <div id="healthDetailsContent" class="modal-body">
                <!-- Health details will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Health History Modal -->
    <div id="healthHistoryModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <h3><i class="fas fa-history"></i> Health History</h3>
                <span class="close" onclick="closeModal('healthHistoryModal')">&times;</span>
            </div>
            <div id="healthHistoryContent" class="modal-body">
                <!-- Health history will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Alert Settings Modal -->
    <div id="alertSettingsModal" class="modal">
        <div class="modal-content small-modal">
            <div class="modal-header">
                <h3><i class="fas fa-cog"></i> Alert Settings</h3>
                <span class="close" onclick="closeModal('alertSettingsModal')">&times;</span>
            </div>
            <form id="alertSettingsForm" class="modal-form">
                <div class="form-group">
                    <label>Heart Rate Threshold (bpm)</label>
                    <div class="threshold-inputs">
                        <input type="number" id="hrMin" placeholder="Min" value="60">
                        <span>to</span>
                        <input type="number" id="hrMax" placeholder="Max" value="100">
                    </div>
                </div>
                <div class="form-group">
                    <label>Blood Pressure Threshold (mmHg)</label>
                    <div class="threshold-inputs">
                        <input type="number" id="bpMin" placeholder="Min" value="90">
                        <span>to</span>
                        <input type="number" id="bpMax" placeholder="Max" value="140">
                    </div>
                </div>
                <div class="form-group">
                    <label>Temperature Threshold (°F)</label>
                    <div class="threshold-inputs">
                        <input type="number" id="tempMin" placeholder="Min" value="97" step="0.1">
                        <span>to</span>
                        <input type="number" id="tempMax" placeholder="Max" value="99" step="0.1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Blood Oxygen Threshold (%)</label>
                    <div class="threshold-inputs">
                        <input type="number" id="o2Min" placeholder="Min" value="95">
                        <span>to</span>
                        <input type="number" id="o2Max" placeholder="Max" value="100">
                    </div>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('alertSettingsModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/health-monitoring.js"></script>
</body>
</html> 