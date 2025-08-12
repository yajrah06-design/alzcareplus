// User Accounts management – loads users, supports add/edit/delete inline

document.addEventListener('DOMContentLoaded', () => {
  loadUsers();
  const addBtn = document.getElementById('addUserBtn');
  if (addBtn) addBtn.addEventListener('click', openAddUserModal);
  
  // Refresh online status every 2 minutes to keep it stable
  setInterval(() => {
    updateOnlineStatus();
  }, 2 * 60 * 1000);
});

async function loadUsers() {
  try {
    const res = await fetch('api/users.php', { credentials: 'same-origin' });
    const json = await res.json();
    if (!json.success) return;
    const tbody = document.getElementById('usersTbody');
    if (!tbody) return;
    tbody.innerHTML = '';
    (json.data || []).forEach(u => {
      const tr = document.createElement('tr');
      // Mark current user row if we can identify them
      if (u.email === getCurrentUserEmail() || u.name === getCurrentUserName()) {
        tr.setAttribute('data-current-user', 'true');
      }
      
      tr.innerHTML = `
        <td>${u.user_id}</td>
        <td>${u.name}</td>
        <td>${u.email}</td>
        <td>${capitalize(u.role)}</td>
        <td>${u.assigned_patients || '-'}</td>
        <td>${u.last_login || '-'}</td>
        <td><span class="status-badge ${isOnline(u.last_login, u.user_id) ? 'status-active' : 'status-inactive'}">${isOnline(u.last_login, u.user_id) ? 'Online' : 'Offline'}</span></td>
        <td>
          <div class="action-buttons">
            <button class="btn btn-sm btn-secondary" data-edit="${u.id}" title="Edit"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-danger" data-delete="${u.id}" title="Delete"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      `;
      tbody.appendChild(tr);
    });
    wireUserActions();
  } catch (e) {
    console.error(e);
  }
}

function wireUserActions() {
  document.querySelectorAll('[data-edit]').forEach(btn => btn.addEventListener('click', () => openEditUserModal(btn.getAttribute('data-edit'))));
  document.querySelectorAll('[data-delete]').forEach(btn => btn.addEventListener('click', () => deleteUser(btn.getAttribute('data-delete'))));
}

function openAddUserModal() {
  const modal = buildUserModal('New Account', { name: '', email: '', role: 'caregiver', phone: '' }, async (payload) => {
    const res = await fetch('api/users.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(payload) });
    const json = await res.json();
    if (json.success) { showNotification('User created', 'success'); loadUsers(); modal.remove(); } else { showNotification('Failed to create user', 'error'); }
  }, true);
  document.body.appendChild(modal);
}

async function openEditUserModal(id) {
  // Simple fetch current row from table for editing
  const row = Array.from(document.querySelectorAll('#usersTbody tr')).find(tr => tr.querySelector('[data-edit]')?.getAttribute('data-edit') === id);
  if (!row) return;
  const t = row.querySelectorAll('td');
  const data = { id: parseInt(id, 10), user_id: t[0].innerText.trim(), name: t[1].innerText.trim(), email: t[2].innerText.trim(), role: t[3].innerText.trim().toLowerCase(), assigned_patients: t[4].innerText.trim(), phone: '' };
  const modal = buildUserModal('Edit Account', data, async (payload) => {
    const res = await fetch('api/users.php', { method: 'PUT', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify(payload) });
    const json = await res.json();
    if (json.success) { showNotification('User updated', 'success'); loadUsers(); modal.remove(); } else { showNotification('Update failed', 'error'); }
  }, false);
  document.body.appendChild(modal);
}

async function deleteUser(id) {
  if (!confirm('Delete this user?')) return;
  const res = await fetch('api/users.php', { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ id }) });
  const json = await res.json();
  if (json.success) { showNotification('User deleted', 'success'); loadUsers(); } else { showNotification('Delete failed', 'error'); }
}

function buildUserModal(title, data, onSubmit, needPassword) {
  const modal = document.createElement('div');
  modal.className = 'modal show';
  modal.style.display = 'flex';
  modal.innerHTML = `
    <div class="modal-content large-modal">
      <div class="modal-header"><h3><i class="fas fa-user"></i> ${title}</h3><button class="close" onclick="this.closest('.modal').remove()">&times;</button></div>
      <div class="modal-body">
        <div class="form-row">
          <div class="form-group"><label>Name</label><input id="userName" class="form-input" value="${escapeHtml(data.name)}" required></div>
          <div class="form-group"><label>Email</label><input id="userEmail" class="form-input" type="email" value="${escapeHtml(data.email)}" required></div>
        </div>
        <div class="form-row">
          <div class="form-group"><label>Role</label>
            <select id="userRole" class="form-select">
              <option value="admin" ${data.role==='admin'?'selected':''}>Admin</option>
              <option value="caregiver" ${data.role==='caregiver'?'selected':''}>Caregiver</option>
            </select>
          </div>
          
        </div>
        <div class="form-row">
          <div class="form-group"><label>Assigned Patients (comma IDs)</label><input id="userAssigned" class="form-input" value="${escapeHtml(data.assigned_patients || '')}"></div>
          <div class="form-group"><label>Phone</label><input id="userPhone" class="form-input" value="${escapeHtml(data.phone || '')}"></div>
        </div>
        ${needPassword ? '<div class="form-row"><div class="form-group"><label>Temporary Password</label><input id="userPassword" class="form-input" type="text" value="password123"></div></div>' : ''}
      </div>
      <div class="modal-actions"><button class="btn btn-secondary" onclick="this.closest('.modal').remove()">Cancel</button><button class="btn btn-primary" id="userSubmit">Save</button></div>
    </div>`;
  modal.querySelector('#userSubmit').addEventListener('click', () => {
    const payload = {
      id: data.id,
      user_id: data.user_id,
      name: modal.querySelector('#userName').value.trim(),
      email: modal.querySelector('#userEmail').value.trim(),
      role: modal.querySelector('#userRole').value,
      phone: modal.querySelector('#userPhone')?.value.trim() || null,
      assigned_patients: modal.querySelector('#userAssigned')?.value.trim() || null
    };
    if (needPassword) payload.password = modal.querySelector('#userPassword').value.trim();
    onSubmit(payload);
  });
  return modal;
}

function capitalize(s){ return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }
function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[c])); }
function isOnline(lastLogin, userId = null){
  // Always show current user as online
  if (userId && userId === getCurrentUserId()) return true;
  
  // If no last login data, show as offline (except current user)
  if (!lastLogin || lastLogin === '-' || lastLogin === 'Never') return false;
  
  let raw = String(lastLogin).trim();
  
  // Handle different date formats more robustly
  let parsed;
  if (/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(raw)) {
    // MySQL format "YYYY-MM-DD HH:MM:SS" - treat as local time
    raw = raw.replace(' ', 'T');
    parsed = new Date(raw);
  } else if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/.test(raw)) {
    // ISO format
    parsed = new Date(raw);
  } else if (/^(Today|Yesterday)/.test(raw)) {
    // Handle "Today, 07:45 AM" format
    const now = new Date();
    if (raw.includes('Today')) {
      const timeMatch = raw.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/);
      if (timeMatch) {
        let hours = parseInt(timeMatch[1]);
        const minutes = parseInt(timeMatch[2]);
        const ampm = timeMatch[3];
        
        if (ampm === 'PM' && hours !== 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;
        
        parsed = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes);
      }
    } else if (raw.includes('Yesterday')) {
      const timeMatch = raw.match(/(\d{1,2}):(\d{2})\s*(AM|PM)/);
      if (timeMatch) {
        let hours = parseInt(timeMatch[1]);
        const minutes = parseInt(timeMatch[2]);
        const ampm = timeMatch[3];
        
        if (ampm === 'PM' && hours !== 12) hours += 12;
        if (ampm === 'AM' && hours === 12) hours = 0;
        
        const yesterday = new Date(now);
        yesterday.setDate(yesterday.getDate() - 1);
        parsed = new Date(yesterday.getFullYear(), yesterday.getMonth(), yesterday.getDate(), hours, minutes);
      }
    }
  } else {
    // Try direct parsing
    parsed = new Date(raw);
  }
  
  const t = parsed.getTime();
  if (Number.isNaN(t)) return false;
  
  const diff = Date.now() - t;
  // More generous: consider online if last activity within past 45 minutes
  // to account for various timezone and system differences
  return diff >= 0 && diff < 45 * 60 * 1000;
}

function getCurrentUserId() {
  // Try to get current user ID from various sources
  // Check if we're in a session or have user data available
  const userRow = document.querySelector('#usersTbody tr[data-current-user="true"]');
  if (userRow) {
    return userRow.querySelector('td:first-child')?.textContent?.trim();
  }
  
  // Fallback: check if any row contains current user's email/name
  // This is a simple heuristic - you might want to pass current user data from PHP
  return null;
}

function getCurrentUserEmail() {
  // Get current user email from PHP session data passed to JavaScript
  return window.currentUser?.email || null;
}

function getCurrentUserName() {
  // Get current user name from PHP session data passed to JavaScript
  return window.currentUser?.name || null;
}

function updateOnlineStatus() {
  // Update online status for all users without reloading the entire table
  const rows = document.querySelectorAll('#usersTbody tr');
  rows.forEach(row => {
    const lastLoginCell = row.querySelector('td:nth-child(6)');
    const statusCell = row.querySelector('td:nth-child(7)');
    
    if (lastLoginCell && statusCell) {
      const lastLogin = lastLoginCell.textContent.trim();
      const userId = row.querySelector('td:first-child')?.textContent?.trim();
      
      const isUserOnline = isOnline(lastLogin, userId);
      const statusBadge = statusCell.querySelector('.status-badge');
      
      if (statusBadge) {
        statusBadge.className = `status-badge ${isUserOnline ? 'status-active' : 'status-inactive'}`;
        statusBadge.textContent = isUserOnline ? 'Online' : 'Offline';
      }
    }
  });
}

// Use global showNotification from header


