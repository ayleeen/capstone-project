const searchInput    = document.getElementById('searchInput');
const categoryFilter = document.getElementById('categoryFilter');
const statusFilter   = document.getElementById('statusFilter');
const sortFilter     = document.getElementById('sortFilter');
const tableBody      = document.getElementById('packageTableBody');
const noResultsMsg   = document.getElementById('noResultsMsg');
const tableFooter    = document.getElementById('tableFooter');

// ---- Package Details slide-out panel refs ----
const viewPkgModalEl    = document.getElementById('viewPackageModal');
const viewPkgModal      = new bootstrap.Modal(viewPkgModalEl);
const detailsPanelBody  = document.getElementById('viewPkgBody');
const detailsPanelTitle = document.getElementById('viewPkgTitle');

const CATEGORY_LABELS = {
  wedding: 'Wedding',
  birthday: 'Birthday',
  corporate: 'Corporate',
  debut: 'Debut'
};

// NOTE: `packages` ay galing na sa Blade (@json), hindi na dine-define dito.

// ---- Helpers ----
function fmtMoney(n){
  return '₱' + Number(n || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
}

function esc(s){
  return String(s ?? '').replace(/[&<>"']/g, c => (
    { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c]
  ));
}

function catLabel(c){
  return CATEGORY_LABELS[c] || (c ? c.charAt(0).toUpperCase() + c.slice(1) : '');
}

function findPkg(id){
  return packages.find(x => String(x.id) === String(id));
}

function goEdit(id){
  const p = findPkg(id);
  if(p) window.location.href = p.editUrl;
}

// ---- Table rendering ----
function renderRows(list){
  tableFooter.textContent = `Showing ${list.length} of ${packages.length} items`;

  if(list.length === 0){
    tableBody.innerHTML = '';
    noResultsMsg.textContent = packages.length === 0
      ? 'No packages found.'
      : 'No packages match your search/filters.';
    noResultsMsg.classList.remove('d-none');
    return;
  }

  noResultsMsg.classList.add('d-none');

  tableBody.innerHTML = list.map(p => `
    <tr data-id="${p.id}">
      <td>
        <div class="pkg-cell">
          ${p.image
            ? `<img class="pkg-thumb" src="${esc(p.image)}" alt="${esc(p.name)}">`
            : `<div class="pkg-thumb d-flex align-items-center justify-content-center"><i class="fa-solid fa-image" style="color:#c9c9c9;"></i></div>`}
          <div>
            <div class="pkg-name">${esc(p.name)}</div>
            <div class="pkg-desc">${esc(p.description || '')}</div>
          </div>
        </div>
      </td>
      <td><span class="cat-badge ${esc(p.category)}">${esc(catLabel(p.category))}</span></td>
      <td>${fmtMoney(p.price)}</td>
      <td>${p.guestMin || 0} - ${p.guestMax || 0}</td>
      <td><span class="status-pill ${p.status === 'active' ? 'confirmed' : 'cancelled'}">${p.status === 'active' ? 'Active' : 'Inactive'}</span></td>
      <td>${p.bookings || 0}</td>
      <td>
        <div class="action-icons">
          <button type="button" class="view-btn" data-id="${p.id}" title="View"><i class="bi bi-eye"></i></button>
          <button type="button" class="edit-btn" data-id="${p.id}" title="Edit"><i class="bi bi-pencil"></i></button>
          <button type="button" class="delete-package-btn" data-id="${p.id}" title="Delete"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>
  `).join('');

  attachRowActionHandlers();
}

// ---- Filtering ----
function applyFilters(){
  const q = searchInput.value.trim().toLowerCase();
  const category = categoryFilter.value;
  const status = statusFilter.value;

  let filtered = packages.filter(p => {
    const haystack = [
      p.name,
      p.description,
      catLabel(p.category),
      p.price,
      fmtMoney(p.price),
      p.guestMin + ' - ' + p.guestMax,
      p.status
    ].join(' ').toLowerCase();

    const matchesSearch = !q || haystack.includes(q);
    const matchesCategory = !category || p.category === category;
    const matchesStatus = !status || p.status === status;
    return matchesSearch && matchesCategory && matchesStatus;
  });

  switch(sortFilter.value){
    case 'oldest':     filtered.sort((a, b) => a.id - b.id); break;
    case 'price-high': filtered.sort((a, b) => (b.price||0) - (a.price||0)); break;
    case 'price-low':  filtered.sort((a, b) => (a.price||0) - (b.price||0)); break;
    default:           filtered.sort((a, b) => b.id - a.id); // newest
  }

  renderRows(filtered);
}

searchInput.addEventListener('input', applyFilters);
categoryFilter.addEventListener('change', applyFilters);
statusFilter.addEventListener('change', applyFilters);
sortFilter.addEventListener('change', applyFilters);

// ---- Row action wiring ----
function attachRowActionHandlers(){
  document.querySelectorAll('.view-btn, .view-details-btn').forEach(btn => {
    btn.addEventListener('click', () => openViewModal(btn.dataset.id));
  });

  document.querySelectorAll('.edit-btn, .edit-package-btn').forEach(btn => {
    btn.addEventListener('click', () => goEdit(btn.dataset.id));
  });

  document.querySelectorAll('.delete-package-btn').forEach(btn => {
    btn.addEventListener('click', () => openDeleteConfirm(btn.dataset.id));
  });

  document.querySelectorAll('.more-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const menu = btn.closest('.action-menu-wrap').querySelector('.action-menu');
      const wasOpen = menu.classList.contains('open');
      closeAllActionMenus();
      if(wasOpen) return;

      menu.classList.add('open');

      // fixed position para hindi maputol ng .table-responsive
      const r = btn.getBoundingClientRect();
      const h = menu.offsetHeight;
      const openUp = r.bottom + 4 + h > window.innerHeight;

      menu.style.position = 'fixed';
      menu.style.left = 'auto';
      menu.style.right = (window.innerWidth - r.right) + 'px';
      menu.style.top = (openUp ? r.top - h - 4 : r.bottom + 4) + 'px';
    });
  });
}

function closeAllActionMenus(){
  document.querySelectorAll('.action-menu.open').forEach(menu => menu.classList.remove('open'));
}
document.addEventListener('click', closeAllActionMenus);
window.addEventListener('scroll', closeAllActionMenus, true);

// ---- Package Details slide-out panel ----
function renderPackageDetailsPanel(p){
  const imgBlock = p.image
    ? `<img class="pkg-detail-img" src="${esc(p.image)}" alt="${esc(p.name)}">`
    : `<div class="pkg-detail-img-empty"><i class="fa-solid fa-image"></i></div>`;

  const inclusionsList = (p.inclusions && p.inclusions.length)
    ? `<ul class="view-pkg-inclusions">
        ${p.inclusions.map(item => `<li><i class="fa-solid fa-check"></i> ${esc(item)}</li>`).join('')}
       </ul>`
    : `<div class="view-pkg-empty">No inclusions listed for this package.</div>`;

  detailsPanelBody.innerHTML = `
    ${imgBlock}

    <div class="pkg-detail-header">
      <h4>${esc(p.name)}</h4>
      <span class="status-pill ${p.status === 'active' ? 'confirmed' : 'cancelled'}">${p.status === 'active' ? 'Active' : 'Inactive'}</span>
    </div>

    <div class="pkg-detail-price-row">
      <span class="pkg-detail-price">${fmtMoney(p.price)}</span>
      <span class="pkg-detail-guests">${p.guestMin || 0} - ${p.guestMax || 0} Guest</span>
    </div>

    <p class="view-pkg-desc">${esc(p.description) || 'No description provided for this package.'}</p>

    <div class="view-pkg-inclusions-title">Inclusions</div>
    ${inclusionsList}

    <button type="button" class="btn-detail-edit-outline" id="detailEditPackageBtn">
      <i class="fa-solid fa-pen"></i> Edit Package
    </button>
  `;

  document.getElementById('detailEditPackageBtn').addEventListener('click', () => goEdit(p.id));
}

function openViewModal(id){
  const p = findPkg(id);
  if(!p) return;
  detailsPanelTitle.textContent = 'Package Details';
  renderPackageDetailsPanel(p);
    viewPkgModal.show();
}

function closeDetailsPanel(){
    viewPkgModal.hide();
}

document.getElementById('closeDetailsPanel')?.addEventListener('click', closeDetailsPanel);

// ---- Delete confirmation modal ----
const deleteConfirmModalEl = document.getElementById('deleteConfirmModal');
const deleteConfirmModal = deleteConfirmModalEl ? new bootstrap.Modal(deleteConfirmModalEl) : null;

function openDeleteConfirm(id){
  const p = findPkg(id);
  if(!p) return;

  // ang form ang magsa-submit ng DELETE sa Laravel
  document.getElementById('deleteForm').action = p.deleteUrl;

  const img = document.getElementById('deletePackageImg');
  img.src = p.image || '';
  img.style.visibility = p.image ? 'visible' : 'hidden';

  document.getElementById('deletePackageName').textContent = p.name;
  document.getElementById('deletePackageCategory').textContent = catLabel(p.category);
  deleteConfirmModal?.show();
}

// ---- Init ----
applyFilters();