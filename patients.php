<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/database.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$patientManager = new PatientManager();
$activityManager = new ActivityManager();

function getJsonBody() {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function generateNextPatientId() {
    try {
        $db = Database::getInstance();
        $rows = $db->query("SELECT patient_id FROM patients ORDER BY id DESC LIMIT 1");
        if ($rows && isset($rows[0]['patient_id'])) {
            $last = $rows[0]['patient_id'];
            if (preg_match('/(\d{4})$/', $last, $m)) {
                $num = intval($m[1]) + 1;
                return 'ASC ' . str_pad((string)$num, 4, '0', STR_PAD_LEFT);
            }
        }
    } catch (Exception $e) {}
    return 'ASC 0001';
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    switch ($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                $patient = $patientManager->getPatientById($_GET['id']);
                echo json_encode(['success' => true, 'data' => $patient]);
            } else {
                $patients = $patientManager->getAllPatients();
                echo json_encode(['success' => true, 'data' => $patients]);
            }
            break;
        case 'POST':
            $data = getJsonBody();
            // Compute age if date_of_birth provided
            $dob = $data['date_of_birth'] ?? null;
            $age = null;
            if ($dob) {
                try { $age = (new DateTime())->diff(new DateTime($dob))->y; } catch (Exception $e) { $age = null; }
            }

            $payload = [
                'patient_id' => $data['patient_id'] ?? generateNextPatientId(),
                'name' => $data['name'] ?? '',
                'guardian' => $data['guardian'] ?? '',
                'stage' => $data['stage'] ?? 'Stage 1',
                'date_of_birth' => $dob,
                'age' => $age,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'emergency_contact' => $data['emergency_contact'] ?? '',
                'emergency_phone' => $data['emergency_phone'] ?? ''
            ];
            $ok = $patientManager->createPatient($payload);
            if ($ok) {
                $activityManager->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'created', 'patient', $payload['patient_id'], json_encode($payload));
            }
            echo json_encode(['success' => $ok, 'data' => $payload]);
            break;
        case 'PUT':
            $data = getJsonBody();
            if (empty($data['patient_id'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'patient_id required']);
                break;
            }
            $dob = $data['date_of_birth'] ?? null;
            $age = null;
            if ($dob) {
                try { $age = (new DateTime())->diff(new DateTime($dob))->y; } catch (Exception $e) { $age = null; }
            }

            $payload = [
                'name' => $data['name'] ?? '',
                'guardian' => $data['guardian'] ?? '',
                'stage' => $data['stage'] ?? 'Stage 1',
                'date_of_birth' => $dob,
                'age' => $age,
                'gender' => $data['gender'] ?? null,
                'phone' => $data['phone'] ?? '',
                'address' => $data['address'] ?? '',
                'emergency_contact' => $data['emergency_contact'] ?? '',
                'emergency_phone' => $data['emergency_phone'] ?? ''
            ];
            $ok = $patientManager->updatePatient($data['patient_id'], $payload);
            if ($ok) {
                $activityManager->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'updated', 'patient', $data['patient_id'], json_encode($payload));
            }
            echo json_encode(['success' => $ok]);
            break;
        case 'DELETE':
            $data = getJsonBody();
            $id = $data['patient_id'] ?? ($_GET['id'] ?? null);
            if (!$id) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'patient_id required']);
                break;
            }
            $ok = $patientManager->deletePatient($id);
            if ($ok) {
                $activityManager->logActivity($_SESSION['user_id'], $_SESSION['user_name'], 'deleted', 'patient', $id, '');
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


