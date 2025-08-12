<?php
session_start();

// Include database connection
require_once 'includes/database.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

try {
    $auth = new Auth();
    $userId = $_SESSION['user_id'];
    
    // Get user data
    $user = $auth->getUserById($userId);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role'],
                'profile_image' => $user['profile_image'],
                'department' => 'Healthcare', // Default department
                'location' => 'Main Office', // Default location
                'created_at' => $user['created_at'],
                'last_login' => $user['last_login']
            ]
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'User not found']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>

