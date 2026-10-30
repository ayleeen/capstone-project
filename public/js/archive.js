const peso = n => '₱' + Number(n).toLocaleString();
const dDate = s => new Date(s).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
const dTime = s => new Date(s).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
const dateCell = s => `<div>${dDate(s)}</div><div class="sub">${dTime(s)}</div>`;
const label = v => String(v).charAt(0).toUpperCase() + String(v).slice(1);
const pill = (label, cls = '') => `<span class="pill ${cls}">${label}</span>`;
const thumb = seed => `<img class="thumb" src="https://picsum.photos/seed/${seed}/72/56" alt="" loading="lazy">`;


const DATA = window.ARCHIVE_DATA;



const TABS = {
  reservations: {
    title: 'Archive Reservation',
    cols: [
      ['Reference No.', i => i.ref],
      ['Event Name', i => i.name],
      ['Client Name', i => i.client],
      ['Event Type', i => pill(label(i.category))],
      ['Date', i => dDate(i.date)],
      ['Guests', i => i.guests],
      ['Package', i => i.pkg],
      ['Total Amount', i => peso(i.total)],
      ['Archive On', i => dateCell(i.archivedOn)]
    ]
  },
packages: {
  title: 'Archive Packages',
  cols: [
    ['Package ID', i => i.id],
    ['Package Name', i => i.name],
    ['Event Type', i => pill(label(i.category))],
    ['Guests', i => i.guests],
    ['Price', i => peso(i.price)],
    ['Status', i => pill(label(i.status))],
    ['Archive On', i => dateCell(i.archivedOn)]
  ]
},
menu: {
  title: 'Archive Menu',
  cols: [
    ['Item ID', i => i.id],
    ['Menu Item', i => i.name],
    ['Category', i => pill(label(i.category))],
    ['Price', i => peso(i.price)],
    ['Status', i => pill(label(i.status))],
    ['Archive On', i => dateCell(i.archivedOn)]
  ]
},
customers: {
    title: 'Archive Customers',
    cols: [
        ['Customer ID', i => i.id],
        ['Customer Name', i => i.name],
        ['Email', i => i.email || '—'],
        ['Phone', i => i.phone || '—'],
        ['Status', i => pill(label(i.status || 'inactive'))],
        ['Archived On', i => dateCell(i.archivedOn)]
    ]
}

};

// ---- State + refs ----
let tab = 'reservations';
const selected = new Set();
let pending = null; // { action: 'restore' | 'delete', ids: [] }

const $ = id => document.getElementById(id);
const searchInput = $('searchInput'), categoryFilter = $('categoryFilter'), statusFilter = $('statusFilter'), sortFilter = $('sortFilter');
const tableHead = $('tableHead'), tableBody = $('tableBody'), emptyMsg = $('emptyMsg'), tableFooter = $('tableFooter');
const bulkBar = $('bulkBar'), bulkCount = $('bulkCount');
const confirmModal = new bootstrap.Modal($('confirmModal'));

function fillSelect(el, allLabel, options) {
  el.innerHTML = `<option value="">${allLabel}</option>` +
    Object.entries(options).map(([v, l]) => `<option value="${v}">${l}</option>`).join('');
}

function refreshFilters() {
  const keep = [categoryFilter.value, statusFilter.value];
  const optionsFor = key => Object.fromEntries([...new Set(DATA[tab].map(i => i[key]))].map(v => [v, label(v)]));
  fillSelect(categoryFilter, 'All Categories', optionsFor('category'));
  fillSelect(statusFilter, 'All Status', optionsFor('status'));
  categoryFilter.value = keep[0];
  statusFilter.value = keep[1];
}

function visibleItems() {
  const q = searchInput.value.trim().toLowerCase();
  const items = DATA[tab].filter(i =>
    (!q || Object.values(i).join(' ').toLowerCase().includes(q)) &&
    (!categoryFilter.value || i.category === categoryFilter.value) &&
    (!statusFilter.value || i.status === statusFilter.value));
  const sort = sortFilter.value;
  return items.sort((a, b) =>
    sort === 'name' ? a.name.localeCompare(b.name)
      : sort === 'oldest' ? a.archivedOn.localeCompare(b.archivedOn)
      : b.archivedOn.localeCompare(a.archivedOn));
}

// ---- Render ----
function render() {
  const cfg = TABS[tab];
  const items = visibleItems();

  Object.keys(DATA).forEach(k => {
  const el = $('count-' + k);
  if (el) el.textContent = DATA[k].length;
});
  $('tableTitle').textContent = cfg.title;

  tableHead.innerHTML = `<tr><th><input type="checkbox" id="selectAll" aria-label="Select all"></th>` +
    cfg.cols.map(c => `<th>${c[0]}</th>`).join('') + `<th>Action</th></tr>`;

  tableBody.innerHTML = items.map(i => `
    <tr>
      <td><input type="checkbox" class="row-check" data-id="${i.id}" ${selected.has(i.id) ? 'checked' : ''} aria-label="Select ${i.id}"></td>
      ${cfg.cols.map(c => `<td>${c[1](i)}</td>`).join('')}
      <td><div class="action-cell">
        <button class="btn-restore" data-action="restore" data-id="${i.id}">Restore</button>
        <button class="btn-delete" data-action="delete" data-id="${i.id}">Delete</button>
      </div></td>
    </tr>`).join('');

  emptyMsg.classList.toggle('d-none', items.length > 0);
  tableFooter.textContent = items.length ? `Showing ${items.length} of ${DATA[tab].length} items` : '';

  const all = $('selectAll');
  const checkedHere = items.filter(i => selected.has(i.id)).length;
  all.checked = items.length > 0 && checkedHere === items.length;
  all.indeterminate = checkedHere > 0 && checkedHere < items.length;
  all.addEventListener('change', () => {
    items.forEach(i => all.checked ? selected.add(i.id) : selected.delete(i.id));
    render();
  });

  bulkBar.hidden = selected.size === 0;
  bulkCount.textContent = `${selected.size} selected`;
}

function switchTab(next) {
  tab = next;
  selected.clear();
  document.querySelectorAll('.archive-card').forEach(c => c.classList.toggle('active', c.dataset.tab === tab));
  refreshFilters();
  searchInput.value = '';
  searchInput.placeholder = {
    reservations: 'Search reservation...',
    packages: 'Search package...',
    menu: 'Search menu item...',
    customers: 'Search customer...'
}[tab];
  render();
}

// ---- Confirm flow ----
function ask(action, ids) {
  pending = { action, ids };
  const many = ids.length > 1;
  const noun = many ? `these ${ids.length} records` : 'this record';
  const isDelete = action === 'delete';
  $('confirmIcon').className = 'confirm-icon ' + (isDelete ? 'red' : 'green');
  $('confirmIcon').innerHTML = `<i class="bi ${isDelete ? 'bi-trash' : 'bi-arrow-counterclockwise'}"></i>`;
  $('confirmTitle').textContent = isDelete ? 'Delete permanently?' : 'Restore from archive?';
  $('confirmText').textContent = isDelete
    ? `Permanently delete ${noun}? This can't be undone.`
    : `Restore ${noun}? It will move back to its original list.`;
  const btn = $('confirmBtn');
  btn.className = isDelete ? 'btn-confirm-red' : 'btn-confirm-green';
  btn.textContent = isDelete ? 'Delete' : 'Restore';
  confirmModal.show();
}

function toast(msg) {
  const t = $('toastMsg');
  t.textContent = msg;
  t.classList.add('show');
  clearTimeout(toast.timer);
  toast.timer = setTimeout(() => t.classList.remove('show'), 2500);
}

$('confirmBtn').addEventListener('click', async () => {
  if (!pending) return;
  const { action, ids } = pending;

  try {
    const res = await fetch(`${window.ARCHIVE_URL}/${tab}/${action}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ ids: ids.map(Number) })
    });
    if (!res.ok) throw new Error();
  } catch {
    confirmModal.hide();
    return toast('Something went wrong. Please try again.');
  }

  DATA[tab] = DATA[tab].filter(i => !ids.includes(i.id));
  ids.forEach(id => selected.delete(id));
  pending = null;
  confirmModal.hide();
  refreshFilters();
  render();
  toast(action === 'delete'
    ? `Deleted ${ids.length} record${ids.length > 1 ? 's' : ''}`
    : `Restored ${ids.length} record${ids.length > 1 ? 's' : ''}`);
});

// ---- Events ----
document.querySelectorAll('.archive-card').forEach(c => c.addEventListener('click', () => switchTab(c.dataset.tab)));
[searchInput, categoryFilter, statusFilter, sortFilter].forEach(el => {
  el.addEventListener(el === searchInput ? 'input' : 'change', render);
});

tableBody.addEventListener('click', e => {
  const btn = e.target.closest('button[data-action]');
  if (btn) return ask(btn.dataset.action, [btn.dataset.id]);
  const check = e.target.closest('.row-check');
  if (check) {
    check.checked ? selected.add(check.dataset.id) : selected.delete(check.dataset.id);
    render();
  }
});

$('bulkRestore').addEventListener('click', () => ask('restore', [...selected]));
$('bulkDelete').addEventListener('click', () => ask('delete', [...selected]));

switchTab('reservations');