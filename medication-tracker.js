// Medication Tracker JavaScript
class MedicationTracker {
    constructor() {
        this.medications = [];
        this.currentFilter = '';
        this.currentStatusFilter = '';
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.fetchMedications();
        this.initializeCharts();
        this.setupSearchAndFilter();
    }

    async fetchMedications() {
        try {
            const res = await fetch('api/medications.php', { credentials: 'same-origin' });
            const json = await res.json();
            if (json.success) {
                // Normalize payload to match existing rendering
                this.medications = (json.data || []).map(r => ({
                    id: String(r.id),
                    patient_id: r.patient_id,
                    medication: r.medication,
                    dosage: r.dosage,
                    frequency: r.frequency,
                    next_dose: this.formatDateTime(r.next_dose),
                    status: r.status || 'Pending',
                    notes: r.notes || ''
                }));
                this.renderTableFromState();
                this.updateStatistics();
            }
        } catch (e) { console.error('Failed to load medications', e); }
    }

    renderTableFromState() {
        const tbody = document.querySelector('#medicationTable tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        this.medications.forEach(m => this.addMedicationToTable(m));
        this.filterMedications();
    }

    setupEventListeners() {
        // Search functionality
        const searchInput = document.getElementById('medicationSearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.currentFilter = e.target.value.toLowerCase();
                this.filterMedications();
            });
        }

        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.currentStatusFilter = e.target.value;
                this.filterMedications();
            });
        }

        // Adherence timeframe
        const adherenceTimeframe = document.getElementById('adherenceTimeframe');
        if (adherenceTimeframe) {
            adherenceTimeframe.addEventListener('change', (e) => {
                this.updateAdherenceChart(e.target.value);
            });
        }

        // Form submission
        const addMedicationForm = document.getElementById('addMedicationForm');
        if (addMedicationForm) {
            addMedicationForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.addMedication(e.target);
            });
        }
    }

    setupSearchAndFilter() {
        // Add app-wide search styling classes to the medication search input
        const input = document.getElementById('medicationSearch');
        if (input) {
            input.classList.add('search-input');
            const wrapper = input.closest('.search-box');
            if (wrapper) {
                wrapper.classList.add('search-box');
                const searchIcon = wrapper.querySelector('i');
                if (searchIcon) searchIcon.classList.add('search-icon');
                // Optional clear button support if present
                const clearBtn = wrapper.querySelector('.search-clear');
                if (clearBtn) {
                    clearBtn.addEventListener('click', () => {
                        input.value = '';
                        this.currentFilter = '';
                        this.filterMedications();
                    });
                }
            }
        }
    }

    filterMedications() {
        const rows = document.querySelectorAll('#medicationTable tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            const medicationName = row.querySelector('.medication-name')?.textContent.toLowerCase() || '';
            const patientName = row.querySelector('.patient-name')?.textContent.toLowerCase() || '';
            const status = row.querySelector('.status-badge')?.textContent.trim() || '';

            const matchesSearch = !this.currentFilter || 
                medicationName.includes(this.currentFilter) || 
                patientName.includes(this.currentFilter);

            const matchesStatus = !this.currentStatusFilter || 
                status === this.currentStatusFilter;

            if (matchesSearch && matchesStatus) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        this.updateVisibleCount(visibleCount);
    }

    updateVisibleCount(count) {
        const footer = document.querySelector('.card-footer');
        if (footer) {
            const countElement = footer.querySelector('.visible-count');
            if (countElement) {
                countElement.textContent = `${count} medications found`;
            } else {
                const newCountElement = document.createElement('span');
                newCountElement.className = 'visible-count';
                newCountElement.textContent = `${count} medications found`;
                footer.appendChild(newCountElement);
            }
        }
    }

    initializeCharts() {
        this.createAdherenceChart();
    }

    createAdherenceChart() {
        const ctx = document.getElementById('adherenceChart');
        if (!ctx) return;

        this.adherenceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Taken on time', 'Taken late', 'Missed', 'Skipped'],
                datasets: [{
                    data: [75, 15, 8, 2],
                    backgroundColor: [
                        '#1e3a8a',
                        '#3b82f6',
                        '#93c5fd',
                        '#d1d5db'
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#3b82f6',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true
                    }
                },
                cutout: '60%',
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }

    updateAdherenceChart(timeframe) {
        if (!this.adherenceChart) return;

        let data;
        switch (timeframe) {
            case '7':
                data = [80, 12, 6, 2];
                break;
            case '30':
                data = [75, 15, 8, 2];
                break;
            case '90':
                data = [70, 18, 10, 2];
                break;
            default:
                data = [75, 15, 8, 2];
        }

        this.adherenceChart.data.datasets[0].data = data;
        this.adherenceChart.update('active');
    }

    updateStatistics() {
        const stats = this.calculateStatistics();
        this.updateStatCards(stats);
    }

    calculateStatistics() {
        const rows = document.querySelectorAll('#medicationTable tbody tr:not([style*="display: none"])');
        const total = rows.length;
        let pending = 0, missed = 0, taken = 0;

        rows.forEach(row => {
            const status = row.querySelector('.status-badge')?.textContent.trim() || '';

            switch (status) {
                case 'Pending':
                    pending++;
                    break;
                case 'Missed':
                    missed++;
                    break;
                case 'Taken':
                    taken++;
                    break;
            }

        });

        return {
            total,
            pending,
            missed,
            taken
        };
    }

    updateStatCards(stats) {
        const totalCard = document.querySelector('.stat-card:nth-child(1) .stat-content h3');
        if (totalCard) totalCard.textContent = stats.total;

        const pendingCard = document.querySelector('.stat-card:nth-child(2) .stat-content h3');
        if (pendingCard) pendingCard.textContent = stats.pending;

        const missedCard = document.querySelector('.stat-card:nth-child(3) .stat-content h3');
        if (missedCard) missedCard.textContent = stats.missed;

        // If an adherence card exists in layout, keep previous value
    }

    openAddMedicationModal() {
        const modal = document.getElementById('addMedicationModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }
    }

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
            }, 300);
        }
    }

    async markAsTaken(medicationId) {
        const row = document.querySelector(`[data-medication-id="${medicationId}"]`);
        if (row) {
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = 'status-badge status-taken';
                statusBadge.innerHTML = '<i class="fas fa-check-circle"></i> Taken';
            }

            const adherenceFill = row.querySelector('.adherence-fill');
            const adherenceText = row.querySelector('.adherence-text');
            if (adherenceFill && adherenceText) {
                const currentAdherence = parseInt(adherenceText.textContent.replace('%', ''));
                const newAdherence = Math.min(100, currentAdherence + 2);
                adherenceFill.style.width = `${newAdherence}%`;
                adherenceText.textContent = `${newAdherence}%`;
            }

            try {
                await fetch('api/medications.php', { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id: medicationId, status: 'Taken' }), credentials: 'same-origin' });
            } catch (e) { console.error('Failed to update status', e); }
            this.showNotification('Medication marked as taken successfully!', 'success');
            this.updateStatistics();
        }
    }

    editMedication(medicationId) {
        const row = document.querySelector(`[data-medication-id="${medicationId}"]`);
        if (!row) return;
        const patient = row.querySelector('.patient-id')?.textContent.trim() || '';
        const medName = row.querySelector('.medication-name')?.textContent.trim() || '';
        const dosage = row.querySelector('.dosage')?.textContent.trim() || '';
        const frequency = row.querySelector('.frequency')?.textContent.trim() || '';
        const nextDose = row.querySelector('.next-dose')?.textContent.trim() || '';

        const modal = document.createElement('div');
        modal.className = 'modal show';
        modal.style.display = 'flex';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class='fas fa-edit'></i> Edit Medication</h3>
                    <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group" style="flex:1">
                            <label>Patient ID</label>
                            <input id="em_patient" class="form-input" value="${patient}" readonly>
                        </div>
                        <div class="form-group" style="flex:2">
                            <label>Medication</label>
                            <input id="em_med" class="form-input" value="${medName}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Dosage</label>
                            <input id="em_dosage" class="form-input" value="${dosage}">
                        </div>
                        <div class="form-group">
                            <label>Frequency</label>
                            <input id="em_frequency" class="form-input" value="${frequency}">
                        </div>
                        <div class="form-group">
                            <label>Next Dose</label>
                            <input id="em_next" type="datetime-local" class="form-input">
                        </div>
                    </div>
                </div>
                <div class="modal-actions">
                    <button class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button>
                    <button class="btn btn-primary" id="em_save">Save</button>
                </div>
            </div>`;
        document.body.appendChild(modal);

        // Try to parse next dose back to a datetime-local value when possible
        const nextInput = modal.querySelector('#em_next');
        if (nextInput && nextDose) {
            const d = new Date(nextDose);
            if (!isNaN(d)) nextInput.value = d.toISOString().slice(0,16);
        }

        modal.querySelector('#em_save').addEventListener('click', async () => {
            const payload = {
                id: medicationId,
                medication: modal.querySelector('#em_med').value.trim(),
                dosage: modal.querySelector('#em_dosage').value.trim(),
                frequency: modal.querySelector('#em_frequency').value.trim(),
                next_dose: modal.querySelector('#em_next').value
            };
            try {
                await fetch('api/medications.php', { method:'PUT', headers:{'Content-Type':'application/json'}, body: JSON.stringify(payload), credentials:'same-origin' });
                // Update row without refetch
                row.querySelector('.medication-name').textContent = payload.medication;
                row.querySelector('.dosage').textContent = payload.dosage;
                row.querySelector('.frequency').textContent = payload.frequency;
                if (payload.next_dose) row.querySelector('.next-dose').textContent = this.formatDateTime(payload.next_dose);
                this.showNotification('Medication updated', 'success');
                modal.remove();
            } catch(e) {
                console.error(e);
                this.showNotification('Failed to update', 'error');
            }
        });
    }

    sendReminder(medicationId) {
        this.showNotification('Reminder sent successfully!', 'success');
    }

    async deleteMedication(medicationId) {
        if (confirm('Are you sure you want to delete this medication?')) {
            const row = document.querySelector(`[data-medication-id="${medicationId}"]`);
            if (row) {
                row.style.opacity = '0';
                setTimeout(() => {
                    row.remove();
                    this.updateStatistics();
                    this.showNotification('Medication deleted successfully!', 'success');
                }, 300);
                try {
                    await fetch(`api/medications.php?id=${encodeURIComponent(medicationId)}`, { method: 'DELETE', credentials: 'same-origin' });
                } catch (e) { console.error('Failed to delete medication', e); }
            }
        }
    }

    async addMedication(form) {
        const formData = new FormData(form);
        const medicationData = {
            id: 'MED' + Date.now(),
            patient_id: formData.get('patientId'),
            medication: formData.get('medicationName'),
            dosage: formData.get('dosage'),
            frequency: formData.get('frequency'),
            next_dose: this.formatDateTime(formData.get('nextDose')),
            status: 'Pending',
            adherence: 100,
            notes: formData.get('notes') || ''
        };

        this.addMedicationToTable(medicationData);
        this.closeModal('addMedicationModal');
        form.reset();
        this.showNotification('Medication added successfully!', 'success');
        this.updateStatistics();

        // Persist to backend
        try {
            await fetch('api/medications.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    patient_id: medicationData.patient_id,
                    medication: medicationData.medication,
                    dosage: medicationData.dosage,
                    frequency: medicationData.frequency,
                    next_dose: formData.get('nextDose'),
                    status: medicationData.status
                }),
                credentials: 'same-origin'
            });
        } catch (e) { console.error('Failed to add medication', e); }
    }

    addMedicationToTable(medicationData) {
        const tbody = document.querySelector('#medicationTable tbody');
        if (!tbody) return;

        const row = document.createElement('tr');
        row.setAttribute('data-medication-id', medicationData.id);
        row.innerHTML = `
            <td>
                <div class="patient-info">
                    <div class="patient-name">${medicationData.patient_id}</div>
                    <div class="patient-id">${medicationData.patient_id}</div>
                </div>
            </td>
            <td>
                <div class="medication-info">
                    <div class="medication-name">${medicationData.medication}</div>
                    <div class="medication-notes">${medicationData.notes}</div>
                </div>
            </td>
            <td>
                <div class="dosage-info">
                    <div class="dosage">${medicationData.dosage}</div>
                    <div class="frequency">${medicationData.frequency}</div>
                </div>
            </td>
            <td>
                <div class="dose-info">
                    <div class="next-dose">${medicationData.next_dose}</div>
                    <div class="last-dose">Last: Not taken yet</div>
                </div>
            </td>
            <td>
                <span class="status-badge status-pending">
                    <i class="fas fa-clock"></i>
                    ${medicationData.status}
                </span>
            </td>
            <td>
                <div class="action-buttons" style="position:static;">
                    <button class="btn btn-sm btn-primary" onclick="medicationTracker.markAsTaken('${medicationData.id}')" title="Mark as Taken">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="medicationTracker.editMedication('${medicationData.id}')" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="medicationTracker.sendReminder('${medicationData.id}')" title="Send Reminder">
                        <i class="fas fa-bell"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="medicationTracker.deleteMedication('${medicationData.id}')" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        `;

        tbody.appendChild(row);
    }

    formatDateTime(dateTimeString) {
        if (!dateTimeString) return 'Not scheduled';
        
        const date = new Date(dateTimeString);
        const now = new Date();
        const diffTime = date - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) {
            return `Today, ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
        } else if (diffDays === 1) {
            return `Tomorrow, ${date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}`;
        } else {
            return date.toLocaleDateString('en-US', { 
                weekday: 'long', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }

    resetForm(formId) {
        const form = document.getElementById(formId);
        if (form) {
            form.reset();
            this.showNotification('Form has been reset successfully', 'success');
        }
    }

    showNotification(message, type = 'info') {
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            // Fallback minimal toast top-center
            const containerId = 'notificationContainer';
            let container = document.getElementById(containerId);
            if (!container) {
                container = document.createElement('div');
                container.id = containerId;
                container.style.position = 'fixed';
                container.style.top = '16px';
                container.style.left = '50%';
                container.style.transform = 'translateX(-50%)';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = `notification notification-${type}`;
            toast.innerHTML = `<i class="fas fa-${this.getNotificationIcon(type)}"></i><span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => { if (toast.parentElement) toast.parentElement.removeChild(toast); }, 3000);
        }
    }

    getNotificationIcon(type) {
        switch (type) {
            case 'success': return 'check-circle';
            case 'error': return 'exclamation-triangle';
            case 'warning': return 'exclamation-circle';
            default: return 'info-circle';
        }
    }

    refreshSchedule() {
        this.showNotification('Schedule refreshed successfully!', 'success');
        // Optionally re-fetch meds
        this.fetchMedications();
    }

    exportMedicationData() {
        // Build CSV from current table state
        const rows = Array.from(document.querySelectorAll('#medicationTable tbody tr'))
            .filter(r => r.style.display !== 'none');
        const headers = ['Patient ID','Patient Name','Medication','Dosage','Frequency','Next Dose','Status'];
        const lines = [headers.join(',')];
        rows.forEach(r => {
            const patientId = (r.querySelector('.patient-id')?.textContent || '').replace(/,/g,' ');
            const patientName = (r.querySelector('.patient-name')?.textContent || '').replace(/,/g,' ');
            const med = (r.querySelector('.medication-name')?.textContent || '').replace(/,/g,' ');
            const dosage = (r.querySelector('.dosage')?.textContent || '').replace(/,/g,' ');
            const freq = (r.querySelector('.frequency')?.textContent || '').replace(/,/g,' ');
            const next = (r.querySelector('.next-dose')?.textContent || '').replace(/,/g,' ');
            const status = (r.querySelector('.status-badge')?.textContent || '').trim();
            lines.push([patientId, patientName, med, dosage, freq, next, status].join(','));
        });
        const blob = new Blob([lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'medications.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        this.showNotification('Exported CSV', 'success');
    }

    openBulkReminderModal() {
        this.showNotification('Bulk reminder functionality coming soon!', 'info');
    }
}

// Initialize Medication Tracker
let medicationTracker;

document.addEventListener('DOMContentLoaded', function() {
    medicationTracker = new MedicationTracker();
});

// Global functions for onclick handlers
function openAddMedicationModal() {
    if (medicationTracker) {
        medicationTracker.openAddMedicationModal();
    }
}

function closeModal(modalId) {
    if (medicationTracker) {
        medicationTracker.closeModal(modalId);
    }
}

function resetForm(formId) {
    if (medicationTracker) {
        medicationTracker.resetForm(formId);
    }
}

function markAsTaken(medicationId) {
    if (medicationTracker) {
        medicationTracker.markAsTaken(medicationId);
    }
}

function editMedication(medicationId) {
    if (medicationTracker) {
        medicationTracker.editMedication(medicationId);
    }
}

function sendReminder(medicationId) {
    if (medicationTracker) {
        medicationTracker.sendReminder(medicationId);
    }
}

function deleteMedication(medicationId) {
    if (medicationTracker) {
        medicationTracker.deleteMedication(medicationId);
    }
}

function refreshSchedule() {
    if (medicationTracker) {
        medicationTracker.refreshSchedule();
    }
}

function exportMedicationData() {
    if (medicationTracker) {
        medicationTracker.exportMedicationData();
    }
}

function openBulkReminderModal() {
    if (medicationTracker) {
        medicationTracker.openBulkReminderModal();
    }
} 