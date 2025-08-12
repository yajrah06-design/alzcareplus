<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<aside class="sidebar">
    <div class="sidebar-header">
        <div class="logo big-logo">
            <i class="fas fa-heartbeat big-heart"></i>
            <span class="alzcare-text">AlzCare+</span>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <h3>MAIN</h3>
            <ul class="nav-list">
                <li class="nav-item <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                    <a href="dashboard.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'patient-profiles' ? 'active' : ''; ?>">
                    <a href="patient-profiles.php" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span>Patient Profiles</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'health-monitoring' ? 'active' : ''; ?>">
                    <a href="health-monitoring.php" class="nav-link">
                        <i class="fas fa-heartbeat"></i>
                        <span>Health Monitoring</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'medication-tracker' ? 'active' : ''; ?>">
                    <a href="medication-tracker.php" class="nav-link">
                        <i class="fas fa-pills"></i>
                        <span>Medication Tracker</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="nav-section">
            <h3>MONITORING</h3>
            <ul class="nav-list">
                <li class="nav-item <?php echo $current_page === 'gps-tracking' ? 'active' : ''; ?>">
                    <a href="gps-tracking.php" class="nav-link">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>GPS Tracking</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'alerts' ? 'active' : ''; ?>">
                    <a href="alerts.php" class="nav-link">
                        <i class="fas fa-bell"></i>
                        <span>Alerts & Notifications</span>
                    </a>
                </li>
            </ul>
        </div>
        
        <?php if ($user_role === 'admin'): ?>
        <div class="nav-section">
            <h3>ADMIN</h3>
            <ul class="nav-list">
                <li class="nav-item <?php echo $current_page === 'user-accounts' ? 'active' : ''; ?>">
                    <a href="user-accounts.php" class="nav-link">
                        <i class="fas fa-user-friends"></i>
                        <span>User Accounts</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'analytics' ? 'active' : ''; ?>">
                    <a href="analytics.php" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics & Reports</span>
                    </a>
                </li>
                <li class="nav-item <?php echo $current_page === 'system-settings' ? 'active' : ''; ?>">
                    <a href="system-settings.php" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span>System Settings</span>
                    </a>
                </li>
            </ul>
        </div>
        <?php endif; ?>
        
        <!-- Real-time Clock Section -->
        <div class="nav-section clock-section">
            <h3>TIME</h3>
            <div class="clock-display">
                <div class="current-time" id="sidebarTime"><?php echo date('H:i'); ?></div>
                <div class="current-date" id="sidebarDate"><?php echo date('l, M j, Y'); ?></div>
            </div>
            <div class="time-details">
                <div class="time-detail">
                    <i class="fas fa-calendar-day"></i>
                    <span id="sidebarDay"><?php echo date('l'); ?></span>
                </div>
                <div class="time-detail">
                    <i class="fas fa-clock"></i>
                    <span id="sidebarTimeZone"><?php echo date('T'); ?></span>
                </div>
            </div>
        </div>
    </nav>
</aside>

<script>
// Real-time clock update for sidebar
function updateSidebarTime() {
    const now = new Date();
    const timeElement = document.getElementById('sidebarTime');
    const dateElement = document.getElementById('sidebarDate');
    const dayElement = document.getElementById('sidebarDay');
    const timeZoneElement = document.getElementById('sidebarTimeZone');
    
    if (timeElement) {
        timeElement.textContent = now.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
    }
    
    if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('en-US', { 
            weekday: 'long', 
            month: 'short', 
            day: 'numeric', 
            year: 'numeric' 
        });
    }
    
    if (dayElement) {
        dayElement.textContent = now.toLocaleDateString('en-US', { 
            weekday: 'long' 
        });
    }
    
    if (timeZoneElement) {
        timeZoneElement.textContent = now.toLocaleDateString('en-US', { 
            timeZoneName: 'short' 
        }).split(', ')[1];
    }
}

// Update time every second
setInterval(updateSidebarTime, 1000);

// Initial update
updateSidebarTime();
</script> 