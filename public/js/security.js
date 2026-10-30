
const passwordForm    = document.getElementById('passwordForm');
const currentPassword = document.getElementById('currentPassword');
const newPassword     = document.getElementById('newPassword');
const confirmPassword = document.getElementById('confirmPassword');
const currentError    = document.getElementById('currentError');
const newError        = document.getElementById('newError');
const confirmError    = document.getElementById('confirmError');
const strengthBars    = document.getElementById('strengthBars');
const strengthText    = document.getElementById('strengthText');
const reqItems        = [...document.querySelectorAll('#reqList li')];
const pwMeta          = document.getElementById('pwMeta');
const activityBody    = document.getElementById('activityBody');
const viewAllBtn      = document.getElementById('viewAllBtn');
const viewAllText     = document.getElementById('viewAllText');

const alertModalEl = document.getElementById('alertModal');
const alertModal   = new bootstrap.Modal(alertModalEl);

// ---- Alert modal ----
const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info', okText = 'OK', onClose = null } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  document.getElementById('alertOkBtn').textContent = okText;
  if(onClose) alertModalEl.addEventListener('hidden.bs.modal', onClose, { once: true });
  alertModal.show();
}

// ---- Show / hide password ----
document.querySelectorAll('.pw-toggle').forEach(btn => {
  btn.addEventListener('click', () => {
    const input = document.getElementById(btn.dataset.target);
    const show  = input.type === 'password';
    input.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
    btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
  });
});

// ---- Password rules ----
const COMMON_PASSWORDS = [
  'password', 'password1', 'password123', '12345678', '123456789', '1234567890',
  'qwerty123', 'qwertyuiop', 'iloveyou', 'admin123', 'welcome1', 'abc12345',
  'letmein123', 'lolays123', 'catering123'
];

function checkRules(pw){
  return {
    length: pw.length >= 8,
    mix:    /[A-Za-z]/.test(pw) && /\d/.test(pw),
    common: pw.length > 0 && !COMMON_PASSWORDS.includes(pw.toLowerCase())
  };
}

// 0 = empty, 1 = Weak, 2 = Fair, 3 = Good, 4 = Strong
function getStrength(pw){
  if(!pw) return 0;
  const rules = checkRules(pw);
  let score = 0;
  if(pw.length >= 8)  score++;
  if(pw.length >= 12) score++;
  if(/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score++;
  if(/\d/.test(pw)) score++;
  if(/[^A-Za-z0-9]/.test(pw)) score++;
  if(!rules.common) score = Math.min(score, 1);
  if(!rules.length) score = Math.min(score, 1);
  return Math.max(1, Math.min(4, Math.ceil(score * 4 / 5)));
}

const STRENGTH_LABELS = ['', 'Weak', 'Fair', 'Good', 'Strong'];

function updateStrength(){
  const pw = newPassword.value;
  const rules = checkRules(pw);

  // only show strength + requirements once something is typed
  pwMeta.hidden = pw.length === 0;

  reqItems.forEach(li => li.classList.toggle('ok', !!rules[li.dataset.req]));

  const level = getStrength(pw);
  strengthBars.dataset.level = level;
  strengthText.textContent = STRENGTH_LABELS[level];
  strengthText.className = 'strength-text' + (level ? ` lvl-${level}` : '');
}

newPassword.addEventListener('input', () => {
  updateStrength();
  newError.textContent = '';
  newPassword.classList.remove('is-invalid');
});
currentPassword.addEventListener('input', () => {
  currentError.textContent = '';
  currentPassword.classList.remove('is-invalid');
});
confirmPassword.addEventListener('input', () => {
  confirmError.textContent = '';
  confirmPassword.classList.remove('is-invalid');
});

// ---- Submit ----
function setError(input, el, msg){
  el.textContent = msg;
  input.classList.add('is-invalid');
}

passwordForm.addEventListener('submit', (e) => {
  e.preventDefault();
  let valid = true;

  const current = currentPassword.value;
  const next    = newPassword.value;
  const confirm = confirmPassword.value;
  const rules   = checkRules(next);

  if(!current){
    setError(currentPassword, currentError, 'Please enter your current password.');
    valid = false;
  }
  if(!next){
    setError(newPassword, newError, 'Please enter a new password.');
    valid = false;
  } else if(!rules.length || !rules.mix || !rules.common){
    setError(newPassword, newError, 'Your new password does not meet the requirements.');
    valid = false;
  } else if(current && next === current){
    setError(newPassword, newError, 'New password must be different from the current one.');
    valid = false;
  }
  if(!confirm){
    setError(confirmPassword, confirmError, 'Please re-enter your new password.');
    valid = false;
  } else if(next && confirm !== next){
    setError(confirmPassword, confirmError, 'Passwords do not match.');
    valid = false;
  }
  if(!valid) return;

  // TODO: send { current, next } to your backend and verify the current password there.
  showAlert('Your password has been updated successfully.', { title: 'Password Updated', type: 'success' });

  passwordForm.reset();
  updateStrength();
});

// ---- Recent login activity ----
// Replace with data from your backend.
const LOGIN_ACTIVITY = [
  // example { device: 'Windows 11 - Chrome', deviceType: 'desktop', browser: 'Chrome 129.0', browserKey: 'chrome', location: 'Bacoor, Philippines',  date: 'Oct 20, 2025', time: '9:12 AM',  status: 'current' },
  
];

const VISIBLE_ROWS = 4;
const STATUS_LABELS = { current: 'Current session', success: 'Successful', blocked: 'Blocked' };
const BROWSER_ICONS = { chrome: 'fa-chrome', edge: 'fa-edge', safari: 'fa-safari' };
let showingAll = false;

function esc(str){
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

function renderActivity(){
  const rows = showingAll ? LOGIN_ACTIVITY : LOGIN_ACTIVITY.slice(0, VISIBLE_ROWS);

  activityBody.innerHTML = rows.map(r => `
    <tr>
      <td><span class="cell-icon"><i class="bi ${r.deviceType === 'mobile' ? 'bi-phone' : 'bi-laptop'}"></i> ${esc(r.device)}</span></td>
      <td><span class="cell-icon"><i class="fa-brands ${BROWSER_ICONS[r.browserKey] || 'fa-chrome'}"></i> ${esc(r.browser)}</span></td>
      <td><span class="cell-icon"><i class="bi bi-geo-alt"></i> ${esc(r.location)}</span></td>
      <td>${esc(r.date)} &nbsp; ${esc(r.time)}</td>
      <td><span class="status-pill ${r.status}">${esc(STATUS_LABELS[r.status] || r.status)}</span></td>
    </tr>
  `).join('');

  viewAllText.textContent = showingAll ? 'Show Less' : 'View All';
  viewAllBtn.hidden = LOGIN_ACTIVITY.length <= VISIBLE_ROWS;
}

viewAllBtn.addEventListener('click', () => {
  showingAll = !showingAll;
  renderActivity();
});

// ---- Init ----
renderActivity();
updateStrength();