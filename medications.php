<?php
// Simple REST API for medications
require_once __DIR__ . '/../includes/database.php';

header('Content-Type: application/json');

// Enable CORS for same-origin usage; credentials are same-origin in fetches
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Optional filter by patient_id
        $patientId = $_GET['patient_id'] ?? null;
        $query = "SELECT m.id,
                         m.patient_id,
                         p.name AS patient_name,
                         m.medication_name AS medication,
                         m.dosage,
                         m.frequency,
                         m.next_dose,
                         m.status
                  FROM medications m
                  JOIN patients p ON p.patient_id = m.patient_id";
        $params = [];
        if ($patientId) { $query .= " WHERE m.patient_id = ?"; $params[] = $patientId; }
        $query .= " ORDER BY m.next_dose ASC";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $rows]);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($method === 'POST') {
        // Create medication
        $required = ['patient_id', 'medication', 'dosage', 'frequency', 'next_dose'];
        foreach ($required as $k) if (!isset($input[$k]) || $input[$k] === '') { http_response_code(400); echo json_encode(['success'=>false,'error'=>"Missing field: $k"]); exit; }

        $stmt = $pdo->prepare("INSERT INTO medications (patient_id, medication_name, dosage, frequency, next_dose, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([
            $input['patient_id'],
            $input['medication'],
            $input['dosage'],
            $input['frequency'],
            date('Y-m-d H:i:s', strtotime($input['next_dose'])),
            $input['status'] ?? 'Pending'
        ]);
        $id = $pdo->lastInsertId();
        echo json_encode(['success'=>true,'id'=>$id]);
        exit;
    }

    if ($method === 'PUT') {
        // Update medication fields or status
        if (!isset($input['id'])) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing id']); exit; }
        $id = (int)$input['id'];

        $fields = [];
        $params = [];
        if (isset($input['medication'])) { $fields[] = 'medication_name = ?'; $params[] = $input['medication']; }
        if (isset($input['dosage'])) { $fields[] = 'dosage = ?'; $params[] = $input['dosage']; }
        if (isset($input['frequency'])) { $fields[] = 'frequency = ?'; $params[] = $input['frequency']; }
        if (isset($input['next_dose'])) { $fields[] = 'next_dose = ?'; $params[] = date('Y-m-d H:i:s', strtotime($input['next_dose'])); }
        if (isset($input['status'])) { $fields[] = 'status = ?'; $params[] = $input['status']; }

        if (empty($fields)) { echo json_encode(['success'=>false,'error'=>'No fields to update']); exit; }

        $sql = 'UPDATE medications SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $params[] = $id;
        $stmt = $pdo->prepare($sql);
        $ok = $stmt->execute($params);
        echo json_encode(['success'=>$ok]);
        exit;
    }

    if ($method === 'DELETE') {
        $id = $_GET['id'] ?? null;
        if (!$id && isset($input['id'])) { $id = $input['id']; }
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'Missing id']); exit; }
        $stmt = $pdo->prepare('DELETE FROM medications WHERE id = ?');
        $ok = $stmt->execute([(int)$id]);
        echo json_encode(['success'=>$ok]);
        exit;
    }

    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method Not Allowed']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>$e->getMessage()]);
}


