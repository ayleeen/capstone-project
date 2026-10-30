// settings.js — Settings hub

const alertModalEl = document.getElementById('alertModal');
const alertModal   = new bootstrap.Modal(alertModalEl);

const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK' } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  document.getElementById('alertOkBtn').textContent = okText;
  alertModal.show();
}

