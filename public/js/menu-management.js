// menu-management.js

// No sample data — populate these from your backend/API.
const menuItems = [];
const categories = [];

let activeTab = 'all'; // 'all' | 'inactive'

const tableBody       = document.getElementById('tableBody');
const tableFooter      = document.getElementById('tableFooter');
const searchInput      = document.getElementById('searchInput');
const categoryFilter   = document.getElementById('categoryFilter');
const statusFilter     = document.getElementById('statusFilter');
const sortFilter        = document.getElementById('sortFilter');
const tabAll            = document.getElementById('tabAll');
const tabInactive       = document.getElementById('tabInactive');
const categoriesList    = document.getElementById('categoriesList');
const addNewMenuBtn     = document.getElementById('addNewMenuBtn');
const manageCategoryBtn = document.getElementById('manageCategoryBtn');

const statTotalItems      = document.getElementById('statTotalItems');
const statTotalCategories = document.getElementById('statTotalCategories');
const statPopularCategory = document.getElementById('statPopularCategory');
const statTotalSale       = document.getElementById('statTotalSale');

function fmtPrice(n){
  return '₱' + Number(n || 0).toLocaleString();
}

function renderStats(){
  statTotalItems.textContent = menuItems.filter(i => i.status === 'Active').length;
  statTotalCategories.textContent = categories.length;

  if(categories.length){
    const top = categories.slice().sort((a,b) => (b.count||0) - (a.count||0))[0];
    statPopularCategory.textContent = top?.name || '—';
  } else {
    statPopularCategory.textContent = '—';
  }

  const totalSale = menuItems.reduce((sum, i) => sum + (Number(i.price) || 0), 0);
  statTotalSale.textContent = fmtPrice(totalSale);
}

function getFilteredList(){
  let list = menuItems.slice();

  if(activeTab === 'inactive'){
    list = list.filter(i => i.status === 'Inactive');
  }

  const q = (searchInput?.value || '').trim().toLowerCase();
  if(q){
    list = list.filter(i =>
      i.name.toLowerCase().includes(q) ||
      (i.desc || '').toLowerCase().includes(q)
    );
  }

  const cat = categoryFilter?.value;
  if(cat && cat !== 'All Categories'){
    list = list.filter(i => i.category === cat);
  }

  const status = statusFilter?.value;
  if(status && status !== 'All Status'){
    list = list.filter(i => i.status === status);
  }

  switch(sortFilter?.value){
    case 'Oldest First': list.sort((a,b) => a.id - b.id); break;
    case 'Newest First': list.sort((a,b) => b.id - a.id); break;
    case 'Price: High to Low': list.sort((a,b) => b.price - a.price); break;
    case 'Price: Low to High': list.sort((a,b) => a.price - b.price); break;
  }

  return list;
}

function renderTable(){
  const list = getFilteredList();

  if(list.length === 0){
    tableBody.innerHTML = `
      <tr>
        <td colspan="5">
          <div class="empty-state-menu">
            <i class="bi bi-egg-fried"></i>
            No menu items found
          </div>
        </td>
      </tr>
    `;
    tableFooter.textContent = `Showing 0 of ${menuItems.length} items`;
    return;
  }

  tableBody.innerHTML = list.map(item => `
    <tr data-id="${item.id}">
      <td>
        <div class="d-flex align-items-center gap-2">
          <img class="item-thumb-menu" src="${item.img || ''}" alt="${item.name}">
          <div>
            <div class="item-name-menu">${item.name}</div>
            <div class="item-desc-menu">${item.desc || ''}</div>
          </div>
        </div>
      </td>
      <td><span class="cat-swatch" style="background:${item.categoryColor || '#eee'};"></span></td>
      <td>${fmtPrice(item.price)}</td>
      <td><span class="status-pill-menu ${item.status === 'Inactive' ? 'inactive' : ''}">${item.status}</span></td>
      <td>
        <div class="d-flex align-items-center gap-1">
          <button class="action-icon-btn-menu" title="View" data-action="view"><i class="bi bi-eye"></i></button>
          <button class="action-icon-btn-menu" title="Edit" data-action="edit"><i class="bi bi-pencil"></i></button>
          <button class="action-icon-btn-menu delete" title="Delete" data-action="delete"><i class="bi bi-trash"></i></button>
        </div>
      </td>
    </tr>
  `).join('');

  tableFooter.textContent = `Showing ${list.length} of ${menuItems.length} items`;

  tableBody.querySelectorAll('[data-action="delete"]').forEach(btn => {
    btn.addEventListener('click', (e) => {
      const row = e.target.closest('tr');
      const id = parseInt(row.dataset.id, 10);
      const idx = menuItems.findIndex(i => i.id === id);
      if(idx > -1 && confirm('Delete this menu item?')){
        menuItems.splice(idx, 1);
        renderAll();
      }
    });
  });
}

function renderCategoryOptions(){
  if(!categoryFilter) return;
  const current = categoryFilter.value;
  categoryFilter.innerHTML = `<option>All Categories</option>` +
    categories.map(c => `<option>${c.name}</option>`).join('');
  categoryFilter.value = current || 'All Categories';
}

function renderCategoriesList(){
  if(categories.length === 0){
    categoriesList.innerHTML = `<div class="category-empty" id="categoriesEmpty">No categories yet.</div>`;
    return;
  }

  categoriesList.innerHTML = categories.map(c => `
    <div class="category-row">
      <div class="category-dot" style="background:${c.color || '#999'};">${(c.name || '?').charAt(0).toUpperCase()}</div>
      <div class="category-name">${c.name}</div>
      <div class="category-count">${c.count || 0}</div>
    </div>
  `).join('');
}

function renderAll(){
  renderStats();
  renderCategoryOptions();
  renderCategoriesList();
  renderTable();
}

// Tabs
tabAll?.addEventListener('click', () => {
  activeTab = 'all';
  tabAll.classList.add('active');
  tabInactive.classList.remove('active');
  renderTable();
});
tabInactive?.addEventListener('click', () => {
  activeTab = 'inactive';
  tabInactive.classList.add('active');
  tabAll.classList.remove('active');
  renderTable();
});

// Filters
searchInput?.addEventListener('input', renderTable);
categoryFilter?.addEventListener('change', renderTable);
statusFilter?.addEventListener('change', renderTable);
sortFilter?.addEventListener('change', renderTable);

// Actions
addNewMenuBtn?.addEventListener('click', () => {
  window.location.href = 'add-menu.html';
});

 manageCategoryBtn = document.getElementById('manageCategoryBtn');

manageCategoryBtn?.addEventListener('click', () => {
  alert('Manage Category — hindi pa ito naka-connect. Sabihin mo kung gusto mo itong gawing gumagana.');
});

renderAll();