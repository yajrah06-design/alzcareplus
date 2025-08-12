// Health Monitoring Dashboard JavaScript
let healthData = [
    {
        patient_id: 'ASC 0001',
        patient_name: 'Agnes B.',
        age: 72,
        heart_rate: 78,
        blood_pressure_systolic: 128,
        blood_pressure_diastolic: 82,
        blood_oxygen: 96,
        temperature: 98.2,
        blood_glucose: 95,
        weight: 65.2,
        sleep_hours: 7.5,
        activity_level: 'Moderate',
        medication_taken: true,
        last_updated: 'Today, 10:30 AM',
        status: 'Normal',
        trend: 'stable',
        alerts: []
    },
    {
        patient_id: 'ASC 0002',
        patient_name: 'Bautista K.',
        age: 68,
        heart_rate: 102,
        blood_pressure_systolic: 142,
        blood_pressure_diastolic: 90,
        blood_oxygen: 94,
        temperature: 99.1,
        blood_glucose: 120,
        weight: 70.1,
        sleep_hours: 6.2,
        activity_level: 'Low',
        medication_taken: false,
        last_updated: 'Today, 01:00 PM',
        status: 'Elevated',
        trend: 'increasing',
        alerts: ['High Blood Pressure', 'Missed Medication']
    },
    {
        patient_id: 'ASC 0003',
        patient_name: 'Yanson A.',
        age: 75,
        heart_rate: 85,
        blood_pressure_systolic: 118,
        blood_pressure_diastolic: 76,
        blood_oxygen: 98,
        temperature: 97.8,
        blood_glucose: 88,
        weight: 62.8,
        sleep_hours: 8.1,
        activity_level: 'High',
        medication_taken: true,
        last_updated: 'Today, 06:15 AM',
        status: 'Normal',
        trend: 'stable',
        alerts: []
    },
    {
        patient_id: 'ASC 0004',
        patient_name: 'Valencia W.',
        age: 70,
        heart_rate: 110,
        blood_pressure_systolic: 150,
        blood_pressure_diastolic: 95,
        blood_oxygen: 92,
        temperature: 100.2,
        blood_glucose: 145,
        weight: 68.5,
        sleep_hours: 5.8,
        activity_level: 'Very Low',
        medication_taken: false,
        last_updated: 'Today, 05:37 PM',
        status: 'Critical',
        trend: 'decreasing',
        alerts: ['Critical Heart Rate', 'High Temperature', 'Low Blood Oxygen', 'Missed Medication']
    },
    {
        patient_id: 'ASC 0005',
        patient_name: 'Swift T.',
        age: 69,
        heart_rate: 72,
        blood_pressure_systolic: 125,
        blood_pressure_diastolic: 80,
        blood_oxygen: 97,
        temperature: 98.6,
        blood_glucose: 92,
        weight: 63.4,
        sleep_hours: 7.8,
        activity_level: 'Moderate',
        medication_taken: true,
        last_updated: 'Today, 09:45 AM',
        status: 'Normal',
        trend: 'stable',
        alerts: []
    },
    {
        patient_id: 'ASC 0006',
        patient_name: 'Styles H.',
        age: 71,
        heart_rate: 95,
        blood_pressure_systolic: 135,
        blood_pressure_diastolic: 88,
        blood_oxygen: 95,
        temperature: 98.9,
        blood_glucose: 110,
        weight: 66.7,
        sleep_hours: 6.5,
        activity_level: 'Low',
        medication_taken: true,
        last_updated: 'Today, 11:20 AM',
        status: 'Elevated',
        trend: 'stable',
        alerts: ['Elevated Blood Pressure']
    }
];

let autoRefreshInterval = null;
let autoRefreshEnabled = false;

// DOM Elements
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearch');
const statusFilter = document.getElementById('statusFilter');
const trendFilter = document.getElementById('trendFilter');
const healthTableBody = document.getElementById('healthTableBody');
const healthCount = document.getElementById('healthCount');
const vitalSignSelect = document.getElementById('vitalSignSelect');
const autoRefreshText = document.getElementById('autoRefreshText');

// Charts
let healthStatusChart = null;
let vitalSignsChart = null;

// Initialize dashboard
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    filterHealthData();
    setupEventListeners();
    updateStatistics();
});

function setupEventListeners() {
    searchInput.addEventListener('input', filterHealthData);
    clearSearchBtn.addEventListener('click', clearSearch);
    statusFilter.addEventListener('change', filterHealthData);
    trendFilter.addEventListener('change', filterHealthData);
    vitalSignSelect.addEventListener('change', updateVitalSignsChart);
}

// Search and Filter Functionality
function filterHealthData() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedStatus = statusFilter.value;
    const selectedTrend = trendFilter.value;
    
    clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
    
    const filteredData = healthData.filter(patient => {
        const matchesSearch = patient.patient_name.toLowerCase().includes(searchTerm) ||
                             patient.patient_id.toLowerCase().includes(searchTerm) ||
                             patient.status.toLowerCase().includes(searchTerm);
        
        const matchesStatus = !selectedStatus || patient.status === selectedStatus;
        const matchesTrend = !selectedTrend || patient.trend === selectedTrend;
        
        return matchesSearch && matchesStatus && matchesTrend;
    });
    
    renderHealthTable(filteredData);
    updateHealthCount(filteredData.length);
}

function clearSearch() {
    searchInput.value = '';
    clearSearchBtn.style.display = 'none';
    filterHealthData();
}

function updateHealthCount(count) {
    healthCount.textContent = `${count} patient${count !== 1 ? 's' : ''} monitored`;
}

// Render Health Table
function renderHealthTable(patientsToRender) {
    healthTableBody.innerHTML = '';
    
    patientsToRender.forEach(patient => {
        const row = document.createElement('tr');
        row.setAttribute('data-patient-id', patient.patient_id);
        
        row.innerHTML = `
            <td>
                <div class="patient-info">
                    <div class="patient-name">${patient.patient_name}</div>
                    <div class="patient-id">${patient.patient_id}</div>
                    <div class="patient-age">${patient.age} years</div>
                </div>
            </td>
            <td>
                <div class="vital-signs">
                    <div class="vital-item">
                        <i class="fas fa-heartbeat"></i>
                        <span class="${patient.heart_rate > 100 ? 'warning' : 'normal'}">${patient.heart_rate} bpm</span>
                    </div>
                    <div class="vital-item">
                        <i class="fas fa-tint"></i>
                        <span class="${patient.blood_pressure_systolic > 140 ? 'warning' : 'normal'}">${patient.blood_pressure_systolic}/${patient.blood_pressure_diastolic}</span>
                    </div>
                    <div class="vital-item">
                        <i class="fas fa-lungs"></i>
                        <span class="${patient.blood_oxygen < 95 ? 'warning' : 'normal'}">${patient.blood_oxygen}%</span>
                    </div>
                    <div class="vital-item">
                        <i class="fas fa-thermometer-half"></i>
                        <span class="${patient.temperature > 99 ? 'warning' : 'normal'}">${patient.temperature}°F</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="health-metrics">
                    <div class="metric-item">
                        <span class="metric-label">Weight:</span>
                        <span class="metric-value">${patient.weight} kg</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">Sleep:</span>
                        <span class="metric-value ${patient.sleep_hours < 7 ? 'warning' : 'normal'}">${patient.sleep_hours} hrs</span>
                    </div>
                    <div class="metric-item">
                        <span class="metric-label">Activity:</span>
                        <span class="metric-value">${patient.activity_level}</span>
                    </div>
                </div>
            </td>
            <td>
                <div class="medication-status">
                    <div class="medication-indicator ${patient.medication_taken ? 'taken' : 'missed'}">
                        <i class="fas fa-${patient.medication_taken ? 'check-circle' : 'times-circle'}"></i>
                        <span>${patient.medication_taken ? 'Taken' : 'Missed'}</span>
                    </div>
                    <div class="last-updated">${patient.last_updated}</div>
                </div>
            </td>
            <td>
                <span class="status-badge status-${patient.status.toLowerCase()}">
                    ${patient.status}
                </span>
            </td>
            <td>
                <div class="trend-indicator ${patient.trend}">
                    <i class="fas fa-${patient.trend === 'stable' ? 'minus' : (patient.trend === 'increasing' ? 'arrow-up' : 'arrow-down')}"></i>
                    <span>${patient.trend.charAt(0).toUpperCase() + patient.trend.slice(1)}</span>
                </div>
            </td>
            <td>
                <div class="alerts-container">
                    ${patient.alerts.length === 0 ? 
                        '<span class="no-alerts">No alerts</span>' : 
                        patient.alerts.map(alert => `<span class="alert-badge">${alert}</span>`).join('')
                    }
                </div>
            </td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-primary" title="View Details" onclick="viewHealthDetails('${patient.patient_id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" title="History" onclick="viewHealthHistory('${patient.patient_id}')">
                        <i class="fas fa-history"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" title="Send Alert" onclick="sendAlert('${patient.patient_id}')">
                        <i class="fas fa-bell"></i>
                    </button>
                </div>
            </td>
        `;
        
        healthTableBody.appendChild(row);
    });
}

// Initialize Charts
function initializeCharts() {
    // Health Status Chart
    const statusCtx = document.getElementById('healthStatusChart').getContext('2d');
    healthStatusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Elevated', 'Critical'],
            datasets: [{
                data: [0, 0, 0],
                backgroundColor: ['#10b981', '#f59e0b', '#dc2626'],
                borderColor: ['#059669', '#d97706', '#b91c1c'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            cutout: '60%'
        }
    });

    // Vital Signs Chart
    const vitalCtx = document.getElementById('vitalSignsChart').getContext('2d');
    vitalSignsChart = new Chart(vitalCtx, {
        type: 'line',
        data: {
            labels: ['6 AM', '9 AM', '12 PM', '3 PM', '6 PM', '9 PM'],
            datasets: [{
                label: 'Heart Rate',
                data: [75, 78, 82, 79, 76, 74],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(241, 241, 241, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: false,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    updateCharts();
}

// Update Charts
function updateCharts() {
    const normalCount = healthData.filter(p => p.status === 'Normal').length;
    const elevatedCount = healthData.filter(p => p.status === 'Elevated').length;
    const criticalCount = healthData.filter(p => p.status === 'Critical').length;

    healthStatusChart.data.datasets[0].data = [normalCount, elevatedCount, criticalCount];
    healthStatusChart.update();
}

// Update Vital Signs Chart
function updateVitalSignsChart() {
    const selectedVital = vitalSignSelect.value;
    const labels = ['6 AM', '9 AM', '12 PM', '3 PM', '6 PM', '9 PM'];
    let data = [];
    let color = '#3b82f6';

    switch(selectedVital) {
        case 'heart_rate':
            data = [75, 78, 82, 79, 76, 74];
            color = '#3b82f6';
            break;
        case 'blood_pressure':
            data = [120, 125, 130, 128, 122, 118];
            color = '#dc2626';
            break;
        case 'temperature':
            data = [98.2, 98.4, 98.6, 98.3, 98.1, 97.9];
            color = '#f59e0b';
            break;
        case 'blood_oxygen':
            data = [96, 97, 95, 96, 98, 97];
            color = '#10b981';
            break;
    }

    vitalSignsChart.data.datasets[0].data = data;
    vitalSignsChart.data.datasets[0].borderColor = color;
    vitalSignsChart.data.datasets[0].backgroundColor = color.replace(')', ', 0.1)').replace('rgb', 'rgba');
    vitalSignsChart.update();
}

// Update Statistics
function updateStatistics() {
    const totalPatients = healthData.length;
    const normalCount = healthData.filter(p => p.status === 'Normal').length;
    const elevatedCount = healthData.filter(p => p.status === 'Elevated').length;
    const criticalCount = healthData.filter(p => p.status === 'Critical').length;
    const medicationTakenCount = healthData.filter(p => p.medication_taken).length;
    const alertsCount = healthData.reduce((sum, p) => sum + p.alerts.length, 0);

    // Update stat cards
    document.querySelectorAll('.health-stat-card').forEach((card, index) => {
        const h3 = card.querySelector('h3');
        const values = [totalPatients, normalCount, elevatedCount, criticalCount, medicationTakenCount, alertsCount];
        if (h3 && values[index] !== undefined) {
            h3.textContent = values[index];
        }
    });
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Use flex to center properly (matches other pages)
    modal.style.display = 'flex';

    // Prepare content for smooth entrance
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.style.opacity = '1';
        modalContent.style.transform = 'translateY(0) scale(1)';
    }

    // Trigger show state
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;

    // Start exit animation
    modal.classList.remove('show');

    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.style.opacity = '0';
        modalContent.style.transform = 'translateY(12px) scale(0.98)';
    }

    // Hide after transition
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            closeModal(modal.id);
        }
    });
}

// View Health Details
function viewHealthDetails(patientId) {
    const patient = healthData.find(p => p.patient_id === patientId);
    if (!patient) return;

    const content = document.getElementById('healthDetailsContent');
    content.innerHTML = `
        <div class="health-details">
            <div class="detail-section">
                <h4>Patient Info</h4>
                <div class="detail-row">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value">${patient.patient_name}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">ID:</span>
                    <span class="detail-value">${patient.patient_id}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Age:</span>
                    <span class="detail-value">${patient.age} years</span>
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Vital Signs</h4>
                <div class="detail-row">
                    <span class="detail-label">Heart Rate:</span>
                    <span class="detail-value ${patient.heart_rate > 100 ? 'warning' : 'normal'}">${patient.heart_rate} bpm</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Blood Pressure:</span>
                    <span class="detail-value ${patient.blood_pressure_systolic > 140 ? 'warning' : 'normal'}">${patient.blood_pressure_systolic}/${patient.blood_pressure_diastolic} mmHg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Blood Oxygen:</span>
                    <span class="detail-value ${patient.blood_oxygen < 95 ? 'warning' : 'normal'}">${patient.blood_oxygen}%</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Temperature:</span>
                    <span class="detail-value ${patient.temperature > 99 ? 'warning' : 'normal'}">${patient.temperature}°F</span>
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Health Metrics</h4>
                <div class="detail-row">
                    <span class="detail-label">Weight:</span>
                    <span class="detail-value">${patient.weight} kg</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Sleep Hours:</span>
                    <span class="detail-value ${patient.sleep_hours < 7 ? 'warning' : 'normal'}">${patient.sleep_hours} hours</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Activity Level:</span>
                    <span class="detail-value">${patient.activity_level}</span>
                </div>
            </div>
            
            <div class="detail-section">
                <h4>Status & Alerts</h4>
                <div class="detail-row">
                    <span class="detail-label">Current Status:</span>
                    <span class="status-badge status-${patient.status.toLowerCase()}">${patient.status}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Trend:</span>
                    <span class="trend-indicator ${patient.trend}">${patient.trend.charAt(0).toUpperCase() + patient.trend.slice(1)}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Medication:</span>
                    <span class="medication-indicator ${patient.medication_taken ? 'taken' : 'missed'}">
                        ${patient.medication_taken ? 'Taken' : 'Missed'}
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Alerts:</span>
                    <div class="alerts-list">
                        ${patient.alerts.length === 0 ? 
                            '<span class="no-alerts">No active alerts</span>' : 
                            patient.alerts.map(alert => `<span class="alert-badge">${alert}</span>`).join('')
                        }
                    </div>
                </div>
            </div>
        </div>
    `;

    openModal('healthDetailsModal');
}

// View Health History
function viewHealthHistory(patientId) {
    const content = document.getElementById('healthHistoryContent');
    content.innerHTML = `
        <div class="health-history">
            <div class="history-filters">
                <select class="history-period-select">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
            <div class="history-chart-container">
                <canvas id="historyChart" height="300"></canvas>
            </div>
            <div class="history-table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Heart Rate</th>
                            <th>Blood Pressure</th>
                            <th>Temperature</th>
                            <th>Blood Oxygen</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Today</td>
                            <td>78 bpm</td>
                            <td>128/82</td>
                            <td>98.2°F</td>
                            <td>96%</td>
                            <td><span class="status-badge status-normal">Normal</span></td>
                        </tr>
                        <tr>
                            <td>Yesterday</td>
                            <td>82 bpm</td>
                            <td>130/85</td>
                            <td>98.5°F</td>
                            <td>95%</td>
                            <td><span class="status-badge status-normal">Normal</span></td>
                        </tr>
                        <tr>
                            <td>2 days ago</td>
                            <td>95 bpm</td>
                            <td>135/88</td>
                            <td>99.1°F</td>
                            <td>94%</td>
                            <td><span class="status-badge status-warning">Elevated</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    `;
    
    openModal('healthHistoryModal');
}

// Send Alert
function sendAlert(patientId) {
    const patient = healthData.find(p => p.patient_id === patientId);
    if (!patient) return;

    showNotification(`Alert sent to ${patient.patient_name}`, 'success');
}

// Alert Settings
function openAlertSettings() {
    openModal('alertSettingsModal');
}

document.getElementById('alertSettingsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    closeModal('alertSettingsModal');
    showNotification('Alert settings updated successfully!', 'success');
});

// Refresh Data
function refreshData() {
    // Simulate data refresh
    healthData.forEach(patient => {
        // Randomly update some values
        if (Math.random() > 0.7) {
            patient.heart_rate += Math.floor(Math.random() * 10) - 5;
            patient.heart_rate = Math.max(60, Math.min(120, patient.heart_rate));
        }
    });

    filterHealthData();
    updateCharts();
    updateStatistics();
    showNotification('Data refreshed successfully!', 'success');
}

// Export Data
function exportData() {
    const csvContent = "data:text/csv;charset=utf-8," + 
        "Patient ID,Name,Age,Heart Rate,Blood Pressure,Blood Oxygen,Temperature,Status\n" +
        healthData.map(p => 
            `${p.patient_id},${p.patient_name},${p.age},${p.heart_rate},${p.blood_pressure_systolic}/${p.blood_pressure_diastolic},${p.blood_oxygen},${p.temperature},${p.status}`
        ).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "health_data.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showNotification('Data exported successfully!', 'success');
}

// Auto Refresh Toggle
function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    
    if (autoRefreshEnabled) {
        autoRefreshInterval = setInterval(refreshData, 30000); // 30 seconds
        autoRefreshText.textContent = 'Auto Refresh: ON';
        showNotification('Auto refresh enabled', 'success');
    } else {
        if (autoRefreshInterval) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
        }
        autoRefreshText.textContent = 'Auto Refresh: OFF';
        showNotification('Auto refresh disabled', 'info');
    }
}

// Use global showNotification from header
