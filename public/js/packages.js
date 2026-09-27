 const packages = [

 ];

  let selectedId = null;

  const tableBody = document.getElementById('tableBody');
  const detailPanel = document.getElementById('detailPanel');
  const searchInput = document.getElementById('searchInput');
  const categoryFilter = document.getElementById('categoryFilter');
  const statusFilter = document.getElementById('statusFilter');
  const sortFilter = document.getElementById('sortFilter');

  function fmtPrice(n){
    return '$' + n.toLocaleString();
  }

  function catBadgeClass(cat){
    return cat === 'Wedding' ? 'badge-cat-wedding' : 'badge-cat-birthday';
  }

  function renderTable(){
    let list = packages.slice();

    const q = searchInput.value.trim().toLowerCase();
    if(q){
      list = list.filter(p => p.name.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q));
    }

    const cat = categoryFilter.value;
    if(cat !== 'All Categories'){
      list = list.filter(p => p.category === cat);
    }

    const status = statusFilter.value;
    if(status !== 'All Status'){
      list = list.filter(p => p.status === status);
    }

    switch(sortFilter.value){
      case 'Oldest First': list.sort((a,b) => a.id - b.id); break;
      case 'Newest First': list.sort((a,b) => b.id - a.id); break;
      case 'Price: High to Low': list.sort((a,b) => b.price - a.price); break;
      case 'Price: Low to High': list.sort((a,b) => a.price - b.price); break;
    }

    if(list.length === 0){
      tableBody.innerHTML = `
        <tr>
          <td colspan="7" class="text-center py-5">
            <i class="bi bi-inbox" style="font-size:28px; color:#c9c9c9;"></i>
            <div class="mt-2" style="font-size:13px; color:#a8a8a8;">No packages found</div>
          </td>
        </tr>
      `;
      document.getElementById('statTotal').textContent = packages.length;
      return;
    }

    tableBody.innerHTML = list.map(p => `
      <tr data-id="${p.id}" class="${p.id === selectedId ? 'table-row-selected' : ''}">
        <td>
          <div class="d-flex align-items-center gap-2">
            <img class="pkg-thumb" src="${p.img}" alt="${p.name}">
            <div>
              <div class="pkg-name">${p.name}</div>
              <div class="pkg-desc">${p.desc}</div>
            </div>
          </div>
        </td>
        <td><span class="badge rounded-pill ${catBadgeClass(p.category)}">${p.category}</span></td>
        <td>${fmtPrice(Math.round(p.price / 3))}</td>
        <td>${p.guestRange}</td>
        <td><span class="badge rounded-pill badge-status">${p.status}</span></td>
        <td>${p.bookings}</td>
        <td>
          <div class="d-flex align-items-center gap-1">
            <button class="icon-btn btn p-0 d-flex align-items-center justify-content-center" title="View"><i class="bi bi-eye"></i></button>
            <button class="icon-btn btn p-0 d-flex align-items-center justify-content-center" title="Edit"><i class="bi bi-pencil"></i></button>
            <button class="icon-btn btn p-0 d-flex align-items-center justify-content-center" title="More"><i class="bi bi-three-dots-vertical"></i></button>
          </div>
        </td>
      </tr>
    `).join('');

    document.querySelectorAll('#tableBody tr').forEach(row => {
      row.addEventListener('click', () => {
        selectedId = parseInt(row.dataset.id);
        renderTable();
        renderDetail();
      });
    });

    document.getElementById('statTotal').textContent = packages.length;
  }

  function renderDetail(){
    const p = packages.find(x => x.id === selectedId);

    if(!p){
      detailPanel.innerHTML = `
        <h6 class="mb-3">Package Details</h6>
        <div class="text-center py-5">
          <i class="bi bi-box-seam" style="font-size:28px; color:#c9c9c9;"></i>
          <div class="mt-2" style="font-size:13px; color:#a8a8a8;">Select a package to view details</div>
        </div>
      `;
      return;
    }

    detailPanel.innerHTML = `
      <h6 class="mb-3">Package Details</h6>
      <img class="detail-img mb-3" src="${p.detailImg}" alt="${p.name}">
      <div class="d-flex justify-content-between align-items-center mb-1">
        <h6 class="mb-0">Package Details</h6>
        <span class="badge rounded-pill badge-status">${p.status}</span>
      </div>
      <div class="d-flex justify-content-between align-items-baseline mb-2">
        <span class="detail-price">${fmtPrice(p.price)}</span>
        <span class="detail-guest">${p.guestRange} Guest</span>
      </div>
      <p class="detail-desc mb-3">${p.description}</p>
      <h6 class="mb-2" style="font-size:12px;">Inclusions</h6>
      <ul class="inclusions-list mb-4 ps-3">
        ${p.inclusions.map(i => `<li>${i}</li>`).join('')}
      </ul>
      <button class="btn btn-edit-package w-100" id="editBtn">Edit Package</button>
    `;

    document.getElementById('editBtn').addEventListener('click', () => {
      alert('Edit "' + p.name + '" — hook this up to your edit flow.');
    });
  }

  searchInput.addEventListener('input', renderTable);
  categoryFilter.addEventListener('change', renderTable);
  statusFilter.addEventListener('change', renderTable);
  sortFilter.addEventListener('change', renderTable);

  //document.getElementById('addPackageBtn').addEventListener('click', () => {
   // alert('Add New Package — hook this up to your create-package flow.');
 // });
 searchInput.addEventListener('input', renderTable);
categoryFilter.addEventListener('change', renderTable);
statusFilter.addEventListener('change', renderTable);
sortFilter.addEventListener('change', renderTable);

renderTable();
renderDetail();

