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

// Load activity log
require_once __DIR__ . '/includes/database.php';
$activityManager = new ActivityManager();
$recentActivities = $activityManager->getRecentActivities(20);

// Sample alert configuration data
$alertConfigs = [
    [
        'alert_name' => 'High Heart Rate',
        'condition' => 'Heart Rate > 100 Bpm',
        'threshold' => '100 Bpm',
        'notification_type' => 'SMS&App',
        'recipients' => 'Family',
        'status' => 'Active'
    ],
    [
        'alert_name' => 'Missed Medication',
        'condition' => 'Medication Not Taken Within 1 Hour',
        'threshold' => '60 Min',
        'notification_type' => 'SMS&App',
        'recipients' => 'Caregiver',
        'status' => 'Active'
    ],
    [
        'alert_name' => 'GPS Boundary',
        'condition' => 'Outside Safe Zone',
        'threshold' => '500M Radius',
        'notification_type' => 'SMS&App',
        'recipients' => 'Family',
        'status' => 'Active'
    ],
    [
        'alert_name' => 'Low Activity',
        'condition' => 'Steps < 500 In 12 Hours',
        'threshold' => '500 Steps',
        'notification_type' => 'SMS&App',
        'recipients' => 'Family, Caregiver',
        'status' => 'Active'
    ]
];

// Sample alert history data
$alertHistory = [
    [
        'datetime' => 'Today, 08:32 AM',
        'patient_id' => 'ASC 0001',
        'alert_type' => 'High Heart Rate',
        'status' => 'Critical',
        'action' => 'Medication Adjusted - Monitoring'
    ],
    [
        'datetime' => 'Today, 09:15 AM',
        'patient_id' => 'ASC 0002',
        'alert_type' => 'Missed Medication',
        'status' => 'Warning',
        'action' => 'Caregiver Notified - Pending'
    ],
    [
        'datetime' => 'Yesterday, 03:45 PM',
        'patient_id' => 'ASC 0003',
        'alert_type' => 'GPS Boundary',
        'status' => 'Critical',
        'action' => 'Family Member Can\'t Locate The Patient'
    ],
    [
        'datetime' => 'Yesterday, 11:20 AM',
        'patient_id' => 'ASC 0004',
        'alert_type' => 'Low Activity',
        'status' => 'Warning',
        'action' => 'Family Notified - No Action Needed'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alerts & Notifications - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Alerts & Notifications Dashboard -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-cog"></i> Alerts Configuration</h2>
                    <div class="card-actions">
                        <input id="configSearch" type="text" class="form-input" placeholder="Search alerts..." style="max-width: 240px;" />
                        <button class="btn btn-primary" id="addAlertBtn">
                            <i class="fas fa-plus"></i>
                            Add New Alert
                        </button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table" id="configsTable">
                        <thead>
                            <tr>
                                <th style="min-width:180px">Alert</th>
                                <th>Condition</th>
                                <th>Threshold</th>
                                <th>Notify</th>
                                <th>Recipients</th>
                                <th>Status</th>
                                <th>Updated</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="configsTbody"></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Recent Activities -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-list"></i> Recent Activities</h2>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>TIME</th>
                                <th>USER</th>
                                <th>ACTION</th>
                                <th>ENTITY</th>
                                <th>ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentActivities as $act): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($act['created_at']); ?></td>
                                    <td><?php echo htmlspecialchars($act['user_name']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $act['action'] === 'deleted' ? 'status-danger' : ($act['action'] === 'updated' ? 'status-warning' : 'status-normal'); ?>">
                                            <?php echo htmlspecialchars(ucfirst($act['action'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars(ucfirst($act['entity'])); ?></td>
                                    <td><?php echo htmlspecialchars($act['entity_id']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Alert History -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-history"></i> Alerts History</h2>
                    <select class="form-select" id="historyRange">
                        <option value="7">Last 7 days</option>
                        <option value="30" selected>Last 30 days</option>
                        <option value="90">Last 90 days</option>
                    </select>
                </div>
                <div class="table-container">
                    <table class="data-table" id="historyTable">
                        <thead>
                            <tr>
                                <th>DATE&TIME</th>
                                <th>PATIENT ID</th>
                                <th>ALERT TYPE</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="historyTbody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        .form-select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            background-color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-secondary {
            background-color: #6b7280;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        
        .btn-danger {
            background-color: #dc2626;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #b91c1c;
        }
    </style>
    
    <script src="assets/js/alerts.js"></script>
</body>
</html> 