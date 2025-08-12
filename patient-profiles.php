<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Load patients from database
require_once __DIR__ . '/includes/database.php';
$patientManager = new PatientManager();
$patients = $patientManager->getAllPatients();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profiles - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="search-container">
                    <div class="search-box">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Search patients by name, ID, or guardian..." class="search-input">
                        <button class="search-clear" id="clearSearch" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="filter-container">
                        <select id="stageFilter" class="filter-select">
                            <option value="">All Stages</option>
                            <option value="Stage 1">Stage 1</option>
                            <option value="Stage 2">Stage 2</option>
                            <option value="Stage 3">Stage 3</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Patients Table -->
            <div class="content-card">
                <div class="card-header">
                    <h2><i class="fas fa-users"></i> Patient Profiles</h2>
                    <button class="btn btn-primary" onclick="openAddPatientModal()">
                        <i class="fas fa-plus"></i>
                        Add New Patient
                    </button>
                </div>
                <div class="table-container">
                    <table class="data-table" id="patientsTable">
                        <thead>
                            <tr>
                                <th>PATIENT ID</th>
                                <th>PATIENT NAME</th>
                                <th>GUARDIAN</th>
                                <th>STAGE</th>
                                <th>PHONE</th>
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody id="patientsTableBody">
                            <?php foreach ($patients as $patient): ?>
                                <tr data-patient-id="<?php echo htmlspecialchars($patient['patient_id']); ?>">
                                    <td><?php echo htmlspecialchars($patient['patient_id']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['name']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['guardian']); ?></td>
                                    <td>
                                        <span class="status-badge status-normal">
                                            <?php echo htmlspecialchars($patient['stage']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary" title="View Details" onclick="viewPatient('<?php echo htmlspecialchars($patient['patient_id']); ?>')">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Edit" onclick="editPatient('<?php echo htmlspecialchars($patient['patient_id']); ?>')">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" title="Delete" onclick="deletePatient('<?php echo htmlspecialchars($patient['patient_id']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <span id="patientCount"><?php echo count($patients); ?> patients found</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Patient Modal -->
    <div id="addPatientModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="modal-title">
                        <h3>Add New Patient</h3>
                        <p>Enter patient information to create a new profile</p>
                    </div>
                </div>
                <span class="close" onclick="closeModal('addPatientModal')">&times;</span>
            </div>
            <form id="addPatientForm" class="modal-form professional-form">
                <div class="form-sections">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-user"></i>
                            <h4>Personal Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patientName" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Patient Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="patientName" name="patientName" class="form-input" placeholder="Enter patient's full name" required>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guardianName" class="form-label">
                                    <i class="fas fa-shield-alt"></i>
                                    Guardian Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="guardianName" name="guardianName" class="form-input" placeholder="Enter guardian's full name" required>
                                    <div class="input-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-phone"></i>
                            <h4>Contact Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patientPhone" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel" id="patientPhone" name="patientPhone" class="form-input" placeholder="+1234567890" required>
                                    <div class="input-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="patientEmail" class="form-label">
                                    <i class="fas fa-envelope"></i>
                                    Email Address
                                </label>
                                <div class="input-wrapper">
                                    <input type="email" id="patientEmail" name="patientEmail" class="form-input" placeholder="patient@example.com">
                                    <div class="input-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medical Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-heartbeat"></i>
                            <h4>Medical Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patientStage" class="form-label">
                                    <i class="fas fa-layer-group"></i>
                                    Alzheimer's Stage
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <select id="patientStage" name="patientStage" class="form-select" required>
                                        <option value="">Select Stage</option>
                                        <option value="Stage 1">Stage 1 - Early Stage</option>
                                        <option value="Stage 2">Stage 2 - Middle Stage</option>
                                        <option value="Stage 3">Stage 3 - Late Stage</option>
                                    </select>
                                    <div class="input-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="patientDob" class="form-label">
                                    <i class="fas fa-birthday-cake"></i>
                                    Date of Birth
                                </label>
                                <div class="input-wrapper">
                                    <input type="date" id="patientDob" name="patientDob" class="form-input">
                                    <div class="input-icon">
                                        <i class="fas fa-birthday-cake"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>Address Information</h4>
                        </div>
                        <div class="form-group">
                            <label for="patientAddress" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Full Address
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <textarea id="patientAddress" name="patientAddress" class="form-textarea" rows="3" placeholder="Enter complete address including street, city, and postal code" required></textarea>
                                <div class="input-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-info-circle"></i>
                            <h4>Additional Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="emergencyContact" class="form-label">
                                    <i class="fas fa-ambulance"></i>
                                    Emergency Contact
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="emergencyContact" name="emergencyContact" class="form-input" placeholder="Emergency contact name">
                                    <div class="input-icon">
                                        <i class="fas fa-ambulance"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="emergencyPhone" class="form-label">
                                    <i class="fas fa-phone-alt"></i>
                                    Emergency Phone
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel" id="emergencyPhone" name="emergencyPhone" class="form-input" placeholder="+1234567890">
                                    <div class="input-icon">
                                        <i class="fas fa-phone-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="patientNotes" class="form-label">
                                <i class="fas fa-sticky-note"></i>
                                Notes
                            </label>
                            <div class="input-wrapper">
                                <textarea id="patientNotes" name="patientNotes" class="form-textarea" rows="3" placeholder="Any additional notes or special considerations"></textarea>
                                <div class="input-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addPatientModal')">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                    <div class="form-actions-right">
                        <button type="button" class="btn btn-outline" onclick="resetForm('addPatientForm')">
                            <i class="fas fa-undo"></i>
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-plus"></i>
                            Add Patient
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- View Patient Modal -->
    <div id="viewPatientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user"></i> Patient Details</h3>
                <span class="close" onclick="closeModal('viewPatientModal')">&times;</span>
            </div>
            <div id="patientDetails" class="modal-body">
                <!-- Patient details will be loaded here -->
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal('viewPatientModal')">Close</button>
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal -->
    <div id="editPatientModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-user-edit"></i>
                    </div>
                    <div class="modal-title">
                        <h3>Edit Patient</h3>
                        <p>Update patient information and save changes</p>
                    </div>
                </div>
                <span class="close" onclick="closeModal('editPatientModal')">&times;</span>
            </div>
            <form id="editPatientForm" class="modal-form professional-form">
                <input type="hidden" id="editPatientId" name="patientId">

                <div class="form-sections">
                    <!-- Personal Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-user"></i>
                            <h4>Personal Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPatientName" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Patient Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="editPatientName" name="patientName" class="form-input" placeholder="Enter patient's full name" required>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="editGuardianName" class="form-label">
                                    <i class="fas fa-shield-alt"></i>
                                    Guardian Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="editGuardianName" name="guardianName" class="form-input" placeholder="Enter guardian's full name" required>
                                    <div class="input-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact & Medical Information Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-address-card"></i>
                            <h4>Contact & Medical</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="editPatientPhone" class="form-label">
                                    <i class="fas fa-phone"></i>
                                    Phone Number
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="tel" id="editPatientPhone" name="patientPhone" class="form-input" placeholder="+1234567890" required>
                                    <div class="input-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="editPatientStage" class="form-label">
                                    <i class="fas fa-layer-group"></i>
                                    Alzheimer's Stage
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <select id="editPatientStage" name="patientStage" class="form-select" required>
                                        <option value="Stage 1">Stage 1 - Early Stage</option>
                                        <option value="Stage 2">Stage 2 - Middle Stage</option>
                                        <option value="Stage 3">Stage 3 - Late Stage</option>
                                    </select>
                                    <div class="input-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>Address</h4>
                        </div>
                        <div class="form-group">
                            <label for="editPatientAddress" class="form-label">
                                <i class="fas fa-map-marker-alt"></i>
                                Full Address
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper">
                                <textarea id="editPatientAddress" name="patientAddress" class="form-textarea" rows="3" placeholder="Enter complete address including street, city, and postal code" required></textarea>
                                <div class="input-icon">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('editPatientModal')">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                    <div class="form-actions-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Update Patient
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deletePatientModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h3>
                <span class="close" onclick="closeModal('deletePatientModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this patient? This action cannot be undone.</p>
                <p><strong>Patient ID:</strong> <span id="deletePatientId"></span></p>
            </div>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="closeModal('deletePatientModal')">Cancel</button>
                <button class="btn btn-danger" onclick="confirmDelete()">Delete Patient</button>
            </div>
        </div>
    </div>

    <script src="assets/js/patient-profiles.js"></script>
</body>
</html> 