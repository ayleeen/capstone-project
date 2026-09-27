const menuRowsBody = document.getElementById('menuRows');
  const addMenuItemBtn = document.getElementById('addMenuItemBtn');
 
  const menuCategories = ["Appetizer", "Main Dish", "Rice", "Drinks", "Dessert", "Add-on"];
 
  function buildCategoryOptions(){
    return `<option value="" selected disabled>Select category</option>` +
      menuCategories.map(c => `<option>${c}</option>`).join('');
  }
 
  function addMenuRow(){
    const row = document.createElement('tr');
    row.innerHTML = `
      <td class="menu-row-item"><input type="text" class="form-control menu-item-input" placeholder="e.g. Chicken Adobo"></td>
      <td class="menu-row-item"><select class="form-select menu-category-input">${buildCategoryOptions()}</select></td>
      <td class="text-center">
        <button type="button" class="btn btn-delete-row remove-row"><i class="fa-solid fa-trash"></i></button>
      </td>
    `;
    menuRowsBody.appendChild(row);
  }
 
  // start with 4 empty rows, matching the layout
  for(let i=0;i<4;i++){ addMenuRow(); }
 
  addMenuItemBtn.addEventListener('click', function(){
    addMenuRow();
    updatePreview();
  });
 
  // delete row (event delegation, keeps at least one row)
  menuRowsBody.addEventListener('click', function(e){
    const btn = e.target.closest('.remove-row');
    if(!btn) return;
    if(menuRowsBody.querySelectorAll('tr').length > 1){
      btn.closest('tr').remove();
    } else {
      btn.closest('tr').querySelectorAll('input, select').forEach(el => el.value = '');
    }
    updatePreview();
  });
 
  // live update inclusions whenever a menu item name or category changes
  menuRowsBody.addEventListener('input', updatePreview);
  menuRowsBody.addEventListener('change', updatePreview);
 
  // status switch text
  const statusSwitch = document.getElementById('statusSwitch');
  const statusText = document.getElementById('statusText');
  statusSwitch.addEventListener('change', function(){
    statusText.textContent = this.checked ? 'Active' : 'Inactive';
    statusText.style.color = this.checked ? '#1f7a34' : '#8a8f98';
  });
 
  // photo upload preview
  const uploadPhotoBtn = document.getElementById('uploadPhotoBtn');
  const photoInput = document.getElementById('photoInput');
  const photoPreview = document.getElementById('photoPreview');
  const photoPlaceholder = document.getElementById('photoPlaceholder');
  uploadPhotoBtn.addEventListener('click', () => photoInput.click());
  photoInput.addEventListener('change', function(){
    const file = this.files[0];
    if(file){
      const reader = new FileReader();
      reader.onload = function(e){
        photoPreview.src = e.target.result;
        photoPreview.classList.remove('d-none');
        photoPlaceholder.classList.add('d-none');
      };
      reader.readAsDataURL(file);
    }
  });
 
  // ----- Live preview logic -----
  const pkgName = document.getElementById('pkgName');
  const pkgPrice = document.getElementById('pkgPrice');
  const pkgCategory = document.getElementById('pkgCategory');
  const pkgGuestMin = document.getElementById('pkgGuestMin');
  const pkgGuestMax = document.getElementById('pkgGuestMax');
  const pkgDescription = document.getElementById('pkgDescription');
 
  const previewName = document.getElementById('previewName');
  const previewPrice = document.getElementById('previewPrice');
  const previewMeta = document.getElementById('previewMeta');
  const previewDesc = document.getElementById('previewDesc');
  const inclusionsList = document.getElementById('inclusionsList');
 
  function updatePreview(){
    // Package name
    previewName.textContent = pkgName.value.trim() || 'Your package name';
 
    // Price
    const priceVal = parseFloat(pkgPrice.value);
    previewPrice.textContent = isNaN(priceVal) ? '₱0' : '₱' + priceVal.toLocaleString();
 
    // Category + guest range meta line
    const metaParts = [];
    if(pkgCategory.value) metaParts.push(pkgCategory.value);
    if(pkgGuestMin.value || pkgGuestMax.value){
      const min = pkgGuestMin.value || '?';
      const max = pkgGuestMax.value || '?';
      metaParts.push(`${min}–${max} Guests`);
    }
    previewMeta.textContent = metaParts.join(' • ');
 
    // Description
    previewDesc.textContent = pkgDescription.value.trim() || 'Your package description will appear here.';
 
    // Inclusions from menu rows
    const rows = menuRowsBody.querySelectorAll('tr');
    const items = [];
    rows.forEach(row => {
      const nameVal = row.querySelector('.menu-item-input').value.trim();
      const catVal = row.querySelector('.menu-category-input').value;
      if(nameVal){
        items.push(catVal ? `${nameVal} (${catVal})` : nameVal);
      }
    });
 
    if(items.length === 0){
      inclusionsList.innerHTML = `<div class="inclusion-empty" id="inclusionsEmpty">No menu items added yet.</div>`;
    } else {
      inclusionsList.innerHTML = items.map(txt =>
        `<div class="inclusion-item"><i class="fa-solid fa-circle-check"></i> ${txt}</div>`
      ).join('');
    }
  }
 
  [pkgName, pkgPrice, pkgCategory, pkgGuestMin, pkgGuestMax, pkgDescription].forEach(el => {
    el.addEventListener('input', updatePreview);
    el.addEventListener('change', updatePreview);
  });
 
  // initialize preview in its empty state
  updatePreview();
 
  // delete package confirm
  document.getElementById('deletePackageBtn').addEventListener('click', function(){
    if(confirm('Are you sure you want to delete this package?')){
      alert('Package deleted (demo only).');
    }
  });
 
  // form submit
  document.getElementById('packageForm').addEventListener('submit', function(e){
    e.preventDefault();
    alert('Package saved (demo only).');
  });