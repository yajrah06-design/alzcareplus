<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$db = Database::getInstance();
$activity = new ActivityManager();

function body() {
    $raw = file_get_contents('php://input');
    $d = json_decode($raw, true);
    return is_array($d) ? $d : [];
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $resource = $_GET['resource'] ?? 'config'; // config | history

    if ($resource === 'config') {
        switch ($method) {
            case 'GET':
                $rows = $db->query("SELECT * FROM alert_configurations ORDER BY created_at DESC");
                echo json_encode(['success' => true, 'data' => $rows]);
                break;
            case 'POST':
                $data = body();
                $ok = $db->execute(
                    "INSERT INTO alert_configurations (alert_name, `condition`, threshold, notification_type, recipients, status) VALUES (?,?,?,?,?,?)",
                    [$data['alert_name'], $data['condition'], $data['threshold'], $data['notification_type'], $data['recipients'], $data['status'] ?? 'Active']
                );
                if ($ok) $activity->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'created', 'alert_config', $data['alert_name'], json_encode($data));
                echo json_encode(['success' => $ok]);
                break;
            case 'PUT':
                $data = body();
                $ok = $db->execute(
                    "UPDATE alert_configurations SET alert_name=?, `condition`=?, threshold=?, notification_type=?, recipients=?, status=? WHERE id=?",
                    [$data['alert_name'], $data['condition'], $data['threshold'], $data['notification_type'], $data['recipients'], $data['status'] ?? 'Active', $data['id']]
                );
                if ($ok) $activity->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'updated', 'alert_config', (string)$data['id'], json_encode($data));
                echo json_encode(['success' => $ok]);
                break;
            case 'DELETE':
                $data = body();
                $ok = $db->execute("DELETE FROM alert_configurations WHERE id=?", [$data['id']]);
                if ($ok) $activity->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'deleted', 'alert_config', (string)$data['id']);
                echo json_encode(['success' => $ok]);
                break;
            default:
                http_response_code(405);
                echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        }
        exit;
    }

    // history
    switch ($method) {
        case 'GET':
            $limit = (int)($_GET['limit'] ?? 100);
            $status = $_GET['status'] ?? '';
            $sql = "SELECT * FROM alert_history";
            $params = [];
            if ($status) { $sql .= " WHERE status=?"; $params[] = $status; }
            $sql .= " ORDER BY triggered_at DESC LIMIT ?"; $params[] = $limit;
            $rows = $db->query($sql, $params);
            echo json_encode(['success' => true, 'data' => $rows]);
            break;
        case 'POST':
            $data = body();
            $ok = $db->execute(
                "INSERT INTO alert_history (patient_id, alert_type, status, action_taken) VALUES (?,?,?,?)",
                [$data['patient_id'], $data['alert_type'], $data['status'] ?? 'Warning', $data['action_taken'] ?? '']
            );
            echo json_encode(['success' => $ok]);
            break;
        case 'PUT':
            $data = body();
            if (($data['action'] ?? '') === 'ack') {
                $ok = $db->execute("UPDATE alert_history SET acknowledged_by=?, acknowledged_at=NOW() WHERE id=?", [$_SESSION['user_name'], $data['id']]);
            } else if (($data['action'] ?? '') === 'resolve') {
                $ok = $db->execute("UPDATE alert_history SET resolved_by=?, resolved_at=NOW() WHERE id=?", [$_SESSION['user_name'], $data['id']]);
            } else {
                $ok = false;
            }
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


