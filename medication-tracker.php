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

// Sample medication data with more comprehensive information
$medications = [
    [
        'id' => 'MED001',
        'patient_id' => 'ASC 0001',
        'patient_name' => 'Brenna Agnes',
        'medication' => 'Donepezil',
        'dosage' => '10mg',
        'frequency' => 'Once Daily',
        'next_dose' => 'Today, 08:00 PM',
        'last_dose' => 'Yesterday, 08:00 PM',
        'status' => 'Pending',
        'adherence' => 95,
        'prescribed_date' => '2024-01-15',
        'end_date' => '2024-12-31',
        'notes' => 'Take with food, avoid alcohol'
    ],
    [
        'id' => 'MED002',
        'patient_id' => 'ASC 0005',
        'patient_name' => 'Harry Styles',
        'medication' => 'Memantine',
        'dosage' => '5mg',
        'frequency' => 'Twice Daily',
        'next_dose' => 'Today, 08:30 PM',
        'last_dose' => 'Today, 08:30 AM',
        'status' => 'Taken',
        'adherence' => 88,
        'prescribed_date' => '2024-02-01',
        'end_date' => '2024-12-31',
        'notes' => 'Take with water, monitor for side effects'
    ],
    [
        'id' => 'MED003',
        'patient_id' => 'ASC 0009',
        'patient_name' => 'Liza Soberano',
        'medication' => 'Rivastigmine',
        'dosage' => '4.6mg/24hr',
        'frequency' => 'Patch (Daily)',
        'next_dose' => 'Tomorrow, 08:00 AM',
        'last_dose' => 'Today, 08:00 AM',
        'status' => 'Pending',
        'adherence' => 92,
        'prescribed_date' => '2024-01-20',
        'end_date' => '2024-12-31',
        'notes' => 'Apply to clean, dry skin, rotate application sites'
    ],
    [
        'id' => 'MED004',
        'patient_id' => 'ASC 0010',
        'patient_name' => 'Michael Chen',
        'medication' => 'Galantamine',
        'dosage' => '8mg',
        'frequency' => 'Twice Daily',
        'next_dose' => 'Today, 09:00 AM',
        'last_dose' => 'Yesterday, 09:00 PM',
        'status' => 'Missed',
        'adherence' => 78,
        'prescribed_date' => '2024-01-10',
        'end_date' => '2024-12-31',
        'notes' => 'Take with meals, avoid taking on empty stomach'
    ],
    [
        'id' => 'MED005',
        'patient_id' => 'ASC 0002',
        'patient_name' => 'Kurt Bautista',
        'medication' => 'Aricept',
        'dosage' => '5mg',
        'frequency' => 'Once Daily',
        'next_dose' => 'Today, 07:00 PM',
        'last_dose' => 'Yesterday, 07:00 PM',
        'status' => 'Pending',
        'adherence' => 96,
        'prescribed_date' => '2024-01-25',
        'end_date' => '2024-12-31',
        'notes' => 'Take at bedtime, may cause drowsiness'
    ]
];

// Adherence data for chart
$adherenceData = [
    'Taken on time' => 75,
    'Taken late' => 15,
    'Missed' => 8,
    'Skipped' => 2
];

// Calculate statistics
$totalMedications = count($medications);
$pendingMedications = count(array_filter($medications, function($med) { return $med['status'] === 'Pending'; }));
$missedMedications = count(array_filter($medications, function($med) { return $med['status'] === 'Missed'; }));
$takenMedications = count(array_filter($medications, function($med) { return $med['status'] === 'Taken'; }));

$averageAdherence = array_sum(array_column($medications, 'adherence')) / count($medications);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Tracker - AlzCare+</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Medication Dashboard Overview -->
            <div class="medication-dashboard">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalMedications; ?></h3>
                            <p>Total Medications</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon pending">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $pendingMedications; ?></h3>
                            <p>Pending Doses</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon missed">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $missedMedications; ?></h3>
                            <p>Missed Doses</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo round($averageAdherence, 1); ?>%</h3>
                            <p>Average Adherence</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <button class="btn btn-primary" onclick="openAddMedicationModal()">
                        <i class="fas fa-plus"></i>
                        Add New Medication
                    </button>
                    <button class="btn btn-secondary" onclick="openBulkReminderModal()">
                        <i class="fas fa-bell"></i>
                        Send Reminders
                    </button>
                    <button class="btn btn-outline" onclick="exportMedicationData()">
                        <i class="fas fa-download"></i>
                        Export Data
                    </button>
                </div>
            </div>

            <!-- Medication Management Section -->
            <div class="content-card">
                <div class="card-header">
                    <div class="header-content">
                        <h2><i class="fas fa-pills"></i> Medication Management</h2>
                        <p>Track and manage patient medications with real-time updates</p>
                    </div>
                    <div class="card-actions">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="medicationSearch" placeholder="Search medications...">
                        </div>
                        <select id="statusFilter" class="filter-select">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Taken">Taken</option>
                            <option value="Missed">Missed</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-container">
                    <table class="data-table" id="medicationTable">
                        <thead>
                            <tr>
                                <th>PATIENT</th>
                                <th>MEDICATION</th>
                                <th>DOSAGE & FREQUENCY</th>
                                <th>NEXT DOSE</th>
                                <th>STATUS</th>
                                <th>ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medications as $med): ?>
                                <tr data-medication-id="<?php echo $med['id']; ?>">
                                    <td>
                                        <div class="patient-info">
                                            <div class="patient-name"><?php echo htmlspecialchars($med['patient_name']); ?></div>
                                            <div class="patient-id"><?php echo htmlspecialchars($med['patient_id']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="medication-info">
                                            <div class="medication-name"><?php echo htmlspecialchars($med['medication']); ?></div>
                                            <div class="medication-notes"><?php echo htmlspecialchars($med['notes']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dosage-info">
                                            <div class="dosage"><?php echo htmlspecialchars($med['dosage']); ?></div>
                                            <div class="frequency"><?php echo htmlspecialchars($med['frequency']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dose-info">
                                            <div class="next-dose"><?php echo htmlspecialchars($med['next_dose']); ?></div>
                                            <div class="last-dose">Last: <?php echo htmlspecialchars($med['last_dose']); ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = 'status-normal';
                                        $statusIcon = 'fas fa-clock';
                                        if ($med['status'] === 'Missed') {
                                            $statusClass = 'status-warning';
                                            $statusIcon = 'fas fa-exclamation-triangle';
                                        } elseif ($med['status'] === 'Taken') {
                                            $statusClass = 'status-success';
                                            $statusIcon = 'fas fa-check-circle';
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $statusClass; ?>">
                                            <i class="<?php echo $statusIcon; ?>"></i>
                                            <?php echo htmlspecialchars($med['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-primary" onclick="markAsTaken('<?php echo $med['id']; ?>')" title="Mark as Taken">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary" onclick="editMedication('<?php echo $med['id']; ?>')" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning" onclick="sendReminder('<?php echo $med['id']; ?>')" title="Send Reminder">
                                                <i class="fas fa-bell"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteMedication('<?php echo $med['id']; ?>')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Analytics Section -->
            <div class="analytics-grid">
                <!-- Medication Adherence Chart -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-chart-pie"></i> Medication Adherence Overview</h3>
                        <div class="chart-legend">
                            <div class="legend-item"><span class="legend-color taken"></span><span>Taken on time</span></div>
                            <div class="legend-item"><span class="legend-color late"></span><span>Taken late</span></div>
                            <div class="legend-item"><span class="legend-color missed"></span><span>Missed</span></div>
                            <div class="legend-item"><span class="legend-color skipped"></span><span>Skipped</span></div>
                        </div>
                    </div>
                    <div class="chart-container adherence-container">
                        <canvas id="adherenceChart"></canvas>
                    </div>
                </div>

                <!-- Medication Schedule -->
                <div class="content-card">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-alt"></i> Today's Schedule</h3>
                        <button class="btn btn-sm btn-outline" onclick="refreshSchedule()">
                            <i class="fas fa-sync-alt"></i>
                            Refresh
                        </button>
                    </div>
                    <div class="schedule-container">
                        <div class="schedule-timeline">
                            <?php
                            $todayMedications = array_filter($medications, function($med) {
                                return strpos($med['next_dose'], 'Today') !== false;
                            });
                            foreach ($todayMedications as $med):
                            ?>
                            <div class="schedule-item">
                                <div class="schedule-time"><?php echo explode(', ', $med['next_dose'])[1]; ?></div>
                                <div class="schedule-content">
                                    <div class="schedule-patient"><?php echo $med['patient_name']; ?></div>
                                    <div class="schedule-medication"><?php echo $med['medication']; ?> - <?php echo $med['dosage']; ?></div>
                                </div>
                                <div class="schedule-status">
                                    <span class="status-badge status-<?php echo strtolower($med['status']); ?>">
                                        <?php echo $med['status']; ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Medication Modal -->
    <div id="addMedicationModal" class="modal">
        <div class="modal-content large-modal">
            <div class="modal-header">
                <div class="modal-header-content">
                    <div class="modal-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="modal-title">
                        <h3>Add New Medication</h3>
                        <p>Enter medication details for patient</p>
                    </div>
                </div>
                <span class="close" onclick="closeModal('addMedicationModal')">&times;</span>
            </div>
            <form id="addMedicationForm" class="modal-form professional-form">
                <div class="form-sections">
                    <!-- Patient Information -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-user"></i>
                            <h4>Patient Information</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patientSelect" class="form-label">
                                    <i class="fas fa-user"></i>
                                    Select Patient
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <select id="patientSelect" name="patientId" class="form-select" required>
                                        <option value="">Choose a patient...</option>
                                        <option value="ASC 0001">ASC 0001 - Brenna Agnes</option>
                                        <option value="ASC 0002">ASC 0002 - Kurt Bautista</option>
                                        <option value="ASC 0005">ASC 0005 - Harry Styles</option>
                                        <option value="ASC 0009">ASC 0009 - Liza Soberano</option>
                                        <option value="ASC 0010">ASC 0010 - Michael Chen</option>
                                    </select>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Medication Details -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-pills"></i>
                            <h4>Medication Details</h4>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="medicationName" class="form-label">
                                    <i class="fas fa-pills"></i>
                                    Medication Name
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="medicationName" name="medicationName" class="form-input" placeholder="e.g., Donepezil" required>
                                    <div class="input-icon">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dosage" class="form-label">
                                    <i class="fas fa-weight"></i>
                                    Dosage
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="text" id="dosage" name="dosage" class="form-input" placeholder="e.g., 10mg" required>
                                    <div class="input-icon">
                                        <i class="fas fa-weight"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="frequency" class="form-label">
                                    <i class="fas fa-clock"></i>
                                    Frequency
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <select id="frequency" name="frequency" class="form-select" required>
                                        <option value="">Select frequency...</option>
                                        <option value="Once Daily">Once Daily</option>
                                        <option value="Twice Daily">Twice Daily</option>
                                        <option value="Three Times Daily">Three Times Daily</option>
                                        <option value="As Needed">As Needed</option>
                                        <option value="Weekly">Weekly</option>
                                    </select>
                                    <div class="input-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nextDose" class="form-label">
                                    <i class="fas fa-calendar"></i>
                                    Next Dose
                                    <span class="required">*</span>
                                </label>
                                <div class="input-wrapper">
                                    <input type="datetime-local" id="nextDose" name="nextDose" class="form-input" required>
                                    <div class="input-icon">
                                        <i class="fas fa-calendar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-info-circle"></i>
                            <h4>Additional Information</h4>
                        </div>
                        <div class="form-group">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note"></i>
                                Notes & Instructions
                            </label>
                            <div class="input-wrapper">
                                <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="Special instructions, side effects to watch for, etc."></textarea>
                                <div class="input-icon">
                                    <i class="fas fa-sticky-note"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addMedicationModal')">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                    <div class="form-actions-right">
                        <button type="button" class="btn btn-outline" onclick="resetForm('addMedicationForm')">
                            <i class="fas fa-undo"></i>
                            Reset
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i>
                            Add Medication
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/medication-tracker.js"></script>
</body>
</html> 