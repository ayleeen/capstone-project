
const notifList      = document.getElementById('notifList');
const notifEmpty     = document.getElementById('notifEmpty');
const notifEmptyText = document.getElementById('notifEmptyText');
const notifTabs      = document.getElementById('notifTabs');
const markAllReadBtn = document.getElementById('markAllReadBtn');
const bellDot        = document.getElementById('bellDot');

const notifModalEl = document.getElementById('notifModal');
const notifModal   = new bootstrap.Modal(notifModalEl);
const prefsModalEl = document.getElementById('prefsModal');
const prefsModal   = new bootstrap.Modal(prefsModalEl);
const alertModalEl = document.getElementById('alertModal');
const alertModal   = new bootstrap.Modal(alertModalEl);

// ---- Lookups ----
const TYPE_LABELS = { reservation: 'Reservations', payment: 'Payments', system: 'System' };

const notifications = [
    // ---- Notifications ----
// No sample data: fill this array from your backend. Each notification looks like:
// {
//   id: 'N-001',
//   type: 'reservation',                 // 'reservation' | 'payment' | 'system'
//   icon: 'bi-calendar2-week',           // any Bootstrap Icons class
//   title: 'New Reservation Request',
//   message: 'A new catering reservation is waiting for your review.',
//   timestamp: '2026-10-23T09:30:00',    // any date string JS can parse
//   read: false,
//   link: 'reservation.html',            // optional: button in the details pop-up
//   linkLabel: 'View Reservations'       // optional: label for that button
// }
];

notifications.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

let currentFilter = 'all'; 

// ---- Helpers ----
function esc(value){
  return String(value ?? '').replace(/[&<>"']/g, ch => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]
  ));
}

function plural(n, word){ return `${n} ${word}${n === 1 ? '' : 's'} ago`; }

function relativeTime(iso){
  const d = new Date(iso);
  if(isNaN(d)) return iso;

  const diffMs = Date.now() - d.getTime();
  const mins = Math.floor(diffMs / 60000);
  if(mins < 1) return 'Just now';
  if(mins < 60) return plural(mins, 'minute');

  const hours = Math.floor(mins / 60);
  if(hours < 24) return plural(hours, 'hour');

  const startOf = (x) => new Date(x.getFullYear(), x.getMonth(), x.getDate());
  const days = Math.round((startOf(new Date()) - startOf(d)) / 86400000);
  if(days <= 1) return 'Yesterday';
  if(days < 7) return `${days} days ago`;
  return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}

function fullDateTime(iso){
  const d = new Date(iso);
  if(isNaN(d)) return iso;
  return d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) +
    ' · ' + d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

// ---- Alert modal (replaces alert()) ----
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

// ---- Rendering ----
function unreadCount(){
  return notifications.filter(n => !n.read).length;
}

function updateBell(){
  bellDot.classList.toggle('d-none', unreadCount() === 0);
}

function renderList(){
  const list = currentFilter === 'all'
    ? notifications
    : notifications.filter(n => n.type === currentFilter);

  if(list.length === 0){
    notifList.innerHTML = '';
    notifEmpty.classList.remove('d-none');
    notifEmptyText.textContent = notifications.length === 0
      ? 'No notifications yet.'
      : `No ${TYPE_LABELS[currentFilter].toLowerCase()} notifications.`;
  } else {
    notifEmpty.classList.add('d-none');
    notifList.innerHTML = list.map(n => `
      <div class="notif-item ${n.read ? 'read' : ''}" data-id="${esc(n.id)}" tabindex="0" role="button">
        <span class="notif-icon"><i class="bi ${esc(n.icon)}"></i></span>
        <span class="notif-text">
          <span class="notif-title">${esc(n.title)}</span>
          <span class="notif-desc">${esc(n.message)}</span>
        </span>
        <span class="notif-meta">
          <span class="notif-dot" title="${n.read ? 'Read' : 'Unread'}"></span>
          <span class="notif-sep">|</span>
          <span>${esc(relativeTime(n.timestamp))}</span>
        </span>
      </div>
    `).join('');

    notifList.querySelectorAll('.notif-item').forEach(item => {
      item.addEventListener('click', () => openNotification(item.dataset.id));
      item.addEventListener('keydown', (e) => {
        if(e.key === 'Enter' || e.key === ' '){
          e.preventDefault();
          openNotification(item.dataset.id);
        }
      });
    });
  }

  updateBell();
}

// ---- Tabs ----
notifTabs.querySelectorAll('.notif-tab').forEach(tab => {
  tab.addEventListener('click', () => {
    currentFilter = tab.dataset.filter;
    notifTabs.querySelectorAll('.notif-tab').forEach(t => {
      const active = t === tab;
      t.classList.toggle('active', active);
      t.setAttribute('aria-selected', active ? 'true' : 'false');
    });
    renderList();
  });
});

// ---- Mark all as read ----
markAllReadBtn.addEventListener('click', () => {
  if(unreadCount() === 0){
    showAlert('You have no unread notifications.', { title: 'All Caught Up', type: 'info' });
    return;
  }
  notifications.forEach(n => { n.read = true; });
  renderList();
});

// ---- Details pop-up ----
function openNotification(id){
  const n = notifications.find(x => x.id === id);
  if(!n) return;

  n.read = true; // opening a notification marks it as read
  renderList();

  document.getElementById('notifModalIcon').innerHTML = `<i class="bi ${esc(n.icon)}"></i>`;
  document.getElementById('notifModalTitle').textContent = n.title;

  const badge = document.getElementById('notifModalType');
  badge.textContent = TYPE_LABELS[n.type] || n.type;
  badge.className = `notif-type-badge ${n.type}`;

  document.getElementById('notifModalMessage').textContent = n.message;
  document.getElementById('notifModalTime').textContent = fullDateTime(n.timestamp);

  const link = document.getElementById('notifModalLink');
  if(n.link){
    link.href = n.link;
    link.textContent = n.linkLabel || 'View';
    link.classList.remove('d-none');
  } else {
    link.classList.add('d-none');
  }

  notifModal.show();
}

// ---- Preferences ----
const PREFS_KEY = 'lolays.notificationPrefs';
const prefInputs = {
  reservation: document.getElementById('prefReservation'),
  payment:     document.getElementById('prefPayment'),
  system:      document.getElementById('prefSystem')
};

function loadPrefs(){
  const defaults = { reservation: true, payment: true, system: true };
  try {
    return { ...defaults, ...JSON.parse(localStorage.getItem(PREFS_KEY) || '{}') };
  } catch (err) {
    return defaults;
  }
}

document.getElementById('prefsBtn').addEventListener('click', () => {
  const prefs = loadPrefs();
  Object.keys(prefInputs).forEach(k => { prefInputs[k].checked = !!prefs[k]; });
  prefsModal.show();
});

document.getElementById('prefsForm').addEventListener('submit', (e) => {
  e.preventDefault();

  const prefs = {};
  Object.keys(prefInputs).forEach(k => { prefs[k] = prefInputs[k].checked; });

  let saved = true;
  try {
    localStorage.setItem(PREFS_KEY, JSON.stringify(prefs));
    // TODO: also send `prefs` to your backend so it knows what to notify about.
  } catch (err) {
    saved = false;
  }

  // Close the form first, then show the result
  prefsModalEl.addEventListener('hidden.bs.modal', () => {
    if(saved){
      showAlert('Your notification preferences have been saved.', { title: 'Preferences Saved', type: 'success' });
    } else {
      showAlert('Your preferences couldn\'t be stored in this browser.', { title: 'Could Not Save', type: 'danger' });
    }
  }, { once: true });
  prefsModal.hide();
});

// ---- Init ----
renderList();
setInterval(() => {
  // keep "5 minutes ago" etc. fresh, but don't steal focus from keyboard users
  if(!notifList.contains(document.activeElement)) renderList();
}, 60000);