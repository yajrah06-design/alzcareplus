// System Settings – Frontend persistence using localStorage
// Applies consistent styles and instant feedback without page reloads.

(function () {
  function showToast(message, type = 'success') {
    if (typeof showNotification === 'function') {
      showNotification(message, type);
      return;
    }
    alert(message);
  }

  function saveToStorage(key, data) {
    localStorage.setItem(key, JSON.stringify(data));
  }

  function loadFromStorage(key, fallback) {
    try {
      const v = JSON.parse(localStorage.getItem(key));
      return v ?? fallback;
    } catch (e) {
      return fallback;
    }
  }

  // Initialize forms with saved values
  function initializeGeneralSettings() {
    const saved = loadFromStorage('generalSettings', {
      system_name: 'AlzCare+',
      heart_rate_threshold: 100,
      date_format: 'MM/DD/YYYY',
      data_retention: '90'
    });

    const name = document.getElementById('system_name');
    const hr = document.getElementById('heart_rate_threshold');
    const df = document.getElementById('date_format');
    const dr = document.getElementById('data_retention');
    if (name) name.value = saved.system_name;
    if (hr) hr.value = saved.heart_rate_threshold;
    if (df) df.value = saved.date_format;
    if (dr) dr.value = saved.data_retention;
  }

  function initializeNotificationSettings() {
    const saved = loadFromStorage('notificationSettings', {
      sms_notification: 'enabled',
      app_notification: 'enabled',
      email_notification: 'enabled',
      critical_calls: 'enabled'
    });

    const sms = document.getElementById('sms_notification');
    const app = document.getElementById('app_notification');
    const email = document.getElementById('email_notification');
    const calls = document.getElementById('critical_calls');
    if (sms) sms.value = saved.sms_notification;
    if (app) app.value = saved.app_notification;
    if (email) email.value = saved.email_notification;
    if (calls) calls.value = saved.critical_calls;
  }

  // Wire up form submits
  function wireForms() {
    const generalForm = document.getElementById('generalSettingsForm');
    if (generalForm) {
      generalForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const payload = {
          system_name: document.getElementById('system_name').value,
          heart_rate_threshold: document.getElementById('heart_rate_threshold').value,
          date_format: document.getElementById('date_format').value,
          data_retention: document.getElementById('data_retention').value
        };
        saveToStorage('generalSettings', payload);
        showToast('General settings saved', 'success');
      });
    }

    const notifForm = document.getElementById('notificationSettingsForm');
    if (notifForm) {
      notifForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const payload = {
          sms_notification: document.getElementById('sms_notification').value,
          app_notification: document.getElementById('app_notification').value,
          email_notification: document.getElementById('email_notification').value,
          critical_calls: document.getElementById('critical_calls').value
        };
        saveToStorage('notificationSettings', payload);
        showToast('Notification settings saved', 'success');
      });
    }
  }

  // Apply appearance tweaks based on theme
  function applyAppearance() {
    // Rely on global dark-theme styles in assets/css/style.css
    // Here, we can add minor enhancements for form controls if needed later.
  }

  // Bootstrap
  document.addEventListener('DOMContentLoaded', function () {
    initializeGeneralSettings();
    initializeNotificationSettings();
    wireForms();
    applyAppearance();
  });
})();


