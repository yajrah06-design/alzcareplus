<?php
session_start();

// Log the logout activity if user was logged in
if (isset($_SESSION['user_id'])) {
    require_once 'includes/database.php';
    
    try {
        $db = Database::getInstance();
        $db->execute(
            "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)",
            [$_SESSION['user_id'], 'logout', 'User logged out', $_SERVER['REMOTE_ADDR'] ?? '']
        );
    } catch (Exception $e) {
        // Log error silently
    }
}

// Destroy all session data
session_destroy();

// Redirect to login page
header("Location: index.php");
exit();
?>