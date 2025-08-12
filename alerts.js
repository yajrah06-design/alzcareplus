// Alerts JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeAlerts();
});

function initializeAlerts() {
    addAlertManagement();
    addDateRangeFilter();
    addAlertConfiguration();
    loadAlertConfigs();
    loadAlertHistory();
    const search = document.getElementById('configSearch');
    if (search) search.addEventListener('input', filterConfigs);
    const addBtn = document.getElementById('addAlertBtn');
    if (addBtn) addBtn.addEventListener('click', (e) => { e.preventDefault(); showAddAlertForm(); });
}

function addAlertSearch() {
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.placeholder = 'Search alerts by type or patient ID...';
    searchInput.className = 'table-search';
    
    const tableContainer = document.querySelector('.table-container');
    tableContainer.insertBefore(searchInput, tableContainer.querySelector('.data-table'));
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('.data-table tbody tr');
        
        rows.forEach(row => {
            const alertType = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const patientId = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
            
            if (alertType.includes(searchTerm) || patientId.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}

function addAlertManagement() {
    // Add action button handlers for alert configuration (icon buttons)
    const configButtons = document.querySelectorAll('.action-buttons .btn[data-action]');
    configButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const action = this.getAttribute('data-action');
            const row = this.closest('tr');
            const alertName = row.querySelector('td:nth-child(1)').textContent;
            if (action === 'edit') editAlert(alertName, row);
            if (action === 'toggle') disableAlert(alertName, row);
        });
    });
    
    // Add "Add New Alert" button handler
    const addButton = document.querySelector('.btn-primary');
    if (addButton && addButton.textContent.includes('Add New Alert')) {
        addButton.addEventListener('click', function(e) {
            e.preventDefault();
            showAddAlertForm();
        });
    }
}

async function loadAlertConfigs() {
    try {
        const res = await fetch('api/alerts.php?resource=config');
        const json = await res.json();
        if (json.success) {
            const tbody = document.getElementById('configsTbody');
            if (!tbody) return;
            tbody.innerHTML = '';
            (json.data || []).forEach(conf => {
                const tr = document.createElement('tr');
                tr.dataset.id = conf.id;
                tr.innerHTML = `
                    <td><strong>${conf.alert_name}</strong></td>
                    <td>${conf.condition}</td>
                    <td>${conf.threshold}</td>
                    <td><span class="status-badge status-normal">${conf.notification_type}</span></td>
                    <td>${conf.recipients}</td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" data-toggle-id="${conf.id}" ${conf.status === 'Active' ? 'checked' : ''} />
                            <span class="slider"></span>
                        </label>
                        <span class="status-badge ${conf.status === 'Active' ? 'status-active' : 'status-inactive'}" data-status-badge="${conf.id}">${conf.status}</span>
                    </td>
                    <td>${conf.updated_at || conf.created_at || ''}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-secondary" data-action="edit" title="Edit"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-primary" data-test="${conf.id}" title="Test"><i class="fas fa-vial"></i></button>
                            <button class="btn btn-sm btn-danger" data-action="toggle" title="${conf.status === 'Active' ? 'Disable' : 'Enable'}"><i class="fas fa-power-off"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            addAlertManagement();
            wireConfigActions();
        }
    } catch (e) { console.error(e); }
}

async function loadAlertHistory() {
    try {
        const res = await fetch('api/alerts.php?resource=history&limit=100');
        const json = await res.json();
        if (json.success) {
            const tbody = document.getElementById('historyTbody');
            if (!tbody) return;
            tbody.innerHTML = '';
            (json.data || []).forEach(h => {
                const tr = document.createElement('tr');
                const statusClass = h.status === 'Critical' ? 'status-critical' : (h.status === 'Warning' ? 'status-warning' : 'status-normal');
                tr.innerHTML = `
                    <td>${h.triggered_at}</td>
                    <td>${h.patient_id}</td>
                    <td>${h.alert_type}</td>
                    <td><span class="status-badge ${statusClass}">${h.status}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn btn-sm btn-primary" data-action="ack" data-id="${h.id}" title="Acknowledge"><i class="fas fa-check"></i></button>
                            <button class="btn btn-sm btn-secondary" data-action="resolve" data-id="${h.id}" title="Resolve"><i class="fas fa-flag"></i></button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
            // Wire action buttons
            document.querySelectorAll('#historyTbody [data-action]').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const id = btn.getAttribute('data-id');
                    const action = btn.getAttribute('data-action');
                    try {
                        const res = await fetch('api/alerts.php?resource=history', {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id, action })
                        });
                        const json = await res.json();
                        if (json.success) {
                            showNotification(`Alert ${action === 'ack' ? 'acknowledged' : 'resolved'}!`, 'success');
                            loadAlertHistory();
                        } else {
                            showNotification('Action failed', 'error');
                        }
                    } catch (err) { console.error(err); showNotification('Action failed', 'error'); }
                });
            });
        }
    } catch (e) { console.error(e); }
}

function filterConfigs() {
    const term = (document.getElementById('configSearch')?.value || '').toLowerCase();
    document.querySelectorAll('#configsTbody tr').forEach(tr => {
        tr.style.display = tr.textContent.toLowerCase().includes(term) ? '' : 'none';
    });
}

function wireConfigActions() {
    // Test button: create a mock history row
    document.querySelectorAll('#configsTbody [data-test]').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.getAttribute('data-test');
            try {
                await fetch('api/alerts.php?resource=history', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ patient_id: 'TEST', alert_type: `Config#${id}`, status: 'Warning', action_taken: 'Test alert' })
                });
                showNotification('Test alert generated', 'success');
                loadAlertHistory();
            } catch (e) { console.error(e); showNotification('Failed to generate test alert', 'error'); }
        });
    });

    // Inline toggle
    document.querySelectorAll('#configsTbody [data-toggle-id]').forEach(input => {
        input.addEventListener('change', async () => {
            const id = input.getAttribute('data-toggle-id');
            const newStatus = input.checked ? 'Active' : 'Inactive';
            // Build minimal payload for status update
            const row = input.closest('tr');
            const tds = row.querySelectorAll('td');
            const payload = {
                id: parseInt(id, 10),
                alert_name: tds[0].innerText.trim(),
                condition: tds[1].innerText.trim(),
                threshold: tds[2].innerText.trim(),
                notification_type: tds[3].innerText.trim(),
                recipients: tds[4].innerText.trim(),
                status: newStatus
            };
            try {
                const res = await fetch('api/alerts.php?resource=config', { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
                const json = await res.json();
                if (json.success) {
                    const badge = document.querySelector(`[data-status-badge="${id}"]`);
                    if (badge) {
                        badge.textContent = newStatus;
                        badge.classList.toggle('status-active', newStatus === 'Active');
                        badge.classList.toggle('status-inactive', newStatus !== 'Active');
                    }
                    showNotification(`Alert ${newStatus.toLowerCase()}`, 'success');
                } else {
                    input.checked = !input.checked; // revert
                    showNotification('Failed to update status', 'error');
                }
            } catch (e) {
                console.error(e);
                input.checked = !input.checked; // revert
                showNotification('Failed to update status', 'error');
            }
        });
    });
}

function editAlert(alertName, row) {
    const modal = createModal(`Edit Alert - ${alertName}`, `
        <form class="edit-alert-form">
            <div class="form-group">
                <label>Alert Name</label>
                <input type="text" value="${alertName}" class="form-input">
            </div>
            <div class="form-group">
                <label>Condition</label>
                <input type="text" value="${getAlertCondition(alertName)}" class="form-input">
            </div>
            <div class="form-group">
                <label>Threshold</label>
                <input type="text" value="${getAlertThreshold(alertName)}" class="form-input">
            </div>
            <div class="form-group">
                <label>Notification Type</label>
                <select class="form-select">
                    <option value="SMS&App" selected>SMS & App</option>
                    <option value="SMS">SMS Only</option>
                    <option value="App">App Only</option>
                    <option value="Email">Email Only</option>
                </select>
            </div>
            <div class="form-group">
                <label>Recipients</label>
                <select class="form-select" multiple>
                    <option value="Family" selected>Family</option>
                    <option value="Caregiver" selected>Caregiver</option>
                    <option value="Nurse">Nurse</option>
                    <option value="Doctor">Doctor</option>
                </select>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            </div>
        </form>
    `);
    
    document.body.appendChild(modal);
    
    // Handle form submission
    const form = modal.querySelector('.edit-alert-form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const inputs = form.querySelectorAll('.form-input, .form-select');
        const payload = {
            id: row ? parseInt(row.dataset.id || '0', 10) : 0,
            alert_name: inputs[0].value,
            condition: inputs[1].value,
            threshold: inputs[2].value,
            notification_type: inputs[3].value,
            recipients: 'Family,Caregiver',
            status: 'Active'
        };
        try {
            const res = await fetch('api/alerts.php?resource=config', { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const json = await res.json();
            if (json.success) {
                showNotification('Alert configuration updated successfully!', 'success');
                modal.remove();
            } else {
                showNotification('Failed to update alert config', 'error');
            }
        } catch (err) { console.error(err); showNotification('Failed to update alert config', 'error'); }
    });
}

function disableAlert(alertName, row) {
    const modal = createModal('Confirm Alert Disable', `
        <div class="disable-confirmation">
            <p>Are you sure you want to disable the alert <strong>${alertName}</strong>?</p>
            <p>This will stop all notifications for this alert type.</p>
            <div class="form-actions">
                <button class="btn btn-danger confirm-disable">Disable Alert</button>
                <button class="btn btn-secondary modal-close">Keep Active</button>
            </div>
        </div>
    `);
    
    document.body.appendChild(modal);
    
    const disableBtn = modal.querySelector('.confirm-disable');
    disableBtn.addEventListener('click', async function() {
        try {
            const id = row ? parseInt(row.dataset.id || '0', 10) : 0;
            const res = await fetch('api/alerts.php?resource=config', { method: 'PUT', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id, status: 'Inactive', alert_name: alertName, condition: '', threshold: '', notification_type: '', recipients: '' }) });
            const json = await res.json();
            if (json.success) {
                showNotification('Alert disabled successfully!', 'success');
                modal.remove();
                if (row) {
                    const statusCell = row.querySelector('td:nth-child(6)');
                    statusCell.innerHTML = '<span class="status-badge status-inactive">Inactive</span>';
                }
            } else {
                showNotification('Failed to disable alert', 'error');
            }
        } catch (err) { console.error(err); showNotification('Failed to disable alert', 'error'); }
    });
}

function showAddAlertForm() {
    const modal = createModal('Add New Alert', `
        <form class="add-alert-form">
            <div class="form-group">
                <label>Alert Name</label>
                <input type="text" class="form-input" placeholder="Enter alert name" required>
            </div>
            <div class="form-group">
                <label>Condition</label>
                <select class="form-select" required>
                    <option value="">Select condition</option>
                    <option value="Heart Rate > 100 Bpm">Heart Rate > 100 Bpm</option>
                    <option value="Medication Not Taken Within 1 Hour">Medication Not Taken Within 1 Hour</option>
                    <option value="Outside Safe Zone">Outside Safe Zone</option>
                    <option value="Steps < 500 In 12 Hours">Steps < 500 In 12 Hours</option>
                    <option value="Blood Pressure > 140/90">Blood Pressure > 140/90</option>
                    <option value="Temperature > 100°F">Temperature > 100°F</option>
                </select>
            </div>
            <div class="form-group">
                <label>Threshold</label>
                <input type="text" class="form-input" placeholder="e.g., 100 Bpm" required>
            </div>
            <div class="form-group">
                <label>Notification Type</label>
                <select class="form-select" required>
                    <option value="">Select notification type</option>
                    <option value="SMS&App">SMS & App</option>
                    <option value="SMS">SMS Only</option>
                    <option value="App">App Only</option>
                    <option value="Email">Email Only</option>
                </select>
            </div>
            <div class="form-group">
                <label>Recipients</label>
                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" value="Family" checked>
                        <span>Family</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" value="Caregiver" checked>
                        <span>Caregiver</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" value="Nurse">
                        <span>Nurse</span>
                    </label>
                    <label class="checkbox-label">
                        <input type="checkbox" value="Doctor">
                        <span>Doctor</span>
                    </label>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Alert</button>
                <button type="button" class="btn btn-secondary modal-close">Cancel</button>
            </div>
        </form>
    `);
    
    document.body.appendChild(modal);
    
    // Handle form submission
    const form = modal.querySelector('.add-alert-form');
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const inputs = form.querySelectorAll('.form-input, .form-select');
        const payload = {
            alert_name: inputs[0].value,
            condition: inputs[1].value,
            threshold: inputs[2].value,
            notification_type: inputs[3].value,
            recipients: Array.from(form.querySelectorAll('.checkbox-label input:checked')).map(i => i.value).join(',')
        };
        try {
            const res = await fetch('api/alerts.php?resource=config', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) });
            const json = await res.json();
            if (json.success) {
                showNotification('New alert added successfully!', 'success');
                modal.remove();
            } else {
                showNotification('Failed to add alert', 'error');
            }
        } catch (err) { console.error(err); showNotification('Failed to add alert', 'error'); }
    });
}

function addDateRangeFilter() {
    const dateFilter = document.querySelector('.form-select');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            const selectedRange = this.value;
            updateAlertHistory(selectedRange);
        });
    }
}

function updateAlertHistory(range) {
    // Simulate updating alert history based on date range
    const historyTable = document.querySelector('.data-table');
    const rows = historyTable.querySelectorAll('tbody tr');
    
    // Add loading effect
    rows.forEach(row => {
        row.style.opacity = '0.5';
    });
    
    setTimeout(() => {
        rows.forEach(row => {
            row.style.opacity = '1';
        });
        showNotification(`Alert history updated for ${range}`, 'success');
    }, 500);
}

function addAlertConfiguration() {
    // Add click handlers to alert history rows
    const historyRows = document.querySelectorAll('.data-table tbody tr');
    historyRows.forEach(row => {
        row.addEventListener('click', function() {
            const patientId = this.querySelector('td:nth-child(2)').textContent;
            const alertType = this.querySelector('td:nth-child(3)').textContent;
            const status = this.querySelector('td:nth-child(4)').textContent.trim();
            const action = this.querySelector('td:nth-child(5)').textContent;
            
            showAlertDetails(patientId, alertType, status, action);
        });
    });
}

function showAlertDetails(patientId, alertType, status, action) {
    const modal = createModal(`Alert Details - ${alertType}`, `
        <div class="alert-details">
            <div class="alert-info">
                <h4>Alert Information</h4>
                <p><strong>Patient ID:</strong> ${patientId}</p>
                <p><strong>Alert Type:</strong> ${alertType}</p>
                <p><strong>Status:</strong> <span class="status-badge ${status === 'Critical' ? 'status-critical' : 'status-warning'}">${status}</span></p>
                <p><strong>Action Taken:</strong> ${action}</p>
                <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
            </div>
            <div class="alert-timeline">
                <h4>Alert Timeline</h4>
                <div class="timeline-item">
                    <span class="time">${new Date().toLocaleTimeString()}</span>
                    <span class="event">Alert triggered</span>
                </div>
                <div class="timeline-item">
                    <span class="time">${new Date(Date.now() - 300000).toLocaleTimeString()}</span>
                    <span class="event">Notification sent</span>
                </div>
                <div class="timeline-item">
                    <span class="time">${new Date(Date.now() - 600000).toLocaleTimeString()}</span>
                    <span class="event">Action taken: ${action}</span>
                </div>
            </div>
            <div class="alert-actions">
                <h4>Available Actions</h4>
                <div class="action-buttons">
                    <button class="btn btn-primary">Acknowledge</button>
                    <button class="btn btn-secondary">Escalate</button>
                    <button class="btn btn-danger">Mark as Resolved</button>
                </div>
            </div>
        </div>
    `);
    
    document.body.appendChild(modal);
    
    // Add action button handlers
    const actionButtons = modal.querySelectorAll('.action-buttons .btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim();
            showNotification(`Alert ${action.toLowerCase()} successfully!`, 'success');
            modal.remove();
        });
    });
}

// Helper functions
function getAlertCondition(alertName) {
    const conditions = {
        'High Heart Rate': 'Heart Rate > 100 Bpm',
        'Missed Medication': 'Medication Not Taken Within 1 Hour',
        'GPS Boundary': 'Outside Safe Zone',
        'Low Activity': 'Steps < 500 In 12 Hours'
    };
    return conditions[alertName] || 'Custom Condition';
}

function getAlertThreshold(alertName) {
    const thresholds = {
        'High Heart Rate': '100 Bpm',
        'Missed Medication': '60 Min',
        'GPS Boundary': '500M Radius',
        'Low Activity': '500 Steps'
    };
    return thresholds[alertName] || 'Custom Threshold';
}

function createModal(title, content) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${title}</h3>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                ${content}
            </div>
        </div>
    `;
    
    // Close modal functionality
    const closeBtn = modal.querySelector('.modal-close');
    closeBtn.addEventListener('click', () => modal.remove());
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) modal.remove();
    });
    
    return modal;
}

// Use global showNotification from header

// Add CSS for alerts
const alertsStyle = document.createElement('style');
alertsStyle.textContent = `
    .edit-alert-form,
    .add-alert-form {
        display: grid;
        gap: 16px;
    }
    
    .checkbox-group {
        display: grid;
        gap: 8px;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    
    .checkbox-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
    }
    
    .disable-confirmation {
        text-align: center;
    }
    
    .disable-confirmation p {
        margin-bottom: 16px;
        color: #374151;
    }
    
    .alert-details {
        display: grid;
        gap: 20px;
    }
    
    .alert-info h4,
    .alert-timeline h4,
    .alert-actions h4 {
        color: #1f2937;
        margin-bottom: 12px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 4px;
    }
    
    .alert-info p {
        margin-bottom: 8px;
        color: #374151;
    }
    
    .alert-timeline {
        background-color: #f9fafb;
        padding: 16px;
        border-radius: 8px;
    }
    
    .timeline-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
    }
    
    .timeline-item:last-child {
        border-bottom: none;
    }
    
    .time {
        font-weight: 500;
        color: #6b7280;
    }
    
    .alert-actions {
        background-color: #f9fafb;
        padding: 16px;
        border-radius: 8px;
    }
    
    .action-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .data-table tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .data-table tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 20px;
    }
`;
document.head.appendChild(alertsStyle); 