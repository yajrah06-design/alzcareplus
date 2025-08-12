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

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($input['name']) || !isset($input['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Name and email are required']);
    exit();
}

// Validate email format
if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email format']);
    exit();
}

// Validate phone number if provided (optional)
$phone = null;
if (!empty($input['phone'])) {
    // Basic phone validation - remove all non-numeric characters and check length
    $cleanPhone = preg_replace('/[^0-9+\-\s\(\)]/', '', $input['phone']);
    if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 20) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid phone number format']);
        exit();
    }
    $phone = $cleanPhone;
}

try {
    $db = Database::getInstance();
    $auth = new Auth();
    
    $userId = $_SESSION['user_id'];
    
    // Check if email is already taken by another user
    $existingUser = $db->querySingle(
        "SELECT id FROM users WHERE email = ? AND id != ?", 
        [$input['email'], $userId]
    );
    
    if ($existingUser) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Email is already taken by another user']);
        exit();
    }
    
    // Prepare profile data
    $profileData = [
        'name' => trim($input['name']),
        'email' => trim($input['email']),
        'phone' => $phone
    ];
    
    // Update profile
    $result = $auth->updateProfile($userId, $profileData);
    
    if ($result) {
        // Update session variables
        $_SESSION['user_name'] = $profileData['name'];
        $_SESSION['user_email'] = $profileData['email'];
        $_SESSION['user_phone'] = $profileData['phone'];
        
        // Get updated user data to return
        $updatedUser = $auth->getUserById($userId);
        
        // Log activity
        $db->execute(
            "INSERT INTO activity_logs (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)",
            [$userId, 'profile_update', 'User updated profile information', $_SERVER['REMOTE_ADDR'] ?? '']
        );
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profile updated successfully',
            'user' => [
                'name' => $updatedUser['name'],
                'email' => $updatedUser['email'],
                'phone' => $updatedUser['phone']
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to update profile']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}
?>
