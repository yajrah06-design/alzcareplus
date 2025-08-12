function computeYearOnlyAge(dobStr) {
    if (!dobStr) return '';
    let y = parseInt(String(dobStr).slice(0, 4), 10);
    if (Number.isNaN(y)) {
        const d = new Date(dobStr);
        if (!isNaN(d.getTime())) y = d.getFullYear();
    }
    if (!y) return '';
    const currentYear = new Date().getFullYear();
    return String(currentYear - y);
}
// Patients loaded from API
let patients = [];

async function loadPatients() {
    try {
        const res = await fetch('api/patients.php', { credentials: 'same-origin' });
        const json = await res.json();
        if (json.success) {
            patients = (json.data || []).map(p => ({
                id: p.patient_id,
                name: p.name,
                guardian: p.guardian || '',
                stage: p.stage || 'Stage 1',
                phone: p.phone || '',
                address: p.address || '',
                date_of_birth: p.date_of_birth || '',
                age: p.age || '',
                emergency_contact: p.emergency_contact || '',
                emergency_phone: p.emergency_phone || ''
            }));
            renderPatientsTable(patients);
            updatePatientCount(patients.length);
        }
    } catch (e) {
        console.error('Failed to load patients', e);
    }
}

document.addEventListener('DOMContentLoaded', loadPatients);

let currentPatientId = null;

// DOM Elements
const searchInput = document.getElementById('searchInput');
const clearSearchBtn = document.getElementById('clearSearch');
const stageFilter = document.getElementById('stageFilter');
const patientsTableBody = document.getElementById('patientsTableBody');
const patientCount = document.getElementById('patientCount');

// Search and Filter Functionality
searchInput.addEventListener('input', filterPatients);
clearSearchBtn.addEventListener('click', clearSearch);
stageFilter.addEventListener('change', filterPatients);

function filterPatients() {
    const searchTerm = searchInput.value.toLowerCase();
    const selectedStage = stageFilter.value;
    
    // Show/hide clear button
    clearSearchBtn.style.display = searchTerm ? 'block' : 'none';
    
    const filteredPatients = patients.filter(patient => {
        const matchesSearch = patient.name.toLowerCase().includes(searchTerm) ||
                             patient.id.toLowerCase().includes(searchTerm) ||
                             patient.guardian.toLowerCase().includes(searchTerm);
        
        const matchesStage = !selectedStage || patient.stage === selectedStage;
        
        return matchesSearch && matchesStage;
    });
    
    renderPatientsTable(filteredPatients);
    updatePatientCount(filteredPatients.length);
}

function clearSearch() {
    searchInput.value = '';
    clearSearchBtn.style.display = 'none';
    filterPatients();
}

function updatePatientCount(count) {
    patientCount.textContent = `${count} patient${count !== 1 ? 's' : ''} found`;
}

function renderPatientsTable(patientsToRender) {
    patientsTableBody.innerHTML = '';
    
    patientsToRender.forEach(patient => {
        const row = document.createElement('tr');
        row.setAttribute('data-patient-id', patient.id);
        
        row.innerHTML = `
            <td>${patient.id}</td>
            <td>${patient.name}</td>
            <td>${patient.guardian}</td>
            <td>
                <span class="status-badge status-normal">
                    ${patient.stage}
                </span>
            </td>
            <td>${patient.phone}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn btn-sm btn-primary" title="View Details" onclick="viewPatient('${patient.id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-primary" title="Edit" onclick="editPatient('${patient.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" title="Delete" onclick="deletePatient('${patient.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;
        
        patientsTableBody.appendChild(row);
    });
}

// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        // Set display to flex immediately
        modal.style.display = 'flex';
        
        // Force the modal content to be visible immediately
        const modalContent = modal.querySelector('.modal-content');
        const largeModal = modal.querySelector('.large-modal');
        
        if (modalContent) {
            modalContent.style.opacity = '1';
            modalContent.style.transform = 'scale(1)';
        }
        
        if (largeModal) {
            largeModal.style.opacity = '1';
            largeModal.style.transform = 'scale(1)';
        }
        
        // Add show class after a small delay to trigger animation
        setTimeout(() => {
            modal.classList.add('show');
        }, 10);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        
        // Reset modal content styles
        const modalContent = modal.querySelector('.modal-content');
        const largeModal = modal.querySelector('.large-modal');
        
        if (modalContent) {
            modalContent.style.opacity = '0';
            modalContent.style.transform = 'scale(0.95)';
        }
        
        if (largeModal) {
            largeModal.style.opacity = '0';
            largeModal.style.transform = 'scale(0.95)';
        }
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300); // Match the transition duration
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            const modalId = modal.id;
            closeModal(modalId);
        }
    });
}

// Add Patient Functions
function openAddPatientModal() {
    openModal('addPatientModal');
    document.getElementById('addPatientForm').reset();
}

document.getElementById('addPatientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const payload = {
        name: formData.get('patientName'),
        guardian: formData.get('guardianName'),
        stage: formData.get('patientStage'),
        phone: formData.get('patientPhone'),
        address: formData.get('patientAddress'),
        date_of_birth: formData.get('patientDob') || null,
        emergency_contact: formData.get('emergencyContact') || '',
        emergency_phone: formData.get('emergencyPhone') || ''
    };
    try {
        const res = await fetch('api/patients.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
            credentials: 'same-origin'
        });
        const json = await res.json();
        if (json.success) {
            // Reload from API to avoid duplicates and ensure sorting
            await loadPatients();
            closeModal('addPatientModal');
            showNotification('Patient added successfully!', 'success');
        } else {
            showNotification('Failed to add patient', 'error');
        }
    } catch (err) {
        console.error(err);
        showNotification('Failed to add patient', 'error');
    }
});

function generatePatientId() {
    const lastId = patients[patients.length - 1]?.id || 'ASC 0000';
    const number = parseInt(lastId.split(' ')[1]) + 1;
    return `ASC ${number.toString().padStart(4, '0')}`;
}

// View Patient Functions
function viewPatient(patientId) {
    const patient = patients.find(p => p.id === patientId);
    if (!patient) return;
    
    const patientDetails = document.getElementById('patientDetails');
    const ageByYear = computeYearOnlyAge(patient.date_of_birth);
    patientDetails.innerHTML = `
        <div class="patient-details-grid two-col">
            <div class="detail-card">
                <h4>Identity</h4>
                <div class="detail-item"><label>Patient ID</label><span>${patient.id}</span></div>
                <div class="detail-item"><label>Name</label><span>${patient.name}</span></div>
                <div class="detail-item"><label>Date of Birth</label><span>${patient.date_of_birth || '—'}</span></div>
                <div class="detail-item"><label>Age</label><span>${ageByYear || '—'}</span></div>
            </div>
            <div class="detail-card">
                <h4>Contacts</h4>
                <div class="detail-item"><label>Guardian</label><span>${patient.guardian}</span></div>
                <div class="detail-item"><label>Phone</label><span>${patient.phone}</span></div>
                <div class="detail-item"><label>Emergency Contact</label><span>${patient.emergency_contact || '—'}</span></div>
                <div class="detail-item"><label>Emergency Phone</label><span>${patient.emergency_phone || '—'}</span></div>
            </div>
            <div class="detail-card">
                <h4>Clinical</h4>
                <div class="detail-item"><label>Stage</label><span class="status-badge status-normal">${patient.stage}</span></div>
            </div>
            <div class="detail-card">
                <h4>Address</h4>
                <div class="detail-item"><label>Address</label><span>${patient.address}</span></div>
            </div>
        </div>
    `;
    
    openModal('viewPatientModal');
}

// Edit Patient Functions
function editPatient(patientId) {
    const patient = patients.find(p => p.id === patientId);
    if (!patient) return;
    
    currentPatientId = patientId;
    
    // Populate form fields
    document.getElementById('editPatientId').value = patient.id;
    document.getElementById('editPatientName').value = patient.name;
    document.getElementById('editGuardianName').value = patient.guardian;
    document.getElementById('editPatientPhone').value = patient.phone;
    document.getElementById('editPatientStage').value = patient.stage;
    document.getElementById('editPatientAddress').value = patient.address;
    
    openModal('editPatientModal');
}

document.getElementById('editPatientForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(e.target);
    const patientIndex = patients.findIndex(p => p.id === currentPatientId);
    if (patientIndex !== -1) {
        const payload = {
            patient_id: currentPatientId,
            name: formData.get('patientName'),
            guardian: formData.get('guardianName'),
            stage: formData.get('patientStage'),
            phone: formData.get('patientPhone'),
            address: formData.get('patientAddress'),
            date_of_birth: formData.get('patientDob') || null,
            emergency_contact: formData.get('emergencyContact') || '',
            emergency_phone: formData.get('emergencyPhone') || ''
        };
        try {
            const res = await fetch('api/patients.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
                credentials: 'same-origin'
            });
            const json = await res.json();
            if (json.success) {
                await loadPatients();
                closeModal('editPatientModal');
                showNotification('Patient updated successfully!', 'success');
            } else {
                showNotification('Failed to update patient', 'error');
            }
        } catch (err) {
            console.error(err);
            showNotification('Failed to update patient', 'error');
        }
    }
});

// Delete Patient Functions
function deletePatient(patientId) {
    currentPatientId = patientId;
    document.getElementById('deletePatientId').textContent = patientId;
    openModal('deletePatientModal');
}

async function confirmDelete() {
    const patientIndex = patients.findIndex(p => p.id === currentPatientId);
    if (patientIndex !== -1) {
        try {
            const res = await fetch('api/patients.php', {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ patient_id: currentPatientId }),
                credentials: 'same-origin'
            });
            const json = await res.json();
            if (json.success) {
                await loadPatients();
                closeModal('deletePatientModal');
                showNotification('Patient deleted successfully!', 'success');
            } else {
                showNotification('Failed to delete patient', 'error');
            }
        } catch (err) {
            console.error(err);
            showNotification('Failed to delete patient', 'error');
        }
    }
}

// Use global showNotification from header

// Add patient details grid styles
const patientDetailsStyle = document.createElement('style');
patientDetailsStyle.textContent = `
    .patient-details-grid {
        display: grid;
        gap: 16px;
    }
    .patient-details-grid.two-col {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .detail-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
    }
    .detail-card h4 { margin: 0 0 8px 0; color: #111827; font-size: 14px; }
    
    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .detail-item:last-child {
        border-bottom: none;
    }
    
    .detail-item label {
        font-weight: 600;
        color: #374151;
        min-width: 100px;
    }
    
    .detail-item span {
        color: #1e40af;
        text-align: right;
    }
    
    .dark-theme .detail-item {
        border-bottom-color: #333;
    }
    
    .dark-theme .detail-item label {
        color: #e5e5e5;
    }
    .dark-theme .detail-card { background: #1f2937; border-color: #334155; }
    .dark-theme .detail-card h4 { color: #f8fafc; }
    .dark-theme .detail-item .status-badge { color: #1e40af; }
    .dark-theme .detail-item span { color: #e6e7e7; }
`;
document.head.appendChild(patientDetailsStyle); 

// Reset form function
function resetForm(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        
        // Clear any custom styling or validation states
        const inputs = form.querySelectorAll('.form-input, .form-select, .form-textarea');
        inputs.forEach(input => {
            input.classList.remove('error', 'success');
            const wrapper = input.closest('.input-wrapper');
            if (wrapper) {
                wrapper.classList.remove('error', 'success');
            }
        });
        
        // Show success message
        showNotification('Form has been reset successfully', 'success');
    }
}

// Enhanced form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('.form-input, .form-select, .form-textarea');
    let isValid = true;
    
    inputs.forEach(input => {
        const wrapper = input.closest('.input-wrapper');
        const isRequired = input.hasAttribute('required');
        const value = input.value.trim();
        
        // Remove previous validation states
        input.classList.remove('error', 'success');
        if (wrapper) {
            wrapper.classList.remove('error', 'success');
        }
        
        // Validate required fields
        if (isRequired && !value) {
            input.classList.add('error');
            if (wrapper) {
                wrapper.classList.add('error');
            }
            isValid = false;
        } else if (value) {
            input.classList.add('success');
            if (wrapper) {
                wrapper.classList.add('success');
            }
        }
        
        // Additional validation for specific fields
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                input.classList.add('error');
                if (wrapper) {
                    wrapper.classList.add('error');
                }
                isValid = false;
            }
        }
        
        if (input.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                input.classList.add('error');
                if (wrapper) {
                    wrapper.classList.add('error');
                }
                isValid = false;
            }
        }
    });
    
    return isValid;
}

// Event Listeners (validation only; data rendering handled by loadPatients())
document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('.form-input, .form-select, .form-textarea');
    formInputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });
});

// Field validation function
function validateField(field) {
    const wrapper = field.closest('.input-wrapper');
    const value = field.value.trim();
    const isRequired = field.hasAttribute('required');
    
    // Remove previous validation states
    field.classList.remove('error', 'success');
    if (wrapper) {
        wrapper.classList.remove('error', 'success');
    }
    
    // Validate required fields
    if (isRequired && !value) {
        field.classList.add('error');
        if (wrapper) {
            wrapper.classList.add('error');
        }
        return false;
    } else if (value) {
        field.classList.add('success');
        if (wrapper) {
            wrapper.classList.add('success');
        }
    }
    
    // Additional validation for specific fields
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            field.classList.add('error');
            if (wrapper) {
                wrapper.classList.add('error');
            }
            return false;
        }
    }
    
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(value.replace(/\s/g, ''))) {
            field.classList.add('error');
            if (wrapper) {
                wrapper.classList.add('error');
            }
            return false;
        }
    }
    
    return true;
} 