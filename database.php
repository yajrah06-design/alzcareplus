<?php
/**
 * Database connection and utility functions for AlzCare+
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        $host = 'localhost';
        $dbname = 'alzcare_plus';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';
        
        $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        try {
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Execute a query and return all results
     */
    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Execute a query and return single result
     */
    public function querySingle($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Execute an INSERT, UPDATE, or DELETE query
     */
    public function execute($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new Exception("Execute failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get the last inserted ID
     */
    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Begin a transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a transaction
     */
    public function commit() {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a transaction
     */
    public function rollback() {
        return $this->connection->rollback();
    }
}

/**
 * User authentication functions
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Authenticate user login
     */
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM users WHERE email = ? AND status = 'active'";
        $user = $this->db->querySingle($sql, [$email]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->db->execute(
                "UPDATE users SET last_login = NOW() WHERE id = ?",
                [$user['id']]
            );
            
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function getUserById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        return $this->db->querySingle($sql, [$id]);
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission($userId, $requiredRole) {
        $user = $this->getUserById($userId);
        if (!$user) return false;
        
        if ($requiredRole === 'admin') {
            return $user['role'] === 'admin';
        }
        
        return in_array($user['role'], ['admin', $requiredRole]);
    }
    
    /**
     * Create new user
     */
    public function createUser($userData) {
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (user_id, name, email, password, role, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $userData['user_id'],
            $userData['name'],
            $userData['email'],
            $hashedPassword,
            $userData['role'],
            $userData['status'] ?? 'active'
        ]);
    }
    
    /**
     * Update user
     */
    public function updateUser($id, $userData) {
        $sql = "UPDATE users SET name = ?, email = ?, role = ?, status = ?, profile_image = ?, phone = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $userData['name'],
            $userData['email'],
            $userData['role'],
            $userData['status'],
            $userData['profile_image'] ?? null,
            $userData['phone'] ?? null,
            $id
        ]);
    }

    /**
     * Update user profile information
     */
    public function updateProfile($id, $profileData) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        return $this->db->execute($sql, [
            $profileData['name'],
            $profileData['email'],
            $profileData['phone'] ?? null,
            $id
        ]);
    }

    /**
     * Update user profile image
     */
    public function updateProfileImage($id, $profileImage) {
        $sql = "UPDATE users SET profile_image = ? WHERE id = ?";
        return $this->db->execute($sql, [$profileImage, $id]);
    }
}

/**
 * Patient management functions
 */
class PatientManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all patients
     */
    public function getAllPatients() {
        $sql = "SELECT * FROM patients ORDER BY created_at DESC";
        return $this->db->query($sql);
    }
    
    /**
     * Get patient by ID
     */
    public function getPatientById($patientId) {
        $sql = "SELECT * FROM patients WHERE patient_id = ?";
        return $this->db->querySingle($sql, [$patientId]);
    }
    
    /**
     * Create new patient
     */
    public function createPatient($patientData) {
        $sql = "INSERT INTO patients (patient_id, name, guardian, stage, date_of_birth, age, gender, phone, address, emergency_contact, emergency_phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $patientData['patient_id'],
            $patientData['name'],
            $patientData['guardian'],
            $patientData['stage'],
            $patientData['date_of_birth'] ?? null,
            $patientData['age'] ?? null,
            $patientData['gender'] ?? null,
            $patientData['phone'] ?? null,
            $patientData['address'] ?? null,
            $patientData['emergency_contact'] ?? null,
            $patientData['emergency_phone'] ?? null
        ]);
    }
    
    /**
     * Update patient
     */
    public function updatePatient($patientId, $patientData) {
        $sql = "UPDATE patients SET name = ?, guardian = ?, stage = ?, date_of_birth = ?, age = ?, gender = ?, phone = ?, address = ?, emergency_contact = ?, emergency_phone = ? 
                WHERE patient_id = ?";
        
        return $this->db->execute($sql, [
            $patientData['name'],
            $patientData['guardian'],
            $patientData['stage'],
            $patientData['date_of_birth'] ?? null,
            $patientData['age'] ?? null,
            $patientData['gender'] ?? null,
            $patientData['phone'] ?? null,
            $patientData['address'] ?? null,
            $patientData['emergency_contact'] ?? null,
            $patientData['emergency_phone'] ?? null,
            $patientId
        ]);
    }
    
    /**
     * Delete patient
     */
    public function deletePatient($patientId) {
        $sql = "DELETE FROM patients WHERE patient_id = ?";
        return $this->db->execute($sql, [$patientId]);
    }
}

/**
 * Health monitoring functions
 */
class HealthManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get health records for a patient
     */
    public function getHealthRecords($patientId, $limit = 10) {
        $sql = "SELECT * FROM health_records WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT ?";
        return $this->db->query($sql, [$patientId, $limit]);
    }
    
    /**
     * Get latest health record for a patient
     */
    public function getLatestHealthRecord($patientId) {
        $sql = "SELECT * FROM health_records WHERE patient_id = ? ORDER BY recorded_at DESC LIMIT 1";
        return $this->db->querySingle($sql, [$patientId]);
    }
    
    /**
     * Add health record
     */
    public function addHealthRecord($healthData) {
        $sql = "INSERT INTO health_records (patient_id, heart_rate, blood_pressure, blood_oxygen, temperature, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $healthData['patient_id'],
            $healthData['heart_rate'],
            $healthData['blood_pressure'],
            $healthData['blood_oxygen'],
            $healthData['temperature'],
            $healthData['status']
        ]);
    }
}

/**
 * Medication management functions
 */
class MedicationManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get medications for a patient
     */
    public function getMedications($patientId) {
        $sql = "SELECT * FROM medications WHERE patient_id = ? ORDER BY next_dose ASC";
        return $this->db->query($sql, [$patientId]);
    }
    
    /**
     * Add medication
     */
    public function addMedication($medicationData) {
        $sql = "INSERT INTO medications (patient_id, medication_name, dosage, frequency, next_dose, status) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $medicationData['patient_id'],
            $medicationData['medication_name'],
            $medicationData['dosage'],
            $medicationData['frequency'],
            $medicationData['next_dose'],
            $medicationData['status'] ?? 'Pending'
        ]);
    }
    
    /**
     * Update medication status
     */
    public function updateMedicationStatus($id, $status) {
        $sql = "UPDATE medications SET status = ? WHERE id = ?";
        return $this->db->execute($sql, [$status, $id]);
    }
}

/**
 * Alert management functions
 */
class AlertManager {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get alert configurations
     */
    public function getAlertConfigurations() {
        $sql = "SELECT * FROM alert_configurations ORDER BY created_at DESC";
        return $this->db->query($sql);
    }
    
    /**
     * Get alert history
     */
    public function getAlertHistory($limit = 50) {
        $sql = "SELECT ah.*, p.name as patient_name 
                FROM alert_history ah 
                JOIN patients p ON ah.patient_id = p.patient_id 
                ORDER BY ah.triggered_at DESC 
                LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
    
    /**
     * Add alert to history
     */
    public function addAlertHistory($alertData) {
        $sql = "INSERT INTO alert_history (patient_id, alert_type, status, action_taken) 
                VALUES (?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $alertData['patient_id'],
            $alertData['alert_type'],
            $alertData['status'],
            $alertData['action_taken']
        ]);
    }
}

/**
 * Activity logging functions
 */
class ActivityManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Log a user activity
     */
    public function logActivity($userId, $userName, $action, $entity, $entityId, $details = '') {
        $sql = "INSERT INTO activity_log (user_id, user_name, action, entity, entity_id, details) VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->execute($sql, [
            $userId,
            $userName,
            $action,
            $entity,
            $entityId,
            $details
        ]);
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 50) {
        $sql = "SELECT * FROM activity_log ORDER BY created_at DESC LIMIT ?";
        return $this->db->query($sql, [$limit]);
    }
}

// Initialize database connection
try {
    $db = Database::getInstance();
    $auth = new Auth();
    $patientManager = new PatientManager();
    $healthManager = new HealthManager();
    $medicationManager = new MedicationManager();
    $alertManager = new AlertManager();
    $activityManager = new ActivityManager();
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?> 