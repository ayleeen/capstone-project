
const customers = [];

let selectedId = null;

const tableBody      = document.getElementById('tableBody');
const detailPanel     = document.getElementById('detailPanel');
const searchInput     = document.getElementById('searchInput');
const categoryFilter  = document.getElementById('categoryFilter');
const statusFilter    = document.getElementById('statusFilter');
const sortFilter       = document.getElementById('sortFilter');
const addCustomerBtn    = document.getElementById('addCustomerBtn');

const statTotalCustomers  = document.getElementById('statTotalCustomers');
const statNewCustomers     = document.getElementById('statNewCustomers');
const statRepeatCustomers   = document.getElementById('statRepeatCustomers');
const statTotalBookings      = document.getElementById('statTotalBookings');
const statTotalSales           = document.getElementById('statTotalSales');

function fmtMoney(n){
  return '$' + Number(n || 0).toLocaleString();
}

function renderStats(){
  statTotalCustomers.textContent = customers.length;
  statNewCustomers.textContent = customers.filter(c => c.isNew).length;
  statRepeatCustomers.textContent = customers.filter(c => c.isRepeat).length;
  statTotalBookings.textContent = customers.reduce((sum, c) => sum + (c.totalBookings || 0), 0);
  statTotalSales.textContent = fmtMoney(customers.reduce((sum, c) => sum + (c.totalSpent || 0), 0));
}

function getFilteredList(){
  let list = customers.slice();

  const q = (searchInput?.value || '').trim().toLowerCase();
  if(q){
    list = list.filter(c =>
      c.name.toLowerCase().includes(q) ||
      (c.email || '').toLowerCase().includes(q) ||
      (c.id || '').toLowerCase().includes(q)
    );
  }

  const cat = categoryFilter?.value;
  if(cat && cat !== 'All Categories'){
    list = list.filter(c => c.customerType === cat);
  }

  const status = statusFilter?.value;
  if(status && status !== 'All Status'){
    list = list.filter(c => c.status === status);
  }

  switch(sortFilter?.value){
    case 'Oldest First': list.sort((a,b) => (a.lastActivity || '').localeCompare(b.lastActivity || '')); break;
    case 'Newest First': list.sort((a,b) => (b.lastActivity || '').localeCompare(a.lastActivity || '')); break;
    case 'Spent: High to Low': list.sort((a,b) => (b.totalSpent||0) - (a.totalSpent||0)); break;
    case 'Spent: Low to High': list.sort((a,b) => (a.totalSpent||0) - (b.totalSpent||0)); break;
  }

  return list;
}

function renderTable(){
  const list = getFilteredList();

  if(list.length === 0){
    tableBody.innerHTML = `
      <tr>
        <td colspan="9">
          <div class="empty-state">
            <i class="bi bi-people"></i>
            <div>No customers found</div>
          </div>
        </td>
      </tr>
    `;
    return;
  }

  tableBody.innerHTML = list.map(c => `
    <tr data-id="${c.id}" class="${c.id === selectedId ? 'table-row-selected' : ''}">
      <td>${c.id}</td>
      <td>
        <div class="cust-cell">
          <img class="cust-avatar" src="${c.avatar || ''}" alt="${c.name}">
          <div class="cust-name">${c.name}</div>
        </div>
      </td>
      <td>
        <div class="cust-contact-phone">${c.phone || ''}</div>
      </td>
      <td><span class="badge-cust-type">${c.customerType || ''}</span></td>
      <td>${c.totalBookings || 0}</td>
      <td>${fmtMoney(c.totalSpent)}</td>
      <td>${c.lastActivity || ''}</td>
      <td><span class="badge rounded-pill badge-status">${c.status || ''}</span></td>
      <td>
        <div class="d-flex align-items-center gap-1">
          <button class="action-icon-btn btn p-0" title="View" data-action="view"><i class="bi bi-eye"></i></button>
          <button class="action-icon-btn btn p-0" title="Edit" data-action="edit"><i class="bi bi-pencil"></i></button>
          <button class="action-icon-btn btn p-0" title="More" data-action="more"><i class="bi bi-three-dots-vertical"></i></button>
        </div>
      </td>
    </tr>
  `).join('');

  document.querySelectorAll('#tableBody tr[data-id]').forEach(row => {
    row.addEventListener('click', (e) => {
      if(e.target.closest('.action-icon-btn')) return;
      selectedId = row.dataset.id;
      renderTable();
      renderDetail();
    });
  });
}

function renderDetail(){
  const c = customers.find(x => x.id === selectedId);

  if(!c){
    detailPanel.innerHTML = `
      <div class="empty-state">
        <i class="bi bi-person"></i>
        <div>Select a customer to view details</div>
      </div>
    `;
    return;
  }

  const avatarHtml = c.avatar
    ? `<img class="cust-detail-avatar" src="${c.avatar}" alt="${c.name}">`
    : `<div class="cust-detail-avatar-empty"><i class="bi bi-person-fill"></i></div>`;

  const bookingsHtml = (c.bookings && c.bookings.length)
    ? c.bookings.slice(0, 3).map(b => `
        <div class="cust-booking-row">
          <div class="cust-booking-date">${b.date || ''}</div>
          <div class="cust-booking-name">${b.name || ''}</div>
          <div class="cust-booking-status">${b.status || ''}</div>
          <div class="cust-booking-price">${fmtMoney(b.price)}</div>
        </div>
      `).join('')
    : `<div class="empty-state" style="padding:20px 0;"><i class="bi bi-journal-x"></i><div>No bookings yet</div></div>`;

  const notesHtml = (c.notes && c.notes.length)
    ? c.notes.map(n => `
        <div class="cust-notes-box mb-2">
          ${n.text}
          <div class="cust-notes-meta">Added by ${n.author || 'Admin User'} • ${n.date || ''}</div>
        </div>
      `).join('')
    : `<div class="empty-state" style="padding:20px 0;"><i class="bi bi-sticky"></i><div>No notes yet</div></div>`;

  detailPanel.innerHTML = `
    <div class="cust-detail-header">
      ${avatarHtml}
      <div class="cust-detail-name">${c.name}</div>
      <span class="cust-detail-status">${c.status || ''}</span>
      <div class="cust-detail-meta">
        ${c.email ? `<div><a href="mailto:${c.email}">${c.email}</a></div>` : ''}
        ${c.phone ? `<div>${c.phone}</div>` : ''}
        ${c.address ? `<div>${c.address}</div>` : ''}
      </div>
    </div>

    <div class="cust-section-title">Customer Overview</div>
    <div class="cust-overview-row">
      <div class="cust-overview-box">
        <div class="cust-overview-value">${c.totalBookings || 0}</div>
        <div class="cust-overview-label">Total Bookings</div>
      </div>
      <div class="cust-overview-box">
        <div class="cust-overview-value">${fmtMoney(c.totalSpent)}</div>
        <div class="cust-overview-label">Total Spent</div>
      </div>
      <div class="cust-overview-box">
        <div class="cust-overview-value">${fmtMoney(c.avgPerBooking)}</div>
        <div class="cust-overview-label">Avg per Booking</div>
      </div>
    </div>

    <div class="cust-type-row">
      <div class="cust-section-title mb-1">Customer Type</div>
      <span class="badge-cust-type">${c.customerType || ''}</span>
      <div class="cust-type-desc">Loyal customer with multiple bookings.</div>
    </div>

    <div class="cust-bookings-header">
      <div class="cust-section-title mb-0">Last 3 Bookings</div>
      <a href="#" class="cust-bookings-link">View all</a>
    </div>
    <div class="mb-3">
      ${bookingsHtml}
    </div>

    <div class="cust-section-title">Notes</div>
    ${notesHtml}
  `;
}

searchInput?.addEventListener('input', renderTable);
categoryFilter?.addEventListener('change', renderTable);
statusFilter?.addEventListener('change', renderTable);
sortFilter?.addEventListener('change', renderTable);

addCustomerBtn?.addEventListener('click', () => {
  alert('Add Customer — hook this up to your create-customer flow.');
});

renderStats();
renderTable();
renderDetail();