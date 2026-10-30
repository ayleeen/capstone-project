
const searchInput        = document.getElementById('searchInput');
const userFilter         = document.getElementById('userFilter');
const actionFilter       = document.getElementById('actionFilter');
const tableFilter        = document.getElementById('tableFilter');
const dateFilter         = document.getElementById('dateFilter');
const tableBody          = document.getElementById('auditTableBody');
const noResultsMsg       = document.getElementById('noResultsMsg');
const tableFooter        = document.getElementById('tableFooter');
const paginationControls = document.getElementById('paginationControls');

const logDetailsModalEl  = document.getElementById('logDetailsModal');
const logDetailsModal    = new bootstrap.Modal(logDetailsModalEl);
const logDetailsBody     = document.getElementById('logDetailsBody');
const logModalSubtitle   = document.getElementById('logModalSubtitle');

const alertModalEl = document.getElementById('alertModal');
const alertModal   = new bootstrap.Modal(alertModalEl);

// ---- Pagination ----
const PAGE_SIZE = 10;
let currentPage = 1;

// ---- Lookups ----
const ACTION_LABELS = { create: 'Create', update: 'Update', delete: 'Delete' };


  const logs = window.AUDIT_LOGS || [];

// Newest first
logs.sort((a, b) => new Date(b.timestamp) - new Date(a.timestamp));

// ---- Helpers ----
function esc(value){
  return String(value ?? '').replace(/[&<>"']/g, ch => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[ch]
  ));
}

function formatDateTime(iso){
  const d = new Date(iso);
  if(isNaN(d)) return iso;
  const date = d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
  const time = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
  return `${date} · ${time}`;
}

function actionBadge(action){
  return `<span class="action-badge ${esc(action)}">${esc(ACTION_LABELS[action] || action)}</span>`;
}

function userCell(user){
  return `
    <div class="log-user">
      <img src="${esc(user.avatar)}" alt="">
      <div>
        <div class="log-user-name">${esc(user.name)}</div>
        <div class="log-user-role">${esc(user.role)}</div>
      </div>
    </div>`;
}

// ---- Alert modal (replaces alert()) ----
const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

function showAlert(message, { title = 'Notice', type = 'info' } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;
  alertModal.show();
}

// ---- Filter dropdowns that depend on the data ----
function populateFilters(){
  const users = [...new Set(logs.map(l => l.user.name))].sort();
  userFilter.innerHTML = '<option value="">All Users</option>' +
    users.map(u => `<option value="${esc(u)}">${esc(u)}</option>`).join('');

  const tables = [...new Set(logs.map(l => l.table))].sort();
  tableFilter.innerHTML = '<option value="">All Tables</option>' +
    tables.map(t => `<option value="${esc(t)}">${esc(t)}</option>`).join('');
}

// ---- Table rendering ----
function renderRows(list){
  const totalPages = Math.max(1, Math.ceil(list.length / PAGE_SIZE));
  if(currentPage > totalPages) currentPage = totalPages;
  if(currentPage < 1) currentPage = 1;

  if(list.length === 0){
    tableBody.innerHTML = '';
    paginationControls.innerHTML = '';
    tableFooter.textContent = `Showing 0 of ${logs.length} items`;
    noResultsMsg.textContent = logs.length === 0
      ? 'No audit logs found.'
      : 'No audit logs match your search/filters.';
    noResultsMsg.classList.remove('d-none');
    return;
  }

  noResultsMsg.classList.add('d-none');

  const start = (currentPage - 1) * PAGE_SIZE;
  const pageItems = list.slice(start, start + PAGE_SIZE);
  tableFooter.textContent = `Showing ${start + 1}-${start + pageItems.length} of ${list.length} items`;

  tableBody.innerHTML = pageItems.map((l, i) => `
    <tr data-id="${esc(l.id)}" tabindex="0" role="button" title="Click to view details">
      <td>${start + i + 1}</td>
      <td class="log-date">${esc(formatDateTime(l.timestamp))}</td>
      <td>${userCell(l.user)}</td>
      <td>${actionBadge(l.action)}</td>
      <td>${esc(l.table)}</td>
      <td>${esc(l.recordId)}</td>
      <td class="log-details-cell">${esc(l.details)}</td>
      <td>${esc(l.ip)}</td>
    </tr>
  `).join('');

  tableBody.querySelectorAll('tr').forEach(row => {
    row.addEventListener('click', () => openLogDetails(row.dataset.id));
    row.addEventListener('keydown', (e) => {
      if(e.key === 'Enter' || e.key === ' '){
        e.preventDefault();
        openLogDetails(row.dataset.id);
      }
    });
  });

  renderPagination(totalPages);
}

function goToPage(page){
  currentPage = page;
  applyFilters(false);
}

function renderPagination(totalPages){
  if(totalPages <= 1){
    paginationControls.innerHTML = '';
    return;
  }

  let html = `<button class="page-btn" data-page="prev" ${currentPage === 1 ? 'disabled' : ''} title="Previous"><i class="fa-solid fa-chevron-left"></i></button>`;

  const pages = new Set([1, totalPages]);
  for(let p = currentPage - 1; p <= currentPage + 1; p++){
    if(p > 1 && p < totalPages) pages.add(p);
  }

  let prev = 0;
  [...pages].sort((a, b) => a - b).forEach(p => {
    if(p - prev > 1) html += '<span class="page-ellipsis">&hellip;</span>';
    html += `<button class="page-btn ${p === currentPage ? 'active' : ''}" data-page="${p}">${p}</button>`;
    prev = p;
  });

  html += `<button class="page-btn" data-page="next" ${currentPage === totalPages ? 'disabled' : ''} title="Next"><i class="fa-solid fa-chevron-right"></i></button>`;
  paginationControls.innerHTML = html;

  paginationControls.querySelectorAll('.page-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const p = btn.dataset.page;
      if(p === 'prev') goToPage(currentPage - 1);
      else if(p === 'next') goToPage(currentPage + 1);
      else goToPage(Number(p));
    });
  });
}

// ---- Filtering ----
let lastFilteredList = logs;

function matchesDateRange(iso, range){
  if(!range) return true;
  const d = new Date(iso);
  const now = new Date();
  if(range === 'today'){
    return d.toDateString() === now.toDateString();
  }
  const days = Number(range);
  const cutoff = new Date();
  cutoff.setDate(now.getDate() - days);
  cutoff.setHours(0, 0, 0, 0);
  return d >= cutoff;
}

function applyFilters(resetPage = true){
  const q = searchInput.value.trim().toLowerCase();
  const user = userFilter.value;
  const action = actionFilter.value;
  const table = tableFilter.value;
  const range = dateFilter.value;

  const filtered = logs.filter(l => {
    const haystack = [
      l.user.name, ACTION_LABELS[l.action], l.table, l.recordId, l.details, l.ip, l.id
    ].join(' ').toLowerCase();

    return (!q || haystack.includes(q)) &&
      (!user || l.user.name === user) &&
      (!action || l.action === action) &&
      (!table || l.table === table) &&
      matchesDateRange(l.timestamp, range);
  });

  lastFilteredList = filtered;
  if(resetPage) currentPage = 1;
  renderRows(filtered);
}

searchInput.addEventListener('input', () => applyFilters(true));
[userFilter, actionFilter, tableFilter, dateFilter].forEach(el =>
  el.addEventListener('change', () => applyFilters(true))
);

// ---- Details pop-up ----
function changeCell(value, cls){
  return value === '' || value == null
    ? '<span class="empty-value">—</span>'
    : `<span class="${cls}">${esc(value)}</span>`;
}

function openLogDetails(id){
  const l = logs.find(x => x.id === id);
  if(!l) return;

  logModalSubtitle.textContent = `${l.id} · ${formatDateTime(l.timestamp)}`;

  const changesHtml = (l.changes && l.changes.length) ? `
    <div class="log-section-title">${l.action === 'update' ? 'What Changed' : (l.action === 'create' ? 'New Values' : 'Deleted Values')}</div>
    <table class="changes-table">
      <thead>
        <tr><th>Field</th><th>Previous Value</th><th>New Value</th></tr>
      </thead>
      <tbody>
        ${l.changes.map(c => `
          <tr>
            <td>${esc(c.field)}</td>
            <td>${changeCell(c.old, 'old-value')}</td>
            <td>${changeCell(c.new, 'new-value')}</td>
          </tr>
        `).join('')}
      </tbody>
    </table>
  ` : '';

  logDetailsBody.innerHTML = `
    <div class="log-summary">
      ${actionBadge(l.action)}
      <span class="log-summary-text">${esc(l.table)} &middot; ${esc(l.recordId)}</span>
    </div>

    <div class="log-field-grid">
      <div>
        <div class="log-field-label">Log ID</div>
        <div class="log-field-value">${esc(l.id)}</div>
      </div>
      <div>
        <div class="log-field-label">Date &amp; Time</div>
        <div class="log-field-value">${esc(formatDateTime(l.timestamp))}</div>
      </div>
      <div>
        <div class="log-field-label">IP Address</div>
        <div class="log-field-value">${esc(l.ip)}</div>
      </div>
      <div>
        <div class="log-field-label">User</div>
        <div class="log-field-value">${userCell(l.user)}</div>
      </div>
      <div>
        <div class="log-field-label">Table</div>
        <div class="log-field-value">${esc(l.table)}</div>
      </div>
      <div>
        <div class="log-field-label">Record ID</div>
        <div class="log-field-value">${esc(l.recordId)}</div>
      </div>
    </div>

    <div class="log-section-title">Details</div>
    <div class="log-details-box">${esc(l.details)}</div>

    ${changesHtml}
  `;

  logDetailsModal.show();
}

// ---- Export (CSV of whatever is currently filtered) ----
document.getElementById('exportBtn').addEventListener('click', () => {
  if(lastFilteredList.length === 0){
    showAlert('There are no audit logs to export. Try changing your search or filters.', { title: 'Nothing to Export', type: 'warning' });
    return;
  }

  const header = ['#', 'Date and Time', 'User', 'Action', 'Table', 'Record ID', 'Details', 'IP Address'];
  const rows = lastFilteredList.map((l, i) => [
    i + 1, formatDateTime(l.timestamp), l.user.name, ACTION_LABELS[l.action] || l.action,
    l.table, l.recordId, l.details, l.ip
  ]);

  const csv = [header, ...rows]
    .map(row => row.map(v => `"${String(v).replace(/"/g, '""')}"`).join(','))
    .join('\n');

  const blob = new Blob([csv], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'audit-logs-export.csv';
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);

  showAlert(`${lastFilteredList.length} audit log record${lastFilteredList.length === 1 ? '' : 's'} exported successfully.`, { title: 'Export Complete', type: 'success' });
});

// ---- Init ----
populateFilters();
applyFilters();