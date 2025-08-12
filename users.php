<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance();

function body() {
    $raw = file_get_contents('php://input');
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            $rows = $db->query("SELECT id, user_id, name, email, role, status, last_login, phone, assigned_patients FROM users ORDER BY id DESC");
            echo json_encode(['success' => true, 'data' => $rows]);
            break;
        case 'POST':
            $d = body();
            $name = trim($d['name'] ?? '');
            $email = trim($d['email'] ?? '');
            if ($name === '' || $email === '') { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Name and email are required']); break; }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Invalid email']); break; }
            // Uniqueness check
            $exists = $db->querySingle("SELECT id FROM users WHERE email = ?", [$email]);
            if ($exists) { http_response_code(409); echo json_encode(['success'=>false,'error'=>'Email already exists']); break; }
            $hashed = password_hash($d['password'] ?? 'password123', PASSWORD_DEFAULT);
            $role = strtolower($d['role'] ?? 'caregiver');
            if (!in_array($role, ['admin','caregiver'], true)) { $role = 'caregiver'; }
            $status = strtolower($d['status'] ?? 'active');
            // Generate user_id with role-specific prefix
            $prefix = $role === 'admin' ? 'ADM' : 'CAR';
            $userId = $d['user_id'] ?? null;
            if ($userId === null) {
                // Find last numeric suffix for this prefix and increment
                $row = $db->querySingle("SELECT user_id FROM users WHERE user_id LIKE ? ORDER BY id DESC LIMIT 1", ["$prefix-%"]);
                $next = 1;
                if ($row && isset($row['user_id'])) {
                    if (preg_match('/(\d+)$/', $row['user_id'], $m)) {
                        $next = intval($m[1]) + 1;
                    }
                }
                $userId = sprintf('%s-%03d', $prefix, $next);
            }
            $ok = $db->execute("INSERT INTO users (user_id, name, email, password, role, status, phone, assigned_patients) VALUES (?,?,?,?,?,?,?,?)",
                [$userId, $name, $email, $hashed, $role, $status, $d['phone'] ?? null, $d['assigned_patients'] ?? null]);
            echo json_encode(['success' => $ok]);
            break;
        case 'PUT':
            $d = body();
            // Update basic fields; status is managed by system (online/offline via last_login)
            $ok = $db->execute("UPDATE users SET name=?, email=?, role=?, phone=?, assigned_patients=? WHERE id=?",
                [$d['name'], $d['email'], strtolower($d['role']), $d['phone'] ?? null, $d['assigned_patients'] ?? null, $d['id']]);
            echo json_encode(['success' => $ok]);
            break;
        case 'DELETE':
            $d = body();
            $ok = $db->execute("DELETE FROM users WHERE id=?", [$d['id']]);
            echo json_encode(['success' => $ok]);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}


