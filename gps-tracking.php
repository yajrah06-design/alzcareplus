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

// Sample GPS data (in production, this would come from the mobile app)
$gpsData = [
    [
        'patient_id' => 'ASC 0001',
        'address' => 'Bagtas, Tanza',
        'coordinates' => '14.3362° N, 120.8585° E',
        'time' => 'Today, 11:45 AM',
        'status' => 'Safe Zone'
    ],
    [
        'patient_id' => 'ASC 0002',
        'address' => 'Tanuan, Tanza',
        'coordinates' => '14.2937° N, 120.8298° E',
        'time' => 'Today, 09:17 PM',
        'status' => 'Near Boundary'
    ],
    [
        'patient_id' => 'ASC 0003',
        'address' => 'Incencio, Trece',
        'coordinates' => '14.2495° N, 120.8772° E',
        'time' => 'Today, 02:30 PM',
        'status' => 'Safe Zone'
    ],
    [
        'patient_id' => 'ASC 0004',
        'address' => 'Conchu, Trece',
        'coordinates' => '14.1539° N, 120.5257° E',
        'time' => 'Today, 06:20 AM',
        'status' => 'Outside Zone'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Tracking - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css">
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
            
            <!-- GPS Tracking Dashboard -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-map-marker-alt"></i> GPS Tracking</h2>
                    <select class="form-select">
                        <option>Last 24 Hours</option>
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>PATIENT ID</th>
                                <th>ADDRESS</th>
                                <th>COORDINATES</th>
                                <th>TIME</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($gpsData as $gps): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($gps['patient_id']); ?></td>
                                    <td><?php echo htmlspecialchars($gps['address']); ?></td>
                                    <td><?php echo htmlspecialchars($gps['coordinates']); ?></td>
                                    <td><?php echo htmlspecialchars($gps['time']); ?></td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-normal';
                                        if ($gps['status'] === 'Near Boundary') {
                                            $statusClass = 'status-warning';
                                        } elseif ($gps['status'] === 'Outside Zone') {
                                            $statusClass = 'status-critical';
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <?php echo htmlspecialchars($gps['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">View on Map</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="#" class="link">View all</a>
                </div>
            </div>
            
            <!-- Map Placeholder -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-map"></i> Live Map View</h2>
                </div>
                <div style="padding: 24px; text-align: center; background-color: #f9fafb; border-radius: 8px; margin: 24px;">
                    <i class="fas fa-map-marked-alt" style="font-size: 48px; color: #6b7280; margin-bottom: 16px;"></i>
                    <h3 style="color: #374151; margin-bottom: 8px;">GPS Data Integration</h3>
                    <p style="color: #6b7280; margin-bottom: 16px;">
                        This section will display real-time GPS tracking data from the AlzCare+ mobile application.
                    </p>
                    <div style="background-color: #e5e7eb; height: 300px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                        <div>
                            <i class="fas fa-mobile-alt" style="font-size: 32px; margin-bottom: 8px;"></i>
                            <p>Map view will be available when GPS data is received from the mobile app</p>
                        </div>
                    </div>
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
    </style>
    
    <script src="assets/js/gps-tracking.js"></script>
</body>
</html> 