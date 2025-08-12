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

// Sample user data
$users = [
    [
        'user_id' => 'ADM-001',
        'name' => 'Dr. Kurt Willard',
        'email' => 'd.willard@alzcare.com',
        'role' => 'Admin',
        'assigned_patients' => 'All',
        'last_login' => 'Today, 07:45 AM',
        'status' => 'Active'
    ],
    [
        'user_id' => 'NUR-002',
        'name' => 'Michael Chen',
        'email' => 'm.chen@alzcare.com',
        'role' => 'Nurse',
        'assigned_patients' => '6',
        'last_login' => 'Today, 08:30 AM',
        'status' => 'Active'
    ],
    [
        'user_id' => 'CAR-003',
        'name' => 'Emma Watson',
        'email' => 'e.watson@alzcare.com',
        'role' => 'Caregiver',
        'assigned_patients' => '8',
        'last_login' => 'Today, 09:15 AM',
        'status' => 'Active'
    ],
    [
        'user_id' => 'FAM-004',
        'name' => 'Brad Pitt',
        'email' => 'b.pitt@gmail.com',
        'role' => 'Family',
        'assigned_patients' => '1',
        'last_login' => 'Today, 10:00 AM',
        'status' => 'Active'
    ],
    [
        'user_id' => 'NUR-004',
        'name' => 'Liza Soberano',
        'email' => 'l.soberano@alzcare.com',
        'role' => 'Nurse',
        'assigned_patients' => '10',
        'last_login' => 'Yesterday, 05:30 PM',
        'status' => 'Inactive'
    ]
];

// Login frequency data for chart
$loginFrequency = [
    'Admins' => 45,
    'Nurses' => 125,
    'Caregivers' => 75,
    'Family' => 65
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Accounts - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Pass current user data to JavaScript
        window.currentUser = {
            id: '<?php echo $_SESSION['user_id']; ?>',
            name: '<?php echo addslashes($_SESSION['user_name']); ?>',
            email: '<?php echo addslashes($_SESSION['user_email']); ?>',
            role: '<?php echo $_SESSION['user_role']; ?>'
        };
    </script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- User Management Dashboard -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-user-friends"></i> Users Accounts</h2>
                    <div class="card-actions">
                        <input id="userSearch" type="text" class="form-input" placeholder="Search users..." style="max-width: 240px;" />
                        <button id="addUserBtn" class="btn btn-primary"><i class="fas fa-plus"></i> New Account</button>
                    </div>
                </div>
                <div class="table-container">
                    <table class="data-table" id="usersTable">
                        <thead>
                            <tr>
                                <th>USER ID</th>
                                <th>NAME</th>
                                <th>EMAIL</th>
                                <th>ROLE</th>
                                <th>ASSIGNED PATIENTS</th>
                                <th>LAST LOGIN</th>
                                <th>STATUS</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="usersTbody"></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quick Admin Actions (replaces Activity Chart) -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-tools"></i> Admin Tools</h2>
                </div>
                <div class="quick-actions-grid" style="padding:16px;">
                    <a href="#" class="quick-action-item" onclick="document.getElementById('addUserBtn').click();return false;">
                        <i class="fas fa-user-plus"></i>
                        <span>Create User</span>
                    </a>
                    <a href="patient-profiles.php" class="quick-action-item">
                        <i class="fas fa-users"></i>
                        <span>Manage Patients</span>
                    </a>
                    <a href="alerts.php" class="quick-action-item">
                        <i class="fas fa-bell"></i>
                        <span>Alerts & Notifications</span>
                    </a>
                    <a href="system-settings.php" class="quick-action-item">
                        <i class="fas fa-cog"></i>
                        <span>System Settings</span>
                    </a>
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
        .chart-container {
            height: 300px !important;
            min-height: 300px !important;
            max-height: 300px !important;
            position: relative;
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

        /* User table column sizing and text overflow */
        #usersTable { table-layout: fixed; width: 100%; }
        #usersTable th:nth-child(1),
        #usersTable td:nth-child(1) { width: 110px; }
        #usersTable th:nth-child(2),
        #usersTable td:nth-child(2) { width: 18%; }
        #usersTable th:nth-child(3),
        #usersTable td:nth-child(3) { width: 24%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #usersTable th:nth-child(4),
        #usersTable td:nth-child(4) { width: 10%; }
        #usersTable th:nth-child(5),
        #usersTable td:nth-child(5) { width: 12%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #usersTable th:nth-child(6),
        #usersTable td:nth-child(6) { width: 14%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        #usersTable th:nth-child(7),
        #usersTable td:nth-child(7) { width: 10%; }
        #usersTable th:nth-child(8),
        #usersTable td:nth-child(8) { width: 12%; }
    </style>
    
    <script>
        // Search filter
        document.addEventListener('DOMContentLoaded', function(){
            const s = document.getElementById('userSearch');
            if (s) s.addEventListener('input', function(){
                const term = this.value.toLowerCase();
                document.querySelectorAll('#usersTbody tr').forEach(tr => {
                    tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
                });
            });
        });
    </script>
    <script src="assets/js/user-accounts.js"></script>
    <script src="assets/js/user-accounts.js"></script>
</body>
</html> 