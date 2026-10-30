const searchInput      = document.getElementById('searchInput');
const typeFilter       = document.getElementById('typeFilter');
const statusFilter     = document.getElementById('statusFilter');
const sortFilter       = document.getElementById('sortFilter');
const tableBody        = document.getElementById('customerTableBody');
const noResultsMsg     = document.getElementById('noResultsMsg');
const tableFooter      = document.getElementById('tableFooter');
const addCustomerBtn   = document.getElementById('addCustomerBtn');

const statTotal    = document.getElementById('statTotal');
const statNew      = document.getElementById('statNew');
const statRepeat   = document.getElementById('statRepeat');
const statBookings = document.getElementById('statBookings');
const statSales    = document.getElementById('statSales');

// ---- Details pop-up refs ----
const detailsModalEl         = document.getElementById('detailsModal');
const detailsModal           = new bootstrap.Modal(detailsModalEl);
const detailsPanelBody       = document.getElementById('detailsPanelBody');
const detailsHeaderEditBtn   = document.getElementById('detailsHeaderEditBtn');
const detailsHeaderDeleteBtn = document.getElementById('detailsHeaderDeleteBtn');

const CUSTOMER_TYPE_LABELS = {
  regular: 'Regular',
  vip: 'VIP',
  new: 'New'
};

const CONTACT_METHOD_LABELS = {
  email: 'Email',
  phone: 'Phone',
  sms: 'SMS'
};

const customers = window.CUSTOMERS || [];
const CUSTOMER_BASE = window.CUSTOMER_BASE;
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

async function api(method, url, body){
  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': CSRF
    },
    body: body ? JSON.stringify(body) : undefined
  });
  const data = await res.json().catch(() => ({}));
  if(!res.ok){
    const msg = data.errors ? Object.values(data.errors)[0][0] : (data.message || 'Something went wrong.');
    throw new Error(msg);
  }
  return data;
}

function toPayload(c){
  return {
    name: c.name, email: c.email, phone: c.phone, dob: c.dob || null,
    address: c.address, city: c.city, province: c.province, zip: c.zip,
    customer_type: c.customerType, preferred_contact_method: c.preferredContactMethod,
    status: c.status, notes: c.notes || []
  };
}

let customerCounter = customers.length;
let lastFilteredList = customers;

// When Edit/Delete is opened from inside the details pop-up, we remember the
// customer here so the details pop-up can come back when that modal closes.
let reopenDetailsId = null;

// Edit-modal flow flags
let editSavedName = null;          // set on save so a success alert shows after the modal closes
let switchingToDelete = false;     // Edit is closing only to show the delete confirmation
let reopenEditAfterDelete = false; // delete was started from Edit, so Cancel returns to Edit

// ---- Helpers ----
function fmtMoney(n){
  return '$' + Number(n || 0).toLocaleString();
}

function formatDateDisplay(dateStr){
  if(!dateStr) return '';
  const d = new Date(dateStr);
  if(isNaN(d)) return dateStr;
  return d.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
}

function generateCustomerId(){
  customerCounter += 1;
  return `CUS-2026-${String(customerCounter).padStart(3, '0')}`;
}

function avatarUrlFor(c){
  // Deterministic placeholder avatar. Swap for c.avatar once your backend
  // provides a real customer photo.
  return (c.avatar && c.avatar !== 'https://...') ? c.avatar : `https://i.pravatar.cc/100?u=${encodeURIComponent(c.id)}`;
}

// Bootstrap can't stack two modals. This hides one modal, waits for it to
// finish closing, then runs fn (usually "show the next modal").
function hideThen(instance, el, fn){
  if(el.classList.contains('show')){
    el.addEventListener('hidden.bs.modal', fn, { once: true });
    instance.hide();
  } else {
    fn();
  }
}

// ---- Alert modal (replaces alert()) ----
const alertModalEl = document.getElementById('alertModal');
const alertModal   = new bootstrap.Modal(alertModalEl);

const ALERT_TYPES = {
  info:    { cls: 'info',    icon: 'fa-circle-info' },
  warning: { cls: 'warning', icon: 'fa-triangle-exclamation' },
  success: { cls: 'success', icon: 'fa-circle-check' },
  danger:  { cls: 'danger',  icon: 'fa-circle-xmark' }
};

/**
 * showAlert('Message', { title: 'Title', type: 'info' | 'warning' | 'success' | 'danger' })
 * If another modal is open, it is closed first and re-opened after the alert is dismissed.
 */
function showAlert(message, { title = 'Notice', type = 'info' } = {}){
  const cfg = ALERT_TYPES[type] || ALERT_TYPES.info;
  document.getElementById('alertIcon').className = `confirm-icon-circle ${cfg.cls}`;
  document.getElementById('alertIconGlyph').className = `fa-solid ${cfg.icon}`;
  document.getElementById('alertTitle').textContent = title;
  document.getElementById('alertMessage').textContent = message;

  const openEl = document.querySelector('.modal.show:not(#alertModal)');
  if(openEl){
    const openInstance = bootstrap.Modal.getInstance(openEl);
    openEl.addEventListener('hidden.bs.modal', () => {
      alertModalEl.addEventListener('hidden.bs.modal', () => openInstance.show(), { once: true });
      alertModal.show();
    }, { once: true });
    openInstance.hide();
  } else {
    alertModal.show();
  }
}

// ---- Stat cards ----
function renderStats(){
  statTotal.textContent = customers.length;
  statNew.textContent = customers.filter(c => c.isNew).length;
  statRepeat.textContent = customers.filter(c => c.isRepeat).length;
  statBookings.textContent = customers.reduce((sum, c) => sum + (c.totalBookings || 0), 0);
  statSales.textContent = fmtMoney(customers.reduce((sum, c) => sum + (c.totalSpent || 0), 0));
}

// ---- Table rendering ----
function renderRows(list){
  tableFooter.textContent = `Showing ${list.length} of ${customers.length} items`;

  if(list.length === 0){
    tableBody.innerHTML = '';
    noResultsMsg.textContent = customers.length === 0
      ? 'No customers found.'
      : 'No customers match your search/filters.';
    noResultsMsg.classList.remove('d-none');
    renderStats();
    return;
  }

  noResultsMsg.classList.add('d-none');

  tableBody.innerHTML = list.map(c => `
    <tr data-id="${c.id}">
      <td>${c.id}</td>
      <td>
        <div class="d-flex align-items-center gap-2">
          <img src="${avatarUrlFor(c)}" alt="${c.name}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
          <span style="font-weight:600;">${c.name}</span>
        </div>
      </td>
      <td>${c.phone || ''}</td>
      <td><span class="status-pill confirmed">${CUSTOMER_TYPE_LABELS[c.customerType] || c.customerType}</span></td>
      <td>${c.totalBookings || 0}</td>
      <td>${fmtMoney(c.totalSpent)}</td>
      <td>${formatDateDisplay(c.lastActivity)}</td>
      <td><span class="status-pill ${c.status === 'active' ? 'confirmed' : 'cancelled'}">${c.status === 'active' ? 'Active' : 'Inactive'}</span></td>
      <td>
        <div class="action-icons">
          <button type="button" class="view-btn" data-id="${c.id}" title="View"><i class="bi bi-eye"></i></button>
          <button type="button" class="edit-btn" data-id="${c.id}" title="Edit"><i class="bi bi-pencil"></i></button>
          <button type="button" class="delete-customer-btn" data-id="${c.id}" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </td> 
    </tr>
  `).join('');

  attachRowActionHandlers();
  renderStats();
}

// ---- Filtering ----
function applyFilters(){
  const q = searchInput.value.trim().toLowerCase();
  const type = typeFilter.value;
  const status = statusFilter.value;

  let filtered = customers.filter(c => {
    const matchesSearch = !q ||
      c.name.toLowerCase().includes(q) ||
      (c.email || '').toLowerCase().includes(q) ||
      c.id.toLowerCase().includes(q);
    const matchesType = !type || c.customerType === type;
    const matchesStatus = !status || c.status === status;
    return matchesSearch && matchesType && matchesStatus;
  });

  switch(sortFilter.value){
    case 'oldest': filtered = filtered.slice().reverse(); break;
    case 'spent-high': filtered = filtered.slice().sort((a,b) => (b.totalSpent||0) - (a.totalSpent||0)); break;
    case 'spent-low': filtered = filtered.slice().sort((a,b) => (a.totalSpent||0) - (b.totalSpent||0)); break;
  }

  lastFilteredList = filtered;
  renderRows(filtered);
}

searchInput.addEventListener('input', applyFilters);
typeFilter.addEventListener('change', applyFilters);
statusFilter.addEventListener('change', applyFilters);
sortFilter.addEventListener('change', applyFilters);

// ---- Row action wiring ----
function attachRowActionHandlers(){
  document.querySelectorAll('.view-btn, .view-details-btn').forEach(btn => {
    btn.addEventListener('click', () => openDetailsPanel(btn.dataset.id));
  });

  document.querySelectorAll('.edit-btn, .edit-customer-btn').forEach(btn => {
    btn.addEventListener('click', () => openEditCustomerModal(btn.dataset.id));
  });

  document.querySelectorAll('.new-reservation-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const c = customers.find(x => x.id === btn.dataset.id);
      showAlert(
        `Creating a new reservation for ${c?.name || 'this customer'} — hook this up to reservation.html (e.g. reservation.html?customerId=${btn.dataset.id}).`,
        { title: 'New Reservation', type: 'info' }
      );
    });
  });

  document.querySelectorAll('.delete-customer-btn').forEach(btn => {
    btn.addEventListener('click', () => openDeleteConfirm(btn.dataset.id));
  });

  document.querySelectorAll('.more-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const menu = btn.closest('.action-menu-wrap').querySelector('.action-menu');
      const wasOpen = menu.classList.contains('open');
      closeAllActionMenus();
      if(!wasOpen) menu.classList.add('open');
    });
  });
}

function closeAllActionMenus(){
  document.querySelectorAll('.action-menu.open').forEach(menu => menu.classList.remove('open'));
}
document.addEventListener('click', closeAllActionMenus);

// ---- Customer Details pop-up ----
let isAddingNote = false;

function renderDetailsPanel(c){
  const bookingsHtml = (c.bookings && c.bookings.length)
    ? c.bookings.slice(0, 3).map(b => `
        <div class="cust-booking-row">
          <div class="cust-booking-date">${formatDateDisplay(b.date)}</div>
          <div class="cust-booking-name">${b.name || ''}</div>
          <div class="cust-booking-status">${b.status || ''}</div>
          <div class="cust-booking-price">${fmtMoney(b.price)}</div>
        </div>
      `).join('')
    : `<div class="no-results" style="padding:16px 0;">No bookings yet</div>`;

  const notesHtml = (c.notes && c.notes.length)
    ? c.notes.map(n => `
        <div class="cust-note-item">
          <div class="cust-note-icon"><i class="fa-solid fa-note-sticky"></i></div>
          <div>
            <div class="cust-note-text">${n.text}</div>
            <div class="cust-note-meta">Added by ${n.author || 'Admin User'} &middot; ${formatDateDisplay(n.date)}</div>
          </div>
        </div>
      `).join('')
    : `<div class="no-results" style="padding:16px 0;">No notes yet</div>`;

  const addNoteFormHtml = isAddingNote ? `
    <div class="cust-add-note-box">
      <textarea class="form-control" id="newNoteText" rows="2" placeholder="Write a note about this customer..."></textarea>
      <div class="cust-add-note-actions">
        <button type="button" class="cust-note-cancel-btn" id="cancelAddNoteBtn" title="Cancel"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" class="cust-note-done-btn" id="doneAddNoteBtn">Done</button>
      </div>
    </div>
  ` : '';

  detailsPanelBody.innerHTML = `
    <div class="cust-identity-row">
      <img class="cust-detail-avatar" src="${avatarUrlFor(c)}" alt="${c.name}">
      <div class="cust-identity-text">
        <div class="cust-detail-name">${c.name}</div>
        <span class="cust-detail-status ${c.status === 'active' ? '' : 'inactive'}">${c.status === 'active' ? 'Active' : 'Inactive'}</span>
      </div>
    </div>

    <div class="cust-contact-list">
      ${c.email ? `<div class="cust-contact-row"><i class="fa-regular fa-envelope"></i> <a href="mailto:${c.email}">${c.email}</a></div>` : ''}
      ${c.phone ? `<div class="cust-contact-row"><i class="fa-solid fa-phone"></i> ${c.phone}</div>` : ''}
      ${c.address ? `<div class="cust-contact-row"><i class="fa-solid fa-location-dot"></i> ${c.address}</div>` : ''}
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
        <div class="cust-overview-label">Average per Booking</div>
      </div>
    </div>

    <div class="cust-type-row">
      <div class="cust-section-title">Customer Type</div>
      <span class="status-pill confirmed">${CUSTOMER_TYPE_LABELS[c.customerType] || c.customerType}</span>
    </div>
    <div class="cust-type-desc">${c.isRepeat ? 'Loyal customer with multiple bookings.' : 'New to booking with us.'}</div>

    <div class="cust-section-header-row">
      <div class="cust-section-title">Last 3 Bookings</div>
      <button type="button" class="cust-pill-link" id="viewAllBookingsBtn">View All</button>
    </div>
    <div class="mb-3">
      ${bookingsHtml}
    </div>

    <div class="cust-section-header-row">
      <div class="cust-section-title">Notes</div>
      ${isAddingNote ? '' : '<button type="button" class="cust-pill-link" id="addNoteBtn">Add Note</button>'}
    </div>
    ${addNoteFormHtml}
    <div class="mb-1">${notesHtml}</div>
  `;

  document.getElementById('viewAllBookingsBtn').addEventListener('click', () => {
    showAlert(
      `Viewing all bookings for ${c.name} — hook this up to a filtered view of reservation.html (e.g. reservation.html?customerId=${c.id}).`,
      { title: 'View All Bookings', type: 'info' }
    );
  });

  if(isAddingNote){
    const textarea = document.getElementById('newNoteText');
    textarea.focus();

    document.getElementById('doneAddNoteBtn').addEventListener('click', () => {
      const text = textarea.value.trim();
      if(text){
        c.notes = c.notes || [];
        c.notes.unshift({ text, author: 'Admin User', date: new Date().toISOString().slice(0, 10) });
      }
      isAddingNote = false;
      renderDetailsPanel(c);
    });

    document.getElementById('cancelAddNoteBtn').addEventListener('click', () => {
      isAddingNote = false;
      renderDetailsPanel(c);
    });
  } else {
    document.getElementById('addNoteBtn').addEventListener('click', () => {
      isAddingNote = true;
      renderDetailsPanel(c);
    });
  }
}

function openDetailsPanel(id){
  const c = customers.find(x => x.id === id);
  if(!c) return;
  isAddingNote = false;
  detailsHeaderEditBtn.onclick = () => openEditCustomerModal(id);
  detailsHeaderDeleteBtn.onclick = () => openDeleteConfirm(id);
  renderDetailsPanel(c);
  detailsModal.show();
}

// ---- Edit Customer modal ----
const editCustomerModalEl = document.getElementById('editCustomerModal');
const editCustomerModal = editCustomerModalEl ? new bootstrap.Modal(editCustomerModalEl) : null;
const editCustomerForm = document.getElementById('editCustomerForm');
let editingCustomerId = null;

function openEditCustomerModal(id){
  const c = customers.find(x => x.id === id);
  if(!c) return;

  editingCustomerId = id;
  // If we were opened from the details pop-up, come back to it afterwards
  reopenDetailsId = detailsModalEl.classList.contains('show') ? id : null;

  document.getElementById('eFullName').value = c.name || '';
  document.getElementById('eEmail').value = c.email || '';
  document.getElementById('ePhone').value = c.phone || '';
  document.getElementById('eDob').value = c.dob || '';
  document.getElementById('eAddress').value = c.address || '';
  document.getElementById('eCity').value = c.city || '';
  document.getElementById('eProvince').value = c.province || '';
  document.getElementById('eZip').value = c.zip || '';
  document.getElementById('eCustomerType').value = CUSTOMER_TYPE_LABELS[c.customerType] || c.customerType || '';
  document.getElementById('ePreferredContactMethod').value = CONTACT_METHOD_LABELS[c.preferredContactMethod] || c.preferredContactMethod || '';
  document.getElementById('eNotesText').value = '';

  hideThen(detailsModal, detailsModalEl, () => editCustomerModal?.show());
}

editCustomerForm?.addEventListener('submit', (e) => {
  e.preventDefault();

  const c = customers.find(x => x.id === editingCustomerId);
  if(!c) return;

  c.name = document.getElementById('eFullName').value.trim();
  c.email = document.getElementById('eEmail').value.trim();
  c.phone = document.getElementById('ePhone').value.trim();
  c.dob = document.getElementById('eDob').value;
  c.address = document.getElementById('eAddress').value.trim();
  c.city = document.getElementById('eCity').value.trim();
  c.province = document.getElementById('eProvince').value.trim();
  c.zip = document.getElementById('eZip').value.trim();
  c.customerType = document.getElementById('eCustomerType').value.trim();
  c.preferredContactMethod = document.getElementById('ePreferredContactMethod').value.trim();

  const noteText = document.getElementById('eNotesText').value.trim();
  if(noteText){
    c.notes = c.notes || [];
    c.notes.unshift({ text: noteText, author: 'Admin User', date: new Date().toISOString().slice(0, 10) });
  }

  applyFilters();
  editSavedName = c.name;
  editCustomerModal?.hide();
  editingCustomerId = null;
});

// Runs after the Edit modal has fully closed
editCustomerModalEl?.addEventListener('hidden.bs.modal', () => {
  // Edit closed only to show the delete confirmation: nothing else to do
  if(switchingToDelete){
    switchingToDelete = false;
    return;
  }

  const detailsId = reopenDetailsId;
  reopenDetailsId = null;

  if(editSavedName){
    // Saved: show the success alert, then return to the details pop-up if we came from it
    const name = editSavedName;
    editSavedName = null;
    if(detailsId){
      alertModalEl.addEventListener('hidden.bs.modal', () => openDetailsPanel(detailsId), { once: true });
    }
    showAlert(`${name}'s information has been updated successfully.`, { title: 'Changes Saved', type: 'success' });
  } else if(detailsId){
    // Cancelled: go straight back to the details pop-up
    openDetailsPanel(detailsId);
  }
});

// Delete button inside the Edit modal
document.getElementById('editDeleteBtn')?.addEventListener('click', () => {
  const c = customers.find(x => x.id === editingCustomerId);
  if(!c) return;

  pendingDeleteId = c.id;
  reopenEditAfterDelete = true;
  switchingToDelete = true;

  document.getElementById('deleteCustomerAvatar').src = avatarUrlFor(c);
  document.getElementById('deleteCustomerName').textContent = c.name;
  document.getElementById('deleteCustomerContact').textContent = c.phone || c.email || '';

  hideThen(editCustomerModal, editCustomerModalEl, () => deleteConfirmModal?.show());
});

// ---- Delete confirmation modal ----
const deleteConfirmModalEl = document.getElementById('deleteConfirmModal');
const deleteConfirmModal = deleteConfirmModalEl ? new bootstrap.Modal(deleteConfirmModalEl) : null;
let pendingDeleteId = null;

function openDeleteConfirm(id){
  const c = customers.find(x => x.id === id);
  if(!c) return;
  pendingDeleteId = id;
  reopenEditAfterDelete = false;
  reopenDetailsId = detailsModalEl.classList.contains('show') ? id : null;

  document.getElementById('deleteCustomerAvatar').src = avatarUrlFor(c);
  document.getElementById('deleteCustomerName').textContent = c.name;
  document.getElementById('deleteCustomerContact').textContent = c.phone || c.email || '';

  hideThen(detailsModal, detailsModalEl, () => deleteConfirmModal?.show());
}


document.getElementById('confirmDeleteBtn')?.addEventListener('click', async () => {
  if (!pendingDeleteId) return;

  const customerId = pendingDeleteId;
  const customer = customers.find(c => String(c.id) === String(customerId));

  if (!customer) {
    showAlert('Customer not found.', {
      title: 'Error',
      type: 'danger'
    });
    return;
  }

  try {
    const url = `${CUSTOMER_BASE}/${encodeURIComponent(customer.dbId)}`;

    await api('DELETE', url);

    const idx = customers.findIndex(
      c => String(c.id) === String(customerId)
    );

    if (idx > -1) {
      customers.splice(idx, 1);
    }

    pendingDeleteId = null;
    reopenDetailsId = null;
    reopenEditAfterDelete = false;
    editingCustomerId = null;

    applyFilters();
    deleteConfirmModal?.hide();

    showAlert(`${customer.name} has been deleted successfully.`, {
      title: 'Customer Deleted',
      type: 'success'
    });

  } catch (error) {
    showAlert(error.message || 'Failed to delete customer.', {
      title: 'Delete Failed',
      type: 'danger'
    });
  }
});


// If the delete was cancelled, go back to wherever it was started from
deleteConfirmModalEl?.addEventListener('hidden.bs.modal', () => {
  if(reopenEditAfterDelete){
    reopenEditAfterDelete = false;
    editCustomerModal?.show();   // back to the Edit form, with the user's changes intact
    return;
  }
  if(reopenDetailsId){
    const id = reopenDetailsId;
    reopenDetailsId = null;
    openDetailsPanel(id);
  }
});

// ---- Add New Customer modal ----
const addCustomerModalEl = document.getElementById('addCustomerModal');
const addCustomerModal = addCustomerModalEl ? new bootstrap.Modal(addCustomerModalEl) : null;
const addCustomerForm = document.getElementById('addCustomerForm');

addCustomerBtn?.addEventListener('click', () => {
  addCustomerForm.reset();
  addCustomerModal?.show();
});

addCustomerForm?.addEventListener('submit', (e) => {
  e.preventDefault();

  const fullName = document.getElementById('cFullName').value.trim();
  const email = document.getElementById('cEmail').value.trim();
  const phone = document.getElementById('cPhone').value.trim();

  if(!fullName || !email || !phone){
    showAlert('Please fill in all required fields.', { title: 'Missing Information', type: 'warning' });
    return;
  }

  const newCustomer = {
    id: generateCustomerId(),
    name: fullName,
    email: email,
    phone: phone,
    dob: document.getElementById('cDob').value,
    address: document.getElementById('cAddress').value.trim(),
    city: document.getElementById('cCity').value.trim(),
    province: document.getElementById('cProvince').value.trim(),
    zip: document.getElementById('cZip').value.trim(),
    customerType: document.getElementById('cType').value.trim() || 'New',
    preferredContactMethod: document.getElementById('cPreferredContactMethod').value.trim(),
    preferredPackage: '',
    status: 'active',
    isNew: true,
    isRepeat: false,
    totalBookings: 0,
    totalSpent: 0,
    avgPerBooking: 0,
    lastActivity: new Date().toISOString().slice(0, 10),
    bookings: [],
    notes: document.getElementById('cNotes').value.trim()
      ? [{ text: document.getElementById('cNotes').value.trim(), author: 'Admin User', date: new Date().toISOString().slice(0, 10) }]
      : []
  };

  customers.push(newCustomer);

  applyFilters();

  // Close the form first, then show the success alert
  addCustomerModalEl.addEventListener('hidden.bs.modal', () => {
    showAlert(`${fullName} has been added successfully.`, { title: 'Customer Added', type: 'success' });
  }, { once: true });
  addCustomerModal?.hide();
  addCustomerForm.reset();
});


applyFilters();