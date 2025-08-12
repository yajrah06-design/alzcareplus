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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_general'])) {
        $success_message = "General settings saved successfully!";
    } elseif (isset($_POST['save_notifications'])) {
        $success_message = "Notification settings saved successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings - AlzCare+</title>
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
            
            <!-- System Settings Dashboard -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Settings Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- General Settings -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-cog"></i> General Settings</h2>
                    </div>
                    <div class="card-body" style="padding: 8px;">
                        <form id="generalSettingsForm">
                            <div class="form-group">
                                <label for="system_name">System Name</label>
                                <input type="text" id="system_name" name="system_name" value="AlzCare+" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label for="heart_rate_threshold">Default Alert Threshold (Heart Rate)</label>
                                <input type="number" id="heart_rate_threshold" name="heart_rate_threshold" value="100" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label for="date_format">Date Format</label>
                                <select id="date_format" name="date_format" class="form-select">
                                    <option value="MM/DD/YYYY" selected>MM/DD/YYYY</option>
                                    <option value="DD/MM/YYYY">DD/MM/YYYY</option>
                                    <option value="YYYY-MM-DD">YYYY-MM-DD</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="data_retention">Data Retention Period</label>
                                <select id="data_retention" name="data_retention" class="form-select">
                                    <option value="30">30 days</option>
                                    <option value="60">60 days</option>
                                    <option value="90" selected>90 days</option>
                                    <option value="180">180 days</option>
                                    <option value="365">1 year</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Notification Settings -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-bell"></i> Notification Settings</h2>
                    </div>
                    <div class="card-body" style="padding: 8px;">
                        <form id="notificationSettingsForm">
                            <div class="form-group">
                                <label for="sms_notification">SMS Notification</label>
                                <select id="sms_notification" name="sms_notification" class="form-select">
                                    <option value="enabled" selected>Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="app_notification">App Notification</label>
                                <select id="app_notification" name="app_notification" class="form-select">
                                    <option value="enabled" selected>Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="email_notification">Email Notification</label>
                                <select id="email_notification" name="email_notification" class="form-select">
                                    <option value="enabled" selected>Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="critical_calls">Critical Alert Phone Calls</label>
                                <select id="critical_calls" name="critical_calls" class="form-select">
                                    <option value="enabled" selected>Enabled</option>
                                    <option value="disabled">Disabled</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="assets/js/system-settings.js"></script>
</body>
</html> 