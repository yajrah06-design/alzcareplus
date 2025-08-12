<?php
// Get current page name for dynamic title
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define page titles
$page_titles = [
    'dashboard' => 'Admin Dashboard',
    'patient-profiles' => 'Patient Profiles',
    'health-monitoring' => 'Health Monitoring',
    'medication-tracker' => 'Medication Tracker',
    'gps-tracking' => 'GPS Tracking',
    'alerts' => 'Alerts & Notifications',
    'user-accounts' => 'User Accounts',
    'analytics' => 'Analytics & Reports',
    'system-settings' => 'System Settings'
];

// Get the page title, default to 'Dashboard' if not found
$page_title = $page_titles[$current_page] ?? 'Dashboard';
?>

<!-- Header -->
<header class="header">
    <div class="header-left">
        <h1><?php echo htmlspecialchars($page_title); ?></h1>
    </div>
    
    <div class="header-right">
        <!-- Notifications Dropdown -->
        <div class="notification-dropdown">
            <button class="icon-btn" title="Notifications" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-badge" id="notificationCount">0</span>
            </button>
            <div class="notification-content" id="notificationDropdown">
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <button class="mark-all-read" onclick="markAllNotificationsRead()">
                        <i class="fas fa-check-double"></i>
                        Mark all read
                    </button>
                </div>
                <div class="notification-list" id="notificationList">
                    <!-- Notifications will be populated by JavaScript -->
                </div>
                <div class="notification-footer">
                    <a href="#" onclick="viewAllNotifications()">View all notifications</a>
                </div>
            </div>
        </div>

        <!-- Messages Dropdown -->
        <div class="message-dropdown">
            <button class="icon-btn" title="Messages" onclick="toggleMessages()">
                <i class="fas fa-envelope"></i>
                <span class="message-badge" id="messageCount">0</span>
            </button>
            <div class="message-content" id="messageDropdown">
                <div class="message-header">
                    <h3>Messages</h3>
                    <button class="compose-btn" onclick="composeMessage()">
                        <i class="fas fa-plus"></i>
                        Compose
                    </button>
                </div>
                <div class="message-list" id="messageList">
                    <!-- Messages will be populated by JavaScript -->
                </div>
                <div class="message-footer">
                    <a href="#" onclick="viewAllMessages()">View all messages</a>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="user-dropdown">
            <button class="user-dropdown-btn" onclick="toggleUserDropdown()">
                <?php if (!empty($_SESSION['profile_image'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile" class="user-profile-image">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
                <span><?php echo htmlspecialchars($user_name); ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-dropdown-content" id="userDropdown">
                <div class="user-dropdown-header">
                    <?php if (!empty($_SESSION['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile" class="dropdown-profile-image">
                    <?php else: ?>
                        <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                    <div class="user-dropdown-name"><?php echo htmlspecialchars($user_name); ?></div>
                    <div class="user-dropdown-email"><?php echo htmlspecialchars($user_email); ?></div>
                </div>
                <div class="user-dropdown-menu">
                    <a href="#" class="user-dropdown-item" onclick="openProfile()">
                        <i class="fas fa-user-circle"></i>
                        <span>Profile</span>
                    </a>
                    <a href="#" class="user-dropdown-item" onclick="openSettings()">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <div class="user-dropdown-divider"></div>
                    <button class="theme-toggle" onclick="toggleTheme()">
                        <i class="fas fa-moon"></i>
                        <span>Dark Mode</span>
                    </button>
                    <div class="user-dropdown-divider"></div>
                    <a href="#" class="user-dropdown-item" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Profile Modal -->
<div class="modal" id="profileModal">
    <div class="modal-content profile-modal">
        <div class="modal-header">
            <h3><i class="fas fa-user-circle"></i> User Profile</h3>
            <button class="close" onclick="closeProfile()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="profile-section">
                <div class="profile-avatar-section">
                    <div class="profile-avatar">
                        <?php if (!empty($_SESSION['profile_image'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['profile_image']); ?>" alt="Profile" id="profileAvatar">
                        <?php else: ?>
                            <img src="assets/images/default-avatar.png" alt="Profile" id="profileAvatar">
                        <?php endif; ?>
                        <div class="avatar-overlay">
                            <button class="change-avatar-btn" onclick="changeAvatar()">
                                <i class="fas fa-camera"></i>
                            </button>
                        </div>
                    </div>
                    <div class="profile-status">
                        <span class="status-indicator online"></span>
                        <span class="status-text">Online</span>
                    </div>
                </div>
                <div class="profile-info">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profileName">Full Name <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="profileName" value="<?php echo htmlspecialchars($user_name); ?>" placeholder="Enter your full name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="profileEmail">Email Address <span class="required">*</span></label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="profileEmail" value="<?php echo htmlspecialchars($user_email); ?>" placeholder="Enter your email">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profilePhone">Phone Number</label>
                            <div class="input-wrapper">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="tel" id="profilePhone" value="<?php echo htmlspecialchars($_SESSION['user_phone'] ?? ''); ?>" placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="profileDepartment">Department</label>
                            <div class="input-wrapper">
                                <i class="fas fa-building input-icon"></i>
                                <input type="text" id="profileDepartment" placeholder="Enter department">
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="profileRole">Role</label>
                            <div class="input-wrapper">
                                <i class="fas fa-id-badge input-icon"></i>
                                <input type="text" value="Administrator" readonly class="readonly-input">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="profileLocation">Location</label>
                            <div class="input-wrapper">
                                <i class="fas fa-map-marker-alt input-icon"></i>
                                <input type="text" id="profileLocation" placeholder="Enter location">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="profile-actions">
                <button class="btn btn-primary" onclick="saveProfile()">
                    <i class="fas fa-save"></i> Save Changes
                </button>
                <button class="btn btn-secondary" onclick="closeProfile()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div class="modal" id="settingsModal">
    <div class="modal-content settings-modal">
        <div class="modal-header">
            <h3><i class="fas fa-cog"></i> System Settings</h3>
            <button class="close" onclick="closeSettings()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="showSettingsTab('general')">
                    <i class="fas fa-cog"></i> General
                </button>
                <button class="tab-btn" onclick="showSettingsTab('notifications')">
                    <i class="fas fa-bell"></i> Notifications
                </button>
                <button class="tab-btn" onclick="showSettingsTab('security')">
                    <i class="fas fa-shield-alt"></i> Security
                </button>
                <button class="tab-btn" onclick="showSettingsTab('appearance')">
                    <i class="fas fa-palette"></i> Appearance
                </button>
            </div>
            
            <div class="settings-content">
                <!-- General Settings -->
                <div class="settings-tab active" id="generalTab">
                    <h4><i class="fas fa-cog"></i> General Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="languageSelect">Language</label>
                            <div class="input-wrapper">
                                <i class="fas fa-globe input-icon"></i>
                                <select id="languageSelect">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                    <option value="it">Italian</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="timezoneSelect">Time Zone</label>
                            <div class="input-wrapper">
                                <i class="fas fa-clock input-icon"></i>
                                <select id="timezoneSelect">
                                    <option value="UTC">UTC</option>
                                    <option value="EST">Eastern Time</option>
                                    <option value="PST">Pacific Time</option>
                                    <option value="CST">Central Time</option>
                                    <option value="MST">Mountain Time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="refreshInterval">Auto-refresh interval</label>
                        <div class="input-wrapper">
                            <i class="fas fa-sync-alt input-icon"></i>
                            <select id="refreshInterval">
                                <option value="30">30 seconds</option>
                                <option value="60">1 minute</option>
                                <option value="300">5 minutes</option>
                                <option value="600">10 minutes</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="settings-tab" id="notificationsTab">
                    <h4><i class="fas fa-bell"></i> Notification Preferences</h4>
                    <div class="notification-settings">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="emailNotifications" checked>
                                <span class="checkmark"></span>
                                <div class="checkbox-content">
                                    <span class="checkbox-title">Email notifications</span>
                                    <span class="checkbox-description">Receive notifications via email</span>
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="pushNotifications" checked>
                                <span class="checkmark"></span>
                                <div class="checkbox-content">
                                    <span class="checkbox-title">Push notifications</span>
                                    <span class="checkbox-description">Receive push notifications in browser</span>
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="criticalAlerts" checked>
                                <span class="checkmark"></span>
                                <div class="checkbox-content">
                                    <span class="checkbox-title">Critical alerts</span>
                                    <span class="checkbox-description">Receive critical patient alerts</span>
                                </div>
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" id="weeklyReports">
                                <span class="checkmark"></span>
                                <div class="checkbox-content">
                                    <span class="checkbox-title">Weekly reports</span>
                                    <span class="checkbox-description">Receive weekly summary reports</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="settings-tab" id="securityTab">
                    <h4><i class="fas fa-shield-alt"></i> Security Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="currentPassword">Current Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" id="currentPassword" placeholder="Enter current password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="newPassword">New Password</label>
                            <div class="input-wrapper">
                                <i class="fas fa-key input-icon"></i>
                                <input type="password" id="newPassword" placeholder="Enter new password">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="input-wrapper">
                            <i class="fas fa-key input-icon"></i>
                            <input type="password" id="confirmPassword" placeholder="Confirm new password">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" id="twoFactorAuth">
                            <span class="checkmark"></span>
                            <div class="checkbox-content">
                                <span class="checkbox-title">Enable two-factor authentication</span>
                                <span class="checkbox-description">Add an extra layer of security</span>
                            </div>
                        </label>
                    </div>
                    <button class="btn btn-primary" onclick="changePassword()">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </div>

                <!-- Appearance Settings -->
                <div class="settings-tab" id="appearanceTab">
                    <h4><i class="fas fa-palette"></i> Appearance Settings</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="themeSelect">Theme</label>
                            <div class="input-wrapper">
                                <i class="fas fa-moon input-icon"></i>
                                <select id="themeSelect" onchange="changeTheme(this.value)">
                                    <option value="light">Light</option>
                                    <option value="dark">Dark</option>
                                    <option value="auto">Auto</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fontSizeSelect">Font Size</label>
                            <div class="input-wrapper">
                                <i class="fas fa-text-height input-icon"></i>
                                <select id="fontSizeSelect">
                                    <option value="small">Small</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="large">Large</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="settings-actions">
                <button class="btn btn-primary" onclick="saveSettings()">
                    <i class="fas fa-save"></i> Save Settings
                </button>
                <button class="btn btn-secondary" onclick="closeSettings()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Compose Message Modal -->
<div class="modal" id="composeModal">
    <div class="modal-content compose-modal">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Compose Message</h3>
            <button class="close" onclick="closeCompose()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label for="messageRecipient">To</label>
                <div class="input-wrapper">
                    <i class="fas fa-user input-icon"></i>
                    <select id="messageRecipient">
                        <option value="">Select recipient...</option>
                        <option value="all">All Staff</option>
                        <option value="nurses">Nurses</option>
                        <option value="doctors">Doctors</option>
                        <option value="admin">Administrators</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="messageSubject">Subject</label>
                <div class="input-wrapper">
                    <i class="fas fa-tag input-icon"></i>
                    <input type="text" id="messageSubject" placeholder="Enter subject">
                </div>
            </div>
            <div class="form-group">
                <label for="messageContent">Message</label>
                <div class="input-wrapper">
                    <i class="fas fa-comment input-icon"></i>
                    <textarea id="messageContent" rows="6" placeholder="Enter your message..."></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" id="urgentMessage">
                    <span class="checkmark"></span>
                    <span>Mark as urgent</span>
                </label>
            </div>
            <div class="compose-actions">
                <button class="btn btn-primary" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
                <button class="btn btn-secondary" onclick="closeCompose()">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Sample data for notifications and messages
    let notifications = [
        { id: 1, type: 'critical', title: 'Patient Alert', message: 'Patient Agnes B. has critical heart rate', time: '2 min ago', read: false },
        { id: 2, type: 'warning', title: 'System Update', message: 'System maintenance scheduled for tonight', time: '15 min ago', read: false },
        { id: 3, type: 'info', title: 'New Patient', message: 'New patient registered: John Doe', time: '1 hour ago', read: false },
        { id: 4, type: 'success', title: 'Backup Complete', message: 'Daily backup completed successfully', time: '2 hours ago', read: true }
    ];

    let messages = [
        { id: 1, from: 'Dr. Smith', subject: 'Patient Review Meeting', preview: 'We need to discuss the treatment plan...', time: '10 min ago', read: false },
        { id: 2, from: 'Nurse Johnson', subject: 'Medication Schedule', preview: 'Updated medication schedule for Room 205...', time: '1 hour ago', read: false },
        { id: 3, from: 'Admin Team', subject: 'Weekly Report', preview: 'Please review the weekly patient statistics...', time: '3 hours ago', read: true }
    ];

    // Initialize notifications and messages
    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationCount();
        updateMessageCount();
        renderNotifications();
        renderMessages();
        loadSettings();
        
        // Load saved theme
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
            const themeIcon = document.querySelector('.theme-toggle i');
            const themeText = document.querySelector('.theme-toggle span');
            if (themeIcon && themeText) {
                themeIcon.className = 'fas fa-sun';
                themeText.textContent = 'Light Mode';
            }
        }
        
        // Ensure all dropdowns are closed on page load
        setTimeout(() => {
            closeAllDropdowns();
        }, 100);
        

    });

    // Notification Functions
    function toggleNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        const messageDropdown = document.getElementById('messageDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        // Close other dropdowns first
        if (messageDropdown) messageDropdown.classList.remove('show');
        if (userDropdown) userDropdown.classList.remove('show');
        
        // Toggle notification dropdown
        if (dropdown) {
            // If dropdown is already open, close it
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            } else {
                // Close any other open dropdowns and open this one
                closeAllDropdowns();
                dropdown.classList.add('show');
            }
        }
    }

    function closeNotifications() {
        const dropdown = document.getElementById('notificationDropdown');
        if (dropdown) {
            dropdown.classList.remove('show');
        }
    }

    function closeAllDropdowns() {
        const notificationDropdown = document.getElementById('notificationDropdown');
        const messageDropdown = document.getElementById('messageDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        if (notificationDropdown && notificationDropdown.classList.contains('show')) {
            notificationDropdown.classList.remove('show');
        }
        if (messageDropdown && messageDropdown.classList.contains('show')) {
            messageDropdown.classList.remove('show');
        }
        if (userDropdown && userDropdown.classList.contains('show')) {
            userDropdown.classList.remove('show');
        }
    }

    function renderNotifications() {
        const list = document.getElementById('notificationList');
        list.innerHTML = '';
        
        notifications.forEach(notification => {
            const item = document.createElement('div');
            item.className = `notification-item ${notification.read ? 'read' : 'unread'} ${notification.type}`;
            item.innerHTML = `
                <div class="notification-icon">
                    <i class="fas fa-${getNotificationIcon(notification.type)}"></i>
                </div>
                <div class="notification-details">
                    <div class="notification-title">${notification.title}</div>
                    <div class="notification-message">${notification.message}</div>
                    <div class="notification-time">${notification.time}</div>
                </div>
                <button class="notification-close" onclick="removeNotification(${notification.id})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(item);
        });
    }

    function getNotificationIcon(type) {
        switch(type) {
            case 'critical': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            case 'success': return 'check-circle';
            default: return 'info-circle';
        }
    }

    function updateNotificationCount() {
        const unreadCount = notifications.filter(n => !n.read).length;
        const badge = document.getElementById('notificationCount');
        if (badge) {
            badge.textContent = unreadCount;
            badge.style.display = unreadCount > 0 ? 'block' : 'none';
        }
    }

    function markAllNotificationsRead() {
        notifications.forEach(n => n.read = true);
        updateNotificationCount();
        renderNotifications();
        showNotification('All notifications marked as read', 'success');
    }

    function removeNotification(id) {
        notifications = notifications.filter(n => n.id !== id);
        updateNotificationCount();
        renderNotifications();
    }

    function viewAllNotifications() {
        showNotification('Opening all notifications...', 'info');
        // Implement view all notifications page
    }

    // Message Functions
    function toggleMessages() {
        const dropdown = document.getElementById('messageDropdown');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userDropdown = document.getElementById('userDropdown');
        
        // Close other dropdowns first
        if (notificationDropdown) notificationDropdown.classList.remove('show');
        if (userDropdown) userDropdown.classList.remove('show');
        
        // Toggle message dropdown
        if (dropdown) {
            // If dropdown is already open, close it
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            } else {
                // Close any other open dropdowns and open this one
                closeAllDropdowns();
                dropdown.classList.add('show');
            }
        }
    }

    function renderMessages() {
        const list = document.getElementById('messageList');
        list.innerHTML = '';
        
        messages.forEach(message => {
            const item = document.createElement('div');
            item.className = `message-item ${message.read ? 'read' : 'unread'}`;
            item.innerHTML = `
                <div class="message-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="message-details">
                    <div class="message-sender">${message.from}</div>
                    <div class="message-subject">${message.subject}</div>
                    <div class="message-preview">${message.preview}</div>
                    <div class="message-time">${message.time}</div>
                </div>
                <button class="message-close" onclick="removeMessage(${message.id})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(item);
        });
    }

    function updateMessageCount() {
        const unreadCount = messages.filter(m => !m.read).length;
        const badge = document.getElementById('messageCount');
        if (badge) {
            badge.textContent = unreadCount;
            badge.style.display = unreadCount > 0 ? 'block' : 'none';
        }
    }

    function removeMessage(id) {
        messages = messages.filter(m => m.id !== id);
        updateMessageCount();
        renderMessages();
    }

    function viewAllMessages() {
        showNotification('Opening all messages...', 'info');
        // Implement view all messages page
    }

    function composeMessage() {
        document.getElementById('composeModal').classList.add('show');
        document.getElementById('messageDropdown').classList.remove('show');
    }

    function closeCompose() {
        document.getElementById('composeModal').classList.remove('show');
    }

    function sendMessage() {
        const recipient = document.getElementById('messageRecipient').value;
        const subject = document.getElementById('messageSubject').value;
        const content = document.getElementById('messageContent').value;
        const urgent = document.getElementById('urgentMessage').checked;

        if (!recipient || !subject || !content) {
            showNotification('Please fill in all fields', 'error');
            return;
        }

        showNotification('Message sent successfully!', 'success');
        closeCompose();
        
        // Clear form
        document.getElementById('messageRecipient').value = '';
        document.getElementById('messageSubject').value = '';
        document.getElementById('messageContent').value = '';
        document.getElementById('urgentMessage').checked = false;
    }

    // User Dropdown Functions
    function toggleUserDropdown() {
        const dropdown = document.getElementById('userDropdown');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const messageDropdown = document.getElementById('messageDropdown');
        
        // Close other dropdowns first
        if (notificationDropdown) notificationDropdown.classList.remove('show');
        if (messageDropdown) messageDropdown.classList.remove('show');
        
        // Toggle user dropdown
        if (dropdown) {
            // If dropdown is already open, close it
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            } else {
                // Close any other open dropdowns and open this one
                closeAllDropdowns();
                dropdown.classList.add('show');
            }
        }
    }

    // Profile Functions
    function openProfile() {
        const modal = document.getElementById('profileModal');
        if (!modal) return;
        modal.style.display = 'flex';
        // Allow CSS transition to apply
        requestAnimationFrame(() => modal.classList.add('show'));
        // Allow page to remain scrollable in background
        loadProfileData();
    }

    function loadProfileData() {
        // Get current user data from PHP session variables
        const currentName = '<?php echo addslashes($_SESSION['user_name'] ?? ''); ?>';
        const currentEmail = '<?php echo addslashes($_SESSION['user_email'] ?? ''); ?>';
        const currentRole = '<?php echo addslashes($_SESSION['user_role'] ?? 'admin'); ?>';
        
        // Populate form fields
        document.getElementById('profileName').value = currentName;
        document.getElementById('profileEmail').value = currentEmail;
        
        // Load additional data from server
        fetch('get_profile_data.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('profilePhone').value = data.user.phone || '';
                    document.getElementById('profileDepartment').value = data.user.department || '';
                    document.getElementById('profileLocation').value = data.user.location || '';
                }
            })
            .catch(error => {
                console.error('Error loading profile data:', error);
            });
    }

    function closeProfile() {
        const modal = document.getElementById('profileModal');
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    function changeAvatar() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                uploadProfileImage(file);
            }
        };
        input.click();
    }

    function uploadProfileImage(file) {
        const formData = new FormData();
        formData.append('profile_image', file);

        fetch('upload_profile_image.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update all profile images on the page
                const profileImages = document.querySelectorAll('.user-profile-image, .dropdown-profile-image, #profileAvatar');
                profileImages.forEach(img => {
                    img.src = data.image_path + '?t=' + new Date().getTime();
                });
                
                showNotification('Profile image updated successfully!', 'success');
            } else {
                showNotification(data.error || 'Failed to upload image', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to upload image', 'error');
        });
    }

    function saveProfile() {
        const name = document.getElementById('profileName').value;
        const email = document.getElementById('profileEmail').value;
        const phone = document.getElementById('profilePhone').value;

        if (!name || !email) {
            showNotification('Please fill in required fields', 'error');
            return;
        }

        // Validate email format
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showNotification('Please enter a valid email address', 'error');
            return;
        }

        // Validate phone number if provided
        if (phone && phone.trim() !== '') {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,20}$/;
            if (!phoneRegex.test(phone.trim())) {
                showNotification('Please enter a valid phone number', 'error');
                return;
            }
        }

        // Show loading state
        const saveButton = document.querySelector('.profile-actions .btn-primary');
        const originalText = saveButton.innerHTML;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        saveButton.disabled = true;

        // Prepare data
        const profileData = {
            name: name.trim(),
            email: email.trim(),
            phone: phone.trim() || null
        };

        // Send to server
        fetch('update_profile.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(profileData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update header display
                const userNameSpans = document.querySelectorAll('.user-dropdown-btn span, .user-dropdown-name');
                userNameSpans.forEach(span => {
                    if (span.textContent.includes('@')) return; // Skip email spans
                    span.textContent = data.user.name;
                });
                
                showNotification('Profile updated successfully!', 'success');
                closeProfile();
            } else {
                showNotification(data.error || 'Failed to update profile', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update profile. Please try again.', 'error');
        })
        .finally(() => {
            // Restore button state
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;
        });
    }

    // Settings Functions
    function openSettings() {
        const modal = document.getElementById('settingsModal');
        if (!modal) return;
        modal.style.display = 'flex';
        requestAnimationFrame(() => modal.classList.add('show'));
        // Allow page to remain scrollable in background
        loadSettings();
    }

    function closeSettings() {
        const modal = document.getElementById('settingsModal');
        if (!modal) return;
        modal.classList.remove('show');
        setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    function showSettingsTab(tabName) {
        // Hide all tabs
        document.querySelectorAll('.settings-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        // Show selected tab
        document.getElementById(tabName + 'Tab').classList.add('active');
        event.target.classList.add('active');
    }

    function loadSettings() {
        // Load saved settings from localStorage
        const savedSettings = JSON.parse(localStorage.getItem('userSettings') || '{}');
        
        if (savedSettings.language) document.getElementById('languageSelect').value = savedSettings.language;
        if (savedSettings.timezone) document.getElementById('timezoneSelect').value = savedSettings.timezone;
        if (savedSettings.refreshInterval) document.getElementById('refreshInterval').value = savedSettings.refreshInterval;
        if (savedSettings.emailNotifications !== undefined) document.getElementById('emailNotifications').checked = savedSettings.emailNotifications;
        if (savedSettings.pushNotifications !== undefined) document.getElementById('pushNotifications').checked = savedSettings.pushNotifications;
        if (savedSettings.criticalAlerts !== undefined) document.getElementById('criticalAlerts').checked = savedSettings.criticalAlerts;
        if (savedSettings.weeklyReports !== undefined) document.getElementById('weeklyReports').checked = savedSettings.weeklyReports;
        if (savedSettings.twoFactorAuth !== undefined) document.getElementById('twoFactorAuth').checked = savedSettings.twoFactorAuth;
        if (savedSettings.fontSize) document.getElementById('fontSizeSelect').value = savedSettings.fontSize;
        if (savedSettings.theme) document.getElementById('themeSelect').value = savedSettings.theme;
    }

    function saveSettings() {
        const settings = {
            language: document.getElementById('languageSelect').value,
            timezone: document.getElementById('timezoneSelect').value,
            refreshInterval: document.getElementById('refreshInterval').value,
            emailNotifications: document.getElementById('emailNotifications').checked,
            pushNotifications: document.getElementById('pushNotifications').checked,
            criticalAlerts: document.getElementById('criticalAlerts').checked,
            weeklyReports: document.getElementById('weeklyReports').checked,
            twoFactorAuth: document.getElementById('twoFactorAuth').checked,
            fontSize: document.getElementById('fontSizeSelect').value,
            theme: document.getElementById('themeSelect').value
        };

        localStorage.setItem('userSettings', JSON.stringify(settings));
        showNotification('Settings saved successfully!', 'success');
        closeSettings();
    }

    function changePassword() {
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!currentPassword || !newPassword || !confirmPassword) {
            showNotification('Please fill in all password fields', 'error');
            return;
        }

        if (newPassword !== confirmPassword) {
            showNotification('New passwords do not match', 'error');
            return;
        }

        if (newPassword.length < 8) {
            showNotification('Password must be at least 8 characters long', 'error');
            return;
        }

        showNotification('Password changed successfully!', 'success');
        
        // Clear password fields
        document.getElementById('currentPassword').value = '';
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
    }

    function changeTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-theme');
        } else {
            document.body.classList.remove('dark-theme');
        }
    }

    // Theme Toggle Functionality
    function toggleTheme() {
        const body = document.body;
        const themeIcon = document.querySelector('.theme-toggle i');
        const themeText = document.querySelector('.theme-toggle span');
        
        if (body.classList.contains('dark-theme')) {
            body.classList.remove('dark-theme');
            themeIcon.className = 'fas fa-moon';
            themeText.textContent = 'Dark Mode';
            localStorage.setItem('theme', 'light');
        } else {
            body.classList.add('dark-theme');
            themeIcon.className = 'fas fa-sun';
            themeText.textContent = 'Light Mode';
            localStorage.setItem('theme', 'dark');
        }
    }

    // Logout Functionality
    function logout() {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) dropdown.classList.remove('show');
        // Custom centered confirm modal
        const modal = document.createElement('div');
        modal.className = 'modal show';
        modal.style.display = 'flex';
        modal.innerHTML = `
            <div class="modal-content" style="max-width:400px;">
                <div class="modal-header">
                    <h3><i class="fas fa-sign-out-alt"></i> Confirm Logout</h3>
                    <button class="close" onclick="this.closest('.modal').remove()">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to logout?</p>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                    <button class="btn btn-danger" id="confirmLogoutBtn">Logout</button>
                </div>
            </div>`;
        document.body.appendChild(modal);
        modal.querySelector('#confirmLogoutBtn').addEventListener('click', function(){ window.location.href = 'logout.php'; });
    }

    // Close dropdowns when clicking outside (robust, non-overriding handler)
    document.addEventListener('click', function(event) {
        // Ignore clicks on the dropdowns themselves or their toggles
        if (event.target.closest('.icon-btn') || event.target.closest('.user-dropdown-btn') ||
            event.target.closest('.notification-content') || event.target.closest('.message-content') ||
            event.target.closest('.user-dropdown-content') || event.target.closest('.modal')) {
            return;
        }

        closeAllDropdowns();
    });

    // Additional safety: Close dropdowns when pressing Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    });

    // Notification System (global, top-center, color-coded)
    function showNotification(message, type = 'info') {
        const validTypes = ['success', 'error', 'warning', 'info'];
        if (!validTypes.includes(type)) type = 'info';

        // Ensure container exists
        let container = document.getElementById('notificationContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'notificationContainer';
            container.className = 'notification-container';
            document.body.appendChild(container);
        }

        const icons = { success: 'check-circle', error: 'exclamation-circle', warning: 'exclamation-triangle', info: 'info-circle' };
        const toast = document.createElement('div');
        toast.className = `notification notification-${type}`;
        toast.innerHTML = `
            <i class="fas fa-${icons[type]}"></i>
            <span>${message}</span>
            <button class="notification-close" aria-label="Close" onclick="this.parentElement.remove()">×</button>
        `;

        container.appendChild(toast);

        // Auto remove after 3s
        setTimeout(() => {
            if (toast && toast.parentElement) toast.parentElement.removeChild(toast);
        }, 3000);
    }

    // Expose globally for other scripts
    window.showNotification = showNotification;
</script>
